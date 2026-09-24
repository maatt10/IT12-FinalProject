<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Sale;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $todaySales = Sale::whereDate('sale_date', $today)
            ->sum('total_amount');

        $todayTransactions = Sale::whereDate('sale_date', $today)
            ->count();

        $recentSales = Sale::with('customer')
            ->orderByDesc('sale_date')
            ->paginate(5);

        $pendingOrders = collect();

        if (auth()->user()->role === 'owner') {
            $pendingOrders = Order::whereIn('order_status', [
                    'pending',
                    'confirmed',
                    'preparing',
                    'ready',
                ])
                ->orderByDesc('order_date')
                ->paginate(5);
        }

        return view('dashboard', compact(
            'todaySales',
            'todayTransactions',
            'recentSales',
            'pendingOrders'
        ));
    }
}