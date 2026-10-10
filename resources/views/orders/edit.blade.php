@extends('layouts.app')

@section('title', 'Edit Order ' . $order->reference_code)

@php
    $activeReservations = $order->reservations->where('status', 'active');
@endphp

@section('content')

<div class="form-wrapper">

    <div class="page-header">
        <div>
            <h1>Edit Order</h1>
            <p>
                Editing
                <strong style="color: #6B5B95; font-family: 'SF Mono', Consolas, monospace;">
                    {{ $order->reference_code }}
                </strong>
            </p>
        </div>
        <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">← Back to Order</a>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="edit-note">
        <strong>Note:</strong>
        @if($order->order_type === 'ready_made')
            Items and order type cannot be changed. To change items, cancel this order and create a new one.
        @else
            You can update the required materials below. Reservations will sync automatically on save.
        @endif
    </div>

    <form action="{{ route('orders.update', $order) }}" method="POST" id="order-form" data-order-type="{{ $order->order_type }}">
        @csrf
        @method('PUT')

        {{-- CHANNEL --}}
        <section class="form-card">
            <h2 class="section-title">Sales Channel</h2>

            <div class="radio-group">
                <label class="radio-option">
                    <input type="radio" name="channel" value="online"
                           {{ old('channel', $order->channel) === 'online' ? 'checked' : '' }}>
                    <div>
                        <strong>Online</strong>
                        <span>Order received through Messenger</span>
                    </div>
                </label>
                <label class="radio-option">
                    <input type="radio" name="channel" value="walk_in"
                           {{ old('channel', $order->channel) === 'walk_in' ? 'checked' : '' }}>
                    <div>
                        <strong>Walk-in</strong>
                        <span>Order placed in person at the shop</span>
                    </div>
                </label>
            </div>
        </section>

        {{-- CUSTOMER --}}
        <section class="form-card">
            <h2 class="section-title">Customer</h2>

            <div class="customer-grid">
                <div class="form-group">
                    <label for="customer_select">Registered Customer</label>
                    <select id="customer_select" class="form-control">
                        <option value="">— Walk-in / Select —</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->customer_id }}"
                                    {{ $order->customer_id == $customer->customer_id ? 'selected' : '' }}>
                                {{ $customer->full_name }}
                            </option>
                        @endforeach
                        <option value="__other__" {{ !$order->customer_id && $order->customer_name ? 'selected' : '' }}>
                            + Unregistered Customer
                        </option>
                    </select>
                    <input type="hidden" name="customer_id" id="customer_id_hidden" value="{{ $order->customer_id }}">
                </div>

                <div class="form-group" id="customer-name-group"
                     style="display: {{ !$order->customer_id && $order->customer_name ? 'block' : 'none' }};">
                    <label for="customer_name">Unregistered Customer Name</label>
                    <input type="text" id="customer_name" name="customer_name" class="form-control"
                           value="{{ old('customer_name', $order->customer_name) }}" maxlength="255">
                </div>
            </div>
        </section>

        {{-- RECEIVER --}}
        <section class="form-card">
            <h2 class="section-title">Receiver Information</h2>

            <div class="form-grid-3">
                <div class="form-group">
                    <label for="receiver_first_name">First Name <span class="req">*</span></label>
                    <input type="text" id="receiver_first_name" name="receiver_first_name"
                           class="form-control" value="{{ old('receiver_first_name', $order->receiver_first_name) }}" required>
                </div>
                <div class="form-group">
                    <label for="receiver_middle_name">Middle Name</label>
                    <input type="text" id="receiver_middle_name" name="receiver_middle_name"
                           class="form-control" value="{{ old('receiver_middle_name', $order->receiver_middle_name) }}">
                </div>
                <div class="form-group">
                    <label for="receiver_last_name">Last Name <span class="req">*</span></label>
                    <input type="text" id="receiver_last_name" name="receiver_last_name"
                           class="form-control" value="{{ old('receiver_last_name', $order->receiver_last_name) }}" required>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="receiver_contact">Mobile Number <span class="req">*</span></label>
                    <input type="text" id="receiver_contact" name="receiver_contact"
                           class="form-control" value="{{ old('receiver_contact', $order->receiver_contact) }}"
                           inputmode="numeric" maxlength="11" required>
                </div>
            </div>
        </section>

        {{-- FULFILLMENT --}}
        <section class="form-card">
            <h2 class="section-title">Fulfillment</h2>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="fulfillment_type">Method <span class="req">*</span></label>
                    <select id="fulfillment_type" name="fulfillment_type" class="form-control" required>
                        <option value="pickup" {{ old('fulfillment_type', $order->fulfillment_type) === 'pickup' ? 'selected' : '' }}>Pickup</option>
                        <option value="delivery" {{ old('fulfillment_type', $order->fulfillment_type) === 'delivery' ? 'selected' : '' }}>Delivery</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="delivery_datetime">Date &amp; Time <span class="req">*</span></label>
                    <input type="datetime-local" id="delivery_datetime" name="delivery_datetime"
                           class="form-control"
                           value="{{ old('delivery_datetime', $order->delivery_datetime?->format('Y-m-d\TH:i')) }}" required>
                </div>
            </div>

            <div class="form-group" id="address-group">
                <label for="delivery_address">Delivery Address</label>
                <textarea id="delivery_address" name="delivery_address" class="form-control" rows="2">{{ old('delivery_address', $order->delivery_address) }}</textarea>
            </div>
        </section>

        {{-- CUSTOMIZED COMPONENTS --}}
        @if($order->order_type === 'customized')
            <section class="form-card">
                <h2 class="section-title">Required Materials</h2>
                <p style="font-size: 13px; color: #64748B; margin-top: -10px; margin-bottom: 16px;">
                    Update the materials needed for this customized bouquet. Reservations will sync on save.
                </p>

                <div class="components-card">
                    <div class="components-header">
                        <div>
                            <h3>Component Materials</h3>
                            <p>Materials needed to build this bouquet. Deducted from production stock on completion.</p>
                        </div>
                        <button type="button" class="btn-add-item" id="add-component">+ Add Material</button>
                    </div>

                    @if($materials->isEmpty())
                        <p class="empty-text" style="padding: 30px 20px;">
                            No production materials available.
                        </p>
                    @else
                        <div id="components-list">
                            @forelse($activeReservations as $reservation)
                                <div class="component-row">
                                    <div class="form-group">
                                        <label>Material <span class="req">*</span></label>
                                        <select class="material-select form-control">
                                            <option value="">— Select Material —</option>
                                            @foreach($materials as $material)
                                                <option value="{{ $material->product_id }}"
                                                        data-stock="{{ $material->production_stock }}"
                                                        data-unit="{{ $material->stock_unit }}"
                                                        {{ $reservation->product_id == $material->product_id ? 'selected' : '' }}>
                                                    {{ $material->display_name }}
                                                    — {{ (float) $material->production_stock }} {{ $material->stock_unit }} available
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group qty-group">
                                        <label>Qty <span class="req">*</span></label>
                                        <input type="number" class="component-qty form-control" min="0.01" step="0.01"
                                               value="{{ (float) $reservation->quantity }}">
                                    </div>

                                    <div class="form-group unit-group">
                                        <label>Unit</label>
                                        <input type="text" class="component-unit form-control readonly-field" readonly
                                               value="{{ $reservation->product->stock_unit }}">
                                    </div>

                                    <button type="button" class="remove-row-btn remove-component" title="Remove">×</button>
                                </div>
                            @empty
                                <div class="component-row">
                                    <div class="form-group">
                                        <label>Material <span class="req">*</span></label>
                                        <select class="material-select form-control">
                                            <option value="">— Select Material —</option>
                                            @foreach($materials as $material)
                                                <option value="{{ $material->product_id }}"
                                                        data-stock="{{ $material->production_stock }}"
                                                        data-unit="{{ $material->stock_unit }}">
                                                    {{ $material->display_name }}
                                                    — {{ (float) $material->production_stock }} {{ $material->stock_unit }} available
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group qty-group">
                                        <label>Qty <span class="req">*</span></label>
                                        <input type="number" class="component-qty form-control" min="0.01" step="0.01" value="1">
                                    </div>
                                    <div class="form-group unit-group">
                                        <label>Unit</label>
                                        <input type="text" class="component-unit form-control readonly-field" readonly value="—">
                                    </div>
                                    <button type="button" class="remove-row-btn remove-component" title="Remove">×</button>
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- PAYMENT --}}
        <section class="form-card">
            <h2 class="section-title">Payment</h2>

            <div class="form-group">
                <label for="delivery_fee">Delivery Fee</label>
                <div class="input-with-prefix">
                    <span class="prefix">₱</span>
                    <input type="number" id="delivery_fee" name="delivery_fee"
                           class="form-control" value="{{ old('delivery_fee', $order->delivery_fee) }}"
                           min="0" step="0.01" placeholder="0.00">
                </div>
                <small class="field-help" id="delivery-fee-help">Only applies for delivery orders.</small>
            </div>

            {{-- WALK-IN PAYMENT --}}
            <div id="walkin-payment" style="display: none;">
                <div class="form-group">
                    <label>Payment Method <span class="req">*</span></label>
                    <div class="payment-methods">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cash"
                                   {{ old('payment_method', $order->payment_method) === 'cash' ? 'checked' : '' }}>
                            <span class="payment-label">Cash</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="gcash"
                                   {{ old('payment_method', $order->payment_method) === 'gcash' ? 'checked' : '' }}>
                            <span class="payment-label">GCash</span>
                        </label>
                    </div>
                </div>

                <div id="walkin-cash-section">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="amount_paid">Amount Paid</label>
                            <div class="input-with-prefix">
                                <span class="prefix">₱</span>
                                <input type="number" id="amount_paid" name="amount_paid"
                                       class="form-control" min="0" step="0.01"
                                       value="{{ old('amount_paid', $order->amount_paid) }}" placeholder="0.00">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Change</label>
                            <input type="text" id="change_display" class="form-control readonly-field"
                                   value="₱{{ number_format((float) $order->change_amount, 2) }}" readonly>
                        </div>
                    </div>

                    <div id="change-warning" class="change-warning" style="display: none;">
                        Amount paid is less than the order total.
                    </div>
                </div>

                <div id="walkin-gcash-section" style="display: none;">
                    <div class="form-group">
                        <label for="payment_proof_reference_walkin">GCash Reference Number <span class="req">*</span></label>
                        <input type="text" id="payment_proof_reference_walkin"
                               class="form-control walkin-gcash-ref"
                               inputmode="numeric" maxlength="13"
                               value="{{ old('walkin_gcash_reference', $order->payment_reference) }}"
                               placeholder="13-digit reference number">
                    </div>
                </div>
            </div>

            {{-- ONLINE PAYMENT --}}
            <div id="online-payment">
                <div class="form-group">
                    <label for="payment_proof_reference">GCash Reference Number <span class="req">*</span></label>
                    <input type="text" id="payment_proof_reference" name="payment_proof_reference"
                           class="form-control" inputmode="numeric" maxlength="13"
                           value="{{ old('payment_proof_reference', $order->payment_proof_reference) }}"
                           placeholder="13-digit reference number">
                </div>
            </div>

            <div class="form-group">
                <label>Discount</label>
                <div class="discount-radios">
                    <label class="discount-radio">
                        <input type="radio" name="discount_type" value="none"
                               {{ old('discount_type', $order->discount_type) === 'none' ? 'checked' : '' }}>
                        <span>None</span>
                    </label>
                    <label class="discount-radio">
                        <input type="radio" name="discount_type" value="pwd"
                               {{ old('discount_type', $order->discount_type) === 'pwd' ? 'checked' : '' }}>
                        <span>PWD</span>
                    </label>
                    <label class="discount-radio">
                        <input type="radio" name="discount_type" value="senior"
                               {{ old('discount_type', $order->discount_type) === 'senior' ? 'checked' : '' }}>
                        <span>Senior Citizen</span>
                    </label>
                </div>
            </div>

            <div id="discount-details" style="display: {{ old('discount_type', $order->discount_type) !== 'none' ? 'block' : 'none' }};">
                <div class="discount-detail-box">
                    <div class="form-group">
                        <label for="discount_name">Name on ID <span class="req">*</span></label>
                        <input type="text" id="discount_name" name="discount_name" class="form-control"
                               value="{{ old('discount_name', $order->discount_name) }}" maxlength="120">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="discount_id_number">
                            <span id="discount-id-label">ID Number</span>
                            <span class="req">*</span>
                        </label>
                        <input type="text" id="discount_id_number" name="discount_id_number"
                               class="form-control"
                               value="{{ old('discount_id_number', $order->discount_id_number) }}">
                        <small class="field-help" id="discount-id-hint">—</small>
                    </div>
                </div>
            </div>

            <div id="discount-line" class="discount-line" style="display: {{ $order->discount_amount > 0 ? 'flex' : 'none' }};">
                <span>Discount (20%)</span>
                <strong id="discount-display">− ₱{{ number_format($order->discount_amount, 2) }}</strong>
            </div>

            <div class="total-row">
                <span>Order Total</span>
                <strong id="grand-total">₱{{ number_format($order->total_amount, 2) }}</strong>
            </div>
        </section>

        <div class="form-actions">
            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>

    </form>

