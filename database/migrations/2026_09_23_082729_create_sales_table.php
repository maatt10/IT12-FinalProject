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

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers', 'customer_id')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->constrained('users', 'user_id')
                ->restrictOnDelete();

            $table->dateTime('sale_date');

            $table->enum('payment_method', [
                'cash',
                'gcash',
                'bank_transfer'
            ]);

            $table->decimal('subtotal', 10, 2);

            $table->decimal('discount_amount', 10, 2)->default(0);

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