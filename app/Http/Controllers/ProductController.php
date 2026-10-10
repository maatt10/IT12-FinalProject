<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
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

            $item->has_retail = $retail !== null;
            $item->has_production = $production !== null;

            $item->retail_stock = $retail ? (float) $retail->current_quantity : null;
            $item->production_stock = $production ? (float) $production->current_quantity : null;

            $threshold = (float) $item->low_stock_threshold;

            $item->retail_low = $item->has_retail && $item->retail_stock <= $threshold;
            $item->production_low = $item->has_production && $item->production_stock <= $threshold;

            $item->total_stock = ($item->retail_stock ?? 0) + ($item->production_stock ?? 0);

            return $item;
        });

        if (in_array('low_stock', $filters)) {
            $items = $items->filter(fn($i) => $i->retail_low || $i->production_low);
        }
        if (in_array('sellable', $filters)) {
            $items = $items->filter(fn($i) => $i->has_retail);
        }
        if (in_array('production', $filters)) {
            $items = $items->filter(fn($i) => $i->has_production);
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
            'units_per_purchase' => [
                'nullable',
                'integer',
                'min:1',
                'required_with:purchase_unit',
            ],
            'bom' => ['nullable', 'array'],
            'bom.*.material_product_id' => ['required', 'exists:products,product_id'],
            'bom.*.quantity_required' => ['required', 'numeric', 'min:0.01'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
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

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }
        $product = Product::create(array_merge(
            collect($validated)->except('bom')->toArray(),
            ['image_path' => $imagePath]
        ));

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

        return redirect()
            ->route('products.index', ['item_type' => $validated['item_type'] === 'material' ? 'material' : 'product'])
            ->with('success', 'Item created successfully.');
    }

    public function show(Product $product)
    {
        $product->load([
            'inventory',
            'parentComponents.materialProduct',
            'usedAsComponent.parentProduct',
        ]);

        return view('products.show', compact('product'));
    }

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
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
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

        // Handle image upload
        $updateData = collect($validated)->except('bom')->toArray();

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $updateData['image_path'] = $request->file('image')->store('products', 'public');
        }

        // Optionally allow removing the image
        if ($request->boolean('remove_image') && $product->image_path) {
            Storage::disk('public')->delete($product->image_path);
            $updateData['image_path'] = null;
        }

        $product->update($updateData);
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

        return redirect()
            ->route('products.index', ['item_type' => $validated['item_type'] === 'material' ? 'material' : 'product'])
            ->with('success', 'Item updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->update(['is_active' => false]);

        return redirect()
            ->route('products.index', ['item_type' => $product->item_type === 'material' ? 'material' : 'product'])
            ->with('success', 'Item archived successfully.');
    }

    public function unarchive(Product $product)
    {
        $product->update(['is_active' => true]);

        return redirect()
            ->route('products.index', [
                'item_type' => $product->item_type === 'material' ? 'material' : 'product',
                'filters' => ['archived'],
            ])
            ->with('success', 'Item restored successfully.');
    }
}
