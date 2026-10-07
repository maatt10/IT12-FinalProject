<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'walk_in' to fulfillment_type enum
        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN fulfillment_type
            ENUM('pickup', 'delivery', 'walk_in') NOT NULL
        ");

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'gcash'])
                ->nullable()
                ->after('delivery_fee');

            $table->string('payment_reference', 20)
                ->nullable()
                ->after('payment_method');

            $table->decimal('amount_paid', 10, 2)
                ->nullable()
                ->after('payment_reference');

            $table->decimal('change_amount', 10, 2)
                ->nullable()
                ->after('amount_paid');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_reference',
                'amount_paid',
                'change_amount',
            ]);
        });

        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN fulfillment_type
            ENUM('pickup', 'delivery') NOT NULL
        ");
    }
};