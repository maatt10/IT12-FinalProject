<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('discount_type', ['none', 'pwd', 'senior'])
                ->default('none')
                ->after('discount_amount');
            $table->string('discount_name')->nullable()->after('discount_type');
            $table->string('discount_id_number', 30)->nullable()->after('discount_name');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'discount_type',
                'discount_name',
                'discount_id_number',
            ]);
        });
    }
};