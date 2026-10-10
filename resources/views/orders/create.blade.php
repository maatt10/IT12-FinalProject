@extends('layouts.app')

@section('title', 'Record Order')

@section('content')

<div class="form-wrapper">

    <div class="page-header">
        <div>
            <h1>Record Order</h1>
            <p>Record a customer order received through Messenger or in-shop.</p>
        </div>
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

    <form action="{{ route('orders.store') }}" method="POST" id="order-form">
        @csrf

        {{-- CHANNEL --}}
        <section class="form-card">
            <h2 class="section-title">Sales Channel</h2>

            <div class="radio-group">
                <label class="radio-option">
                    <input type="radio" name="channel" value="online"
                        {{ old('channel', 'online') === 'online' ? 'checked' : '' }}>
                    <div>
                        <strong>Online</strong>
                        <span>Order received through Messenger</span>
                    </div>
                </label>
                <label class="radio-option">
                    <input type="radio" name="channel" value="walk_in"
                        {{ old('channel') === 'walk_in' ? 'checked' : '' }}>
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
                            {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                            {{ $customer->full_name }}
                        </option>
                        @endforeach
                        <option value="__other__" {{ old('customer_name') ? 'selected' : '' }}>
                            + Unregistered Customer
                        </option>
                    </select>
                    <input type="hidden" name="customer_id" id="customer_id_hidden" value="{{ old('customer_id') }}">
                </div>

                <div class="form-group" id="customer-name-group" style="display: none;">
                    <label for="customer_name">Unregistered Customer Name</label>
                    <input type="text" id="customer_name" name="customer_name" class="form-control"
                        value="{{ old('customer_name') }}" maxlength="255">
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
                        class="form-control" value="{{ old('receiver_first_name') }}" required>
                </div>
                <div class="form-group">
                    <label for="receiver_middle_name">Middle Name</label>
                    <input type="text" id="receiver_middle_name" name="receiver_middle_name"
                        class="form-control" value="{{ old('receiver_middle_name') }}">
                </div>
                <div class="form-group">
                    <label for="receiver_last_name">Last Name <span class="req">*</span></label>
                    <input type="text" id="receiver_last_name" name="receiver_last_name"
                        class="form-control" value="{{ old('receiver_last_name') }}" required>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="receiver_contact">Mobile Number <span class="req">*</span></label>
                    <input type="text" id="receiver_contact" name="receiver_contact"
                        class="form-control" value="{{ old('receiver_contact') }}"
                        inputmode="numeric" maxlength="11" placeholder="09XXXXXXXXX" required>
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
                        <option value="">— Select —</option>
                        <option value="pickup" {{ old('fulfillment_type') === 'pickup' ? 'selected' : '' }}>Pickup</option>
                        <option value="delivery" {{ old('fulfillment_type') === 'delivery' ? 'selected' : '' }}>Delivery</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="delivery_datetime">Date &amp; Time <span class="req">*</span></label>
                    <input type="datetime-local" id="delivery_datetime" name="delivery_datetime"
                        class="form-control" value="{{ old('delivery_datetime') }}" required>
                    <small class="field-help">Store hours: 8:00 AM – 6:00 PM.</small>
                </div>
            </div>

            <div class="form-group" id="address-group" style="display: none;">
                <label for="delivery_address">Delivery Address</label>
                <textarea id="delivery_address" name="delivery_address" class="form-control" rows="2"
                    placeholder="Complete address for delivery">{{ old('delivery_address') }}</textarea>
            </div>
        </section>

        {{-- BOUQUET SELECTION --}}
        <section class="form-card">
            <h2 class="section-title">Bouquet Selection</h2>

            <div class="radio-group">
                <label class="radio-option">
                    <input type="radio" name="order_type" value="ready_made"
                        {{ old('order_type', 'ready_made') === 'ready_made' ? 'checked' : '' }}>
                    <div>
                        <strong>Ready-Made Bouquet</strong>
                        <span>Select from existing bouquets in stock</span>
                    </div>
                </label>
                <label class="radio-option">
                    <input type="radio" name="order_type" value="customized"
                        {{ old('order_type') === 'customized' ? 'checked' : '' }}>
                    <div>
                        <strong>Customized Bouquet</strong>
                        <span>Build a custom arrangement from materials</span>
                    </div>
                </label>
            </div>

            {{-- READY-MADE SECTION --}}
            <div id="ready-made-section">
                <div class="items-header">
                    <span></span>
                    <button type="button" class="btn-add-item" id="add-item">+ Add Bouquet</button>
                </div>

                <div id="order-items">
                    <div class="order-item-row">
                        <div class="bouquet-thumb" data-bouquet-thumb>
                            <span>No image</span>
                        </div>

                        <div class="form-group bouquet-group">
                            <label>Bouquet <span class="req">*</span></label>
                            <select class="product-select form-control">
                                <option value="">— Select Bouquet —</option>
                                @foreach($products as $product)
                                <option value="{{ $product->product_id }}"
                                    data-price="{{ $product->selling_price }}"
                                    data-stock="{{ $product->retail_stock }}"
                                    data-image="{{ $product->image_url }}"
                                    {{ $product->retail_stock <= 0 ? 'disabled' : '' }}>
                                    {{ $product->display_name }}
                                    — {{ (float) $product->retail_stock }} available
                                    — ₱{{ number_format($product->selling_price, 2) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group qty-group">
                            <label>Qty <span class="req">*</span></label>
                            <input type="number" class="quantity-input form-control" min="1" step="1" value="1">
                        </div>

                        <div class="form-group total-group">
                            <label>Line Total</label>
                            <input type="text" class="line-total form-control readonly-field" value="₱0.00" readonly>
                        </div>

                        <button type="button" class="remove-row-btn remove-item" title="Remove">×</button>
                    </div>
                </div>
            </div>

            {{-- CUSTOMIZED SECTION --}}
            <div id="customized-section" style="display: none;">
                <div class="custom-item-card">
                    <div class="form-group">
                        <label for="custom_description">Bouquet Description <span class="req">*</span></label>
                        <textarea id="custom_description" name="custom_description" class="form-control"
                            rows="3" maxlength="1000"
                            placeholder="e.g. 6 red roses with baby's breath, wrapped in kraft paper, red ribbon">{{ old('custom_description') }}</textarea>
                    </div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="custom_quantity">Quantity <span class="req">*</span></label>
                            <input type="number" id="custom_quantity" name="custom_quantity"
                                class="form-control" min="1" step="1"
                                value="{{ old('custom_quantity', 1) }}">
                        </div>

                        <div class="form-group">
                            <label for="custom_unit_price">Price per Bouquet <span class="req">*</span></label>
                            <div class="input-with-prefix">
                                <span class="prefix">₱</span>
                                <input type="number" id="custom_unit_price" name="custom_unit_price"
                                    class="form-control" min="0" step="0.01"
                                    value="{{ old('custom_unit_price') }}" placeholder="0.00">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Subtotal</label>
                            <input type="text" id="custom_subtotal" class="form-control readonly-field" value="₱0.00" readonly>
                        </div>
                    </div>
                </div>

                <div class="components-card">
                    <div class="components-header">
                        <div>
                            <h3>Bouquet Components</h3>
                            <p>Materials needed to build this customized bouquet. Deducted from production stock when the order is recorded.</p>
                        </div>
                        <button type="button" class="btn-add-item" id="add-component">+ Add Material</button>
                    </div>

                    @if($materials->isEmpty())
                    <p class="empty-text" style="padding: 30px 20px;">
                        No production materials available. Add materials to the system first.
                    </p>
                    @else
                    <div id="components-list">
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
                    </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- PAYMENT --}}
        <section class="form-card">
            <h2 class="section-title">Payment</h2>

            <div class="form-group">
                <label for="delivery_fee">Delivery Fee</label>
                <div class="input-with-prefix">
                    <span class="prefix">₱</span>
                    <input type="number" id="delivery_fee" name="delivery_fee"
                        class="form-control" value="{{ old('delivery_fee') }}"
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
                                {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }}>
                            <span class="payment-label">Cash</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="gcash"
                                {{ old('payment_method') === 'gcash' ? 'checked' : '' }}>
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
                                    value="{{ old('amount_paid') }}" placeholder="0.00">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Change</label>
                            <input type="text" id="change_display" class="form-control readonly-field"
                                value="₱0.00" readonly>
                        </div>
                    </div>
                </div>

                <div id="walkin-gcash-section" style="display: none;">
                    <div class="form-group">
                        <label for="payment_proof_reference_walkin">GCash Reference Number <span class="req">*</span></label>
                        <input type="text" id="payment_proof_reference_walkin"
                            class="form-control walkin-gcash-ref"
                            inputmode="numeric" maxlength="13"
                            value="{{ old('walkin_gcash_reference') }}"
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
                        value="{{ old('payment_proof_reference') }}"
                        placeholder="13-digit reference number">
                </div>
            </div>

            <div class="form-group">
                <label>Discount</label>
                <div class="discount-radios">
                    <label class="discount-radio">
                        <input type="radio" name="discount_type" value="none" checked>
                        <span>None</span>
                    </label>
                    <label class="discount-radio">
                        <input type="radio" name="discount_type" value="pwd">
                        <span>PWD</span>
                    </label>
                    <label class="discount-radio">
                        <input type="radio" name="discount_type" value="senior">
                        <span>Senior Citizen</span>
                    </label>
                </div>
            </div>

            <div id="discount-details" style="display: none;">
                <div class="discount-detail-box">
                    <div class="form-group">
                        <label for="discount_name">Name on ID <span class="req">*</span></label>
                        <input type="text" id="discount_name" name="discount_name"
                            class="form-control" maxlength="120"
                            placeholder="Full name as shown on ID">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="discount_id_number">
                            <span id="discount-id-label">ID Number</span>
                            <span class="req">*</span>
                        </label>
                        <input type="text" id="discount_id_number" name="discount_id_number"
                            class="form-control" placeholder="Enter ID number">
                        <small class="field-help" id="discount-id-hint">—</small>
                    </div>
                </div>
            </div>

            <div id="discount-line" class="discount-line" style="display: none;">
                <span>Discount (20%)</span>
                <strong id="discount-display">− ₱0.00</strong>
            </div>

            <div class="total-row">
                <span>Order Total</span>
                <strong id="grand-total">₱0.00</strong>
            </div>
        </section>

        <div class="form-actions">
            <a href="{{ route('dashboard') }}" class="action-btn-secondary">Cancel</a>
            <button type="submit" class="action-btn-primary">Record Order</button>
        </div>

    </form>

