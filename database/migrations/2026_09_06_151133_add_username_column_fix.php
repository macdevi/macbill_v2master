<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users','username')) {

            Schema::table('users', function(Blueprint $table){
                $table->string('username')->nullable()->unique()->after('name');
            });

            DB::table('users')
            ->whereNull('username')
            ->update([
                'username'=>DB::raw('name')
            ]);

        }
    }


    public function down(): void
    {
        Schema::table('users', function(Blueprint $table){
            $table->dropColumn('username');
        });
    }
};
