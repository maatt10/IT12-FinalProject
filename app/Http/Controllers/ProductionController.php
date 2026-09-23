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
        $productions = Production::with([
            'product',
            'producedBy',
        ])
            ->orderByDesc('production_date')
            ->get();

        return view('production.index', compact('productions'));
    }

    public function create()
    {
        /*
         * A product is considered producible when it has
         * at least one BOM component.
         */
        $products = Product::with([
            'parentComponents.materialProduct',
        ])
            ->whereHas('parentComponents')
            ->orderBy('name')
            ->orderBy('variation')
            ->get();

        return view('production.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => [
                'required',
                'integer',
                'exists:products,product_id',
            ],
            'quantity_produced' => [
                'required',
                'numeric',
                'gt:0',
            ],
        ]);

        try {
            DB::transaction(function () use ($validated) {

                $product = Product::with([
                    'parentComponents.materialProduct',
                ])
                    ->lockForUpdate()
                    ->findOrFail($validated['product_id']);

                $quantityProduced = (float) $validated['quantity_produced'];

                /*
                 * A product must have a BOM before it can be produced.
                 */
                if ($product->parentComponents->isEmpty()) {
                    throw new \RuntimeException(
                        'This product does not have any BOM components.'
                    );
                }

                /*
                 * Calculate all required materials first.
                 */
                $requirements = [];

                foreach ($product->parentComponents as $component) {

                    $requiredQuantity =
                        (float) $component->quantity_required
                        * $quantityProduced;

                    $materialProductId =
                        $component->material_product_id;

                    if (!isset($requirements[$materialProductId])) {
                        $requirements[$materialProductId] = [
                            'product' => $component->materialProduct,
                            'quantity' => 0,
                        ];
                    }

                    $requirements[$materialProductId]['quantity'] +=
                        $requiredQuantity;
                }

                /*
                 * Lock all required production inventory rows
                 * and verify that enough stock exists.
                 */
                $inventoryRows = [];

                foreach ($requirements as $materialProductId => $requirement) {

                    $inventory = Inventory::where(
                        'product_id',
                        $materialProductId
                    )
                        ->where(
                            'reserve_type',
                            'production'
                        )
                        ->lockForUpdate()
                        ->first();

                    $availableQuantity = $inventory
                        ? (float) $inventory->current_quantity
                        : 0;

                    $requiredQuantity =
                        (float) $requirement['quantity'];

                    if ($availableQuantity < $requiredQuantity) {

                        $materialName =
                            $requirement['product']->display_name;

                        throw new \RuntimeException(
                            'Insufficient production stock for ' .
                            $materialName .
                            '. Required: ' .
                            number_format($requiredQuantity, 2) .
                            ', Available: ' .
                            number_format($availableQuantity, 2) .
                            '.'
                        );
                    }

                    $inventoryRows[$materialProductId] = $inventory;
                }

                /*
                 * Create the production record first so its ID
                 * can be used as the inventory transaction reference.
                 */
                $production = Production::create([
                    'product_id' => $product->product_id,
                    'quantity_produced' => $quantityProduced,
                    'produced_by' => auth()->user()->user_id,
                    'production_date' => now(),
                ]);

                /*
                 * Deduct all BOM materials from production inventory.
                 */
                foreach ($requirements as $materialProductId => $requirement) {

                    $inventory =
                        $inventoryRows[$materialProductId];

                    $requiredQuantity =
                        (float) $requirement['quantity'];

                    $inventory->current_quantity =
                        (float) $inventory->current_quantity
                        - $requiredQuantity;

                    $inventory->last_updated = now();

                    $inventory->save();

                    InventoryTransaction::create([
                        'inventory_id' =>
                            $inventory->inventory_id,

                        'transaction_type' =>
                            'production_use',

                        'quantity_change' =>
                            -$requiredQuantity,

                        'reference_id' =>
                            $production->production_id,

                        'reference_type' =>
                            'production',

                        'notes' =>
                            'Materials used to produce ' .
                            $quantityProduced .
                            ' × ' .
                            $product->display_name,

                        'transaction_date' =>
                            now(),

                        'recorded_by' =>
                            auth()->user()->user_id,
                    ]);
                }

                /*
                 * Add the finished product to production inventory.
                 *
                 * If the production inventory row does not exist yet,
                 * create it.
                 */
                $finishedInventory = Inventory::where(
                    'product_id',
                    $product->product_id
                )
                    ->where(
                        'reserve_type',
                        'production'
                    )
                    ->lockForUpdate()
                    ->first();

                if (!$finishedInventory) {
                    $finishedInventory = Inventory::create([
                        'product_id' =>
                            $product->product_id,

                        'reserve_type' =>
                            'production',

                        'current_quantity' =>
                            $quantityProduced,

                        'last_updated' =>
                            now(),
                    ]);
                } else {
                    $finishedInventory->current_quantity =
                        (float) $finishedInventory->current_quantity
                        + $quantityProduced;

                    $finishedInventory->last_updated = now();

                    $finishedInventory->save();
                }

                /*
                 * Record the finished-product inventory movement.
                 */
                InventoryTransaction::create([
                    'inventory_id' =>
                        $finishedInventory->inventory_id,

                    'transaction_type' =>
                        'production_output',

                    'quantity_change' =>
                        $quantityProduced,

                    'reference_id' =>
                        $production->production_id,

                    'reference_type' =>
                        'production',

                    'notes' =>
                        'Finished product produced: ' .
                        $quantityProduced .
                        ' × ' .
                        $product->display_name,

                    'transaction_date' =>
                        now(),

                    'recorded_by' =>
                        auth()->user()->user_id,
                ]);

                /*
                 * Record the production itself in the Audit Trail.
                 */
                app(AuditLogger::class)->log(
                    'create',
                    'productions',
                    $production->production_id,
                    'Production recorded. Product: ' .
                        $product->display_name .
                        ', Quantity: ' .
                        number_format(
                            $quantityProduced,
                            2
                        )
                );
            });

        } catch (\RuntimeException $e) {

            return redirect()
                ->route('production.create')
                ->withInput()
                ->with('error', $e->getMessage());

        } catch (\Throwable $e) {

            return redirect()
                ->route('production.create')
                ->withInput()
                ->with(
                    'error',
                    'Production could not be completed. No inventory changes were made.'
                );
        }

        return redirect()
            ->route('production.index')
            ->with(
                'success',
                'Production completed successfully.'
            );
    }
}