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

    <div style="overflow-x: auto;">
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
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($products as $product)
                <tr>
                    <td style="color: #94A3B8; font-size: 12px;">
                        #{{ $product->product_id }}
                    </td>

                    <td style="font-weight: 600; color: #212121;">
                        {{ $product->name }}
                    </td>

                    <td style="color: #64748B;">
                        {{ $product->variation ?? '—' }}
                    </td>
                    <td>
                        @if($product->is_sellable)
                        <span class="sellable-yes">✓ Yes</span>
                        @else
                        <span class="sellable-no">No</span>
                        @endif
                    </td>

                    <td style="font-weight: 600; color: #2E5A3B;">
                        @if($product->selling_price !== null)
                        ₱{{ number_format($product->selling_price, 2) }}
                        @else
                        <span style="color: #94A3B8;">—</span>
                        @endif
                    </td>

                    <td style="color: #64748B;">
                        {{ $product->stock_unit }}
                    </td>

                    <td style="color: #64748B;">
                        {{ $product->purchase_unit ?? '—' }}
                    </td>

                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('products.show', $product) }}" class="action-btn view">
                                View
                            </a>

                            <a href="{{ route('products.edit', $product) }}" class="action-btn edit">
                                Edit
                            </a>

                            <form action="{{ route('products.destroy', $product) }}"
                                method="POST"
                                style="display: inline;"
                                onsubmit="return confirm('Are you sure you want to delete this product?');">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="action-btn delete">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @else

    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <h3>No products yet</h3>
        <p>Start by adding your first product or material.</p>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            + Add Your First Product
        </a>
    </div>

    @endif

</div>

<style>
    /* Compact action buttons */
    .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
    }

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

    .action-btn.view {
        background: #F1F5F9;
        color: #475569;
    }

    .action-btn.view:hover {
        background: #E2E8F0;
        color: #1E293B;
    }

    .action-btn.edit {
        background: #FCE4EC;
        color: #E85D75;
    }

    .action-btn.edit:hover {
        background: #F8BBD0;
        color: #D14A62;
    }

    .action-btn.delete {
        background: #FDECEA;
        color: #DC3545;
    }

    .action-btn.delete:hover {
        background: #F8D7DA;
        color: #B02A37;
    }

    /* Sellable indicator — plain text, clearly not a button */
    .sellable-yes {
        color: #2E5A3B;
        font-weight: 600;
        font-size: 13px;
        cursor: default;
        user-select: none;
    }

    .sellable-no {
        color: #94A3B8;
        font-weight: 500;
        font-size: 13px;
        font-style: italic;
        cursor: default;
        user-select: none;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748B;
    }

    .empty-state svg {
        color: #D4AF37;
        margin-bottom: 16px;
        opacity: 0.6;
    }

    .empty-state h3 {
        font-size: 18px;
        color: #212121;
        margin-bottom: 6px;
        font-weight: 700;
    }

    .empty-state p {
        font-size: 14px;
        margin-bottom: 20px;
    }
</style>

@endsection