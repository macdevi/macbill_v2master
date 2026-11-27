<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\InternetPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class CustomerExcelRoundtrip
{
    private const KEY = 'customer_excel_roundtrip_v1';

    private static function customers()
    {
        return CustomerAccessScope::customers(Customer::query());
    }

    private static function fingerprint(Customer $customer): string
    {
        $values = $customer->getRawOriginal();
        ksort($values);

        return hash('sha256', json_encode($values, JSON_THROW_ON_ERROR));
    }

    private static function usernameRule(Customer $customer)
    {
        return Rule::unique($customer->getTable(), 'pppoe_username')
            ->ignore($customer->getKey(), $customer->getKeyName());
    }

    public static function dispatch(Request $request)
    {
        if ($request->has('_excel_update_token')) {
            return self::store($request);
        }

        $request->session()->forget(self::KEY);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        try {
            $sheets = Excel::toCollection(null, $request->file('file'));
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors([
                'file' => 'File tidak dapat dibaca. Gunakan file Excel yang valid.',
            ]);
        }

        $sheet = $sheets->first();

        if (!$sheet || $sheet->isEmpty()) {
            return back()->withErrors(['file' => 'File tidak berisi data.']);
        }

        $normalize = static fn ($value) => trim(
            preg_replace('/[^a-z0-9]+/', '_', strtolower(trim((string) $value))),
            '_'
        );

        $headers = $sheet->first()->map($normalize)->all();

        // Template lama tetap ditangani controller impor yang sudah ada.
        if (!in_array('kode_pelanggan', $headers, true)) {
            if (in_array('nama_pelanggan', $headers, true)
                || in_array('tanggal_jatuh_tempo', $headers, true)) {
                return back()->withErrors([
                    'file' => 'File tampak seperti hasil ekspor, tetapi kolom Kode Pelanggan hilang.',
                ]);
            }

            return null;
        }

        if ($sheets->count() !== 1) {
            return back()->withErrors([
                'file' => 'Update harus memakai file dengan tepat satu sheet pelanggan.',
            ]);
        }

        if ($sheet->count() > 2001) {
            return back()->withErrors([
                'file' => 'Maksimal 2000 baris pelanggan per file update.',
            ]);
        }

        $required = [
            'kode_pelanggan', 'kode_wilayah', 'router', 'paket',
            'harga_custom', 'mode_pajak', 'nama_pelanggan',
            'no_telepon', 'alamat', 'username_pppoe',
            'password_pppoe', 'tanggal_jatuh_tempo', 'status',
        ];

        foreach ($required as $header) {
            if (count(array_keys($headers, $header, true)) !== 1) {
                return back()->withErrors([
                    'file' => "Kolom $header harus ada tepat satu kali. Gunakan hasil Export Excel tanpa mengganti header.",
                ]);
            }
        }

        $positions = array_flip($headers);
        $seenCodes = [];
        $seenIds = [];
        $seenUsernames = [];
        $updates = [];
        $display = [];
        $errors = [];
        $unchanged = 0;

        foreach ($sheet->slice(1) as $index => $row) {
            if ($row->every(fn ($value) => $value === null || trim((string) $value) === '')) {
                continue;
            }

            $line = $index + 1;
            $get = static fn ($key) => $row[$positions[$key]] ?? null;
            $str = static fn ($key) => trim((string) $get($key));
            $code = $str('kode_pelanggan');
            $codeKey = mb_strtolower($code);

            if ($code === '' || isset($seenCodes[$codeKey])) {
                $errors[] = "Baris $line: kode pelanggan kosong atau berulang.";
                continue;
            }
            $seenCodes[$codeKey] = true;

            $matches = self::customers()
                ->with(['area', 'router', 'internetPackage'])
                ->where('customer_code', $code)
                ->limit(2)->get();

            if ($matches->count() !== 1) {
                $errors[] = "Baris $line: kode $code tidak ditemukan dalam akses Anda atau tidak unik.";
                continue;
            }

            $customer = $matches->first();

            if (isset($seenIds[$customer->id])) {
                $errors[] = "Baris $line: pelanggan yang sama muncul lebih dari sekali.";
                continue;
            }
            $seenIds[$customer->id] = true;

            // Wilayah hanya dipindahkan jika Kode Wilayah benar-benar berubah.
            $areaId = $customer->area_id;
            $areaChanged = $str('kode_wilayah') !== (string) $customer->area?->code;

            if ($areaChanged) {
                $areas = CustomerAccessScope::areas()
                    ->where('code', $str('kode_wilayah'))->limit(2)->get();

                if ($areas->count() !== 1) {
                    $errors[] = "Baris $line: wilayah tujuan tidak aktif, tidak tersedia, atau ambigu.";
                    continue;
                }

                $areaId = $areas->first()->id;
            }

            $routerId = $customer->router_id;
            $routerChanged = $str('router') !== (string) $customer->router?->name;

            if ($routerChanged) {
                $routers = CustomerAccessScope::routers()
                    ->where('name', $str('router'))->limit(2)->get();

                if ($routers->count() !== 1) {
                    $errors[] = "Baris $line: router tidak aktif, tidak tersedia, atau ambigu.";
                    continue;
                }

                $routerId = $routers->first()->id;
            }

            $packageId = $customer->internet_package_id;
            $packageChanged = $str('paket') !== (string) $customer->internetPackage?->name;

            if ($areaChanged || $routerChanged || $packageChanged) {
                if (!$areaId || !$routerId
                    || !CustomerAccessScope::routerSupportsArea((int) $routerId, (int) $areaId)) {
                    $errors[] = "Baris $line: router tidak tersedia pada wilayah tujuan.";
                    continue;
                }

                $packages = InternetPackage::query()
                    ->where('active', true)
                    ->where('name', $str('paket'))
                    ->whereHas('routers', fn ($q) => $q->where('routers.id', $routerId))
                    ->limit(2)->get();

                if ($packages->count() !== 1) {
                    $errors[] = "Baris $line: paket tidak aktif, tidak cocok dengan router, atau ambigu.";
                    continue;
                }

                $packageId = $packages->first()->id;
            }

            $rawPrice = $str('harga_custom');

            // Hasil ekspor menyimpan harga sebagai angka, bukan format Rupiah teks.
            if ($rawPrice !== ''
                && !preg_match('/^\d+(?:\.\d{1,2})?$/D', $rawPrice)) {
                $errors[] = "Baris $line: Harga Custom harus kosong atau angka, misalnya 150000.";
                continue;
            }

            $price = $rawPrice === '' ? null : number_format((float) $rawPrice, 2, '.', '');
            $oldPrice = $customer->monthly_price_override === null
                ? null
                : number_format((float) $customer->monthly_price_override, 2, '.', '');

            $password = (string) $get('password_pppoe');
            $oldPassword = (string) $customer->getRawOriginal('pppoe_password');

            if ($password === '') {
                $password = $oldPassword;
            }

            $data = [
                'area_id' => $areaId,
                'router_id' => $routerId,
                'internet_package_id' => $packageId,
                'monthly_price_override' => $price,
                'tax_mode' => mb_strtolower($str('mode_pajak')),
                'name' => $str('nama_pelanggan'),
                'phone' => $str('no_telepon') ?: null,
                'address' => $str('alamat') ?: null,
                'pppoe_username' => $str('username_pppoe'),
                'pppoe_password' => $password,
                'due_day' => $str('tanggal_jatuh_tempo'),
                'status' => mb_strtolower($str('status')),
            ];

            $validator = Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
                'address' => ['nullable', 'string', 'max:65535'],
                'pppoe_username' => [
                    'required', 'string', 'max:100', self::usernameRule($customer),
                ],
                'due_day' => ['required', 'integer', 'between:1,28'],
                'tax_mode' => ['required', Rule::in(['none', 'inclusive', 'exclusive'])],
                'status' => ['required', Rule::in(['active', 'inactive'])],
            ]);

            if ($validator->fails()) {
                $errors[] = "Baris $line: ".implode(' ', $validator->errors()->all());
                continue;
            }

            if ($password !== $oldPassword
                && (mb_strlen($password) < 6 || preg_match('/^\*+$/', $password))) {
                $errors[] = "Baris $line: password baru minimal 6 karakter dan tidak boleh hanya tanda bintang.";
                continue;
            }

            $usernameKey = mb_strtolower($data['pppoe_username']);

            if (isset($seenUsernames[$usernameKey])) {
                $errors[] = "Baris $line: username PPPoE berulang dalam file.";
                continue;
            }
            $seenUsernames[$usernameKey] = true;

            $data['due_day'] = (int) $data['due_day'];
            $changes = [];
            $summary = [];

            foreach ($data as $field => $value) {
                $old = match ($field) {
                    'monthly_price_override' => $oldPrice,
                    'pppoe_password' => $oldPassword,
                    default => $customer->getAttribute($field),
                };

                if (($old === null && $value === null)
                    || ($old !== null && $value !== null && (string) $old === (string) $value)) {
                    continue;
                }

                $changes[$field] = $value;

                $shownOld = $old;
                $shownNew = $value;

                if ($field === 'pppoe_password') {
                    $shownOld = '[disembunyikan]';
                    $shownNew = '[password baru]';
                } elseif ($field === 'area_id') {
                    $shownOld = $customer->area?->code;
                    $shownNew = $str('kode_wilayah');
                } elseif ($field === 'router_id') {
                    $shownOld = $customer->router?->name;
                    $shownNew = $str('router');
                } elseif ($field === 'internet_package_id') {
                    $shownOld = $customer->internetPackage?->name;
                    $shownNew = $str('paket');
                }

                $summary[] = [
                    'field' => $field,
                    'old' => $shownOld,
                    'new' => $shownNew,
                ];
            }

            if ($changes === []) {
                $unchanged++;
                continue;
            }

            $updates[] = [
                'id' => $customer->id,
                'code' => $customer->customer_code,
                'fingerprint' => self::fingerprint($customer),
                'changes' => $changes,
                'area_id' => $areaId,
                'router_id' => $routerId,
                'package_id' => $packageId,
                'check_relations' => $areaChanged || $routerChanged || $packageChanged,
            ];

            $display[] = [
                'code' => $customer->customer_code,
                'name' => $customer->name,
                'changes' => $summary,
            ];
        }

        if ($errors !== []) {
            return back()->withErrors(array_merge(
                ['file' => 'File update ditolak seluruhnya. Belum ada data yang disimpan.'],
                array_slice($errors, 0, 40),
                count($errors) > 40 ? ['more' => 'Hanya 40 kesalahan pertama ditampilkan.'] : []
            ));
        }

        if ($updates === []) {
            return back()->with('excel_update_status', 'Tidak ada perubahan yang perlu disimpan.');
        }

        usort($updates, fn ($a, $b) => $a['id'] <=> $b['id']);

        $request->session()->put(self::KEY, [
            'user_id' => (int) $request->user()->id,
            'expires' => time() + 1800,
            'token' => bin2hex(random_bytes(32)),
            'payload' => Crypt::encryptString(json_encode($updates, JSON_THROW_ON_ERROR)),
            'display' => $display,
            'unchanged' => $unchanged,
        ]);

        return redirect()->route('customers.import.excel');
    }

    private static function store(Request $request)
    {
        $preview = $request->session()->get(self::KEY);
        $token = $request->input('_excel_update_token');

        if (!$preview || !is_string($token)
            || $preview['user_id'] !== (int) $request->user()->id
            || $preview['expires'] < time()
            || !hash_equals($preview['token'], $token)) {
            throw ValidationException::withMessages([
                'file' => 'Preview tidak valid atau kedaluwarsa. Upload ulang file.',
            ]);
        }

        $updates = json_decode(
            Crypt::decryptString($preview['payload']), true, 512, JSON_THROW_ON_ERROR
        );

        DB::transaction(function () use ($updates) {
            foreach ($updates as $update) {
                $customer = self::customers()
                    ->whereKey($update['id'])->lockForUpdate()->first();

                if (!$customer || $customer->customer_code !== $update['code']
                    || !hash_equals($update['fingerprint'], self::fingerprint($customer))) {
                    throw ValidationException::withMessages([
                        'file' => 'Data atau akses berubah sejak preview. Seluruh update dibatalkan. Upload ulang file.',
                    ]);
                }

                if ($update['check_relations']) {
                    $validRouter = CustomerAccessScope::routerSupportsArea(
                        (int) $update['router_id'], (int) $update['area_id']
                    );

                    $validPackage = InternetPackage::query()
                        ->whereKey($update['package_id'])->where('active', true)
                        ->whereHas('routers', fn ($q) => $q->where('routers.id', $update['router_id']))
                        ->exists();

                    if (!$validRouter || !$validPackage) {
                        throw ValidationException::withMessages([
                            'file' => 'Akses wilayah/router/paket berubah. Upload ulang file.',
                        ]);
                    }
                }

                if (array_key_exists('pppoe_username', $update['changes'])) {
                    Validator::make($update['changes'], [
                        'pppoe_username' => [
                            'required', 'string', 'max:100', self::usernameRule($customer),
                        ],
                    ])->validate();
                }

                // Hanya kolom berubah. Tidak memanggil sinkronisasi MikroTik.
                DB::table($customer->getTable())
                    ->where($customer->getKeyName(), $customer->getKey())
                    ->update($update['changes']);
            }
        });

        $request->session()->forget(self::KEY);

        return redirect()->route('customers.import.excel')->with(
            'excel_update_status',
            count($updates).' pelanggan diperbarui. Tidak ada perubahan ke MikroTik atau invoice lama.'
        );
    }
}
