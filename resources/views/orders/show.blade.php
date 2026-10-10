@extends('layouts.app')

@section('title', 'Order ' . $order->reference_code)

@php
    $backUrl = request('from') === 'records'
        ? route('records.index', ['tab' => 'sales', 'sub' => 'online'])
        : route('orders.index');
@endphp

@section('content')

<div class="form-page-wide">

    <div class="page-header">
        <div>
            <h1>
                Order Details
                <span class="ref-badge">{{ $order->reference_code }}</span>
            </h1>
            <p>View the complete information for this bouquet order.</p>
        </div>

        <a href="{{ $backUrl }}" class="btn btn-secondary">← Back</a>
    </div>

    {{-- ORDER INFORMATION --}}
    <div class="card">
        <h2 class="card-heading">Order Information</h2>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Order Number</span>
                <span class="info-value mono">{{ $order->reference_code }}</span>
            </div>

            <div class="info-item">
                <span class="info-label">Order Date</span>
                <span class="info-value">{{ $order->order_date->format('M d, Y h:i A') }}</span>
            </div>

            <div class="info-item">
                <span class="info-label">Customer</span>
                <span class="info-value">
                    {{ $order->customer->full_name ?? $order->customer_name ?? 'Unregistered Customer' }}
                </span>
            </div>

            <div class="info-item">
                <span class="info-label">Recorded By</span>
                <span class="info-value">{{ $order->user->full_name ?? 'Unknown' }}</span>
            </div>

            <div class="info-item">
                <span class="info-label">Order Type</span>
                <span class="info-value">
                    {{ $order->order_type === 'ready_made' ? 'Ready-Made' : 'Customized' }}
                </span>
            </div>

            <div class="info-item">
                <span class="info-label">Sales Channel</span>
                <span class="info-value">
                    {{ $order->channel === 'walk_in' ? 'Walk-in' : 'Online' }}
                </span>
            </div>

            <div class="info-item">
                <span class="info-label">Current Status</span>
                <span class="status-text status-{{ $order->order_status }}">
                    {{ ucfirst($order->order_status) }}
                </span>
            </div>
        </div>

        {{-- STATUS UPDATE FORM --}}
        @if(in_array($order->order_status, ['completed', 'cancelled']))
            <div class="status-update">
                <div class="locked-note">
                    This order is <strong>{{ $order->order_status }}</strong>. Status can no longer be changed.
                </div>

                @if($order->order_status === 'cancelled' && $order->cancellation_note)
                    <div class="cancel-note">
                        <strong>Cancellation reason:</strong>
                        {{ $order->cancellation_note }}
                    </div>
                @endif
            </div>
        @else
            <div class="status-update">
                <form action="{{ route('orders.status.update', $order) }}" method="POST" id="status-form">
                    @csrf
                    @method('PUT')

                    <div class="status-form-row">
                        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                            <label for="order_status">Update Status</label>
                            <select id="order_status" name="order_status" class="form-control" required>
                                <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                            Update Status
                        </button>
                    </div>

                    <div id="cancel-note-group" style="display: none; margin-top: 16px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="cancellation_note">Cancellation Reason (optional)</label>
                            <textarea id="cancellation_note" name="cancellation_note"
                                      class="form-control" rows="2" maxlength="1000"
                                      placeholder="e.g. Customer changed their mind"></textarea>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    </div>

    {{-- RECEIVER INFORMATION --}}
    <div class="card">
        <h2 class="card-heading">Receiver Information</h2>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Receiver Name</span>
                <span class="info-value">{{ $order->receiver_full_name }}</span>
            </div>

            <div class="info-item">
                <span class="info-label">Mobile Number</span>
                <span class="info-value">{{ $order->receiver_contact }}</span>
            </div>

            <div class="info-item">
                <span class="info-label">Delivery / Pickup Schedule</span>
                <span class="info-value">
                    {{ $order->delivery_datetime ? $order->delivery_datetime->format('M d, Y h:i A') : '—' }}
                </span>
            </div>

            <div class="info-item">
                <span class="info-label">Fulfillment</span>
                <span class="info-value">{{ ucfirst($order->fulfillment_type) }}</span>
            </div>

            @if($order->fulfillment_type === 'delivery')
                <div class="info-item" style="grid-column: 1 / -1;">
                    <span class="info-label">Delivery Address</span>
                    <span class="info-value">{{ $order->delivery_address ?: '—' }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- PAYMENT INFORMATION --}}
    <div class="card">
        <h2 class="card-heading">Payment Information</h2>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Payment Method</span>
                <span class="info-value">
                    {{ $order->payment_method ? ucfirst($order->payment_method) : ($order->channel === 'online' ? 'GCash' : '—') }}
                </span>
            </div>

            @if($order->payment_method === 'gcash' || $order->channel === 'online')
                <div class="info-item">
                    <span class="info-label">GCash Reference Number</span>
                    <span class="info-value mono-soft">
                        {{ $order->payment_reference ?? $order->payment_proof_reference ?? '—' }}
                    </span>
                </div>
            @endif

            @if($order->payment_method === 'cash')
                <div class="info-item">
                    <span class="info-label">Amount Paid</span>
                    <span class="info-value">₱{{ number_format($order->amount_paid, 2) }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Change</span>
                    <span class="info-value" style="color: #2E5A3B; font-weight: 700;">
                        ₱{{ number_format($order->change_amount, 2) }}
                    </span>
                </div>
            @endif

            <div class="info-item">
                <span class="info-label">Delivery Fee</span>
                <span class="info-value">₱{{ number_format($order->delivery_fee, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- ORDER ITEMS --}}
    <div class="card">
        <h2 class="card-heading">Order Items</h2>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="num">Quantity</th>
                        <th class="num">Unit Price</th>
                        <th class="num">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                @if($item->product)
                                    <div style="font-weight: 600; color: #212121;">
                                        {{ $item->product->display_name }}
                                    </div>
                                @else
                                    <div style="font-weight: 600; color: #212121; font-style: italic;">
                                        Custom Bouquet
                                    </div>
                                    @if($item->customization_details)
                                        <div style="font-size: 12px; color: #64748B; margin-top: 3px; line-height: 1.4;">
                                            {{ $item->customization_details }}
                                        </div>
                                    @endif
                                @endif
                            </td>

                            <td class="num" style="color: #212121;">
                                {{ (float) $item->quantity }}
                            </td>

                            <td class="num" style="color: #64748B;">
                                ₱{{ number_format($item->unit_price, 2) }}
                            </td>

                            <td class="num" style="font-weight: 700; color: #2E5A3B;">
                                ₱{{ number_format($item->line_total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($order->discount_type && $order->discount_type !== 'none')
            <div class="discount-info">
                <div class="discount-info-title">
                    {{ $order->discount_type === 'pwd' ? 'PWD Discount Applied' : 'Senior Citizen Discount Applied' }}
                </div>
                <div class="discount-info-row">
                    <span>Name:</span>
                    <span>{{ $order->discount_name ?: '—' }}</span>
                </div>
                <div class="discount-info-row">
                    <span>ID No:</span>
                    <span>{{ $order->discount_id_number ?: '—' }}</span>
                </div>
            </div>
        @endif

        <div class="order-summary">
            <div class="summary-line">
                <span>Subtotal</span>
                <strong>₱{{ number_format($order->subtotal, 2) }}</strong>
            </div>

            @if($order->discount_amount > 0)
                <div class="summary-line discount-line">
                    <span>Discount (20%)</span>
                    <strong>− ₱{{ number_format($order->discount_amount, 2) }}</strong>
                </div>
            @endif

            @if($order->fulfillment_type === 'delivery' && $order->delivery_fee > 0)
                <div class="summary-line">
                    <span>Delivery Fee</span>
                    <strong>₱{{ number_format($order->delivery_fee, 2) }}</strong>
                </div>
            @endif

            <div class="summary-line total">
                <span>Total</span>
                <strong>₱{{ number_format($order->total_amount, 2) }}</strong>
            </div>
        </div>
    </div>

    {{-- BOUQUET COMPONENTS (customized only) --}}
    @if($order->order_type === 'customized' && $order->components->count() > 0)
        <div class="card">
            <h2 class="card-heading">Bouquet Components</h2>
            <p style="font-size: 13px; color: #64748B; margin-top: -10px; margin-bottom: 18px;">
                Materials used to build this customized bouquet. Deducted from production stock when the order was recorded.
            </p>

            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th class="num">Quantity Used</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->components as $component)
                            <tr>
                                <td style="font-weight: 600;">{{ $component->product->display_name }}</td>
                                <td class="num" style="font-weight: 600;">{{ (float) $component->quantity }}</td>
                                <td style="color: #64748B;">{{ $component->product->stock_unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

<style>
    .form-page-wide { max-width: 900px; margin: 0 auto; }

    .card { margin-bottom: 20px; }

    .ref-badge {
        font-family: 'SF Mono', Consolas, monospace;
        font-size: 15px;
        color: #94A3B8;
        font-weight: 600;
        margin-left: 8px;
        vertical-align: middle;
    }

    .card-heading {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 18px;
        padding-bottom: 8px;
        border-bottom: 2px solid #EFEBF7;
        display: inline-block;
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
        word-break: break-word;
    }

    .info-value.mono {
        font-family: 'SF Mono', Consolas, monospace;
        font-weight: 700;
        color: #6B5B95;
        letter-spacing: 0.5px;
    }

    .info-value.mono-soft {
        font-family: 'SF Mono', Consolas, monospace;
        font-weight: 700;
        color: #212121;
        letter-spacing: 0.5px;
    }

    .status-text {
        font-size: 14px;
        font-weight: 700;
        text-transform: capitalize;
        cursor: default;
        user-select: none;
    }
    .status-pending { color: #B8860B; }
    .status-completed { color: #2E5A3B; }
    .status-cancelled { color: #DC3545; }

    .status-update {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #F0E6DD;
    }
    .status-form-row {
        display: flex;
        gap: 10px;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .locked-note {
        padding: 14px 18px;
        background: #F1F5F9;
        color: #64748B;
        font-size: 13px;
        border-radius: 10px;
        text-align: center;
    }
    .locked-note strong {
        text-transform: capitalize;
        color: #212121;
    }

    .cancel-note {
        margin-top: 12px;
        padding: 12px 16px;
        background: #FDECEA;
        border-left: 4px solid #DC3545;
        border-radius: 8px;
        font-size: 13px;
        color: #7C2D12;
    }
    .cancel-note strong {
        color: #C0392B;
        margin-right: 4px;
    }

    .discount-info {
        margin-top: 20px;
        padding: 14px 16px;
        background: #FFF8E1;
        border-left: 4px solid #D4AF37;
        border-radius: 8px;
    }
    .discount-info-title {
        font-size: 11px;
        font-weight: 700;
        color: #B8860B;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 10px;
    }
    .discount-info-row {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        font-size: 13px;
    }
    .discount-info-row span:first-child { color: #94A3B8; }
    .discount-info-row span:last-child {
        color: #212121;
        font-weight: 600;
        text-align: right;
        max-width: 65%;
        word-break: break-word;
    }

    .order-summary {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 2px solid #D5C9E8;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .summary-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        max-width: 320px;
        padding: 6px 0;
        font-size: 14px;
        color: #64748B;
    }
    .summary-line strong {
        font-size: 15px;
        color: #212121;
        font-weight: 600;
    }

    .summary-line.discount-line span,
    .summary-line.discount-line strong {
        color: #B8860B;
        font-weight: 600;
    }

    .summary-line.total {
        margin-top: 10px;
        padding-top: 14px;
        border-top: 1px dashed #F0E6DD;
    }
    .summary-line.total span {
        font-size: 15px;
        font-weight: 700;
        color: #212121;
    }
    .summary-line.total strong {
        font-size: 24px;
        color: #6B5B95;
        font-weight: 700;
    }

    table td.num, table th.num {
        text-align: right;
        white-space: nowrap;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.getElementById('order_status');
    const cancelNoteGroup = document.getElementById('cancel-note-group');

    if (statusSelect && cancelNoteGroup) {
        function updateCancelNote() {
            cancelNoteGroup.style.display = statusSelect.value === 'cancelled' ? 'block' : 'none';
        }
        statusSelect.addEventListener('change', updateCancelNote);
        updateCancelNote();
    }
});
</script>

@endsection