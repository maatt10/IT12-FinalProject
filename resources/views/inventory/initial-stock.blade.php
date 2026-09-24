@extends('layouts.app')

@section('title', 'Set Initial Stock')

@section('content')

<div class="page-header">
    <div>
        <h1>Set Initial Stock</h1>
        <p>Enter the existing physical stock for this product.</p>
    </div>

    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
        ← Back to Inventory
    </a>
</div>

<div class="card" style="max-width: 720px; border-top: 4px solid #D4AF37;">

    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
        </svg>
        {{ $product->name }}
        @if($product->variation)
            <span style="color: #94A3B8; font-weight: 500;">— {{ $product->variation }}</span>
        @endif
    </h2>

    <p style="font-size: 13px; color: #64748B; margin-top: -10px; margin-bottom: 20px;">
        Stock Unit: <strong style="color: #212121;">{{ $product->stock_unit }}</strong>
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

    <form action="{{ route('inventory.initial-stock.store', $product) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="retail_quantity">Retail Stock <span class="req">*</span></label>
            <input
                type="number"
                id="retail_quantity"
                name="retail_quantity"
                class="form-control"
                min="0"
                step="0.01"
                value="{{ old('retail_quantity', 0) }}"
                required>
            <small style="color: #94A3B8; font-size: 12px;">
                Stock available for direct customer sales.
            </small>
        </div>

        <div class="form-group">
            <label for="production_quantity">Production Stock <span class="req">*</span></label>
            <input
                type="number"
                id="production_quantity"
                name="production_quantity"
                class="form-control"
                min="0"
                step="0.01"
                value="{{ old('production_quantity', 0) }}"
                required>
            <small style="color: #94A3B8; font-size: 12px;">
                Stock reserved for bouquet or product production.
            </small>
        </div>

        <div class="form-actions">
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Initial Stock</button>
        </div>

    </form>

</div>

<style>
    .card-heading {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .req {
        color: #E85D75;
        margin-left: 2px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 20px;
        margin-top: 10px;
        border-top: 1px solid #F0E6DD;
    }
</style>

@endsection