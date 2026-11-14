<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_sync_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internet_package_id')
                ->constrained('internet_packages')->cascadeOnDelete();
            $table->foreignId('router_id')
                ->constrained('routers')->cascadeOnDelete();
            $table->string('status', 20);
            $table->string('fingerprint', 64)->nullable();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unique(
                ['internet_package_id', 'router_id'],
                'package_sync_states_pair_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_sync_states');
    }
};
