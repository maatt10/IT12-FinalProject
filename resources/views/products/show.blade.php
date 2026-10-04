@extends('layouts.app')

@php
$typeLabel = $product->item_type === 'material' ? 'Material' : 'Product';
$tabParam = $product->item_type === 'material' ? 'material' : 'product';
$isArchived = !$product->is_active;
$isOwner = auth()->user()->role === 'owner';
@endphp

@section('title', $product->name)

@section('content')

<div class="page-header">
    <div>
        <h1>
            {{ $product->name }}
            @if($product->variation)
            <span style="color: #94A3B8; font-weight: 500;">— {{ $product->variation }}</span>
            @endif
            @if($isArchived)
            <span class="archived-tag">Archived</span>
            @endif
        </h1>
        <p>{{ $typeLabel }} details and Bill of Materials.</p>
    </div>

    <a href="{{ route('products.index', ['item_type' => $tabParam]) }}" class="btn btn-secondary">
        ← Back to Inventory
    </a>
</div>

{{-- ACTION BAR --}}
@if($isOwner)
<div class="action-bar">
    @if($isArchived)
    <form action="{{ route('products.unarchive', $product) }}"
        method="POST" style="display: inline;"
        onsubmit="return confirm('Restore this item?');">
        @csrf
        <button type="submit" class="btn btn-primary">Unarchive</button>
    </form>
    @else
    <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit {{ $typeLabel }}</a>
    @endif
</div>
@endif

