<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id('inventory_id');

            $table->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->cascadeOnDelete();

            $table->enum('reserve_type', ['retail', 'production']);

            $table->decimal('current_quantity', 10, 2)->default(0);

            $table->timestamp('last_updated')->nullable();

            $table->timestamps();

            $table->unique(['product_id', 'reserve_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};