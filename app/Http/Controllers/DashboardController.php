<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Router;
use App\Services\MikroTikService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Query customer yang terlihat oleh user saat ini.
     * Super Admin melihat global; Admin hanya area assignment aktif.
     */
    private function visibleCustomerQuery(): Builder
    {
        $user = auth()->user();

        $query = Customer::query();

        if (!$user || !$user->isSuperAdmin()) {
            $query->whereIn('area_id', $user?->activeAreaIds() ?? []);
        }

        return $query;
    }

    /**
     * Batasi query invoice melalui customer.area_id untuk Admin.
     */
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

    /**
     * Batasi query payment melalui invoice.customer.area_id untuk Admin.
     */
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

    /**
     * Batasi expense langsung memakai area_id.
     * Expense global (area_id NULL) hanya tampil untuk Super Admin.
     */
    private function applyExpenseAreaScope(Builder $query): Builder
    {
        $user = auth()->user();

        if (!$user || !$user->isSuperAdmin()) {
            $query->whereIn('area_id', $user?->activeAreaIds() ?? []);
        }

        return $query;
    }

    /**
     * Dashboard ringkasan koneksi PPPoE real-time.
     */
    public function index(MikroTikService $mikrotik): View
    {
        $onlineUsernames = [];

        $routers = Router::query()
            ->where('active', true)
            ->get();

        foreach ($routers as $router) {
            try {
                $activeUsers = $mikrotik->onlineUsers($router);

                foreach ($activeUsers as $activeUser) {
                    $username = mb_strtolower(
                        trim((string) ($activeUser['name'] ?? ''))
                    );

                    if ($username !== '') {
                        $onlineUsernames[$username] = true;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Dashboard gagal membaca PPPoE aktif dari MikroTik.', [
                    'router_id' => $router->id,
                    'router_name' => $router->name,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $customers = $this->visibleCustomerQuery()
            ->with(['router:id,name'])
            ->select([
                'id',
                'area_id',
                'router_id',
                'customer_code',
                'name',
                'phone',
                'pppoe_username',
                'status',
            ])
            ->orderBy('name')
            ->get();

        $onlineList = [];
        $offlineList = [];
        $isolatedList = [];

        foreach ($customers as $customer) {
            $username = mb_strtolower(trim((string) $customer->pppoe_username));
            $isOnline = $username !== '' && isset($onlineUsernames[$username]);
            $isIsolated = $customer->status === 'isolated';

            $customerData = [
                'id' => $customer->id,
                'customer_code' => $customer->customer_code,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'pppoe_username' => $customer->pppoe_username,
                'router_name' => $customer->router?->name,
            ];

            if ($isIsolated) {
                $isolatedList[] = $customerData;
            } elseif ($isOnline) {
                $onlineList[] = $customerData;
            } else {
                $offlineList[] = $customerData;
            }
        }

        $totalCustomers = $customers->count();
        $onlineCustomers = count($onlineList);
        $offlineCustomers = count($offlineList);
        $isolatedCustomers = count($isolatedList);

        $onlinePercentage = $totalCustomers > 0
            ? round(($onlineCustomers / $totalCustomers) * 100, 1)
            : 0;

        $offlinePercentage = $totalCustomers > 0
            ? round(($offlineCustomers / $totalCustomers) * 100, 1)
            : 0;

        $customerStatusLists = [
            'online' => $onlineList,
            'offline' => $offlineList,
            'isolated' => $isolatedList,
        ];

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

        $financialMonthLabel = now()->translatedFormat('F Y');

        return view('dashboard', compact(
            'totalCustomers',
            'onlineCustomers',
            'offlineCustomers',
            'isolatedCustomers',
            'onlinePercentage',
            'offlinePercentage',
            'customerStatusLists',
            'estimatedRevenue',
            'monthlyIncome',
            'monthlyExpense',
            'netProfit',
            'pendingRevenue',
            'pendingInvoiceCount',
            'financialMonthLabel',
        ));
    }
}
