<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

class InstallationBillingService
{
    public function firstBillingOn(string $activatedOn, int $billingDay): string
    {
        $this->validateDay($billingDay);
        $activation = CarbonImmutable::parse($activatedOn)->startOfDay();
        $boundary = $activation->startOfMonth()->day($billingDay);

        if ($boundary->lessThanOrEqualTo($activation)) {
            $boundary = $activation->startOfMonth()->addMonth()->day($billingDay);
        }

        return $boundary->toDateString();
    }

    public function period(
        string $activatedOn,
        string $firstBillingOn,
        int $billingDay,
        string $issueMonth,
        float $monthlyPrice
    ): ?array {
        $this->validateDay($billingDay);

        if (! is_finite($monthlyPrice) || $monthlyPrice < 0) {
            throw new InvalidArgumentException('Tarif bulanan tidak valid.');
        }

        $activation = CarbonImmutable::parse($activatedOn)->startOfDay();
        $first = CarbonImmutable::parse($firstBillingOn)->startOfDay();
        $issue = CarbonImmutable::parse($issueMonth)->startOfMonth()->day($billingDay);

        if ($first->lessThanOrEqualTo($activation)) {
            throw new InvalidArgumentException('Tagihan pertama harus setelah aktivasi.');
        }

        if ($issue->lessThan($first)) {
            return null;
        }

        $cycleStart = $issue->startOfMonth()->subMonth()->day($billingDay);
        $serviceStart = $activation->greaterThan($cycleStart)
            ? $activation
            : $cycleStart;

        $cycleDays = (int) round($cycleStart->diffInDays($issue, true));
        $chargedDays = (int) round($serviceStart->diffInDays($issue, true));

        if ($chargedDays < 1 || $chargedDays > $cycleDays) {
            throw new InvalidArgumentException('Periode layanan tidak valid.');
        }

        $monthlyPrice = round($monthlyPrice, 2);
        $isProrated = $chargedDays < $cycleDays;
        $serviceAmount = $isProrated
            ? round($monthlyPrice * $chargedDays / $cycleDays, 0)
            : $monthlyPrice;

        return [
            'billing_period' => $issue->startOfMonth()->toDateString(),
            'billing_date' => $issue->toDateString(),
            'due_date' => $issue->toDateString(),
            'service_period_start' => $serviceStart->toDateString(),
            'service_period_end' => $issue->subDay()->toDateString(),
            'is_prorated' => $isProrated,
            'charged_days' => $chargedDays,
            'cycle_days' => $cycleDays,
            'monthly_price_snapshot' => $monthlyPrice,
            'service_amount' => $serviceAmount,
        ];
    }

    private function validateDay(int $day): void
    {
        if ($day < 1 || $day > 28) {
            throw new InvalidArgumentException('Tanggal tagihan harus 1 sampai 28.');
        }
    }
}
