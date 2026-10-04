<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Production;
use App\Models\Purchase;
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

        $filename = $report . '-report-' . $data['from'] . '-to-' . $data['to'] . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data, $report) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Common header
            fputcsv($handle, ["Lara's Flowershop"]);
            fputcsv($handle, [$this->reportTitle($report)]);
            fputcsv($handle, ['Period', $data['periodLabel']]);
            fputcsv($handle, ['Generated', now()->format('M d, Y h:i A')]);
            fputcsv($handle, []);

            switch ($report) {
                case 'sales':
                    $this->writeSalesCsv($handle, $data);
                    break;
                case 'online-orders':
                    $this->writeOrdersCsv($handle, $data);
                    break;
                case 'stock':
                    $this->writeStockCsv($handle, $data);
                    break;
                case 'stock-in':
                    $this->writeStockInCsv($handle, $data);
                    break;
                case 'production':
                    $this->writeProductionCsv($handle, $data);
                    break;
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function reportTitle(string $report): string
    {
        return match ($report) {
            'sales' => 'Sales Report',
            'online-orders' => 'Online Orders Report',
            'stock' => 'Overall Stock Report',
            'stock-in' => 'Stock-in Report',
            'production' => 'Production Report',
            default => 'Report',
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
        fputcsv($handle, ['Cash Payments (₱)', number_format($s['paymentCash'], 2)]);
        fputcsv($handle, ['GCash Payments (₱)', number_format($s['paymentGcash'], 2)]);
        fputcsv($handle, []);

        fputcsv($handle, ['SALES RECORDS']);
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
        fputcsv($handle, ['Avg. Order Value (₱)', number_format($s['avgOrder'], 2)]);
        fputcsv($handle, []);

        fputcsv($handle, ['STATUS BREAKDOWN']);
        foreach ($s['statusCounts'] as $status => $count) {
            fputcsv($handle, [ucfirst($status), $count]);
        }
        fputcsv($handle, []);

        fputcsv($handle, ['ORDERS']);
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

    private function writeStockCsv($handle, array $data): void
    {
        $s = $data['stockData'];
        fputcsv($handle, ['SUMMARY']);
        fputcsv($handle, ['Total Items', $s['totalItems']]);
        fputcsv($handle, ['Products', $s['totalProducts']]);
        fputcsv($handle, ['Materials', $s['totalMaterials']]);
        fputcsv($handle, ['Low Stock', $s['lowStockCount']]);
        fputcsv($handle, ['Out of Stock', $s['outOfStockCount']]);
        fputcsv($handle, []);

        fputcsv($handle, ['CURRENT STOCK']);
        fputcsv($handle, ['Item', 'Type', 'Retail', 'Production', 'Total', 'Unit']);

        foreach ($data['stockProducts'] as $product) {
            fputcsv($handle, [
                $product->name . ($product->variation ? ' — ' . $product->variation : ''),
                $product->item_type === 'material' ? 'Material' : 'Product',
                $product->retail_stock,
                $product->production_stock,
                $product->total_stock,
                $product->stock_unit,
            ]);
        }
    }

    private function writeStockInCsv($handle, array $data): void
    {
        $s = $data['stockInData'];
        fputcsv($handle, ['SUMMARY']);
        fputcsv($handle, ['Transactions', $s['transactions']]);
        fputcsv($handle, ['Items Received', (float) $s['totalItems']]);
        fputcsv($handle, ['Total Spent (₱)', number_format($s['totalSpent'], 2)]);
        fputcsv($handle, ['Avg. Transaction (₱)', number_format($s['avgTransaction'], 2)]);
        fputcsv($handle, []);

        fputcsv($handle, ['STOCK-IN RECORDS']);
        fputcsv($handle, ['Date & Time', 'Supplier', 'Recorded By', 'Total (₱)']);

        foreach ($data['purchaseRecords'] as $purchase) {
            fputcsv($handle, [
                $purchase->purchase_date->format('M d, Y h:i A'),
                $purchase->supplier_name ?: '—',
                $purchase->user->full_name ?? 'Unknown',
                number_format($purchase->total_amount, 2),
            ]);
        }
    }

    private function writeProductionCsv($handle, array $data): void
    {
        $s = $data['productionData'];
        fputcsv($handle, ['SUMMARY']);
        fputcsv($handle, ['Batches', $s['batches']]);
        fputcsv($handle, ['Total Produced', (float) $s['totalQuantity']]);
        fputcsv($handle, ['Unique Products', $s['uniqueProducts']]);
        fputcsv($handle, ['Avg. per Batch', (float) $s['avgBatch']]);
        fputcsv($handle, []);

        fputcsv($handle, ['PRODUCTION RECORDS']);
        fputcsv($handle, ['Date & Time', 'Product', 'Produced By', 'Quantity']);

        foreach ($data['productionRecords'] as $production) {
            fputcsv($handle, [
                $production->production_date->format('M d, Y h:i A'),
                $production->product->display_name,
                $production->producedBy->full_name ?? 'Unknown',
                (float) $production->quantity_produced . ' ' . $production->product->stock_unit,
            ]);
        }
    }

    /* ============================================
       SHARED DATA BUILDER
       ============================================ */
    private function buildData(Request $request): array
    {
        $report = $request->input('report', 'sales');
        $period = $request->input('period', 'weekly');
        $date = $request->input('date', now()->toDateString());

        [$from, $to, $periodLabel] = $this->resolvePeriod($period, $date);

        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $data = [
            'report' => $report,
            'period' => $period,
            'date' => $date,
            'from' => $from,
            'to' => $to,
            'periodLabel' => $periodLabel,
        ];

        switch ($report) {
            case 'sales':
                $data += $this->salesData($fromDate, $toDate);
                break;
            case 'online-orders':
                $data += $this->onlineOrdersData($fromDate, $toDate);
                break;
            case 'stock':
                $data += $this->stockData();
                break;
            case 'stock-in':
                $data += $this->stockInData($fromDate, $toDate);
                break;
            case 'production':
                $data += $this->productionData($fromDate, $toDate);
                break;
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
        $itemsSold = $sales->sum(function ($sale) {
            return $sale->items->sum('quantity');
        });

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
                    'confirmed' => $orders->where('order_status', 'confirmed')->count(),
                    'preparing' => $orders->where('order_status', 'preparing')->count(),
                    'ready' => $orders->where('order_status', 'ready')->count(),
                    'completed' => $orders->where('order_status', 'completed')->count(),
                    'cancelled' => $orders->where('order_status', 'cancelled')->count(),
                ],
            ],
            'orderRecords' => $orders,
        ];
    }

    private function stockData(): array
    {
        $products = Product::with('inventory')
            ->where('is_active', true)
            ->orderBy('item_type')
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                $retail = $product->inventory->firstWhere('reserve_type', 'retail');
                $production = $product->inventory->firstWhere('reserve_type', 'production');
                $threshold = (float) $product->low_stock_threshold;

                $product->retail_stock = $retail ? (float) $retail->current_quantity : 0;
                $product->production_stock = $production ? (float) $production->current_quantity : 0;
                $product->total_stock = $product->retail_stock + $product->production_stock;

                $product->retail_low = $retail && $product->retail_stock <= $threshold;
                $product->production_low = $production && $product->production_stock <= $threshold;
                $product->has_low = $product->retail_low || $product->production_low;

                return $product;
            });

        return [
            'stockData' => [
                'totalItems' => $products->count(),
                'totalProducts' => $products->where('item_type', 'made_product')->count(),
                'totalMaterials' => $products->where('item_type', 'material')->count(),
                'lowStockCount' => $products->where('has_low', true)->count(),
                'outOfStockCount' => $products->filter(function ($p) {
                    return $p->retail_stock <= 0 && $p->production_stock <= 0;
                })->count(),
            ],
            'stockProducts' => $products,
        ];
    }

    private function stockInData(Carbon $from, Carbon $to): array
    {
        $purchases = Purchase::with(['user', 'items'])
            ->whereBetween('purchase_date', [$from, $to])
            ->orderByDesc('purchase_date')
            ->get();

        $totalSpent = $purchases->sum('total_amount');
        $totalItems = $purchases->sum(function ($p) {
            return $p->items->sum('quantity');
        });

        return [
            'stockInData' => [
                'transactions' => $purchases->count(),
                'totalSpent' => $totalSpent,
                'totalItems' => (float) $totalItems,
                'avgTransaction' => $purchases->count() > 0 ? $totalSpent / $purchases->count() : 0,
            ],
            'purchaseRecords' => $purchases,
        ];
    }

    private function productionData(Carbon $from, Carbon $to): array
    {
        $productions = Production::with(['product', 'producedBy'])
            ->whereBetween('production_date', [$from, $to])
            ->orderByDesc('production_date')
            ->get();

        $totalQuantity = $productions->sum('quantity_produced');
        $uniqueProducts = $productions->pluck('product_id')->unique()->count();

        return [
            'productionData' => [
                'batches' => $productions->count(),
                'totalQuantity' => (float) $totalQuantity,
                'uniqueProducts' => $uniqueProducts,
                'avgBatch' => $productions->count() > 0 ? $totalQuantity / $productions->count() : 0,
            ],
            'productionRecords' => $productions,
        ];
    }
}