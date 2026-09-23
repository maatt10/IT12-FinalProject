<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\AuditLogger;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();

        return view('products.index', compact('products'));
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
            'is_sellable' => ['required', 'boolean'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'stock_unit' => ['required', 'string', 'max:50'],
            'purchase_unit' => ['nullable', 'string', 'max:50'],
            'units_per_purchase' => ['nullable', 'numeric', 'min:0.01'],
        ]);

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

            $product->delete();

            app(AuditLogger::class)->log(
                'delete',
                'products',
                $productId,
                'Product deleted: ' . $productName
            );

            return redirect()
                ->route('products.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('products.index')
                ->with('error', 'This product cannot be deleted because it is already being used in the system.');
        }
    }
}