</div>

{{-- ============================================
     CONFIRMATION MODAL
     ============================================ --}}
<div class="modal-overlay" id="confirm-modal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Confirm Order</h3>
            <p class="modal-subtitle">Review the details below before recording this order.</p>
        </div>

        <div class="modal-body">
            <div class="modal-section">
                <div class="modal-row">
                    <span class="modal-row-label">Channel</span>
                    <span class="modal-row-value" id="modal-channel">Online</span>
                </div>
                <div class="modal-row">
                    <span class="modal-row-label">Customer</span>
                    <span class="modal-row-value" id="modal-customer">Walk-in Customer</span>
                </div>
                <div class="modal-row">
                    <span class="modal-row-label">Receiver</span>
                    <span class="modal-row-value" id="modal-receiver">—</span>
                </div>
                <div class="modal-row">
                    <span class="modal-row-label">Fulfillment</span>
                    <span class="modal-row-value" id="modal-fulfillment">—</span>
                </div>
                <div class="modal-row">
                    <span class="modal-row-label">Payment Method</span>
                    <span class="modal-row-value" id="modal-payment-method">—</span>
                </div>
            </div>

            <div class="modal-section">
                <div class="modal-section-title">Bouquet</div>
                <div id="modal-items-list" class="modal-items-list"></div>
            </div>

            <div class="modal-section">
                <div class="modal-row">
                    <span class="modal-row-label">Subtotal</span>
                    <span class="modal-row-value" id="modal-subtotal">₱0.00</span>
                </div>
                <div class="modal-row" id="modal-discount-row" style="display: none;">
                    <span class="modal-row-label" id="modal-discount-label">Discount</span>
                    <span class="modal-row-value modal-value-discount" id="modal-discount">− ₱0.00</span>
                </div>
                <div class="modal-row" id="modal-delivery-fee-row" style="display: none;">
                    <span class="modal-row-label">Delivery Fee</span>
                    <span class="modal-row-value" id="modal-delivery-fee">₱0.00</span>
                </div>
                <div class="modal-row modal-row-total">
                    <span class="modal-row-label">Total</span>
                    <span class="modal-row-value modal-value-total" id="modal-total">₱0.00</span>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeConfirmModal()">
                Cancel
            </button>
            <button type="button" class="modal-btn modal-btn-primary" id="modal-confirm-btn">
                Confirm & Record Order
            </button>
        </div>
    </div>
