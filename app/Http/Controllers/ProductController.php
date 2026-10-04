<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductComponent;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /* INDEX — Inventory with tabs + stackable filters */
    public function index(Request $request)
    {
        $itemType = $request->query('item_type', 'product');
        if (!in_array($itemType, ['product', 'material'])) {
            $itemType = 'product';
        }

        $dbType = $itemType === 'material' ? 'material' : 'made_product';
        $filters = (array) $request->query('filters', []);

        $query = Product::with('inventory')->where('item_type', $dbType);

        if (in_array('archived', $filters)) {
            $query->where('is_active', false);
        } else {
            $query->where('is_active', true);
        }

        $items = $query->orderBy('name')->orderBy('variation')->get();

        $items = $items->map(function ($item) {
            $retail = $item->inventory->firstWhere('reserve_type', 'retail');
            $production = $item->inventory->firstWhere('reserve_type', 'production');

            $item->retail_stock = $retail ? (float) $retail->current_quantity : null;
            $item->production_stock = $production ? (float) $production->current_quantity : null;

            $threshold = (float) $item->low_stock_threshold;

            $item->retail_low = $item->retail_stock !== null && $item->retail_stock <= $threshold;
            $item->production_low = $item->production_stock !== null && $item->production_stock <= $threshold;
            $item->has_low_stock = $item->retail_low || $item->production_low;

            $item->total_stock = (float) ($item->retail_stock ?? 0) + (float) ($item->production_stock ?? 0);

            return $item;
        });

        if (in_array('low_stock', $filters)) {
            $items = $items->filter(fn($i) => $i->has_low_stock);
        }
        if (in_array('sellable', $filters)) {
            $items = $items->filter(fn($i) => $i->retail_stock !== null);
        }
        if (in_array('production', $filters)) {
            $items = $items->filter(fn($i) => $i->production_stock !== null);
        }

        $items = $items->values();

        $productCount = Product::where('item_type', 'made_product')->where('is_active', true)->count();
        $materialCount = Product::where('item_type', 'material')->where('is_active', true)->count();

        return view('products.index', compact(
            'items',
            'itemType',
            'productCount',
            'materialCount',
            'filters'
        ));
    }

    /* CREATE */
    public function create(Request $request)
    {
        $itemType = $request->query('item_type') === 'material' ? 'material' : 'made_product';

        $materials = Product::where('item_type', 'material')
            ->whereIn('stock_purpose', ['production', 'both'])
            ->where('is_active', true)
            ->orderBy('name')
            ->orderBy('variation')
            ->get();

        return view('products.create', compact('itemType', 'materials'));
    }

    /* STORE */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_type' => ['required', 'in:made_product,material'],
            'name' => ['required', 'string', 'max:255'],
            'variation' => ['nullable', 'string', 'max:255'],
            'stock_purpose' => ['required', 'in:retail,production,both'],
            'low_stock_threshold' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'stock_unit' => ['required', 'string', 'max:50'],
            'purchase_unit' => ['nullable', 'string', 'max:50'],
            'units_per_purchase' => ['nullable', 'numeric', 'min:0.01'],
            'bom' => ['nullable', 'array'],
            'bom.*.material_product_id' => ['required', 'exists:products,product_id'],
            'bom.*.quantity_required' => ['required', 'numeric', 'min:0.01'],
        ]);

        if ($validated['item_type'] === 'made_product') {
            $validated['purchase_unit'] = null;
            $validated['units_per_purchase'] = null;
        }

        if (!in_array($validated['stock_purpose'], ['retail', 'both'])) {
            $validated['selling_price'] = null;
        }

        if (empty($validated['low_stock_threshold'])) {
            $validated['low_stock_threshold'] = 10;
        }

        $product = Product::create(collect($validated)->except('bom')->toArray());

        // Generate the reference code (PRD-00001 or MAT-00001)
        $prefix = $product->item_type === 'material' ? 'MAT' : 'PRD';
        $lastCount = Product::where('item_type', $product->item_type)
            ->where('reference_code', 'LIKE', $prefix . '-%')
            ->count();
        $product->update([
            'reference_code' => $prefix . '-' . str_pad($lastCount + 1, 5, '0', STR_PAD_LEFT),
        ]);
        

        if ($validated['item_type'] === 'made_product' && !empty($validated['bom'])) {
            foreach ($validated['bom'] as $row) {
                ProductComponent::create([
                    'parent_product_id' => $product->product_id,
                    'material_product_id' => $row['material_product_id'],
                    'quantity_required' => $row['quantity_required'],
                ]);
            }
        }

        app(AuditLogger::class)->log(
            'create',
            'products',
            $product->product_id,
            'Item created: ' . $product->display_name
        );

        return redirect()
            ->route('products.index', ['item_type' => $validated['item_type'] === 'material' ? 'material' : 'product'])
            ->with('success', 'Item created successfully.');
    }

    /* SHOW */
    public function show(Product $product)
    {
        $product->load([
            'inventory',
            'parentComponents.materialProduct',
            'usedAsComponent.parentProduct',
        ]);

        return view('products.show', compact('product'));
    }

    /* EDIT */
    public function edit(Product $product)
    {
        $materials = Product::where('item_type', 'material')
            ->whereIn('stock_purpose', ['production', 'both'])
            ->where('is_active', true)
            ->where('product_id', '!=', $product->product_id)
            ->orderBy('name')
            ->orderBy('variation')
            ->get();

        $product->load('parentComponents');

        return view('products.edit', compact('product', 'materials'));
    }

    /* UPDATE */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'item_type' => ['required', 'in:made_product,material'],
            'name' => ['required', 'string', 'max:255'],
            'variation' => ['nullable', 'string', 'max:255'],
            'stock_purpose' => ['required', 'in:retail,production,both'],
            'low_stock_threshold' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'stock_unit' => ['required', 'string', 'max:50'],
            'purchase_unit' => ['nullable', 'string', 'max:50'],
            'units_per_purchase' => ['nullable', 'numeric', 'min:0.01'],
            'bom' => ['nullable', 'array'],
            'bom.*.material_product_id' => ['required', 'exists:products,product_id'],
            'bom.*.quantity_required' => ['required', 'numeric', 'min:0.01'],
        ]);

        if ($validated['item_type'] === 'made_product') {
            $validated['purchase_unit'] = null;
            $validated['units_per_purchase'] = null;
        }

        if (!in_array($validated['stock_purpose'], ['retail', 'both'])) {
            $validated['selling_price'] = null;
        }

        if (empty($validated['low_stock_threshold'])) {
            $validated['low_stock_threshold'] = 10;
        }

        $product->update(collect($validated)->except('bom')->toArray());

        if ($validated['item_type'] === 'made_product') {
            ProductComponent::where('parent_product_id', $product->product_id)->delete();

            if (!empty($validated['bom'])) {
                foreach ($validated['bom'] as $row) {
                    ProductComponent::create([
                        'parent_product_id' => $product->product_id,
                        'material_product_id' => $row['material_product_id'],
                        'quantity_required' => $row['quantity_required'],
                    ]);
                }
            }
        }

        app(AuditLogger::class)->log(
            'update',
            'products',
            $product->product_id,
            'Item updated: ' . $product->display_name
        );

        return redirect()
            ->route('products.index', ['item_type' => $validated['item_type'] === 'material' ? 'material' : 'product'])
            ->with('success', 'Item updated successfully.');
    }

    /* DESTROY (Archive) */
    public function destroy(Product $product)
    {
        $product->update(['is_active' => false]);

        app(AuditLogger::class)->log(
            'update',
            'products',
            $product->product_id,
            'Item archived: ' . $product->display_name
        );

        return redirect()
            ->route('products.index', [
                'item_type' => $product->item_type === 'material' ? 'material' : 'product',
            ])
            ->with('success', 'Item archived successfully.');
    }

    /* UNARCHIVE */
    public function unarchive(Product $product)
    {
        $product->update(['is_active' => true]);

        app(AuditLogger::class)->log(
            'update',
            'products',
            $product->product_id,
            'Item restored: ' . $product->display_name
        );

        return redirect()
            ->route('products.index', [
                'item_type' => $product->item_type === 'material' ? 'material' : 'product',
                'filters' => ['archived'],
            ])
            ->with('success', 'Item restored successfully.');
    }
}
