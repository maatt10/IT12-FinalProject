<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
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

        // Shared filters
        $search = trim((string) $request->query('search', ''));
        $from = $request->query('from');
        $to = $request->query('to');

        $data = [
            'tab' => $tab,
            'sub' => $sub,
            'search' => $search,
            'from' => $from,
            'to' => $to,
            'payment' => $request->query('payment'),
            'status' => $request->query('status'),
            'channel' => $request->query('channel'),
            'regular' => $request->query('regular'),
        ];

        if ($tab === 'sales' && $sub === 'walk-in') {
            $data['sales'] = $this->walkInSales($request);
        } elseif ($tab === 'sales' && $sub === 'online') {
            $data['orders'] = $this->onlineOrders($request);
        } elseif ($tab === 'purchases') {
            $data['purchases'] = $this->purchases($request);
        } elseif ($tab === 'production') {
            $data['productions'] = $this->productions($request);
        } elseif ($tab === 'customers') {
            $data['customers'] = $this->customers($request);
        }

        return view('records.index', $data);
    }

    /* ============================================
       TABS
       ============================================ */
    private function walkInSales(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $from = $request->query('from');
        $to = $request->query('to');
        $payment = $request->query('payment');

        $query = Sale::with(['customer', 'user']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('reference_code', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('first_name', 'LIKE', "%{$search}%")
                         ->orWhere('middle_name', 'LIKE', "%{$search}%")
                         ->orWhere('last_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($from) $query->whereDate('sale_date', '>=', $from);
        if ($to) $query->whereDate('sale_date', '<=', $to);
        if (in_array($payment, ['cash', 'gcash'])) $query->where('payment_method', $payment);

        return $query->orderByDesc('sale_date')->paginate(10)->withQueryString();
    }

    private function onlineOrders(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $from = $request->query('from');
        $to = $request->query('to');
        $status = $request->query('status');
        $channel = $request->query('channel');

        $query = Order::with(['customer']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('reference_code', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('receiver_first_name', 'LIKE', "%{$search}%")
                  ->orWhere('receiver_last_name', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('first_name', 'LIKE', "%{$search}%")
                         ->orWhere('last_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($from) $query->whereDate('order_date', '>=', $from);
        if ($to) $query->whereDate('order_date', '<=', $to);
        if (in_array($status, ['pending', 'completed', 'cancelled'])) $query->where('order_status', $status);
        if (in_array($channel, ['online', 'walk_in'])) $query->where('channel', $channel);

        return $query->orderByDesc('order_date')->paginate(10)->withQueryString();
    }

    private function purchases(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $from = $request->query('from');
        $to = $request->query('to');

        $query = Purchase::with('user');

        if ($search !== '') {
            $query->where('supplier_name', 'LIKE', "%{$search}%");
        }

        if ($from) $query->whereDate('purchase_date', '>=', $from);
        if ($to) $query->whereDate('purchase_date', '<=', $to);

        return $query->orderByDesc('purchase_date')->paginate(10)->withQueryString();
    }

    private function productions(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $from = $request->query('from');
        $to = $request->query('to');

        $query = Production::with(['product', 'producedBy', 'order']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'LIKE', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('variation', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('reference_code', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($from) $query->whereDate('production_date', '>=', $from);
        if ($to) $query->whereDate('production_date', '<=', $to);

        return $query->orderByDesc('production_date')->paginate(10)->withQueryString();
    }

    private function customers(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $regular = $request->query('regular');

        $query = Customer::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('middle_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('contact_number', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        if ($regular === 'yes') $query->where('is_regular', true);
        if ($regular === 'no')  $query->where('is_regular', false);

        return $query
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(10)
            ->withQueryString();
    }
}