</div>

{{-- ============================================
     ERROR MODAL
     ============================================ --}}
<div class="modal-overlay" id="error-modal">
    <div class="modal-box modal-box-error">
        <div class="modal-error-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h3 class="modal-error-title">Something's not right</h3>
        <p class="modal-error-message" id="error-modal-message"></p>
        <div class="modal-error-footer">
            <button type="button" class="modal-btn modal-btn-primary" onclick="closeErrorModal()">
                Got it
            </button>
        </div>
    </div>
</div>

<style>
    .form-wrapper {
        max-width: 900px;
        margin: 0 auto;
    }

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

    .req {
        color: #6B5B95;
        margin-left: 2px;
    }

    .field-help {
        display: block;
        margin-top: 6px;
        color: #94A3B8;
        font-size: 12px;
    }

    .input-with-prefix {
        position: relative;
    }

    .input-with-prefix .prefix {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        font-weight: 600;
        font-size: 14px;
        pointer-events: none;
    }

    .input-with-prefix .form-control {
        padding-left: 32px;
    }

    .readonly-field {
        background: #FEFCF9 !important;
        color: #64748B;
        cursor: default;
    }

    .radio-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 10px;
        margin-bottom: 20px;
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

    .radio-option:hover {
        border-color: #D5C9E8;
        background: #FEFCF9;
    }

    .radio-option:has(input[type="radio"]:checked) {
        border-color: #6B5B95;
        background: #EFEBF7;
    }

    .radio-option input[type="radio"] {
        margin-top: 2px;
        accent-color: #6B5B95;
        cursor: pointer;
    }

    .radio-option strong {
        display: block;
        font-size: 14px;
        color: #212121;
        margin-bottom: 2px;
    }

    .radio-option span {
        font-size: 12px;
        color: #64748B;
        line-height: 1.4;
    }

    .items-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .items-header span {
        font-size: 13px;
        font-weight: 700;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 1px;
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

    .btn-add-item:hover {
        background: #D5C9E8;
    }

    /* ===============================
       ORDER ITEM ROW (WITH THUMBNAIL)
       =============================== */
    .order-item-row {
        display: grid;
        grid-template-columns: 80px 1fr 90px 120px 36px;
        gap: 12px;
        align-items: end;
        padding: 14px;
        background: #FEFCF9;
        border: 1px solid #F0E6DD;
        border-radius: 10px;
        margin-bottom: 10px;
    }

    .order-item-row .form-group {
        margin-bottom: 0;
    }

    .bouquet-thumb {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        background: #FDFBFF;
        border: 1.5px dashed #D5C9E8;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
        color: #94A3B8;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        text-align: center;
        line-height: 1.3;
        padding: 4px;
    }

    .bouquet-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .bouquet-thumb.has-image {
        border-style: solid;
        border-color: #F0E6DD;
        background: #FFFFFF;
        padding: 0;
    }

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
        align-self: end;
    }

    .remove-row-btn:hover {
        background: #F8D7DA;
    }

    .custom-item-card {
        padding: 18px;
        background: #FEFCF9;
        border: 1px solid #F0E6DD;
        border-radius: 10px;
        margin-bottom: 16px;
    }

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

    .component-row .form-group {
        margin-bottom: 0;
    }

    /* PAYMENT */
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

    .payment-option input[type="radio"] {
        accent-color: #6B5B95;
        cursor: pointer;
    }

    .payment-option:hover {
        border-color: #D5C9E8;
        background: #FEFCF9;
    }

    .payment-option:has(input:checked) {
        border-color: #6B5B95;
        background: #EFEBF7;
        color: #6B5B95;
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

    .discount-radio input[type="radio"] {
        display: none;
    }

    .discount-radio:hover {
        border-color: #D5C9E8;
        background: #FEFCF9;
    }

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

    .discount-line strong {
        color: #B8860B;
        font-weight: 700;
    }

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
        align-items: center;
    }

    .form-actions .action-btn-primary,
    .form-actions .action-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        height: 44px;
        min-height: 44px;
        min-width: 140px;
        padding: 0 22px;
        font-size: 14px;
        font-weight: 600;
        line-height: 1;
        border-radius: 8px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .action-btn-primary {
        background: #6B5B95;
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(107, 91, 149, 0.3);
    }
    .action-btn-primary:hover {
        background: #594B7D;
        box-shadow: 0 4px 10px rgba(107, 91, 149, 0.4);
    }
    .action-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

    .action-btn-secondary {
        background: #F0E6DD;
        color: #212121;
    }
    .action-btn-secondary:hover { background: #E5D5C5; }

    .empty-text {
        text-align: center;
        color: #94A3B8;
        font-size: 13px;
    }

    .shortfall-hint {
        margin-top: 6px;
        padding: 4px 8px;
        background: #FFF8E1;
        color: #B8860B;
        font-size: 11px;
        font-weight: 600;
        border-radius: 4px;
        white-space: nowrap;
        display: inline-block;
    }

    /* MODALS */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(33, 33, 33, 0.55);
        backdrop-filter: blur(3px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fade-in 0.15s ease;
    }
    .modal-overlay.open { display: flex; }

    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-box {
        background: #FFFFFF;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        width: 100%;
        max-width: 520px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: slide-up 0.2s ease;
    }

    @keyframes slide-up {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        padding: 22px 26px 16px;
        border-bottom: 1px solid #F0E6DD;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: #212121;
        margin: 0;
    }

    .modal-subtitle {
        font-size: 13px;
        color: #64748B;
        margin-top: 4px;
    }

    .modal-body {
        padding: 20px 26px;
        overflow-y: auto;
        flex: 1;
        min-height: 0;
    }

    .modal-section { margin-bottom: 20px; }
    .modal-section:last-child { margin-bottom: 0; }

    .modal-section-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94A3B8;
        margin-bottom: 10px;
        padding-bottom: 6px;
        border-bottom: 1px dashed #F0E6DD;
    }

    .modal-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 14px;
    }

    .modal-row-label { color: #64748B; }
    .modal-row-value {
        color: #212121;
        font-weight: 600;
        text-align: right;
    }

    .modal-row-total {
        padding-top: 12px;
        margin-top: 6px;
        border-top: 2px solid #D5C9E8;
    }

    .modal-value-total {
        font-size: 22px;
        font-weight: 700;
        color: #6B5B95;
    }

    .modal-value-discount {
        color: #B8860B;
        font-weight: 700;
    }

    .modal-items-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .modal-item {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 12px;
        padding: 10px 12px;
        background: #FDFBFF;
        border-radius: 8px;
        font-size: 13px;
        align-items: center;
    }

    .modal-item-name {
        color: #212121;
        font-weight: 600;
    }

    .modal-item-total {
        color: #2E5A3B;
        font-weight: 700;
        white-space: nowrap;
        text-align: right;
    }

    .modal-footer {
        padding: 16px 26px 22px;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        border-top: 1px solid #F0E6DD;
        background: #FEFCF9;
    }

    .modal-btn {
        padding: 11px 22px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        border: none;
        transition: all 0.15s ease;
        min-width: 120px;
    }

    .modal-btn-primary {
        background: #6B5B95;
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(107, 91, 149, 0.3);
    }
    .modal-btn-primary:hover { background: #594B7D; }
    .modal-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

    .modal-btn-secondary {
        background: #F0E6DD;
        color: #212121;
    }
    .modal-btn-secondary:hover { background: #E5D5C5; }

    .modal-box-error {
        max-width: 420px;
        text-align: center;
        padding: 30px 30px 24px;
    }

    .modal-error-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #FDECEA;
        color: #DC3545;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }

    .modal-error-title {
        font-size: 18px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 8px;
    }

    .modal-error-message {
        font-size: 14px;
        color: #64748B;
        line-height: 1.5;
        margin-bottom: 24px;
        word-break: break-word;
        white-space: pre-line;
    }

    .modal-error-footer { display: flex; justify-content: center; }
    .modal-error-footer .modal-btn { min-width: 120px; }

    @media (max-width: 720px) {
        .order-item-row {
            grid-template-columns: 80px 1fr;
        }
        .order-item-row .qty-group,
        .order-item-row .total-group {
            grid-column: span 1;
        }
        .order-item-row .remove-row-btn {
            grid-column: span 2;
            justify-self: end;
        }
        .form-card { padding: 18px; }
        .component-row {
            grid-template-columns: 1fr 1fr;
        }
        .component-row .remove-row-btn {
            grid-column: span 2;
            justify-self: end;
        }
        .modal-box { max-width: 100%; }
        .form-actions .action-btn-primary,
        .form-actions .action-btn-secondary { flex: 1; min-width: 0; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ============================================
       ELEMENTS
       ============================================ */
    const customerSelect = document.getElementById('customer_select');
    const customerHidden = document.getElementById('customer_id_hidden');
    const customerNameGroup = document.getElementById('customer-name-group');

    const fulfillmentType = document.getElementById('fulfillment_type');
    const addressGroup = document.getElementById('address-group');
    const deliveryFee = document.getElementById('delivery_fee');
    const deliveryFeeHelp = document.getElementById('delivery-fee-help');

    const orderTypeRadios = document.querySelectorAll('input[name="order_type"]');
    const readyMadeSection = document.getElementById('ready-made-section');
    const customizedSection = document.getElementById('customized-section');

    const orderItems = document.getElementById('order-items');
    const addItemButton = document.getElementById('add-item');

    const componentsList = document.getElementById('components-list');
    const addComponentButton = document.getElementById('add-component');

    const customQty = document.getElementById('custom_quantity');
    const customPrice = document.getElementById('custom_unit_price');
    const customSubtotal = document.getElementById('custom_subtotal');

    const grandTotal = document.getElementById('grand-total');
    const discountLine = document.getElementById('discount-line');
    const discountDisplay = document.getElementById('discount-display');
    const discountDetails = document.getElementById('discount-details');
    const discountNameInput = document.getElementById('discount_name');
    const discountIdInput = document.getElementById('discount_id_number');
    const discountIdLabel = document.getElementById('discount-id-label');
    const discountIdHint = document.getElementById('discount-id-hint');

    const walkinPayment = document.getElementById('walkin-payment');
    const onlinePayment = document.getElementById('online-payment');
    const walkinCashSection = document.getElementById('walkin-cash-section');
    const walkinGcashSection = document.getElementById('walkin-gcash-section');
    const amountPaidInput = document.getElementById('amount_paid');
    const changeDisplay = document.getElementById('change_display');
    const walkinGcashRef = document.getElementById('payment_proof_reference_walkin');
    const onlineGcashRef = document.getElementById('payment_proof_reference');

    const confirmModal = document.getElementById('confirm-modal');
    const errorModal = document.getElementById('error-modal');
    const errorModalMessage = document.getElementById('error-modal-message');
    const modalConfirmBtn = document.getElementById('modal-confirm-btn');

    const form = document.getElementById('order-form');

    let activeDiscount = 'none';

    /* ============================================
       MODAL HELPERS
       ============================================ */
    function showError(message) {
        errorModalMessage.textContent = message;
        errorModal.classList.add('open');
    }
    window.closeErrorModal = function () {
        errorModal.classList.remove('open');
    };
    window.closeConfirmModal = function () {
        confirmModal.classList.remove('open');
    };

    /* ============================================
       CUSTOMER
       ============================================ */
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

    /* ============================================
       FULFILLMENT
       ============================================ */
    function updateFulfillment() {
        const type = fulfillmentType.value;

        if (type === 'delivery') {
            addressGroup.style.display = 'block';
            deliveryFee.readOnly = false;
            deliveryFee.classList.remove('readonly-field');
            deliveryFeeHelp.textContent = 'Enter the delivery fee charged.';
        } else {
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

    /* ============================================
       CHANNEL → PAYMENT
       ============================================ */
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

        if (paid === 0) {
            changeDisplay.value = '₱0.00';
            return;
        }

        const change = paid - total;
        changeDisplay.value = '₱' + Math.abs(change).toFixed(2);
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

    /* ============================================
       ORDER TYPE
       ============================================ */
    orderTypeRadios.forEach(radio => {
        radio.addEventListener('change', updateOrderType);
    });

    function updateOrderType() {
        const type = document.querySelector('input[name="order_type"]:checked')?.value;
        if (type === 'customized') {
            readyMadeSection.style.display = 'none';
            customizedSection.style.display = 'block';
        } else {
            readyMadeSection.style.display = 'block';
            customizedSection.style.display = 'none';
        }
        updateTotals();
    }
    updateOrderType();

    /* ============================================
       READY-MADE — THUMBNAIL UPDATE
       ============================================ */
    function updateBouquetThumb(row) {
        const sel = row.querySelector('.product-select');
        const thumb = row.querySelector('[data-bouquet-thumb]');
        if (!sel || !thumb) return;

        const opt = sel.selectedOptions[0];
        const url = opt?.dataset.image;

        if (url) {
            thumb.innerHTML = `<img src="${url}" alt="">`;
            thumb.classList.add('has-image');
        } else {
            thumb.innerHTML = '<span>No image</span>';
            thumb.classList.remove('has-image');
        }
    }

    /* ============================================
       READY-MADE — ITEM ROWS
       ============================================ */
    function refreshProductOptions() {
        const rows = document.querySelectorAll('.order-item-row');
        if (!rows.length) return;

        const selectedIds = new Set();
        rows.forEach(row => {
            const sel = row.querySelector('.product-select');
            if (sel.value) selectedIds.add(sel.value);
        });

        rows.forEach(row => {
            const sel = row.querySelector('.product-select');
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

    function hasDuplicateSelection(row) {
        const sel = row.querySelector('.product-select');
        if (!sel.value) return false;

        let count = 0;
        document.querySelectorAll('.order-item-row .product-select').forEach(s => {
            if (s.value === sel.value) count++;
        });
        return count > 1;
    }

    function attachItemListeners(row) {
        row.querySelector('.product-select').addEventListener('change', function () {
            if (hasDuplicateSelection(row)) {
                showError('This bouquet is already in the order.');
                this.value = '';
                refreshProductOptions();
                updateTotals();
                updateBouquetThumb(row);
                return;
            }

            const stock = parseInt(this.selectedOptions[0]?.dataset.stock || 0);
            const qtyInput = row.querySelector('.quantity-input');
            qtyInput.max = stock;
            if (parseInt(qtyInput.value) > stock) {
                qtyInput.value = stock;
            }

            refreshProductOptions();
            updateTotals();
            updateBouquetThumb(row);
        });

        row.querySelector('.quantity-input').addEventListener('input', function () {
            const sel = row.querySelector('.product-select');
            const stock = parseInt(sel.selectedOptions[0]?.dataset.stock || 0);
            const qty = parseInt(this.value || 0);

            if (sel.value && qty > stock) {
                showError(`Only ${stock} in stock for this bouquet.`);
                this.value = stock;
            }

            updateTotals();
        });

        row.querySelector('.remove-item').addEventListener('click', function () {
            const rows = document.querySelectorAll('.order-item-row');
            if (rows.length === 1) {
                row.querySelector('.product-select').value = '';
                row.querySelector('.quantity-input').value = 1;
                row.querySelector('.line-total').value = '₱0.00';
                refreshProductOptions();
                updateTotals();
                updateBouquetThumb(row);
                return;
            }
            row.remove();
            refreshProductOptions();
            updateTotals();
        });
    }

    if (addItemButton && orderItems) {
        addItemButton.addEventListener('click', function () {
            const first = document.querySelector('.order-item-row');
            if (!first) return;

            const newRow = first.cloneNode(true);
            newRow.querySelector('.product-select').value = '';
            newRow.querySelector('.quantity-input').value = 1;
            newRow.querySelector('.quantity-input').removeAttribute('max');
            newRow.querySelector('.line-total').value = '₱0.00';

            const newThumb = newRow.querySelector('[data-bouquet-thumb]');
            if (newThumb) {
                newThumb.innerHTML = '<span>No image</span>';
                newThumb.classList.remove('has-image');
            }

            orderItems.appendChild(newRow);
            attachItemListeners(newRow);
            refreshProductOptions();
            updateTotals();
        });

        document.querySelectorAll('.order-item-row').forEach(attachItemListeners);
        refreshProductOptions();
    }

    /* ============================================
       COMPONENTS
       ============================================ */
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

    function updateShortfallHint(row) {
        const select = row.querySelector('.material-select');
        const qtyInput = row.querySelector('.component-qty');
        const qtyGroup = row.querySelector('.qty-group');

        if (!select || !qtyInput || !qtyGroup) return;

        const existing = qtyGroup.querySelector('.shortfall-hint');
        if (existing) existing.remove();

        const opt = select.selectedOptions[0];
        if (!opt || !opt.value) return;

        const available = parseFloat(opt.dataset.stock || 0);
        const requested = parseFloat(qtyInput.value || 0);
        const shortfall = requested - available;

        if (shortfall > 0) {
            const hint = document.createElement('div');
            hint.className = 'shortfall-hint';
            hint.textContent = `⚠ ${shortfall} ${opt.dataset.unit || 'unit'} to purchase`;
            qtyGroup.appendChild(hint);
        }
    }

    function attachComponentListeners(row) {
        const select = row.querySelector('.material-select');
        const qtyInput = row.querySelector('.component-qty');
        const unitField = row.querySelector('.component-unit');

        if (select) {
            select.addEventListener('change', function () {
                if (hasDuplicateMaterial(row)) {
                    showError('This material is already in the list.');
                    this.value = '';
                    refreshMaterialOptions();
                    updateShortfallHint(row);
                    return;
                }

                const opt = this.selectedOptions[0];
                if (opt && opt.value) {
                    unitField.value = opt.dataset.unit || '—';
                } else {
                    unitField.value = '—';
                }

                refreshMaterialOptions();
                updateShortfallHint(row);
            });
        }

        if (qtyInput) {
            qtyInput.addEventListener('input', function () {
                if (parseFloat(this.value) <= 0) {
                    this.value = 1;
                }
                updateShortfallHint(row);
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
                    updateShortfallHint(row);
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
        document.querySelectorAll('.component-row').forEach(updateShortfallHint);
    }

    /* ============================================
       TOTALS
       ============================================ */
    function updateTotals() {
        let subtotal = 0;
        const type = document.querySelector('input[name="order_type"]:checked')?.value;

        if (type === 'ready_made') {
            document.querySelectorAll('.order-item-row').forEach(row => {
                const sel = row.querySelector('.product-select');
                const qty = parseFloat(row.querySelector('.quantity-input').value || 0);
                const price = parseFloat(sel.selectedOptions[0]?.dataset.price || 0);
                const line = qty * price;
                row.querySelector('.line-total').value = '₱' + line.toFixed(2);
                subtotal += line;
            });
        } else {
            const qty = parseFloat(customQty.value || 0);
            const price = parseFloat(customPrice.value || 0);
            const line = qty * price;
            customSubtotal.value = '₱' + line.toFixed(2);
            subtotal += line;
        }

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

    if (customQty) customQty.addEventListener('input', updateTotals);
    if (customPrice) customPrice.addEventListener('input', updateTotals);

    /* ============================================
       DISCOUNT
       ============================================ */
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

    /* ============================================
       CONFIRM MODAL POPULATE
       ============================================ */
    function showConfirmModal() {
        const channel = document.querySelector('input[name="channel"]:checked')?.value || 'online';
        const orderType = document.querySelector('input[name="order_type"]:checked')?.value;

        document.getElementById('modal-channel').textContent =
            channel === 'walk_in' ? 'Walk-in' : 'Online';

        let customerText = 'Walk-in Customer';
        if (customerSelect.value === '__other__') {
            customerText = document.getElementById('customer_name').value.trim() || 'Unregistered Customer';
        } else if (customerSelect.value) {
            customerText = customerSelect.options[customerSelect.selectedIndex].textContent.trim();
        }
        document.getElementById('modal-customer').textContent = customerText;

        const fn = document.getElementById('receiver_first_name').value.trim();
        const ln = document.getElementById('receiver_last_name').value.trim();
        document.getElementById('modal-receiver').textContent = fn || ln ? `${fn} ${ln}`.trim() : '—';

        const fulfillment = fulfillmentType.value === 'delivery' ? 'Delivery' : 'Pickup';
        document.getElementById('modal-fulfillment').textContent = fulfillment;

        let paymentMethodText = 'GCash (Online)';
        if (channel === 'walk_in') {
            const pm = document.querySelector('input[name="payment_method"]:checked')?.value;
            paymentMethodText = pm === 'cash' ? 'Cash' : 'GCash';
        }
        document.getElementById('modal-payment-method').textContent = paymentMethodText;

        const itemsList = document.getElementById('modal-items-list');
        itemsList.innerHTML = '';

        let subtotal = 0;

        if (orderType === 'ready_made') {
            document.querySelectorAll('.order-item-row').forEach(row => {
                const sel = row.querySelector('.product-select');
                const qty = parseFloat(row.querySelector('.quantity-input').value || 0);
                const price = parseFloat(sel.selectedOptions[0]?.dataset.price || 0);
                const line = qty * price;

                if (sel.value && qty > 0) {
                    subtotal += line;
                    const item = document.createElement('div');
                    item.className = 'modal-item';
                    item.innerHTML = `
                        <span class="modal-item-name">${sel.selectedOptions[0].textContent.split('—')[0].trim()} <span style="color:#64748B;font-weight:500;">× ${qty}</span></span>
                        <span class="modal-item-total">₱${line.toFixed(2)}</span>
                    `;
                    itemsList.appendChild(item);
                }
            });
        } else {
            const qty = parseFloat(customQty.value || 0);
            const price = parseFloat(customPrice.value || 0);
            subtotal = qty * price;
            const desc = document.getElementById('custom_description').value.trim() || 'Custom Bouquet';

            const item = document.createElement('div');
            item.className = 'modal-item';
            item.innerHTML = `
                <span class="modal-item-name">${desc.substring(0, 60)}${desc.length > 60 ? '...' : ''} <span style="color:#64748B;font-weight:500;">× ${qty}</span></span>
                <span class="modal-item-total">₱${subtotal.toFixed(2)}</span>
            `;
            itemsList.appendChild(item);

            const components = [];
            document.querySelectorAll('.component-row').forEach(row => {
                const sel = row.querySelector('.material-select');
                const qty = parseFloat(row.querySelector('.component-qty').value || 0);
                if (sel.value && qty > 0) {
                    components.push({
                        name: sel.selectedOptions[0].textContent.split('—')[0].trim(),
                        qty: qty,
                        unit: sel.selectedOptions[0].dataset.unit || ''
                    });
                }
            });

            if (components.length > 0) {
                const compHeader = document.createElement('div');
                compHeader.style.marginTop = '12px';
                compHeader.style.fontSize = '11px';
                compHeader.style.fontWeight = '700';
                compHeader.style.textTransform = 'uppercase';
                compHeader.style.letterSpacing = '1px';
                compHeader.style.color = '#94A3B8';
                compHeader.textContent = 'Components';
                itemsList.appendChild(compHeader);

                components.forEach(c => {
                    const item = document.createElement('div');
                    item.className = 'modal-item';
                    item.innerHTML = `
                        <span class="modal-item-name" style="font-size:12px;">${c.name}</span>
                        <span class="modal-item-total" style="font-size:12px;color:#64748B;">${c.qty} ${c.unit}</span>
                    `;
                    itemsList.appendChild(item);
                });
            }
        }

        document.getElementById('modal-subtotal').textContent = '₱' + subtotal.toFixed(2);

        const discountRow = document.getElementById('modal-discount-row');
        if (activeDiscount !== 'none' && subtotal > 0) {
            const discountAmount = subtotal * 0.20;
            discountRow.style.display = 'flex';
            document.getElementById('modal-discount-label').textContent =
                activeDiscount === 'pwd' ? 'PWD Discount (20%)' : 'Senior Discount (20%)';
            document.getElementById('modal-discount').textContent = '− ₱' + discountAmount.toFixed(2);
        } else {
            discountRow.style.display = 'none';
        }

        const feeRow = document.getElementById('modal-delivery-fee-row');
        const fee = fulfillmentType.value === 'delivery' ? parseFloat(deliveryFee.value || 0) : 0;
        if (fee > 0) {
            feeRow.style.display = 'flex';
            document.getElementById('modal-delivery-fee').textContent = '₱' + fee.toFixed(2);
        } else {
            feeRow.style.display = 'none';
        }

        const total = parseFloat(grandTotal.textContent.replace(/[^\d.-]/g, '')) || 0;
        document.getElementById('modal-total').textContent = '₱' + total.toFixed(2);

        confirmModal.classList.add('open');
    }

    /* ============================================
       SUBMIT — validate → show confirm modal
       ============================================ */
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const channel = document.querySelector('input[name="channel"]:checked')?.value;
        const type = document.querySelector('input[name="order_type"]:checked')?.value;

        if (!channel) {
            showError('Please select a sales channel.');
            return;
        }

        if (!document.getElementById('receiver_first_name').value.trim() ||
            !document.getElementById('receiver_last_name').value.trim()) {
            showError('Please fill in the receiver name.');
            return;
        }

        const contact = document.getElementById('receiver_contact').value.trim();
        if (!/^09\d{9}$/.test(contact)) {
            showError('Mobile number must be 11 digits and start with 09 (e.g. 09171234567).');
            return;
        }

        if (!fulfillmentType.value) {
            showError('Please select a fulfillment method.');
            return;
        }

        if (!document.getElementById('delivery_datetime').value) {
            showError('Please set the delivery or pickup date and time.');
            return;
        }

        if (type === 'ready_made') {
            let hasItem = false;
            document.querySelectorAll('.order-item-row').forEach(row => {
                if (row.querySelector('.product-select').value) hasItem = true;
            });
            if (!hasItem) {
                showError('Please select at least one bouquet.');
                return;
            }
        } else {
            if (!document.getElementById('custom_description').value.trim()) {
                showError('Please enter a bouquet description.');
                return;
            }
            const price = parseFloat(customPrice.value || 0);
            if (price <= 0) {
                showError('Please enter the price for the customized bouquet.');
                return;
            }
            let hasComponent = false;
            document.querySelectorAll('.component-row').forEach(row => {
                if (row.querySelector('.material-select').value) hasComponent = true;
            });
            if (!hasComponent) {
                showError('Please add at least one component material for the customized bouquet.');
                return;
            }
        }

        if (channel === 'walk_in') {
            const pm = document.querySelector('input[name="payment_method"]:checked')?.value;
            if (pm === 'cash') {
                const total = parseFloat(grandTotal.textContent.replace(/[^\d.-]/g, '')) || 0;
                const paid = parseFloat(amountPaidInput.value) || 0;
                if (paid < total) {
                    const short = total - paid;
                    showError(`Amount paid is less than the order total.\n\nTotal: ₱${total.toFixed(2)}\nReceived: ₱${paid.toFixed(2)}\nShort by: ₱${short.toFixed(2)}`);
                    amountPaidInput.focus();
                    return;
                }
            } else {
                const ref = walkinGcashRef.value.trim();
                if (!/^\d{13}$/.test(ref)) {
                    showError('GCash reference must be exactly 13 digits.');
                    walkinGcashRef.focus();
                    return;
                }
            }
        } else {
            const ref = onlineGcashRef.value.trim();
            if (!/^\d{13}$/.test(ref)) {
                showError('GCash reference must be exactly 13 digits.');
                onlineGcashRef.focus();
                return;
            }
        }

        if (activeDiscount === 'pwd') {
            const name = discountNameInput.value.trim();
            const id = discountIdInput.value.replace(/\D/g, '');
            if (!name) {
                showError('Please enter the name on the PWD ID.');
                discountNameInput.focus();
                return;
            }
            if (id.length !== 16) {
                showError('PWD ID must contain exactly 16 digits.');
                discountIdInput.focus();
                return;
            }
        }

        if (activeDiscount === 'senior') {
            const name = discountNameInput.value.trim();
            const id = discountIdInput.value.trim();
            if (!name) {
                showError('Please enter the name on the Senior Citizen ID.');
                discountNameInput.focus();
                return;
            }
            if (id.length < 4) {
                showError('Senior Citizen ID must be at least 4 characters.');
                discountIdInput.focus();
                return;
            }
        }

        showConfirmModal();
    });

    /* ============================================
       CONFIRM → ACTUALLY SUBMIT
       ============================================ */
    modalConfirmBtn.addEventListener('click', function () {
        closeConfirmModal();

        const type = document.querySelector('input[name="order_type"]:checked')?.value;

        document.querySelectorAll('input[data-generated="1"]').forEach(el => el.remove());

        if (type === 'ready_made') {
            let idx = 0;
            document.querySelectorAll('.order-item-row').forEach(row => {
                const pid = row.querySelector('.product-select').value;
                const qty = row.querySelector('.quantity-input').value;
                if (!pid || !qty) return;

                appendHidden(`items[${idx}][product_id]`, pid);
                appendHidden(`items[${idx}][quantity]`, qty);
                idx++;
            });
        } else {
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

        modalConfirmBtn.disabled = true;
        modalConfirmBtn.textContent = 'Recording...';

        form.submit();
    });

    function appendHidden(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        input.setAttribute('data-generated', '1');
        form.appendChild(input);
    }

    /* ============================================
       MODAL CLOSE HANDLERS
       ============================================ */
    confirmModal.addEventListener('click', function (e) {
        if (e.target === confirmModal) closeConfirmModal();
    });
    errorModal.addEventListener('click', function (e) {
        if (e.target === errorModal) closeErrorModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeConfirmModal();
            closeErrorModal();
        }
    });

    /* ============================================
       INIT
       ============================================ */
    updateChannelPayment();
    updateFulfillment();
    updateTotals();
});
</script>

@endsection