<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\AuditLogger;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $itemType = $request->query('item_type', 'product');

        if (!in_array($itemType, ['product', 'material'])) {
            $itemType = 'product';
        }

        // Base query for the active tab
        if ($itemType === 'material') {
            $query = Product::where('item_type', 'material');
        } else {
            $query = Product::whereIn('item_type', ['retail_product', 'made_product']);
        }

        $query->orderBy('name')->orderBy('variation');

        if (!$request->boolean('show_archived')) {
            $query->where('is_active', true);
        }

        $items = $query->get();

        // Counts for tab badges
        $productCount = Product::whereIn('item_type', ['retail_product', 'made_product'])
            ->where('is_active', true)
            ->count();

        $materialCount = Product::where('item_type', 'material')
            ->where('is_active', true)
            ->count();

        return view('products.index', compact(
            'items',
            'itemType',
            'productCount',
            'materialCount'
        ));
    }
    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'variation' => ['nullable', 'string', 'max:100'],

            'item_type' => [
                'required',
                Rule::in([
                    'material',
                    'retail_product',
                    'made_product',
                ]),
            ],

            'is_sellable' => ['required', 'boolean'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'stock_unit' => ['required', 'string', 'max:50'],
            'purchase_unit' => ['nullable', 'string', 'max:50'],
            'units_per_purchase' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $validated['is_active'] = true;

        $product = Product::create($validated);

        app(AuditLogger::class)->log(
            'create',
            'products',
            $product->product_id,
            'Product created: ' . $product->display_name
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'Product added successfully.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'variation' => ['nullable', 'string', 'max:100'],

            'item_type' => [
                'required',
                Rule::in([
                    'material',
                    'retail_product',
                    'made_product',
                ]),
            ],

            'is_sellable' => ['required', 'boolean'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'stock_unit' => ['required', 'string', 'max:50'],
            'purchase_unit' => ['nullable', 'string', 'max:50'],
            'units_per_purchase' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $product->update($validated);

        app(AuditLogger::class)->log(
            'update',
            'products',
            $product->product_id,
            'Product updated: ' . $product->display_name
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        try {
            $productName = $product->display_name;
            $productId = $product->product_id;

            $product->update([
                'is_active' => false,
            ]);

            app(AuditLogger::class)->log(
                'update',
                'products',
                $productId,
                'Product archived: ' . $productName
            );

            return redirect()
                ->route('products.index')
                ->with('success', 'Product archived successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('products.index')
                ->with('error', 'The product could not be archived.');
        }
    }
}
