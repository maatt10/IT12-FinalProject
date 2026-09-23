@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1>Set Initial Stock</h1>
        <p>Enter the existing physical stock for this product.</p>
    </div>

    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
        Back to Inventory
    </a>
</div>

<div class="card">

    <h2 style="margin-bottom: 20px;">
        {{ $product->name }}

        @if($product->variation)
            - {{ $product->variation }}
        @endif
    </h2>

    <p style="margin-bottom: 20px;">
        Stock Unit:
        <strong>{{ $product->stock_unit }}</strong>
    </p>

    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('inventory.initial-stock.store', $product) }}"
        method="POST"
    >
        @csrf

        <div class="form-group">
            <label for="retail_quantity">
                Retail Stock
            </label>

            <input
                type="number"
                id="retail_quantity"
                name="retail_quantity"
                class="form-control"
                min="0"
                step="0.01"
                value="{{ old('retail_quantity', 0) }}"
                required
            >

            <small>
                Stock available for direct customer sales.
            </small>
        </div>

        <div class="form-group">
            <label for="production_quantity">
                Production Stock
            </label>

            <input
                type="number"
                id="production_quantity"
                name="production_quantity"
                class="form-control"
                min="0"
                step="0.01"
                value="{{ old('production_quantity', 0) }}"
                required
            >

            <small>
                Stock reserved for bouquet or product production.
            </small>
        </div>

        <button type="submit" class="btn btn-primary">
            Save Initial Stock
        </button>

        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </form>

</div>

@endsection