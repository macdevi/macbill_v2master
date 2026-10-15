<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Customer;
use App\Models\Installation;
use App\Models\InternetPackage;
use App\Models\Router;
use App\Services\InstallationBillingService;
use App\Services\MikroTikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InstallationController extends Controller
{
    private function actor()
    {
        $user = auth()->user();
        abort_unless(
            $user && in_array($user->role, ['super_admin', 'admin'], true),
            403
        );
        return $user;
    }

    private function areas()
    {
        $user = $this->actor();
        return Area::query()->where('active', true)
            ->when(!$user->isSuperAdmin(), fn ($q) =>
                $q->whereIn('id', $user->activeAreaIds()))
            ->orderBy('name')->get(['id', 'code', 'name']);
    }

    private function authorizeInstallation(Installation $installation): void
    {
        $user = $this->actor();
        abort_unless(
            $user->isSuperAdmin() ||
            $user->activeAreaIds()->contains((int) $installation->area_id),
            403
        );
    }

    public function index()
    {
        $user = $this->actor();
        $installations = Installation::query()
            ->when(!$user->isSuperAdmin(), fn ($q) =>
                $q->whereIn('area_id', $user->activeAreaIds()))
            ->latest('id')->paginate(20);

        return view('installations.index', compact('installations'));
    }

    public function create()
    {
        return view('installations.create', [
            'areas' => $this->areas(),
            'packages' => InternetPackage::where('active', true)
                ->orderBy('monthly_price')->get(),
            'routers' => Router::where('active', true)->orderBy('name')->get(),
            'submissionKey' => (string) Str::uuid(),
        ]);
    }

    public function store(
        Request $request,
        InstallationBillingService $billing,
        MikroTikService $mikrotik
    )
    {
        $user = $this->actor();

        $request->validate([
            'submission_key' => ['required', 'uuid'],
            'save_target' => ['required', 'in:billing,billing_mikrotik'],
            'psb_is_test' => ['sometimes', 'boolean'],
        ]);
        $isTestData = $request->boolean('psb_is_test');
        abort_unless(!$isTestData || $user->isSuperAdmin(), 403);

        $existing = Installation::where(
            'submission_key', $request->input('submission_key')
        )->first();

        if ($existing) {
            $this->authorizeInstallation($existing);
            abort_unless((int) $existing->created_by === (int) $user->id, 403);
            return redirect()->route('installations.show', $existing);
        }

        $areaIds = $this->areas()->pluck('id')->all();
        $money = ['required', 'numeric', 'min:0', 'max:999999999', 'decimal:0,2'];

        $data = $request->validate([
            'submission_key' => ['required', 'uuid'],
            'save_target' => ['required', 'in:billing,billing_mikrotik'],
            'area_id' => ['required', 'integer',
                Rule::exists('areas', 'id')->whereIn('id', $areaIds)],
            'router_id' => ['required', 'integer',
                Rule::exists('routers', 'id')->where('active', true)],
            'internet_package_id' => ['required', 'integer',
                Rule::exists('internet_packages', 'id')->where('active', true)],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30',
                'regex:/^\+?[0-9][0-9\s().-]{6,29}$/'],
            'address' => ['nullable', 'string', 'max:3000'],
            'pppoe_username' => ['required', 'string', 'max:100',
                'unique:customers,pppoe_username'],
            'pppoe_password' => ['required', 'string', 'min:6', 'max:255'],
            'due_day' => ['required', 'integer', 'min:1', 'max:28'],
            'monthly_price_override' => ['nullable', 'numeric', 'min:0',
                'max:999999999', 'decimal:0,2'],
            'tax_mode' => ['required', 'in:none,inclusive,exclusive'],
            'activated_on' => ['required', 'date_format:Y-m-d',
                'before_or_equal:'.now()->toDateString()],
            'installation_fee' => $money,
            'equipment_fee' => $money,
            'other_fee' => $money,
            'paid_amount' => $money,
            'payment_method' => ['nullable', 'in:cash,transfer'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $total = round(
            (float) $data['installation_fee'] +
            (float) $data['equipment_fee'] +
            (float) $data['other_fee'], 2
        );
        $paid = round((float) $data['paid_amount'], 2);

        if ($paid > $total || ($paid > 0 && empty($data['payment_method']))) {
            return back()->withErrors([
                'paid_amount' => 'Pembayaran tidak boleh melebihi total; metode wajib dipilih jika ada pembayaran.',
            ])->withInput($request->except('pppoe_password'));
        }

        $first = $billing->firstBillingOn(
            $data['activated_on'], (int) $data['due_day']
        );

        $installation = DB::transaction(function () use ($data, $user, $first, $total, $paid, $isTestData) {
            $package = InternetPackage::findOrFail($data['internet_package_id']);
            $customer = Customer::create([
                'area_id' => $data['area_id'],
                'router_id' => $data['router_id'],
                'internet_package_id' => $data['internet_package_id'],
                'customer_code' => 'CUST-'.strtoupper(Str::random(8)),
                'name' => $data['name'],
                'phone' => $data['phone'],
                'address' => $data['address'] ?? null,
                'pppoe_username' => $data['pppoe_username'],
                'pppoe_password' => $data['pppoe_password'],
                'due_day' => $data['due_day'],
                'monthly_price_override' => $data['monthly_price_override'] ?? null,
                'tax_mode' => $data['tax_mode'],
                'status' => 'active',
                'credit_balance' => 0,
                'service_activated_on' => $data['activated_on'],
                'first_billing_on' => $first,
            ]);

            $customer->forceFill([
                'mikrotik_sync_status' => 'pending',
            ])->save();

            $installation = Installation::create([
                'submission_key' => $data['submission_key'],
                'receipt_number' => 'PSB-'.now()->format('Ymd').'-'.strtoupper(Str::random(10)),
                'customer_id' => $customer->id,
                'area_id' => $customer->area_id,
                'created_by' => $user->id,
                'customer_name' => $customer->name,
                'customer_phone' => $customer->phone,
                'customer_address' => $customer->address,
                'package_name' => $package->name,
                'activated_on' => $data['activated_on'],
                'first_billing_on' => $first,
                'billing_day' => $customer->due_day,
                'monthly_price' => $customer->effective_monthly_price,
                'tax_mode' => $customer->tax_mode,
                'installation_fee' => $data['installation_fee'],
                'equipment_fee' => $data['equipment_fee'],
                'other_fee' => $data['other_fee'],
                'total_amount' => $total,
                'paid_amount' => $paid,
                'payment_method' => $paid > 0 ? $data['payment_method'] : null,
                'paid_at' => $paid > 0 ? now() : null,
                'notes' => $data['notes'] ?? null,
            ]);

            app(\App\Services\InstallationPaymentService::class)
                ->record($installation, $user, $isTestData);

            return $installation;
        });

        if ($data['save_target'] === 'billing_mikrotik') {
            $customer = $installation->customer;
            try {
                if (! $customer) {
                    throw new \RuntimeException('Pelanggan tidak ditemukan.');
                }

                $customer->loadMissing(['router', 'internetPackage']);
                if (! $customer->router) {
                    throw new \RuntimeException('Router tidak ditemukan.');
                }

                $response = $mikrotik->updatePppoeSecret(
                    $customer->router, $customer
                );
                $this->assertMikrotikResponse($response);

                $customer->forceFill([
                    'mikrotik_sync_status' => 'synced',
                ])->save();

                return redirect()->route('installations.show', $installation)
                    ->with('success', 'Billing dan nota tersimpan; sinkronisasi PPPoE MikroTik berhasil.');
            } catch (\Throwable $exception) {
                report($exception);
                if ($customer) {
                    $customer->forceFill([
                        'mikrotik_sync_status' => 'failed',
                    ])->save();
                }

                return redirect()->route('installations.show', $installation)
                    ->withErrors([
                        'mikrotik' => 'Billing dan nota sudah tersimpan, tetapi sinkronisasi MikroTik gagal. Jangan membuat PSB ulang. Periksa router lalu sinkronkan pelanggan melalui halaman edit.',
                    ]);
            }
        }

        return redirect()->route('installations.show', $installation)
            ->with('success', 'Billing dan nota tersimpan. MikroTik tidak diubah; layanan dicatat aktif sesuai tanggal yang diisi.');
    }


    private function assertMikrotikResponse(array $response): void
    {
        foreach ($response as $key => $value) {
            if (in_array($key, ['!trap', '!fatal'], true) ||
                (is_string($value) && in_array($value, ['!trap', '!fatal'], true))) {
                throw new \RuntimeException('RouterOS mengembalikan error API.');
            }

            if (is_array($value)) {
                $this->assertMikrotikResponse($value);
            }
        }
    }

    public function show(Installation $installation)
    {
        $this->authorizeInstallation($installation);
        return view('installations.show', compact('installation'));
    }
}
