<?php
namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Installation;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InstallationBillingCorrectionService
{
    private function reject(string $message): void
    {
        throw ValidationException::withMessages(['correction' => $message]);
    }

    public function authorizeNote(Installation $note, User $actor): void
    {
        abort_unless(in_array($actor->role, ['super_admin', 'admin'], true), 403);
        abort_unless($actor->isSuperAdmin() ||
            $actor->activeAreaIds()->contains((int) $note->area_id), 403);
    }

    private function makePlan(Installation $note, Customer $customer, User $actor,
        int $day, string $reason): array
    {
        $this->authorizeNote($note, $actor);
        abort_unless($actor->isSuperAdmin() ||
            $actor->activeAreaIds()->contains((int) $customer->area_id), 403);
        if ((int) $note->customer_id !== (int) $customer->id ||
            (int) $note->area_id !== (int) $customer->area_id) {
            $this->reject('Relasi atau wilayah PSB dan pelanggan tidak sesuai.');
        }
        if ($day < 1 || $day > 28 || mb_strlen(trim($reason)) < 5 ||
            mb_strlen(trim($reason)) > 500) {
            $this->reject('Tanggal harus 1–28 dan alasan harus 5–500 karakter.');
        }
        if ($customer->status !== 'active' || $customer->billing_resumed_on !== null) {
            $this->reject('Hanya PSB aktif tanpa riwayat mulai ulang penagihan yang dapat dikoreksi.');
        }
        if (Installation::where('customer_id', $customer->id)->count() !== 1) {
            $this->reject('Pelanggan harus memiliki tepat satu nota PSB.');
        }
        if (Invoice::where('customer_id', $customer->id)->exists()) {
            $this->reject('Pelanggan sudah memiliki invoice bulanan. Koreksi PSB otomatis ditolak.');
        }
        $activation = $customer->service_activated_on?->toDateString();
        if (!$activation || !$note->activated_on ||
            $activation !== $note->activated_on->toDateString()) {
            $this->reject('Tanggal aktivasi PSB dan pelanggan tidak sama atau kosong.');
        }
        $today = CarbonImmutable::today();
        if (CarbonImmutable::parse($activation)->greaterThan($today)) {
            $this->reject('Tanggal aktivasi belum tiba.');
        }
        $customer->loadMissing('internetPackage');
        $price = round((float) $customer->effective_monthly_price, 2);
        if (!is_finite($price) || $price < 0 ||
            round((float) $note->monthly_price, 2) !== $price ||
            (string) $note->tax_mode !== (string) $customer->tax_mode) {
            $this->reject('Tarif atau mode pajak PSB berbeda dari pelanggan. Periksa terpisah dahulu.');
        }
        $credit = round((float) $customer->credit_balance, 2);
        if (!is_finite($credit) || $credit < 0) {
            $this->reject('Saldo pelanggan tidak valid.');
        }
        $first = app(InstallationBillingService::class)->firstBillingOn($activation, $day);
        if (CarbonImmutable::parse($first)->lessThan($today->startOfMonth())) {
            $this->reject('Jadwal baru masuk bulan lampau. Fitur ini tidak membuat backfill lintas bulan.');
        }
        $before = [
            'psb_billing_day' => (int) $note->billing_day,
            'psb_first_billing_on' => $note->first_billing_on?->toDateString(),
            'customer_due_day' => (int) $customer->due_day,
            'customer_first_billing_on' => $customer->first_billing_on?->toDateString(),
        ];
        if ($before['psb_billing_day'] === $day && $before['customer_due_day'] === $day &&
            $before['psb_first_billing_on'] === $first &&
            $before['customer_first_billing_on'] === $first) {
            $this->reject('Jadwal PSB dan pelanggan sudah sesuai. Tidak ada koreksi yang diperlukan.');
        }
        $candidate = clone $customer;
        $candidate->due_day = $day;
        $candidate->first_billing_on = $first;
        $tax = app(BillingTaxService::class)->calculateForBillingPeriod(
            $candidate, CarbonImmutable::parse($first)->startOfMonth()->toDateString()
        );
        if ($tax === null) {
            $this->reject('Rencana tagihan pertama tidak dapat dihitung.');
        }
        $gross = round((float) $tax['gross_amount'], 2);
        if (!is_finite($gross) || $gross < 0) {
            $this->reject('Nominal tagihan tidak valid.');
        }
        $createNow = CarbonImmutable::parse($first)->lessThanOrEqualTo($today);
        $creditUsed = round(min($credit, $gross), 2);
        return [
            'installation_id' => (int) $note->id,
            'receipt_number' => $note->receipt_number,
            'customer_id' => (int) $customer->id,
            'customer_name' => $customer->name,
            'area_id' => (int) $customer->area_id,
            'actor_id' => (int) $actor->id,
            'reason' => trim($reason),
            'today' => $today->toDateString(),
            'timezone' => config('app.timezone'),
            'activated_on' => $activation,
            'before' => $before,
            'billing_day' => $day,
            'first_billing_on' => $first,
            'create_now' => $createNow,
            'package_id' => (int) $customer->internet_package_id,
            'tax_mode' => $customer->tax_mode,
            'monthly_price' => $price,
            'credit_balance' => $credit,
            'credit_used' => $creditUsed,
            'amount' => round(max($gross - $creditUsed, 0), 2),
            'tax' => $tax,
        ];
    }

    private function fingerprint(array $plan): string
    {
        return hash('sha256', json_encode($plan,
            JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR));
    }

    public function preview(int $id, User $actor, int $day, string $reason): array
    {
        $note = Installation::findOrFail($id);
        $this->authorizeNote($note, $actor);
        $customer = Customer::findOrFail($note->customer_id);
        $plan = $this->makePlan($note, $customer, $actor, $day, $reason);
        return [
            'plan' => $plan,
            'token' => Crypt::encryptString(json_encode([
                'actor_id' => (int) $actor->id,
                'hash' => $this->fingerprint($plan),
                'expires' => now()->addMinutes(10)->timestamp,
            ], JSON_THROW_ON_ERROR)),
        ];
    }

    public function apply(int $id, User $actor, int $day, string $reason,
        string $token, ?string $ip): array
    {
        try {
            $approved = json_decode(Crypt::decryptString($token), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            $this->reject('Pratinjau tidak valid. Buat pratinjau baru.');
        }
        if ((int) ($approved['actor_id'] ?? 0) !== (int) $actor->id ||
            (int) ($approved['expires'] ?? 0) < now()->timestamp ||
            !is_string($approved['hash'] ?? null)) {
            $this->reject('Pratinjau kedaluwarsa atau pengguna berbeda. Buat pratinjau baru.');
        }
        return DB::transaction(function () use ($id, $actor, $day, $reason, $approved, $ip) {
            // Urutan kunci sama dengan pembatalan PSB: nota, lalu pelanggan.
            $note = Installation::whereKey($id)->lockForUpdate()->firstOrFail();
            $this->authorizeNote($note, $actor);
            $customer = Customer::whereKey($note->customer_id)->lockForUpdate()->firstOrFail();
            $plan = $this->makePlan($note, $customer, $actor, $day, $reason);
            if (!hash_equals($approved['hash'], $this->fingerprint($plan))) {
                $this->reject('Data, nominal, saldo, atau tanggal proses berubah. Buat pratinjau baru.');
            }
            // Audit di transaksi yang sama; pada SQLite ini juga mengambil write lock.
            $audit = ActivityLog::create([
                'action' => 'installation.billing_corrected',
                'description' => json_encode($plan, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'ip_address' => $ip,
            ]);
            $note->update(['billing_day' => $day, 'first_billing_on' => $plan['first_billing_on']]);
            $customer->update(['due_day' => $day, 'first_billing_on' => $plan['first_billing_on']]);
            $invoice = null;
            if ($plan['create_now']) {
                // Periksa lagi sesudah mengambil write lock; tidak mengubah invoice yang sudah ada.
                if (Invoice::where('customer_id', $customer->id)->exists()) {
                    $this->reject('Invoice baru muncul saat koreksi. Muat ulang dan periksa tagihan.');
                }
                $period = CarbonImmutable::parse($plan['tax']['billing_period'])->format('Ym');
                $invoice = Invoice::create([
                    ...$plan['tax'],
                    'customer_id' => $customer->id,
                    'invoice_number' => 'INV-'.$period.'-'.str_pad((string) $customer->id, 6, '0', STR_PAD_LEFT),
                    'credit_used' => $plan['credit_used'],
                    'amount' => $plan['amount'],
                    'status' => $plan['amount'] <= 0 ? 'paid' : 'unpaid',
                ]);
                if ($plan['credit_used'] > 0) {
                    $customer->decrement('credit_balance', $plan['credit_used']);
                    if ($plan['amount'] <= 0) {
                        Payment::create([
                            'invoice_id' => $invoice->id,
                            'method' => 'credit_balance',
                            'amount' => $plan['credit_used'],
                            'proof_path' => null,
                            'status' => 'verified',
                            'notes' => 'Pembayaran otomatis dari titip saldo pelanggan.',
                            'paid_at' => now(),
                            'verified_at' => now(),
                        ]);
                    }
                }
            }
            $plan['invoice_id'] = $invoice?->id;
            $plan['invoice_number'] = $invoice?->invoice_number;
            $plan['actor_name'] = $actor->name;
            $audit->update(['description' => json_encode($plan,
                JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)]);
            return ['invoice_id' => $invoice?->id,
                'invoice_number' => $invoice?->invoice_number,
                'first_billing_on' => $plan['first_billing_on']];
        }, 1);
    }
}
