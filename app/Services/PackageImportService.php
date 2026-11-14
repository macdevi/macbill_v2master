<?php

namespace App\Services;

use App\Models\InternetPackage;
use App\Models\Router;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PackageImportService
{
    public function importPackages(Router $router, array $rows): array
    {
        $validated = Validator::make(['packages' => $rows], [
            'packages' => ['required', 'array', 'min:1'],
            'packages.*' => ['required', 'array'],
            'packages.*.name' => ['required', 'string', 'max:100'],
            'packages.*.mikrotik_profile' => [
                'required', 'string', 'max:100', 'distinct',
            ],
            'packages.*.import_action' => [
                'required', 'string', 'regex:/^(new|link:[1-9][0-9]*)$/',
            ],
            'packages.*.download_speed' => ['nullable', 'integer', 'min:0'],
            'packages.*.upload_speed' => ['nullable', 'integer', 'min:0'],
            'packages.*.monthly_price' => ['nullable', 'numeric', 'min:0'],
            'packages.*.burst_limit' => ['nullable', 'string', 'max:50'],
            'packages.*.burst_threshold' => ['nullable', 'string', 'max:50'],
            'packages.*.burst_time' => ['nullable', 'string', 'max:50'],
            'packages.*.priority' => ['nullable', 'integer', 'min:1', 'max:8'],
        ])->validate();

        return DB::transaction(function () use ($router, $validated) {
            if (!Router::whereKey($router->id)->where('active', true)->exists()) {
                throw ValidationException::withMessages([
                    'router_id' => 'Router sumber tidak tersedia atau tidak aktif.',
                ]);
            }

            $result = ['created' => 0, 'linked' => 0, 'unchanged' => 0];

            foreach ($validated['packages'] as $index => $row) {
                $profile = trim($row['mikrotik_profile']);
                $name = trim($row['name']);
                $action = $row['import_action'];

                if ($profile === '' || $name === '') {
                    throw ValidationException::withMessages([
                        "packages.$index.name" => 'Nama dan profil tidak boleh kosong.',
                    ]);
                }

                $matches = InternetPackage::query()
                    ->availableForRouter((int) $router->id)
                    ->where('mikrotik_profile', $profile)
                    ->get();

                if ($matches->count() > 1) {
                    throw ValidationException::withMessages([
                        "packages.$index.import_action" =>
                            'Ada pemetaan profil ganda pada router sumber.',
                    ]);
                }

                $existing = $matches->first();

                if ($existing) {
                    if (
                        str_starts_with($action, 'link:') &&
                        (int) substr($action, 5) !== (int) $existing->id
                    ) {
                        throw ValidationException::withMessages([
                            "packages.$index.import_action" =>
                                'Profil pada router ini sudah memakai paket lain.',
                        ]);
                    }

                    $result['unchanged']++;
                    continue;
                }

                if (str_starts_with($action, 'link:')) {
                    $target = InternetPackage::query()
                        ->whereKey((int) substr($action, 5))
                        ->where('active', true)
                        ->where('mikrotik_profile', $profile)
                        ->first();

                    if (!$target) {
                        throw ValidationException::withMessages([
                            "packages.$index.import_action" =>
                                'Paket tujuan harus aktif dan memiliki profil yang sama.',
                        ]);
                    }

                    $target->routers()->syncWithoutDetaching([$router->id]);
                    $result['linked']++;
                    continue;
                }

                if (
                    !array_key_exists('monthly_price', $row) ||
                    $row['monthly_price'] === null ||
                    $row['monthly_price'] === ''
                ) {
                    throw ValidationException::withMessages([
                        "packages.$index.monthly_price" =>
                            'Isi harga paket baru. Masukkan 0 jika memang gratis.',
                    ]);
                }

                $package = InternetPackage::create([
                    'name' => $name,
                    'mikrotik_profile' => $profile,
                    'download_speed' => $row['download_speed'] ?? 0,
                    'upload_speed' => $row['upload_speed'] ?? 0,
                    'monthly_price' => $row['monthly_price'],
                    'burst_limit' => $row['burst_limit'] ?? null,
                    'burst_threshold' => $row['burst_threshold'] ?? null,
                    'burst_time' => $row['burst_time'] ?? null,
                    'priority' => $row['priority'] ?? 8,
                    'active' => true,
                ]);

                $package->routers()->attach($router->id);
                $result['created']++;
            }

            return $result;
        });
    }
}
