<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SalesReportController extends Controller
{
    /**
     * Display the report on screen.
     */
    public function index(Request $request)
    {
        $data = $this->buildReportData($request);
        return view('reports.sales', $data);
    }

    /**
     * Display the print-optimized report.
     */
    public function print(Request $request)
    {
        $data = $this->buildReportData($request);
        return view('reports.sales-print', $data);
    }

    /**
     * Export the report as a CSV file.
     */
    public function exportCsv(Request $request)
    {
        $data = $this->buildReportData($request);

        $filename = 'sales-report-' . $data['from'] . '-to-' . $data['to'] . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM so Excel opens ₱ symbols correctly
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header
            fputcsv($handle, ["Lara's Flowershop — Sales Report"]);
            fputcsv($handle, ['Period', $data['from'] . ' to ' . $data['to']]);
            fputcsv($handle, ['Generated', now()->format('M d, Y h:i A')]);
            fputcsv($handle, []);

            // Summary
            fputcsv($handle, ['SUMMARY']);
            fputcsv($handle, ['Total Sales (₱)', number_format($data['totalSales'], 2)]);
            fputcsv($handle, ['Total Discounts (₱)', number_format($data['totalDiscounts'], 2)]);
            fputcsv($handle, ['Transactions', $data['transactionCount']]);
            fputcsv($handle, []);

            // Payment Methods
            fputcsv($handle, ['PAYMENT METHODS']);
            fputcsv($handle, ['Cash (₱)', number_format($data['paymentTotals']['cash'], 2)]);
            fputcsv($handle, ['GCash (₱)', number_format($data['paymentTotals']['gcash'], 2)]);
            fputcsv($handle, ['Bank Transfer (₱)', number_format($data['paymentTotals']['bank_transfer'], 2)]);
            fputcsv($handle, []);

            // Transactions
            fputcsv($handle, ['TRANSACTIONS']);
            fputcsv($handle, [
                'Sale ID',
                'Date & Time',
                'Customer',
                'Payment Method',
                'Subtotal (₱)',
                'Discount (₱)',
                'Total (₱)',
                'Recorded By',
            ]);

            foreach ($data['sales'] as $sale) {
                fputcsv($handle, [
                    $sale->sale_id,
                    $sale->sale_date->format('M d, Y h:i A'),
                    $sale->customer->full_name ?? 'Walk-in',
                    strtoupper(str_replace('_', ' ', $sale->payment_method)),
                    number_format($sale->subtotal, 2),
                    number_format($sale->discount_amount, 2),
                    number_format($sale->total_amount, 2),
                    $sale->user->full_name ?? 'Unknown',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Shared report data builder (reused by index, print, and exportCsv).
     */
    private function buildReportData(Request $request): array
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $sales = Sale::with(['customer', 'user'])
            ->whereBetween('sale_date', [$fromDate, $toDate])
            ->orderByDesc('sale_date')
            ->get();

        return [
            'sales' => $sales,
            'from' => $from,
            'to' => $to,
            'totalSales' => $sales->sum('total_amount'),
            'totalDiscounts' => $sales->sum('discount_amount'),
            'transactionCount' => $sales->count(),
            'paymentTotals' => [
                'cash' => $sales->where('payment_method', 'cash')->sum('total_amount'),
                'gcash' => $sales->where('payment_method', 'gcash')->sum('total_amount'),
                'bank_transfer' => $sales->where('payment_method', 'bank_transfer')->sum('total_amount'),
            ],
        ];
    }
}