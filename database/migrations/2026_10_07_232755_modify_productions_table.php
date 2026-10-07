<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make product_id nullable (custom bouquets have no product)
        DB::statement('ALTER TABLE productions MODIFY COLUMN product_id BIGINT UNSIGNED NULL');

        // Drop the existing foreign key first
        Schema::table('productions', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        // Re-add as nullable with set-null on delete
        Schema::table('productions', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('product_id')
                ->on('products')
                ->nullOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->after('product_id')
                ->constrained('orders', 'order_id')
                ->nullOnDelete();

            $table->text('notes')
                ->nullable()
                ->after('quantity_produced');
        });
    }

    public function down(): void
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['order_id']);
            $table->dropColumn(['order_id', 'notes']);
        });

        Schema::table('productions', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('product_id')
                ->on('products')
                ->restrictOnDelete();
        });

        DB::statement('ALTER TABLE productions MODIFY COLUMN product_id BIGINT UNSIGNED NOT NULL');
    }
};