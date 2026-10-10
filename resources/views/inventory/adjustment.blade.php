@extends('layouts.app')

@php
$tab = request('tab', 'adjust');
$retail = $product->inventory->firstWhere('reserve_type', 'retail');
$production = $product->inventory->firstWhere('reserve_type', 'production');

$showRetail = in_array($product->stock_purpose, ['retail', 'both']);
$showProduction = in_array($product->stock_purpose, ['production', 'both']);

// Transfer only makes sense when the product can have both pools
$canTransfer = $product->stock_purpose === 'both' && $retail && $production;

$tabParam = $product->item_type === 'material' ? 'material' : 'product';

if ($tab === 'transfer' && !$canTransfer) {
$tab = 'adjust';
}

$purposeLabel = $product->stock_purpose === 'retail' ? 'Retail only'
: ($product->stock_purpose === 'production' ? 'Production only' : 'Retail & Production');
@endphp

@section('title', 'Adjust Stock')

@section('content')

<div class="form-page">

    <div class="page-header">
        <div>
            <h1>Adjust Stock</h1>
            <p>Correct recorded quantities or move stock between allocations.</p>
        </div>

        <a href="{{ route('products.index', ['item_type' => $tabParam]) }}" class="btn btn-secondary">
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

        <div class="stock-summary">
            <div class="stock-box">
                <span class="stock-label">Retail Stock</span>
                <span class="stock-value">
                    {{ $retail ? (float) $retail->current_quantity : 0 }}
                    <span class="stock-unit">{{ $product->stock_unit }}</span>
                </span>
            </div>

            <div class="stock-box">
                <span class="stock-label">Production Stock</span>
                <span class="stock-value">
                    {{ $production ? (float) $production->current_quantity : 0 }}
                    <span class="stock-unit">{{ $product->stock_unit }}</span>
                </span>
            </div>
        </div>

        <div class="adjust-tabs">
            <a href="{{ route('inventory.adjustment', ['product' => $product, 'tab' => 'adjust']) }}"
                class="adjust-tab {{ $tab === 'adjust' ? 'active' : '' }}">
                Correct Quantity
            </a>

            @if($canTransfer)
            <a href="{{ route('inventory.adjustment', ['product' => $product, 'tab' => 'transfer']) }}"
                class="adjust-tab {{ $tab === 'transfer' ? 'active' : '' }}">
                Transfer Allocation
            </a>
            @endif
        </div>

        @if($tab === 'adjust')
        <form action="{{ route('inventory.adjustment.store', $product) }}" method="POST" id="adjust-form">
            @csrf

            @php
            $allowedAllocations = $product->inventory->filter(function ($inv) use ($showRetail, $showProduction) {
            if ($inv->reserve_type === 'retail') return $showRetail;
            if ($inv->reserve_type === 'production') return $showProduction;
            return false;
            });
            @endphp

            @if($allowedAllocations->count() > 1)
            <div class="form-group">
                <label for="reserve_type">Stock Allocation <span class="req">*</span></label>
                <select id="reserve_type" name="reserve_type" class="form-control" required>
                    <option value="">— Select stock allocation —</option>
                    @foreach($allowedAllocations as $inventory)
                    <option value="{{ $inventory->reserve_type }}"
                        data-quantity="{{ $inventory->current_quantity }}"
                        {{ old('reserve_type') === $inventory->reserve_type ? 'selected' : '' }}>
                        {{ ucfirst($inventory->reserve_type) }} Stock
                    </option>
                    @endforeach
                </select>
                <small class="field-help">
                    Choose whether you are correcting retail stock or production stock.
                </small>
            </div>
            @else
            @php
            $single = $allowedAllocations->first();
            @endphp

            @if($single)
            <div class="form-group">
                <label>Stock Allocation</label>
                <input type="text" class="form-control readonly-field"
                    value="{{ ucfirst($single->reserve_type) }} Stock" readonly>
                <input type="hidden" id="reserve_type" name="reserve_type"
                    value="{{ $single->reserve_type }}"
                    data-quantity="{{ $single->current_quantity }}">
                <small class="field-help">
                    This item only tracks {{ $single->reserve_type }} stock.
                </small>
            </div>
            @else
            <p class="empty-text" style="padding: 20px 0;">
                No stock allocation available for this item.
            </p>
            @endif
            @endif

            <div class="form-grid">
                <div class="form-group">
                    <label>Current Recorded Quantity</label>
                    <input type="text" id="current_quantity" class="form-control readonly-field" value="—" readonly>
                </div>

                <div class="form-group">
                    <label for="actual_quantity">Actual Physical Quantity <span class="req">*</span></label>
                    <input type="number" id="actual_quantity" name="actual_quantity"
                        class="form-control" min="0" step="0.01"
                        value="{{ old('actual_quantity') }}"
                        placeholder="Enter quantity" required>
                    <small class="field-help">Enter the quantity physically counted in the shop.</small>
                </div>
            </div>

            <div class="form-group">
                <label>Adjustment</label>
                <input type="text" id="adjustment" class="form-control readonly-field" value="—" readonly>
            </div>

            <div class="form-group">
                <label for="notes">Reason / Notes</label>
                <textarea id="notes" name="notes" class="form-control" rows="4" maxlength="1000"
                    placeholder="Example: Damaged materials, missing stock, physical count correction">{{ old('notes') }}</textarea>
                <small class="field-help">Optional explanation for the stock adjustment.</small>
            </div>

            <div class="form-actions">
                <a href="{{ route('products.index', ['item_type' => $tabParam]) }}" class="action-btn-secondary">Cancel</a>
                <button type="submit" class="action-btn-primary">Save Adjustment</button>
            </div>
        </form>

        @else
        <form action="{{ route('inventory.transfer.store', $product) }}" method="POST" id="transfer-form">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label for="from">Transfer From <span class="req">*</span></label>
                    <select id="from" name="from" class="form-control" required>
                        <option value="">— Select source —</option>
                        @if($retail)
                        <option value="retail" {{ old('from') === 'retail' ? 'selected' : '' }}>
                            Retail Stock ({{ (float) $retail->current_quantity }} {{ $product->stock_unit }})
                        </option>
                        @endif
                        @if($production)
                        <option value="production" {{ old('from') === 'production' ? 'selected' : '' }}>
                            Production Stock ({{ (float) $production->current_quantity }} {{ $product->stock_unit }})
                        </option>
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="to">Transfer To <span class="req">*</span></label>
                    <select id="to" name="to" class="form-control" required>
                        <option value="">— Select destination —</option>
                        <option value="retail" {{ old('to') === 'retail' ? 'selected' : '' }}>Retail Stock</option>
                        <option value="production" {{ old('to') === 'production' ? 'selected' : '' }}>Production Stock</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity to Transfer <span class="req">*</span></label>
                <input type="number" id="quantity" name="quantity" class="form-control"
                    min="0.01" step="0.01" value="{{ old('quantity') }}"
                    placeholder="Enter quantity" required>
                <div class="input-feedback" id="quantity-feedback">
                    <span class="feedback-status" id="quantity-status"></span>
                    <span class="feedback-count" id="quantity-available"></span>
                </div>
                <small class="field-help">
                    Must not exceed the available stock in the source allocation.
                </small>
            </div>

            <div class="form-group">
                <label for="notes">Reason / Notes</label>
                <textarea id="notes" name="notes" class="form-control" rows="3" maxlength="1000"
                    placeholder="e.g. Reallocating reserved materials for upcoming orders">{{ old('notes') }}</textarea>
            </div>

            <div class="form-actions">
                <a href="{{ route('products.index', ['item_type' => $tabParam]) }}" class="action-btn-secondary">Cancel</a>
                <button type="submit" class="action-btn-primary">Transfer Stock</button>
            </div>
        </form>
        @endif

    </div>

