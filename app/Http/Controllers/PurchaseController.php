<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('user')
            ->orderByDesc('purchase_date')
            ->get();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $products = Product::orderBy('name')
            ->orderBy('variation')
            ->get();

        return view('purchases.create', compact('products'));
    }

    public function show(Purchase $purchase)
    {
        $purchase->load([
            'user',
            'items.product',
        ]);

        return view('purchases.show', compact('purchase'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => [
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

            'items.*.unit_cost' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.reserve_type' => [
                'required',
                'in:retail,production',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            $totalAmount = 0;
            $purchaseItems = [];

            foreach ($validated['items'] as $item) {

                $product = Product::findOrFail(
                    $item['product_id']
                );

                /*
                 * A product being purchased must have
                 * a purchase unit and conversion value.
                 */
                if (
                    empty($product->purchase_unit) ||
                    $product->units_per_purchase === null
                ) {
                    throw new \Exception(
                        "{$product->name} does not have valid purchase unit information."
                    );
                }

                $purchaseQuantity = (float) $item['quantity'];
                $unitCost = (float) $item['unit_cost'];

                /*
                 * Convert purchase quantity into
                 * the product's base stock quantity.
                 */
                $stockQuantity =
                    $purchaseQuantity *
                    (float) $product->units_per_purchase;

                $lineTotal =
                    $purchaseQuantity *
                    $unitCost;

                $totalAmount += $lineTotal;

                $purchaseItems[] = [
                    'product_id' => $product->product_id,
                    'quantity' => $purchaseQuantity,
                    'unit_cost' => $unitCost,
                    'reserve_type' => $item['reserve_type'],
                    'stock_quantity' => $stockQuantity,
                ];
            }

            $purchase = Purchase::create([
                'supplier_name' => $validated['supplier_name'] ?? null,
                'user_id' => auth()->user()->user_id,
                'purchase_date' => now(),
                'total_amount' => $totalAmount,
            ]);

            foreach ($purchaseItems as $item) {

                PurchaseItem::create([
                    'purchase_id' => $purchase->purchase_id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'reserve_type' => $item['reserve_type'],
                ]);

                /*
                 * Find the correct inventory allocation.
                 */
                $inventory = Inventory::where(
                    'product_id',
                    $item['product_id']
                )
                    ->where(
                        'reserve_type',
                        $item['reserve_type']
                    )
                    ->lockForUpdate()
                    ->first();

                /*
                 * If the allocation does not exist yet,
                 * create it with zero stock.
                 */
                if (!$inventory) {
                    $inventory = Inventory::create([
                        'product_id' => $item['product_id'],
                        'reserve_type' => $item['reserve_type'],
                        'current_quantity' => 0,
                        'last_updated' => now(),
                    ]);
                }

                $inventory->current_quantity +=
                    $item['stock_quantity'];

                $inventory->last_updated = now();

                $inventory->save();

                InventoryTransaction::create([
                    'inventory_id' => $inventory->inventory_id,
                    'transaction_type' => 'purchase_in',
                    'quantity_change' => $item['stock_quantity'],
                    'reference_id' => $purchase->purchase_id,
                    'reference_type' => 'purchase',
                    'notes' => 'Stock received from purchase.',
                    'transaction_date' => now(),
                    'recorded_by' => auth()->user()->user_id,
                ]);
            }

            /*
             * Record the purchase in the audit trail.
             */
            app(AuditLogger::class)->log(
                'create',
                'purchases',
                $purchase->purchase_id,
                'Purchase recorded. Supplier: ' .
                    ($purchase->supplier_name ?? 'Not specified') .
                    ', Total: ₱' .
                    number_format((float) $purchase->total_amount, 2)
            );
        });

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase recorded successfully.');
    }
}