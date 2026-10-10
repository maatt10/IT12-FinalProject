@extends('layouts.app')

@php
    $showRetail = in_array($product->stock_purpose, ['retail', 'both']);
    $showProduction = in_array($product->stock_purpose, ['production', 'both']);
    $purposeLabel = $product->stock_purpose === 'retail' ? 'Retail only'
        : ($product->stock_purpose === 'production' ? 'Production only' : 'Retail & Production');
@endphp

@section('title', 'Set Initial Stock')

@section('content')

<div class="form-page">

    <div class="page-header">
        <div>
            <h1>Set Initial Stock</h1>
            <p>Enter the existing physical stock for this product.</p>
        </div>

        <a href="{{ route('products.index', ['item_type' => $product->item_type === 'material' ? 'material' : 'product']) }}" class="btn btn-secondary">
            ← Back to Inventory
        </a>
    </div>

    <div class="card">

        <h2 class="card-heading">
            {{ $product->name }}
            @if($product->variation)
                <span style="color: #94A3B8; font-weight: 500;">— {{ $product->variation }}</span>
            @endif
        </h2>

        <p style="font-size: 13px; color: #64748B; margin-top: -10px; margin-bottom: 20px;">
            Stock Unit: <strong style="color: #212121;">{{ $product->stock_unit }}</strong>
            <span style="color: #CBD5E1; margin: 0 6px;">·</span>
            Stock Purpose: <strong style="color: #6B5B95;">{{ $purposeLabel }}</strong>
        </p>

        @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin-left: 20px;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('inventory.initial-stock.store', $product) }}" method="POST" id="initial-stock-form">
            @csrf

            @if($showRetail)
                <div class="form-group">
                    <label for="retail_quantity">Retail Stock <span class="req">*</span></label>
                    <input
                        type="number"
                        id="retail_quantity"
                        name="retail_quantity"
                        class="form-control"
                        min="0"
                        step="0.01"
                        value="{{ old('retail_quantity', 0) }}"
                        required>
                    <small class="field-help">
                        Stock available for direct customer sales.
                    </small>
                </div>
            @else
                <input type="hidden" name="retail_quantity" value="0">
            @endif

            @if($showProduction)
                <div class="form-group">
                    <label for="production_quantity">Production Stock <span class="req">*</span></label>
                    <input
                        type="number"
                        id="production_quantity"
                        name="production_quantity"
                        class="form-control"
                        min="0"
                        step="0.01"
                        value="{{ old('production_quantity', 0) }}"
                        required>
                    <small class="field-help">
                        Stock reserved for bouquet or product production.
                    </small>
                </div>
            @else
                <input type="hidden" name="production_quantity" value="0">
            @endif

            <div class="form-actions">
                <a href="{{ route('products.index', ['item_type' => $product->item_type === 'material' ? 'material' : 'product']) }}" class="action-btn-secondary">Cancel</a>
                <button type="submit" class="action-btn-primary">Save Initial Stock</button>
            </div>

        </form>

    </div>

</div>

{{-- ============================================
     CONFIRMATION MODAL
     ============================================ --}}
<div class="modal-overlay" id="confirm-modal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Confirm Initial Stock</h3>
            <p class="modal-subtitle">Review the quantities before saving.</p>
        </div>

        <div class="modal-body">
            <div class="modal-row">
                <span class="modal-row-label">Item</span>
                <span class="modal-row-value" id="modal-product">—</span>
            </div>

            @if($showRetail)
                <div class="modal-row">
                    <span class="modal-row-label">Retail Stock</span>
                    <span class="modal-row-value" id="modal-retail">—</span>
                </div>
            @endif

            @if($showProduction)
                <div class="modal-row">
                    <span class="modal-row-label">Production Stock</span>
                    <span class="modal-row-value" id="modal-production">—</span>
                </div>
            @endif
        </div>

        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeConfirmModal()">
                Cancel
            </button>
            <button type="button" class="modal-btn modal-btn-primary" id="modal-confirm-btn">
                Save Initial Stock
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
    .card-heading {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
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
        line-height: 1.4;
    }

    /* ===============================
       CONSISTENT FORM ACTIONS
       =============================== */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 20px;
        margin-top: 10px;
        border-top: 1px solid #F0E6DD;
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
    .action-btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .action-btn-secondary {
        background: #F0E6DD;
        color: #212121;
    }
    .action-btn-secondary:hover {
        background: #E5D5C5;
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
        max-width: 460px;
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

    .modal-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 14px;
        border-bottom: 1px dashed #F0E6DD;
        gap: 12px;
    }
    .modal-row:last-child { border-bottom: none; }

    .modal-row-label { color: #64748B; }
    .modal-row-value {
        color: #212121;
        font-weight: 700;
        text-align: right;
        font-variant-numeric: tabular-nums;
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
    .modal-btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .modal-btn-secondary {
        background: #F0E6DD;
        color: #212121;
    }
    .modal-btn-secondary:hover { background: #E5D5C5; }

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
        white-space: pre-line;
    }

    .modal-error-footer { display: flex; justify-content: center; }
    .modal-error-footer .modal-btn { min-width: 120px; }

    @media (max-width: 640px) {
        .modal-box { max-width: 100%; }
        .form-actions .action-btn-primary,
        .form-actions .action-btn-secondary { flex: 1; min-width: 0; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('initial-stock-form');
    const retailInput = document.getElementById('retail_quantity');
    const productionInput = document.getElementById('production_quantity');

    const SHOW_RETAIL = @json($showRetail);
    const SHOW_PRODUCTION = @json($showProduction);

    const confirmModal = document.getElementById('confirm-modal');
    const confirmBtn = document.getElementById('modal-confirm-btn');
    const errorModal = document.getElementById('error-modal');
    const errorMessage = document.getElementById('error-modal-message');

    const stockUnit = @json($product->stock_unit);
    const productName = @json($product->display_name);

    function formatQty(n) {
        const num = parseFloat(n || 0);
        return parseFloat(num.toFixed(2)).toString();
    }

    window.closeConfirmModal = function () {
        confirmModal.classList.remove('open');
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Save Initial Stock';
    };

    window.closeErrorModal = function () {
        errorModal.classList.remove('open');
    };

    function showError(message) {
        errorMessage.textContent = message;
        errorModal.classList.add('open');
    }

    /* Overlay click + ESC to close */
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

    /* SUBMIT */
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        let retail = 0;
        let production = 0;

        if (SHOW_RETAIL) {
            retail = parseFloat(retailInput.value);
            if (Number.isNaN(retail) || retail < 0) {
                showError('Please enter a valid retail quantity (0 or greater).');
                retailInput.focus();
                return;
            }
        }

        if (SHOW_PRODUCTION) {
            production = parseFloat(productionInput.value);
            if (Number.isNaN(production) || production < 0) {
                showError('Please enter a valid production quantity (0 or greater).');
                productionInput.focus();
                return;
            }
        }

        if (retail === 0 && production === 0) {
            showError('Enter at least one non-zero quantity to set initial stock.');
            return;
        }

        document.getElementById('modal-product').textContent = productName;

        const retailRow = document.getElementById('modal-retail');
        if (retailRow) retailRow.textContent = formatQty(retail) + ' ' + stockUnit;

        const productionRow = document.getElementById('modal-production');
        if (productionRow) productionRow.textContent = formatQty(production) + ' ' + stockUnit;

        confirmModal.classList.add('open');
    });

    confirmBtn.addEventListener('click', function () {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Saving...';
        form.submit();
    });
});
</script>

@endsection