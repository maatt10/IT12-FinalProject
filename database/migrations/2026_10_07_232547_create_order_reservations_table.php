<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_reservations', function (Blueprint $table) {
            $table->id('reservation_id');

            $table->foreignId('order_id')
                ->constrained('orders', 'order_id')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->restrictOnDelete();

            $table->enum('reserve_type', ['retail', 'production']);
            $table->decimal('quantity', 10, 2);

            $table->enum('status', ['active', 'consumed', 'released'])
                ->default('active');

            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['product_id', 'reserve_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_reservations');
    }
};