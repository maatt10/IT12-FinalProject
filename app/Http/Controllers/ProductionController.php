<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Production;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function index()
    {
        $productions = Production::with(['product', 'producedBy'])
            ->orderByDesc('production_date')
            ->get();

        return view('production.index', compact('productions'));
    }

    public function create()
    {
        $products = Product::with(['parentComponents.materialProduct.inventory'])
            ->whereHas('parentComponents')
            ->orderBy('name')
            ->orderBy('variation')
            ->get();

        $products = $products->map(function ($product) {
            $product->parentComponents = $product->parentComponents->map(function ($component) {
                $material = $component->materialProduct;
                $prodInventory = $material->inventory->firstWhere('reserve_type', 'production');

                $component->material_physical = $prodInventory ? (float) $prodInventory->current_quantity : 0;
                $component->material_available = $component->material_physical;

                return $component;
            });

            return $product;
        });

        return view('production.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,product_id'],
            'items.*.quantity_produced' => ['required', 'numeric', 'gt:0'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['items'] as $item) {
                    $this->processProduction(
                        (int) $item['product_id'],
                        (float) $item['quantity_produced']
                    );
                }
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Production could not be completed. No inventory changes were made.');
        }

        return redirect()
            ->route('products.index', ['item_type' => 'product'])
            ->with('success', 'Production recorded successfully.');
    }

    /* ============================================
       CORE — process a single production entry
       ============================================ */
    private function processProduction(int $productId, float $quantityProduced): void
    {
        $product = Product::with(['parentComponents.materialProduct'])
            ->lockForUpdate()
            ->findOrFail($productId);

        if ($product->parentComponents->isEmpty()) {
            throw new \RuntimeException(
                "{$product->display_name} does not have any BOM components."
            );
        }

        // Aggregate requirements per material
        $requirements = [];

        foreach ($product->parentComponents as $component) {
            $requiredQuantity = (float) $component->quantity_required * $quantityProduced;
            $materialProductId = $component->material_product_id;

            if (!isset($requirements[$materialProductId])) {
                $requirements[$materialProductId] = [
                    'product' => $component->materialProduct,
                    'quantity' => 0,
                ];
            }

            $requirements[$materialProductId]['quantity'] += $requiredQuantity;
        }

        // Verify availability against production pool
        $inventoryRows = [];

        foreach ($requirements as $materialProductId => $requirement) {
            $inventory = Inventory::where('product_id', $materialProductId)
                ->where('reserve_type', 'production')
                ->lockForUpdate()
                ->first();

            $availableQuantity = $inventory ? (float) $inventory->current_quantity : 0;
            $requiredQuantity = (float) $requirement['quantity'];

            if ($availableQuantity < $requiredQuantity) {
                $materialName = $requirement['product']->display_name;

                throw new \RuntimeException(
                    'Insufficient production stock for ' . $materialName .
                    '. Required: ' . number_format($requiredQuantity, 2) .
                    ', Available: ' . number_format($availableQuantity, 2) . '.'
                );
            }

            $inventoryRows[$materialProductId] = $inventory;
        }

        // Create the production record
        $production = Production::create([
            'product_id' => $product->product_id,
            'quantity_produced' => $quantityProduced,
            'produced_by' => auth()->user()->user_id,
            'production_date' => now(),
        ]);

        // Deduct BOM materials from production pool
        foreach ($requirements as $materialProductId => $requirement) {
            $inventory = $inventoryRows[$materialProductId];
            $requiredQuantity = (float) $requirement['quantity'];

            $inventory->current_quantity = (float) $inventory->current_quantity - $requiredQuantity;
            $inventory->last_updated = now();
            $inventory->save();

            InventoryTransaction::create([
                'inventory_id' => $inventory->inventory_id,
                'transaction_type' => 'production_use',
                'quantity_change' => -$requiredQuantity,
                'reference_id' => $production->production_id,
                'reference_type' => 'production',
                'notes' => 'Materials used to produce ' . $quantityProduced . ' × ' . $product->display_name,
                'transaction_date' => now(),
                'recorded_by' => auth()->user()->user_id,
            ]);
        }

        // Add finished product to the pool matching its stock purpose
        $finishedPool = in_array($product->stock_purpose, ['retail', 'both']) ? 'retail' : 'production';

        $finishedInventory = Inventory::where('product_id', $product->product_id)
            ->where('reserve_type', $finishedPool)
            ->lockForUpdate()
            ->first();

        if (!$finishedInventory) {
            $finishedInventory = Inventory::create([
                'product_id' => $product->product_id,
                'reserve_type' => $finishedPool,
                'current_quantity' => $quantityProduced,
                'last_updated' => now(),
            ]);
        } else {
            $finishedInventory->current_quantity = (float) $finishedInventory->current_quantity + $quantityProduced;
            $finishedInventory->last_updated = now();
            $finishedInventory->save();
        }

        InventoryTransaction::create([
            'inventory_id' => $finishedInventory->inventory_id,
            'transaction_type' => 'production_output',
            'quantity_change' => $quantityProduced,
            'reference_id' => $production->production_id,
            'reference_type' => 'production',
            'notes' => 'Finished product produced: ' . $quantityProduced . ' × ' . $product->display_name,
            'transaction_date' => now(),
            'recorded_by' => auth()->user()->user_id,
        ]);

        app(AuditLogger::class)->log(
            'create',
            'productions',
            $production->production_id,
            'Production recorded. Product: ' . $product->display_name .
                ', Quantity: ' . number_format($quantityProduced, 2)
        );
    }
}