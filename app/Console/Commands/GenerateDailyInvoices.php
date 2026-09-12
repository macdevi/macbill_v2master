<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\BillingTaxService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class GenerateDailyInvoices extends Command
{
    protected $signature = 'billing:generate-daily {--date= : Tanggal proses dengan format YYYY-MM-DD, hanya untuk testing}';

    protected $description = 'Membuat invoice harian sesuai tanggal tagihan pelanggan, termasuk tagihan tertinggal bulan berjalan';

    public function handle(BillingTaxService $billingTax): int
    {
        $dateOption = $this->option('date');

        try {
            $today = $dateOption
                ? Carbon::createFromFormat('Y-m-d', $dateOption)->startOfDay()
                : Carbon::today();
        } catch (\Throwable) {
            $this->error('Format --date tidak valid. Gunakan YYYY-MM-DD, contoh: --date=2026-09-15');

            return self::FAILURE;
        }

        $periodDate = $today->copy()->startOfMonth();
        $period = $periodDate->format('Ym');

        $created = 0;
        $skipped = 0;
        $creditApplied = 0;
        $autoPaid = 0;
        $failed = 0;

        Customer::query()
            ->with('internetPackage')
            ->where('status', '!=', 'inactive')
            ->orderBy('id')
            ->chunkById(100, function ($customers) use (
                $today,
                $periodDate,
                $period,
                $billingTax,
                &$created,
                &$skipped,
                &$creditApplied,
                &$autoPaid,
                &$failed
            ): void {
                foreach ($customers as $customer) {
                    $billingDay = min(
                        max((int) $customer->due_day, 1),
                        $periodDate->daysInMonth
                    );

                    if ($today->day < $billingDay) {
                        $skipped++;

                        continue;
                    }

                    try {
                        $result = DB::transaction(function () use (
                            $customer,
                            $periodDate,
                            $period,
                            $billingDay,
                            $billingTax
                        ): ?array {
                            $customer = Customer::query()
                                ->with('internetPackage')
                                ->lockForUpdate()
                                ->findOrFail($customer->id);

                            $alreadyExists = Invoice::query()
                                ->where('customer_id', $customer->id)
                                ->whereDate('billing_period', $periodDate->toDateString())
                                ->exists();

                            if ($alreadyExists) {
                                return null;
                            }

                            $billingDate = $periodDate->copy()->day($billingDay);
                            $tax = $billingTax->calculate($customer);

                            $availableCredit = round((float) $customer->credit_balance, 2);
                            $grossAmount = round((float) $tax['gross_amount'], 2);
                            $creditUsed = round(min($availableCredit, $grossAmount), 2);
                            $finalAmount = round(max($grossAmount - $creditUsed, 0), 2);

                            $invoice = Invoice::create([
                                'customer_id' => $customer->id,
                                'billing_period' => $periodDate->toDateString(),
                                'invoice_number' => 'INV-' . $period . '-' . str_pad((string) $customer->id, 6, '0', STR_PAD_LEFT),
                                'billing_date' => $billingDate,
                                'due_date' => $billingDate,
                                'service_amount' => $tax['service_amount'],
                                'subtotal' => $tax['subtotal'],
                                'tax_name' => $tax['tax_name'],
                                'tax_rate' => $tax['tax_rate'],
                                'tax_amount' => $tax['tax_amount'],
                                'tax_mode' => $tax['tax_mode'],
                                'gross_amount' => $tax['gross_amount'],
                                'credit_used' => $creditUsed,
                                'amount' => $finalAmount,
                                'status' => $finalAmount <= 0 ? 'paid' : 'unpaid',
                            ]);

                            if ($creditUsed > 0) {
                                $customer->decrement('credit_balance', $creditUsed);

                                if ($finalAmount <= 0) {
                                    Payment::create([
                                        'invoice_id' => $invoice->id,
                                        'method' => 'cash',
                                        'amount' => $creditUsed,
                                        'proof_path' => null,
                                        'status' => 'verified',
                                        'notes' => 'Pembayaran otomatis dari titip saldo pelanggan.',
                                        'paid_at' => now(),
                                        'verified_at' => now(),
                                    ]);
                                }
                            }

                            return [
                                'credit_used' => $creditUsed,
                                'auto_paid' => $invoice->status === 'paid',
                            ];
                        });

                        if ($result === null) {
                            $skipped++;

                            continue;
                        }

                        $created++;

                        if ($result['credit_used'] > 0) {
                            $creditApplied++;
                        }

                        if ($result['auto_paid']) {
                            $autoPaid++;
                        }
                    } catch (QueryException $exception) {
                        if (str_contains(strtolower($exception->getMessage()), 'unique')) {
                            $skipped++;

                            continue;
                        }

                        $failed++;
                        report($exception);
                        $this->warn("Gagal membuat invoice customer ID {$customer->id}: {$exception->getMessage()}");
                    } catch (\Throwable $exception) {
                        $failed++;
                        report($exception);
                        $this->warn("Gagal membuat invoice customer ID {$customer->id}: {$exception->getMessage()}");
                    }
                }
            });

        $this->info(
            "Generate invoice {$periodDate->translatedFormat('F Y')} selesai. "
            . "Dibuat: {$created}; Dilewati: {$skipped}; "
            . "Pakai titip saldo: {$creditApplied}; Otomatis lunas: {$autoPaid}; Gagal: {$failed}."
        );

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
