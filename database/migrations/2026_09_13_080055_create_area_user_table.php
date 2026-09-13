<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('area_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
             * User yang memberikan penugasan. NULL diperbolehkan untuk
             * data awal/migrasi atau ketika akun pemberi penugasan dihapus.
             */
            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('assigned_at')->nullable();
            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->unique(['user_id', 'area_id']);
            $table->index(['user_id', 'active']);
            $table->index(['area_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_user');
    }
};
