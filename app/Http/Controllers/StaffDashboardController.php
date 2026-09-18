<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\MikroTikService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

class StaffDashboardController extends Controller
{
    public function index(MikroTikService $mikrotik): View
    {
        $user = auth()->user();
        $areaIds = $user->activeAreaIds();

        $areas = $user->areas()
            ->wherePivot('active', true)
            ->where('areas.active', true)
            ->orderBy('areas.name')
            ->get(['areas.id', 'areas.code', 'areas.name']);

        $customers = Customer::query()
            ->with(['router:id,name,host,port,username,password,ssl'])
            ->whereIn('area_id', $areaIds)
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

        /*
         * Penting: sesi aktif dipisahkan per router.
         * Satu username yang aktif di Router A tidak boleh menandai
         * customer dengan username serupa di Router B sebagai online.
         */
        $routers = $customers->pluck('router')->filter()->unique('id')->values();

        $mikrotikConnected = 'Disconnected';
        $mikrotikCpu = null;
        $mikrotikUptime = null;

        foreach ($routers as $router) {
            try {
                $resource = $mikrotik->systemResource($router);

                if (is_array($resource)) {
                    $mikrotikConnected = 'Connected';
                    $mikrotikCpu = isset($resource['cpu-load'])
                        ? (int) $resource['cpu-load']
                        : null;
                    $mikrotikUptime = $resource['uptime'] ?? null;
                    break;
                }
            } catch (Throwable $e) {
                Log::warning('Dashboard staff gagal membaca resource MikroTik.', [
                    'user_id' => $user->id,
                    'router_id' => $router->id,
                    'router_name' => $router->name,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $onlineByRouter = [];

        foreach ($customers->pluck('router')->filter()->unique('id') as $router) {
            try {
                $onlineByRouter[$router->id] = collect($mikrotik->onlineUsers($router))
                    ->keyBy(fn ($activeUser) => mb_strtolower(
                        trim((string) ($activeUser['name'] ?? ''))
                    ))
                    ->map(fn ($activeUser) => [
                        'uptime' => $activeUser['uptime'] ?? null,
                        'address' => $activeUser['address'] ?? null,
                    ])
                    ->all();
            } catch (\Throwable $e) {
                $onlineByRouter[$router->id] = [];

                Log::warning('Dashboard staff gagal membaca PPPoE aktif dari MikroTik.', [
                    'user_id' => $user->id,
                    'router_id' => $router->id,
                    'router_name' => $router->name,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $onlineList = [];
        $offlineList = [];
        $isolatedList = [];

        foreach ($customers as $customer) {
            $username = mb_strtolower(trim((string) $customer->pppoe_username));

            $session = $onlineByRouter[$customer->router_id][$username] ?? null;
            $isOnline = $session !== null;
            $isIsolated = $customer->status === 'isolated';

            $customerData = [
                'id' => $customer->id,
                'customer_code' => $customer->customer_code,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'pppoe_username' => $customer->pppoe_username,
                'router_name' => $customer->router?->name,
                'uptime' => $session['uptime'] ?? null,
                'address' => $session['address'] ?? null,
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

        $areaInvoiceQuery = Invoice::query()
            ->whereHas(
                'customer',
                fn ($query) => $query->whereIn('area_id', $areaIds)
            );

        $currentPeriodInvoices = (clone $areaInvoiceQuery)
            ->whereBetween('billing_period', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ]);

        $currentPeriodInvoiceCount = (clone $currentPeriodInvoices)->count();

        if ($currentPeriodInvoiceCount > 0) {
            $estimatedRevenue = (float) (clone $currentPeriodInvoices)->sum('amount');
        } else {
            $estimatedRevenue = (float) Customer::query()
                ->with('internetPackage:id,monthly_price')
                ->whereIn('area_id', $areaIds)
                ->whereIn('status', ['active', 'isolated'])
                ->get(['id', 'internet_package_id', 'monthly_price_override'])
                ->sum(function (Customer $customer): float {
                    return $customer->monthly_price_override !== null
                        ? (float) $customer->monthly_price_override
                        : (float) ($customer->internetPackage?->monthly_price ?? 0);
                });
        }

        $monthlyIncome = (float) Payment::query()
            ->where('status', 'verified')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [
                $monthStart->copy()->startOfDay(),
                $monthEnd->copy()->endOfDay(),
            ])
            ->whereHas(
                'invoice.customer',
                fn ($query) => $query->whereIn('area_id', $areaIds)
            )
            ->sum('amount');

        $monthlyExpense = (float) Expense::query()
            ->whereIn('area_id', $areaIds)
            ->where('status', 'posted')
            ->whereBetween('expense_date', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->sum('amount');

        $netProfit = $monthlyIncome - $monthlyExpense;

        $pendingInvoiceQuery = (clone $areaInvoiceQuery)
            ->whereDate('billing_date', '<=', $today)
            ->whereIn('status', ['unpaid', 'isolated']);

        $pendingRevenue = (float) (clone $pendingInvoiceQuery)->sum('amount');
        $pendingInvoiceCount = (int) (clone $pendingInvoiceQuery)->count();

        $financialMonthLabel = now()->translatedFormat('F Y');


        return view('staff.home', compact(
            'user',
            'areas',
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
            'mikrotikConnected',
            'mikrotikCpu',
            'mikrotikUptime',
        ));
    }
}
