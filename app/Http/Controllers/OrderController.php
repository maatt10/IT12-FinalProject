<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\OrderComponent;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Production;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /* ============================================
       INDEX
       ============================================ */
    public function index()
    {
        $orders = Order::with('customer')
            ->orderByDesc('order_date')
            ->get();

        return view('orders.index', compact('orders'));
    }

    /* ============================================
       CREATE
       ============================================ */
    public function create()
    {
        $customers = Customer::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

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

        $materials = Product::with('inventory')
            ->where('item_type', 'material')
            ->whereIn('stock_purpose', ['production', 'both'])
            ->where('is_active', true)
            ->orderBy('name')
            ->orderBy('variation')
            ->get()
            ->map(function ($material) {
                $prod = $material->inventory->firstWhere('reserve_type', 'production');
                $material->production_stock = $prod ? (float) $prod->current_quantity : 0;
                return $material;
            });

        return view('orders.create', compact('customers', 'products', 'materials'));
    }

    /* ============================================
       STORE
       ============================================ */
    public function store(Request $request)
    {
        $rules = [
            'customer_id' => ['nullable', 'exists:customers,customer_id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'order_type' => ['required', 'in:ready_made,customized'],
            'channel' => ['required', 'in:online,walk_in'],
            'receiver_first_name' => ['required', 'string', 'max:100'],
            'receiver_middle_name' => ['nullable', 'string', 'max:100'],
            'receiver_last_name' => ['required', 'string', 'max:100'],
            'receiver_contact' => ['required', 'regex:/^09\d{9}$/'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'delivery_datetime' => ['required', 'date'],
            'fulfillment_type' => ['required', 'in:pickup,delivery'],
            'delivery_fee' => ['required', 'numeric', 'min:0'],

            'discount_type' => ['required', 'in:none,pwd,senior'],
            'discount_name' => ['nullable', 'string', 'max:120'],
            'discount_id_number' => ['nullable', 'string', 'max:30'],

            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['required', 'exists:products,product_id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],

            'components' => ['nullable', 'array'],
            'components.*.product_id' => ['required', 'exists:products,product_id'],
            'components.*.quantity' => ['required', 'numeric', 'gt:0'],

            'custom_description' => ['nullable', 'string', 'max:1000'],
            'custom_quantity' => ['nullable', 'numeric', 'gt:0'],
            'custom_unit_price' => ['nullable', 'numeric', 'min:0'],
        ];

        if ($request->input('channel') === 'online') {
            $rules['payment_proof_reference'] = ['required', 'digits:13'];
        } else {
            $rules['payment_method'] = ['required', 'in:cash,gcash'];
            $rules['amount_paid'] = ['nullable', 'numeric', 'min:0'];
            $rules['payment_proof_reference'] = ['nullable', 'digits:13'];
        }

        $validated = $request->validate($rules);

        if (empty($validated['customer_id']) && empty($validated['customer_name'])) {
            return back()->withErrors([
                'customer_id' => 'Select a registered customer or provide an unregistered customer name.',
            ])->withInput();
        }

        if ($validated['channel'] === 'walk_in') {
            if ($validated['payment_method'] === 'gcash' && empty($validated['payment_proof_reference'])) {
                return back()->withErrors([
                    'payment_proof_reference' => 'GCash reference is required for GCash payments.',
                ])->withInput();
            }
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
            if (empty($validated['components'])) {
                return back()->withErrors([
                    'components' => 'Please add at least one component material.',
                ])->withInput();
            }
        }

        $this->validateDiscount($validated);

        $orderId = null;

        try {
            DB::transaction(function () use ($validated, &$orderId) {

                $subtotal = 0;
                $orderItems = [];

                if ($validated['order_type'] === 'ready_made') {
                    foreach ($validated['items'] as $item) {
                        $product = Product::findOrFail($item['product_id']);

                        if ($product->item_type !== 'made_product' || !in_array($product->stock_purpose, ['retail', 'both'])) {
                            throw new \Exception("{$product->name} is not available for orders.");
                        }

                        $quantity = (float) $item['quantity'];
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
                }

                $discountAmount = $validated['discount_type'] !== 'none' ? $subtotal * 0.20 : 0;

                $deliveryFee = (float) $validated['delivery_fee'];
                if ($validated['fulfillment_type'] === 'pickup') {
                    $deliveryFee = 0;
                }

                $total = $subtotal - $discountAmount + $deliveryFee;

                $paymentMethod = null;
                $paymentReference = null;
                $amountPaid = null;
                $changeAmount = null;

                if ($validated['channel'] === 'online') {
                    $paymentMethod = 'gcash';
                    $paymentReference = $validated['payment_proof_reference'];
                } else {
                    $paymentMethod = $validated['payment_method'];
                    if ($paymentMethod === 'gcash') {
                        $paymentReference = $validated['payment_proof_reference'] ?? null;
                    } else {
                        $amountPaid = (float) ($validated['amount_paid'] ?? 0);
                        if ($amountPaid < $total) {
                            throw new \Exception('Amount paid is less than the order total.');
                        }
                        $changeAmount = $amountPaid - $total;
                    }
                }

                $order = Order::create([
                    'customer_id' => $validated['customer_id'] ?? null,
                    'customer_name' => $validated['customer_id'] ? null : $validated['customer_name'],
                    'user_id' => auth()->user()->user_id,
                    'order_type' => $validated['order_type'],
                    'channel' => $validated['channel'],
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
                    'payment_method' => $paymentMethod,
                    'payment_reference' => $paymentReference,
                    'payment_proof_reference' => $paymentReference,
                    'amount_paid' => $amountPaid,
                    'change_amount' => $changeAmount,
                    'order_status' => 'pending',
                    'total_amount' => $total,
                    'order_date' => now(),
                ]);

                $this->assignReferenceCode($order);
                $orderId = $order->order_id;

                foreach ($orderItems as $item) {
                    $item['order_id'] = $order->order_id;
                    OrderItem::create($item);
                }

                // Create component records for customized orders
                if ($validated['order_type'] === 'customized') {
                    foreach ($validated['components'] as $comp) {
                        OrderComponent::create([
                            'order_id' => $order->order_id,
                            'product_id' => $comp['product_id'],
                            'quantity' => (float) $comp['quantity'],
                        ]);
                    }
                }

                // IMMEDIATE STOCK DEDUCTION on order creation
                $this->deductStockForOrder($order);

                app(AuditLogger::class)->log(
                    'create',
                    'orders',
                    $order->order_id,
                    'Order recorded (' . $validated['channel'] . ') for ' . $order->receiver_full_name .
                        '. Total: ₱' . number_format((float) $order->total_amount, 2) .
                        '. Stock deducted.'
                );
            });

            return redirect()
                ->route('orders.print', $orderId)
                ->with('success', 'Order recorded successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /* ============================================
       SHOW
       ============================================ */
    public function show(Order $order)
    {
        $order->load(['items.product', 'customer', 'user', 'components.product']);
        return view('orders.show', compact('order'));
    }

    /* ============================================
       EDIT
       ============================================ */
    public function edit(Order $order)
    {
        if (in_array($order->order_status, ['completed', 'cancelled'])) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Cannot edit an order that is already ' . $order->order_status . '.');
        }

        $order->load(['items.product', 'customer', 'components.product']);

        $customers = Customer::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

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

        $materials = Product::with('inventory')
            ->where('item_type', 'material')
            ->whereIn('stock_purpose', ['production', 'both'])
            ->where('is_active', true)
            ->orderBy('name')
            ->orderBy('variation')
            ->get()
            ->map(function ($material) {
                $prod = $material->inventory->firstWhere('reserve_type', 'production');
                $material->production_stock = $prod ? (float) $prod->current_quantity : 0;
                return $material;
            });

        return view('orders.edit', compact('order', 'customers', 'products', 'materials'));
    }

    /* ============================================
       UPDATE
       ============================================ */
    public function update(Request $request, Order $order)
    {
        if (in_array($order->order_status, ['completed', 'cancelled'])) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Cannot edit an order that is already ' . $order->order_status . '.');
        }

        $rules = [
            'customer_id' => ['nullable', 'exists:customers,customer_id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'channel' => ['required', 'in:online,walk_in'],
            'receiver_first_name' => ['required', 'string', 'max:100'],
            'receiver_middle_name' => ['nullable', 'string', 'max:100'],
            'receiver_last_name' => ['required', 'string', 'max:100'],
            'receiver_contact' => ['required', 'regex:/^09\d{9}$/'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'delivery_datetime' => ['required', 'date'],
            'fulfillment_type' => ['required', 'in:pickup,delivery'],
            'delivery_fee' => ['required', 'numeric', 'min:0'],

            'discount_type' => ['required', 'in:none,pwd,senior'],
            'discount_name' => ['nullable', 'string', 'max:120'],
            'discount_id_number' => ['nullable', 'string', 'max:30'],

            'components' => ['nullable', 'array'],
            'components.*.product_id' => ['required', 'exists:products,product_id'],
            'components.*.quantity' => ['required', 'numeric', 'gt:0'],
        ];

        if ($request->input('channel') === 'online') {
            $rules['payment_proof_reference'] = ['required', 'digits:13'];
        } else {
            $rules['payment_method'] = ['required', 'in:cash,gcash'];
            $rules['amount_paid'] = ['nullable', 'numeric', 'min:0'];
            $rules['payment_proof_reference'] = ['nullable', 'digits:13'];
        }

        $validated = $request->validate($rules);

        if (empty($validated['customer_id']) && empty($validated['customer_name'])) {
            return back()->withErrors([
                'customer_id' => 'Select a registered customer or provide an unregistered customer name.',
            ])->withInput();
        }

        if ($validated['channel'] === 'walk_in') {
            if ($validated['payment_method'] === 'gcash' && empty($validated['payment_proof_reference'])) {
                return back()->withErrors([
                    'payment_proof_reference' => 'GCash reference is required for GCash payments.',
                ])->withInput();
            }
        }

        if ($order->order_type === 'customized' && empty($validated['components'])) {
            return back()->withErrors([
                'components' => 'Please add at least one component material.',
            ])->withInput();
        }

        $this->validateDiscount($validated);

        DB::transaction(function () use ($validated, $order) {

            // Reverse previous stock deduction
            $this->reverseStockForOrder($order);

            $discountAmount = $validated['discount_type'] !== 'none' ? (float) $order->subtotal * 0.20 : 0;

            $deliveryFee = (float) $validated['delivery_fee'];
            if ($validated['fulfillment_type'] === 'pickup') {
                $deliveryFee = 0;
            }

            $total = (float) $order->subtotal - $discountAmount + $deliveryFee;

            $paymentMethod = null;
            $paymentReference = null;
            $amountPaid = null;
            $changeAmount = null;

            if ($validated['channel'] === 'online') {
                $paymentMethod = 'gcash';
                $paymentReference = $validated['payment_proof_reference'];
            } else {
                $paymentMethod = $validated['payment_method'];
                if ($paymentMethod === 'gcash') {
                    $paymentReference = $validated['payment_proof_reference'] ?? null;
                } else {
                    $amountPaid = (float) ($validated['amount_paid'] ?? 0);
                    if ($amountPaid < $total) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'amount_paid' => 'Amount paid is less than the order total.',
                        ]);
                    }
                    $changeAmount = $amountPaid - $total;
                }
            }

            $order->update([
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_id'] ? null : $validated['customer_name'],
                'channel' => $validated['channel'],
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
                'payment_method' => $paymentMethod,
                'payment_reference' => $paymentReference,
                'payment_proof_reference' => $paymentReference,
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'total_amount' => $total,
            ]);

            // Sync component records for customized orders
            if ($order->order_type === 'customized') {
                $order->components()->delete();

                foreach ($validated['components'] as $comp) {
                    OrderComponent::create([
                        'order_id' => $order->order_id,
                        'product_id' => $comp['product_id'],
                        'quantity' => (float) $comp['quantity'],
                    ]);
                }
            }

            // Re-apply stock deduction based on new state
            $order->refresh();
            $this->deductStockForOrder($order);

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

    /* ============================================
       UPDATE STATUS
       ============================================ */
    public function updateStatus(Request $request, Order $order)
    {
        if (in_array($order->order_status, ['completed', 'cancelled'])) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'This order is already ' . $order->order_status . ' and cannot be changed.');
        }

        $validated = $request->validate([
            'order_status' => ['required', 'in:pending,completed,cancelled'],
            'cancellation_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldStatus = $order->order_status;
        $newStatus = $validated['order_status'];

        DB::transaction(function () use ($order, $oldStatus, $newStatus, $validated) {

            // If cancelling → return stock to inventory
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                $this->reverseStockForOrder($order);
            }

            $order->update([
                'order_status' => $newStatus,
                'cancellation_note' => $newStatus === 'cancelled'
                    ? ($validated['cancellation_note'] ?? null)
                    : null,
            ]);

            app(AuditLogger::class)->log(
                'update',
                'orders',
                $order->order_id,
                'Order status changed from ' . ucfirst($oldStatus) . ' to ' . ucfirst($newStatus) .
                    ($newStatus === 'cancelled' ? '. Stock returned.' : '.')
            );
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order status updated successfully.');
    }

    /* ============================================
       PRINT
       ============================================ */
    public function print(Order $order)
    {
        $order->load(['items.product', 'customer', 'user']);
        return view('orders.print', compact('order'));
    }

    /* ============================================
       HELPERS
       ============================================ */

    private function validateDiscount(array $validated): void
    {
        if ($validated['discount_type'] === 'pwd') {
            if (empty($validated['discount_name'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'discount_name' => 'Enter the name on the PWD ID.',
                ]);
            }
            $digits = preg_replace('/\D/', '', $validated['discount_id_number'] ?? '');
            if (strlen($digits) !== 16) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'discount_id_number' => 'PWD ID must contain exactly 16 digits.',
                ]);
            }
        }

        if ($validated['discount_type'] === 'senior') {
            if (empty($validated['discount_name'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'discount_name' => 'Enter the name on the Senior Citizen ID.',
                ]);
            }
            if (strlen($validated['discount_id_number'] ?? '') < 4) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'discount_id_number' => 'Senior ID must be at least 4 characters.',
                ]);
            }
        }
    }

    private function assignReferenceCode(Order $order): void
    {
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
    }

    /**
     * Immediately deduct stock when order is created.
     */
    private function deductStockForOrder(Order $order): void
    {
        if ($order->order_type === 'ready_made') {
            foreach ($order->items as $item) {
                if (!$item->product_id) continue;

                $inventory = Inventory::where('product_id', $item->product_id)
                    ->where('reserve_type', 'retail')
                    ->lockForUpdate()
                    ->first();

                if (!$inventory) continue;

                $inventory->update([
                    'current_quantity' => (float) $inventory->current_quantity - (float) $item->quantity,
                    'last_updated' => now(),
                ]);

                InventoryTransaction::create([
                    'inventory_id' => $inventory->inventory_id,
                    'transaction_type' => 'sale_out',
                    'quantity_change' => -(float) $item->quantity,
                    'reference_id' => $order->order_id,
                    'reference_type' => 'order',
                    'notes' => 'Deducted for order ' . $order->reference_code . '.',
                    'transaction_date' => now(),
                    'recorded_by' => auth()->user()->user_id,
                ]);
            }
        } else {
            // Customized — deduct each component from production pool
            $order->load('components');

            foreach ($order->components as $component) {
                $inventory = Inventory::where('product_id', $component->product_id)
                    ->where('reserve_type', 'production')
                    ->lockForUpdate()
                    ->first();

                if (!$inventory) {
                    $inventory = Inventory::create([
                        'product_id' => $component->product_id,
                        'reserve_type' => 'production',
                        'current_quantity' => 0,
                        'last_updated' => now(),
                    ]);
                }

                $inventory->update([
                    'current_quantity' => (float) $inventory->current_quantity - (float) $component->quantity,
                    'last_updated' => now(),
                ]);

                InventoryTransaction::create([
                    'inventory_id' => $inventory->inventory_id,
                    'transaction_type' => 'sale_out',
                    'quantity_change' => -(float) $component->quantity,
                    'reference_id' => $order->order_id,
                    'reference_type' => 'order',
                    'notes' => 'Custom bouquet component for ' . $order->reference_code . '.',
                    'transaction_date' => now(),
                    'recorded_by' => auth()->user()->user_id,
                ]);
            }

            // Log a production record for the custom bouquet
            Production::create([
                'product_id' => null,
                'order_id' => $order->order_id,
                'quantity_produced' => 1,
                'produced_by' => auth()->user()->user_id,
                'production_date' => now(),
                'notes' => 'Custom bouquet for order ' . $order->reference_code . '.',
            ]);
        }
    }

    /**
     * Return stock when order is cancelled (or on edit reversal).
     */
    private function reverseStockForOrder(Order $order): void
    {
        if ($order->order_type === 'ready_made') {
            foreach ($order->items as $item) {
                if (!$item->product_id) continue;

                $inventory = Inventory::where('product_id', $item->product_id)
                    ->where('reserve_type', 'retail')
                    ->lockForUpdate()
                    ->first();

                if (!$inventory) continue;

                $inventory->update([
                    'current_quantity' => (float) $inventory->current_quantity + (float) $item->quantity,
                    'last_updated' => now(),
                ]);

                InventoryTransaction::create([
                    'inventory_id' => $inventory->inventory_id,
                    'transaction_type' => 'return_in',
                    'quantity_change' => (float) $item->quantity,
                    'reference_id' => $order->order_id,
                    'reference_type' => 'order_reversal',
                    'notes' => 'Returned — order ' . $order->reference_code . '.',
                    'transaction_date' => now(),
                    'recorded_by' => auth()->user()->user_id,
                ]);
            }
        } else {
            $order->load('components');

            foreach ($order->components as $component) {
                $inventory = Inventory::where('product_id', $component->product_id)
                    ->where('reserve_type', 'production')
                    ->lockForUpdate()
                    ->first();

                if (!$inventory) continue;

                $inventory->update([
                    'current_quantity' => (float) $inventory->current_quantity + (float) $component->quantity,
                    'last_updated' => now(),
                ]);

                InventoryTransaction::create([
                    'inventory_id' => $inventory->inventory_id,
                    'transaction_type' => 'return_in',
                    'quantity_change' => (float) $component->quantity,
                    'reference_id' => $order->order_id,
                    'reference_type' => 'order_reversal',
                    'notes' => 'Returned — order ' . $order->reference_code . '.',
                    'transaction_date' => now(),
                    'recorded_by' => auth()->user()->user_id,
                ]);
            }
        }
    }
}