<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Customer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CustomerServiceStateService
{
    public function __construct(private MikroTikService $mikrotik)
    {
    }

    public function isolate(Customer $customer): array
    {
        return Cache::store('file')
            ->lock('customer-service-state-'.$customer->id, 180)
            ->block(5, function () use ($customer) {
                $current = DB::transaction(function () use ($customer) {
                    $current = Customer::lockForUpdate()
                        ->findOrFail($customer->id);

                    if (!in_array($current->status, ['active', 'isolated'], true)) {
                        throw new RuntimeException(
                            'Isolir hanya berlaku untuk pelanggan aktif atau yang sudah diisolir.'
                        );
                    }

                    if ($current->status === 'active') {
                        $current->forceFill([
                            'status' => 'isolated',
                            'isolated_at' => now(),
                        ])->save();

                        $this->log(
                            'customer.isolated',
                            "Billing pelanggan {$current->name} ".
                            "({$current->pppoe_username}) diisolir."
                        );
                    }

                    return $current;
                });

                // Transaksi billing sudah selesai sebelum menghubungi router.
                return MikroTikService::assertSuccessfulResponse(
                    $this->mikrotik->isolate($current)
                );
            });
    }

    public function activate(Customer $customer): bool
    {
        return Cache::store('file')
            ->lock('customer-service-state-'.$customer->id, 180)
            ->block(5, function () use ($customer) {
                $current = Customer::findOrFail($customer->id);

                // Pengulangan tombol tidak menggeser tanggal prorata.
                if ($current->status === 'active') {
                    return false;
                }

                $previousStatus = $current->status;

                MikroTikService::assertSuccessfulResponse(
                    $this->mikrotik->activate($current)
                );

                return DB::transaction(function () use ($current, $previousStatus) {
                    $locked = Customer::lockForUpdate()
                        ->findOrFail($current->id);

                    if ($locked->status !== $previousStatus) {
                        throw new RuntimeException(
                            'Status pelanggan berubah selama proses router. '.
                            'Periksa keadaan router dan billing sebelum mengulang.'
                        );
                    }

                    $changes = ['status' => 'active'];

                    // Aktivasi pelanggan inactive tetap memakai aturan aktivasi awal.
                    if ($previousStatus === 'isolated') {
                        $changes['billing_resumed_on'] = today()->toDateString();
                    }

                    $locked->forceFill($changes)->save();

                    $description =
                        "Pelanggan {$locked->name} ".
                        "({$locked->pppoe_username}) diaktifkan.";

                    if ($previousStatus === 'isolated') {
                        $description .=
                            ' Billing dimulai kembali pada '.
                            $locked->billing_resumed_on->toDateString().'.';
                    }

                    $this->log('customer.activated', $description);

                    return true;
                });
            });
    }

    private function log(string $action, string $description): void
    {
        ActivityLog::create([
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}
