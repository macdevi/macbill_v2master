<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Customer;
use App\Models\InternetPackage;
use App\Models\Router;
use App\Services\MikroTikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

class CustomerMikrotikImportController extends Controller
{
    public function create()
    {
        return $this->index();
    }

    public function index()
    {
        return view('customers.import-mikrotik', [
            'routers' => Router::query()
                ->where('active', true)
                ->orderBy('name')
                ->get(),
            'areas' => Area::query()
                ->where('active', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name']),
        ]);
    }

    public function preview(Request $request, MikroTikService $mikrotik)
    {
        $data = $request->validate([
            'router_id' => ['required', 'integer', 'exists:routers,id'],
            'area_id' => [
                'required',
                'integer',
                Rule::exists('areas', 'id')->where(
                    fn ($query) => $query->where('active', true)
                ),
            ],
        ], [
            'area_id.required' => 'Pilih wilayah tujuan import.',
            'area_id.exists' => 'Wilayah tujuan tidak tersedia atau sudah nonaktif.',
        ]);

        $router = Router::query()
            ->where('active', true)
            ->findOrFail($data['router_id']);

        $area = Area::query()
            ->where('active', true)
            ->findOrFail($data['area_id']);

        try {
            $secrets = $mikrotik->allPppoeUsers($router);
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'router_id' => 'Tidak dapat mengambil data dari MikroTik: '.$e->getMessage(),
                ]);
        }

        $existingUsernames = Customer::query()
            ->whereNotNull('pppoe_username')
            ->pluck('id', 'pppoe_username')
            ->mapWithKeys(fn ($id, $username) => [
                mb_strtolower(trim((string) $username)) => $id,
            ])
            ->all();

        $seen = [];
        $records = [];

        foreach ($secrets as $secret) {
            $username = trim((string) ($secret['name'] ?? ''));
            $service = strtolower(trim((string) ($secret['service'] ?? 'any')));

            if ($username === '' || !in_array($service, ['pppoe', 'any'], true)) {
                continue;
            }

            $key = mb_strtolower($username);

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;

            $records[] = [
                'username' => $username,
                'password' => (string) ($secret['password'] ?? ''),
                'profile' => trim((string) ($secret['profile'] ?? '')),
                'disabled' => strtolower(trim((string) ($secret['disabled'] ?? 'no'))) === 'yes',
                'existing_customer_id' => $existingUsernames[$key] ?? null,
            ];
        }

        usort($records, fn (array $a, array $b) => strcasecmp($a['username'], $b['username']));

        return view('customers.import-mikrotik-preview', [
            'router' => $router,
            'area' => $area,
            'records' => $records,
            'packages' => InternetPackage::query()
                ->where('active', true)
                ->orderBy('monthly_price')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'router_id' => ['required', 'integer', 'exists:routers,id'],
            'area_id' => [
                'required',
                'integer',
                Rule::exists('areas', 'id')->where(
                    fn ($query) => $query->where('active', true)
                ),
            ],
            'customers' => ['required', 'array', 'min:1'],
            'customers.*.selected' => ['nullable', 'in:1'],
            'customers.*.pppoe_username' => ['nullable', 'string', 'max:100'],
            'customers.*.pppoe_password' => ['nullable', 'string', 'max:255'],
            'customers.*.name' => ['nullable', 'string', 'max:255'],
            'customers.*.phone' => ['nullable', 'string', 'max:30'],
            'customers.*.address' => ['nullable', 'string'],
            'customers.*.internet_package_id' => ['nullable', 'integer'],
            'customers.*.due_day' => ['nullable', 'integer', 'min:1', 'max:28'],
            'customers.*.status' => ['nullable', 'in:active,inactive'],
        ], [
            'area_id.required' => 'Wilayah tujuan import wajib dipilih.',
            'area_id.exists' => 'Wilayah tujuan tidak tersedia atau sudah nonaktif.',
        ]);

        $router = Router::query()
            ->where('active', true)
            ->findOrFail($data['router_id']);

        $area = Area::query()
            ->where('active', true)
            ->findOrFail($data['area_id']);

        $selected = collect($data['customers'])
            ->filter(fn (array $row) => ($row['selected'] ?? null) === '1')
            ->values();

        if ($selected->isEmpty()) {
            return back()->withErrors([
                'customers' => 'Pilih minimal satu pelanggan untuk diimpor.',
            ])->withInput();
        }

        $errors = [];
        $created = 0;

        DB::transaction(function () use ($selected, $router, $area, &$errors, &$created) {
            $seen = [];

            foreach ($selected as $number => $row) {
                $username = trim((string) ($row['pppoe_username'] ?? ''));
                $password = (string) ($row['pppoe_password'] ?? '');
                $name = trim((string) ($row['name'] ?? ''));
                $phone = trim((string) ($row['phone'] ?? ''));
                $address = trim((string) ($row['address'] ?? ''));
                $packageId = $row['internet_package_id'] ?? null;
                $dueDay = $row['due_day'] ?? null;
                $status = ($row['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

                if ($username === '' || $password === '' || $name === '' || !$packageId || !$dueDay) {
                    $errors[] = 'Baris '.($number + 1).' belum lengkap. Username, password PPPoE, nama, paket, dan jatuh tempo wajib diisi.';
                    continue;
                }

                $key = mb_strtolower($username);

                if (isset($seen[$key])) {
                    $errors[] = 'Username PPPoE '.$username.' duplikat dalam import.';
                    continue;
                }

                $seen[$key] = true;

                if (Customer::query()->whereRaw('LOWER(pppoe_username) = ?', [$key])->exists()) {
                    $errors[] = 'Username PPPoE '.$username.' sudah terdaftar.';
                    continue;
                }

                if (!InternetPackage::query()
                    ->where('active', true)
                    ->whereKey($packageId)
                    ->exists()) {
                    $errors[] = 'Paket untuk '.$username.' tidak valid atau tidak aktif.';
                    continue;
                }

                Customer::create([
                    'area_id' => $area->id,
                    'router_id' => $router->id,
                    'internet_package_id' => $packageId,
                    'customer_code' => 'CUST-'.strtoupper(Str::random(8)),
                    'name' => $name,
                    'phone' => $phone !== '' ? $phone : null,
                    'address' => $address !== '' ? $address : null,
                    'pppoe_username' => $username,
                    'pppoe_password' => $password,
                    'due_day' => (int) $dueDay,
                    'status' => $status,
                ]);

                $created++;
            }

            if ($created === 0) {
                throw new RuntimeException(
                    $errors !== []
                        ? implode(' ', $errors)
                        : 'Tidak ada pelanggan yang berhasil diimpor.'
                );
            }
        });

        $message = $created.' pelanggan berhasil diimpor ke wilayah '.$area->name.'.';

        if ($errors !== []) {
            $message .= ' Sebagian baris dilewati: '.implode(' ', $errors);
        }

        return redirect()
            ->route('customers.index')
            ->with('success', $message);
    }
}
