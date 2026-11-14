<?php
namespace App\Services;

use App\Models\InternetPackage;
use App\Models\Router;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PackageCatalogService
{
    public function save(array $input, ?InternetPackage $package = null): InternetPackage
    {
        foreach (['name', 'mikrotik_profile', 'burst_limit', 'burst_threshold', 'burst_time'] as $field) {
            if (isset($input[$field]) && is_string($input[$field])) {
                $input[$field] = trim($input[$field]);
            }
        }
        $data = Validator::make($input, [
            'name' => ['required', 'string', 'max:100'],
            'mikrotik_profile' => ['required', 'string', 'max:100'],
            'router_ids' => ['required', 'array', 'min:1', 'max:1000'],
            'router_ids.*' => ['required', 'integer', 'min:1', 'distinct'],
            'download_speed' => ['required', 'integer', $package ? 'min:0' : 'min:1', 'max:2147483647'],
            'upload_speed' => ['required', 'integer', $package ? 'min:0' : 'min:1', 'max:2147483647'],
            'monthly_price' => ['required', 'numeric', 'min:0', 'max:9999999999999.99', 'decimal:0,2'],
            'burst_limit' => ['nullable', 'string', 'max:50'],
            'burst_threshold' => ['nullable', 'string', 'max:50'],
            'burst_time' => ['nullable', 'string', 'max:50'],
            'priority' => ['nullable', 'integer', 'min:1', 'max:8'],
        ], [
            'router_ids.required' => 'Pilih minimal satu router untuk paket ini.',
            'router_ids.min' => 'Pilih minimal satu router untuk paket ini.',
        ], [
            'name' => 'nama paket', 'mikrotik_profile' => 'profil MikroTik',
            'router_ids' => 'pilihan router', 'router_ids.*' => 'pilihan router',
            'monthly_price' => 'harga bulanan', 'download_speed' => 'download',
            'upload_speed' => 'upload',
        ])->validate();

        $ids = array_map('intval', $data['router_ids']);
        sort($ids);
        unset($data['router_ids']);
        $data['priority'] = $data['priority'] ?? 8;
        foreach (['burst_limit', 'burst_threshold', 'burst_time'] as $field) {
            $data[$field] = ($data[$field] ?? '') === '' ? null : $data[$field];
        }
        if (DB::connection()->getDriverName() !== 'sqlite') {
            throw ValidationException::withMessages([
                'router_ids' => 'Jenis database berubah. Pengaman penyimpanan perlu diperiksa.',
            ]);
        }

        return DB::transaction(function () use ($package, $data, $ids) {
            // Operasi tulis pertama mengambil lock SQLite sebelum pemeriksaan relasi.
            DB::table('routers')->whereIn('id', $ids)
                ->update(['active' => DB::raw('active')]);
            $current = $package ? InternetPackage::findOrFail($package->id) : null;
            $oldIds = $current ? $current->routers()->pluck('routers.id')
                ->map(fn ($id) => (int) $id)->all() : [];
            $routers = Router::whereIn('id', $ids)->get();
            if ($routers->count() !== count($ids)) {
                throw ValidationException::withMessages([
                    'router_ids' => 'Ada router yang tidak tersedia. Muat ulang form.',
                ]);
            }
            foreach ($routers as $router) {
                if (!$router->active && !in_array((int) $router->id, $oldIds, true)) {
                    throw ValidationException::withMessages([
                        'router_ids' => 'Router nonaktif tidak boleh ditambahkan ke paket.',
                    ]);
                }
            }
            if ($current) {
                $usedIds = DB::table('customers')
                    ->where('internet_package_id', $current->id)
                    ->distinct()->pluck('router_id')->map(fn ($id) => (int) $id)->all();
                if (array_diff($usedIds, $ids)) {
                    throw ValidationException::withMessages([
                        'router_ids' => 'Router masih dipakai pelanggan paket ini dan tidak boleh dilepas. '
                            .'Pindahkan paket pelanggan terlebih dahulu.',
                    ]);
                }
            }
            $collision = InternetPackage::query()
                ->where('mikrotik_profile', $data['mikrotik_profile'])
                ->whereHas('routers', fn ($query) => $query->whereIn('routers.id', $ids));
            if ($current) {
                $collision->where('internet_packages.id', '!=', $current->id);
            }
            if ($collision->exists()) {
                throw ValidationException::withMessages([
                    'mikrotik_profile' => 'Profil ini sudah dipakai paket lain pada salah satu router pilihan. '
                        .'Edit paket yang ada untuk menjadikannya paket bersama, atau pilih profil berbeda.',
                ]);
            }
            if ($current) {
                $current->update($data);
            } else {
                $current = InternetPackage::create($data + ['active' => true]);
            }
            $current->routers()->sync($ids);
            return $current->load('routers');
        });
    }
}
