<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installation_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('installation_id')->nullable()
                ->constrained('installations')->nullOnDelete();

            // Snapshot tidak bergantung pada keberadaan nota/pelanggan.
            $table->unsignedBigInteger('original_installation_id')->unique();
            $table->string('receipt_number')->unique();
            $table->unsignedBigInteger('original_customer_id');
            $table->unsignedBigInteger('area_id')->nullable()->index();
            $table->string('customer_name');

            $table->decimal('amount', 15, 2);
            $table->string('method', 30);
            $table->timestamp('paid_at')->index();
            $table->string('status', 20)->default('verified');
            $table->boolean('is_test_data')->default(false);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installation_payments');
    }
};
