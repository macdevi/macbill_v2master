<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Router;
use App\Services\MikroTikService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Dashboard ringkasan koneksi PPPoE real-time.
     *
     * Online dihitung dari username yang muncul pada /ppp/active/print
     * di seluruh router MikroTik yang aktif.
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
                /*
                 * Dashboard tetap terbuka bila salah satu router tidak dapat dibaca.
                 * Jangan tampilkan kredensial router pada respons/browser.
                 */
                Log::warning('Dashboard gagal membaca PPPoE aktif dari MikroTik.', [
                    'router_id' => $router->id,
                    'router_name' => $router->name,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        /*
         * Ambil data aman untuk dashboard.
         * pppoe_password tidak dipilih dan tidak pernah dikirim ke browser.
         */
        $customers = Customer::query()
            ->with(['router:id,name'])
            ->select([
                'id',
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

            /*
             * Pelanggan isolate selalu ditampilkan sebagai Terisolir,
             * walaupun sesi PPPoE-nya masih kebetulan terlihat aktif.
             */
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

        /*
         * Ringkasan keuangan bulan berjalan.
         *
         * - Estimasi: invoice periode bulan ini, selain invoice terisolir.
         * - Pendapatan: pembayaran yang sudah diverifikasi pada bulan ini.
         * - Pengeluaran: pengeluaran posted pada bulan ini.
         * - Tertunda: invoice unpaid maupun isolated yang telah terbit sampai hari ini.
         */
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $today = now()->toDateString();

        /*
         * Estimasi pendapatan hybrid:
         * - Jika invoice periode bulan ini sudah dibuat, gunakan total invoice resmi.
         * - Jika belum ada invoice sama sekali, gunakan tarif pelanggan yang masih ditagih:
         *   override harga pelanggan bila tersedia, jika tidak harga paket internet.
         *
         * Customer isolated tetap memiliki tagihan; mereka tidak dikeluarkan dari estimasi.
         */
        $currentPeriodInvoices = Invoice::query()
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
            ->sum('amount');

        $monthlyExpense = (float) Expense::query()
            ->where('status', 'posted')
            ->whereBetween('expense_date', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->sum('amount');

        $netProfit = $monthlyIncome - $monthlyExpense;

        $pendingInvoiceQuery = Invoice::query()
            ->whereDate('billing_date', '<=', $today)
            ->whereIn('status', ['unpaid', 'isolated']);

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
