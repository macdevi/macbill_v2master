<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExcelExport;
use App\Exports\CustomerExcelTemplateExport;
use App\Imports\CustomerExcelImport;
use App\Models\Area;
use App\Models\Customer;
use App\Models\InternetPackage;
use App\Models\Router;
use Illuminate\Http\Request;
use App\Services\CustomerAccessScope;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class CustomerExcelController extends Controller
{
    public function export()
    {
        return Excel::download(
            new CustomerExcelExport(CustomerAccessScope::areaIds()),
            'pelanggan-'.now()->format('Y-m-d_His').'.xlsx'
        );
    }

    public function template()
    {
        return Excel::download(
            new CustomerExcelTemplateExport(),
            'template-import-pelanggan.xlsx'
        );
    }

    public function importForm(Request $request)
    {
        $user = $request->user();

        $areas = Area::query()
            ->where('active', true)
            ->when(! $user?->isSuperAdmin(), function ($query) use ($user) {
                $query->whereIn('id', $user?->activeAreaIds() ?? []);
            })
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        return view('customers.import-excel', compact('areas'));
    }

    public function importStore(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'area_id' => ['required', 'integer', 'exists:areas,id'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'area_id.required' => 'Wilayah tujuan import wajib dipilih.',
            'area_id.exists' => 'Wilayah tujuan import tidak ditemukan.',
        ]);

        $area = Area::query()
            ->where('active', true)
            ->findOrFail($data['area_id']);

        abort_unless(
            $user?->isSuperAdmin()
                || $user?->activeAreaIds()->contains((int) $area->id),
            403,
            'Wilayah tujuan import bukan wilayah penugasan Anda.'
        );

        $import = new CustomerExcelImport();

        try {
            Excel::import($import, $request->file('file'));
        } catch (Throwable $e) {
            return back()
                ->withErrors([
                    'file' => 'File Excel tidak dapat dibaca: '.$e->getMessage(),
                ]);
        }

        $rows = collect($import->rows ?? [])
            ->map(fn ($row) => collect($row)->map(
                fn ($value) => is_string($value) ? trim($value) : $value
            )->all())
            ->filter(fn ($row) => collect($row)->filter(
                fn ($value) => $value !== null && $value !== ''
            )->isNotEmpty())
            ->values();

        if ($rows->isEmpty()) {
            return back()->withErrors([
                'file' => 'File Excel tidak berisi data pelanggan.',
            ]);
        }

        $requiredColumns = [
            'router',
            'package',
            'monthly_price_override',
            'tax_mode',
            'name',
            'phone',
            'address',
            'pppoe_username',
            'pppoe_password',
            'due_day',
            'status',
        ];

        $actualColumns = array_keys((array) $rows->first());
        $missingColumns = array_values(array_diff($requiredColumns, $actualColumns));

        if ($missingColumns !== []) {
            return back()->withErrors([
                'file' => 'Header wajib tidak ditemukan: '.implode(', ', $missingColumns).'. Gunakan file Download Template Excel.',
            ]);
        }

        $routers = CustomerAccessScope::routers()->get()
            ->groupBy(fn ($router) => mb_strtolower(trim($router->name)));

        $packages = InternetPackage::with('routers')
            ->where('active', true)->get()
            ->groupBy(fn ($package) => mb_strtolower(trim($package->name)));

        $existingUsernames = Customer::query()
            ->pluck('pppoe_username')
            ->map(fn ($username) => mb_strtolower(trim((string) $username)))
            ->flip()
            ->all();

        $seenUsernames = [];
        $validRows = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            $excelRow = $index + 2;

            $routerName = trim((string) ($row['router'] ?? ''));
            $packageName = trim((string) ($row['package'] ?? ''));
            $monthlyPriceOverrideRaw = trim((string) ($row['monthly_price_override'] ?? ''));
            $taxMode = mb_strtolower(trim((string) ($row['tax_mode'] ?? '')));
            $name = trim((string) ($row['name'] ?? ''));
            $phone = trim((string) ($row['phone'] ?? ''));
            $address = trim((string) ($row['address'] ?? ''));
            $username = trim((string) ($row['pppoe_username'] ?? ''));
            $password = (string) ($row['pppoe_password'] ?? '');
            $dueDay = filter_var(
                $row['due_day'] ?? null,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1, 'max_range' => 28]]
            );
            $status = mb_strtolower(trim((string) ($row['status'] ?? 'active')));

            if ($routerName === '' || $packageName === '' || $taxMode === '' || $name === '' || $username === '' || $password === '' || $dueDay === false) {
                $errors[] = 'Baris '.$excelRow.': router, package, tax_mode, name, pppoe_username, pppoe_password, dan due_day wajib diisi.';
                continue;
            }

            if (mb_strlen($name) > 255 || mb_strlen($username) > 100 || mb_strlen($phone) > 30) {
                $errors[] = 'Baris '.$excelRow.': panjang nama, username, atau nomor telepon melebihi batas.';
                continue;
            }

            if (mb_strlen($password) < 6 || preg_match('/^\*+$/', $password)) {
                $errors[] = 'Baris '.$excelRow.': password PPPoE minimal 6 karakter dan tidak boleh hanya berupa tanda *.';
                continue;
            }

            if (!in_array($taxMode, ['none', 'inclusive', 'exclusive'], true)) {
                $errors[] = 'Baris '.$excelRow.': tax_mode harus none, inclusive, atau exclusive.';
                continue;
            }

            $monthlyPriceOverride = null;
            if ($monthlyPriceOverrideRaw !== '') {
                $price = preg_replace('/\s+/', '', $monthlyPriceOverrideRaw);
                $price = preg_replace('/^rp/i', '', $price);

                $validPricePattern = '/^(?:\d+(?:[.,]\d+)?|\d{1,3}(?:\.\d{3})+(?:,\d+)?|\d{1,3}(?:,\d{3})+(?:\.\d+)?)$/';

                if (!preg_match($validPricePattern, $price)) {
                    $errors[] = 'Baris '.$excelRow.': monthly_price_override harus kosong atau nominal angka yang valid.';
                    continue;
                }

                $lastComma = strrpos($price, ',');
                $lastDot = strrpos($price, '.');

                if ($lastComma !== false && $lastDot !== false) {
                    if ($lastComma > $lastDot) {
                        $price = str_replace('.', '', $price);
                        $price = str_replace(',', '.', $price);
                    } else {
                        $price = str_replace(',', '', $price);
                    }
                } elseif ($lastComma !== false) {
                    $afterComma = strlen($price) - $lastComma - 1;
                    $price = $afterComma === 3
                        ? str_replace(',', '', $price)
                        : str_replace(',', '.', $price);
                } elseif ($lastDot !== false) {
                    $afterDot = strlen($price) - $lastDot - 1;
                    $price = $afterDot === 3
                        ? str_replace('.', '', $price)
                        : $price;
                }

                if (!is_numeric($price) || (float) $price < 0) {
                    $errors[] = 'Baris '.$excelRow.': monthly_price_override harus kosong atau angka nol/positif.';
                    continue;
                }

                $monthlyPriceOverride = (float) $price;
            }

            if (!in_array($status, ['active', 'inactive'], true)) {
                $errors[] = 'Baris '.$excelRow.': status harus active atau inactive.';
                continue;
            }

            $routerKey = mb_strtolower($routerName);
            $packageKey = mb_strtolower($packageName);
            $usernameKey = mb_strtolower($username);

            $routerMatches = $routers->get($routerKey, collect());
            if ($routerMatches->count() !== 1) {
                $errors[] = 'Baris '.$excelRow.': router tidak tersedia dalam akses Anda, nonaktif, atau namanya ambigu.';
                continue;
            }
            $matchedRouter = $routerMatches->first();
            if (!CustomerAccessScope::routerSupportsArea((int)$matchedRouter->id,(int)$area->id)) {
                $errors[] = 'Baris '.$excelRow.': router tidak terhubung ke wilayah tujuan impor.';
                continue;
            }
            $packageMatches = $packages->get($packageKey, collect())->filter(
                fn ($package) => $package->routers->contains('id', $matchedRouter->id)
            );
            if ($packageMatches->count() !== 1) {
                $errors[] = 'Baris '.$excelRow.': paket tidak tersedia pada router pilihan atau namanya ambigu pada router tersebut.';
                continue;
            }
            $matchedPackage = $packageMatches->first();

            if (isset($existingUsernames[$usernameKey])) {
                $errors[] = 'Baris '.$excelRow.': username PPPoE "'.$username.'" sudah terdaftar.';
                continue;
            }

            if (isset($seenUsernames[$usernameKey])) {
                $errors[] = 'Baris '.$excelRow.': username PPPoE "'.$username.'" duplikat dalam file Excel.';
                continue;
            }

            $seenUsernames[$usernameKey] = true;

            $validRows[] = [
                'area_id' => $area->id,
                'router_id' => $matchedRouter->id,
                'internet_package_id' => $matchedPackage->id,
                'monthly_price_override' => $monthlyPriceOverride,
                'tax_mode' => $taxMode,
                'customer_code' => 'CUST-'.strtoupper(Str::random(8)),
                'name' => $name,
                'phone' => $phone !== '' ? $phone : null,
                'address' => $address !== '' ? $address : null,
                'pppoe_username' => $username,
                'pppoe_password' => $password,
                'due_day' => (int) $dueDay,
                'status' => $status,
                'mikrotik_sync_status' => 'pending',
            ];
        }

        if ($validRows === []) {
            return back()
                ->withErrors(['file' => 'Tidak ada baris yang dapat diimpor.'])
                ->with('import_errors', $errors);
        }

        DB::transaction(function () use ($validRows) {
            foreach ($validRows as $row) {
                Customer::create($row);
            }
        });

        $message = count($validRows).' pelanggan berhasil diimpor ke wilayah '.$area->name.' tanpa push ke MikroTik.';

        return redirect()
            ->route('customers.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}
