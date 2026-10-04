<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('reference_code', 25)
                ->nullable()
                ->unique()
                ->after('order_id');

            $table->decimal('subtotal', 10, 2)->default(0)->after('delivery_fee');

            $table->enum('discount_type', ['none', 'pwd', 'senior'])
                ->default('none')
                ->after('subtotal');

            $table->string('discount_name')->nullable()->after('discount_type');
            $table->string('discount_id_number', 30)->nullable()->after('discount_name');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_id_number');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['reference_code']);
            $table->dropColumn([
                'reference_code',
                'subtotal',
                'discount_type',
                'discount_name',
                'discount_id_number',
                'discount_amount',
            ]);
        });
    }
};