</div>

{{-- ============================================
     CONFIRMATION MODAL (shared)
     ============================================ --}}
<div class="modal-overlay" id="confirm-modal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title" id="modal-title">Confirm</h3>
            <p class="modal-subtitle" id="modal-subtitle">Review the details below.</p>
        </div>

        <div class="modal-body" id="modal-body"></div>

        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeConfirmModal()">
                Cancel
            </button>
            <button type="button" class="modal-btn modal-btn-primary" id="modal-confirm-btn">
                Confirm
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

    .stock-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stock-box {
        padding: 16px 20px;
        background: #FEFCF9;
        border: 1.5px solid #F0E6DD;
        border-radius: 10px;
    }

    .stock-label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 6px;
    }

    .stock-value {
        font-size: 22px;
        font-weight: 700;
        color: #2E5A3B;
        display: block;
        text-align: right;
    }

    .stock-unit {
        font-size: 13px;
        font-weight: 500;
        color: #64748B;
    }

    .adjust-tabs {
        display: flex;
        gap: 28px;
        margin-bottom: 24px;
        border-bottom: 1.5px solid #F0E6DD;
    }

    .adjust-tab {
        display: inline-flex;
        align-items: center;
        padding: 10px 2px;
        text-decoration: none;
        font-size: 15px;
        font-weight: 600;
        color: #94A3B8;
        border-bottom: 2px solid transparent;
        margin-bottom: -1.5px;
        transition: all 0.15s ease;
    }

    .adjust-tab:hover {
        color: #6B5B95;
    }

    .adjust-tab.active {
        color: #6B5B95;
        border-bottom-color: #6B5B95;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px 24px;
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

    .readonly-field {
        background: #FEFCF9 !important;
        color: #64748B;
        cursor: default;
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

    .feedback-count {
        font-family: 'SF Mono', Consolas, monospace;
        font-weight: 700;
        letter-spacing: 0.5px;
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

    .modal-overlay.open {
        display: flex;
    }

    @keyframes fade-in {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .modal-box {
        background: #FFFFFF;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        width: 100%;
        max-width: 480px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: slide-up 0.2s ease;
    }

    @keyframes slide-up {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
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
    }

    .modal-row:last-child {
        border-bottom: none;
    }

    .modal-row-label {
        color: #64748B;
    }

    .modal-row-value {
        color: #212121;
        font-weight: 600;
        text-align: right;
    }

    .modal-row-value.mono {
        font-family: 'SF Mono', Consolas, monospace;
        letter-spacing: 0.5px;
    }

    .modal-row-value.increase {
        color: #2E5A3B;
        font-weight: 700;
    }

    .modal-row-value.decrease {
        color: #DC3545;
        font-weight: 700;
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

    .modal-btn-primary:hover {
        background: #594B7D;
    }

    .modal-btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
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
        white-space: pre-line;
    }

    .modal-error-footer {
        display: flex;
        justify-content: center;
    }

    .modal-error-footer .modal-btn {
        min-width: 120px;
    }

    @media (max-width: 640px) {
        .adjust-tabs {
            gap: 20px;
        }

        .adjust-tab {
            font-size: 13px;
        }

        .modal-box {
            max-width: 100%;
        }

        .form-actions .action-btn-primary,
        .form-actions .action-btn-secondary {
            flex: 1;
            min-width: 0;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        /* ============================================
           MODAL HELPERS
           ============================================ */
        const confirmModal = document.getElementById('confirm-modal');
        const confirmTitle = document.getElementById('modal-title');
        const confirmSubtitle = document.getElementById('modal-subtitle');
        const confirmBody = document.getElementById('modal-body');
        const confirmBtn = document.getElementById('modal-confirm-btn');

        const errorModal = document.getElementById('error-modal');
        const errorMessage = document.getElementById('error-modal-message');

        let pendingSubmit = null;

        window.closeConfirmModal = function() {
            confirmModal.classList.remove('open');
            pendingSubmit = null;
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm';
        };

        window.closeErrorModal = function() {
            errorModal.classList.remove('open');
        };

        function showError(message) {
            errorMessage.textContent = message;
            errorModal.classList.add('open');
        }

        function openConfirmModal(title, subtitle, bodyHtml, buttonLabel, onSubmit) {
            confirmTitle.textContent = title;
            confirmSubtitle.textContent = subtitle;
            confirmBody.innerHTML = bodyHtml;
            confirmBtn.textContent = buttonLabel;
            confirmBtn.disabled = false;
            pendingSubmit = onSubmit;
            confirmModal.classList.add('open');
        }

        confirmBtn.addEventListener('click', function() {
            if (typeof pendingSubmit === 'function') {
                const submit = pendingSubmit;
                confirmBtn.disabled = true;
                confirmBtn.textContent = 'Processing...';
                submit();
            }
        });

        /* Close on overlay click */
        confirmModal.addEventListener('click', function(e) {
            if (e.target === confirmModal) closeConfirmModal();
        });
        errorModal.addEventListener('click', function(e) {
            if (e.target === errorModal) closeErrorModal();
        });

        /* Close on ESC */
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeConfirmModal();
                closeErrorModal();
            }
        });

        function row(label, value, valueClass) {
            const cls = valueClass ? ` ${valueClass}` : '';
            return `<div class="modal-row">
            <span class="modal-row-label">${label}</span>
            <span class="modal-row-value${cls}">${value}</span>
        </div>`;
        }

        /* ============================================
           ADJUST TAB
           ============================================ */
        @if($tab === 'adjust')
        const reserveType = document.getElementById('reserve_type');
        const currentQuantity = document.getElementById('current_quantity');
        const actualQuantity = document.getElementById('actual_quantity');
        const adjustment = document.getElementById('adjustment');
        const adjustForm = document.getElementById('adjust-form');

        function formatQty(n) {
            return parseFloat(Number(n || 0).toFixed(2)).toString();
        }

        function updateAdjustment() {
            const selectedOption = reserveType.options[reserveType.selectedIndex];

            if (!selectedOption || !selectedOption.dataset.quantity) {
                currentQuantity.value = '—';
                adjustment.value = '—';
                return;
            }

            const current = parseFloat(selectedOption.dataset.quantity);
            const actual = parseFloat(actualQuantity.value);

            currentQuantity.value = formatQty(current);

            if (Number.isNaN(actual)) {
                adjustment.value = '—';
                return;
            }

            const difference = actual - current;
            adjustment.value = difference > 0 ?
                '+' + formatQty(difference) :
                formatQty(difference);
        }

        reserveType.addEventListener('change', updateAdjustment);
        actualQuantity.addEventListener('input', updateAdjustment);
        updateAdjustment();

        adjustForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!reserveType.value) {
                showError('Please select a stock allocation.');
                return;
            }

            const actual = parseFloat(actualQuantity.value);
            if (Number.isNaN(actual) || actual < 0) {
                showError('Please enter a valid physical quantity (0 or greater).');
                actualQuantity.focus();
                return;
            }

            const selectedOption = reserveType.options[reserveType.selectedIndex];
            const current = parseFloat(selectedOption.dataset.quantity || 0);
            const difference = actual - current;
            const allocationLabel = selectedOption.text.trim();

            const unit = @json($product -> stock_unit);
            const productLabel = @json($product -> display_name);

            const diffClass = difference > 0 ? 'increase' : (difference < 0 ? 'decrease' : '');
            const diffText = difference > 0 ?
                '+' + formatQty(difference) + ' ' + unit :
                (difference < 0 ? formatQty(difference) + ' ' + unit : 'No change');

            const bodyHtml = `
            ${row('Item', productLabel)}
            ${row('Allocation', allocationLabel)}
            ${row('Recorded', formatQty(current) + ' ' + unit)}
            ${row('Actual', formatQty(actual) + ' ' + unit)}
            ${row('Adjustment', diffText, diffClass)}
        `;

            openConfirmModal(
                'Confirm Adjustment',
                'Apply this stock correction?',
                bodyHtml,
                'Save Adjustment',
                () => adjustForm.submit()
            );
        });
        @endif

        /* ============================================
           TRANSFER TAB
           ============================================ */
        @if($tab === 'transfer')
        const STOCK_UNIT = @json($product -> stock_unit);
        const fromSelect = document.getElementById('from');
        const toSelect = document.getElementById('to');
        const quantityInput = document.getElementById('quantity');
        const quantityFeedback = document.getElementById('quantity-feedback');
        const quantityStatus = document.getElementById('quantity-status');
        const quantityAvailable = document.getElementById('quantity-available');
        const transferForm = document.getElementById('transfer-form');

        const RETAIL_AVAILABLE = {
            {
                $retail ? (float) $retail -> current_quantity : 0
            }
        };
        const PRODUCTION_AVAILABLE = {
            {
                $production ? (float) $production -> current_quantity : 0
            }
        };

        function getSourceAvailable() {
            if (fromSelect.value === 'retail') return RETAIL_AVAILABLE;
            if (fromSelect.value === 'production') return PRODUCTION_AVAILABLE;
            return 0;
        }

        function syncTransferOptions() {
            const from = fromSelect.value;
            Array.from(toSelect.options).forEach(opt => {
                if (!opt.value) return;
                opt.disabled = (opt.value === from);
            });
            if (toSelect.value === from) toSelect.value = '';
            validateQuantity();
        }

        function validateQuantity() {
            const available = getSourceAvailable();
            const qty = parseFloat(quantityInput.value || 0);

            quantityFeedback.classList.remove('valid', 'invalid', 'neutral');

            if (!fromSelect.value) {
                quantityFeedback.classList.remove('visible');
                return true;
            }

            quantityInput.max = available;

            if (qty <= 0) {
                quantityFeedback.classList.remove('visible');
                return true;
            }

            if (qty > available) {
                quantityFeedback.classList.add('visible', 'invalid');
                quantityStatus.textContent = 'Exceeds available stock';
                quantityAvailable.textContent = `Max ${parseFloat(available.toFixed(2))}`;
                return false;
            }

            quantityFeedback.classList.add('visible', 'valid');
            quantityStatus.textContent = 'Within available stock';
            quantityAvailable.textContent = `${parseFloat(qty.toFixed(2))} / ${parseFloat(available.toFixed(2))}`;
            return true;
        }

        fromSelect.addEventListener('change', syncTransferOptions);
        quantityInput.addEventListener('input', validateQuantity);
        syncTransferOptions();

        transferForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const available = getSourceAvailable();
            const qty = parseFloat(quantityInput.value || 0);

            if (!fromSelect.value) {
                showError('Please select a source allocation.');
                return;
            }
            if (!toSelect.value) {
                showError('Please select a destination allocation.');
                return;
            }
            if (qty <= 0) {
                showError('Enter a valid quantity to transfer.');
                quantityInput.focus();
                return;
            }
            if (qty > available) {
                showError(`Cannot transfer ${qty} ${STOCK_UNIT}.\nOnly ${available} ${STOCK_UNIT} available in ${fromSelect.value} stock.`);
                quantityInput.focus();
                return;
            }

            const fromLabel = fromSelect.options[fromSelect.selectedIndex].text.trim();
            const toLabel = toSelect.options[toSelect.selectedIndex].text.trim();
            const productLabel = @json($product -> display_name);

            const bodyHtml = `
            ${row('Item', productLabel)}
            ${row('From', fromLabel)}
            ${row('To', toLabel)}
            ${row('Quantity', qty + ' ' + STOCK_UNIT, 'increase')}
        `;

            openConfirmModal(
                'Confirm Transfer',
                'Move this stock between allocations?',
                bodyHtml,
                'Transfer Stock',
                () => transferForm.submit()
            );
        });
        @endif
    });
</script>

@endsection