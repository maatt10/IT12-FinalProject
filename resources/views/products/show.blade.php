@extends('layouts.app')

@php
$typeLabel = $product->item_type === 'material' ? 'Material' : 'Product';
$tabParam = $product->item_type === 'material' ? 'material' : 'product';
$isArchived = !$product->is_active;
$isOwner = auth()->user()->role === 'owner';

$fmtQty = function ($val) {
$formatted = number_format((float) $val, 2, '.', ',');
$trimmed = rtrim(rtrim($formatted, '0'), '.');
return $trimmed === '' ? '0' : $trimmed;
};
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
<div class="card" style="margin-bottom: 20px;">
    <h2 class="card-heading">Item Information</h2>

    <div class="info-grid">
        <div class="info-item">
            <span class="info-label">Reference Code</span>
            <span class="info-value mono">{{ $product->reference_code }}</span>
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
            <span class="info-value">{{ $fmtQty($product->low_stock_threshold) }}</span>
        </div>
        @if($product->item_type === 'material' && $product->purchase_unit)
        <div class="info-item">
            <span class="info-label">Purchase Unit</span>
            <span class="info-value">{{ $product->purchase_unit }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Units per Purchase</span>
            <span class="info-value">{{ $fmtQty($product->units_per_purchase) }}</span>
        </div>
        @endif
    </div>
</div>

{{-- STOCK CARD --}}
<div class="card" style="margin-bottom: 20px;">
    <h2 class="card-heading">Current Stock</h2>

    <div class="stock-grid">
        @php
        $retail = $product->inventory->firstWhere('reserve_type', 'retail');
        $production = $product->inventory->firstWhere('reserve_type', 'production');
        $threshold = (float) $product->low_stock_threshold;
        @endphp

        <div class="stock-box {{ $retail && $retail->current_quantity <= $threshold ? 'low' : '' }}">
            <div class="stock-box-header">
                <span class="stock-label">Retail Stock</span>
                @if($retail && $retail->current_quantity <= $threshold)
                    <span class="stock-badge low">Low</span>
                    @endif
            </div>
            <div class="stock-primary">
                <span class="stock-value {{ $retail && $retail->current_quantity <= $threshold ? 'low' : '' }}">
                    @if($retail)
                    {{ $fmtQty($retail->current_quantity) }}
                    @else
                    <span style="color: #CBD5E1;">—</span>
                    @endif
                </span>
                @if($retail)
                <span class="stock-unit">{{ $product->stock_unit }}</span>
                @endif
            </div>
        </div>

        <div class="stock-box {{ $production && $production->current_quantity <= $threshold ? 'low' : '' }}">
            <div class="stock-box-header">
                <span class="stock-label">Production Stock</span>
                @if($production && $production->current_quantity <= $threshold)
                    <span class="stock-badge low">Low</span>
                    @endif
            </div>
            <div class="stock-primary">
                <span class="stock-value {{ $production && $production->current_quantity <= $threshold ? 'low' : '' }}">
                    @if($production)
                    {{ $fmtQty($production->current_quantity) }}
                    @else
                    <span style="color: #CBD5E1;">—</span>
                    @endif
                </span>
                @if($production)
                <span class="stock-unit">{{ $product->stock_unit }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- BOM --}}
@if($product->item_type === 'made_product')
<div class="card">
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
                <th class="num">Quantity Required</th>
                <th>Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($product->parentComponents as $component)
            <tr>
                <td style="font-weight: 600;">{{ $component->materialProduct->display_name }}</td>
                <td class="num">{{ $fmtQty($component->quantity_required) }}</td>
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
<div class="card" style="margin-top: 20px;">
    <h2 class="card-heading">Used In</h2>
    <p style="font-size: 13px; color: #64748B; margin-top: -10px; margin-bottom: 18px;">
        Products that use this material.
    </p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="num">Quantity Required</th>
            </tr>
        </thead>
        <tbody>
            @foreach($product->usedAsComponent as $component)
            <tr>
                <td style="font-weight: 600;">{{ $component->parentProduct->display_name }}</td>
                <td class="num">{{ $fmtQty($component->quantity_required) }}</td>
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

    .info-value.mono {
        font-family: 'SF Mono', Consolas, monospace;
        font-weight: 700;
        color: #6B5B95;
        letter-spacing: 0.5px;
    }

    .info-value.price {
        font-weight: 700;
        color: #2E5A3B;
        font-size: 16px;
    }

    table td.num,
    table th.num {
        text-align: right;
        white-space: nowrap;
    }

    /* STOCK GRID */
    .stock-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
    }

    .stock-box {
        padding: 20px;
        background: #FEFCF9;
        border: 1.5px solid #F0E6DD;
        border-radius: 12px;
    }

    .stock-box.low {
        background: #FFF8E1;
        border-color: #FFECB3;
    }

    .stock-box.short {
        background: #FDECEA;
        border-color: #F8D7DA;
    }

    .stock-box-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .stock-label {
        font-size: 11px;
        font-weight: 700;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .stock-badge {
        font-size: 9px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stock-badge.low {
        background: #FFECB3;
        color: #B8860B;
    }

    .stock-badge.short {
        background: #F8D7DA;
        color: #DC3545;
    }

    .stock-primary {
        display: flex;
        align-items: baseline;
        gap: 6px;
    }

    .stock-value {
        font-size: 32px;
        font-weight: 700;
        color: #2E5A3B;
        line-height: 1;
    }

    .stock-value.low {
        color: #B8860B;
    }

    .stock-value.short {
        color: #DC3545;
    }

    .stock-unit {
        font-size: 13px;
        font-weight: 500;
        color: #64748B;
    }

    .stock-breakdown {
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px dashed #F0E6DD;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .breakdown-line {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
    }

    .breakdown-label {
        color: #94A3B8;
        font-weight: 500;
    }

    .breakdown-value {
        color: #212121;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }

    .breakdown-value.reserved {
        color: #B8860B;
    }

    .breakdown-value.short {
        color: #DC3545;
    }

    .breakdown-line.total {
        padding-top: 6px;
        border-top: 1px solid #F0E6DD;
        margin-top: 4px;
    }

    .breakdown-line.total .breakdown-label {
        font-weight: 700;
        color: #64748B;
    }

    .breakdown-line.total .breakdown-value {
        font-weight: 700;
        color: #212121;
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