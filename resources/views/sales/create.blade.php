@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1>Sales / POS</h1>
        <p>Process walk-in and customer sales.</p>
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

<div class="pos-layout">

    {{-- ============================================
         LEFT COLUMN: Products + Customer
         ============================================ --}}
    <div class="pos-left">

        {{-- PRODUCTS CARD --}}
        <div class="card">
            <h2 class="card-heading">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Products
            </h2>

            {{-- Search bar (cafe POS style) --}}
            <div class="pos-search">
                <div class="pos-search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    id="product-search"
                    class="pos-search-input"
                    placeholder="Search products..."
                    autocomplete="off">
            </div>

            {{-- Product grid --}}
            @if($products->count() > 0)
            <div class="product-grid" id="product-grid">
                @foreach($products as $product)
                <button
                    type="button"
                    class="product-card"
                    data-id="{{ $product->product_id }}"
                    data-name="{{ $product->display_name }}"
                    data-price="{{ $product->selling_price }}"
                    data-unit="{{ $product->stock_unit }}"
                    data-search="{{ strtolower($product->display_name) }}"
                    onclick="addProductToCart(this)">
                    <div class="product-card-name">{{ $product->display_name }}</div>
                    <div class="product-card-price">
                        ₱{{ number_format($product->selling_price, 2) }}
                    </div>
                    <div class="product-card-unit">per {{ $product->stock_unit }}</div>
                </button>
                @endforeach
            </div>
            <div id="no-results" class="no-results" style="display: none;">
                No products match your search.
            </div>
            @else
            <div class="empty-state" style="padding: 40px 20px;">
                <p>No sellable products available.</p>
            </div>
            @endif
        </div>

        {{-- CUSTOMER CARD --}}
        <div class="card">
            <h2 class="card-heading">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Customer
            </h2>

            <div class="form-group">
                <select id="customer_id" class="form-control">
                    <option value="">Walk-in Customer</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->customer_id }}">
                        {{ $customer->last_name }}, {{ $customer->first_name }}
                    </option>
                    @endforeach
                </select>
                <small style="color: #94A3B8; font-size: 12px;">
                    Customer selection is optional.
                </small>
            </div>
        </div>

    </div>

    {{-- ============================================
         RIGHT COLUMN: Cart + Summary + Payment
         ============================================ --}}
    <div class="pos-right">

        {{-- CART CARD --}}
        <div class="card pos-cart-card">
            <h2 class="card-heading">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Cart
                <span id="cart-count" class="cart-count">0</span>
            </h2>

            <div id="cart-items" class="cart-items">
                <div class="cart-empty">
                    No products added yet.
                </div>
            </div>

            <div class="cart-subtotal-row">
                <span>Subtotal</span>
                <strong id="subtotal">₱0.00</strong>
            </div>
        </div>

        {{-- SUMMARY CARD --}}
        <div class="card">
            <h2 class="card-heading">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Summary
            </h2>

            <div class="form-group">
                <label>Subtotal</label>
                <input type="text" id="summary-subtotal" class="form-control readonly-field" value="₱0.00" readonly>
            </div>

            <div class="form-group">
                <label for="discount_amount">Discount Amount</label>
                <input
                    type="number"
                    id="discount_amount"
                    class="form-control"
                    min="0"
                    step="0.01"
                    value="0"
                    placeholder="0.00">
                <small style="color: #94A3B8; font-size: 12px;">
                    Enter the applicable discount amount, if any.
                </small>
            </div>

            <div class="summary-total-row">
                <span>Total Amount</span>
                <strong id="total_amount" class="total-display">₱0.00</strong>
            </div>

            {{-- CASH-ONLY: Money Received + Change --}}
            <div id="cash-section" style="margin-top: 20px; padding-top: 18px; border-top: 1px dashed #F0E6DD;">

                <div class="form-group">
                    <label for="money_received">Money Received</label>
                    <div class="input-with-prefix">
                        <span class="prefix">₱</span>
                        <input
                            type="number"
                            id="money_received"
                            class="form-control"
                            min="0"
                            step="0.01"
                            value=""
                            placeholder="0.00">
                    </div>
                </div>

                <div class="change-row" id="change-row">
                    <span>Change</span>
                    <strong id="change_amount" class="change-display">₱0.00</strong>
                </div>

                <div id="change-warning" class="change-warning" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Money received is less than the total amount.
                </div>

            </div>
        </div>

        {{-- PAYMENT CARD --}}
        <div class="card">
            <h2 class="card-heading">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2E5A3B" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Payment
            </h2>

            <div class="form-group">
                <label>Payment Method</label>
                <div class="payment-methods">
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="cash" checked>
                        <span class="payment-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Cash
                        </span>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="gcash">
                        <span class="payment-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            GCash
                        </span>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="bank_transfer">
                        <span class="payment-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                            Bank Transfer
                        </span>
                    </label>
                </div>
            </div>

            {{-- RECEIPT TOGGLE --}}
            <div class="form-group">
                <label class="toggle-option" for="receipt_issued">
                    <div class="toggle-info">
                        <div class="toggle-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="toggle-title">Issue Receipt</div>
                            <div class="toggle-desc">Print or send receipt to customer</div>
                        </div>
                    </div>
                    <span class="toggle-switch">
                        <input type="checkbox" id="receipt_issued">
                        <span class="toggle-slider"></span>
                    </span>
                </label>
            </div>

            <form id="sale-form" action="{{ route('sales.store') }}" method="POST">
                @csrf
                <input type="hidden" name="customer_id" id="form-customer-id">
                <input type="hidden" name="discount_amount" id="form-discount" value="0">
                <input type="hidden" name="payment_method" id="form-payment-method" value="cash">
                <input type="hidden" name="receipt_issued" id="form-receipt-issued" value="0">
                <div id="cart-inputs"></div>

                <button type="submit" class="btn btn-primary btn-block" id="process-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Process Sale
                </button>
            </form>
        </div>

    </div>