</div>

<style>
    .form-wrapper { max-width: 900px; margin: 0 auto; }

    .form-card {
        background: #FFFFFF;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        padding: 24px 28px;
        margin-bottom: 16px;
    }

    .section-title {
        font-size: 15px;
        font-weight: 700;
        color: #212121;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0 0 18px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #EFEBF7;
        display: inline-block;
    }

    .edit-note {
        background: #FFF8E1;
        border-left: 4px solid #D4AF37;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 13px;
        color: #7C5A0E;
        margin-bottom: 20px;
    }

    .customer-grid,
    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px 20px;
    }
    .form-grid-3 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px 20px;
    }

    .req { color: #6B5B95; margin-left: 2px; }
    .field-help { display: block; margin-top: 6px; color: #94A3B8; font-size: 12px; }

    .input-with-prefix { position: relative; }
    .input-with-prefix .prefix {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #94A3B8; font-weight: 600; font-size: 14px; pointer-events: none;
    }
    .input-with-prefix .form-control { padding-left: 32px; }

    .readonly-field {
        background: #FEFCF9 !important;
        color: #64748B;
        cursor: default;
    }

    .radio-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 10px;
        margin-bottom: 0;
    }
    .radio-option {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        border: 1.5px solid #F0E6DD;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #FFFFFF;
    }
    .radio-option:hover { border-color: #D5C9E8; background: #FEFCF9; }
    .radio-option:has(input[type="radio"]:checked) {
        border-color: #6B5B95;
        background: #EFEBF7;
    }
    .radio-option input[type="radio"] { margin-top: 2px; accent-color: #6B5B95; cursor: pointer; }
    .radio-option strong {
        display: block;
        font-size: 14px;
        color: #212121;
        margin-bottom: 2px;
    }
    .radio-option span { font-size: 12px; color: #64748B; line-height: 1.4; }

    .components-card {
        padding: 18px;
        background: #FFF8E1;
        border: 1px solid #FFECB3;
        border-radius: 10px;
    }

    .components-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .components-header h3 {
        font-size: 14px;
        font-weight: 700;
        color: #B8860B;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0 0 4px 0;
    }
    .components-header p {
        font-size: 12px;
        color: #7C5A0E;
        margin: 0;
        line-height: 1.4;
        max-width: 500px;
    }

    .btn-add-item {
        padding: 7px 14px;
        background: #EFEBF7;
        color: #6B5B95;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
    }
    .btn-add-item:hover { background: #D5C9E8; }

    .component-row {
        display: grid;
        grid-template-columns: 1fr 100px 100px 36px;
        gap: 12px;
        align-items: end;
        padding: 12px;
        background: #FFFFFF;
        border: 1px solid #F0E6DD;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    .component-row .form-group { margin-bottom: 0; }

    .remove-row-btn {
        width: 36px;
        height: 38px;
        background: #FDECEA;
        color: #DC3545;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 18px;
        font-weight: 700;
        font-family: inherit;
        line-height: 1;
    }
    .remove-row-btn:hover { background: #F8D7DA; }

    /* PAYMENT METHODS */
    .payment-methods {
        display: flex;
        gap: 8px;
    }
    .payment-option {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        border: 1.5px solid #F0E6DD;
        border-radius: 10px;
        cursor: pointer;
        background: #FFFFFF;
        font-size: 14px;
        font-weight: 600;
        color: #64748B;
        transition: all 0.15s ease;
    }
    .payment-option input[type="radio"] { accent-color: #6B5B95; cursor: pointer; }
    .payment-option:hover { border-color: #D5C9E8; background: #FEFCF9; }
    .payment-option:has(input:checked) {
        border-color: #6B5B95;
        background: #EFEBF7;
        color: #6B5B95;
    }
    .change-warning {
        margin-top: 8px;
        padding: 8px 12px;
        font-size: 12px;
        color: #DC3545;
        background: #FDECEA;
        border-radius: 6px;
        font-weight: 600;
    }

    .discount-radios {
        display: flex;
        gap: 8px;
    }
    .discount-radio {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 8px;
        border: 1.5px solid #F0E6DD;
        border-radius: 10px;
        cursor: pointer;
        background: #FFFFFF;
        font-size: 13px;
        font-weight: 600;
        color: #64748B;
        transition: all 0.15s ease;
    }
    .discount-radio input[type="radio"] { display: none; }
    .discount-radio:hover { border-color: #D5C9E8; background: #FEFCF9; }
    .discount-radio:has(input:checked) {
        border-color: #6B5B95;
        background: #EFEBF7;
        color: #6B5B95;
    }

    .discount-detail-box {
        padding: 14px;
        margin-bottom: 14px;
        background: #FEFCF9;
        border: 1px dashed #F0E6DD;
        border-radius: 10px;
        margin-top: 12px;
    }

    .discount-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        margin-top: 10px;
        background: #FFF8E1;
        border-radius: 8px;
        font-size: 13px;
        color: #B8860B;
        font-weight: 600;
    }
    .discount-line strong { color: #B8860B; font-weight: 700; }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 18px;
        margin-top: 18px;
        border-top: 2px solid #D5C9E8;
    }
    .total-row span {
        font-size: 15px;
        font-weight: 700;
        color: #212121;
    }
    .total-row strong {
        font-size: 26px;
        font-weight: 700;
        color: #6B5B95;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 20px 0;
    }

    .empty-text { text-align: center; color: #94A3B8; font-size: 13px; }

    @media (max-width: 640px) {
        .form-card { padding: 18px; }
        .component-row { grid-template-columns: 1fr 1fr; }
        .component-row .remove-row-btn { grid-column: span 2; justify-self: end; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const customerSelect = document.getElementById('customer_select');
    const customerHidden = document.getElementById('customer_id_hidden');
    const customerNameGroup = document.getElementById('customer-name-group');

    const fulfillmentType = document.getElementById('fulfillment_type');
    const addressGroup = document.getElementById('address-group');
    const deliveryFee = document.getElementById('delivery_fee');
    const deliveryFeeHelp = document.getElementById('delivery-fee-help');

    const componentsList = document.getElementById('components-list');
    const addComponentButton = document.getElementById('add-component');

    const grandTotal = document.getElementById('grand-total');
    const discountLine = document.getElementById('discount-line');
    const discountDisplay = document.getElementById('discount-display');
    const discountDetails = document.getElementById('discount-details');
    const discountIdInput = document.getElementById('discount_id_number');
    const discountIdLabel = document.getElementById('discount-id-label');
    const discountIdHint = document.getElementById('discount-id-hint');

    const walkinPayment = document.getElementById('walkin-payment');
    const onlinePayment = document.getElementById('online-payment');
    const walkinCashSection = document.getElementById('walkin-cash-section');
    const walkinGcashSection = document.getElementById('walkin-gcash-section');
    const amountPaidInput = document.getElementById('amount_paid');
    const changeDisplay = document.getElementById('change_display');
    const changeWarning = document.getElementById('change-warning');
    const walkinGcashRef = document.getElementById('payment_proof_reference_walkin');
    const onlineGcashRef = document.getElementById('payment_proof_reference');

    const form = document.getElementById('order-form');
    const orderType = form.dataset.orderType;

    const subtotal = {{ (float) $order->subtotal }};
    let activeDiscount = '{{ old('discount_type', $order->discount_type) }}';

    /* CUSTOMER */
    function updateCustomerField() {
        const val = customerSelect.value;
        if (val === '__other__') {
            customerNameGroup.style.display = 'block';
            customerHidden.value = '';
        } else {
            customerNameGroup.style.display = 'none';
            customerHidden.value = val;
        }
    }
    customerSelect.addEventListener('change', updateCustomerField);
    updateCustomerField();

    /* FULFILLMENT */
    function updateFulfillment() {
        const type = fulfillmentType.value;

        if (type === 'delivery') {
            addressGroup.style.display = 'block';
            deliveryFee.readOnly = false;
            deliveryFee.classList.remove('readonly-field');
            deliveryFeeHelp.textContent = 'Enter the delivery fee charged.';        } else {
            addressGroup.style.display = 'none';
            deliveryFee.value = '0';
            deliveryFee.readOnly = true;
            deliveryFee.classList.add('readonly-field');
            deliveryFeeHelp.textContent = 'Pickup orders have no delivery fee.';
        }
        updateTotals();
    }
    fulfillmentType.addEventListener('change', updateFulfillment);
    deliveryFee.addEventListener('input', updateTotals);

    /* CHANNEL → PAYMENT */
    function updateChannelPayment() {
        const channel = document.querySelector('input[name="channel"]:checked')?.value;

        if (channel === 'walk_in') {
            walkinPayment.style.display = 'block';
            onlinePayment.style.display = 'none';
            onlineGcashRef.removeAttribute('required');
            onlineGcashRef.removeAttribute('name');
            updateWalkinPaymentMethod();
        } else {
            walkinPayment.style.display = 'none';
            onlinePayment.style.display = 'block';
            onlineGcashRef.setAttribute('required', 'required');
            onlineGcashRef.setAttribute('name', 'payment_proof_reference');
            walkinGcashRef.removeAttribute('name');
            amountPaidInput.removeAttribute('name');
        }
    }

    function updateWalkinPaymentMethod() {
        const method = document.querySelector('input[name="payment_method"]:checked')?.value;

        if (method === 'cash') {
            walkinCashSection.style.display = 'block';
            walkinGcashSection.style.display = 'none';
            walkinGcashRef.removeAttribute('name');
            amountPaidInput.setAttribute('name', 'amount_paid');
            updateChangeDisplay();
        } else {
            walkinCashSection.style.display = 'none';
            walkinGcashSection.style.display = 'block';
            amountPaidInput.removeAttribute('name');
            walkinGcashRef.setAttribute('name', 'payment_proof_reference');
        }
    }

    function updateChangeDisplay() {
        if (!amountPaidInput || !changeDisplay) return;

        const total = parseFloat(grandTotal.textContent.replace(/[^\d.-]/g, '')) || 0;
        const paid = parseFloat(amountPaidInput.value) || 0;
        const change = paid - total;

        if (paid === 0) {
            changeDisplay.value = '₱0.00';
            changeWarning.style.display = 'none';
            return;
        }

        if (change < 0) {
            changeDisplay.value = '₱' + Math.abs(change).toFixed(2);
            changeWarning.style.display = 'block';
        } else {
            changeDisplay.value = '₱' + change.toFixed(2);
            changeWarning.style.display = 'none';
        }
    }

    document.querySelectorAll('input[name="channel"]').forEach(radio => {
        radio.addEventListener('change', updateChannelPayment);
    });
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', updateWalkinPaymentMethod);
    });
    if (amountPaidInput) {
        amountPaidInput.addEventListener('input', updateChangeDisplay);
    }

    /* COMPONENTS */
    function refreshMaterialOptions() {
        const rows = document.querySelectorAll('.component-row');
        if (!rows.length) return;

        const selectedIds = new Set();
        rows.forEach(row => {
            const sel = row.querySelector('.material-select');
            if (sel && sel.value) selectedIds.add(sel.value);
        });

        rows.forEach(row => {
            const sel = row.querySelector('.material-select');
            if (!sel) return;
            const currentValue = sel.value;

            Array.from(sel.options).forEach(opt => {
                if (!opt.value) return;
                if (opt.value === currentValue) {
                    opt.disabled = false;
                } else if (selectedIds.has(opt.value)) {
                    opt.disabled = true;
                } else {
                    opt.disabled = false;
                }
            });
        });
    }

    function hasDuplicateMaterial(row) {
        const sel = row.querySelector('.material-select');
        if (!sel || !sel.value) return false;

        let count = 0;
        document.querySelectorAll('.component-row .material-select').forEach(s => {
            if (s.value === sel.value) count++;
        });
        return count > 1;
    }

    function attachComponentListeners(row) {
        const select = row.querySelector('.material-select');
        const qtyInput = row.querySelector('.component-qty');
        const unitField = row.querySelector('.component-unit');

        if (select) {
            select.addEventListener('change', function () {
                if (hasDuplicateMaterial(row)) {
                    alert('This material is already in the list.');
                    this.value = '';
                    refreshMaterialOptions();
                    return;
                }

                const opt = this.selectedOptions[0];
                if (opt && opt.value) {
                    unitField.value = opt.dataset.unit || '—';
                } else {
                    unitField.value = '—';
                }

                refreshMaterialOptions();
            });
        }

        if (qtyInput) {
            qtyInput.addEventListener('input', function () {
                if (parseFloat(this.value) <= 0) {
                    this.value = 1;
                }
            });
        }

        const removeBtn = row.querySelector('.remove-component');
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                const rows = document.querySelectorAll('.component-row');
                if (rows.length === 1) {
                    row.querySelector('.material-select').value = '';
                    row.querySelector('.component-qty').value = 1;
                    row.querySelector('.component-unit').value = '—';
                    refreshMaterialOptions();
                    return;
                }
                row.remove();
                refreshMaterialOptions();
            });
        }
    }

    if (addComponentButton && componentsList) {
        addComponentButton.addEventListener('click', function () {
            const first = document.querySelector('.component-row');
            if (!first) return;

            const newRow = first.cloneNode(true);
            newRow.querySelector('.material-select').value = '';
            newRow.querySelector('.component-qty').value = 1;
            newRow.querySelector('.component-unit').value = '—';

            componentsList.appendChild(newRow);
            attachComponentListeners(newRow);
            refreshMaterialOptions();
        });

        document.querySelectorAll('.component-row').forEach(attachComponentListeners);
        refreshMaterialOptions();
    }

    /* TOTALS */
    function updateTotals() {
        let discountAmount = 0;

        if (activeDiscount !== 'none' && subtotal > 0) {
            discountAmount = subtotal * 0.20;
            discountLine.style.display = 'flex';
            discountDisplay.textContent = '− ₱' + discountAmount.toFixed(2);
        } else {
            discountLine.style.display = 'none';
        }

        let fee = 0;
        if (fulfillmentType.value === 'delivery') {
            fee = parseFloat(deliveryFee.value || 0);
        }

        const total = subtotal - discountAmount + fee;
        grandTotal.textContent = '₱' + total.toFixed(2);

        updateChangeDisplay();
    }

    /* DISCOUNT */
    document.querySelectorAll('input[name="discount_type"]').forEach(radio => {
        radio.addEventListener('change', function () {
            activeDiscount = this.value;
            handleDiscountChange();
        });
    });

    function handleDiscountChange() {
        if (activeDiscount === 'none') {
            discountDetails.style.display = 'none';
            updateTotals();
            return;
        }

        discountDetails.style.display = 'block';

        if (activeDiscount === 'pwd') {
            discountIdLabel.textContent = 'PWD ID Number';
            discountIdHint.textContent = 'Format: RR-PPMM-BBB-NNNNNNN (16 digits).';
            discountIdInput.setAttribute('maxlength', '19');
        } else {
            discountIdLabel.textContent = 'Senior Citizen ID Number';
            discountIdHint.textContent = 'Formats vary by LGU (e.g. 12345, QC-12345).';
            discountIdInput.setAttribute('maxlength', '30');
        }

        updateTotals();
    }
    handleDiscountChange();

    if (discountIdInput) {
        discountIdInput.addEventListener('input', function () {
            if (activeDiscount === 'pwd') {
                let digits = this.value.replace(/\D/g, '').slice(0, 16);
                let formatted = '';
                for (let i = 0; i < digits.length; i++) {
                    if (i === 2 || i === 6 || i === 9) formatted += '-';
                    formatted += digits[i];
                }
                this.value = formatted;
            } else if (activeDiscount === 'senior') {
                this.value = this.value.replace(/[^A-Za-z0-9\-\/\s]/g, '').toUpperCase().slice(0, 30);
            }
        });
    }

    /* SUBMIT */
    form.addEventListener('submit', function (e) {
        document.querySelectorAll('input[data-generated="1"]').forEach(el => el.remove());

        if (orderType === 'customized') {
            let idx = 0;
            document.querySelectorAll('.component-row').forEach(row => {
                const pid = row.querySelector('.material-select').value;
                const qty = row.querySelector('.component-qty').value;
                if (!pid || !qty) return;

                appendHidden(`components[${idx}][product_id]`, pid);
                appendHidden(`components[${idx}][quantity]`, qty);
                idx++;
            });
        }
    });

    function appendHidden(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        input.setAttribute('data-generated', '1');
        form.appendChild(input);
    }

    /* INIT */
    updateChannelPayment();
    updateFulfillment();
    updateTotals();
});
</script>

@endsection