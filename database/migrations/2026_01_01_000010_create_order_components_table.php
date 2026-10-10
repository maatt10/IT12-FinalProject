<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_components', function (Blueprint $table) {
            $table->id('order_component_id');

            $table->foreignId('order_id')
                ->constrained('orders', 'order_id')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->restrictOnDelete();

            $table->decimal('quantity', 10, 2);

            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_components');
    }
};