<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input(
            'from',
            now()->startOfMonth()->toDateString()
        );

        $to = $request->input(
            'to',
            now()->toDateString()
        );

        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $sales = Sale::with([
            'customer',
            'user',
        ])
            ->whereBetween('sale_date', [
                $fromDate,
                $toDate,
            ])
            ->orderByDesc('sale_date')
            ->get();

        $totalSales = $sales->sum('total_amount');

        $totalDiscounts = $sales->sum('discount_amount');

        $transactionCount = $sales->count();

        $paymentTotals = [
            'cash' => $sales
                ->where('payment_method', 'cash')
                ->sum('total_amount'),

            'gcash' => $sales
                ->where('payment_method', 'gcash')
                ->sum('total_amount'),

            'bank_transfer' => $sales
                ->where('payment_method', 'bank_transfer')
                ->sum('total_amount'),
        ];

        return view('reports.sales', compact(
            'sales',
            'from',
            'to',
            'totalSales',
            'totalDiscounts',
            'transactionCount',
            'paymentTotals'
        ));
    }
}