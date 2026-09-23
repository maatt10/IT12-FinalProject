<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id('transaction_id');

            $table->foreignId('inventory_id')
                ->constrained('inventory', 'inventory_id')
                ->restrictOnDelete();

            $table->enum('transaction_type', [
                'purchase_in',
                'sale_out',
                'production_use',
                'production_output',
                'return_in',
                'manual_addition',
                'adjustment'
            ]);

            $table->decimal('quantity_change', 10, 2);

            $table->unsignedBigInteger('reference_id')->nullable();

            $table->string('reference_type')->nullable();

            $table->dateTime('transaction_date');

            $table->foreignId('recorded_by')
                ->constrained('users', 'user_id')
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};