<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->date('service_activated_on')->nullable();
            $table->date('first_billing_on')->nullable();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->date('service_period_start')->nullable();
            $table->date('service_period_end')->nullable();
            $table->boolean('is_prorated')->default(false);
            $table->unsignedSmallInteger('charged_days')->nullable();
            $table->unsignedSmallInteger('cycle_days')->nullable();
            $table->decimal('monthly_price_snapshot', 15, 2)->nullable();
        });

        Schema::create('installations', function (Blueprint $table) {
            $table->id();
            $table->uuid('submission_key')->unique();
            $table->string('receipt_number')->unique();

            $table->foreignId('customer_id')->nullable()
                ->constrained('customers')->nullOnDelete();
            $table->foreignId('area_id')->nullable()
                ->constrained('areas')->nullOnDelete();
            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->string('customer_name');
            $table->string('customer_phone', 30);
            $table->text('customer_address')->nullable();
            $table->string('package_name');
            $table->date('activated_on');
            $table->date('first_billing_on');
            $table->unsignedTinyInteger('billing_day');
            $table->decimal('monthly_price', 15, 2);
            $table->string('tax_mode', 20);

            $table->decimal('installation_fee', 15, 2)->default(0);
            $table->decimal('equipment_fee', 15, 2)->default(0);
            $table->decimal('other_fee', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->string('payment_method', 30)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['area_id', 'activated_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installations');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'service_period_start',
                'service_period_end',
                'is_prorated',
                'charged_days',
                'cycle_days',
                'monthly_price_snapshot',
            ]);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'service_activated_on',
                'first_billing_on',
            ]);
        });
    }
};
