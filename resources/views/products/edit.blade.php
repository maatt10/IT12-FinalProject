@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')

<div class="page-header">
    <div>
        <h1>Edit Product</h1>
        <p>Update the information for this product.</p>
    </div>

    <a href="{{ route('products.index') }}" class="btn btn-secondary">
        Back to Products
    </a>
</div>

<div class="card">

    <form action="{{ route('products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Product Name</label>

            <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                value="{{ old('name', $product->name) }}"
                required>

            @error('name')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="variation">Variation</label>

            <input
                type="text"
                id="variation"
                name="variation"
                class="form-control"
                value="{{ old('variation', $product->variation) }}"
                placeholder="e.g. Red, Blue, Small, Large">

            @error('variation')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="is_sellable">Sellable</label>

            <select
                id="is_sellable"
                name="is_sellable"
                class="form-control"
                required>
                <option value="1"
                    {{ old('is_sellable', $product->is_sellable) == '1' ? 'selected' : '' }}>
                    Yes
                </option>

                <option value="0"
                    {{ old('is_sellable', $product->is_sellable) == '0' ? 'selected' : '' }}>
                    No
                </option>
            </select>

            @error('is_sellable')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="selling_price">Selling Price</label>

            <input
                type="number"
                id="selling_price"
                name="selling_price"
                class="form-control"
                value="{{ old('selling_price', $product->selling_price) }}"
                min="0"
                step="0.01"
                placeholder="0.00">

            @error('selling_price')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="stock_unit">Stock Unit</label>

            <input
                type="text"
                id="stock_unit"
                name="stock_unit"
                class="form-control"
                value="{{ old('stock_unit', $product->stock_unit) }}"
                required>

            @error('stock_unit')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="purchase_unit">Purchase Unit</label>

            <input
                type="text"
                id="purchase_unit"
                name="purchase_unit"
                class="form-control"
                value="{{ old('purchase_unit', $product->purchase_unit) }}">

            @error('purchase_unit')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="units_per_purchase">
                Units per Purchase
            </label>

            <input
                type="number"
                id="units_per_purchase"
                name="units_per_purchase"
                class="form-control"
                value="{{ old('units_per_purchase', $product->units_per_purchase) }}"
                min="0.01"
                step="0.01">

            @error('units_per_purchase')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            Update Product
        </button>

        <a href="{{ route('products.index') }}"
            class="btn btn-secondary">
            Cancel
        </a>

    </form>

</div>

@endsection