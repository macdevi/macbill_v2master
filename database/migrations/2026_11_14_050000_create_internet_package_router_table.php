<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $macnet = DB::table('routers')->where('id', 1)->first();

        if (!$macnet || $macnet->name !== 'MACNET') {
            throw new RuntimeException(
                'Pemetaan awal dibatalkan: router ID 1 bukan MACNET.'
            );
        }

        if (DB::table('customers')->where('router_id', '!=', 1)->exists()) {
            throw new RuntimeException(
                'Pemetaan awal dibatalkan: ada pelanggan pada router lain.'
            );
        }

        Schema::create('internet_package_router', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internet_package_id')
                ->constrained('internet_packages')
                ->cascadeOnDelete();
            $table->foreignId('router_id')
                ->constrained('routers')
                ->restrictOnDelete();
            $table->timestamps();

            $table->unique(
                ['internet_package_id', 'router_id'],
                'package_router_pair_unique'
            );
        });

        $now = now();

        foreach (DB::table('internet_packages')->orderBy('id')->pluck('id') as $id) {
            DB::table('internet_package_router')->insert([
                'internet_package_id' => $id,
                'router_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('internet_package_router');
    }
};
