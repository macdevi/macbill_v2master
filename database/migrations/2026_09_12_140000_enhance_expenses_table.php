<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('category', 100)->default('Lainnya')->after('title');
            $table->string('vendor', 255)->nullable()->after('description');
            $table->string('payment_method', 30)->default('other')->after('amount');
            $table->string('status', 20)->default('posted')->after('payment_method');
            $table->timestamp('voided_at')->nullable()->after('status');

            $table->index(['status', 'expense_date'], 'expenses_status_expense_date_index');
            $table->index(['category', 'expense_date'], 'expenses_category_expense_date_index');
        });

        DB::table('expenses')
            ->whereNull('category')
            ->update([
                'category' => 'Lainnya',
                'payment_method' => 'other',
                'status' => 'posted',
            ]);
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_status_expense_date_index');
            $table->dropIndex('expenses_category_expense_date_index');
            $table->dropColumn([
                'category',
                'vendor',
                'payment_method',
                'status',
                'voided_at',
            ]);
        });
    }
};