{{-- INFO CARD --}}
<div class="card" style="margin-bottom: 20px;  #E85D75;">
    <h2 class="card-heading">Item Information</h2>

    <div class="info-grid">
        <div class="info-item">
            <span class="info-label">Reference Code</span>
            <span class="info-value" style="font-family: 'SF Mono', Consolas, monospace; font-weight: 700; color: #E85D75; letter-spacing: 0.5px;">
                {{ $product->reference_code }}
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Type</span>
            <span class="info-value">{{ $typeLabel }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Stock Purpose</span>
            <span class="info-value">
                @if($product->stock_purpose === 'retail') Retail only
                @elseif($product->stock_purpose === 'production') Production only
                @else Both
                @endif
            </span>
        </div>
        @if($product->selling_price !== null)
        <div class="info-item">
            <span class="info-label">Selling Price</span>
            <span class="info-value price">₱{{ number_format($product->selling_price, 2) }}</span>
        </div>
        @endif
        <div class="info-item">
            <span class="info-label">Inventory Unit</span>
            <span class="info-value">{{ $product->stock_unit }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Low Stock Threshold</span>
            <span class="info-value">{{ (float) $product->low_stock_threshold }}</span>
        </div>
        @if($product->item_type === 'material' && $product->purchase_unit)
        <div class="info-item">
            <span class="info-label">Purchase Unit</span>
            <span class="info-value">{{ $product->purchase_unit }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Units per Purchase</span>
            <span class="info-value">{{ (float) $product->units_per_purchase }}</span>
        </div>
        @endif
    </div>
</div>

{{-- STOCK CARD --}}
<div class="card" style="margin-bottom: 20px;  #2E5A3B;">
    <h2 class="card-heading">Current Stock</h2>

    <div class="stock-grid">
        @php
        $retail = $product->inventory->firstWhere('reserve_type', 'retail');
        $production = $product->inventory->firstWhere('reserve_type', 'production');
        $threshold = (float) $product->low_stock_threshold;
        @endphp

        <div class="stock-grid">
            @php
            $retail = $product->inventory->firstWhere('reserve_type', 'retail');
            $production = $product->inventory->firstWhere('reserve_type', 'production');
            $threshold = (float) $product->low_stock_threshold;
            @endphp

            <div class="stock-box {{ $retail && $retail->current_quantity <= $threshold ? 'low' : '' }}">
                <span class="stock-label">Retail Stock</span>
                <span class="stock-value">
                    @if($retail)
                    {{ (float) $retail->current_quantity }} <span class="stock-unit">{{ $product->stock_unit }}</span>
                    @else
                    <span style="color: #CBD5E1;">—</span>
                    @endif
                </span>
                @if($retail && $retail->current_quantity <= $threshold)
                    <span class="low-tag">Low</span>
                    @endif
            </div>

            <div class="stock-box {{ $production && $production->current_quantity <= $threshold ? 'low' : '' }}">
                <span class="stock-label">Production Stock</span>
                <span class="stock-value">
                    @if($production)
                    {{ (float) $production->current_quantity }} <span class="stock-unit">{{ $product->stock_unit }}</span>
                    @else
                    <span style="color: #CBD5E1;">—</span>
                    @endif
                </span>
                @if($production && $production->current_quantity <= $threshold)
                    <span class="low-tag">Low</span>
                    @endif
            </div>
        </div>
    </div>
</div>

{{-- BOM --}}
@if($product->item_type === 'made_product')
<div class="card" style=" #D4AF37;">
    <h2 class="card-heading">Bill of Materials</h2>
    <p style="font-size: 13px; color: #64748B; margin-top: -10px; margin-bottom: 18px;">
        Materials required to make one unit of this product.
    </p>

    @if($product->parentComponents->isEmpty())
    <p style="color: #94A3B8; font-size: 13px; padding: 20px 0;">
        No materials defined for this product.
    </p>
    @else
    <table>
        <thead>
            <tr>
                <th>Material</th>
                <th style="text-align: right;">Quantity Required</th>
                <th>Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($product->parentComponents as $component)
            <tr>
                <td style="font-weight: 600;">{{ $component->materialProduct->display_name }}</td>
                <td style="text-align: right;">{{ (float) $component->quantity_required }}</td>
                <td style="color: #64748B;">{{ $component->materialProduct->stock_unit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endif

{{-- USED IN --}}
@if($product->item_type === 'material' && $product->usedAsComponent->count() > 0)
<div class="card" style="margin-top: 20px;  #D4AF37;">
    <h2 class="card-heading">Used In</h2>
    <p style="font-size: 13px; color: #64748B; margin-top: -10px; margin-bottom: 18px;">
        Products that use this material.
    </p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th style="text-align: right;">Quantity Required</th>
            </tr>
        </thead>
        <tbody>
            @foreach($product->usedAsComponent as $component)
            <tr>
                <td style="font-weight: 600;">{{ $component->parentProduct->display_name }}</td>
                <td style="text-align: right;">{{ (float) $component->quantity_required }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<style>
    .card-heading {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 18px;
    }

    .action-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 18px 24px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding-bottom: 14px;
        border-bottom: 1px dashed #F0E6DD;
    }

    .info-label {
        font-size: 11px;
        font-weight: 600;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .info-value {
        font-size: 14px;
        color: #212121;
        font-weight: 500;
    }

    .info-value.price {
        font-weight: 700;
        color: #2E5A3B;
        font-size: 16px;
    }

    .stock-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }

    .stock-box {
        padding: 20px;
        background: #FEFCF9;
        border: 1.5px solid #F0E6DD;
        border-radius: 12px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .stock-box.low {
        background: #FDECEA;
        border-color: #F8D7DA;
    }

    .stock-label {
        font-size: 11px;
        font-weight: 600;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .stock-value {
        font-size: 24px;
        font-weight: 700;
        color: #2E5A3B;
        text-align: right;
    }

    .stock-box.low .stock-value {
        color: #DC3545;
    }

    .stock-unit {
        font-size: 13px;
        font-weight: 500;
        color: #64748B;
    }

    .low-tag {
        position: absolute;
        top: 8px;
        right: 12px;
        font-size: 9px;
        font-weight: 700;
        color: #DC3545;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #FFFFFF;
        padding: 2px 6px;
        border-radius: 4px;
    }

    .archived-tag {
        display: inline-block;
        margin-left: 8px;
        vertical-align: middle;
        padding: 3px 10px;
        border-radius: 6px;
        background: #F1F5F9;
        color: #64748B;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

@endsection