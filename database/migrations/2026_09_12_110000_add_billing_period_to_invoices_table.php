<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('billing_period')
                ->nullable()
                ->after('customer_id');
        });

        DB::table('invoices')
            ->orderBy('id')
            ->each(function (object $invoice): void {
                DB::table('invoices')
                    ->where('id', $invoice->id)
                    ->update([
                        'billing_period' => \Carbon\Carbon::parse($invoice->billing_date)
                            ->startOfMonth()
                            ->toDateString(),
                    ]);
            });

        Schema::table('invoices', function (Blueprint $table) {
            $table->unique(
                ['customer_id', 'billing_period'],
                'invoices_customer_billing_period_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique('invoices_customer_billing_period_unique');
            $table->dropColumn('billing_period');
        });
    }
};
