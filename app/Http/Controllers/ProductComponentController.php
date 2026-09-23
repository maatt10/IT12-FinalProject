<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductComponent;
use Illuminate\Http\Request;

class ProductComponentController extends Controller
{
    public function create(Product $product)
    {
        $materials = Product::where('product_id', '!=', $product->product_id)
            ->orderBy('name')
            ->orderBy('variation')
            ->get();

        return view('products.components.create', compact('product', 'materials'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'material_product_id' => ['required', 'exists:products,product_id'],
            'quantity_required' => ['required', 'numeric', 'min:0.01'],
        ]);

        if ((int) $validated['material_product_id'] === (int) $product->product_id) {
            return back()
                ->withErrors([
                    'material_product_id' => 'A product cannot be used as its own component.'
                ])
                ->withInput();
        }

        $alreadyExists = ProductComponent::where('parent_product_id', $product->product_id)
            ->where('material_product_id', $validated['material_product_id'])
            ->exists();

        if ($alreadyExists) {
            return back()
                ->withErrors([
                    'material_product_id' => 'This component is already part of the product BOM.'
                ])
                ->withInput();
        }

        ProductComponent::create([
            'parent_product_id' => $product->product_id,
            'material_product_id' => $validated['material_product_id'],
            'quantity_required' => $validated['quantity_required'],
        ]);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'BOM component added successfully.');
    }

    public function destroy(Product $product, ProductComponent $component)
    {
        if ((int) $component->parent_product_id !== (int) $product->product_id) {
            abort(404);
        }

        $component->delete();

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'BOM component removed successfully.');
    }
}