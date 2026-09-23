@extends('layouts.app')

@section('title', 'Add Product')

@section('content')

<div class="page-header">
    <div>
        <h1>Add Product</h1>
        <p>Add a new product or material to the system.</p>
    </div>

    <a href="{{ route('products.index') }}" class="btn btn-secondary">
        Back to Products
    </a>
</div>

<div class="card">

    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Product Name</label>

            <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                value="{{ old('name') }}"
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
                value="{{ old('variation') }}"
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
                <option value="1" {{ old('is_sellable', '1') == '1' ? 'selected' : '' }}>
                    Yes
                </option>

                <option value="0" {{ old('is_sellable') === '0' ? 'selected' : '' }}>
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
                value="{{ old('selling_price') }}"
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
                value="{{ old('stock_unit') }}"
                placeholder="e.g. piece, stem, bundle"
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
                value="{{ old('purchase_unit') }}"
                placeholder="e.g. bundle, box">

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
                value="{{ old('units_per_purchase') }}"
                min="0.01"
                step="0.01"
                placeholder="e.g. 10">

            @error('units_per_purchase')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>


        <button type="submit" class="btn btn-primary">
            Save Product
        </button>

        <a href="{{ route('products.index') }}"
            class="btn btn-secondary">
            Cancel
        </a>

    </form>

</div>

@endsection