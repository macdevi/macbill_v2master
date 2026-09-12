<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Invoice;
use App\Services\BillingTaxService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateDailyInvoices extends Command
{
    protected $signature = 'billing:generate-daily';

    protected $description = 'Generate invoice harian untuk pelanggan yang jatuh tempo hari ini';

    public function handle(BillingTaxService $billingTax): int
    {
        $now = Carbon::now();
        $period = $now->format('Ym');
        $todayDay = $now->day;
        $count = 0;

        foreach (
            Customer::with('internetPackage')
                ->where('status', '!=', 'inactive')
                ->where('due_day', $todayDay)
                ->get() as $customer
        ) {
            if (
                Invoice::where('customer_id', $customer->id)
                    ->whereRaw("strftime('%Y%m', billing_date) = ?", [$period])
                    ->exists()
            ) {
                continue;
            }

            $billingDate = $now->copy()->startOfMonth();
            $dueDate = $billingDate->copy()
                ->day(min($customer->due_day, $billingDate->daysInMonth));

            $tax = $billingTax->calculate($customer);

            Invoice::create([
                'customer_id' => $customer->id,
                'invoice_number' => 'INV-' . $period . '-' . str_pad((string) $customer->id, 6, '0', STR_PAD_LEFT),
                'billing_date' => $billingDate,
                'due_date' => $dueDate,
                'service_amount' => $tax['service_amount'],
                'subtotal' => $tax['subtotal'],
                'tax_name' => $tax['tax_name'],
                'tax_rate' => $tax['tax_rate'],
                'tax_amount' => $tax['tax_amount'],
                'tax_mode' => $tax['tax_mode'],
                'gross_amount' => $tax['gross_amount'],
                'credit_used' => 0,
                'amount' => $tax['gross_amount'],
                'status' => 'unpaid',
            ]);

            $count++;
        }

        $this->info("Invoice daily generation: {$count} invoice dibuat.");

        return self::SUCCESS;
    }
}
