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

            $table->string('reference_code', 25)->nullable()->unique();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers', 'customer_id')
                ->nullOnDelete();
            $table->string('customer_name', 255)->nullable();

            $table->foreignId('user_id')
                ->constrained('users', 'user_id')
                ->restrictOnDelete();

            $table->enum('order_type', ['ready_made', 'customized']);
            $table->enum('channel', ['online', 'walk_in'])->default('online');

            $table->string('receiver_first_name');
            $table->string('receiver_middle_name')->nullable();
            $table->string('receiver_last_name');
            $table->string('receiver_contact');

            $table->text('delivery_address')->nullable();
            $table->dateTime('delivery_datetime')->nullable();

            $table->enum('fulfillment_type', ['pickup', 'delivery']);

            $table->decimal('delivery_fee', 10, 2)->default(0);

            $table->enum('payment_method', ['cash', 'gcash'])->nullable();
            $table->string('payment_reference', 20)->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->decimal('change_amount', 10, 2)->nullable();

            $table->decimal('subtotal', 10, 2)->default(0);

            $table->enum('discount_type', ['none', 'pwd', 'senior'])->default('none');
            $table->string('discount_name')->nullable();
            $table->string('discount_id_number', 30)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);

            $table->string('payment_proof_reference')->nullable();

            $table->enum('order_status', ['pending', 'completed', 'cancelled'])
                ->default('pending');
            $table->text('cancellation_note')->nullable();

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