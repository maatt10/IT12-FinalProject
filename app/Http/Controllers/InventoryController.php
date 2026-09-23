<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::with('inventory')
            ->orderBy('name')
            ->orderBy('variation')
            ->get();

        return view('inventory.index', compact('products'));
    }

    public function initialStock(Product $product)
    {
        return view('inventory.initial-stock', compact('product'));
    }

    public function storeInitialStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'retail_quantity' => ['required', 'numeric', 'min:0'],
            'production_quantity' => ['required', 'numeric', 'min:0'],
        ]);

        $retailQuantity = (float) $validated['retail_quantity'];
        $productionQuantity = (float) $validated['production_quantity'];

        if ($retailQuantity == 0 && $productionQuantity == 0) {
            return back()
                ->withErrors([
                    'retail_quantity' =>
                    'At least one initial stock quantity must be greater than zero.',
                ])
                ->withInput();
        }

        $alreadyInitialized = Inventory::where(
            'product_id',
            $product->product_id
        )->exists();

        if ($alreadyInitialized) {
            return back()
                ->withErrors([
                    'retail_quantity' =>
                    'Initial stock has already been initialized for this product.',
                ])
                ->withInput();
        }

        DB::transaction(function () use (
            $product,
            $retailQuantity,
            $productionQuantity
        ) {
            $quantities = [
                'retail' => $retailQuantity,
                'production' => $productionQuantity,
            ];

            foreach ($quantities as $reserveType => $quantity) {
                if ($quantity <= 0) {
                    continue;
                }

                $inventory = Inventory::create([
                    'product_id' => $product->product_id,
                    'reserve_type' => $reserveType,
                    'current_quantity' => $quantity,
                    'last_updated' => now(),
                ]);

                InventoryTransaction::create([
                    'inventory_id' => $inventory->inventory_id,
                    'transaction_type' => 'manual_addition',
                    'quantity_change' => $quantity,
                    'reference_id' => null,
                    'reference_type' => 'initial_stock',
                    'transaction_date' => now(),
                    'recorded_by' => auth()->user()->user_id,
                ]);
            }

            app(AuditLogger::class)->log(
                'create',
                'inventory',
                $product->product_id,
                'Initial stock recorded for ' .
                    $product->display_name .
                    '. Retail: ' .
                    $retailQuantity .
                    ', Production: ' .
                    $productionQuantity
            );
        });

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Initial stock added successfully.');
    }

    public function adjustment(Product $product)
    {
        $product->load('inventory');

        return view('inventory.adjustment', compact('product'));
    }

    public function storeAdjustment(Request $request, Product $product)
    {
        $validated = $request->validate([
            'reserve_type' => ['required', 'in:retail,production'],
            'actual_quantity' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $inventory = Inventory::where(
            'product_id',
            $product->product_id
        )
            ->where(
                'reserve_type',
                $validated['reserve_type']
            )
            ->first();

        if (!$inventory) {
            return back()
                ->withErrors([
                    'reserve_type' =>
                    'This stock allocation has not been initialized yet.',
                ])
                ->withInput();
        }

        $currentQuantity = (float) $inventory->current_quantity;
        $actualQuantity = (float) $validated['actual_quantity'];

        $adjustment = $actualQuantity - $currentQuantity;

        if ($adjustment == 0) {
            return back()
                ->withErrors([
                    'actual_quantity' =>
                    'No adjustment is needed because the physical quantity matches the recorded quantity.',
                ])
                ->withInput();
        }

        $previousQuantity = $currentQuantity;

        DB::transaction(function () use (
            $inventory,
            $actualQuantity,
            $adjustment,
            $validated,
            $product,
            $previousQuantity
        ) {
            $inventory->update([
                'current_quantity' => $actualQuantity,
                'last_updated' => now(),
            ]);

            InventoryTransaction::create([
                'inventory_id' => $inventory->inventory_id,
                'transaction_type' => 'adjustment',
                'quantity_change' => $adjustment,
                'reference_id' => null,
                'reference_type' => 'stock_adjustment',
                'notes' => $validated['notes'] ?? null,
                'transaction_date' => now(),
                'recorded_by' => auth()->user()->user_id,
            ]);

            app(AuditLogger::class)->log(
                'update',
                'inventory',
                $product->product_id,
                'Inventory adjusted for ' .
                    $product->display_name .
                    ' (' .
                    ucfirst($validated['reserve_type']) .
                    '). Previous: ' .
                    $previousQuantity .
                    ', New: ' .
                    $actualQuantity .
                    ($validated['notes']
                        ? '. Notes: ' . $validated['notes']
                        : '.')
            );
        });

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Stock adjustment recorded successfully.');
    }

    public function history(Product $product)
    {
        $product->load([
            'inventory.transactions.recordedBy',
        ]);

        $transactions = $product->inventory
            ->flatMap(function ($inventory) {
                return $inventory->transactions->map(function ($transaction) use ($inventory) {
                    $transaction->reserve_type = $inventory->reserve_type;

                    return $transaction;
                });
            })
            ->sortByDesc('transaction_date')
            ->values();

        return view('inventory.history', compact(
            'product',
            'transactions'
        ));
    }
}
