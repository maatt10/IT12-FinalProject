@extends('layouts.app')

@section('title', 'Product Details')

@section('content')

<div class="page-header">
    <div>
        <h1>{{ $product->display_name }}</h1>
        <p>View product information and bill of materials.</p>
    </div>

<a href="{{ route('products.index') }}" class="btn btn-secondary">
    Back to Products
</a>

</div>

<div class="card">

<h2>Product Information</h2>

<table style="margin-top: 15px;">
    <tr>
        <th>Product ID</th>
        <td>{{ $product->product_id }}</td>
    </tr>

    <tr>
        <th>Product Name</th>
        <td>{{ $product->name }}</td>
    </tr>

    <tr>
        <th>Variation</th>
        <td>{{ $product->variation ?? '—' }}</td>
    </tr>

    <tr>
        <th>Sellable</th>
        <td>{{ $product->is_sellable ? 'Yes' : 'No' }}</td>
    </tr>

    <tr>
        <th>Selling Price</th>
        <td>
            @if($product->selling_price !== null)
                ₱{{ number_format($product->selling_price, 2) }}
            @else
                —
            @endif
        </td>
    </tr>

    <tr>
        <th>Stock Unit</th>
        <td>{{ $product->stock_unit }}</td>
    </tr>

    <tr>
        <th>Purchase Unit</th>
        <td>{{ $product->purchase_unit ?? '—' }}</td>
    </tr>

    <tr>
        <th>Units per Purchase</th>
        <td>{{ $product->units_per_purchase ?? '—' }}</td>
    </tr>
</table>

</div>

<div class="card" style="margin-top: 20px;">

<div class="page-header" style="margin-bottom: 15px;">

    <div>
        <h2>Bill of Materials</h2>

        <p>
            Materials required to produce this product.
        </p>
    </div>

    <a
        href="{{ route('products.components.create', $product) }}"
        class="btn btn-primary"
    >
        + Add Component
    </a>

</div>


@if($product->parentComponents->count() > 0)

    <table>

        <thead>
            <tr>
                <th>Material</th>
                <th>Quantity Required</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

            @foreach($product->parentComponents as $component)

                <tr>

                    <td>
                        {{ $component->materialProduct->display_name }}
                    </td>

                    <td>
                        {{ $component->quantity_required }}
                        {{ $component->materialProduct->stock_unit }}
                    </td>

                    <td>

                        <form
                            action="{{ route('products.components.destroy', [
                                'product' => $product,
                                'materialProduct' => $component->material_product_id,
                            ]) }}"
                            method="POST"
                            onsubmit="return confirm('Remove this component from the BOM?');"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn-danger"
                            >
                                Remove
                            </button>

                        </form>

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

@else

    <p>
        No components have been added to this product yet.
    </p>

@endif


</div>

<div style="margin-top: 20px;">

<a
    href="{{ route('products.edit', $product) }}"
    class="btn btn-primary"
>
    Edit Product
</a>

</div>

@endsection