<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installation_cancellations', function (Blueprint $table) {
            $table->id();

            // ID asal adalah snapshot, bukan relasi yang ikut terhapus.
            $table->unsignedBigInteger('original_installation_id')->unique();
            $table->string('receipt_number')->unique();
            $table->unsignedBigInteger('original_customer_id');
            $table->unsignedBigInteger('original_area_id')->nullable();
            $table->unsignedBigInteger('original_router_id')->nullable();

            $table->string('customer_name');
            $table->string('pppoe_username', 100);
            $table->decimal('installation_total', 15, 2);
            $table->decimal('payment_received', 15, 2);
            $table->string('original_payment_method', 30)->nullable();
            $table->timestamp('original_paid_at')->nullable();

            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->string('refund_method', 30)->nullable();
            $table->string('refund_reference', 255)->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->boolean('is_test_data')->default(false);

            $table->string('cancel_target', 30);
            $table->text('reason');
            $table->unsignedBigInteger('actor_id');
            $table->string('actor_name');
            $table->string('ip_address', 45)->nullable();
            $table->json('invoice_snapshot');
            $table->timestamp('cancelled_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installation_cancellations');
    }
};
