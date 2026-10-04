@extends('layouts.app')

@section('title', 'POS / Point of Sale')

@section('content')

<div class="page-header">
    <div>
        <h1>POS / Point of Sale</h1>
        <p>Record a customer purchase and issue a receipt.</p>
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

    {{-- LEFT --}}
    <div class="pos-left">

        {{-- CUSTOMER --}}
        <div class="card">
            <h2 class="card-heading">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Customer
                <span style="font-size: 11px; font-weight: 500; color: #94A3B8; text-transform: none; letter-spacing: 0;">
                    (Optional)
                </span>
            </h2>

            <div class="form-group" style="margin-bottom: 0;">
                <select id="customer_id" class="form-control">
                    <option value="">Walk-in Customer</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer['id'] }}">
                        {{ $customer['label'] }}
                    </option>
                    @endforeach
                </select>
                <small style="color: #94A3B8; font-size: 12px;">
                    Leave as Walk-in if the customer is not registered.
                </small>
            </div>
        </div>

        {{-- PRODUCTS --}}
        <div class="card">
            <h2 class="card-heading">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Select Products
            </h2>

            <div class="pos-search">
                <div class="pos-search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="product-search" class="pos-search-input" placeholder="Search products by name..." autocomplete="off">
            </div>

            @if($products->count() > 0)
            <div class="product-table-wrap">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style="width: 110px; text-align: right;">Price</th>
                            <th style="width: 130px; text-align: right;">Available</th>
                            <th style="width: 100px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body"></tbody>
                </table>

                <div id="no-results" class="no-results" style="display: none;">
                    No products match your search.
                </div>
            </div>

            <div class="product-pagination">
                <div class="pagination-info" id="pagination-info">—</div>
                <div class="pagination-controls">
                    <button type="button" class="page-btn" id="prev-page" disabled>‹ Prev</button>
                    <span class="page-indicator" id="page-indicator">1 / 1</span>
                    <button type="button" class="page-btn" id="next-page" disabled>Next ›</button>
                </div>
            </div>
            @else
            <div class="empty-state" style="padding: 40px 20px;">
                <p>No products are available for sale.</p>
            </div>
            @endif
        </div>

    </div>

    {{-- RIGHT --}}
    <div class="pos-right">

        {{-- CART --}}
        <div class="card">
            <h2 class="card-heading">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Current Order
                <span id="cart-count" class="cart-count">0</span>
            </h2>

            <div id="cart-items" class="cart-items">
                <div class="cart-empty">No items yet. Add products from the list.</div>
            </div>

            <div class="cart-subtotal-row">
                <span>Subtotal</span>
                <strong id="subtotal">₱0.00</strong>
            </div>
        </div>

        {{-- PAYMENT --}}
        <div class="card">
            <h2 class="card-heading">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2E5A3B" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Payment
            </h2>

            {{-- PAYMENT METHOD --}}
            <div class="form-group">
                <label>Payment Method</label>
                <div class="payment-methods">
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="cash" checked>
                        <span class="payment-label">Cash</span>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="gcash">
                        <span class="payment-label">GCash</span>
                    </label>
                </div>
            </div>

            {{-- GCASH REFERENCE --}}
            <div class="form-group" id="gcash-section" style="display: none;">
                <label for="gcash_reference">GCash Reference Number <span style="color: #E85D75;">*</span></label>
                <input type="text" id="gcash_reference" class="form-control" inputmode="numeric" maxlength="13" placeholder="Enter 13-digit reference number">
                <small style="color: #94A3B8; font-size: 12px;">Exactly 13 digits, numbers only.</small>
                <div class="input-feedback" id="gcash-feedback">
                    <span class="feedback-status" id="gcash-status"></span>
                    <span class="feedback-count" id="gcash-count"></span>
                </div>
                <div id="gcash-error" class="error" style="display: none;">GCash reference must be exactly 13 digits.</div>
            </div>

            {{-- DISCOUNT RADIOS --}}
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

            {{-- DISCOUNT DETAILS (only for PWD / Senior) --}}
            <div id="discount-details" style="display: none;">
                <div class="discount-detail-box">
                    <div class="form-group">
                        <label for="discount_name">Name on ID <span style="color: #E85D75;">*</span></label>
                        <input type="text" id="discount_name" class="form-control" placeholder="Full name as shown on ID" maxlength="120">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="discount_id_number">
                            <span id="discount-id-label">ID Number</span>
                            <span style="color: #E85D75;">*</span>
                        </label>
                        <input type="text" id="discount_id_number" class="form-control" placeholder="Enter ID number" autocomplete="off">
                        <small style="color: #94A3B8; font-size: 12px;" id="discount-id-hint">—</small>
                        <div class="input-feedback" id="discount-id-feedback">
                            <span class="feedback-status" id="discount-id-status"></span>
                            <span class="feedback-count" id="discount-id-count"></span>
                        </div>
                        <div id="discount-id-error" class="error" style="display: none;">Invalid format.</div>
                    </div>
                </div>
            </div>

            {{-- DISCOUNT LINE (only when PWD / Senior active) --}}
            <div id="discount-line" class="discount-line" style="display: none;">
                <span>Discount (20%)</span>
                <strong id="discount-display">− ₱0.00</strong>
            </div>

            {{-- TOTAL --}}
            <div class="summary-total-row">
                <span>Total</span>
                <strong id="total_amount" class="total-display">₱0.00</strong>
            </div>

            {{-- CASH SECTION --}}
            <div id="cash-section" style="margin-top: 20px; padding-top: 18px; border-top: 1px dashed #F0E6DD;">
                <div class="form-group">
                    <label for="money_received">Amount Paid</label>
                    <div class="input-with-prefix">
                        <span class="prefix">₱</span>
                        <input type="number" id="money_received" class="form-control" min="0" step="0.01" placeholder="0.00">
                    </div>
                </div>

                <div class="change-row" id="change-row">
                    <span>Change</span>
                    <strong id="change_amount" class="change-display">₱0.00</strong>
                </div>

                <div id="change-warning" class="change-warning" style="display: none;">
                    Amount paid is less than the total.
                </div>
            </div>

            {{-- FORM --}}
            <form id="sale-form" action="{{ route('sales.store') }}" method="POST">
                @csrf
                <input type="hidden" name="customer_id" id="form-customer-id">
                <input type="hidden" name="discount_type" id="form-discount-type" value="none">
                <input type="hidden" name="discount_name" id="form-discount-name">
                <input type="hidden" name="discount_id_number" id="form-discount-id">
                <input type="hidden" name="payment_method" id="form-payment-method" value="cash">
                <input type="hidden" name="gcash_reference" id="form-gcash-reference">
                <input type="hidden" name="receipt_issued" value="1">
                <div id="cart-inputs"></div>

                <button type="submit" class="btn btn-primary btn-block" id="process-btn">
                    Complete Sale
                </button>
            </form>
        </div>

    </div>

