<?php
namespace App\Console\Commands;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\BillingTaxService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateInvoices extends Command
{
    protected $signature='billing:generate';
    protected $description='Generate invoice bulanan pelanggan';
    public function handle(BillingTaxService $billingTax): int
    {
        $now=Carbon::now();$period=$now->format('Ym');
        foreach(Customer::with('internetPackage')->where('status','!=','inactive')->get() as $c){
            if(Invoice::where('customer_id',$c->id)->whereRaw("strftime('%Y%m', billing_date)=?",[$period])->exists())continue;
            $date=$now->copy()->startOfMonth();$due=$date->copy()->day(min($c->due_day,$date->daysInMonth));
            $tax = $billingTax->calculate($c);
            Invoice::create([
                'customer_id' => $c->id,
                'invoice_number' => 'INV-' . $period . '-' . str_pad((string) $c->id, 6, '0', STR_PAD_LEFT),
                'billing_date' => $date,
                'due_date' => $due,
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
        }
        $this->info('Invoice generation selesai.');return self::SUCCESS;
    }
}