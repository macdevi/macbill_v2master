<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerAccessScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class CustomerDueDayExcelController extends Controller
{
    private const KEY = 'customer_due_day_preview_v1';

    public function index(Request $request)
    {
        return view('customers.update-due-day', [
            'preview' => $request->session()->get(self::KEY),
        ]);
    }

    private function customers()
    {
        return CustomerAccessScope::customers(Customer::query());
    }

    public function preview(Request $request)
    {
        $request->session()->forget(self::KEY);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        try {
            // Baca header mentah supaya tidak bergantung pada heading formatter.
            $sheets = Excel::toCollection(null, $request->file('file'));
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors([
                'file' => 'File tidak dapat dibaca. Gunakan hasil Export Excel aplikasi.',
            ]);
        }

        if ($sheets->count() !== 1) {
            return back()->withErrors([
                'file' => 'Gunakan file dengan tepat satu sheet pelanggan.',
            ]);
        }

        $rows = $sheets->first();
        if (!$rows || $rows->count() < 2) {
            return back()->withErrors(['file' => 'File tidak berisi data pelanggan.']);
        }

        if ($rows->count() > 5001) {
            return back()->withErrors(['file' => 'Maksimal 5000 pelanggan per upload.']);
        }

        $normalize = static fn ($value) => trim(
            preg_replace('/[^a-z0-9]+/', '_', strtolower(trim((string) $value))),
            '_'
        );

        $headers = $rows->first()->map($normalize)->all();
        $codeIndexes = array_keys($headers, 'kode_pelanggan', true);
        $dayIndexes = array_keys($headers, 'tanggal_jatuh_tempo', true);

        if (count($codeIndexes) !== 1 || count($dayIndexes) !== 1) {
            return back()->withErrors([
                'file' => 'Baris pertama wajib memiliki tepat satu kolom Kode Pelanggan dan Tanggal Jatuh Tempo.',
            ]);
        }

        $codeIndex = $codeIndexes[0];
        $dayIndex = $dayIndexes[0];
        $seen = [];
        $changes = [];
        $errors = [];
        $unchanged = 0;

        foreach ($rows->slice(1) as $index => $row) {
            if ($row->every(fn ($value) => $value === null || trim((string) $value) === '')) {
                continue;
            }

            $line = $index + 1;
            $code = trim((string) ($row[$codeIndex] ?? ''));
            $rawDay = trim((string) ($row[$dayIndex] ?? ''));

            if ($code === '' || !preg_match('/^\d{1,2}$/D', $rawDay)
                || (int) $rawDay < 1 || (int) $rawDay > 28) {
                $errors[] = "Baris $line: kode wajib diisi dan jatuh tempo harus angka 1 sampai 28.";
                continue;
            }

            $key = strtolower($code);
            if (isset($seen[$key])) {
                $errors[] = "Baris $line: kode pelanggan $code berulang.";
                continue;
            }
            $seen[$key] = true;

            $matches = $this->customers()->where('customer_code', $code)->limit(2)->get();

            if ($matches->count() !== 1) {
                $errors[] = "Baris $line: kode $code tidak ditemukan dalam akses Anda atau tidak unik.";
                continue;
            }

            $customer = $matches->first();
            $id = (int) $customer->id;

            if (isset($changes[$id])) {
                $errors[] = "Baris $line: pelanggan yang sama muncul lebih dari sekali.";
                continue;
            }

            if ((int) $customer->due_day === (int) $rawDay) {
                $unchanged++;
                continue;
            }

            $changes[$id] = [
                'id' => $id,
                'code' => $customer->customer_code,
                'name' => $customer->name,
                'old' => (int) $customer->due_day,
                'new' => (int) $rawDay,
            ];
        }

        if ($errors !== []) {
            return back()->withErrors(array_merge(
                ['file' => 'Upload ditolak seluruhnya. Belum ada data yang diubah.'],
                array_slice($errors, 0, 50),
                count($errors) > 50
                    ? ['more' => 'Hanya 50 kesalahan pertama ditampilkan.']
                    : []
            ));
        }

        if ($changes === []) {
            return back()->with('status', 'Tidak ada perubahan jatuh tempo yang perlu disimpan.');
        }

        ksort($changes);

        $request->session()->put(self::KEY, [
            'user_id' => (int) $request->user()->id,
            'expires' => time() + 1800,
            'token' => bin2hex(random_bytes(32)),
            'unchanged' => $unchanged,
            'changes' => array_values($changes),
        ]);

        return redirect()->route('customers.due-day-excel');
    }

    public function store(Request $request)
    {
        $request->validate(['token' => ['required', 'string']]);
        $preview = $request->session()->get(self::KEY);

        if (!$preview
            || $preview['user_id'] !== (int) $request->user()->id
            || $preview['expires'] < time()
            || !hash_equals($preview['token'], $request->input('token'))) {
            return back()->withErrors([
                'preview' => 'Preview tidak valid atau kedaluwarsa. Upload ulang file.',
            ]);
        }

        DB::transaction(function () use ($preview) {
            foreach ($preview['changes'] as $change) {
                $customer = $this->customers()
                    ->whereKey($change['id'])
                    ->lockForUpdate()
                    ->first();

                if (!$customer
                    || $customer->customer_code !== $change['code']
                    || (int) $customer->due_day !== $change['old']) {
                    throw ValidationException::withMessages([
                        'preview' => 'Data atau akses berubah sejak preview. Semua perubahan dibatalkan. Upload ulang file.',
                    ]);
                }

                // Query langsung: hanya due_day, tanpa save event model.
                DB::table($customer->getTable())
                    ->where($customer->getKeyName(), $customer->getKey())
                    ->update(['due_day' => $change['new']]);
            }
        });

        $request->session()->forget(self::KEY);

        return redirect()->route('customers.due-day-excel')->with(
            'status',
            count($preview['changes']).' pelanggan berhasil diperbarui. Invoice lama tidak diubah.'
        );
    }
}
