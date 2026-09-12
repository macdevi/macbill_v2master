<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('service_amount', 15, 2)->default(0)->after('due_date');
            $table->decimal('subtotal', 15, 2)->default(0)->after('service_amount');
            $table->string('tax_name')->nullable()->after('subtotal');
            $table->decimal('tax_rate', 8, 4)->default(0)->after('tax_name');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
            $table->string('tax_mode', 20)->default('none')->after('tax_amount');
            $table->decimal('gross_amount', 15, 2)->default(0)->after('tax_mode');
        });

        DB::table('invoices')->orderBy('id')->each(function (object $invoice) {
            DB::table('invoices')
                ->where('id', $invoice->id)
                ->update([
                    'service_amount' => $invoice->amount,
                    'subtotal' => $invoice->amount,
                    'tax_name' => null,
                    'tax_rate' => 0,
                    'tax_amount' => 0,
                    'tax_mode' => 'none',
                    'gross_amount' => $invoice->amount,
                ]);
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'service_amount',
                'subtotal',
                'tax_name',
                'tax_rate',
                'tax_amount',
                'tax_mode',
                'gross_amount',
            ]);
        });
    }
};
