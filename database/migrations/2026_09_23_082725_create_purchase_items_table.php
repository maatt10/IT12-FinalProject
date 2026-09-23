<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id('purchase_item_id');

            $table->foreignId('purchase_id')
                ->constrained('purchases', 'purchase_id')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->restrictOnDelete();

            $table->decimal('quantity', 10, 2);

            $table->decimal('unit_cost', 10, 2);

            $table->enum('reserve_type', ['retail', 'production']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};