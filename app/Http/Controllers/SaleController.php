<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function create()
    {
        $products = Product::where('is_sellable', true)
            ->orderBy('name')
            ->orderBy('variation')
            ->get();

        $customers = Customer::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('sales.create', compact(
            'products',
            'customers'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,customer_id'],
            'payment_method' => ['required', 'in:cash,gcash,bank_transfer'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'receipt_issued' => ['required', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,product_id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
        ]);

        DB::transaction(function () use ($validated) {

            $subtotal = 0;

            foreach ($validated['items'] as $item) {

                $product = Product::findOrFail($item['product_id']);

                if (!$product->is_sellable) {
                    throw new \Exception(
                        "{$product->name} is not available for sale."
                    );
                }

                $inventory = Inventory::where(
                    'product_id',
                    $product->product_id
                )
                    ->where('reserve_type', 'retail')
                    ->lockForUpdate()
                    ->first();

                if (!$inventory) {
                    throw new \Exception(
                        "{$product->name} has no retail inventory."
                    );
                }

                $quantity = (float) $item['quantity'];

                if ($inventory->current_quantity < $quantity) {
                    throw new \Exception(
                        "Insufficient retail stock for {$product->name}."
                    );
                }

                $unitPrice = (float) $product->selling_price;
                $lineTotal = $quantity * $unitPrice;

                $subtotal += $lineTotal;
            }

            $discount = (float) $validated['discount_amount'];

            if ($discount > $subtotal) {
                throw new \Exception(
                    'Discount cannot exceed the sale subtotal.'
                );
            }

            $total = $subtotal - $discount;

            $sale = Sale::create([
                'customer_id' => $validated['customer_id'] ?? null,
                'user_id' => auth()->user()->user_id,
                'sale_date' => now(),
                'payment_method' => $validated['payment_method'],
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'receipt_issued' => $validated['receipt_issued'],
            ]);

            foreach ($validated['items'] as $item) {

                $product = Product::findOrFail($item['product_id']);

                $inventory = Inventory::where(
                    'product_id',
                    $product->product_id
                )
                    ->where('reserve_type', 'retail')
                    ->lockForUpdate()
                    ->firstOrFail();

                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $product->selling_price;
                $lineTotal = $quantity * $unitPrice;

                SaleItem::create([
                    'sale_id' => $sale->sale_id,
                    'product_id' => $product->product_id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);

                $inventory->update([
                    'current_quantity' =>
                        $inventory->current_quantity - $quantity,
                    'last_updated' => now(),
                ]);

                InventoryTransaction::create([
                    'inventory_id' => $inventory->inventory_id,
                    'transaction_type' => 'sale_out',
                    'quantity_change' => -$quantity,
                    'reference_id' => $sale->sale_id,
                    'reference_type' => 'sale',
                    'notes' => null,
                    'transaction_date' => now(),
                    'recorded_by' => auth()->user()->user_id,
                ]);
            }

            /*
             * Record the completed sale in the audit trail.
             */
            $customerName = $sale->customer
                ? $sale->customer->full_name
                : 'Walk-in Customer';

            app(AuditLogger::class)->log(
                'create',
                'sales',
                $sale->sale_id,
                'Sale completed for ' .
                    $customerName .
                    '. Total: ₱' .
                    number_format((float) $sale->total_amount, 2) .
                    ', Payment: ' .
                    strtoupper($sale->payment_method)
            );
        });

        return redirect()
            ->route('sales.create')
            ->with('success', 'Sale completed successfully.');
    }
}