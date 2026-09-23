@extends('layouts.app')

@section('title', 'Products')

@section('content')

<div class="page-header">
    <div>
        <h1>Products</h1>
        <p>Manage products and materials used by the shop.</p>
    </div>

    <a href="{{ route('products.create') }}" class="btn btn-primary">
        + Add Product
    </a>
</div>

<div class="card">

    @if($products->count() > 0)

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Variation</th>
                <th>Sellable</th>
                <th>Selling Price</th>
                <th>Stock Unit</th>
                <th>Purchase Unit</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->product_id }}</td>

                <td>
                    {{ $product->name }}
                </td>
                <td>
                    {{ $product->variation ?? '—' }}
                </td>
                <td>
                    {{ $product->is_sellable ? 'Yes' : 'No' }}
                </td>

                <td>
                    @if($product->selling_price !== null)
                    ₱{{ number_format($product->selling_price, 2) }}
                    @else
                    —
                    @endif
                </td>

                <td>
                    {{ $product->stock_unit }}
                </td>

                <td>
                    {{ $product->purchase_unit ?? '—' }}
                </td>

                <td>
                    <div class="actions">

                        <a href="{{ route('products.show', $product) }}"
                            class="btn btn-secondary">
                            View
                        </a>

                        <a href="{{ route('products.edit', $product) }}"
                            class="btn btn-primary">
                            Edit
                        </a>

                        <form action="{{ route('products.destroy', $product) }}"
                            method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this product?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger">
                                Delete
                            </button>
                        </form>

                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @else

    <p>No products have been added yet.</p>

    <div style="margin-top: 15px;">
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            Add Your First Product
        </a>
    </div>

    @endif

</div>

@endsection