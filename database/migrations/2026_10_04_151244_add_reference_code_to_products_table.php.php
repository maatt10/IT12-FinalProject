<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('reference_code', 20)
                ->nullable()
                ->unique()
                ->after('product_id');
        });

        // Backfill existing items
        $products = DB::table('products')->orderBy('product_id')->get();

        $counters = ['made_product' => 0, 'material' => 0];

        foreach ($products as $product) {
            $type = $product->item_type ?? 'made_product';
            $prefix = $type === 'material' ? 'MAT' : 'PRD';

            if (!isset($counters[$type])) {
                $counters[$type] = 0;
            }
            $counters[$type]++;

            DB::table('products')
                ->where('product_id', $product->product_id)
                ->update([
                    'reference_code' => $prefix . '-' . str_pad($counters[$type], 5, '0', STR_PAD_LEFT),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['reference_code']);
            $table->dropColumn('reference_code');
        });
    }
};