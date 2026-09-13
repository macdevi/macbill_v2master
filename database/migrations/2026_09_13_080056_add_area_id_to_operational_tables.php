<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('area_id')
                ->nullable()
                ->after('router_id')
                ->constrained()
                ->nullOnDelete();

            $table->index('area_id');
        });

        Schema::table('routers', function (Blueprint $table) {
            $table->foreignId('area_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();

            $table->index('area_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            /*
             * NULL = biaya global; pada tahap akses nanti hanya superadmin
             * yang dapat melihat biaya global.
             */
            $table->foreignId('area_id')
                ->nullable()
                ->after('created_by')
                ->constrained()
                ->nullOnDelete();

            $table->index('area_id');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('area_id');
        });

        Schema::table('routers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('area_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('area_id');
        });
    }
};
