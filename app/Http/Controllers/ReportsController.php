<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->buildData($request);
        return view('reports.index', $data);
    }

    public function print(Request $request)
    {
        $data = $this->buildData($request);
        return view('reports.print', $data);
    }

    public function exportCsv(Request $request)
    {
        $data = $this->buildData($request);
        $report = $data['report'];
        $sub = $data['sub'];

        $filename = $report . '-' . $sub . '-report-' . $data['from'] . '-to-' . $data['to'] . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data, $report, $sub) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ["Lara's Flowershop"]);
            fputcsv($handle, [$this->reportTitle($report, $sub)]);
            fputcsv($handle, ['Period', $data['periodLabel']]);
            fputcsv($handle, ['Generated', now()->format('M d, Y h:i A')]);
            fputcsv($handle, []);

            if ($report === 'sales') {
                switch ($sub) {
                    case 'walk-in': $this->writeSalesCsv($handle, $data); break;
                    case 'online': $this->writeOrdersCsv($handle, $data); break;
                    case 'overall': $this->writeOverallCsv($handle, $data); break;
                }
            } else {
                $this->writeStockLedgerCsv($handle, $data);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function reportTitle(string $report, ?string $sub = null): string
    {
        if ($report === 'sales') {
            return match ($sub) {
                'walk-in' => 'Walk-in Sales Report',
                'online' => 'Online Orders Report',
                'overall' => 'Overall Sales Report',
                default => 'Sales Report',
            };
        }

        return match ($sub) {
            'materials' => 'Materials Stock Report',
            'products' => 'Products Stock Report',
            'overall' => 'Overall Stock Report',
            default => 'Stock Report',
        };
    }

    /* ============================================
       CSV WRITERS
       ============================================ */
    private function writeSalesCsv($handle, array $data): void
    {
        $s = $data['salesData'];
        fputcsv($handle, ['SUMMARY']);
        fputcsv($handle, ['Orders', $s['orders']]);
        fputcsv($handle, ['Items Sold', (float) $s['itemsSold']]);
        fputcsv($handle, ['Gross Sales (₱)', number_format($s['gross'], 2)]);
        fputcsv($handle, ['Discounts (₱)', number_format($s['discounts'], 2)]);
        fputcsv($handle, ['Net Sales (₱)', number_format($s['net'], 2)]);
        fputcsv($handle, ['Avg. Order Value (₱)', number_format($s['avgOrder'], 2)]);
        fputcsv($handle, []);
        fputcsv($handle, ['WALK-IN SALES']);
        fputcsv($handle, ['Receipt No', 'Date & Time', 'Customer', 'Payment', 'Subtotal (₱)', 'Discount (₱)', 'Total (₱)']);

        foreach ($data['salesRecords'] as $sale) {
            fputcsv($handle, [
                $sale->reference_code,
                $sale->sale_date->format('M d, Y h:i A'),
                $sale->customer->full_name ?? 'Walk-in',
                strtoupper(str_replace('_', ' ', $sale->payment_method)),
                number_format($sale->subtotal, 2),
                number_format($sale->discount_amount, 2),
                number_format($sale->total_amount, 2),
            ]);
        }
    }

    private function writeOrdersCsv($handle, array $data): void
    {
        $s = $data['ordersData'];
        fputcsv($handle, ['SUMMARY']);
        fputcsv($handle, ['Total Orders', $s['total']]);
        fputcsv($handle, ['Completed', $s['completed']]);
        fputcsv($handle, ['Cancelled', $s['cancelled']]);
        fputcsv($handle, ['Net Revenue (₱)', number_format($s['net'], 2)]);
        fputcsv($handle, []);
        fputcsv($handle, ['ONLINE ORDERS']);
        fputcsv($handle, ['Order No', 'Date & Time', 'Customer', 'Status', 'Fulfillment', 'Total (₱)']);

        foreach ($data['orderRecords'] as $order) {
            fputcsv($handle, [
                $order->reference_code,
                $order->order_date->format('M d, Y h:i A'),
                $order->customer->full_name ?? $order->customer_name ?? 'Unregistered',
                ucfirst($order->order_status),
                ucfirst($order->fulfillment_type),
                number_format($order->total_amount, 2),
            ]);
        }
    }

    private function writeOverallCsv($handle, array $data): void
    {
        $o = $data['overallData'];
        fputcsv($handle, ['SUMMARY']);
        fputcsv($handle, ['Total Revenue (₱)', number_format($o['totalRevenue'], 2)]);
        fputcsv($handle, ['Total Transactions', $o['totalCount']]);
        fputcsv($handle, ['Walk-in Revenue (₱)', number_format($o['walkInRevenue'], 2)]);
        fputcsv($handle, ['Online Revenue (₱)', number_format($o['onlineRevenue'], 2)]);
        fputcsv($handle, []);
        fputcsv($handle, ['ALL TRANSACTIONS']);
        fputcsv($handle, ['Channel', 'Reference', 'Date & Time', 'Customer', 'Total (₱)']);

        foreach ($data['overallRecords'] as $record) {
            fputcsv($handle, [
                $record['source'],
                $record['reference'],
                $record['date']->format('M d, Y h:i A'),
                $record['customer'],
                number_format($record['total'], 2),
            ]);
        }
    }

    private function writeStockLedgerCsv($handle, array $data): void
    {
        fputcsv($handle, ['STOCK LEDGER']);
        fputcsv($handle, ['Item', 'Beginning Balance', 'Stock-in', 'Stock-out', 'Remaining Balance', 'Unit']);

        foreach ($data['ledgerRows'] as $row) {
            fputcsv($handle, [
                $row['name'],
                $row['beginning'],
                $row['stock_in'],
                $row['stock_out'],
                $row['remaining'],
                $row['unit'],
            ]);
        }

        fputcsv($handle, []);
        fputcsv($handle, [
            'TOTAL',
            $data['ledgerTotals']['beginning'],
            $data['ledgerTotals']['stock_in'],
            $data['ledgerTotals']['stock_out'],
            $data['ledgerTotals']['remaining'],
            '',
        ]);
    }

    /* ============================================
       SHARED DATA BUILDER
       ============================================ */
    private function buildData(Request $request): array
    {
        $report = $request->input('report', 'sales');
        $sub = $request->input('sub');

        // Legacy support
        if ($report === 'online-orders') {
            $report = 'sales';
            $sub = 'online';
        } elseif ($report === 'stock') {
            $report = 'stocks';
            $sub = 'overall';
        }

        // Sanitize
        if (!in_array($report, ['sales', 'stocks'])) {
            $report = 'sales';
        }

        if ($report === 'sales') {
            if (!in_array($sub, ['walk-in', 'online', 'overall'])) {
                $sub = 'walk-in';
            }
        } else {
            if (!in_array($sub, ['materials', 'products', 'overall'])) {
                $sub = 'materials';
            }
        }

        $period = $request->input('period', 'weekly');
        $date = $request->input('date', now()->toDateString());

        [$from, $to, $periodLabel] = $this->resolvePeriod($period, $date);

        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $data = [
            'report' => $report,
            'sub' => $sub,
            'period' => $period,
            'date' => $date,
            'from' => $from,
            'to' => $to,
            'periodLabel' => $periodLabel,
        ];

        if ($report === 'sales') {
            $data += $this->salesData($fromDate, $toDate);
            $data += $this->onlineOrdersData($fromDate, $toDate);

            if ($sub === 'overall') {
                $data += $this->overallSalesData(
                    $data['salesData'],
                    $data['ordersData'],
                    $data['salesRecords'],
                    $data['orderRecords']
                );
            }
        } else {
            $data += $this->stockLedgerData($fromDate, $toDate, $sub);
        }

        return $data;
    }

    private function resolvePeriod(string $period, string $date): array
    {
        $base = Carbon::parse($date);

        switch ($period) {
            case 'daily':
                $from = $base->copy()->startOfDay();
                $to = $base->copy()->endOfDay();
                $label = $base->format('F d, Y');
                break;

            case 'monthly':
                $from = $base->copy()->startOfMonth();
                $to = $base->copy()->endOfMonth();
                $label = $base->format('F Y');
                break;

            case 'weekly':
            default:
                $from = $base->copy()->startOfWeek();
                $to = $base->copy()->endOfWeek();
                $label = 'Week of ' . $from->format('M d') . ' – ' . $to->format('M d, Y');
                break;
        }

        return [$from->toDateString(), $to->toDateString(), $label];
    }

    private function salesData(Carbon $from, Carbon $to): array
    {
        $sales = Sale::with(['customer', 'user', 'items'])
            ->whereBetween('sale_date', [$from, $to])
            ->orderByDesc('sale_date')
            ->get();

        $gross = $sales->sum('subtotal');
        $discounts = $sales->sum('discount_amount');
        $net = $sales->sum('total_amount');
        $count = $sales->count();
        $itemsSold = $sales->sum(fn ($sale) => $sale->items->sum('quantity'));

        return [
            'salesData' => [
                'orders' => $count,
                'itemsSold' => (float) $itemsSold,
                'gross' => $gross,
                'discounts' => $discounts,
                'net' => $net,
                'avgOrder' => $count > 0 ? $net / $count : 0,
                'paymentCash' => $sales->where('payment_method', 'cash')->sum('total_amount'),
                'paymentGcash' => $sales->where('payment_method', 'gcash')->sum('total_amount'),
            ],
            'salesRecords' => $sales,
        ];
    }

    private function onlineOrdersData(Carbon $from, Carbon $to): array
    {
        $orders = Order::with(['customer', 'user'])
            ->whereBetween('order_date', [$from, $to])
            ->orderByDesc('order_date')
            ->get();

        $nonCancelled = $orders->whereNotIn('order_status', ['cancelled']);
        $net = $nonCancelled->sum('total_amount');
        $count = $orders->count();

        return [
            'ordersData' => [
                'total' => $count,
                'completed' => $orders->where('order_status', 'completed')->count(),
                'cancelled' => $orders->where('order_status', 'cancelled')->count(),
                'net' => $net,
                'avgOrder' => $count > 0 ? $net / $count : 0,
                'statusCounts' => [
                    'pending' => $orders->where('order_status', 'pending')->count(),
                    'completed' => $orders->where('order_status', 'completed')->count(),
                    'cancelled' => $orders->where('order_status', 'cancelled')->count(),
                ],
            ],
            'orderRecords' => $orders,
        ];
    }

    private function overallSalesData(array $salesData, array $ordersData, $salesRecords, $orderRecords): array
    {
        $walkInRevenue = (float) $salesData['net'];
        $onlineRevenue = (float) $ordersData['net'];
        $totalRevenue = $walkInRevenue + $onlineRevenue;

        $walkInCount = (int) $salesData['orders'];
        $onlineCount = (int) $ordersData['total'];
        $totalCount = $walkInCount + $onlineCount;

        $merged = collect();

        foreach ($salesRecords as $sale) {
            $merged->push([
                'source' => 'Walk-in',
                'reference' => $sale->reference_code,
                'date' => $sale->sale_date,
                'customer' => $sale->customer->full_name ?? 'Walk-in',
                'total' => (float) $sale->total_amount,
            ]);
        }

        foreach ($orderRecords as $order) {
            $merged->push([
                'source' => 'Online',
                'reference' => $order->reference_code,
                'date' => $order->order_date,
                'customer' => $order->customer->full_name ?? $order->customer_name ?? 'Unregistered',
                'total' => (float) $order->total_amount,
            ]);
        }

        $merged = $merged->sortByDesc('date')->values();

        return [
            'overallData' => [
                'totalRevenue' => $totalRevenue,
                'totalCount' => $totalCount,
                'walkInRevenue' => $walkInRevenue,
                'walkInCount' => $walkInCount,
                'onlineRevenue' => $onlineRevenue,
                'onlineCount' => $onlineCount,
                'avgTransaction' => $totalCount > 0 ? $totalRevenue / $totalCount : 0,
            ],
            'overallRecords' => $merged,
        ];
    }

    private function stockLedgerData(Carbon $from, Carbon $to, string $sub): array
    {
        $query = Product::with('inventory')->where('is_active', true);

        if ($sub === 'materials') {
            $query->where('item_type', 'material');
        } elseif ($sub === 'products') {
            $query->where('item_type', 'made_product');
        }

        $products = $query->orderBy('name')->orderBy('variation')->get();

        $rows = [];

        foreach ($products as $product) {
            $inventoryIds = $product->inventory->pluck('inventory_id');

            if ($inventoryIds->isEmpty()) {
                $rows[] = [
                    'name' => $product->display_name,
                    'unit' => $product->stock_unit,
                    'beginning' => 0.0,
                    'stock_in' => 0.0,
                    'stock_out' => 0.0,
                    'remaining' => 0.0,
                ];
                continue;
            }

            // Beginning = sum of all changes before the period start
            $beginning = (float) InventoryTransaction::whereIn('inventory_id', $inventoryIds)
                ->where('transaction_date', '<', $from)
                ->sum('quantity_change');

            // Stock-in during period
            $stockIn = (float) InventoryTransaction::whereIn('inventory_id', $inventoryIds)
                ->whereBetween('transaction_date', [$from, $to])
                ->where('quantity_change', '>', 0)
                ->sum('quantity_change');

            // Stock-out during period (abs value)
            $stockOut = abs((float) InventoryTransaction::whereIn('inventory_id', $inventoryIds)
                ->whereBetween('transaction_date', [$from, $to])
                ->where('quantity_change', '<', 0)
                ->sum('quantity_change'));

            // Remaining = beginning + stock_in - stock_out
            $remaining = $beginning + $stockIn - $stockOut;

            $rows[] = [
                'name' => $product->display_name,
                'unit' => $product->stock_unit,
                'beginning' => $beginning,
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
                'remaining' => $remaining,
            ];
        }

        $totals = [
            'beginning' => collect($rows)->sum('beginning'),
            'stock_in' => collect($rows)->sum('stock_in'),
            'stock_out' => collect($rows)->sum('stock_out'),
            'remaining' => collect($rows)->sum('remaining'),
        ];

        return [
            'ledgerRows' => $rows,
            'ledgerTotals' => $totals,
        ];
    }
}