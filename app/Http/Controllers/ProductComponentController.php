<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductComponent;
use Illuminate\Http\Request;

class ProductComponentController extends Controller
{
    public function create(Product $product)
    {
        // Only Made Products can have a BOM.
        if ($product->item_type !== 'made_product') {
            abort(404);
        }

        // Only active Materials can be added as BOM components.
        $materials = Product::where('item_type', 'material')
            ->where('is_active', true)
            ->orderBy('name')
            ->orderBy('variation')
            ->get();

        return view('products.components.create', compact('product', 'materials'));
    }

    public function store(Request $request, Product $product)
    {
        // Only Made Products can have BOM components.
        if ($product->item_type !== 'made_product') {
            abort(404);
        }

        $validated = $request->validate([
            'material_product_id' => [
                'required',
                'integer',
                'exists:products,product_id',
            ],
            'quantity_required' => [
                'required',
                'numeric',
                'min:0.01',
            ],
        ]);

        $material = Product::where('product_id', $validated['material_product_id'])
            ->where('item_type', 'material')
            ->where('is_active', true)
            ->first();

        if (!$material) {
            return back()
                ->withErrors([
                    'material_product_id' =>
                        'Only active materials can be used as BOM components.'
                ])
                ->withInput();
        }

        // Prevent a product from being its own component.
        if ((int) $material->product_id === (int) $product->product_id) {
            return back()
                ->withErrors([
                    'material_product_id' =>
                        'A product cannot be used as its own component.'
                ])
                ->withInput();
        }

        // Prevent duplicate BOM components.
        $alreadyExists = ProductComponent::where(
            'parent_product_id',
            $product->product_id
        )
            ->where(
                'material_product_id',
                $material->product_id
            )
            ->exists();

        if ($alreadyExists) {
            return back()
                ->withErrors([
                    'material_product_id' =>
                        'This material is already part of the product BOM.'
                ])
                ->withInput();
        }

        ProductComponent::create([
            'parent_product_id' => $product->product_id,
            'material_product_id' => $material->product_id,
            'quantity_required' => $validated['quantity_required'],
        ]);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'BOM component added successfully.');
    }

    public function destroy(Product $product, $materialProduct)
    {
        $component = ProductComponent::where('parent_product_id', $product->product_id)
            ->where('material_product_id', $materialProduct)
            ->firstOrFail();

        $component->delete();

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'BOM component removed successfully.');
    }
}
