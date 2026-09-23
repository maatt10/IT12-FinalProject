<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('customer')
            ->orderByDesc('order_date')
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $products = Product::where('is_sellable', true)
            ->orderBy('name')
            ->orderBy('variation')
            ->get();

        return view('orders.create', compact(
            'customers',
            'products'
        ));
    }

    public function show(Order $order)
    {
        $order->load([
            'customer',
            'user',
            'items.product',
        ]);

        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_status' => [
                'required',
                'in:pending,confirmed,preparing,ready,completed,cancelled',
            ],
        ]);

        $oldStatus = $order->order_status;

        $order->update([
            'order_status' => $validated['order_status'],
        ]);

        app(AuditLogger::class)->log(
            'update',
            'orders',
            $order->order_id,
            'Order status changed from ' .
                ucfirst($oldStatus) .
                ' to ' .
                ucfirst($order->order_status) .
                '.'
        );

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order status updated successfully.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => [
                'required',
                'exists:customers,customer_id',
            ],

            'order_type' => [
                'required',
                'in:ready_made,customized',
            ],

            'receiver_first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'receiver_middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'receiver_last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'receiver_contact' => [
                'required',
                'string',
                'max:30',
            ],

            'delivery_address' => [
                'nullable',
                'string',
                'max:500',
            ],

            'delivery_timing' => [
                'required',
                'string',
                'max:255',
            ],

            'fulfillment_type' => [
                'required',
                'in:pickup,delivery',
            ],

            'delivery_fee' => [
                'required',
                'numeric',
                'min:0',
            ],

            'payment_proof_reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'exists:products,product_id',
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'items.*.customization_details' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            $subtotal = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {

                $product = Product::findOrFail(
                    $item['product_id']
                );

                if (!$product->is_sellable) {
                    throw new \Exception(
                        "{$product->name} is not available for orders."
                    );
                }

                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $product->selling_price;
                $lineTotal = $quantity * $unitPrice;

                $subtotal += $lineTotal;

                $orderItems[] = [
                    'product_id' => $product->product_id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'customization_details' =>
                        $item['customization_details'] ?? null,
                    'line_total' => $lineTotal,
                ];
            }

            $deliveryFee = (float) $validated['delivery_fee'];

            if ($validated['fulfillment_type'] === 'pickup') {
                $deliveryFee = 0;
            }

            $total = $subtotal + $deliveryFee;

            $order = Order::create([
                'customer_id' =>
                    $validated['customer_id'],

                'user_id' =>
                    auth()->user()->user_id,

                'order_type' =>
                    $validated['order_type'],

                'receiver_first_name' =>
                    $validated['receiver_first_name'],

                'receiver_middle_name' =>
                    $validated['receiver_middle_name'] ?? null,

                'receiver_last_name' =>
                    $validated['receiver_last_name'],

                'receiver_contact' =>
                    $validated['receiver_contact'],

                'delivery_address' =>
                    $validated['delivery_address'] ?? null,

                'delivery_timing' =>
                    $validated['delivery_timing'],

                'fulfillment_type' =>
                    $validated['fulfillment_type'],

                'delivery_fee' =>
                    $deliveryFee,

                'payment_proof_reference' =>
                    $validated['payment_proof_reference'] ?? null,

                'order_status' =>
                    'pending',

                'total_amount' =>
                    $total,

                'order_date' =>
                    now(),
            ]);

            foreach ($orderItems as $item) {
                $item['order_id'] = $order->order_id;

                OrderItem::create($item);
            }

            /*
             * Record the order creation in the audit trail.
             */
            app(AuditLogger::class)->log(
                'create',
                'orders',
                $order->order_id,
                'Online order recorded for ' .
                    $order->receiver_full_name .
                    '. Total: ₱' .
                    number_format((float) $order->total_amount, 2) .
                    ', Status: Pending.'
            );
        });

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order recorded successfully.');
    }
}