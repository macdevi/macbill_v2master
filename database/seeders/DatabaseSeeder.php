<?php

namespace Database\Seeders;

use App\Models\InternetPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@macbilling.local'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'timezone' => 'Asia/Jakarta',
                'role' => 'super_admin',
                'password' => Hash::make('ChangeMe123!'),
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        InternetPackage::query()->updateOrCreate(
            ['name' => 'Paket 5 Mbps'],
            [
                'mikrotik_profile' => 'profile-5m',
                'download_speed' => 5,
                'upload_speed' => 5,
                'monthly_price' => 100000,
            ]
        );

        InternetPackage::query()->updateOrCreate(
            ['name' => 'Paket 10 Mbps'],
            [
                'mikrotik_profile' => 'profile-10m',
                'download_speed' => 10,
                'upload_speed' => 10,
                'monthly_price' => 150000,
            ]
        );
    }
}
