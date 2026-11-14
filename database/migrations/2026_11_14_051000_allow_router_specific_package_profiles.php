<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internet_packages', function (Blueprint $table) {
            $table->dropUnique('internet_packages_mikrotik_profile_unique');
            $table->index(
                'mikrotik_profile',
                'internet_packages_profile_lookup_index'
            );
        });
    }

    public function down(): void
    {
        $duplicates = DB::table('internet_packages')
            ->select('mikrotik_profile')
            ->groupBy('mikrotik_profile')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($duplicates) {
            throw new RuntimeException(
                'Rollback ditolak: ada profil sama pada paket berbeda. '
                .'Selesaikan pemetaan data sebelum mengembalikan indeks unik.'
            );
        }

        Schema::table('internet_packages', function (Blueprint $table) {
            $table->dropIndex('internet_packages_profile_lookup_index');
            $table->unique(
                'mikrotik_profile',
                'internet_packages_mikrotik_profile_unique'
            );
        });
    }
};
