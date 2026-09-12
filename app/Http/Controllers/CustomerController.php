<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExcelExport;
use App\Exports\CustomerExcelTemplateExport;
use App\Models\Customer;
use App\Models\InternetPackage;
use App\Models\Router;
use App\Services\MikroTikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function index(MikroTikService $mikrotik)
    {
        $customers = Customer::with(['internetPackage', 'router'])->latest()->get();

        $onlineByRouter = [];

        foreach ($customers->pluck('router')->filter()->unique('id') as $router) {
            try {
                $onlineByRouter[$router->id] = collect($mikrotik->onlineUsers($router))
                    ->keyBy('name')
                    ->map(fn ($user) => [
                        'uptime' => $user['uptime'] ?? null,
                        'address' => $user['address'] ?? null,
                    ])
                    ->all();
            } catch (\Throwable $e) {
                $onlineByRouter[$router->id] = [];
            }
        }

        $customers->transform(function ($customer) use ($onlineByRouter) {
            $session = $onlineByRouter[$customer->router_id][$customer->pppoe_username] ?? null;

            $customer->setAttribute('realtime_online', $session !== null);
            $customer->setAttribute('realtime_uptime', $session['uptime'] ?? null);
            $customer->setAttribute('realtime_address', $session['address'] ?? null);

            return $customer;
        });

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create', [
            'packages' => InternetPackage::where('active', true)->orderBy('monthly_price')->get(),
            'routers' => Router::where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, MikroTikService $mikrotik)
    {
        $data = $request->validate([
            'router_id' => 'required|exists:routers,id',
            'internet_package_id' => 'required|exists:internet_packages,id',
            'monthly_price_override' => 'nullable|numeric|min:0',
            'tax_mode' => 'required|in:none,inclusive,exclusive',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'pppoe_username' => 'required|string|max:100|unique:customers,pppoe_username',
            'pppoe_password' => 'required|string|min:6',
            'due_day' => 'required|integer|min:1|max:28',
        ]);

        $data['customer_code'] = 'CUST-' . strtoupper(Str::random(8));

        $customer = DB::transaction(fn () => Customer::create($data));

        ActivityLogController::write(
            'customer.created',
            'Pelanggan ' . $customer->name . ' dibuat. Sinkronisasi MikroTik menunggu aksi push manual.'
        );

        return redirect()->route('customers.index')->with(
            'success',
            'Pelanggan berhasil dibuat. Data MikroTik belum disinkronkan; gunakan aksi push manual saat tersedia.'
        );
    }

    public function show(Customer $customer)
    {
        $customer->load(['internetPackage', 'router', 'invoices']);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', [
            'customer' => $customer,
            'packages' => InternetPackage::where('active', true)->get(),
            'routers' => Router::where('active', true)->get(),
        ]);
    }

    public function update(Request $request, Customer $customer, MikroTikService $mikrotik)
    {
        $data = $request->validate([
            'router_id' => 'required|exists:routers,id',
            'internet_package_id' => 'required|exists:internet_packages,id',
            'monthly_price_override' => 'nullable|numeric|min:0',
            'tax_mode' => 'required|in:none,inclusive,exclusive',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'pppoe_username' => 'required|string|max:100|unique:customers,pppoe_username,' . $customer->id,
            'pppoe_password' => 'nullable|string|min:6',
            'due_day' => 'required|integer|min:1|max:28',
        ]);

        if (!$data['pppoe_password']) {
            unset($data['pppoe_password']);
        }

        $customer->update($data);

        ActivityLogController::write(
            'customer.updated',
            'Data pelanggan ' . $customer->name . ' diperbarui. Sinkronisasi MikroTik menunggu aksi push manual.'
        );

        return redirect()->route('customers.index')->with(
            'success',
            'Pelanggan berhasil diperbarui. Data MikroTik belum disinkronkan.'
        );
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return back()->with('success', 'Pelanggan dihapus.');
    }

    public function addCreditBalance(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
        ], [
            'amount.required' => 'Nominal titip saldo wajib diisi.',
            'amount.numeric' => 'Nominal titip saldo harus berupa angka.',
            'amount.min' => 'Nominal titip saldo minimal 1.',
        ]);

        $customer->increment('credit_balance', $data['amount']);
        $customer->refresh();

        ActivityLogController::write(
            'customer.credit_balance_added',
            'Titip saldo pelanggan ' . $customer->name . ' sebesar Rp ' . number_format((float) $data['amount'], 0, ',', '.') . '.'
        );

        return back()->with(
            'success',
            'Titip saldo berhasil ditambahkan. Saldo kredit saat ini Rp ' . number_format((float) $customer->credit_balance, 0, ',', '.') . '.'
        );
    }

    public function isolate(Customer $customer, MikroTikService $mikrotik)
    {
        try {
            $activeSessions = $mikrotik->isolate($customer);

            $customer->update(['status' => 'isolated']);

            ActivityLogController::write(
                'customer.isolated',
                "Pelanggan {$customer->name} ({$customer->pppoe_username}) diisolir."
            );

            $message = count($activeSessions) > 0
                ? 'Pelanggan diisolir dan ' . count($activeSessions) . ' sesi PPPoE aktif diputus.'
                : 'Pelanggan diisolir. Tidak ada sesi PPPoE aktif yang perlu diputus.';

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'Gagal mengisolir pelanggan: ' . $e->getMessage()
            );
        }
    }

    public function activate(Customer $customer, MikroTikService $mikrotik)
    {
        try {
            $mikrotik->activate($customer);

            $customer->update(['status' => 'active']);

            ActivityLogController::write(
                'customer.activated',
                "Pelanggan {$customer->name} ({$customer->pppoe_username}) diaktifkan."
            );

            return back()->with('success', 'Pelanggan diaktifkan.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'Gagal mengaktifkan pelanggan: ' . $e->getMessage()
            );
        }
    }
}

class ActivityLogController
{
    public static function write(string $action, string $description): void
    {
        \App\Models\ActivityLog::create([
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}
