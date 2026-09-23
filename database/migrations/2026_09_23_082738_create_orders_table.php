<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');

            $table->foreignId('customer_id')
                ->constrained('customers', 'customer_id')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained('users', 'user_id')
                ->restrictOnDelete();

            $table->enum('order_type', [
                'ready_made',
                'customized'
            ]);

            $table->string('receiver_first_name');
            $table->string('receiver_middle_name')->nullable();
            $table->string('receiver_last_name');

            $table->string('receiver_contact');

            $table->text('delivery_address')->nullable();

            $table->string('delivery_timing');

            $table->enum('fulfillment_type', [
                'pickup',
                'delivery'
            ]);

            $table->decimal('delivery_fee', 10, 2)->default(0);

            $table->string('payment_proof_reference')->nullable();

            $table->enum('order_status', [
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'completed',
                'cancelled'
            ])->default('pending');

            $table->decimal('total_amount', 10, 2);

            $table->dateTime('order_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};