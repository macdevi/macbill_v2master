<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Setting;

class BillingTaxService
{
    public function calculate(Customer $customer): array
    {
        $serviceAmount = round((float) $customer->effective_monthly_price, 2);

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
}
