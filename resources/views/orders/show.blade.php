@extends('layouts.app')

@section('title', 'Order Details')

@section('content')

<div class="page-header">
    <div>
        <h1>Order Details</h1>
        <p>View the complete information for this online bouquet order.</p>
    </div>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
        ← Back to Orders
    </a>
</div>

{{-- ORDER INFORMATION --}}
<div class="card" style="margin-bottom: 20px; border-top: 4px solid #E85D75;">
    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Order Information
    </h2>

    <div class="info-grid">

        <div class="info-item">
            <span class="info-label">Customer</span>
            <span class="info-value">{{ $order->customer->full_name }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Recorded By</span>
            <span class="info-value">{{ $order->user->full_name }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Order Date</span>
            <span class="info-value">{{ $order->order_date->format('M d, Y h:i A') }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Order Type</span>
            <span class="info-value">{{ $order->order_type === 'ready_made' ? 'Ready-Made' : 'Customized' }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Fulfillment</span>
            <span class="info-value">{{ ucfirst($order->fulfillment_type) }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Current Status</span>
            <span class="status-text status-{{ $order->order_status }}">
                {{ ucfirst($order->order_status) }}
            </span>
        </div>

    </div>

    {{-- STATUS UPDATE FORM --}}
    <div class="status-update">
        <form action="{{ route('orders.status.update', $order) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;">
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                    <label for="order_status">Update Status</label>
                    <select id="order_status" name="order_status" class="form-control" required>
                        <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->order_status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="preparing" {{ $order->order_status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="ready" {{ $order->order_status === 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>

{{-- RECEIVER INFORMATION --}}
<div class="card" style="margin-bottom: 20px;">
    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        Receiver Information
    </h2>

    <div class="info-grid">

        <div class="info-item">
            <span class="info-label">Receiver Name</span>
            <span class="info-value">{{ $order->receiver_full_name }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Contact Number</span>
            <span class="info-value">{{ $order->receiver_contact }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Delivery / Pickup Timing</span>
            <span class="info-value">{{ $order->delivery_timing }}</span>
        </div>

        <div class="info-item" style="grid-column: 1 / -1;">
            <span class="info-label">Delivery Address</span>
            <span class="info-value">{{ $order->delivery_address ?: '—' }}</span>
        </div>

    </div>
</div>

{{-- PAYMENT & FULFILLMENT --}}
<div class="card" style="margin-bottom: 20px;">
    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2E5A3B" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
        Payment &amp; Fulfillment
    </h2>

    <div class="info-grid">

        <div class="info-item">
            <span class="info-label">Fulfillment Type</span>
            <span class="info-value">{{ ucfirst($order->fulfillment_type) }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Delivery Fee</span>
            <span class="info-value price">₱{{ number_format($order->delivery_fee, 2) }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Payment Proof Reference</span>
            <span class="info-value">{{ $order->payment_proof_reference ?: '—' }}</span>
        </div>

    </div>
</div>

{{-- ORDER ITEMS --}}
<div class="card" style="border-top: 4px solid #D4AF37;">
    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        Order Items
    </h2>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align: right;">Quantity</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th>Customization</th>
                    <th style="text-align: right;">Line Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td style="font-weight: 600; color: #212121;">
                            {{ $item->product->display_name }}
                        </td>

                        <td style="text-align: right; color: #212121;">
                            {{ (float) $item->quantity }}
                        </td>

                        <td style="text-align: right; color: #64748B;">
                            ₱{{ number_format($item->unit_price, 2) }}
                        </td>

                        <td style="color: #64748B;">
                            {{ $item->customization_details ?: '—' }}
                        </td>

                        <td style="text-align: right; font-weight: 700; color: #2E5A3B;">
                            ₱{{ number_format($item->line_total, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- TOTALS --}}
    <div class="order-summary">
        <div class="summary-line">
            <span>Delivery Fee</span>
            <strong>₱{{ number_format($order->delivery_fee, 2) }}</strong>
        </div>
        <div class="summary-line total">
            <span>Total</span>
            <strong>₱{{ number_format($order->total_amount, 2) }}</strong>
        </div>
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

    .status-text {
        font-size: 14px;
        font-weight: 700;
        text-transform: capitalize;
        cursor: default;
        user-select: none;
    }
    .status-pending { color: #64748B; }
    .status-confirmed { color: #E85D75; }
    .status-preparing { color: #B8860B; }
    .status-ready { color: #D14A62; }
    .status-completed { color: #2E5A3B; }
    .status-cancelled { color: #DC3545; }

    .status-update {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #F0E6DD;
    }

    .order-summary {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid #F8BBD0;
    }
    .summary-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 14px;
        color: #64748B;
    }
    .summary-line strong {
        font-size: 16px;
        color: #212121;
        font-weight: 700;
    }
    .summary-line.total {
        margin-top: 6px;
        padding-top: 12px;
        border-top: 1px dashed #F0E6DD;
    }
    .summary-line.total span {
        font-size: 15px;
        font-weight: 600;
        color: #212121;
    }
    .summary-line.total strong {
        font-size: 24px;
        color: #E85D75;
        cursor: default;
        user-select: none;
    }
</style>

@endsection