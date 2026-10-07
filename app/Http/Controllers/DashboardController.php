<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $isOwner = auth()->user()->role === 'owner';

        /* ============================================
           RANGE PARAMETER (7 or 30 days)
           ============================================ */
        $range = (int) $request->input('range', 7);
        if (!in_array($range, [7, 30])) {
            $range = 7;
        }

        /* ============================================
           TODAY'S METRICS (Sales + Online Orders)
           ============================================ */
        $todaySales = Sale::with('items')
            ->whereDate('sale_date', $today)
            ->get();

        $todaySaleRevenue = (float) $todaySales->sum('total_amount');
        $todayTransactions = $todaySales->count();
        $todayItemsSold = (float) $todaySales->sum(function ($sale) {
            return $sale->items->sum('quantity');
        });

        // Online orders (non-cancelled)
        $todayOrders = Order::whereDate('order_date', $today)
            ->whereNotIn('order_status', ['cancelled'])
            ->get();

        $todayOrderRevenue = (float) $todayOrders->sum('total_amount');
        $todayOnlineOrders = Order::whereDate('order_date', $today)->count();

        // Combined revenue
        $todayRevenue = $todaySaleRevenue + $todayOrderRevenue;

        /* ============================================
           SALES OVERVIEW — RANGE DAYS (Sales + Orders)
           ============================================ */
        $chartData = collect();
        for ($i = $range - 1; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);

            $saleRevenue = (float) Sale::whereDate('sale_date', $date)->sum('total_amount');

            $orderRevenue = (float) Order::whereDate('order_date', $date)
                ->whereNotIn('order_status', ['cancelled'])
                ->sum('total_amount');

            $total = $saleRevenue + $orderRevenue;

            $chartData->push([
                'label' => $date->format('D'),
                'full_label' => $date->format('M d'),
                'short_date' => $date->format('M d'),
                'revenue' => $total,
                'walk_in' => $saleRevenue,
                'online' => $orderRevenue,
            ]);
        }

        $maxRevenue = $chartData->max('revenue') ?: 1;
        $rangeRevenue = $chartData->sum('revenue');
        $rangeAverage = $rangeRevenue / $range;

        $bestDay = $chartData->sortByDesc('revenue')->first();

        /* ============================================
           LOW STOCK ALERTS
           ============================================ */
        $lowStockItems = Product::with('inventory')
            ->where('is_active', true)
            ->get()
            ->map(function ($product) {
                $retail = $product->inventory->firstWhere('reserve_type', 'retail');
                $production = $product->inventory->firstWhere('reserve_type', 'production');
                $threshold = (float) $product->low_stock_threshold;

                $product->retail_stock = $retail ? (float) $retail->current_quantity : 0;
                $product->production_stock = $production ? (float) $production->current_quantity : 0;
                $product->has_retail = $retail !== null;
                $product->has_production = $production !== null;
                $product->retail_low = $retail && $product->retail_stock <= $threshold;
                $product->production_low = $production && $product->production_stock <= $threshold;
                $product->has_low = $product->retail_low || $product->production_low;

                return $product;
            })
            ->filter(fn ($p) => $p->has_low)
            ->sortBy('name')
            ->take(10)
            ->values();

        /* ============================================
           ORDER STATUS COUNTS (OWNER)
           ============================================ */
        $orderStatusCounts = [
            'pending' => 0,
            'completed' => 0,
            'cancelled' => 0,
        ];

        if ($isOwner) {
            $orderStatusCounts['pending'] = Order::where('order_status', 'pending')->count();
            $orderStatusCounts['completed'] = Order::where('order_status', 'completed')->count();
            $orderStatusCounts['cancelled'] = Order::where('order_status', 'cancelled')->count();
        }

        /* ============================================
           RECENT SALES (EMPLOYEE)
           ============================================ */
        $recentSales = Sale::with(['customer', 'user'])
            ->orderByDesc('sale_date')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'isOwner',
            'range',
            'todayRevenue',
            'todaySaleRevenue',
            'todayOrderRevenue',
            'todayTransactions',
            'todayItemsSold',
            'todayOnlineOrders',
            'chartData',
            'maxRevenue',
            'rangeRevenue',
            'rangeAverage',
            'bestDay',
            'lowStockItems',
            'orderStatusCounts',
            'recentSales',
        ));
    }
}