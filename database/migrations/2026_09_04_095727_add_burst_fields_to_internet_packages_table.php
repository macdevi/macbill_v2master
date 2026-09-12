<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internet_packages', function (Blueprint $table) {
            $table->string('burst_limit')->nullable()->after('upload_speed');
            $table->string('burst_threshold')->nullable()->after('burst_limit');
            $table->string('burst_time')->nullable()->after('burst_threshold');
            $table->integer('priority')->default(8)->after('burst_time');
        });
    }

    public function down(): void
    {
        Schema::table('internet_packages', function (Blueprint $table) {
            $table->dropColumn([
                'burst_limit',
                'burst_threshold',
                'burst_time',
                'priority'
            ]);
        });
    }
};
