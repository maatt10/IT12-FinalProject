<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_components', function (Blueprint $table) {
            $table->foreignId('parent_product_id')
                ->constrained('products', 'product_id')
                ->cascadeOnDelete();

            $table->foreignId('material_product_id')
                ->constrained('products', 'product_id')
                ->restrictOnDelete();

            $table->decimal('quantity_required', 10, 2);

            $table->primary([
                'parent_product_id',
                'material_product_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_components');
    }
};