<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExcelExport;
use App\Exports\CustomerExcelTemplateExport;
use App\Models\Area;
use App\Models\Customer;
use App\Models\CreditBalanceTransaction;
use App\Models\InternetPackage;
use App\Models\Router;
use App\Services\MikroTikService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function index(MikroTikService $mikrotik)
    {
        $customers = \App\Services\CustomerAccessScope::customers(
            Customer::with(['area', 'internetPackage', 'router'])->latest()
        )->get();
        $onlineByRouter = [];
        $checkedByRouter = [];
        foreach ($customers->pluck('router')->filter()->unique('id') as $router) {
            $checkedByRouter[$router->id] = false;
            if (!$router->active) {
                continue;
            }
            try {
                $onlineByRouter[$router->id] = collect($mikrotik->onlineUsers($router))
                    ->keyBy('name')
                    ->map(fn ($session) => [
                        'uptime' => $session['uptime'] ?? null,
                        'address' => $session['address'] ?? null,
                    ])->all();
                $checkedByRouter[$router->id] = true;
            } catch (\Throwable $e) {
                logger()->warning('Pemeriksaan koneksi pelanggan belum berhasil.', [
                    'router_id' => $router->id,
                    'exception_class' => get_class($e),
                ]);
            }
        }
        $customers->transform(function ($customer) use ($onlineByRouter, $checkedByRouter) {
            $known = $checkedByRouter[$customer->router_id] ?? false;
            $session = $known
                ? ($onlineByRouter[$customer->router_id][$customer->pppoe_username] ?? null)
                : null;
            $customer->setAttribute('realtime_connection_state', !$known ? 'unknown' : ($session !== null ? 'online' : 'offline'));
            $customer->setAttribute('realtime_online', $session !== null);
            $customer->setAttribute('realtime_uptime', $session['uptime'] ?? null);
            $customer->setAttribute('realtime_address', $session['address'] ?? null);
            return $customer;
        });
        $routers = $customers->pluck('router')->filter()->unique('id')->sortBy('name')->values();
        return view('customers.index', compact('customers', 'routers'));
    }

    public function create()
    {
        return view('customers.create', [
            'areas' => $this->availableAreas(),
            'packages' => InternetPackage::with('routers')->where('active', true)
                ->orderBy('monthly_price')->get(),
            'routers' => \App\Services\CustomerAccessScope::routers()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, MikroTikService $mikrotik)
    {
        $data = $request->validate([
            'area_id' => $this->areaRule(),
            'router_id' => ['bail', 'required', 'integer', $this->customerRouterRule()],
            'internet_package_id' => [
                'bail',
                'required',
                'integer',
                new \App\Rules\PackageAvailableOnRouter(
                    $request->input('router_id')
                ),
            ],
            'monthly_price_override' => ['nullable', 'numeric', 'min:0'],
            'tax_mode' => ['required', 'in:none,inclusive,exclusive'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'pppoe_username' => ['required', 'string', 'max:100', 'unique:customers,pppoe_username'],
            'pppoe_password' => ['required', 'string', 'min:6'],
            'due_day' => ['required', 'integer', 'min:1', 'max:28'],
        ], [
            'area_id.required' => 'Wilayah operasional wajib dipilih.',
            'area_id.exists' => 'Wilayah yang dipilih tidak tersedia atau bukan wilayah penugasan Anda.',
        ]);

        if (!\App\Services\CustomerAccessScope::routerSupportsArea((int)$data['router_id'],(int)$data['area_id'])) {
            throw \Illuminate\Validation\ValidationException::withMessages(['router_id'=>'Router tidak terhubung ke wilayah pelanggan atau tidak tersedia dalam akses Anda.']);
        }
        $data['customer_code'] = 'CUST-'.strtoupper(Str::random(8));

        $customer = DB::transaction(fn () => Customer::create($data));

        ActivityLogController::write(
            'customer.created',
            'Pelanggan '.$customer->name.' dibuat. Sinkronisasi MikroTik menunggu aksi push manual.'
        );

        return redirect()->route('customers.index')->with(
            'success',
            'Pelanggan berhasil dibuat. Data MikroTik belum disinkronkan; gunakan aksi push manual saat tersedia.'
        );
    }

    public function show(Customer $customer)
    {
        $this->authorizeCustomerArea($customer);
        $customer->load(['area', 'internetPackage', 'router', 'invoices']);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $this->authorizeCustomerArea($customer);
        return view('customers.edit', [
            'customer' => $customer,
            'areas' => $this->availableAreas($customer->area_id),
            'canSyncRouter' => \App\Services\CustomerAccessScope::routers()->whereKey($customer->router_id)->exists(),
            'packages' => InternetPackage::with('routers')
                ->where(fn ($q) => $q->where('active', true)->orWhere('id', $customer->internet_package_id))
                ->orderBy('monthly_price')->get(),
            'routers' => Router::query()->where(fn ($q) => $q
                ->whereIn('id', \App\Services\CustomerAccessScope::routers()->select('routers.id'))
                ->orWhere('id', $customer->router_id))->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Customer $customer, MikroTikService $mikrotik)
    {
        $this->authorizeCustomerArea($customer);

        $data = $request->validate([
            'area_id' => $this->areaRule(),
            'router_id' => ['bail', 'required', 'integer', $this->customerRouterRule($customer, $request->input('save_target') === 'billing')],
            'internet_package_id' => [
                'bail',
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request, $customer) {
                    $sameRouter = (string) $request->input('router_id') === (string) $customer->router_id;
                    $samePackage = (string) $value === (string) $customer->internet_package_id;
                    if ($request->input('save_target') === 'billing' && $sameRouter && $samePackage) {
                        return;
                    }
                    (new \App\Rules\PackageAvailableOnRouter($request->input('router_id')))
                        ->validate($attribute, $value, $fail);
                },
            ],
            'monthly_price_override' => ['nullable', 'numeric', 'min:0'],
            'tax_mode' => ['required', 'in:none,inclusive,exclusive'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'pppoe_username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('customers', 'pppoe_username')->ignore($customer->id),
            ],
            'pppoe_password' => ['nullable', 'string', 'min:6'],
            'due_day' => ['required', 'integer', 'min:1', 'max:28'],
            'save_target' => ['required', 'in:billing,billing_mikrotik'],
        ], [
            'area_id.required' => 'Wilayah operasional wajib dipilih.',
            'area_id.exists' => 'Wilayah yang dipilih tidak tersedia atau bukan wilayah penugasan Anda.',
            'save_target.required' => 'Pilihan penyimpanan wajib dipilih.',
            'save_target.in' => 'Pilihan penyimpanan tidak valid.',
        ]);

        $preserveOldPair = $data['save_target'] === 'billing'
            && (int)$data['router_id'] === (int)$customer->router_id
            && (int)$data['area_id'] === (int)$customer->area_id;
        if (!$preserveOldPair && !\App\Services\CustomerAccessScope::routerSupportsArea((int)$data['router_id'],(int)$data['area_id'])) {
            throw \Illuminate\Validation\ValidationException::withMessages(['router_id'=>'Router tidak terhubung ke wilayah pelanggan atau tidak tersedia dalam akses Anda.']);
        }
        if ((int) $data['router_id'] !== (int) $customer->router_id) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'router_id' => 'Pergantian router tidak tersedia melalui edit biasa. '
                    .'Pemindahan akun antar-MikroTik harus ditangani terpisah.',
            ]);
        }

        $syncToMikrotik = $data['save_target'] === 'billing_mikrotik';
        unset($data['save_target']);

        if (empty($data['pppoe_password'])) {
            unset($data['pppoe_password']);

            if ($syncToMikrotik) {
                $customer->pppoe_password = $customer->getOriginal('pppoe_password');
            }
        }

        $customer->update($data);

        if ($syncToMikrotik) {
            try {
                $customer->refresh();
                $customer->loadMissing(['router', 'internetPackage']);

                $mikrotik->updatePppoeSecret($customer->router, $customer);

                ActivityLogController::write(
                    'customer.updated_mikrotik',
                    'Data pelanggan '.$customer->name.' diperbarui dan disinkronkan ke MikroTik.'
                );

                return redirect()->route('customers.index')->with(
                    'success',
                    'Pelanggan berhasil diperbarui dan disinkronkan ke MikroTik.'
                );
            } catch (\Throwable $e) {
                report($e);

                return redirect()
                    ->route('customers.edit', $customer)
                    ->withInput()
                    ->with(
                        'error',
                        'Data billing berhasil disimpan, tetapi sinkronisasi MikroTik gagal: '.$e->getMessage()
                    );
            }
        }

        ActivityLogController::write(
            'customer.updated',
            'Data pelanggan '.$customer->name.' diperbarui. Sinkronisasi MikroTik menunggu aksi push manual.'
        );

        return redirect()->route('customers.index')->with(
            'success',
            'Pelanggan berhasil diperbarui. Data MikroTik belum disinkronkan.'
        );
    }

    public function destroy(Customer $customer)
    {
        $this->authorizeCustomerArea($customer);
        $customer->delete();

        return back()->with('success', 'Pelanggan dihapus.');
    }

    public function addCreditBalance(Request $request, Customer $customer)
    {
        $this->authorizeCustomerArea($customer);
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ], [
            'amount.required' => 'Nominal titip saldo wajib diisi.',
            'amount.numeric' => 'Nominal titip saldo harus berupa angka.',
            'amount.min' => 'Nominal titip saldo minimal 1.',
        ]);

        $customer->increment('credit_balance', $data['amount']);
        $customer->refresh();

        ActivityLogController::write(
            'customer.credit_balance_added',
            'Titip saldo pelanggan '.$customer->name.' sebesar Rp '.number_format((float) $data['amount'], 0, ',', '.').'.'
        );

        return back()->with(
            'success',
            'Titip saldo berhasil ditambahkan. Saldo kredit saat ini Rp '.number_format((float) $customer->credit_balance, 0, ',', '.').'.'
        );
    }


    public function updateCreditBalance(Request $request, Customer $customer)
    {
        $user = auth()->user();

        abort_unless(
            in_array($user?->role, ['super_admin', 'admin'], true),
            403,
            'Anda tidak memiliki akses untuk mengoreksi titip saldo.'
        );

        $this->authorizeCustomerArea($customer);

        $data = $request->validate([
            'credit_balance' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'credit_balance.required' => 'Saldo akhir wajib diisi.',
            'credit_balance.numeric' => 'Saldo akhir harus berupa angka.',
            'credit_balance.min' => 'Saldo akhir tidak boleh kurang dari 0.',
            'reason.required' => 'Alasan koreksi saldo wajib diisi.',
            'reason.max' => 'Alasan koreksi saldo maksimal 500 karakter.',
        ]);

        $wasChanged = DB::transaction(function () use ($customer, $data) {
            $lockedCustomer = Customer::query()
                ->lockForUpdate()
                ->findOrFail($customer->id);

            $before = round((float) $lockedCustomer->credit_balance, 2);
            $after = round((float) $data['credit_balance'], 2);
            $difference = round($after - $before, 2);

            if ($difference === 0.0) {
                return false;
            }

            $lockedCustomer->update([
                'credit_balance' => $after,
            ]);

            CreditBalanceTransaction::create([
                'customer_id' => $lockedCustomer->id,
                'user_id' => auth()->id(),
                'type' => 'adjustment',
                'balance_before' => $before,
                'amount' => $difference,
                'balance_after' => $after,
                'reason' => $data['reason'],
            ]);

            $differenceLabel = $difference >= 0
                ? '+Rp '.number_format($difference, 0, ',', '.')
                : '-Rp '.number_format(abs($difference), 0, ',', '.');

            ActivityLogController::write(
                'customer.credit_balance_adjusted',
                'Koreksi titip saldo pelanggan '.$lockedCustomer->name
                .'. Saldo sebelum Rp '.number_format($before, 0, ',', '.')
                .'. Saldo sesudah Rp '.number_format($after, 0, ',', '.')
                .'. Selisih '.$differenceLabel
                .'. Alasan: '.$data['reason']
            );

            return true;
        });

        if (! $wasChanged) {
            return back()->with(
                'success',
                'Saldo kredit tidak berubah karena nominal baru sama dengan saldo saat ini.'
            );
        }

        return back()->with(
            'success',
            'Saldo kredit pelanggan berhasil dikoreksi.'
        );
    }

    public function isolate(
        Customer $customer,
        \App\Services\CustomerServiceStateService $state
    ) {
        $this->authorizeCustomerArea($customer);

        try {
            $sessions = $state->isolate($customer);

            $message = count($sessions) > 0
                ? 'Billing diisolir dan '.count($sessions).' sesi PPPoE aktif diputus.'
                : 'Billing diisolir. Tidak ada sesi PPPoE aktif yang perlu diputus.';

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            report($e);

            $billingIsolated = $customer->fresh()?->status === 'isolated';

            return back()->with(
                'error',
                $billingIsolated
                    ? 'Billing tetap diisolir. Proses router belum berhasil: '.$e->getMessage()
                    : 'Gagal mengisolir pelanggan: '.$e->getMessage()
            );
        }
    }

    public function activate(
        Customer $customer,
        \App\Services\CustomerServiceStateService $state
    ) {
        $this->authorizeCustomerArea($customer);

        try {
            $changed = $state->activate($customer);

            return back()->with(
                'success',
                $changed
                    ? 'Pelanggan diaktifkan. Tanggal mulai billing telah dicatat bila sebelumnya diisolir.'
                    : 'Pelanggan sudah aktif. Tanggal mulai billing tidak diubah.'
            );
        } catch (\Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'Aktivasi belum selesai. Periksa status billing dan router: '.$e->getMessage()
            );
        }
    }

    private function customerRouterRule(?Customer $customer = null, bool $preserve = false)
    {
        return Rule::exists('routers', 'id')->where(function ($query) use ($customer, $preserve) {
            $query->where(function ($allowed) use ($customer, $preserve) {
                $allowed->whereIn('id', \App\Services\CustomerAccessScope::routers()->select('routers.id'));
                if ($customer && $preserve) {
                    $allowed->orWhere('id', $customer->router_id);
                }
            });
        });
    }

    private function authorizeCustomerArea(Customer $customer): void
    {
        $user = auth()->user();

        abort_unless($user, 403);

        if ($user->isSuperAdmin()) {
            return;
        }

        abort_unless(
            $user->activeAreaIds()->contains((int) $customer->area_id),
            403,
            'Anda tidak memiliki akses ke pelanggan di wilayah ini.'
        );
    }

    private function availableAreas(?int $includeAreaId = null): Collection
    {
        $user = auth()->user();

        $query = Area::query()
            ->where('active', true)
            ->orderBy('name');

        if (!$user || !$user->isSuperAdmin()) {
            $query->whereIn('id', $user?->activeAreaIds() ?? []);
        }

        if ($includeAreaId && $user?->isSuperAdmin()) {
            $query->orWhere('id', $includeAreaId);
        }

        return $query->get(['id', 'code', 'name']);
    }

    private function areaRule(): array
    {
        $user = auth()->user();

        $query = Area::query()->where('active', true);

        if (!$user || !$user->isSuperAdmin()) {
            $query->whereIn('id', $user?->activeAreaIds() ?? []);
        }

        return [
            'required',
            'integer',
            Rule::exists('areas', 'id')->where(
                fn ($exists) => $exists->whereIn('id', $query->pluck('id'))
            ),
        ];
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
