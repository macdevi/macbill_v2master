<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Installation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RouterOS\Client;
use RouterOS\Query;

class InstallationCancellationService
{
    private function reject(string $message): void
    {
        throw ValidationException::withMessages(['cancellation' => $message]);
    }

    public function cancel(
        int $installationId,
        User $actor,
        string $target,
        string $reason,
        string $confirmation,
        ?string $ip,
        array $refund = []
    ): void {
        if (!in_array($target, ['billing', 'billing_mikrotik'], true)) {
            $this->reject('Target pembatalan tidak valid.');
        }

        DB::transaction(function () use (
            $installationId, $actor, $target, $reason, $confirmation, $ip, $refund
        ) {
            abort_unless(
                in_array($actor->role, ['super_admin', 'admin'], true), 403
            );

            $note = Installation::whereKey($installationId)
                ->lockForUpdate()->firstOrFail();

            abort_unless(
                $actor->isSuperAdmin() ||
                $actor->activeAreaIds()->contains((int) $note->area_id),
                403
            );

            if (!hash_equals($note->receipt_number, $confirmation)) {
                $this->reject('Nomor nota konfirmasi tidak sesuai.');
            }

            if (!$note->customer_id) {
                $this->reject('Pelanggan sudah tidak ada. Nota tidak dibatalkan otomatis.');
            }

            $customer = Customer::whereKey($note->customer_id)
                ->lockForUpdate()->firstOrFail();

            abort_unless(
                $actor->isSuperAdmin() ||
                $actor->activeAreaIds()->contains((int) $customer->area_id),
                403
            );

            if ((int) $customer->area_id !== (int) $note->area_id) {
                $this->reject('Area pelanggan dan nota berbeda. Periksa terlebih dahulu.');
            }

            $isTestData = (bool) ($refund['is_test_data'] ?? false);
            abort_unless(!$isTestData || $actor->isSuperAdmin(), 403);

            $paidCents = $this->cents((string) $note->paid_amount);
            $refundCents = $this->cents((string) ($refund['amount'] ?? '0'));
            $refundMethod = $refund['method'] ?? null;
            $refundReference = $refund['reference'] ?? null;

            if ($paidCents === 0 && $note->paid_at !== null) {
                $this->reject('Nominal pembayaran nol tetapi tanggal pembayaran terisi.');
            }

            if ($isTestData) {
                if ($refundCents !== 0) {
                    $this->reject('Data uji harus memakai refund Rp 0, bukan pengeluaran nyata.');
                }
                $refundMethod = null;
                $refundReference = null;
            } else {
                if ($refundCents !== $paidCents) {
                    $this->reject('Pembatalan permanen memerlukan refund penuh sesuai pembayaran diterima.');
                }
                if ($paidCents > 0) {
                    if (!in_array($refundMethod, ['cash', 'bank_transfer'], true)) {
                        $this->reject('Pilih metode pengembalian tunai atau transfer.');
                    }
                    if (!(bool) ($refund['confirmed'] ?? false)) {
                        $this->reject('Konfirmasikan bahwa uang benar-benar sudah dikembalikan.');
                    }
                } else {
                    $refundMethod = null;
                    $refundReference = null;
                }
            }

            $existingPayment = DB::table('installation_payments')
                ->where('original_installation_id', $note->id)->first();

            if ($existingPayment &&
                (bool) $existingPayment->is_test_data !== $isTestData) {
                $this->reject('Penanda data uji berbeda dari catatan pembayaran awal.');
            }

            if ((float) $customer->credit_balance != 0.0 ||
                $customer->creditBalanceTransactions()->exists()) {
                $this->reject('Pelanggan memiliki saldo atau riwayat transaksi saldo.');
            }

            if (Installation::where('customer_id', $customer->id)->count() !== 1) {
                $this->reject('Pelanggan harus terkait dengan tepat satu nota PSB.');
            }

            $invoices = $customer->invoices()->lockForUpdate()->get();
            $invoiceIds = $invoices->pluck('id')->all();

            if ($invoices->contains(fn ($invoice) =>
                !in_array($invoice->status, ['unpaid', 'isolated'], true) ||
                (float) $invoice->credit_used != 0.0
            )) {
                $this->reject('Ada invoice berstatus lunas/tidak dikenal atau menggunakan saldo.');
            }

            if (DB::table('payments')->whereIn('invoice_id', $invoiceIds)->exists()) {
                $this->reject('Ada riwayat pembayaran invoice. Pembatalan permanen ditolak.');
            }

            $snapshot = [
                'actor_id' => $actor->id,
                'actor_name' => $actor->name,
                'installation_id' => $note->id,
                'receipt_number' => $note->receipt_number,
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'pppoe_username' => $customer->pppoe_username,
                'router_id' => $customer->router_id,
                'invoice_ids' => $invoiceIds,
                'invoice_numbers' => $invoices->pluck('invoice_number')->all(),
                'installation_total' => $note->total_amount,
                'target' => $target,
                'reason' => $reason,
            ];

            // Untuk nota lama, catat pembayaran awal terlebih dahulu.
            // Seluruhnya tetap dalam transaksi pembatalan yang sama.
            $paymentId = app(InstallationPaymentService::class)
                ->record($note, $actor, $isTestData);

            $expense = null;
            if (!$isTestData && $refundCents > 0) {
                $expense = \App\Models\Expense::create([
                    'created_by' => $actor->id,
                    'area_id' => $note->area_id,
                    'title' => 'Refund pemasangan '.$note->receipt_number,
                    'category' => 'Lainnya',
                    'description' => 'Pengembalian penuh pembayaran pemasangan. '.
                        'Pelanggan: '.$customer->name.
                        '. Alasan: '.$reason.
                        ($refundReference ? '. Referensi: '.$refundReference : ''),
                    'vendor' => $customer->name,
                    'amount' => number_format($refundCents / 100, 2, '.', ''),
                    'payment_method' => $refundMethod,
                    'status' => 'posted',
                    'expense_date' => now()->toDateString(),
                ]);
            }

            $archiveId = DB::table('installation_cancellations')->insertGetId([
                'original_installation_id' => $note->id,
                'receipt_number' => $note->receipt_number,
                'original_customer_id' => $customer->id,
                'original_area_id' => $note->area_id,
                'original_router_id' => $customer->router_id,
                'customer_name' => $customer->name,
                'pppoe_username' => $customer->pppoe_username,
                'installation_total' => $note->total_amount,
                'payment_received' => $note->paid_amount,
                'original_payment_method' => $note->payment_method,
                'original_paid_at' => $note->paid_at?->format('Y-m-d H:i:s'),
                'refund_amount' => number_format($refundCents / 100, 2, '.', ''),
                'refund_method' => $refundMethod,
                'refund_reference' => $refundReference,
                'refunded_at' => (!$isTestData && $refundCents > 0) ? now() : null,
                'is_test_data' => $isTestData,
                'cancel_target' => $target,
                'reason' => $reason,
                'actor_id' => $actor->id,
                'actor_name' => $actor->name,
                'ip_address' => $ip,
                'invoice_snapshot' => json_encode(
                    $invoices->toArray(), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
                ),
                'cancelled_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $snapshot['archive_id'] = $archiveId;
            $snapshot['installation_payment_id'] = $paymentId;
            $snapshot['payment_received'] = $note->paid_amount;
            $snapshot['refund_amount'] = number_format($refundCents / 100, 2, '.', '');
            $snapshot['refund_method'] = $refundMethod;
            $snapshot['refund_reference'] = $refundReference;
            $snapshot['refund_expense_id'] = $expense?->id;
            $snapshot['is_test_data'] = $isTestData;

            // Membuat write lock SQLite sebelum operasi eksternal.
            ActivityLog::create([
                'action' => 'installation.cancelled',
                'description' => json_encode(
                    $snapshot, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
                ),
                'ip_address' => $ip,
            ]);

            if ($target === 'billing_mikrotik') {
                $this->removeAccount($customer);
            }

            $deletedInvoices = DB::table('invoices')
                ->where('customer_id', $customer->id)
                ->whereIn('id', $invoiceIds)->delete();

            if ($deletedInvoices !== count($invoiceIds)) {
                throw new \RuntimeException('Jumlah invoice berubah saat pembatalan.');
            }

            if (DB::table('installations')->where('id', $note->id)->delete() !== 1 ||
                DB::table('customers')->where('id', $customer->id)->delete() !== 1) {
                throw new \RuntimeException('Penghapusan nota/pelanggan gagal.');
            }
        }, 1);
    }

    public function removeAccount(Customer $customer): void
    {
        $customer->loadMissing('router');
        if (!$customer->router) {
            throw new \RuntimeException('Router pelanggan tidak ditemukan.');
        }

        $router = $customer->router;
        $username = trim((string) $customer->pppoe_username);
        if ($username === '') {
            throw new \RuntimeException('Username PPPoE kosong.');
        }

        $client = new Client([
            'host' => $router->host,
            'user' => $router->username,
            'pass' => $router->password,
            'port' => $router->port,
            'timeout' => config('mikrotik.timeout', 5),
        ]);

        $read = function ($query) use ($client): array {
            $rows = $client->query($query)->read();
            $this->assertResponse($rows);
            return $rows;
        };

        $marker = 'macbilling_v2 customer #'.$customer->id;

        $owned = $read(
            (new Query('/ppp/secret/print'))->where('comment', $marker)
        );

        if (count($owned) > 1) {
            throw new \RuntimeException('Lebih dari satu akun memakai penanda pelanggan.');
        }

        $named = $read(
            (new Query('/ppp/secret/print'))->where('name', $username)
        );

        if (!$owned) {
            if ($named) {
                throw new \RuntimeException('Nama akun cocok tetapi kepemilikannya tidak terverifikasi.');
            }

            $active = $read(
                (new Query('/ppp/active/print'))->where('name', $username)
            );
            if ($active) {
                throw new \RuntimeException('Secret tidak ada tetapi sesi aktif masih ditemukan. Periksa manual.');
            }
            return;
        }

        $secret = $owned[0];
        $actualName = (string) ($secret['name'] ?? '');
        $secretId = (string) ($secret['.id'] ?? '');

        if ($actualName === '' || $secretId === '' ||
            ($secret['comment'] ?? '') !== $marker) {
            throw new \RuntimeException('Identitas akun router tidak lengkap.');
        }

        foreach ($named as $row) {
            if (($row['.id'] ?? '') !== $secretId) {
                throw new \RuntimeException('Username pelanggan menunjuk akun router lain.');
            }
        }

        if (Customer::where('router_id', $router->id)
            ->where('pppoe_username', $actualName)
            ->where('id', '!=', $customer->id)->exists()) {
            throw new \RuntimeException('Nama akun router juga dipakai pelanggan lain.');
        }

        $sessions = $read(
            (new Query('/ppp/active/print'))->where('name', $actualName)
        );

        foreach ($sessions as $session) {
            if (empty($session['.id']) ||
                ($session['name'] ?? '') !== $actualName) {
                throw new \RuntimeException('Identitas sesi aktif tidak lengkap.');
            }
        }

        // Secret dihapus terlebih dahulu untuk mencegah login ulang lokal.
        $read(
            (new Query('/ppp/secret/remove'))->equal('.id', $secretId)
        );

        foreach ($sessions as $session) {
            $read(
                (new Query('/ppp/active/remove'))
                    ->equal('.id', $session['.id'])
            );
        }

        $remainingSecrets = $read(
            (new Query('/ppp/secret/print'))->where('name', $actualName)
        );
        $remainingSessions = $read(
            (new Query('/ppp/active/print'))->where('name', $actualName)
        );

        if ($remainingSecrets || $remainingSessions) {
            throw new \RuntimeException('Akun/sesi masih terdeteksi setelah penghapusan.');
        }
    }

    private function cents(string $value): int
    {
        $value = trim($value);
        if (!preg_match('/^\d{1,9}(?:\.\d{1,2})?$/', $value)) {
            $this->reject('Nominal pembayaran/refund tidak valid.');
        }

        $parts = explode('.', $value, 2);
        return ((int) $parts[0] * 100) +
            (int) str_pad($parts[1] ?? '', 2, '0');
    }

    private function assertResponse(array $response): void
    {
        foreach ($response as $key => $value) {
            if (in_array($key, ['!trap', '!fatal'], true) ||
                (is_string($value) &&
                 in_array($value, ['!trap', '!fatal'], true))) {
                throw new \RuntimeException('RouterOS mengembalikan error API.');
            }
            if (is_array($value)) {
                $this->assertResponse($value);
            }
        }
    }
}
