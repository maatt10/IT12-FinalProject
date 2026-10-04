<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('item_type')->nullable()->after('variation');
            $table->boolean('is_active')->default(true)->after('item_type');

            $table->index('item_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['products_item_type_index']);
            $table->dropIndex(['products_is_active_index']);

            $table->dropColumn([
                'item_type',
                'is_active',
            ]);
        });
    }
};