</div>

<style>
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
        top: 90px;
    }

    .card-heading {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* SEARCH */
    .pos-search {
        position: relative;
        margin-bottom: 18px;
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

    /* PRODUCT TABLE */
    .product-table-wrap {
        overflow-x: auto;
        margin-bottom: 14px;
    }

    .product-table {
        width: 100%;
        border-collapse: collapse;
    }

    .product-table thead th {
        background: #FCE4EC;
        color: #E85D75;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 1px;
        padding: 10px 12px;
        border-bottom: 2px solid #F8BBD0;
    }

    .product-table tbody td {
        padding: 10px 12px;
        border-bottom: 1px solid #F5EEE4;
        font-size: 13px;
        color: #212121;
        vertical-align: middle;
    }

    .product-table tbody tr:hover {
        background: #FEFCF9;
    }

    .product-name-cell {
        font-weight: 600;
        color: #212121;
    }

    .product-price-cell {
        font-weight: 700;
        color: #E85D75;
        text-align: right;
        white-space: nowrap;
    }

    .product-stock-cell {
        color: #2E5A3B;
        font-size: 12px;
        font-weight: 600;
        text-align: right;
        white-space: nowrap;
    }

    .product-stock-cell.low {
        color: #B8860B;
    }

    .product-stock-cell.empty {
        color: #DC3545;
    }

    .add-row-btn {
        padding: 7px 14px;
        border-radius: 6px;
        background: #E85D75;
        color: #FFFFFF;
        border: none;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s ease;
    }

    .add-row-btn:hover {
        background: #D14A62;
    }

    .add-row-btn:disabled {
        background: #E2E8F0;
        color: #94A3B8;
        cursor: not-allowed;
    }

    .out-of-stock-text {
        color: #DC3545;
        font-weight: 600;
        font-size: 12px;
    }

    .no-results {
        text-align: center;
        padding: 40px 20px;
        color: #94A3B8;
        font-size: 14px;
    }

    /* PAGINATION */
    .product-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 14px;
        border-top: 1px solid #F0E6DD;
        flex-wrap: wrap;
        gap: 10px;
    }

    .pagination-info {
        font-size: 12px;
        color: #64748B;
    }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .page-btn {
        padding: 6px 12px;
        border-radius: 6px;
        background: #FFFFFF;
        border: 1.5px solid #F0E6DD;
        color: #64748B;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
    }

    .page-btn:hover:not(:disabled) {
        border-color: #E85D75;
        color: #E85D75;
    }

    .page-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .page-indicator {
        font-size: 12px;
        font-weight: 600;
        color: #212121;
        padding: 0 6px;
    }

    /* CART */
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
        margin-bottom: 8px;
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
        font-family: inherit;
    }

    .cart-item-remove:hover {
        color: #DC3545;
        background: #FDECEA;
    }

    .cart-item-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .qty-controls {
        display: inline-flex;
        align-items: center;
        background: #FFFFFF;
        border: 1px solid #F0E6DD;
        border-radius: 8px;
        overflow: hidden;
    }

    .qty-btn {
        width: 30px;
        height: 30px;
        background: transparent;
        border: none;
        cursor: pointer;
        font-weight: 700;
        font-size: 15px;
        color: #64748B;
        font-family: inherit;
    }

    .qty-btn:hover {
        background: #FCE4EC;
        color: #E85D75;
    }

    .qty-input {
        width: 50px;
        height: 30px;
        border: none;
        border-left: 1px solid #F0E6DD;
        border-right: 1px solid #F0E6DD;
        background: transparent;
        text-align: center;
        font-size: 13px;
        font-weight: 700;
        color: #212121;
        font-family: inherit;
        outline: none;
        -moz-appearance: textfield;
    }

    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
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

    /* DISCOUNT LINE */
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

    /* TOTAL */
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

    /* CHANGE */
    .change-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 14px;
        background: #E8F5E9;
        border-radius: 10px;
        margin-top: 6px;
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
    }

    .change-row.insufficient {
        background: #FDECEA;
    }

    .change-row.insufficient span,
    .change-row.insufficient .change-display {
        color: #DC3545;
    }

    .change-warning {
        margin-top: 8px;
        font-size: 12px;
        color: #DC3545;
        font-weight: 500;
    }

    /* PAYMENT METHODS */
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
        font-size: 14px;
        color: #212121;
        font-weight: 500;
    }

    /* DISCOUNT RADIOS */
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
        text-align: center;
    }

    .discount-radio input[type="radio"] {
        display: none;
    }

    .discount-radio:hover {
        border-color: #F8BBD0;
        background: #FEFCF9;
    }

    .discount-radio:has(input:checked) {
        border-color: #E85D75;
        background: #FCE4EC;
        color: #E85D75;
    }

    /* DISCOUNT DETAIL BOX */
    .discount-detail-box {
        padding: 14px;
        margin-bottom: 14px;
        background: #FEFCF9;
        border: 1px dashed #F0E6DD;
        border-radius: 10px;
    }

    .btn-block {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px;
        font-size: 15px;
        font-weight: 600;
        margin-top: 16px;
    }

    @media (max-width: 1024px) {
        .pos-layout {
            grid-template-columns: 1fr;
        }

        .pos-right {
            position: static;
        }
    }

    @media (max-width: 640px) {

        .product-table thead th,
        .product-table tbody td {
            padding: 8px;
            font-size: 12px;
        }
    }

    /* ===============================
   INPUT FEEDBACK PILL
   =============================== */
    .input-feedback {
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 6px;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .input-feedback.visible {
        display: flex;
    }

    .input-feedback.valid {
        background: #E8F5E9;
        color: #2E5A3B;
    }

    .input-feedback.invalid {
        background: #FDECEA;
        color: #C0392B;
    }

    .input-feedback.neutral {
        background: #F1F5F9;
        color: #64748B;
    }

    .feedback-status {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .feedback-count {
        font-family: 'SF Mono', Consolas, monospace;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    /* Input border color reflects state */
    .form-control.input-valid {
        border-color: #80B918 !important;
    }

    .form-control.input-invalid {
        border-color: #DC3545 !important;
        animation: shake 0.3s ease;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-3px);
        }

        75% {
            transform: translateX(3px);
        }
    }
</style>

<script>
    const allProducts = @json($products);
    const allCustomers = @json($customers);
    const DISCOUNT_RATE = 0.20;
    const ITEMS_PER_PAGE = 10;

    let currentPage = 1;
    let filteredProducts = [...allProducts];
    let activeDiscount = 'none';
    let cart = [];

    /* ELEMENTS */
    const productTableBody = document.getElementById('product-table-body');
    const noResults = document.getElementById('no-results');
    const searchInput = document.getElementById('product-search');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const pageIndicator = document.getElementById('page-indicator');
    const paginationInfo = document.getElementById('pagination-info');

    const cartItemsEl = document.getElementById('cart-items');
    const cartCountEl = document.getElementById('cart-count');
    const subtotalDisplay = document.getElementById('subtotal');

    const discountDetails = document.getElementById('discount-details');
    const discountNameInput = document.getElementById('discount_name');
    const discountIdInput = document.getElementById('discount_id_number');
    const discountIdLabel = document.getElementById('discount-id-label');
    const discountIdHint = document.getElementById('discount-id-hint');
    const discountIdError = document.getElementById('discount-id-error');
    const discountLine = document.getElementById('discount-line');
    const discountDisplay = document.getElementById('discount-display');

    const totalAmount = document.getElementById('total_amount');
    const moneyReceivedInput = document.getElementById('money_received');
    const changeAmountEl = document.getElementById('change_amount');
    const changeRow = document.getElementById('change-row');
    const changeWarning = document.getElementById('change-warning');
    const cashSection = document.getElementById('cash-section');
    const gcashSection = document.getElementById('gcash-section');
    const gcashInput = document.getElementById('gcash_reference');
    const gcashError = document.getElementById('gcash-error');

    const saleForm = document.getElementById('sale-form');
    const customerSelect = document.getElementById('customer_id');
    const cartInputs = document.getElementById('cart-inputs');
    const formCustomerId = document.getElementById('form-customer-id');
    const formDiscountType = document.getElementById('form-discount-type');
    const formDiscountName = document.getElementById('form-discount-name');
    const formDiscountId = document.getElementById('form-discount-id');
    const formPaymentMethod = document.getElementById('form-payment-method');
    const formGcashReference = document.getElementById('form-gcash-reference');

    /* HELPERS */
    function formatMoney(n) {
        return '₱' + Number(n || 0).toFixed(2);
    }

    function formatQty(n) {
        const num = Number(n || 0);
        return parseFloat(num.toFixed(2)).toString();
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function getCurrentSubtotal() {
        return cart.reduce((sum, item) => sum + (item.quantity * item.unitPrice), 0);
    }

    function getCurrentDiscount() {
        if (activeDiscount === 'none') return 0;
        return getCurrentSubtotal() * DISCOUNT_RATE;
    }

    function getCurrentTotal() {
        return getCurrentSubtotal() - getCurrentDiscount();
    }

    /* ============================================
       PWD ID FORMATTER
       Format: RR-PPMM-BBB-NNNNNNN
       Hyphens auto-inserted after 2, 6, and 9 digits
       ============================================ */
    function formatPwdId(value) {
        const digits = value.replace(/\D/g, '').slice(0, 16);

        let result = '';
        for (let i = 0; i < digits.length; i++) {
            if (i === 2 || i === 6 || i === 9) {
                result += '-';
            }
            result += digits[i];
        }
        return result;
    }

    /* ============================================
       SENIOR ID FORMATTER
       Alphanumeric + hyphens/slashes/spaces
       Auto-uppercase, strip disallowed chars
       ============================================ */
    function formatSeniorId(value) {
        // Allow letters, digits, hyphens, slashes, spaces
        let cleaned = value.replace(/[^A-Za-z0-9\-\/\s]/g, '');
        // Collapse multiple spaces
        cleaned = cleaned.replace(/\s+/g, ' ');
        // Uppercase
        return cleaned.toUpperCase().slice(0, 30);
    }

    /* ============================================
       FEEDBACK HELPERS
       ============================================ */
    function showInputFeedback(feedbackEl, statusEl, countEl, state, message, countText) {
        feedbackEl.classList.remove('visible', 'valid', 'invalid', 'neutral');
        feedbackEl.classList.add('visible', state);
        statusEl.textContent = message;
        countEl.textContent = countText || '';
    }

    function hideInputFeedback(feedbackEl) {
        feedbackEl.classList.remove('visible', 'valid', 'invalid', 'neutral');
    }

    /* ============================================
       DISCOUNT ID INPUT — live formatting + feedback
       ============================================ */
    discountIdInput.addEventListener('input', function(e) {
        const cursorAtEnd = this.selectionStart === this.value.length;
        const rawInput = this.value;

        if (activeDiscount === 'pwd') {
            this.value = formatPwdId(this.value);
        } else if (activeDiscount === 'senior') {
            this.value = formatSeniorId(this.value);
        }

        if (cursorAtEnd) {
            const len = this.value.length;
            this.setSelectionRange(len, len);
        }

        discountIdError.style.display = 'none';

        // Live feedback
        const feedback = document.getElementById('discount-id-feedback');
        const status = document.getElementById('discount-id-status');
        const count = document.getElementById('discount-id-count');

        if (activeDiscount === 'pwd') {
            const digits = this.value.replace(/\D/g, '').length;
            const maxDigits = 16;

            if (digits === 0) {
                hideInputFeedback(feedback);
                this.classList.remove('input-valid', 'input-invalid');
            } else if (digits < maxDigits) {
                showInputFeedback(feedback, status, count, 'neutral', 'Complete the ID', `${digits} / ${maxDigits} digits`);
                this.classList.remove('input-valid', 'input-invalid');
            } else {
                showInputFeedback(feedback, status, count, 'valid', '✓ Valid PWD ID format', `${digits} / ${maxDigits} digits`);
                this.classList.remove('input-invalid');
                this.classList.add('input-valid');
            }
        } else if (activeDiscount === 'senior') {
            const len = this.value.length;
            const maxLen = 30;

            if (len === 0) {
                hideInputFeedback(feedback);
                this.classList.remove('input-valid', 'input-invalid');
            } else if (len < 4) {
                showInputFeedback(feedback, status, count, 'neutral', '⏳ Too short', `${len} / ${maxLen} characters`);
                this.classList.remove('input-valid', 'input-invalid');
            } else {
                showInputFeedback(feedback, status, count, 'valid', '✓ Accepted format', `${len} / ${maxLen} characters`);
                this.classList.remove('input-invalid');
                this.classList.add('input-valid');
            }
        }
    });

    // Prevent invalid keystrokes + flash feedback
    discountIdInput.addEventListener('keypress', function(e) {
        if (activeDiscount === 'pwd' && !/[0-9]/.test(e.key)) {
            e.preventDefault();
            flashInvalidFeedback(
                document.getElementById('discount-id-feedback'),
                document.getElementById('discount-id-status'),
                document.getElementById('discount-id-count'),
                this,
                '✕ Numbers only',
                'PWD ID accepts digits only'
            );
        }
    });

    // Paste handling — strip invalid chars and warn
    discountIdInput.addEventListener('paste', function(e) {
        const paste = (e.clipboardData || window.clipboardData).getData('text');

        if (activeDiscount === 'pwd' && /[^0-9\s\-]/.test(paste)) {
            e.preventDefault();
            flashInvalidFeedback(
                document.getElementById('discount-id-feedback'),
                document.getElementById('discount-id-status'),
                document.getElementById('discount-id-count'),
                this,
                '✕ Invalid characters',
                'Numbers and hyphens only'
            );
        }
    });

    function flashInvalidFeedback(feedbackEl, statusEl, countEl, inputEl, message, subtitle) {
        showInputFeedback(feedbackEl, statusEl, countEl, 'invalid', message, subtitle);
        inputEl.classList.add('input-invalid');

        setTimeout(() => {
            inputEl.classList.remove('input-invalid');
            // Re-run current state
            if (inputEl.value.length === 0) {
                hideInputFeedback(feedbackEl);
            } else {
                inputEl.dispatchEvent(new Event('input'));
            }
        }, 1500);
    }

    /* ============================================
       GCASH REFERENCE — digits only, max 13 + feedback
       ============================================ */
    gcashInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 13);
        if (this.value.length === 13) gcashError.style.display = 'none';

        const feedback = document.getElementById('gcash-feedback');
        const status = document.getElementById('gcash-status');
        const count = document.getElementById('gcash-count');
        const len = this.value.length;

        if (len === 0) {
            hideInputFeedback(feedback);
            this.classList.remove('input-valid', 'input-invalid');
        } else if (len < 13) {
            showInputFeedback(feedback, status, count, 'neutral', 'Incomplete', `${len} / 13 digits`);
            this.classList.remove('input-valid', 'input-invalid');
        } else {
            showInputFeedback(feedback, status, count, 'valid', '✓ Valid GCash reference', `${len} / 13 digits`);
            this.classList.remove('input-invalid');
            this.classList.add('input-valid');
        }
    });

    gcashInput.addEventListener('keypress', function(e) {
        if (!/[0-9]/.test(e.key)) {
            e.preventDefault();
            flashInvalidFeedback(
                document.getElementById('gcash-feedback'),
                document.getElementById('gcash-status'),
                document.getElementById('gcash-count'),
                this,
                '✕ Numbers only',
                '13 digits required'
            );
        }
    });

    gcashInput.addEventListener('paste', function(e) {
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        if (/[^0-9]/.test(paste)) {
            e.preventDefault();
            flashInvalidFeedback(
                document.getElementById('gcash-feedback'),
                document.getElementById('gcash-status'),
                document.getElementById('gcash-count'),
                this,
                '✕ Invalid characters',
                'Numbers only'
            );
        }
    });

    /* ============================================
       PRODUCT TABLE
       ============================================ */
    function renderProductTable() {
        const totalItems = filteredProducts.length;
        const totalPages = Math.max(1, Math.ceil(totalItems / ITEMS_PER_PAGE));

        if (currentPage > totalPages) currentPage = totalPages;

        const startIdx = (currentPage - 1) * ITEMS_PER_PAGE;
        const endIdx = startIdx + ITEMS_PER_PAGE;
        const pageItems = filteredProducts.slice(startIdx, endIdx);

        productTableBody.innerHTML = '';

        if (totalItems === 0) {
            noResults.style.display = 'block';
            paginationInfo.textContent = 'No products';
            pageIndicator.textContent = '0 / 0';
            prevPageBtn.disabled = true;
            nextPageBtn.disabled = true;
            return;
        }

        noResults.style.display = 'none';

        pageItems.forEach(function(product) {
            const stock = Number(product.stock) || 0;
            const canAdd = stock > 0;

            let stockClass = 'product-stock-cell';
            if (stock === 0) stockClass += ' empty';
            else if (stock <= 10) stockClass += ' low';

            let actionCell;
            if (!canAdd) {
                actionCell = `<span class="out-of-stock-text">Out of Stock</span>`;
            } else {
                actionCell = `<button type="button" class="add-row-btn" onclick="handleAddProduct(${product.id})">Add</button>`;
            }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="product-name-cell">${escapeHtml(product.name)}</td>
                <td class="product-price-cell">${formatMoney(product.price)}</td>
                <td class="${stockClass}">${formatQty(stock)} ${product.unit}</td>
                <td style="text-align: center;">${actionCell}</td>
            `;
            productTableBody.appendChild(row);
        });

        paginationInfo.textContent = `Showing ${startIdx + 1}–${Math.min(endIdx, totalItems)} of ${totalItems} products`;
        pageIndicator.textContent = `${currentPage} / ${totalPages}`;
        prevPageBtn.disabled = currentPage === 1;
        nextPageBtn.disabled = currentPage === totalPages;
    }

    /* ADD */
    function handleAddProduct(productId) {
        const product = allProducts.find(p => p.id === productId);
        if (!product) return;

        const stock = Number(product.stock) || 0;
        const existing = cart.find(item => item.productId === productId);
        const existingQty = existing ? existing.quantity : 0;

        if (existingQty + 1 > stock) {
            alert(`Only ${formatQty(stock)} ${product.unit} of "${product.name}" available in stock.`);
            return;
        }

        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({
                productId: product.id,
                productName: product.name,
                quantity: 1,
                unitPrice: product.price,
                stockUnit: product.unit,
                maxStock: stock,
            });
        }

        renderCart();
    }

    /* ============================================
       CART
       ============================================ */
    function renderCart() {
        cartItemsEl.innerHTML = '';

        const totalCount = cart.reduce((s, i) => s + i.quantity, 0);
        cartCountEl.textContent = formatQty(totalCount);

        if (cart.length === 0) {
            cartItemsEl.innerHTML = '<div class="cart-empty">No items yet. Add products from the list.</div>';
            subtotalDisplay.textContent = '₱0.00';
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
                    <div class="cart-item-name">${escapeHtml(item.productName)}</div>
                    <button type="button" class="cart-item-remove" onclick="removeFromCart(${index})" title="Remove">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="cart-item-bottom">
                    <div class="qty-controls">
                        <button type="button" class="qty-btn" onclick="changeQuantity(${index}, -1)">−</button>
                        <input
                            type="number"
                            class="qty-input"
                            value="${item.quantity}"
                            min="1"
                            step="1"
                            data-index="${index}"
                            onchange="setQuantityFromInput(this)"
                            onkeyup="if(event.key==='Enter'){this.blur();}">
                        <button type="button" class="qty-btn" onclick="changeQuantity(${index}, 1)">+</button>
                    </div>
                    <div class="cart-item-total">${formatMoney(lineTotal)}</div>
                </div>
            `;
            cartItemsEl.appendChild(div);
        });

        subtotalDisplay.textContent = formatMoney(subtotal);
        updateTotal();
    }

    function changeQuantity(index, delta) {
        const item = cart[index];
        if (!item) return;

        const newQty = item.quantity + delta;

        if (delta > 0 && newQty > item.maxStock) {
            alert(`Only ${formatQty(item.maxStock)} ${item.stockUnit} of "${item.productName}" available in stock.`);
            return;
        }

        item.quantity = newQty;
        if (item.quantity <= 0) cart.splice(index, 1);

        renderCart();
    }

    function setQuantityFromInput(inputEl) {
        const index = parseInt(inputEl.dataset.index);
        const item = cart[index];
        if (!item) return;

        let qty = parseInt(inputEl.value) || 0;

        if (qty <= 0) {
            cart.splice(index, 1);
            renderCart();
            return;
        }

        if (qty > item.maxStock) {
            alert(`Only ${formatQty(item.maxStock)} ${item.stockUnit} of "${item.productName}" available in stock.`);
            qty = item.maxStock;
        }

        item.quantity = qty;
        renderCart();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    /* ============================================
       TOTAL + DISCOUNT
       ============================================ */
    function updateTotal() {
        const subtotal = getCurrentSubtotal();
        const discount = getCurrentDiscount();
        const total = subtotal - discount;

        totalAmount.textContent = formatMoney(total);

        if (activeDiscount !== 'none' && subtotal > 0) {
            discountLine.style.display = 'flex';
            discountDisplay.textContent = '− ' + formatMoney(discount);
        } else {
            discountLine.style.display = 'none';
        }

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
            changeWarning.style.display = 'block';
        } else {
            changeAmountEl.textContent = formatMoney(change);
            changeRow.classList.remove('insufficient');
            changeWarning.style.display = 'none';
        }
    }

    moneyReceivedInput.addEventListener('input', updateChange);

    /* ============================================
       PAYMENT METHOD
       ============================================ */
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'cash') {
                cashSection.style.display = 'block';
                gcashSection.style.display = 'none';
                gcashInput.value = '';
                gcashError.style.display = 'none';
            } else {
                cashSection.style.display = 'none';
                gcashSection.style.display = 'block';
                moneyReceivedInput.value = '';
                updateChange();
            }
        });
    });

    /* ============================================
       DISCOUNT RADIOS
       ============================================ */
    document.querySelectorAll('input[name="discount_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            handleDiscountChange(this.value);
        });
    });

    function handleDiscountChange(type) {
        activeDiscount = type;
        discountIdError.style.display = 'none';

        if (type === 'none') {
            discountDetails.style.display = 'none';
            discountNameInput.value = '';
            discountIdInput.value = '';
            discountIdInput.classList.remove('input-valid', 'input-invalid');
            hideInputFeedback(document.getElementById('discount-id-feedback'));
            // Reset any previous feedback
            discountIdInput.classList.remove('input-valid', 'input-invalid');
            hideInputFeedback(document.getElementById('discount-id-feedback'));
            updateTotal();
            return;
        }

        discountDetails.style.display = 'block';

        if (type === 'pwd') {
            discountIdLabel.textContent = 'PWD ID Number';
            discountIdHint.textContent = 'Format: RR-PPMM-BBB-NNNNNNN (16 digits). Hyphens auto-insert.';
            discountIdInput.placeholder = '12-3456-789-0123456';
            discountIdInput.setAttribute('maxlength', '19');
            discountIdInput.setAttribute('inputmode', 'numeric');
            // Re-format any existing value
            discountIdInput.value = formatPwdId(discountIdInput.value);
        } else {
            discountIdLabel.textContent = 'Senior Citizen ID Number';
            discountIdHint.textContent = 'Formats vary by LGU (e.g. 12345, QC-12345, 2024-0012).';
            discountIdInput.placeholder = 'e.g. QC-12345 or 12345';
            discountIdInput.setAttribute('maxlength', '30');
            discountIdInput.removeAttribute('inputmode');
            // Uppercase existing value
            discountIdInput.value = formatSeniorId(discountIdInput.value);
        }

        // Auto-fill name from registered customer
        if (customerSelect.value) {
            const customer = allCustomers.find(c => c.id == customerSelect.value);
            if (customer && customer.full_name) {
                discountNameInput.value = customer.full_name;
            }
        }

        updateTotal();
    }

    /* ============================================
       CUSTOMER SELECT
       ============================================ */
    customerSelect.addEventListener('change', function() {
        const customerId = this.value;

        if (!customerId) {
            const noneRadio = document.querySelector('input[name="discount_type"][value="none"]');
            if (noneRadio) {
                noneRadio.checked = true;
                handleDiscountChange('none');
            }
            return;
        }

        const customer = allCustomers.find(c => c.id == customerId);
        if (!customer) return;

        if (customer.discount_type && customer.discount_type !== 'none') {
            const radio = document.querySelector(`input[name="discount_type"][value="${customer.discount_type}"]`);
            if (radio) {
                radio.checked = true;
                handleDiscountChange(customer.discount_type);
                discountNameInput.value = customer.full_name || '';
                discountIdInput.value = customer.discount_id_number || '';
                // Re-run formatter for the auto-filled value
                discountIdInput.dispatchEvent(new Event('input'));
            }
        } else {
            const noneRadio = document.querySelector('input[name="discount_type"][value="none"]');
            if (noneRadio) {
                noneRadio.checked = true;
                handleDiscountChange('none');
            }
        }
    });

    /* ============================================
       SEARCH + PAGINATION
       ============================================ */
    searchInput.addEventListener('input', function() {
        const term = this.value.toLowerCase().trim();
        filteredProducts = allProducts.filter(p => p.name.toLowerCase().includes(term));
        currentPage = 1;
        renderProductTable();
    });

    prevPageBtn.addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            renderProductTable();
        }
    });
    nextPageBtn.addEventListener('click', function() {
        const totalPages = Math.max(1, Math.ceil(filteredProducts.length / ITEMS_PER_PAGE));
        if (currentPage < totalPages) {
            currentPage++;
            renderProductTable();
        }
    });

    /* ============================================
       SUBMIT
       ============================================ */
    saleForm.addEventListener('submit', function(event) {
        event.preventDefault();

        if (cart.length === 0) {
            alert('Please add at least one item to the order.');
            return;
        }

        const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
        const paymentValue = selectedPayment ? selectedPayment.value : 'cash';

        if (paymentValue === 'cash') {
            const total = getCurrentTotal();
            const received = parseFloat(moneyReceivedInput.value) || 0;
            if (received < total) {
                alert('Amount paid is less than the total. Please collect enough cash.');
                return;
            }
        }

        if (paymentValue === 'gcash') {
            const ref = gcashInput.value.trim();
            if (!/^\d{13}$/.test(ref)) {
                gcashError.style.display = 'block';
                gcashInput.focus();
                return;
            }
        }

        const discountName = discountNameInput.value.trim();
        const discountId = discountIdInput.value.trim();

        if (activeDiscount === 'pwd') {
            if (!discountName) {
                alert('Please enter the name on the PWD ID.');
                discountNameInput.focus();
                return;
            }
            const digits = discountId.replace(/\D/g, '');
            if (digits.length !== 16) {
                discountIdError.textContent = 'PWD ID must contain exactly 16 digits.';
                discountIdError.style.display = 'block';
                discountIdInput.focus();
                return;
            }
        }

        if (activeDiscount === 'senior') {
            if (!discountName) {
                alert('Please enter the name on the Senior Citizen ID.');
                discountNameInput.focus();
                return;
            }
            if (discountId.length < 4) {
                discountIdError.textContent = 'Senior Citizen ID must be at least 4 characters.';
                discountIdError.style.display = 'block';
                discountIdInput.focus();
                return;
            }
        }

        formCustomerId.value = customerSelect.value;
        formDiscountType.value = activeDiscount;
        formDiscountName.value = activeDiscount === 'none' ? '' : discountName;
        formDiscountId.value = activeDiscount === 'none' ? '' : discountId;
        formPaymentMethod.value = paymentValue;
        formGcashReference.value = paymentValue === 'gcash' ? gcashInput.value.trim() : '';

        cartInputs.innerHTML = '';

        const discountInput = document.createElement('input');
        discountInput.type = 'hidden';
        discountInput.name = 'discount_amount';
        discountInput.value = getCurrentDiscount().toFixed(2);
        cartInputs.appendChild(discountInput);

        cart.forEach(function(item, index) {
            const p = document.createElement('input');
            p.type = 'hidden';
            p.name = `items[${index}][product_id]`;
            p.value = item.productId;

            const q = document.createElement('input');
            q.type = 'hidden';
            q.name = `items[${index}][quantity]`;
            q.value = item.quantity;

            cartInputs.appendChild(p);
            cartInputs.appendChild(q);
        });

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

    /* INIT */
    renderProductTable();
    renderCart();
</script>

@endsection