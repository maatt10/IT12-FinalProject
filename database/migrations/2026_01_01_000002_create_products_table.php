<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');

            $table->string('reference_code', 20)->nullable()->unique();

            $table->string('name');
            $table->string('variation')->nullable();
            $table->string('image_path')->nullable();

            $table->string('item_type');

            $table->boolean('is_active')->default(true);
            $table->boolean('is_sellable')->default(true);

            $table->enum('stock_purpose', ['retail', 'production', 'both'])
                ->default('retail');
            $table->decimal('low_stock_threshold', 10, 2)->default(10);

            $table->decimal('selling_price', 10, 2)->nullable();

            $table->string('stock_unit');
            $table->string('purchase_unit')->nullable();
            $table->decimal('units_per_purchase', 10, 2)->nullable();

            $table->timestamps();

            $table->index('item_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};