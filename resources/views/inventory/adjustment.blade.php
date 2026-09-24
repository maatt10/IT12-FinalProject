@extends('layouts.app')

@section('title', 'Adjust Stock')

@section('content')

<div class="page-header">
    <div>
        <h1>Adjust Stock</h1>
        <p>Update inventory based on the actual physical stock count.</p>
    </div>

    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
        ← Back to Inventory
    </a>
</div>

<div class="card" style="max-width: 720px;">

    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
        </svg>
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

    <form action="{{ route('inventory.adjustment.store', $product) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="reserve_type">Stock Allocation <span class="req">*</span></label>
            <select id="reserve_type" name="reserve_type" class="form-control" required>
                <option value="">— Select stock allocation —</option>
                @foreach($product->inventory as $inventory)
                    <option
                        value="{{ $inventory->reserve_type }}"
                        data-quantity="{{ $inventory->current_quantity }}"
                        {{ old('reserve_type') === $inventory->reserve_type ? 'selected' : '' }}>
                        {{ ucfirst($inventory->reserve_type) }} Stock
                    </option>
                @endforeach
            </select>
            <small style="color: #94A3B8; font-size: 12px;">
                Choose whether you are adjusting retail stock or production stock.
            </small>
        </div>

        <div class="form-grid">

            <div class="form-group">
                <label>Current Recorded Quantity</label>
                <input type="text" id="current_quantity" class="form-control readonly-field" value="—" readonly>
            </div>

            <div class="form-group">
                <label for="actual_quantity">Actual Physical Quantity <span class="req">*</span></label>
                <input
                    type="number"
                    id="actual_quantity"
                    name="actual_quantity"
                    class="form-control"
                    min="0"
                    step="0.01"
                    value="{{ old('actual_quantity') }}"
                    placeholder="Enter quantity"
                    required>
                <small style="color: #94A3B8; font-size: 12px;">
                    Enter the quantity physically counted in the shop.
                </small>
            </div>

        </div>

        <div class="form-group">
            <label>Adjustment</label>
            <input type="text" id="adjustment" class="form-control readonly-field" value="—" readonly>
        </div>

        <div class="form-group">
            <label for="notes">Reason / Notes</label>
            <textarea
                id="notes"
                name="notes"
                class="form-control"
                rows="4"
                maxlength="1000"
                placeholder="Example: Damaged materials, missing stock, physical count correction">{{ old('notes') }}</textarea>
            <small style="color: #94A3B8; font-size: 12px;">
                Optional explanation for the stock adjustment.
            </small>
        </div>

        <div class="form-actions">
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Adjustment</button>
        </div>

    </form>

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

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px 24px;
    }

    .req {
        color: #E85D75;
        margin-left: 2px;
    }

    .readonly-field {
        background: #FEFCF9 !important;
        color: #64748B;
        cursor: default;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 20px;
        margin-top: 10px;
        border-top: 1px solid #F0E6DD;
    }
</style>

<script>
    const reserveType = document.getElementById('reserve_type');
    const currentQuantity = document.getElementById('current_quantity');
    const actualQuantity = document.getElementById('actual_quantity');
    const adjustment = document.getElementById('adjustment');

    // Strips trailing .00 for non-money values
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
</script>

@endsection