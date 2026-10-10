@extends('layouts.app')

@section('title', 'POS')

@section('content')

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

    {{-- LEFT COLUMN --}}
    <div class="pos-left">

        {{-- CUSTOMER --}}
        <div class="card customer-card">
            <h2 class="card-heading">Customer</h2>
            <div class="form-group" style="margin-bottom: 0;">
                <select id="customer_id" class="form-control">
                    <option value="">Walk-in Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer['id'] }}">
                            {{ $customer['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- ITEMS --}}
        <div class="card products-card">
            <h2 class="card-heading">Select Items</h2>

            @if(count($products) > 0)
                <div class="pos-search">
                    <div class="pos-search-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="product-search" class="pos-search-input"
                           placeholder="Search items by name..." autocomplete="off">
                </div>

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
                        No items match your search.
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
                <div class="empty-state">
                    <p style="font-size: 14px; color: #94A3B8;">
                        No items available for sale right now.
                    </p>
                    <p style="font-size: 12px; color: #CBD5E1; margin-top: 6px;">
                        All items are out of stock.
                    </p>
                </div>
            @endif
        </div>

    </div>

    {{-- RIGHT COLUMN --}}
    <div class="pos-right">

        {{-- CART --}}
        <div class="card pos-cart-card">
            <h2 class="card-heading">
                Current Order
                <span id="cart-count" class="cart-count">0</span>
            </h2>

            <div id="cart-items" class="cart-items">
                <div class="cart-empty">No items yet. Add items from the list.</div>
            </div>

            <div class="cart-subtotal-row">
                <span>Subtotal</span>
                <strong id="subtotal">₱0.00</strong>
            </div>
        </div>

        {{-- PAYMENT --}}
        <div class="card payment-card">
            <h2 class="card-heading">Payment</h2>

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

            {{-- GCASH --}}
            <div class="form-group" id="gcash-section" style="display: none;">
                <label for="gcash_reference">GCash Reference Number <span style="color: #6B5B95;">*</span></label>
                <input type="text" id="gcash_reference" class="form-control" inputmode="numeric" maxlength="13" placeholder="13-digit reference number">
                <small class="field-help">Exactly 13 digits, numbers only.</small>
            </div>

            {{-- DISCOUNT --}}
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
                        <span>Senior</span>
                    </label>
                </div>
            </div>

            {{-- DISCOUNT DETAILS --}}
            <div id="discount-details" style="display: none;">
                <div class="discount-detail-box">
                    <div class="form-group">
                        <label for="discount_name">Name on ID <span style="color: #6B5B95;">*</span></label>
                        <input type="text" id="discount_name" class="form-control" placeholder="Full name as shown on ID" maxlength="120">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="discount_id_number">
                            <span id="discount-id-label">ID Number</span>
                            <span style="color: #6B5B95;">*</span>
                        </label>
                        <input type="text" id="discount_id_number" class="form-control" placeholder="Enter ID number">
                        <small class="field-help" id="discount-id-hint">—</small>
                    </div>
                </div>
            </div>

            {{-- DISCOUNT LINE --}}
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
            <div id="cash-section" class="cash-section">
                <div class="form-group">
                    <label for="money_received">Amount Paid</label>
                    <div class="input-with-prefix">
                        <span class="prefix">₱</span>
                        <input type="number" id="money_received" class="form-control" min="0" step="0.01" placeholder="0.00">
                    </div>
                </div>

                <div class="change-row" id="change-row">
                    <span id="change-label">Change</span>
                    <strong id="change_amount" class="change-display">₱0.00</strong>
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

{{-- ============================================
     CONFIRMATION MODAL
     ============================================ --}}
<div class="modal-overlay" id="confirm-modal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Confirm Sale</h3>
            <p class="modal-subtitle">Review the details below before completing the sale.</p>
        </div>

        <div class="modal-body">
            <div class="modal-section">
                <div class="modal-row">
                    <span class="modal-row-label">Customer</span>
                    <span class="modal-row-value" id="modal-customer">Walk-in Customer</span>
                </div>
                <div class="modal-row">
                    <span class="modal-row-label">Payment</span>
                    <span class="modal-row-value" id="modal-payment-method">Cash</span>
                </div>
            </div>

            <div class="modal-section">
                <div class="modal-section-title">Items</div>
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
                <div class="modal-row modal-row-total">
                    <span class="modal-row-label">Total</span>
                    <span class="modal-row-value modal-value-total" id="modal-total">₱0.00</span>
                </div>
            </div>

            <div class="modal-section" id="modal-payment-detail">
                <div class="modal-row" id="modal-cash-received-row" style="display: none;">
                    <span class="modal-row-label">Amount Paid</span>
                    <span class="modal-row-value" id="modal-cash-received">₱0.00</span>
                </div>
                <div class="modal-row" id="modal-change-row" style="display: none;">
                    <span class="modal-row-label">Change</span>
                    <span class="modal-row-value modal-value-change" id="modal-change">₱0.00</span>
                </div>
                <div class="modal-row" id="modal-gcash-ref-row" style="display: none;">
                    <span class="modal-row-label">GCash Reference</span>
                    <span class="modal-row-value modal-value-mono" id="modal-gcash-ref">—</span>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeConfirmModal()">
                Cancel
            </button>
            <button type="button" class="modal-btn modal-btn-primary" id="modal-confirm-btn">
                Confirm & Process Sale
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
    /* =========================================
       POS LAYOUT — no page scroll, only payment card scrolls
       ========================================= */
    .pos-layout {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 20px;
        height: calc(100vh - 100px);
        min-height: 560px;
        overflow: hidden;
    }

    .pos-left {
        display: flex;
        flex-direction: column;
        gap: 16px;
        min-width: 0;
        min-height: 0;
        overflow: hidden;
    }

    .customer-card { flex-shrink: 0; }

    .products-card {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .products-card > .card-heading,
    .products-card > .pos-search { flex-shrink: 0; }
    .products-card > .product-table-wrap {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        margin-bottom: 14px;
    }
    .products-card > .product-pagination { flex-shrink: 0; }
    .products-card > .empty-state {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .pos-right {
        display: flex;
        flex-direction: column;
        gap: 16px;
        min-height: 0;
        overflow: hidden;
    }

    .pos-cart-card {
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        max-height: 320px;
        overflow: hidden;
    }
    .pos-cart-card .cart-items {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        max-height: none;
        margin-bottom: 14px;
    }

    .payment-card {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        padding-right: 22px;
    }

    /* =========================================
       BASE
       ========================================= */
    .card-heading {
        font-size: 15px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pos-search { position: relative; margin-bottom: 16px; }
    .pos-search-icon {
        position: absolute; left: 8px; top: 50%; transform: translateY(-50%);
        width: 34px; height: 34px; border-radius: 50%;
        background: #6B5B95; color: #FFFFFF;
        display: flex; align-items: center; justify-content: center;
        pointer-events: none;
    }
    .pos-search-input {
        width: 100%; height: 48px;
        padding: 0 18px 0 54px;
        border: 1.5px solid #F0E6DD; border-radius: 24px;
        font-size: 14px; background: #FFFFFF; color: #212121;
        transition: all 0.2s ease; font-family: inherit;
    }
    .pos-search-input:focus {
        outline: none; border-color: #6B5B95;
        box-shadow: 0 0 0 4px rgba(107, 91, 149, 0.1);
    }
    .pos-search-input::placeholder { color: #B0A99F; }

    /* PRODUCT TABLE */
    .product-table { width: 100%; border-collapse: collapse; }
    .product-table thead th {
        background: #EFEBF7; color: #6B5B95; font-weight: 700;
        text-transform: uppercase; font-size: 10px; letter-spacing: 1px;
        padding: 10px 12px; border-bottom: 2px solid #D5C9E8;
        position: sticky; top: 0; z-index: 1;
    }
    .product-table tbody td {
        padding: 10px 12px; border-bottom: 1px solid #F5EEE4;
        font-size: 13px; color: #212121; vertical-align: middle;
    }
    .product-table tbody tr:hover { background: #FDFBFF; }
    .product-name-cell { font-weight: 600; color: #212121; }
    .product-price-cell { font-weight: 700; color: #6B5B95; text-align: right; white-space: nowrap; }
    .product-stock-cell { color: #2E5A3B; font-size: 12px; font-weight: 600; text-align: right; white-space: nowrap; }
    .product-stock-cell.low { color: #B8860B; }

    .add-row-btn {
        padding: 6px 14px; border-radius: 6px;
        background: #6B5B95; color: #FFFFFF; border: none;
        font-size: 12px; font-weight: 600; cursor: pointer;
        font-family: inherit; transition: all 0.15s ease;
    }
    .add-row-btn:hover { background: #594B7D; }

    .no-results { text-align: center; padding: 40px 20px; color: #94A3B8; font-size: 14px; }

    .product-pagination {
        display: flex; align-items: center; justify-content: space-between;
        padding-top: 12px; border-top: 1px solid #F0E6DD;
        flex-wrap: wrap; gap: 10px;
    }
    .pagination-info { font-size: 12px; color: #64748B; }
    .pagination-controls { display: flex; align-items: center; gap: 8px; }
    .page-btn {
        padding: 5px 12px; border-radius: 6px;
        background: #FFFFFF; border: 1.5px solid #F0E6DD;
        color: #64748B; font-size: 12px; font-weight: 600;
        cursor: pointer; font-family: inherit;
    }
    .page-btn:hover:not(:disabled) { border-color: #6B5B95; color: #6B5B95; }
    .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    .page-indicator { font-size: 12px; font-weight: 600; color: #212121; padding: 0 6px; }

    /* CART */
    .cart-count {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 22px; height: 22px; padding: 0 7px;
        background: #6B5B95; color: #FFFFFF;
        border-radius: 11px; font-size: 11px; font-weight: 700; margin-left: 4px;
    }
    .cart-empty {
        text-align: center; padding: 30px 20px; color: #94A3B8; font-size: 13px;
        background: #FDFBFF; border-radius: 10px;
    }
    .cart-item {
        padding: 10px; background: #FDFBFF; border-radius: 10px;
        margin-bottom: 8px; border: 1px solid #F5EEE4;
    }
    .cart-item-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 6px; }
    .cart-item-name { font-size: 13px; font-weight: 600; color: #212121; line-height: 1.3; }
    .cart-item-remove {
        background: transparent; border: none; color: #94A3B8; cursor: pointer;
        padding: 2px; border-radius: 4px; font-family: inherit;
    }
    .cart-item-remove:hover { color: #DC3545; background: #FDECEA; }
    .cart-item-bottom { display: flex; justify-content: space-between; align-items: center; gap: 10px; }

    .qty-controls {
        display: inline-flex; align-items: center;
        background: #FFFFFF; border: 1px solid #F0E6DD;
        border-radius: 8px; overflow: hidden;
    }
    .qty-btn {
        width: 28px; height: 28px;
        background: transparent; border: none; cursor: pointer;
        font-weight: 700; font-size: 14px; color: #64748B;
        font-family: inherit;
    }
    .qty-btn:hover { background: #EFEBF7; color: #6B5B95; }

    .qty-input {
        width: 44px; height: 28px; border: none;
        border-left: 1px solid #F0E6DD; border-right: 1px solid #F0E6DD;
        background: transparent; text-align: center;
        font-size: 13px; font-weight: 700; color: #212121;
        font-family: inherit; outline: none;
        -moz-appearance: textfield;
    }
    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none; margin: 0;
    }

    .cart-item-total { font-size: 14px; font-weight: 700; color: #2E5A3B; }
    .cart-subtotal-row {
        display: flex; justify-content: space-between; align-items: center;
        padding-top: 12px; border-top: 1px solid #F0E6DD;
        font-size: 14px; color: #64748B;
        flex-shrink: 0;
    }
    .cart-subtotal-row strong { font-size: 18px; color: #212121; font-weight: 700; }

    /* PAYMENT */
    .discount-line {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 14px; margin-top: 10px;
        background: #FFF8E1; border-radius: 8px;
        font-size: 13px; color: #B8860B; font-weight: 600;
    }
    .discount-line strong { color: #B8860B; font-weight: 700; }

    .summary-total-row {
        display: flex; justify-content: space-between; align-items: center;
        padding-top: 16px; margin-top: 6px;
        border-top: 2px solid #D5C9E8;
    }
    .summary-total-row span { font-size: 15px; font-weight: 600; color: #212121; }
    .total-display { font-size: 22px; font-weight: 700; color: #6B5B95; }

    .input-with-prefix { position: relative; }
    .input-with-prefix .prefix {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #94A3B8; font-weight: 600; font-size: 14px; pointer-events: none;
    }
    .input-with-prefix .form-control { padding-left: 32px; }

    .cash-section {
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px dashed #F0E6DD;
    }

    .change-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 12px 14px; background: #E8F5E9; border-radius: 10px;
        margin-top: 6px;
        transition: all 0.2s ease;
    }
    .change-row span { font-size: 14px; font-weight: 600; color: #2E5A3B; }
    .change-display { font-size: 20px; font-weight: 700; color: #2E5A3B; }

    .change-row.insufficient {
        background: #FDECEA;
        border: 1.5px solid #DC3545;
        animation: pulse-warning 1.5s ease-in-out infinite;
    }
    .change-row.insufficient span,
    .change-row.insufficient .change-display {
        color: #C0392B;
        font-weight: 800;
    }

    @keyframes pulse-warning {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.3); }
        50% { box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
    }

    .payment-methods { display: flex; flex-direction: column; gap: 8px; }
    .payment-option {
        display: flex; align-items: center; padding: 10px 14px;
        border: 1.5px solid #F0E6DD; border-radius: 10px;
        cursor: pointer; background: #FFFFFF;
    }
    .payment-option:hover { border-color: #D5C9E8; background: #FDFBFF; }
    .payment-option input[type="radio"] { margin: 0 10px 0 0; accent-color: #6B5B95; cursor: pointer; }
    .payment-option:has(input:checked) { border-color: #6B5B95; background: #EFEBF7; }
    .payment-option:has(input:checked) .payment-label { color: #6B5B95; font-weight: 600; }
    .payment-label { font-size: 14px; color: #212121; font-weight: 500; }

    .discount-radios { display: flex; gap: 8px; }
    .discount-radio {
        flex: 1;
        display: flex; align-items: center; justify-content: center;
        gap: 6px; padding: 10px 8px;
        border: 1.5px solid #F0E6DD; border-radius: 10px;
        cursor: pointer; background: #FFFFFF;
        font-size: 13px; font-weight: 600; color: #64748B;
        transition: all 0.15s ease; text-align: center;
    }
    .discount-radio input[type="radio"] { display: none; }
    .discount-radio:hover { border-color: #D5C9E8; background: #FDFBFF; }
    .discount-radio:has(input:checked) {
        border-color: #6B5B95; background: #EFEBF7; color: #6B5B95;
    }

    .discount-detail-box {
        padding: 14px; margin-bottom: 14px;
        background: #FDFBFF; border: 1px dashed #F0E6DD;
        border-radius: 10px;
    }

    .btn-block {
        width: 100%; display: flex; align-items: center; justify-content: center;
        gap: 8px; padding: 14px; font-size: 15px; font-weight: 600; margin-top: 16px;
    }

    .field-help {
        display: block;
        margin-top: 6px;
        color: #94A3B8;
        font-size: 12px;
    }

    /* =========================================
       MODALS
       ========================================= */
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

    .modal-section {
        margin-bottom: 20px;
    }
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

    .modal-row-label {
        color: #64748B;
    }

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

    .modal-value-change {
        color: #2E5A3B;
        font-weight: 700;
    }

    .modal-value-mono {
        font-family: 'SF Mono', Consolas, monospace;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .modal-items-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .modal-item {
        display: grid;
        grid-template-columns: 1fr auto auto;
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

    .modal-item-qty {
        color: #64748B;
        font-size: 12px;
        white-space: nowrap;
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
    }

    .modal-btn-primary {
        background: #6B5B95;
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(107, 91, 149, 0.3);
    }
    .modal-btn-primary:hover {
        background: #594B7D;
        box-shadow: 0 4px 12px rgba(107, 91, 149, 0.4);
    }

    .modal-btn-secondary {
        background: #F0E6DD;
        color: #212121;
    }
    .modal-btn-secondary:hover {
        background: #E5D5C5;
    }

    /* ERROR MODAL */
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
    }

    .modal-error-footer {
        display: flex;
        justify-content: center;
    }
    .modal-error-footer .modal-btn {
        min-width: 120px;
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .pos-layout {
            grid-template-columns: 1fr;
            height: auto;
            overflow: visible;
        }
        .pos-left, .pos-right {
            height: auto;
            overflow: visible;
        }
        .products-card, .payment-card { overflow: visible; }
        .pos-cart-card { max-height: none; }
    }
    @media (max-width: 640px) {
        .product-table thead th,
        .product-table tbody td { padding: 8px; font-size: 12px; }
        .modal-box { max-width: 100%; }
    }
</style>

<script>
    const allProducts = @json($products);
    const allCustomers = @json($customers);

    const ITEMS_PER_PAGE = 10;
    let currentPage = 1;
    let filteredProducts = [...allProducts];

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
    const discountLine = document.getElementById('discount-line');
    const discountDisplay = document.getElementById('discount-display');

    const totalAmount = document.getElementById('total_amount');
    const moneyReceivedInput = document.getElementById('money_received');
    const changeAmountEl = document.getElementById('change_amount');
    const changeLabel = document.getElementById('change-label');
    const changeRow = document.getElementById('change-row');
    const cashSection = document.getElementById('cash-section');
    const gcashSection = document.getElementById('gcash-section');
    const gcashInput = document.getElementById('gcash_reference');

    const saleForm = document.getElementById('sale-form');
    const customerSelect = document.getElementById('customer_id');
    const cartInputs = document.getElementById('cart-inputs');
    const formCustomerId = document.getElementById('form-customer-id');
    const formDiscountType = document.getElementById('form-discount-type');
    const formDiscountName = document.getElementById('form-discount-name');
    const formDiscountId = document.getElementById('form-discount-id');
    const formPaymentMethod = document.getElementById('form-payment-method');
    const formGcashReference = document.getElementById('form-gcash-reference');

    /* MODAL ELEMENTS */
    const confirmModal = document.getElementById('confirm-modal');
    const errorModal = document.getElementById('error-modal');
    const errorModalMessage = document.getElementById('error-modal-message');
    const modalConfirmBtn = document.getElementById('modal-confirm-btn');

    let cart = [];
    let activeDiscount = 'none';

    /* HELPERS */
    function formatMoney(n) { return '₱' + Number(n || 0).toFixed(2); }
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
        return getCurrentSubtotal() * 0.20;
    }
    function getCurrentTotal() {
        return getCurrentSubtotal() - getCurrentDiscount();
    }

    /* ============================================
       ERROR MODAL
       ============================================ */
    function showError(message) {
        errorModalMessage.textContent = message;
        errorModal.classList.add('open');
    }
    function closeErrorModal() {
        errorModal.classList.remove('open');
    }

    /* ============================================
       CONFIRMATION MODAL
       ============================================ */
    function showConfirmModal() {
        // Populate items
        const itemsList = document.getElementById('modal-items-list');
        itemsList.innerHTML = '';

        cart.forEach(item => {
            const row = document.createElement('div');
            row.className = 'modal-item';
            row.innerHTML = `
                <span class="modal-item-name">${escapeHtml(item.productName)}</span>
                <span class="modal-item-qty">${formatQty(item.quantity)} ${escapeHtml(item.stockUnit)}</span>
                <span class="modal-item-total">${formatMoney(item.quantity * item.unitPrice)}</span>
            `;
            itemsList.appendChild(row);
        });

        // Customer
        const selectedCustomer = customerSelect.options[customerSelect.selectedIndex];
        document.getElementById('modal-customer').textContent =
            selectedCustomer && selectedCustomer.value ? selectedCustomer.textContent.trim() : 'Walk-in Customer';

        // Payment method
        const paymentRadio = document.querySelector('input[name="payment_method"]:checked');
        const paymentValue = paymentRadio ? paymentRadio.value : 'cash';
        document.getElementById('modal-payment-method').textContent =
            paymentValue === 'cash' ? 'Cash' : 'GCash';

        // Subtotal
        const subtotal = getCurrentSubtotal();
        document.getElementById('modal-subtotal').textContent = formatMoney(subtotal);

        // Discount
        const discount = getCurrentDiscount();
        const discountRow = document.getElementById('modal-discount-row');
        if (activeDiscount !== 'none' && discount > 0) {
            discountRow.style.display = 'flex';
            document.getElementById('modal-discount-label').textContent =
                activeDiscount === 'pwd' ? 'PWD Discount (20%)' : 'Senior Discount (20%)';
            document.getElementById('modal-discount').textContent = '− ' + formatMoney(discount);
        } else {
            discountRow.style.display = 'none';
        }

        // Total
        const total = getCurrentTotal();
        document.getElementById('modal-total').textContent = formatMoney(total);

        // Payment detail — cash vs gcash
        const cashReceivedRow = document.getElementById('modal-cash-received-row');
        const changeRow = document.getElementById('modal-change-row');
        const gcashRefRow = document.getElementById('modal-gcash-ref-row');

        if (paymentValue === 'cash') {
            const received = parseFloat(moneyReceivedInput.value) || 0;
            cashReceivedRow.style.display = 'flex';
            changeRow.style.display = 'flex';
            gcashRefRow.style.display = 'none';

            document.getElementById('modal-cash-received').textContent = formatMoney(received);
            document.getElementById('modal-change').textContent = formatMoney(received - total);
        } else {
            cashReceivedRow.style.display = 'none';
            changeRow.style.display = 'none';
            gcashRefRow.style.display = 'flex';

            document.getElementById('modal-gcash-ref').textContent = gcashInput.value.trim() || '—';
        }

        confirmModal.classList.add('open');
    }

    function closeConfirmModal() {
        confirmModal.classList.remove('open');
    }

    /* ============================================
       PRODUCT TABLE
       ============================================ */
    function renderProductTable() {
        if (!productTableBody) return;

        const totalItems = filteredProducts.length;
        const totalPages = Math.max(1, Math.ceil(totalItems / ITEMS_PER_PAGE));

        if (currentPage > totalPages) currentPage = totalPages;

        const startIdx = (currentPage - 1) * ITEMS_PER_PAGE;
        const endIdx = startIdx + ITEMS_PER_PAGE;
        const pageItems = filteredProducts.slice(startIdx, endIdx);

        productTableBody.innerHTML = '';

        if (totalItems === 0) {
            if (noResults) noResults.style.display = 'block';
            if (paginationInfo) paginationInfo.textContent = 'No items';
            if (pageIndicator) pageIndicator.textContent = '0 / 0';
            if (prevPageBtn) prevPageBtn.disabled = true;
            if (nextPageBtn) nextPageBtn.disabled = true;
            return;
        }

        if (noResults) noResults.style.display = 'none';

        pageItems.forEach(function(product) {
            const stock = Number(product.stock) || 0;
            let stockClass = 'product-stock-cell';
            if (stock <= 10) stockClass += ' low';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="product-name-cell">${escapeHtml(product.name)}</td>
                <td class="product-price-cell">${formatMoney(product.price)}</td>
                <td class="${stockClass}">${formatQty(stock)} ${product.unit}</td>
                <td style="text-align: center;">
                    <button type="button" class="add-row-btn" onclick="handleAddProduct(${product.id})">Add</button>
                </td>
            `;
            productTableBody.appendChild(row);
        });

        if (paginationInfo) {
            paginationInfo.textContent = `Showing ${startIdx + 1}–${Math.min(endIdx, totalItems)} of ${totalItems} items`;
        }
        if (pageIndicator) pageIndicator.textContent = `${currentPage} / ${totalPages}`;
        if (prevPageBtn) prevPageBtn.disabled = currentPage === 1;
        if (nextPageBtn) nextPageBtn.disabled = currentPage === totalPages;
    }

    function handleAddProduct(productId) {
        const product = allProducts.find(p => p.id === productId);
        if (!product) return;

        const stock = Number(product.stock) || 0;
        const existing = cart.find(item => item.productId === productId);
        const existingQty = existing ? existing.quantity : 0;

        if (existingQty + 1 > stock) {
            showError(`Only ${formatQty(stock)} ${product.unit} of "${product.name}" available in stock.`);
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
        if (!cartItemsEl) return;

        cartItemsEl.innerHTML = '';

        const totalCount = cart.reduce((s, i) => s + i.quantity, 0);
        if (cartCountEl) cartCountEl.textContent = formatQty(totalCount);

        if (cart.length === 0) {
            cartItemsEl.innerHTML = '<div class="cart-empty">No items yet. Add items from the list.</div>';
            if (subtotalDisplay) subtotalDisplay.textContent = '₱0.00';
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
                        <input type="number" class="qty-input" value="${item.quantity}" min="1" step="1"
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

        if (subtotalDisplay) subtotalDisplay.textContent = formatMoney(subtotal);
        updateTotal();
    }

    function changeQuantity(index, delta) {
        const item = cart[index];
        if (!item) return;

        const newQty = item.quantity + delta;

        if (delta > 0 && newQty > item.maxStock) {
            showError(`Only ${formatQty(item.maxStock)} ${item.stockUnit} of "${item.productName}" available in stock.`);
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
            showError(`Only ${formatQty(item.maxStock)} ${item.stockUnit} of "${item.productName}" available in stock.`);
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
       TOTAL + CHANGE
       ============================================ */
    function updateTotal() {
        const subtotal = getCurrentSubtotal();
        const discount = getCurrentDiscount();
        const total = subtotal - discount;

        if (totalAmount) totalAmount.textContent = formatMoney(total);

        if (activeDiscount !== 'none' && subtotal > 0) {
            if (discountLine) discountLine.style.display = 'flex';
            if (discountDisplay) discountDisplay.textContent = '− ' + formatMoney(discount);
        } else {
            if (discountLine) discountLine.style.display = 'none';
        }

        updateChange();
    }

    function updateChange() {
        const total = getCurrentTotal();
        const received = parseFloat(moneyReceivedInput?.value) || 0;

        if (!changeAmountEl || !changeRow || !changeLabel) return;

        if (received === 0) {
            changeLabel.textContent = 'Change';
            changeAmountEl.textContent = formatMoney(0);
            changeRow.classList.remove('insufficient');
            return;
        }

        const diff = received - total;

        if (diff < 0) {
            changeLabel.textContent = 'Short by';
            changeAmountEl.textContent = formatMoney(Math.abs(diff));
            changeRow.classList.add('insufficient');
        } else {
            changeLabel.textContent = 'Change';
            changeAmountEl.textContent = formatMoney(diff);
            changeRow.classList.remove('insufficient');
        }
    }

    if (moneyReceivedInput) moneyReceivedInput.addEventListener('input', updateChange);

    /* ============================================
       PAYMENT METHOD
       ============================================ */
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'cash') {
                if (cashSection) cashSection.style.display = 'block';
                if (gcashSection) gcashSection.style.display = 'none';
                if (gcashInput) gcashInput.value = '';
            } else {
                if (cashSection) cashSection.style.display = 'none';
                if (gcashSection) gcashSection.style.display = 'block';
                if (moneyReceivedInput) moneyReceivedInput.value = '';
                updateChange();
            }
        });
    });

    if (gcashInput) {
        gcashInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 13);
        });
    }

    /* ============================================
       DISCOUNT
       ============================================ */
    document.querySelectorAll('input[name="discount_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            handleDiscountChange(this.value);
        });
    });

    function handleDiscountChange(type) {
        activeDiscount = type;

        if (type === 'none') {
            if (discountDetails) discountDetails.style.display = 'none';
            if (discountNameInput) discountNameInput.value = '';
            if (discountIdInput) discountIdInput.value = '';
            updateTotal();
            return;
        }

        if (discountDetails) discountDetails.style.display = 'block';

        if (type === 'pwd') {
            if (discountIdLabel) discountIdLabel.textContent = 'PWD ID Number';
            if (discountIdHint) discountIdHint.textContent = 'Format: RR-PPMM-BBB-NNNNNNN (16 digits).';
            if (discountIdInput) {
                discountIdInput.placeholder = '12-3456-789-1234567';
                discountIdInput.setAttribute('maxlength', '19');
                discountIdInput.setAttribute('inputmode', 'numeric');
            }
        } else {
            if (discountIdLabel) discountIdLabel.textContent = 'Senior Citizen ID Number';
            if (discountIdHint) discountIdHint.textContent = 'Formats vary by LGU.';
            if (discountIdInput) {
                discountIdInput.placeholder = 'e.g. QC-12345';
                discountIdInput.setAttribute('maxlength', '30');
                discountIdInput.removeAttribute('inputmode');
            }
        }

        if (customerSelect && customerSelect.value) {
            const customer = allCustomers.find(c => c.id == customerSelect.value);
            if (customer && customer.full_name && discountNameInput) {
                discountNameInput.value = customer.full_name;
            }
        }

        updateTotal();
    }

    function formatPwdId(value) {
        const digits = value.replace(/\D/g, '').slice(0, 16);
        let result = '';
        for (let i = 0; i < digits.length; i++) {
            if (i === 2 || i === 6 || i === 9) result += '-';
            result += digits[i];
        }
        return result;
    }

    function formatSeniorId(value) {
        let cleaned = value.replace(/[^A-Za-z0-9\-\/\s]/g, '');
        cleaned = cleaned.replace(/\s+/g, ' ');
        return cleaned.toUpperCase().slice(0, 30);
    }

    if (discountIdInput) {
        discountIdInput.addEventListener('input', function() {
            if (activeDiscount === 'pwd') {
                this.value = formatPwdId(this.value);
            } else if (activeDiscount === 'senior') {
                this.value = formatSeniorId(this.value);
            }
        });
    }

    /* ============================================
       CUSTOMER SELECT
       ============================================ */
    if (customerSelect) {
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
                    if (discountNameInput) discountNameInput.value = customer.full_name || '';
                    if (discountIdInput) discountIdInput.value = customer.discount_id_number || '';
                }
            }
        });
    }

    /* ============================================
       SEARCH + PAGINATION
       ============================================ */
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            filteredProducts = allProducts.filter(p => p.name.toLowerCase().includes(term));
            currentPage = 1;
            renderProductTable();
        });
    }

    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', function() {
            if (currentPage > 1) { currentPage--; renderProductTable(); }
        });
    }
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', function() {
            const totalPages = Math.max(1, Math.ceil(filteredProducts.length / ITEMS_PER_PAGE));
            if (currentPage < totalPages) { currentPage++; renderProductTable(); }
        });
    }

    /* ============================================
       SUBMIT — validate, show confirm modal, then process
       ============================================ */
    if (saleForm) {
        saleForm.addEventListener('submit', function(event) {
            event.preventDefault();

            // Validation
            if (cart.length === 0) {
                showError('Please add at least one item to the order.');
                return;
            }

            const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
            const paymentValue = selectedPayment ? selectedPayment.value : 'cash';

            if (paymentValue === 'cash') {
                const total = getCurrentTotal();
                const received = parseFloat(moneyReceivedInput?.value) || 0;
                if (received <= 0) {
                    showError('Please enter the amount paid by the customer.');
                    moneyReceivedInput?.focus();
                    return;
                }
                if (received < total) {
                    showError(`Amount paid is less than the total.\n\nTotal: ${formatMoney(total)}\nReceived: ${formatMoney(received)}\nShort by: ${formatMoney(total - received)}`);
                    moneyReceivedInput?.focus();
                    return;
                }
            }

            if (paymentValue === 'gcash') {
                const ref = gcashInput?.value.trim() || '';
                if (!/^\d{13}$/.test(ref)) {
                    showError('GCash reference must be exactly 13 digits.');
                    gcashInput?.focus();
                    return;
                }
            }

            const discountName = discountNameInput?.value.trim() || '';
            const discountId = discountIdInput?.value.trim() || '';

            if (activeDiscount === 'pwd') {
                if (!discountName) {
                    showError('Please enter the name on the PWD ID.');
                    discountNameInput?.focus();
                    return;
                }
                const digits = discountId.replace(/\D/g, '');
                if (digits.length !== 16) {
                    showError('PWD ID must contain exactly 16 digits.');
                    discountIdInput?.focus();
                    return;
                }
            }

            if (activeDiscount === 'senior') {
                if (!discountName) {
                    showError('Please enter the name on the Senior Citizen ID.');
                    discountNameInput?.focus();
                    return;
                }
                if (discountId.length < 4) {
                    showError('Senior Citizen ID must be at least 4 characters.');
                    discountIdInput?.focus();
                    return;
                }
            }

            // All good — show confirmation modal
            showConfirmModal();
        });
    }

    /* ============================================
       CONFIRM → ACTUALLY SUBMIT
       ============================================ */
    if (modalConfirmBtn) {
        modalConfirmBtn.addEventListener('click', function() {
            // Close the modal first
            closeConfirmModal();

            const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
            const paymentValue = selectedPayment ? selectedPayment.value : 'cash';

            const discountName = discountNameInput?.value.trim() || '';
            const discountId = discountIdInput?.value.trim() || '';

            formCustomerId.value = customerSelect?.value || '';
            formDiscountType.value = activeDiscount;
            formDiscountName.value = activeDiscount === 'none' ? '' : discountName;
            formDiscountId.value = activeDiscount === 'none' ? '' : discountId;
            formPaymentMethod.value = paymentValue;
            formGcashReference.value = paymentValue === 'gcash' ? gcashInput?.value.trim() : '';

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

            // Disable button to prevent double submit
            modalConfirmBtn.disabled = true;
            modalConfirmBtn.textContent = 'Processing...';

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
                    showError(data.message || 'Something went wrong processing the sale.');
                    modalConfirmBtn.disabled = false;
                    modalConfirmBtn.textContent = 'Confirm & Process Sale';
                }
            })
            .catch(error => {
                console.error(error);
                showError('Could not process the sale. Please try again.');
                modalConfirmBtn.disabled = false;
                modalConfirmBtn.textContent = 'Confirm & Process Sale';
            });
        });
    }

    /* Close modal by clicking outside */
    if (confirmModal) {
        confirmModal.addEventListener('click', function(e) {
            if (e.target === confirmModal) closeConfirmModal();
        });
    }
    if (errorModal) {
        errorModal.addEventListener('click', function(e) {
            if (e.target === errorModal) closeErrorModal();
        });
    }

    /* ESC key closes modals */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeConfirmModal();
            closeErrorModal();
        }
    });

    /* INIT */
    renderProductTable();
    renderCart();
</script>

@endsection