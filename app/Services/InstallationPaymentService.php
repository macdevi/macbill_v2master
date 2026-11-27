<?php

namespace App\Services;

use App\Models\Installation;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class InstallationPaymentService
{
    public function visibleIncomeQuery(?User $actor): Builder
    {
        return DB::table('installation_payments')
            ->where('status', 'verified')
            ->where('is_test_data', false)
            ->when(!$actor?->isSuperAdmin(), function (Builder $query) use ($actor) {
                $query->whereIn('area_id', $actor?->activeAreaIds() ?? []);
            });
    }

    public function record(
        Installation $installation,
        User $actor,
        bool $isTestData = false
    ): ?int {
        abort_unless(
            in_array($actor->role, ['super_admin', 'admin'], true), 403
        );
        abort_unless(!$isTestData || $actor->isSuperAdmin(), 403);

        return DB::transaction(function () use ($installation, $actor, $isTestData) {
            $note = Installation::whereKey($installation->id)
                ->lockForUpdate()->firstOrFail();

            abort_unless(
                $actor->isSuperAdmin() ||
                $actor->activeAreaIds()->contains((int) $note->area_id),
                403
            );

            $amount = (float) $note->paid_amount;
            if ($amount === 0.0) {
                return null;
            }

            if ($amount < 0 || !$note->paid_at || !$note->customer_id) {
                throw new \RuntimeException('Data pembayaran pemasangan tidak lengkap.');
            }

            $method = match ($note->payment_method) {
                'cash' => 'cash',
                'transfer' => 'bank_transfer',
                default => throw new \RuntimeException('Metode pembayaran PSB tidak valid.'),
            };

            $existing = DB::table('installation_payments')
                ->where('original_installation_id', $note->id)->first();

            if ($existing) {
                if (
                    $existing->receipt_number !== $note->receipt_number ||
                    (float) $existing->amount !== $amount ||
                    $existing->method !== $method ||
                    (bool) $existing->is_test_data !== $isTestData
                ) {
                    throw new \RuntimeException('Catatan pembayaran PSB berbeda dari nota.');
                }
                return (int) $existing->id;
            }

            return DB::table('installation_payments')->insertGetId([
                'installation_id' => $note->id,
                'original_installation_id' => $note->id,
                'receipt_number' => $note->receipt_number,
                'original_customer_id' => $note->customer_id,
                'area_id' => $note->area_id,
                'customer_name' => $note->customer_name,
                'amount' => $note->paid_amount,
                'method' => $method,
                'paid_at' => $note->paid_at->format('Y-m-d H:i:s'),
                'status' => 'verified',
                'is_test_data' => $isTestData,
                'created_by' => $actor->id,
                'notes' => $isTestData
                    ? 'Data uji; tidak dihitung sebagai pemasukan nyata.'
                    : 'Pembayaran pemasangan dari nota PSB.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
