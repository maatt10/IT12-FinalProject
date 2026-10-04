@extends('layouts.app')

@php
    $tab = request('tab', 'adjust');
    $retail = $product->inventory->firstWhere('reserve_type', 'retail');
    $production = $product->inventory->firstWhere('reserve_type', 'production');
    $hasAnyAllocation = $retail || $production;
    $tabParam = $product->item_type === 'material' ? 'material' : 'product';

    if ($tab === 'transfer' && !$hasAnyAllocation) {
        $tab = 'adjust';
    }
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

            @if($hasAnyAllocation)
                <a href="{{ route('inventory.adjustment', ['product' => $product, 'tab' => 'transfer']) }}"
                   class="adjust-tab {{ $tab === 'transfer' ? 'active' : '' }}">
                    Transfer Allocation
                </a>
            @endif
        </div>

        @if($tab === 'adjust')
            <form action="{{ route('inventory.adjustment.store', $product) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="reserve_type">Stock Allocation <span class="req">*</span></label>
                    <select id="reserve_type" name="reserve_type" class="form-control" required>
                        <option value="">— Select stock allocation —</option>
                        @foreach($product->inventory as $inventory)
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
                    <a href="{{ route('products.index', ['item_type' => $tabParam]) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Adjustment</button>
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
                    <a href="{{ route('products.index', ['item_type' => $tabParam]) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Transfer Stock</button>
                </div>
            </form>
        @endif

    </div>

</div>

<style>
    .card-heading {
        font-size: 16px; font-weight: 700; color: #212121;
        margin-bottom: 18px;
        display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    }

    .stock-summary {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px; margin-bottom: 24px;
    }
    .stock-box {
        padding: 16px 20px; background: #FEFCF9;
        border: 1.5px solid #F0E6DD; border-radius: 10px;
    }
    .stock-label {
        display: block; font-size: 11px; font-weight: 600;
        color: #94A3B8; text-transform: uppercase;
        letter-spacing: 1px; margin-bottom: 6px;
    }
    .stock-value {
        font-size: 22px; font-weight: 700; color: #2E5A3B;
        display: block; text-align: right;
    }
    .stock-unit { font-size: 13px; font-weight: 500; color: #64748B; }

    .adjust-tabs {
        display: flex; gap: 28px; margin-bottom: 24px;
        border-bottom: 1.5px solid #F0E6DD;
    }
    .adjust-tab {
        display: inline-flex; align-items: center;
        padding: 10px 2px; text-decoration: none;
        font-size: 15px; font-weight: 600;
        color: #94A3B8; border-bottom: 2px solid transparent;
        margin-bottom: -1.5px; transition: all 0.15s ease;
    }
    .adjust-tab:hover { color: #E85D75; }
    .adjust-tab.active { color: #E85D75; border-bottom-color: #E85D75; }

    .form-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px 24px;
    }

    .req { color: #E85D75; margin-left: 2px; }

    .field-help {
        display: block; margin-top: 6px;
        color: #94A3B8; font-size: 12px; line-height: 1.4;
    }

    .readonly-field {
        background: #FEFCF9 !important;
        color: #64748B; cursor: default;
    }

    .form-actions {
        display: flex; justify-content: flex-end; gap: 10px;
        padding-top: 20px; margin-top: 10px;
        border-top: 1px solid #F0E6DD;
    }

    .input-feedback {
        display: none; align-items: center; justify-content: space-between;
        gap: 10px; margin-top: 6px; padding: 5px 10px;
        border-radius: 6px; font-size: 11px; font-weight: 600;
    }
    .input-feedback.visible { display: flex; }
    .input-feedback.valid { background: #E8F5E9; color: #2E5A3B; }
    .input-feedback.invalid { background: #FDECEA; color: #C0392B; }
    .input-feedback.neutral { background: #F1F5F9; color: #64748B; }
    .feedback-count {
        font-family: 'SF Mono', Consolas, monospace;
        font-weight: 700; letter-spacing: 0.5px;
    }

    @media (max-width: 640px) {
        .adjust-tabs { gap: 20px; }
        .adjust-tab { font-size: 13px; }
    }
</style>

<script>
@if($tab === 'adjust')
    const reserveType = document.getElementById('reserve_type');
    const currentQuantity = document.getElementById('current_quantity');
    const actualQuantity = document.getElementById('actual_quantity');
    const adjustment = document.getElementById('adjustment');

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

        adjustment.value = difference > 0
            ? '+' + formatQty(difference)
            : formatQty(difference);
    }

    reserveType.addEventListener('change', updateAdjustment);
    actualQuantity.addEventListener('input', updateAdjustment);
    updateAdjustment();
@endif

@if($tab === 'transfer')
    const STOCK_UNIT = @json($product->stock_unit);
    const fromSelect = document.getElementById('from');
    const toSelect = document.getElementById('to');
    const quantityInput = document.getElementById('quantity');
    const quantityFeedback = document.getElementById('quantity-feedback');
    const quantityStatus = document.getElementById('quantity-status');
    const quantityAvailable = document.getElementById('quantity-available');
    const transferForm = document.getElementById('transfer-form');

    const RETAIL_AVAILABLE = {{ $retail ? (float) $retail->current_quantity : 0 }};
    const PRODUCTION_AVAILABLE = {{ $production ? (float) $production->current_quantity : 0 }};

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

    transferForm.addEventListener('submit', function (e) {
        const available = getSourceAvailable();
        const qty = parseFloat(quantityInput.value || 0);

        if (!fromSelect.value) {
            e.preventDefault();
            alert('Please select a source allocation.');
            return;
        }
        if (!toSelect.value) {
            e.preventDefault();
            alert('Please select a destination allocation.');
            return;
        }
        if (qty <= 0) {
            e.preventDefault();
            alert('Enter a valid quantity.');
            quantityInput.focus();
            return;
        }
        if (qty > available) {
            e.preventDefault();
            alert(`Cannot transfer ${qty}. Only ${available} ${STOCK_UNIT} available in ${fromSelect.value} stock.`);
            quantityInput.focus();
            return;
        }
    });
@endif
</script>

@endsection