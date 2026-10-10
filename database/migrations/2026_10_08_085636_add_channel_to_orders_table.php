<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Convert any existing 'walk_in' fulfillment_type rows to 'pickup'
        DB::table('orders')
            ->where('fulfillment_type', 'walk_in')
            ->update(['fulfillment_type' => 'pickup']);

        // Step 2: Remove walk_in from fulfillment_type enum
        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN fulfillment_type
            ENUM('pickup', 'delivery') NOT NULL
        ");

        // Step 3: Add channel column
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('channel', ['online', 'walk_in'])
                ->default('online')
                ->after('order_type');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('channel');
        });

        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN fulfillment_type
            ENUM('pickup', 'delivery', 'walk_in') NOT NULL
        ");
    }
};