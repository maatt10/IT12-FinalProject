<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Production;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;

class RecordsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'sales');
        $sub = $request->query('sub', 'walk-in');

        // Sanitize
        if (!in_array($tab, ['sales', 'purchases', 'production', 'customers'])) {
            $tab = 'sales';
        }
        if ($tab === 'sales' && !in_array($sub, ['walk-in', 'online'])) {
            $sub = 'walk-in';
        }

        $data = [
            'tab' => $tab,
            'sub' => $sub,
        ];

        if ($tab === 'sales' && $sub === 'walk-in') {
            $data['sales'] = Sale::with(['customer', 'user'])
                ->orderByDesc('sale_date')
                ->paginate(10, ['*'], 'page');
        } elseif ($tab === 'sales' && $sub === 'online') {
            $data['orders'] = Order::with(['customer'])
                ->orderByDesc('order_date')
                ->paginate(10, ['*'], 'page');
        } elseif ($tab === 'purchases') {
            $data['purchases'] = Purchase::with('user')
                ->orderByDesc('purchase_date')
                ->paginate(10, ['*'], 'page');
        } elseif ($tab === 'production') {
            $data['productions'] = Production::with(['product', 'producedBy', 'order'])
                ->orderByDesc('production_date')
                ->paginate(10, ['*'], 'page');
        } elseif ($tab === 'customers') {
            $data['customers'] = Customer::orderBy('last_name')
                ->orderBy('first_name')
                ->paginate(10, ['*'], 'page');
        }

        return view('records.index', $data);
    }
}
