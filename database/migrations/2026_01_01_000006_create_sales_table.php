<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id('sale_id');

            $table->string('reference_code', 20)->nullable()->unique();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers', 'customer_id')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->constrained('users', 'user_id')
                ->restrictOnDelete();

            $table->dateTime('sale_date');

            $table->enum('payment_method', ['cash', 'gcash', 'bank_transfer']);
            $table->string('gcash_reference', 13)->nullable();

            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);

            $table->enum('discount_type', ['none', 'pwd', 'senior'])->default('none');
            $table->string('discount_name')->nullable();
            $table->string('discount_id_number', 30)->nullable();

            $table->decimal('total_amount', 10, 2);

            $table->boolean('receipt_issued')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};