@extends('layouts.app')

@section('title', 'Purchase Details')

@section('content')

<div class="page-header">
    <div>
        <h1>Purchase Details</h1>
        <p>Review the recorded purchase and its items.</p>
    </div>

    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
        ← Back to Purchases
    </a>
</div>

{{-- PURCHASE INFO CARD --}}
<div class="card" style="margin-bottom: 20px; border-top: 4px solid #E85D75;">

    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Purchase Information
    </h2>

    <div class="info-grid">

        <div class="info-item">
            <span class="info-label">Purchase Date &amp; Time</span>
            <span class="info-value">{{ $purchase->purchase_date->format('M d, Y h:i A') }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Supplier</span>
            <span class="info-value">{{ $purchase->supplier_name ?: '—' }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Recorded By</span>
            <span class="info-value">{{ $purchase->user->full_name ?? 'Unknown' }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Total Amount</span>
            <span class="info-value price">₱{{ number_format($purchase->total_amount, 2) }}</span>
        </div>

    </div>

</div>

{{-- PURCHASED ITEMS CARD --}}
<div class="card" style="border-top: 4px solid #D4AF37;">

    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        Purchased Items
    </h2>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align: right;">Quantity</th>
                    <th>Purchase Unit</th>
                    <th style="text-align: right;">Unit Cost</th>
                    <th>Allocation</th>
                    <th style="text-align: right;">Stock Added</th>
                    <th style="text-align: right;">Line Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach($purchase->items as $item)
                @php
                $product = $item->product;
                $stockQuantity = (float) $item->quantity * (float) $product->units_per_purchase;
                @endphp

                <tr>
                    <td style="font-weight: 600; color: #212121;">
                        {{ $product->display_name }}
                    </td>

                    <td style="text-align: right; color: #212121; font-weight: 500;">
                        {{ (float) $item->quantity }}
                    </td>

                    <td style="color: #64748B;">
                        {{ $product->purchase_unit }}
                    </td>

                    <td style="text-align: right; color: #64748B;">
                        ₱{{ number_format($item->unit_cost, 2) }}
                    </td>

                    <td>
                        <span class="alloc-tag alloc-{{ $item->reserve_type }}">
                            {{ ucfirst($item->reserve_type) }}
                        </span>
                    </td>

                    <td style="text-align: right; font-weight: 600; color: #2E5A3B;">
                        {{ (float) $stockQuantity }} {{ $product->stock_unit }}
                    </td>

                    <td style="text-align: right; font-weight: 700; color: #2E5A3B;">
                        ₱{{ number_format($item->quantity * $item->unit_cost, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

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
    }

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

    /* Allocation tag — plain colored text, non-clickable */
    .alloc-tag {
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        cursor: default;
        user-select: none;
    }

    .alloc-retail {
        color: #E85D75;
    }

    .alloc-production {
        color: #B8860B;
    }
</style>

@endsection