@extends('layouts.app')

@section('title', 'Product Details')

@section('content')

<div class="page-header">
    <div>
        <h1>{{ $product->display_name }}</h1>
        <p>View product information and bill of materials.</p>
    </div>

    <div style="display: flex; gap: 8px;">
        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
            Edit Product
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            ← Back
        </a>
    </div>
</div>

{{-- PRODUCT INFORMATION --}}
<div class="card" style="margin-bottom: 20px; border-top: 4px solid #E85D75;">

    <h2 style="font-size: 16px; font-weight: 700; color: #212121; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        Product Information
    </h2>

    <div class="info-grid">

        <div class="info-item">
            <span class="info-label">Product ID</span>
            <span class="info-value">#{{ $product->product_id }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Product Name</span>
            <span class="info-value">{{ $product->name }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Variation</span>
            <span class="info-value">{{ $product->variation ?? '—' }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Sellable</span>
            <span class="info-value">
                @if($product->is_sellable)
                <span class="sellable-yes">✓ Yes</span>
                @else
                <span class="sellable-no">No</span>
                @endif
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">Selling Price</span>
            <span class="info-value price">
                @if($product->selling_price !== null)
                ₱{{ number_format($product->selling_price, 2) }}
                @else
                <span style="color: #94A3B8;">—</span>
                @endif
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">Stock Unit</span>
            <span class="info-value">{{ $product->stock_unit }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Purchase Unit</span>
            <span class="info-value">{{ $product->purchase_unit ?? '—' }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Units per Purchase</span>
            <span class="info-value">
                {{ $product->units_per_purchase !== null ? (float) $product->units_per_purchase : '—' }}
            </span>
        </div>

    </div>

</div>

{{-- BILL OF MATERIALS --}}
<div class="card" style="border-top: 4px solid #D4AF37;">

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 16px; font-weight: 700; color: #212121; display: flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Bill of Materials
            </h2>
            <p style="font-size: 13px; color: #64748B; margin-top: 4px;">
                Materials required to produce this product.
            </p>
        </div>

        <a href="{{ route('products.components.create', $product) }}" class="btn btn-primary">
            + Add Component
        </a>
    </div>

    @if($product->parentComponents->count() > 0)

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Quantity Required</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($product->parentComponents as $component)
                <tr>
                    <td style="font-weight: 600; color: #212121;">
                        {{ $component->materialProduct->display_name }}
                    </td>

                    <td style="color: #212121; font-weight: 600;">
                        {{ (float) $component->quantity_required }} {{ $component->materialProduct->stock_unit }}
                    </td>

                    <td style="text-align: right;">
                        <form
                            action="{{ route('products.components.destroy', [
                                    'product' => $product,
                                    'materialProduct' => $component->material_product_id,
                                ]) }}"
                            method="POST"
                            style="display: inline;"
                            onsubmit="return confirm('Remove this component from the BOM?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="action-btn delete">
                                Remove
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @else

    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <h3>No components yet</h3>
        <p>Add materials needed to produce this product.</p>
    </div>

    @endif

</div>

<style>
    /* Info grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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

    /* Sellable — non-clickable text */
    .sellable-yes {
        color: #2E5A3B;
        font-weight: 600;
        cursor: default;
        user-select: none;
    }

    .sellable-no {
        color: #94A3B8;
        font-weight: 500;
        font-style: italic;
        cursor: default;
        user-select: none;
    }


    /* Action buttons */
    .action-btn {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .action-btn.delete {
        background: #FDECEA;
        color: #DC3545;
    }

    .action-btn.delete:hover {
        background: #F8D7DA;
        color: #B02A37;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #64748B;
    }

    .empty-state svg {
        color: #D4AF37;
        margin-bottom: 12px;
        opacity: 0.6;
    }

    .empty-state h3 {
        font-size: 16px;
        color: #212121;
        margin-bottom: 4px;
        font-weight: 700;
    }

    .empty-state p {
        font-size: 13px;
    }
</style>

@endsection