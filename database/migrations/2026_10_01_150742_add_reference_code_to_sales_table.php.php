<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('reference_code', 20)
                ->nullable()
                ->unique()
                ->after('sale_id');
        });

        // Backfill existing sales using DDMMYY-NNNNN with per-day reset
        $sales = DB::table('sales')
            ->orderBy('sale_date')
            ->orderBy('sale_id')
            ->get();

        $dailyCounters = [];

        foreach ($sales as $sale) {
            $datePart = Carbon::parse($sale->sale_date)->format('dmy');

            if (!isset($dailyCounters[$datePart])) {
                $dailyCounters[$datePart] = 0;
            }

            $dailyCounters[$datePart]++;

            DB::table('sales')
                ->where('sale_id', $sale->sale_id)
                ->update([
                    'reference_code' => $datePart . '-' . str_pad($dailyCounters[$datePart], 5, '0', STR_PAD_LEFT),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropUnique(['reference_code']);
            $table->dropColumn('reference_code');
        });
    }
};