</div>

<style>
    /* ===============================
       POS LAYOUT
       =============================== */
    .pos-layout {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 20px;
        align-items: start;
    }

    .pos-left {
        display: flex;
        flex-direction: column;
        gap: 20px;
        min-width: 0;
    }

    .pos-right {
        display: flex;
        flex-direction: column;
        gap: 20px;
        position: sticky;
        top: 20px;
    }

    /* ===============================
       CARD HEADINGS
       =============================== */
    .card-heading {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ===============================
       SEARCH BAR (cafe POS style)
       =============================== */
    .pos-search {
        position: relative;
        margin-bottom: 20px;
    }

    .pos-search-icon {
        position: absolute;
        left: 8px;
        top: 50%;
        transform: translateY(-50%);
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #E85D75;
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        box-shadow: 0 2px 6px rgba(232, 93, 117, 0.3);
    }

    .pos-search-input {
        width: 100%;
        height: 54px;
        padding: 0 20px 0 60px;
        border: 1.5px solid #F0E6DD;
        border-radius: 27px;
        font-size: 15px;
        background: #FFFFFF;
        color: #212121;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .pos-search-input:focus {
        outline: none;
        border-color: #E85D75;
        box-shadow: 0 0 0 4px rgba(232, 93, 117, 0.1);
    }

    .pos-search-input::placeholder {
        color: #B0A99F;
    }

    /* ===============================
       PRODUCT GRID
       =============================== */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 12px;
        max-height: 520px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .product-card {
        background: #FFFFFF;
        border: 1.5px solid #F0E6DD;
        border-radius: 12px;
        padding: 14px;
        text-align: left;
        cursor: pointer;
        transition: all 0.15s ease;
        font-family: inherit;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .product-card:hover {
        border-color: #E85D75;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(232, 93, 117, 0.15);
    }

    .product-card:active {
        transform: translateY(0);
    }

    .product-card-name {
        font-size: 13px;
        font-weight: 600;
        color: #212121;
        line-height: 1.3;
        margin-bottom: 4px;
    }

    .product-card-price {
        font-size: 16px;
        font-weight: 700;
        color: #E85D75;
    }

    .product-card-unit {
        font-size: 11px;
        color: #94A3B8;
        font-weight: 500;
    }

    .no-results {
        text-align: center;
        padding: 40px 20px;
        color: #94A3B8;
        font-size: 14px;
    }

    /* ===============================
       CART
       =============================== */
    .cart-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 22px;
        padding: 0 7px;
        background: #E85D75;
        color: #FFFFFF;
        border-radius: 11px;
        font-size: 11px;
        font-weight: 700;
        margin-left: 4px;
    }

    .cart-items {
        max-height: 340px;
        overflow-y: auto;
        margin-bottom: 14px;
    }

    .cart-empty {
        text-align: center;
        padding: 30px 20px;
        color: #94A3B8;
        font-size: 13px;
        background: #FEFCF9;
        border-radius: 10px;
    }

    .cart-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 12px;
        background: #FEFCF9;
        border-radius: 10px;
        margin-bottom: 10px;
        border: 1px solid #F5EEE4;
    }

    .cart-item-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 8px;
    }

    .cart-item-name {
        font-size: 13px;
        font-weight: 600;
        color: #212121;
        line-height: 1.3;
    }

    .cart-item-remove {
        background: transparent;
        border: none;
        color: #94A3B8;
        cursor: pointer;
        padding: 2px;
        border-radius: 4px;
        transition: all 0.15s ease;
        flex-shrink: 0;
    }

    .cart-item-remove:hover {
        color: #DC3545;
        background: #FDECEA;
    }

    .cart-item-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .qty-controls {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #FFFFFF;
        border: 1px solid #F0E6DD;
        border-radius: 8px;
        padding: 2px;
    }

    .qty-btn {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        background: transparent;
        border: none;
        cursor: pointer;
        font-weight: 700;
        font-size: 14px;
        color: #64748B;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
        font-family: inherit;
    }

    .qty-btn:hover {
        background: #FCE4EC;
        color: #E85D75;
    }

    .qty-value {
        min-width: 32px;
        text-align: center;
        font-size: 13px;
        font-weight: 600;
        color: #212121;
        cursor: default;
        user-select: none;
    }

    .cart-item-total {
        font-size: 14px;
        font-weight: 700;
        color: #2E5A3B;
    }

    .cart-subtotal-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 14px;
        border-top: 1px solid #F0E6DD;
        font-size: 14px;
        color: #64748B;
    }

    .cart-subtotal-row strong {
        font-size: 18px;
        color: #212121;
        font-weight: 700;
    }

    /* ===============================
       SUMMARY
       =============================== */
    .readonly-field {
        background: #FEFCF9 !important;
        color: #64748B;
        cursor: default;
    }

    .summary-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        margin-top: 6px;
        border-top: 2px solid #F8BBD0;
    }

    .summary-total-row span {
        font-size: 15px;
        font-weight: 600;
        color: #212121;
    }

    .total-display {
        font-size: 22px;
        font-weight: 700;
        color: #E85D75;
        cursor: default;
        user-select: none;
    }

    /* ===============================
       CASH CHANGE SECTION
       =============================== */
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

    .change-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 14px;
        background: #E8F5E9;
        border-radius: 10px;
        margin-top: 6px;
        transition: all 0.2s ease;
    }

    .change-row span {
        font-size: 14px;
        font-weight: 600;
        color: #2E5A3B;
    }

    .change-display {
        font-size: 20px;
        font-weight: 700;
        color: #2E5A3B;
        cursor: default;
        user-select: none;
    }

    .change-row.insufficient {
        background: #FDECEA;
    }

    .change-row.insufficient span,
    .change-row.insufficient .change-display {
        color: #DC3545;
    }

    .change-warning {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        font-size: 12px;
        color: #DC3545;
        font-weight: 500;
    }

    /* ===============================
       PAYMENT METHODS
       =============================== */
    .payment-methods {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .payment-option {
        display: flex;
        align-items: center;
        padding: 10px 14px;
        border: 1.5px solid #F0E6DD;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #FFFFFF;
    }

    .payment-option:hover {
        border-color: #F8BBD0;
        background: #FEFCF9;
    }

    .payment-option input[type="radio"] {
        margin: 0 10px 0 0;
        accent-color: #E85D75;
        cursor: pointer;
    }

    .payment-option:has(input:checked) {
        border-color: #E85D75;
        background: #FCE4EC;
    }

    .payment-option:has(input:checked) .payment-label {
        color: #E85D75;
        font-weight: 600;
    }

    .payment-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #212121;
        font-weight: 500;
    }

    /* ===============================
       RECEIPT TOGGLE
       =============================== */
    .toggle-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border: 1.5px solid #F0E6DD;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #FFFFFF;
    }

    .toggle-option:hover {
        border-color: #F8BBD0;
        background: #FEFCF9;
    }

    .toggle-option:has(input:checked) {
        border-color: #E85D75;
        background: #FCE4EC;
    }

    .toggle-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .toggle-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #FCE4EC;
        color: #E85D75;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.15s ease;
    }

    .toggle-option:has(input:checked) .toggle-icon {
        background: #E85D75;
        color: #FFFFFF;
    }

    .toggle-title {
        font-size: 13px;
        font-weight: 600;
        color: #212121;
    }

    .toggle-desc {
        font-size: 11px;
        color: #94A3B8;
        margin-top: 1px;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
        flex-shrink: 0;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #E2E8F0;
        transition: 0.2s;
        border-radius: 22px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.2s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
    }

    .toggle-switch input:checked+.toggle-slider {
        background-color: #E85D75;
    }

    .toggle-switch input:checked+.toggle-slider:before {
        transform: translateX(18px);
    }

    /* Full-width button */
    .btn-block {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px;
        font-size: 15px;
        font-weight: 600;
        margin-top: 8px;
    }

    /* ===============================
       RESPONSIVE
       =============================== */
    @media (max-width: 1024px) {
        .pos-layout {
            grid-template-columns: 1fr;
        }

        .pos-right {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 10px;
        }

        .product-card {
            padding: 12px;
        }

        .product-card-name {
            font-size: 12px;
        }

        .product-card-price {
            font-size: 14px;
        }
    }
