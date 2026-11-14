<?php
namespace App\Http\Controllers;
use App\Models\Area;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Router;
use App\Services\MikroTikService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    private function visibleCustomerQuery(): Builder
    {
        $user = auth()->user();
        $query = Customer::query();
        if (!$user || !$user->isSuperAdmin()) {
            $query->whereIn('area_id', $user?->activeAreaIds() ?? []);
        }
        return $query;
    }

    private function applyInvoiceAreaScope(Builder $query): Builder
    {
        $user = auth()->user();
        if (!$user || !$user->isSuperAdmin()) {
            $areaIds = $user?->activeAreaIds() ?? [];
            $query->whereHas('customer', function (Builder $customerQuery) use ($areaIds) {
                $customerQuery->whereIn('area_id', $areaIds);
            });
        }
        return $query;
    }

    private function applyPaymentAreaScope(Builder $query): Builder
    {
        $user = auth()->user();
        if (!$user || !$user->isSuperAdmin()) {
            $areaIds = $user?->activeAreaIds() ?? [];
            $query->whereHas('invoice.customer', function (Builder $customerQuery) use ($areaIds) {
                $customerQuery->whereIn('area_id', $areaIds);
            });
        }
        return $query;
    }

    private function applyExpenseAreaScope(Builder $query): Builder
    {
        $user = auth()->user();
        if (!$user || !$user->isSuperAdmin()) {
            $query->whereIn('area_id', $user?->activeAreaIds() ?? []);
        }
        return $query;
    }

    private function psbIncomeQuery(): QueryBuilder
    {
        return app(\App\Services\InstallationPaymentService::class)
            ->visibleIncomeQuery(auth()->user());
    }

    private function buildFinanceActivityQuery(): QueryBuilder
    {
        $user = auth()->user();
        $isSuperAdmin = $user?->isSuperAdmin() ?? false;
        $areaIds = $user?->activeAreaIds() ?? [];

        $incomeQuery = DB::table('payments')
            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->where('payments.status', 'verified')
            ->whereNotNull('payments.paid_at')
            ->when(!$isSuperAdmin, function (QueryBuilder $q) use ($areaIds) {
                $q->whereIn('customers.area_id', $areaIds);
            })
            ->select([
                DB::raw("'income' as activity_type"),
                'payments.id as activity_id',
                'payments.paid_at as activity_date',
                'customers.name as activity_title',
                'invoices.invoice_number as activity_reference',
                'payments.method as activity_method',
                'payments.amount as activity_amount',
                'payments.status as activity_status',
            ]);

        $expenseQuery = DB::table('expenses')
            ->where('status', 'posted')
            ->when(!$isSuperAdmin, function (QueryBuilder $q) use ($areaIds) {
                $q->whereIn('area_id', $areaIds);
            })
            ->select([
                DB::raw("'expense' as activity_type"),
                'id as activity_id',
                DB::raw("CASE
                    WHEN date(expense_date) = date(created_at)
                        THEN created_at
                    ELSE datetime(expense_date)
                END as activity_date"),
                'title as activity_title',
                'category as activity_reference',
                'payment_method as activity_method',
                'amount as activity_amount',
                'status as activity_status',
            ]);

        $psbIncomeQuery = $this->psbIncomeQuery()
            ->select([
                DB::raw("'income' as activity_type"),
                'id as activity_id',
                'paid_at as activity_date',
                'customer_name as activity_title',
                'receipt_number as activity_reference',
                'method as activity_method',
                'amount as activity_amount',
                'status as activity_status',
            ]);

        return $incomeQuery
            ->unionAll($psbIncomeQuery)
            ->unionAll($expenseQuery)
            ->orderByDesc('activity_date')
            ->orderByDesc('activity_id');
    }

    public function financeActivity(): View
    {
        $activities = DB::query()
            ->fromSub($this->buildFinanceActivityQuery(), 'finance_activity')
            ->orderByDesc('activity_date')
            ->orderByDesc('activity_id')
            ->paginate(20)
            ->withQueryString();

        return view('finance.activity', compact('activities'));
    }

    public function index(MikroTikService $mikrotik): View
    {
        $network=app(\App\Services\DashboardNetworkService::class)
            ->snapshot(auth()->user()?->isSuperAdmin() ? request()->query('router_id') : null,$mikrotik);
        $monitorRouters=$network['monitorRouters'];
        $selectedMonitorRouter=$network['selectedMonitorRouter'];
        $mikrotikConnected=$network['mikrotikConnected'];
        $mikrotikCpu=$network['mikrotikCpu'];
        $mikrotikUptime=$network['mikrotikUptime'];
        $mikrotikIdentity=$network['mikrotikIdentity'];
        $traffic=$network['traffic'];
        $totalCustomers=$network['totalCustomers'];
        $onlineCustomers=$network['onlineCustomers'];
        $offlineCustomers=$network['offlineCustomers'];
        $isolatedCustomers=$network['isolatedCustomers'];
        $unknownCustomers=$network['unknownCustomers'];
        $onlinePercentage=$network['onlinePercentage'];
        $offlinePercentage=$network['offlinePercentage'];
        $customerStatusLists=$network['customerStatusLists'];
        $networkCheckedAt=$network['networkCheckedAt'];
        $dashboardScope=auth()->user()?->isSuperAdmin()?'global':'area';
        $dashboardAreas=\App\Services\CustomerAccessScope::areas()->orderBy('name')->pluck('name');
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $today = now()->toDateString();
        $currentPeriodInvoices = $this->applyInvoiceAreaScope(
            Invoice::query()
                ->whereBetween('billing_period', [
                    $monthStart->toDateString(),
                    $monthEnd->toDateString(),
                ])
        );
        $currentPeriodInvoiceCount = (clone $currentPeriodInvoices)->count();
        if ($currentPeriodInvoiceCount > 0) {
            $estimatedRevenue = (float) (clone $currentPeriodInvoices)->sum('amount');
        } else {
            $estimatedRevenue = (float) $this->visibleCustomerQuery()
                ->with('internetPackage:id,monthly_price')
                ->whereIn('status', ['active', 'isolated'])
                ->get(['id', 'internet_package_id', 'monthly_price_override'])
                ->sum(function (Customer $customer): float {
                    return $customer->monthly_price_override !== null
                        ? (float) $customer->monthly_price_override
                        : (float) ($customer->internetPackage?->monthly_price ?? 0);
                });
        }
        $monthlyIncome = (float) $this->applyPaymentAreaScope(
            Payment::query()
                ->where('status', 'verified')
                ->whereNotNull('paid_at')
                ->whereBetween('paid_at', [
                    $monthStart->copy()->startOfDay(),
                    $monthEnd->copy()->endOfDay(),
                ])
        )->sum('amount');
        $monthlyIncome += (float) $this->psbIncomeQuery()
            ->whereBetween('paid_at', [
                $monthStart->copy()->startOfDay(),
                $monthEnd->copy()->endOfDay(),
            ])->sum('amount');

        $monthlyExpense = (float) $this->applyExpenseAreaScope(
            Expense::query()
                ->where('status', 'posted')
                ->whereBetween('expense_date', [
                    $monthStart->toDateString(),
                    $monthEnd->toDateString(),
                ])
        )->sum('amount');
        $netProfit = $monthlyIncome - $monthlyExpense;
        $pendingInvoiceQuery = $this->applyInvoiceAreaScope(
            Invoice::query()
                ->whereDate('billing_date', '<=', $today)
                ->whereIn('status', ['unpaid', 'isolated'])
        );
        $pendingRevenue = (float) (clone $pendingInvoiceQuery)->sum('amount');
        $pendingInvoiceCount = (int) (clone $pendingInvoiceQuery)->count();

        $pendingInvoiceList = (clone $pendingInvoiceQuery)
            ->with([
                'customer:id,area_id,name,phone',
                'customer.area:id,name',
            ])
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->orderBy('billing_date')
            ->limit(15)
            ->get();

        $financialMonthLabel = now()->translatedFormat('F Y');

        $recentFinanceActivity = DB::query()
            ->fromSub($this->buildFinanceActivityQuery(), 'finance_activity')
            ->orderByDesc('activity_date')
            ->orderByDesc('activity_id')
            ->limit(5)
            ->get();

        $areaFinancialSummaries = collect();
        if (auth()->user()?->isSuperAdmin()) {
            $customerCountsByArea = Customer::query()
                ->selectRaw('area_id, COUNT(*) as customer_count')
                ->whereNotNull('area_id')
                ->whereIn('status', ['active', 'isolated'])
                ->groupBy('area_id')
                ->pluck('customer_count', 'area_id');
            $incomeByArea = Payment::query()
                ->selectRaw('customers.area_id, SUM(payments.amount) as total_income')
                ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
                ->join('customers', 'customers.id', '=', 'invoices.customer_id')
                ->whereNotNull('customers.area_id')
                ->where('payments.status', 'verified')
                ->whereNotNull('payments.paid_at')
                ->whereBetween('payments.paid_at', [
                    $monthStart->copy()->startOfDay(),
                    $monthEnd->copy()->endOfDay(),
                ])
                ->groupBy('customers.area_id')
                ->pluck('total_income', 'customers.area_id');

            $psbIncomeByArea = $this->psbIncomeQuery()
                ->whereNotNull('area_id')
                ->whereBetween('paid_at', [
                    $monthStart->copy()->startOfDay(),
                    $monthEnd->copy()->endOfDay(),
                ])
                ->selectRaw('area_id, SUM(amount) as total_income')
                ->groupBy('area_id')
                ->pluck('total_income', 'area_id');

            foreach ($psbIncomeByArea as $areaId => $amount) {
                $incomeByArea[$areaId] =
                    (float) ($incomeByArea[$areaId] ?? 0) + (float) $amount;
            }

            $expenseByArea = Expense::query()
                ->selectRaw('area_id, SUM(amount) as total_expense')
                ->whereNotNull('area_id')
                ->where('status', 'posted')
                ->whereBetween('expense_date', [
                    $monthStart->toDateString(),
                    $monthEnd->toDateString(),
                ])
                ->groupBy('area_id')
                ->pluck('total_expense', 'area_id');
            $pendingByArea = Invoice::query()
                ->selectRaw('customers.area_id, SUM(invoices.amount) as total_pending, COUNT(*) as pending_invoice_count')
                ->join('customers', 'customers.id', '=', 'invoices.customer_id')
                ->whereNotNull('customers.area_id')
                ->whereDate('invoices.billing_date', '<=', $today)
                ->whereIn('invoices.status', ['unpaid', 'isolated'])
                ->groupBy('customers.area_id')
                ->get()
                ->keyBy('area_id');
            $areas = Area::query()
                ->select(['id', 'code', 'name'])
                ->orderBy('name')
                ->get();
            $areaFinancialSummaries = $areas->map(function (Area $area) use (
                $customerCountsByArea,
                $incomeByArea,
                $expenseByArea,
                $pendingByArea
            ): array {
                $income = (float) ($incomeByArea[$area->id] ?? 0);
                $expense = (float) ($expenseByArea[$area->id] ?? 0);
                $pending = $pendingByArea->get($area->id);
                return [
                    'id' => $area->id,
                    'code' => $area->code,
                    'name' => $area->name,
                    'customer_count' => (int) ($customerCountsByArea[$area->id] ?? 0),
                    'income' => $income,
                    'expense' => $expense,
                    'net_profit' => $income - $expense,
                    'pending_revenue' => (float) ($pending?->total_pending ?? 0),
                    'pending_invoice_count' => (int) ($pending?->pending_invoice_count ?? 0),
                ];
            })->values();
            $globalExpense = (float) Expense::query()
                ->whereNull('area_id')
                ->where('status', 'posted')
                ->whereBetween('expense_date', [
                    $monthStart->toDateString(),
                    $monthEnd->toDateString(),
                ])
                ->sum('amount');
            if ($globalExpense > 0) {
                $areaFinancialSummaries->push([
                    'id' => null,
                    'code' => null,
                    'name' => 'Tanpa Wilayah / Biaya Global',
                    'customer_count' => 0,
                    'income' => 0,
                    'expense' => $globalExpense,
                    'net_profit' => -$globalExpense,
                    'pending_revenue' => 0,
                    'pending_invoice_count' => 0,
                ]);
            }
        }
        return view('dashboard', compact(
            'totalCustomers',
            'onlineCustomers',
            'offlineCustomers',
            'isolatedCustomers',
            'onlinePercentage',
            'offlinePercentage',
            'customerStatusLists',
            'monitorRouters',
            'selectedMonitorRouter',
            'unknownCustomers',
            'networkCheckedAt',
            'dashboardScope',
            'dashboardAreas',
            'estimatedRevenue',
            'monthlyIncome',
            'monthlyExpense',
            'netProfit',
            'pendingRevenue',
            'pendingInvoiceCount',
            'pendingInvoiceList',
            'financialMonthLabel',
            'areaFinancialSummaries',
            'recentFinanceActivity',
            'mikrotikConnected',
'traffic',
            'mikrotikCpu',
            'mikrotikUptime',
            'mikrotikIdentity',
        ));
    }
}
