<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Setting;

class BillingTaxService
{
    public function calculate(Customer $customer, ?float $amount = null): array
    {
        $serviceAmount = round($amount ?? (float) $customer->effective_monthly_price, 2);

        $taxEnabled = filter_var(
            Setting::value('billing_tax_enabled', '1'),
            FILTER_VALIDATE_BOOLEAN
        );

        $taxName = trim((string) Setting::value('billing_tax_name', 'PPN'));
        $taxRate = max(0, round((float) Setting::value('billing_tax_rate', '11.00'), 4));

        $taxMode = in_array($customer->tax_mode, ['none', 'inclusive', 'exclusive'], true)
            ? $customer->tax_mode
            : 'none';

        if (! $taxEnabled || $taxMode === 'none' || $taxRate <= 0) {
            return [
                'service_amount' => $serviceAmount,
                'subtotal' => $serviceAmount,
                'tax_name' => null,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'tax_mode' => 'none',
                'gross_amount' => $serviceAmount,
            ];
        }

        $rateDecimal = $taxRate / 100;

        if ($taxMode === 'inclusive') {
            $subtotal = round($serviceAmount / (1 + $rateDecimal), 2);
            $taxAmount = round($serviceAmount - $subtotal, 2);
            $grossAmount = $serviceAmount;
        } else {
            $subtotal = $serviceAmount;
            $taxAmount = round($subtotal * $rateDecimal, 2);
            $grossAmount = round($subtotal + $taxAmount, 2);
        }

        return [
            'service_amount' => $serviceAmount,
            'subtotal' => $subtotal,
            'tax_name' => $taxName !== '' ? $taxName : 'Pajak',
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'tax_mode' => $taxMode,
            'gross_amount' => $grossAmount,
        ];
    }

    public function calculateForBillingPeriod(
        Customer $customer,
        string $issueMonth
    ): ?array {
        if ($customer->status !== 'active') {
            return null;
        }

        $billing = new InstallationBillingService();

        if ($customer->billing_resumed_on !== null) {
            $activation = $customer->billing_resumed_on->toDateString();
            $firstBilling = $billing->firstBillingOn(
                $activation, (int) $customer->due_day
            );
        } elseif ($customer->service_activated_on !== null) {
            $activation = $customer->service_activated_on->toDateString();

            if ($customer->first_billing_on === null) {
                throw new \LogicException('Tanggal tagihan pertama belum tersedia.');
            }

            $firstBilling = $customer->first_billing_on->toDateString();
        } else {
            return $this->calculate($customer);
        }

        $plan = $billing->period(
            $activation,
            $firstBilling,
            (int) $customer->due_day,
            $issueMonth,
            (float) $customer->effective_monthly_price
        );

        if ($plan === null) {
            return null;
        }

        return array_merge(
            $plan,
            $this->calculate($customer, (float) $plan['service_amount'])
        );
    }
}
