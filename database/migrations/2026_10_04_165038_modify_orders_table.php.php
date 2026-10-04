<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop customer FK so we can make it nullable
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->change();
            $table->string('customer_name', 255)->nullable()->after('customer_id');

            // Replace string delivery_timing with proper datetime
            $table->dropColumn('delivery_timing');
            $table->dateTime('delivery_datetime')->nullable()->after('delivery_address');

            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['customer_name', 'delivery_datetime']);
            $table->string('delivery_timing')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable(false)->change();
            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->restrictOnDelete();
        });
    }
};