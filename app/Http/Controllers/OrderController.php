<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
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

        // Only bouquets — items that are made products, sellable, and have "bouquet" in the name
        $products = Product::with('inventory')
            ->where('item_type', 'made_product')
            ->whereIn('stock_purpose', ['retail', 'both'])
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('name', 'LIKE', '%bouquet%')
                    ->orWhere('variation', 'LIKE', '%bouquet%');
            })
            ->orderBy('name')
            ->orderBy('variation')
            ->get()
            ->map(function ($product) {
                $retail = $product->inventory->firstWhere('reserve_type', 'retail');
                $product->retail_stock = $retail ? (float) $retail->current_quantity : 0;
                return $product;
            });

        return view('orders.create', compact('customers', 'products'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'user', 'items.product']);
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        // Lock completed and cancelled orders
        if (in_array($order->order_status, ['completed', 'cancelled'])) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'This order is already ' . $order->order_status . ' and cannot be changed.');
        }

        $validated = $request->validate([
            'order_status' => ['required', 'in:pending,confirmed,preparing,ready,completed,cancelled'],
        ]);

        $oldStatus = $order->order_status;
        $newStatus = $validated['order_status'];

        DB::transaction(function () use ($order, $oldStatus, $newStatus) {

            // If cancelling → return stock to retail
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    if (!$item->product_id) continue;

                    $inventory = \App\Models\Inventory::where('product_id', $item->product_id)
                        ->where('reserve_type', 'retail')
                        ->lockForUpdate()
                        ->first();

                    if (!$inventory) continue;

                    $inventory->increment('current_quantity', $item->quantity);
                    $inventory->update(['last_updated' => now()]);

                    \App\Models\InventoryTransaction::create([
                        'inventory_id' => $inventory->inventory_id,
                        'transaction_type' => 'return_in',
                        'quantity_change' => $item->quantity,
                        'reference_id' => $order->order_id,
                        'reference_type' => 'order_cancellation',
                        'notes' => 'Returned to stock — order ' . $order->reference_code . ' cancelled.',
                        'transaction_date' => now(),
                        'recorded_by' => auth()->user()->user_id,
                    ]);
                }
            }

            $order->update(['order_status' => $newStatus]);

            app(AuditLogger::class)->log(
                'update',
                'orders',
                $order->order_id,
                'Order status changed from ' . ucfirst($oldStatus) . ' to ' . ucfirst($newStatus) . '.'
            );
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order status updated successfully.');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,customer_id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'order_type' => ['required', 'in:ready_made,customized'],
            'receiver_first_name' => ['required', 'string', 'max:100'],
            'receiver_middle_name' => ['nullable', 'string', 'max:100'],
            'receiver_last_name' => ['required', 'string', 'max:100'],
            'receiver_contact' => ['required', 'regex:/^09\d{9}$/'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'delivery_datetime' => ['required', 'date'],
            'fulfillment_type' => ['required', 'in:pickup,delivery'],
            'delivery_fee' => ['required', 'numeric', 'min:0'],
            'payment_proof_reference' => ['required', 'digits:13'],

            'discount_type' => ['required', 'in:none,pwd,senior'],
            'discount_name' => ['nullable', 'string', 'max:120'],
            'discount_id_number' => ['nullable', 'string', 'max:30'],

            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['required', 'exists:products,product_id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],

            'custom_description' => ['nullable', 'string', 'max:1000'],
            'custom_quantity' => ['nullable', 'numeric', 'gt:0'],
            'custom_unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        if (empty($validated['customer_id']) && empty($validated['customer_name'])) {
            return back()->withErrors([
                'customer_id' => 'Select a registered customer or provide an unregistered customer name.',
            ])->withInput();
        }

        if ($validated['order_type'] === 'ready_made') {
            if (empty($validated['items'])) {
                return back()->withErrors(['items' => 'Please add at least one bouquet.'])->withInput();
            }
        } else {
            if (empty($validated['custom_description']) || empty($validated['custom_unit_price'])) {
                return back()->withErrors([
                    'custom_description' => 'Provide a description and price for the customized bouquet.',
                ])->withInput();
            }
        }

        if ($validated['discount_type'] === 'pwd') {
            if (empty($validated['discount_name'])) {
                return back()->withErrors(['discount_name' => 'Enter the name on the PWD ID.'])->withInput();
            }
            $digits = preg_replace('/\D/', '', $validated['discount_id_number'] ?? '');
            if (strlen($digits) !== 16) {
                return back()->withErrors(['discount_id_number' => 'PWD ID must contain exactly 16 digits.'])->withInput();
            }
        }

        if ($validated['discount_type'] === 'senior') {
            if (empty($validated['discount_name'])) {
                return back()->withErrors(['discount_name' => 'Enter the name on the Senior Citizen ID.'])->withInput();
            }
            if (strlen($validated['discount_id_number'] ?? '') < 4) {
                return back()->withErrors(['discount_id_number' => 'Senior ID must be at least 4 characters.'])->withInput();
            }
        }

        $orderId = null;

        try {
            DB::transaction(function () use ($validated, &$orderId) {

                $subtotal = 0;
                $orderItems = [];
                $stockToDeduct = []; // product_id => quantity to deduct

                if ($validated['order_type'] === 'ready_made') {

                    foreach ($validated['items'] as $item) {
                        $product = Product::findOrFail($item['product_id']);

                        if ($product->item_type !== 'made_product' || !in_array($product->stock_purpose, ['retail', 'both'])) {
                            throw new \Exception("{$product->name} is not available for orders.");
                        }

                        $quantity = (float) $item['quantity'];

                        // Check retail stock
                        $retailInventory = Inventory::where('product_id', $product->product_id)
                            ->where('reserve_type', 'retail')
                            ->lockForUpdate()
                            ->first();

                        $available = $retailInventory ? (float) $retailInventory->current_quantity : 0;

                        if ($available < $quantity) {
                            throw new \Exception(
                                "Insufficient retail stock for {$product->name}. " .
                                    "Available: " . number_format($available, 2) . ", Requested: " . number_format($quantity, 2) . "."
                            );
                        }

                        $unitPrice = (float) $product->selling_price;
                        $lineTotal = $quantity * $unitPrice;
                        $subtotal += $lineTotal;

                        $orderItems[] = [
                            'product_id' => $product->product_id,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'customization_details' => null,
                            'line_total' => $lineTotal,
                        ];

                        // Track for deduction
                        if (!isset($stockToDeduct[$product->product_id])) {
                            $stockToDeduct[$product->product_id] = 0;
                        }
                        $stockToDeduct[$product->product_id] += $quantity;
                    }
                } else {
                    $quantity = (float) $validated['custom_quantity'];
                    $unitPrice = (float) $validated['custom_unit_price'];
                    $lineTotal = $quantity * $unitPrice;
                    $subtotal += $lineTotal;

                    $orderItems[] = [
                        'product_id' => null,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'customization_details' => $validated['custom_description'],
                        'line_total' => $lineTotal,
                    ];
                    // No stock deduction for customized orders
                }

                $discountAmount = 0;
                if ($validated['discount_type'] !== 'none') {
                    $discountAmount = $subtotal * 0.20;
                }

                $deliveryFee = (float) $validated['delivery_fee'];
                if ($validated['fulfillment_type'] === 'pickup') {
                    $deliveryFee = 0;
                }

                $total = $subtotal - $discountAmount + $deliveryFee;

                $order = Order::create([
                    'customer_id' => $validated['customer_id'] ?? null,
                    'customer_name' => $validated['customer_id'] ? null : $validated['customer_name'],
                    'user_id' => auth()->user()->user_id,
                    'order_type' => $validated['order_type'],
                    'receiver_first_name' => $validated['receiver_first_name'],
                    'receiver_middle_name' => $validated['receiver_middle_name'] ?? null,
                    'receiver_last_name' => $validated['receiver_last_name'],
                    'receiver_contact' => $validated['receiver_contact'],
                    'delivery_address' => $validated['delivery_address'] ?? null,
                    'delivery_datetime' => $validated['delivery_datetime'],
                    'fulfillment_type' => $validated['fulfillment_type'],
                    'delivery_fee' => $deliveryFee,
                    'subtotal' => $subtotal,
                    'discount_type' => $validated['discount_type'],
                    'discount_name' => $validated['discount_type'] === 'none' ? null : $validated['discount_name'],
                    'discount_id_number' => $validated['discount_type'] === 'none' ? null : $validated['discount_id_number'],
                    'discount_amount' => $discountAmount,
                    'payment_proof_reference' => $validated['payment_proof_reference'],
                    'order_status' => 'pending',
                    'total_amount' => $total,
                    'order_date' => now(),
                ]);

                // Reference code
                $datePart = $order->order_date->format('dmy');
                $prefix = 'ORD-' . $datePart . '-';

                $lastToday = Order::where('reference_code', 'LIKE', $prefix . '%')
                    ->orderByDesc('reference_code')
                    ->value('reference_code');

                $nextNumber = 1;
                if ($lastToday) {
                    $parts = explode('-', $lastToday);
                    $nextNumber = intval(end($parts)) + 1;
                }

                $order->update([
                    'reference_code' => $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT),
                ]);

                $orderId = $order->order_id;

                // Create order items
                foreach ($orderItems as $item) {
                    $item['order_id'] = $order->order_id;
                    OrderItem::create($item);
                }

                // Deduct stock for ready-made items
                foreach ($stockToDeduct as $productId => $quantity) {
                    $inventory = Inventory::where('product_id', $productId)
                        ->where('reserve_type', 'retail')
                        ->lockForUpdate()
                        ->first();

                    if (!$inventory) {
                        throw new \Exception("Inventory record missing for product ID {$productId}.");
                    }

                    $previousQty = (float) $inventory->current_quantity;
                    $inventory->update([
                        'current_quantity' => $previousQty - $quantity,
                        'last_updated' => now(),
                    ]);

                    InventoryTransaction::create([
                        'inventory_id' => $inventory->inventory_id,
                        'transaction_type' => 'sale_out',
                        'quantity_change' => -$quantity,
                        'reference_id' => $order->order_id,
                        'reference_type' => 'order',
                        'notes' => 'Reserved for online order ' . $order->reference_code,
                        'transaction_date' => now(),
                        'recorded_by' => auth()->user()->user_id,
                    ]);
                }

                app(AuditLogger::class)->log(
                    'create',
                    'orders',
                    $order->order_id,
                    'Online order recorded for ' . $order->receiver_full_name .
                        '. Total: ₱' . number_format((float) $order->total_amount, 2) .
                        ', Status: Pending.'
                );
            });

            return redirect()
                ->route('orders.print', $orderId)
                ->with('success', 'Order recorded successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function print(Order $order)
    {
        $order->load(['items.product', 'customer', 'user']);
        return view('orders.print', compact('order'));
    }

    public function edit(Order $order)
    {
        // Block editing completed/cancelled orders
        if (in_array($order->order_status, ['completed', 'cancelled'])) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Cannot edit an order that is already ' . $order->order_status . '.');
        }

        $order->load(['items.product', 'customer']);

        $customers = \App\Models\Customer::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $products = \App\Models\Product::with('inventory')
            ->where('item_type', 'made_product')
            ->whereIn('stock_purpose', ['retail', 'both'])
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('name', 'LIKE', '%bouquet%')
                    ->orWhere('variation', 'LIKE', '%bouquet%');
            })
            ->orderBy('name')
            ->orderBy('variation')
            ->get()
            ->map(function ($product) {
                $retail = $product->inventory->firstWhere('reserve_type', 'retail');
                $product->retail_stock = $retail ? (float) $retail->current_quantity : 0;
                return $product;
            });

        return view('orders.edit', compact('order', 'customers', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        if (in_array($order->order_status, ['completed', 'cancelled'])) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Cannot edit an order that is already ' . $order->order_status . '.');
        }

        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,customer_id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'receiver_first_name' => ['required', 'string', 'max:100'],
            'receiver_middle_name' => ['nullable', 'string', 'max:100'],
            'receiver_last_name' => ['required', 'string', 'max:100'],
            'receiver_contact' => ['required', 'regex:/^09\d{9}$/'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'delivery_datetime' => ['required', 'date'],
            'fulfillment_type' => ['required', 'in:pickup,delivery'],
            'delivery_fee' => ['required', 'numeric', 'min:0'],
            'payment_proof_reference' => ['required', 'digits:13'],

            'discount_type' => ['required', 'in:none,pwd,senior'],
            'discount_name' => ['nullable', 'string', 'max:120'],
            'discount_id_number' => ['nullable', 'string', 'max:30'],
        ]);

        if (empty($validated['customer_id']) && empty($validated['customer_name'])) {
            return back()->withErrors([
                'customer_id' => 'Select a registered customer or provide an unregistered customer name.',
            ])->withInput();
        }

        if ($validated['discount_type'] === 'pwd') {
            if (empty($validated['discount_name'])) {
                return back()->withErrors(['discount_name' => 'Enter the name on the PWD ID.'])->withInput();
            }
            $digits = preg_replace('/\D/', '', $validated['discount_id_number'] ?? '');
            if (strlen($digits) !== 16) {
                return back()->withErrors(['discount_id_number' => 'PWD ID must contain exactly 16 digits.'])->withInput();
            }
        }

        if ($validated['discount_type'] === 'senior') {
            if (empty($validated['discount_name'])) {
                return back()->withErrors(['discount_name' => 'Enter the name on the Senior Citizen ID.'])->withInput();
            }
            if (strlen($validated['discount_id_number'] ?? '') < 4) {
                return back()->withErrors(['discount_id_number' => 'Senior ID must be at least 4 characters.'])->withInput();
            }
        }

        DB::transaction(function () use ($validated, $order) {

            // Recalculate discount from existing subtotal
            $discountAmount = 0;
            if ($validated['discount_type'] !== 'none') {
                $discountAmount = (float) $order->subtotal * 0.20;
            }

            $deliveryFee = (float) $validated['delivery_fee'];
            if ($validated['fulfillment_type'] === 'pickup') {
                $deliveryFee = 0;
            }

            $total = (float) $order->subtotal - $discountAmount + $deliveryFee;

            $order->update([
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_id'] ? null : $validated['customer_name'],
                'receiver_first_name' => $validated['receiver_first_name'],
                'receiver_middle_name' => $validated['receiver_middle_name'] ?? null,
                'receiver_last_name' => $validated['receiver_last_name'],
                'receiver_contact' => $validated['receiver_contact'],
                'delivery_address' => $validated['delivery_address'] ?? null,
                'delivery_datetime' => $validated['delivery_datetime'],
                'fulfillment_type' => $validated['fulfillment_type'],
                'delivery_fee' => $deliveryFee,
                'discount_type' => $validated['discount_type'],
                'discount_name' => $validated['discount_type'] === 'none' ? null : $validated['discount_name'],
                'discount_id_number' => $validated['discount_type'] === 'none' ? null : $validated['discount_id_number'],
                'discount_amount' => $discountAmount,
                'payment_proof_reference' => $validated['payment_proof_reference'],
                'total_amount' => $total,
            ]);

            app(AuditLogger::class)->log(
                'update',
                'orders',
                $order->order_id,
                'Order ' . $order->reference_code . ' updated. New total: ₱' . number_format($total, 2) . '.'
            );
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }
}
