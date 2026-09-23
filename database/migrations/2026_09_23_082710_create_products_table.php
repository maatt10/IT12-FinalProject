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

            $table->string('name');

            $table->boolean('is_sellable')->default(true);

            $table->decimal('selling_price', 10, 2)->nullable();

            $table->string('stock_unit');

            $table->string('purchase_unit')->nullable();
            $table->decimal('units_per_purchase', 10, 2)->nullable();

            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('variation')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