</style>

<script>
    /* ============================================
       ELEMENT REFERENCES
       ============================================ */
    const summarySubtotal = document.getElementById('summary-subtotal');
    const discountInput = document.getElementById('discount_amount');
    const totalAmount = document.getElementById('total_amount');
    const cartItemsEl = document.getElementById('cart-items');
    const subtotalDisplay = document.getElementById('subtotal');
    const cartCountEl = document.getElementById('cart-count');
    const searchInput = document.getElementById('product-search');
    const productGrid = document.getElementById('product-grid');
    const noResults = document.getElementById('no-results');

    const moneyReceivedInput = document.getElementById('money_received');
    const changeAmountEl = document.getElementById('change_amount');
    const changeRow = document.getElementById('change-row');
    const changeWarning = document.getElementById('change-warning');
    const cashSection = document.getElementById('cash-section');

    const saleForm = document.getElementById('sale-form');
    const customerSelect = document.getElementById('customer_id');
    const cartInputs = document.getElementById('cart-inputs');
    const formCustomerId = document.getElementById('form-customer-id');
    const formDiscount = document.getElementById('form-discount');
    const formPaymentMethod = document.getElementById('form-payment-method');
    const formReceiptIssued = document.getElementById('form-receipt-issued');

    let cart = [];

    /* ============================================
       HELPERS
       ============================================ */
    function formatMoney(n) {
        return '₱' + Number(n || 0).toFixed(2);
    }

    function formatQty(n) {
        const num = Number(n || 0);
        return parseFloat(num.toFixed(2)).toString();
    }

    function getCurrentSubtotal() {
        return cart.reduce((sum, item) => sum + (item.quantity * item.unitPrice), 0);
    }

    function getCurrentTotal() {
        const subtotal = getCurrentSubtotal();
        let discount = parseFloat(discountInput.value) || 0;
        if (discount < 0) discount = 0;
        if (discount > subtotal) discount = subtotal;
        return subtotal - discount;
    }

    /* ============================================
       ADD PRODUCT
       ============================================ */
    function addProductToCart(btn) {
        const productId = btn.dataset.id;
        const productName = btn.dataset.name;
        const unitPrice = parseFloat(btn.dataset.price);
        const stockUnit = btn.dataset.unit;

        if (!productId || isNaN(unitPrice)) return;

        const existing = cart.find(item => item.productId === productId);

        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({
                productId: productId,
                productName: productName,
                quantity: 1,
                unitPrice: unitPrice,
                stockUnit: stockUnit
            });
        }

        renderCart();
    }

    /* ============================================
       RENDER CART
       ============================================ */
    function renderCart() {
        cartItemsEl.innerHTML = '';

        const totalCount = cart.reduce((s, i) => s + i.quantity, 0);
        cartCountEl.textContent = formatQty(totalCount);

        if (cart.length === 0) {
            cartItemsEl.innerHTML = '<div class="cart-empty">No products added yet.</div>';
            subtotalDisplay.textContent = '₱0.00';
            summarySubtotal.value = '₱0.00';
            updateTotal();
            return;
        }

        const subtotal = getCurrentSubtotal();

        cart.forEach(function(item, index) {
            const lineTotal = item.quantity * item.unitPrice;

            const div = document.createElement('div');
            div.className = 'cart-item';

            div.innerHTML = `
                <div class="cart-item-top">
                    <div class="cart-item-name">${item.productName}</div>
                    <button type="button" class="cart-item-remove" onclick="removeFromCart(${index})" title="Remove">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="cart-item-bottom">
                    <div class="qty-controls">
                        <button type="button" class="qty-btn" onclick="changeQuantity(${index}, -1)">−</button>
                        <span class="qty-value">${formatQty(item.quantity)} ${item.stockUnit}</span>
                        <button type="button" class="qty-btn" onclick="changeQuantity(${index}, 1)">+</button>
                    </div>
                    <div class="cart-item-total">${formatMoney(lineTotal)}</div>
                </div>
            `;

            cartItemsEl.appendChild(div);
        });

        subtotalDisplay.textContent = formatMoney(subtotal);
        summarySubtotal.value = formatMoney(subtotal);
        updateTotal();
    }

    /* ============================================
       QUANTITY CONTROL
       ============================================ */
    function changeQuantity(index, delta) {
        const item = cart[index];
        if (!item) return;

        item.quantity += delta;

        if (item.quantity <= 0) {
            cart.splice(index, 1);
        }

        renderCart();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    /* ============================================
       TOTAL + CHANGE
       ============================================ */
    function updateTotal() {
        const subtotal = getCurrentSubtotal();
        let discount = parseFloat(discountInput.value) || 0;

        if (discount < 0) {
            discount = 0;
            discountInput.value = '0';
        }

        if (discount > subtotal) {
            discount = subtotal;
            discountInput.value = subtotal.toFixed(2);
        }

        const total = subtotal - discount;
        totalAmount.textContent = formatMoney(total);

        updateChange();
    }

    function updateChange() {
        const total = getCurrentTotal();
        const received = parseFloat(moneyReceivedInput.value) || 0;
        const change = received - total;

        if (received === 0) {
            changeAmountEl.textContent = formatMoney(0);
            changeRow.classList.remove('insufficient');
            changeWarning.style.display = 'none';
            return;
        }

        if (change < 0) {
            changeAmountEl.textContent = formatMoney(Math.abs(change));
            changeRow.classList.add('insufficient');
            changeWarning.style.display = 'flex';
        } else {
            changeAmountEl.textContent = formatMoney(change);
            changeRow.classList.remove('insufficient');
            changeWarning.style.display = 'none';
        }
    }

    discountInput.addEventListener('input', updateTotal);
    moneyReceivedInput.addEventListener('input', updateChange);

    /* ============================================
       PAYMENT METHOD — toggle cash section
       ============================================ */
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'cash') {
                cashSection.style.display = 'block';
            } else {
                cashSection.style.display = 'none';
                moneyReceivedInput.value = '';
                updateChange();
            }
        });
    });

    /* ============================================
       SEARCH FILTER
       ============================================ */
    if (searchInput && productGrid) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            const cards = productGrid.querySelectorAll('.product-card');
            let visible = 0;

            cards.forEach(card => {
                const matches = card.dataset.search.includes(term);
                card.style.display = matches ? '' : 'none';
                if (matches) visible++;
            });

            if (noResults) {
                noResults.style.display = (visible === 0 && cards.length > 0) ? 'block' : 'none';
            }
        });
    }

    /* ============================================
       FORM SUBMIT
       ============================================ */
    saleForm.addEventListener('submit', function(event) {
        event.preventDefault();

        if (cart.length === 0) {
            alert('Please add at least one product to the cart.');
            return;
        }

        const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
        const paymentValue = selectedPayment ? selectedPayment.value : 'cash';

        if (paymentValue === 'cash') {
            const total = getCurrentTotal();
            const received = parseFloat(moneyReceivedInput.value) || 0;

            if (received < total) {
                alert('Money received is less than the total amount. Please collect enough cash before processing.');
                return;
            }
        }

        // Fill hidden form fields
        formCustomerId.value = customerSelect.value;
        formDiscount.value = parseFloat(discountInput.value) || 0;
        formPaymentMethod.value = paymentValue;
        formReceiptIssued.value = document.getElementById('receipt_issued').checked ? '1' : '0';

        // Build item inputs
        cartInputs.innerHTML = '';
        cart.forEach(function(item, index) {
            const productInput = document.createElement('input');
            productInput.type = 'hidden';
            productInput.name = `items[${index}][product_id]`;
            productInput.value = item.productId;

            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `items[${index}][quantity]`;
            quantityInput.value = item.quantity;

            cartInputs.appendChild(productInput);
            cartInputs.appendChild(quantityInput);
        });

        // Submit via fetch to get receipt URL
        const formData = new FormData(saleForm);

        fetch(saleForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to the receipt page
                    window.location.href = data.receipt_url;
                } else {
                    alert('Error: ' + (data.message || 'Something went wrong'));
                }
            })
            .catch(error => {
                console.error(error);
                alert('Error processing sale. Please try again.');
            });
    });
    /* ============================================
       INIT
       ============================================ */
    renderCart();
</script>

@endsection