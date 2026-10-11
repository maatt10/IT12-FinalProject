<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\OrderComponent;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Production;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoHistorySeeder extends Seeder
{
    private int $ownerId;
    private int $staffId;

    /** @var array<string, Product> keyed by display_name */
    private array $products = [];

    /** @var array<string, Customer> keyed by full_name */
    private array $customers = [];

    private array $saleCounters = [];
    private array $orderCounters = [];
    private int $auditLogCounter = 0;

    public function run(): void
    {
        if (Sale::count() > 0 || Order::count() > 0) {
            $this->command->warn('History data already exists. Skipping DemoHistorySeeder.');
            return;
        }

        $owner = User::where('role', 'owner')->first();
        $staff = User::where('role', 'staff')->first();
        if (!$owner) {
            $this->command->error('No owner user found. Run UserSeeder first.');
            return;
        }
        $this->ownerId = $owner->user_id;
        $this->staffId = $staff?->user_id ?? $owner->user_id;

        $this->loadProducts();
        if (empty($this->products)) {
            $this->command->error('No products found. Run DemoDataSeeder first.');
            return;
        }

        DB::transaction(function () {
            $this->seedCustomers();
            $this->seedSales();
            $this->seedOrders();
            $this->seedPurchases();
            $this->seedProductions();
            $this->seedAuditLogs();
        });

        $this->command->info('Demo history seeded successfully.');
    }

    /* ============================================
       BOOTSTRAP
       ============================================ */
    private function loadProducts(): void
    {
        foreach (Product::all() as $product) {
            $this->products[$product->display_name] = $product;
        }
    }

    /* ============================================
       CUSTOMERS
       ============================================ */
    private function seedCustomers(): void
    {
        $rows = [
            // Regular customers
            ['Maria', 'Cruz', 'Santos', '09171111111', 'Ateneo Ave, Davao City', true],
            ['Juan', null, 'Dela Cruz', '09182222222', 'JP Laurel Ave, Davao City', true],
            ['Ana', 'Reyes', 'Villanueva', '09193333333', 'Matina, Davao City', true],
            ['Carlo', 'Lim', 'Mendoza', '09204444444', 'Bajada, Davao City', true],
            ['Jasmine', null, 'Tan', '09215555555', 'Torres St, Davao City', true],
            // Non-regular
            ['Miguel', 'Tan', 'Reyes', '09226666666', 'Lanang, Davao City', false],
            ['Bea', null, 'Aquino', '09237777777', 'Ecoland, Davao City', false],
            ['Ryan', 'Santos', 'Cruz', '09248888888', 'Buhangin, Davao City', false],
        ];

        foreach ($rows as [$first, $middle, $last, $contact, $address, $isRegular]) {
            $c = Customer::create([
                'first_name' => $first,
                'middle_name' => $middle,
                'last_name' => $last,
                'contact_number' => $contact,
                'address' => $address,
                'is_regular' => $isRegular,
                'discount_type' => 'none',
                'discount_id_number' => null,
            ]);
            $this->customers[$c->full_name] = $c;
        }
    }

    /* ============================================
       SALES (walk-in)
       ============================================ */
    private function seedSales(): void
    {
        // [days_ago, customer_key|null, [display_name => qty], payment, gcash_ref, user_type]
        $sales = [
            [21, 'Maria Cruz Santos',    ['Fuzzy Wire - Red' => 5],                       'cash',  null,           'staff'],
            [20, null,                    ['Kraft Paper' => 3],                           'cash',  null,           'owner'],
            [19, 'Juan Dela Cruz',        ['Fuzzy Wire - Blue' => 10, 'Fuzzy Wire - White' => 5], 'cash', null,    'owner'],
            [17, 'Ana Reyes Villanueva',  ['Satin Ribbon' => 2],                          'gcash', '9012345678901', 'owner'],
            [16, null,                    ['Fuzzy Wire - Soft Pink' => 8],                'cash',  null,           'staff'],
            [14, 'Jasmine Tan',           ['Hot Pink Delight' => 1],                      'cash',  null,           'owner'],
            [13, null,                    ['Fuzzy Wire - Bright Yellow' => 12],           'gcash', '9012345678902', 'owner'],
            [12, 'Carlo Lim Mendoza',     ['Sunshine Yellow Bouquet' => 1],               'cash',  null,           'owner'],
            [10, null,                    ['Lace Netting' => 2, 'Organza Ribbon' => 1],   'cash',  null,           'staff'],
            [9,  'Maria Cruz Santos',     ['Fuzzy Wire - Lavender' => 6],                 'gcash', '9012345678903', 'owner'],
            [7,  null,                    ['Fuzzy Wire - Sky Blue' => 15],                'cash',  null,           'owner'],
            [6,  'Bea Aquino',            ['Pink Petals Bouquet' => 1],                   'cash',  null,           'owner'],
            [5,  null,                    ['Fuzzy Wire - Cream' => 10],                   'gcash', '9012345678904', 'staff'],
            [4,  'Juan Dela Cruz',        ['Blush Romance Bouquet' => 1],                 'cash',  null,           'owner'],
            [3,  'Miguel Tan Reyes',      ['Fuzzy Wire - Hot Pink' => 8],                 'cash',  null,           'owner'],
            [2,  null,                    ['Rainbow Mini Flowers' => 1],                  'gcash', '9012345678905', 'owner'],
            [2,  'Ana Reyes Villanueva',  ['Fuzzy Wire - Lilac' => 5],                    'cash',  null,           'staff'],
            [1,  'Maria Cruz Santos',     ['Midnight Noir Bouquet' => 1],                 'cash',  null,           'owner'],
        ];

        foreach ($sales as [$daysAgo, $customerKey, $items, $payment, $gcashRef, $userType]) {
            $date = now()->subDays($daysAgo)->setTime(rand(9, 17), rand(0, 59));
            $customer = $customerKey ? ($this->customers[$customerKey] ?? null) : null;
            $userId = $userType === 'staff' ? $this->staffId : $this->ownerId;

            $this->createSale($date, $customer, $items, $payment, $gcashRef, $userId);
        }
    }

    private function createSale(
        Carbon $date,
        ?Customer $customer,
        array $itemList,
        string $payment,
        ?string $gcashRef,
        int $userId
    ): Sale {
        $subtotal = 0;
        $resolvedItems = [];

        foreach ($itemList as $displayName => $qty) {
            $product = $this->products[$displayName] ?? null;
            if (!$product) {
                $this->command->warn("Missing product: {$displayName}");
                continue;
            }
            $price = (float) $product->selling_price;
            $lineTotal = $price * $qty;
            $subtotal += $lineTotal;
            $resolvedItems[] = ['product' => $product, 'qty' => $qty, 'price' => $price];
        }

        // Apply PWD/Senior 20% if customer has a discount type
        $discountAmount = 0;
        $discountType = 'none';
        $discountName = null;
        $discountIdNumber = null;

        if ($customer && $customer->discount_type !== 'none') {
            $discountType = $customer->discount_type;
            $discountName = $customer->full_name;
            $discountIdNumber = $customer->discount_id_number;
            $discountAmount = round($subtotal * 0.20, 2);
        }

        $total = $subtotal - $discountAmount;

        $sale = Sale::create([
            'reference_code' => $this->nextSaleRef($date),
            'customer_id' => $customer?->customer_id,
            'user_id' => $userId,
            'sale_date' => $date,
            'payment_method' => $payment,
            'gcash_reference' => $gcashRef,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'discount_type' => $discountType,
            'discount_name' => $discountName,
            'discount_id_number' => $discountIdNumber,
            'total_amount' => $total,
            'receipt_issued' => true,
        ]);

        DB::table('sales')->where('sale_id', $sale->sale_id)->update([
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        foreach ($resolvedItems as $item) {
            SaleItem::create([
                'sale_id' => $sale->sale_id,
                'product_id' => $item['product']->product_id,
                'quantity' => $item['qty'],
                'unit_price' => $item['price'],
                'line_total' => $item['qty'] * $item['price'],
            ]);

            // Deduct retail inventory
            $inventory = Inventory::where('product_id', $item['product']->product_id)
                ->where('reserve_type', 'retail')
                ->first();

            if ($inventory) {
                $newQty = (float) $inventory->current_quantity - (float) $item['qty'];
                $inventory->update([
                    'current_quantity' => $newQty,
                    'last_updated' => $date,
                ]);

                InventoryTransaction::create([
                    'inventory_id' => $inventory->inventory_id,
                    'transaction_type' => 'sale_out',
                    'quantity_change' => -$item['qty'],
                    'reference_id' => $sale->sale_id,
                    'reference_type' => 'sale',
                    'notes' => null,
                    'transaction_date' => $date,
                    'recorded_by' => $userId,
                ]);
            }
        }

        DB::table('sale_items')->where('sale_id', $sale->sale_id)->update([
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        return $sale;
    }

    /* ============================================
       ORDERS
       ============================================ */
    private function seedOrders(): void
    {
        // [days_ago, customer_key|null, type, channel, items, status, notes]
        $orders = [
            [20, 'Maria Cruz Santos',   'ready_made', 'online',  ['Autumn Kraft Bouquet' => 1],                  'completed'],
            [18, 'Juan Dela Cruz',      'ready_made', 'online',  ['Sunshine Yellow Bouquet' => 2],               'completed'],
            [17, null,                  'customized', 'online',  null,                                            'completed'],
            [15, 'Carlo Lim Mendoza',   'ready_made', 'online',  ['Sky Blue Tulip Lily Bouquet' => 1],           'completed'],
            [14, 'Ana Reyes Villanueva','ready_made', 'walk_in', ['Pink Petals Bouquet' => 1],                    'completed'],
            [12, null,                  'customized', 'online',  null,                                            'completed'],
            [11, 'Jasmine Tan',         'ready_made', 'online',  ['Lavender Dream Bouquet' => 1],                'completed'],
            [9,  'Maria Cruz Santos',   'ready_made', 'online',  ['Midnight Noir Bouquet' => 1],                 'completed'],
            [7,  null,                  'customized', 'online',  null,                                            'cancelled'],
            [5,  'Bea Aquino',          'ready_made', 'walk_in', ['Hot Pink Delight' => 1],                       'completed'],
            [3,  'Juan Dela Cruz',      'ready_made', 'online',  ['Blush Romance Bouquet' => 1],                 'pending'],
            [1,  null,                  'customized', 'online',  null,                                            'pending'],
        ];

        foreach ($orders as [$daysAgo, $customerKey, $type, $channel, $items, $status]) {
            $date = now()->subDays($daysAgo)->setTime(rand(9, 18), rand(0, 59));
            $customer = $customerKey ? ($this->customers[$customerKey] ?? null) : null;

            $this->createOrder($date, $customer, $type, $channel, $items, $status);
        }
    }

    private function createOrder(
        Carbon $date,
        ?Customer $customer,
        string $type,
        string $channel,
        ?array $items,
        string $status
    ): Order {
        $receiverName = $customer
            ? explode(' ', $customer->full_name)
            : ['Walk-in', '', 'Customer'];

        $subtotal = 0;
        $resolvedItems = [];

        if ($type === 'ready_made' && $items) {
            foreach ($items as $displayName => $qty) {
                $product = $this->products[$displayName] ?? null;
                if (!$product) continue;
                $price = (float) $product->selling_price;
                $subtotal += $price * $qty;
                $resolvedItems[] = ['product' => $product, 'qty' => $qty, 'price' => $price];
            }
        } else {
            // Customized — flat price
            $subtotal = 800 + rand(0, 4) * 100; // ₱800-1200
        }

        $deliveryFee = $channel === 'walk_in' ? 0 : (rand(0, 1) ? 50 : 20);
        $total = $subtotal + $deliveryFee;

        $paymentMethod = $channel === 'walk_in' ? 'cash' : 'gcash';

        $order = Order::create([
            'reference_code' => $this->nextOrderRef($date),
            'customer_id' => $customer?->customer_id,
            'customer_name' => $customer ? null : 'Walk-in Customer',
            'user_id' => $this->ownerId,
            'order_type' => $type,
            'channel' => $channel,
            'receiver_first_name' => $receiverName[0],
            'receiver_middle_name' => $receiverName[1] ?: null,
            'receiver_last_name' => $receiverName[2] ?? '',
            'receiver_contact' => $customer?->contact_number ?? '09170000000',
            'delivery_address' => $channel === 'walk_in' ? null : 'Davao City',
            'delivery_datetime' => $date->copy()->addDay()->setTime(14, 0),
            'fulfillment_type' => $channel === 'walk_in' ? 'pickup' : 'delivery',
            'delivery_fee' => $deliveryFee,
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentMethod === 'gcash' ? (string) rand(1000000000000, 9999999999999) : null,
            'amount_paid' => $paymentMethod === 'cash' ? $total : null,
            'change_amount' => 0,
            'subtotal' => $subtotal,
            'discount_type' => 'none',
            'discount_amount' => 0,
            'payment_proof_reference' => $paymentMethod === 'gcash' ? (string) rand(1000000000000, 9999999999999) : null,
            'order_status' => $status,
            'cancellation_note' => $status === 'cancelled' ? 'Customer cancelled the order.' : null,
            'total_amount' => $total,
            'order_date' => $date,
        ]);

        DB::table('orders')->where('order_id', $order->order_id)->update([
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        if ($type === 'ready_made') {
            foreach ($resolvedItems as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item['product']->product_id,
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'line_total' => $item['qty'] * $item['price'],
                    'customization_details' => null,
                ]);

                // Deduct retail inventory — only for completed orders
                if ($status === 'completed') {
                    $inventory = Inventory::where('product_id', $item['product']->product_id)
                        ->where('reserve_type', 'retail')
                        ->first();

                    if ($inventory) {
                        $newQty = (float) $inventory->current_quantity - (float) $item['qty'];
                        $inventory->update([
                            'current_quantity' => $newQty,
                            'last_updated' => $date,
                        ]);

                        InventoryTransaction::create([
                            'inventory_id' => $inventory->inventory_id,
                            'transaction_type' => 'sale_out',
                            'quantity_change' => -$item['qty'],
                            'reference_id' => $order->order_id,
                            'reference_type' => 'order',
                            'notes' => 'Consumed by order ' . $order->reference_code . '.',
                            'transaction_date' => $date,
                            'recorded_by' => $this->ownerId,
                        ]);
                    }
                }
            }
        } else {
            // Customized — flat item + components
            OrderItem::create([
                'order_id' => $order->order_id,
                'product_id' => null,
                'quantity' => 1,
                'unit_price' => $subtotal,
                'line_total' => $subtotal,
                'customization_details' => 'Customized bouquet per customer request.',
            ]);

            // Add some fuzzy wire components
            $componentColors = ['Fuzzy Wire - Red', 'Fuzzy Wire - White', 'Fuzzy Wire - Cream'];
            foreach ($componentColors as $color) {
                $material = $this->products[$color] ?? null;
                if (!$material) continue;

                $qty = rand(4, 10);
                OrderComponent::create([
                    'order_id' => $order->order_id,
                    'product_id' => $material->product_id,
                    'quantity' => $qty,
                ]);

                // Deduct production inventory for completed orders
                if ($status === 'completed') {
                    $inventory = Inventory::where('product_id', $material->product_id)
                        ->where('reserve_type', 'production')
                        ->first();

                    if ($inventory) {
                        $newQty = (float) $inventory->current_quantity - $qty;
                        $inventory->update([
                            'current_quantity' => $newQty,
                            'last_updated' => $date,
                        ]);

                        InventoryTransaction::create([
                            'inventory_id' => $inventory->inventory_id,
                            'transaction_type' => 'sale_out',
                            'quantity_change' => -$qty,
                            'reference_id' => $order->order_id,
                            'reference_type' => 'order',
                            'notes' => 'Custom bouquet component for ' . $order->reference_code . '.',
                            'transaction_date' => $date,
                            'recorded_by' => $this->ownerId,
                        ]);
                    }
                }
            }

            if ($status === 'completed') {
                Production::create([
                    'product_id' => null,
                    'order_id' => $order->order_id,
                    'quantity_produced' => 1,
                    'notes' => 'Custom bouquet for order ' . $order->reference_code . '.',
                    'produced_by' => $this->ownerId,
                    'production_date' => $date,
                ]);
            }
        }

        DB::table('order_items')->where('order_id', $order->order_id)->update([
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        return $order;
    }

    /* ============================================
       PURCHASES
       ============================================ */
    private function seedPurchases(): void
    {
        // [days_ago, supplier, items: [display_name => ['qty' => bundles, 'unit_cost' => per bundle, 'pool' => 'retail'|'production']]]
        $purchases = [
            [20, 'Davao Craft Supply', [
                'Fuzzy Wire - Red' => ['qty' => 5, 'unit_cost' => 75, 'pool' => 'production'],
                'Fuzzy Wire - White' => ['qty' => 3, 'unit_cost' => 75, 'pool' => 'production'],
            ]],
            [17, 'Davao Craft Supply', [
                'Fuzzy Wire - Blue' => ['qty' => 4, 'unit_cost' => 75, 'pool' => 'production'],
                'Fuzzy Wire - Cream' => ['qty' => 3, 'unit_cost' => 75, 'pool' => 'production'],
            ]],
            [14, 'Craft World Davao', [
                'Kraft Paper' => ['qty' => 5, 'unit_cost' => 80, 'pool' => 'production'],
                'Printed Wrap' => ['qty' => 3, 'unit_cost' => 90, 'pool' => 'production'],
            ]],
            [11, 'Ribbon Central', [
                'Satin Ribbon' => ['qty' => 4, 'unit_cost' => 100, 'pool' => 'production'],
                'Organza Ribbon' => ['qty' => 3, 'unit_cost' => 100, 'pool' => 'production'],
            ]],
            [8, 'Davao Craft Supply', [
                'Fuzzy Wire - Lavender' => ['qty' => 4, 'unit_cost' => 75, 'pool' => 'production'],
                'Fuzzy Wire - Sky Blue' => ['qty' => 3, 'unit_cost' => 75, 'pool' => 'production'],
            ]],
            [5, 'Craft World Davao', [
                'Lace Netting' => ['qty' => 4, 'unit_cost' => 120, 'pool' => 'production'],
                'Tissue Paper' => ['qty' => 3, 'unit_cost' => 100, 'pool' => 'production'],
            ]],
            [2, 'Ribbon Central', [
                'Pearl Beads' => ['qty' => 2, 'unit_cost' => 150, 'pool' => 'production'],
                'Floral Wire' => ['qty' => 3, 'unit_cost' => 90, 'pool' => 'production'],
            ]],
        ];

        foreach ($purchases as [$daysAgo, $supplier, $items]) {
            $date = now()->subDays($daysAgo)->setTime(rand(10, 15), rand(0, 59));

            $totalAmount = 0;
            $resolvedItems = [];

            foreach ($items as $displayName => $data) {
                $product = $this->products[$displayName] ?? null;
                if (!$product) {
                    $this->command->warn("Missing purchase product: {$displayName}");
                    continue;
                }
                $lineTotal = $data['qty'] * $data['unit_cost'];
                $totalAmount += $lineTotal;

                $unitsPerPurchase = (float) ($product->units_per_purchase ?? 1);
                $stockAdded = $data['qty'] * $unitsPerPurchase;

                $resolvedItems[] = [
                    'product' => $product,
                    'qty' => $data['qty'],
                    'unit_cost' => $data['unit_cost'],
                    'pool' => $data['pool'],
                    'stock_added' => $stockAdded,
                ];
            }

            $purchase = Purchase::create([
                'supplier_name' => $supplier,
                'user_id' => $this->ownerId,
                'purchase_date' => $date,
                'total_amount' => $totalAmount,
            ]);

            DB::table('purchases')->where('purchase_id', $purchase->purchase_id)->update([
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            foreach ($resolvedItems as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->purchase_id,
                    'product_id' => $item['product']->product_id,
                    'quantity' => $item['qty'],
                    'unit_cost' => $item['unit_cost'],
                    'reserve_type' => $item['pool'],
                ]);

                // Add stock
                $inventory = Inventory::where('product_id', $item['product']->product_id)
                    ->where('reserve_type', $item['pool'])
                    ->first();

                if (!$inventory) {
                    $inventory = Inventory::create([
                        'product_id' => $item['product']->product_id,
                        'reserve_type' => $item['pool'],
                        'current_quantity' => 0,
                        'last_updated' => $date,
                    ]);
                }

                $newQty = (float) $inventory->current_quantity + (float) $item['stock_added'];
                $inventory->update([
                    'current_quantity' => $newQty,
                    'last_updated' => $date,
                ]);

                InventoryTransaction::create([
                    'inventory_id' => $inventory->inventory_id,
                    'transaction_type' => 'purchase_in',
                    'quantity_change' => $item['stock_added'],
                    'reference_id' => $purchase->purchase_id,
                    'reference_type' => 'purchase',
                    'notes' => 'Stock received from purchase.',
                    'transaction_date' => $date,
                    'recorded_by' => $this->ownerId,
                ]);
            }

            DB::table('purchase_items')->where('purchase_id', $purchase->purchase_id)->update([
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }

    /* ============================================
       PRODUCTIONS
       ============================================ */
    private function seedProductions(): void
    {
        // [days_ago, bouquet_display_name, qty]
        $productions = [
            [19, 'Autumn Kraft Bouquet',        2],
            [16, 'Sunshine Yellow Bouquet',     3],
            [13, 'Sky Blue Tulip Lily Bouquet', 1],
            [10, 'Pink Petals Bouquet',         2],
            [6,  'Blush Romance Bouquet',       2],
            [3,  'Hot Pink Delight',            3],
        ];

        foreach ($productions as [$daysAgo, $bouquetName, $qty]) {
            $date = now()->subDays($daysAgo)->setTime(rand(9, 15), rand(0, 59));
            $bouquet = $this->products[$bouquetName] ?? null;
            if (!$bouquet) continue;

            $production = Production::create([
                'product_id' => $bouquet->product_id,
                'order_id' => null,
                'quantity_produced' => $qty,
                'notes' => null,
                'produced_by' => $this->ownerId,
                'production_date' => $date,
            ]);

            DB::table('productions')->where('production_id', $production->production_id)->update([
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // Consume BOM materials from production pool
            foreach ($bouquet->parentComponents as $component) {
                $material = $component->materialProduct;
                $requiredPerUnit = (float) $component->quantity_required;
                $requiredTotal = $requiredPerUnit * $qty;

                $inventory = Inventory::where('product_id', $material->product_id)
                    ->where('reserve_type', 'production')
                    ->first();

                if (!$inventory) continue;

                $newQty = (float) $inventory->current_quantity - $requiredTotal;
                $inventory->update([
                    'current_quantity' => $newQty,
                    'last_updated' => $date,
                ]);

                InventoryTransaction::create([
                    'inventory_id' => $inventory->inventory_id,
                    'transaction_type' => 'production_use',
                    'quantity_change' => -$requiredTotal,
                    'reference_id' => $production->production_id,
                    'reference_type' => 'production',
                    'notes' => 'Materials used to produce ' . $qty . ' × ' . $bouquet->display_name,
                    'transaction_date' => $date,
                    'recorded_by' => $this->ownerId,
                ]);
            }

            // Add finished product to retail pool (production is internal; bouquets go to retail)
            $finishedInv = Inventory::where('product_id', $bouquet->product_id)
                ->where('reserve_type', 'retail')
                ->first();

            if (!$finishedInv) {
                $finishedInv = Inventory::create([
                    'product_id' => $bouquet->product_id,
                    'reserve_type' => 'retail',
                    'current_quantity' => 0,
                    'last_updated' => $date,
                ]);
            }

            $newQty = (float) $finishedInv->current_quantity + $qty;
            $finishedInv->update([
                'current_quantity' => $newQty,
                'last_updated' => $date,
            ]);

            InventoryTransaction::create([
                'inventory_id' => $finishedInv->inventory_id,
                'transaction_type' => 'production_output',
                'quantity_change' => $qty,
                'reference_id' => $production->production_id,
                'reference_type' => 'production',
                'notes' => 'Finished product produced: ' . $qty . ' × ' . $bouquet->display_name,
                'transaction_date' => $date,
                'recorded_by' => $this->ownerId,
            ]);
        }
    }

    /* ============================================
       AUDIT LOGS
       ============================================ */
    private function seedAuditLogs(): void
    {
        // Build audit logs from the sales/orders/purchases/productions we just created
        $entries = [];

        foreach (Sale::orderBy('sale_date')->get() as $sale) {
            $customerName = $sale->customer->full_name ?? 'Walk-in Customer';
            $entries[] = [
                'user_id' => $sale->user_id,
                'action_type' => 'create',
                'table_affected' => 'sales',
                'record_id' => $sale->sale_id,
                'action_timestamp' => $sale->sale_date,
                'details' => 'Sale completed for ' . $customerName . '. Total: ₱' .
                    number_format((float) $sale->total_amount, 2) .
                    ', Payment: ' . strtoupper($sale->payment_method),
            ];
        }

        foreach (Order::orderBy('order_date')->get() as $order) {
            $entries[] = [
                'user_id' => $order->user_id,
                'action_type' => 'create',
                'table_affected' => 'orders',
                'record_id' => $order->order_id,
                'action_timestamp' => $order->order_date,
                'details' => 'Order recorded (' . $order->channel . ') for ' .
                    $order->receiver_full_name . '. Total: ₱' .
                    number_format((float) $order->total_amount, 2) .
                    ', Status: Pending.',
            ];

            if ($order->order_status !== 'pending') {
                $entries[] = [
                    'user_id' => $order->user_id,
                    'action_type' => 'update',
                    'table_affected' => 'orders',
                    'record_id' => $order->order_id,
                    'action_timestamp' => $order->order_date->copy()->addHour(),
                    'details' => 'Order status changed from Pending to ' .
                        ucfirst($order->order_status) . '.',
                ];
            }
        }

        foreach (Purchase::orderBy('purchase_date')->get() as $purchase) {
            $entries[] = [
                'user_id' => $purchase->user_id,
                'action_type' => 'create',
                'table_affected' => 'purchases',
                'record_id' => $purchase->purchase_id,
                'action_timestamp' => $purchase->purchase_date,
                'details' => 'Purchase recorded. Supplier: ' .
                    ($purchase->supplier_name ?? 'Not specified') .
                    ', Total: ₱' . number_format((float) $purchase->total_amount, 2),
            ];
        }

        foreach (Production::orderBy('production_date')->get() as $production) {
            $productName = $production->product?->display_name ?? 'Custom Bouquet';
            $entries[] = [
                'user_id' => $production->produced_by,
                'action_type' => 'create',
                'table_affected' => 'productions',
                'record_id' => $production->production_id,
                'action_timestamp' => $production->production_date,
                'details' => 'Production recorded. Product: ' . $productName .
                    ', Quantity: ' . number_format((float) $production->quantity_produced, 2),
            ];
        }

        // Sort chronologically for cleaner seeding
        usort($entries, fn($a, $b) => $a['action_timestamp'] <=> $b['action_timestamp']);

        foreach ($entries as $entry) {
            AuditLog::create($entry);
            $this->auditLogCounter++;
        }
    }

    /* ============================================
       HELPERS
       ============================================ */
    private function nextSaleRef(Carbon $date): string
    {
        $key = $date->format('dmy');
        $this->saleCounters[$key] = ($this->saleCounters[$key] ?? 0) + 1;
        return $key . '-' . str_pad((string) $this->saleCounters[$key], 5, '0', STR_PAD_LEFT);
    }

    private function nextOrderRef(Carbon $date): string
    {
        $key = $date->format('dmy');
        $this->orderCounters[$key] = ($this->orderCounters[$key] ?? 0) + 1;
        return 'ORD-' . $key . '-' . str_pad((string) $this->orderCounters[$key], 5, '0', STR_PAD_LEFT);
    }
}