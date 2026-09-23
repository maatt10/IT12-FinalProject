@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1>Adjust Stock</h1>
        <p>Update inventory based on the actual physical stock count.</p>
    </div>

    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
        Back to Inventory
    </a>
</div>

<div class="card">

    <h2 style="margin-bottom: 20px;">
        {{ $product->name }}

        @if($product->variation)
            - {{ $product->variation }}
        @endif
    </h2>

    <p style="margin-bottom: 20px;">
        Stock Unit:
        <strong>{{ $product->stock_unit }}</strong>
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

    <form
        action="{{ route('inventory.adjustment.store', $product) }}"
        method="POST"
    >
        @csrf

        <div class="form-group">
            <label for="reserve_type">
                Stock Allocation
            </label>

            <select
                id="reserve_type"
                name="reserve_type"
                class="form-control"
                required
            >
                <option value="">Select stock allocation</option>

                @foreach($product->inventory as $inventory)
                    <option
                        value="{{ $inventory->reserve_type }}"
                        data-quantity="{{ $inventory->current_quantity }}"
                        {{ old('reserve_type') === $inventory->reserve_type ? 'selected' : '' }}
                    >
                        {{ ucfirst($inventory->reserve_type) }} Stock
                    </option>
                @endforeach
            </select>

            <small>
                Choose whether you are adjusting retail stock or production stock.
            </small>
        </div>

        <div class="form-group">
            <label>
                Current Recorded Quantity
            </label>

            <input
                type="text"
                id="current_quantity"
                class="form-control"
                value="—"
                readonly
            >
        </div>

        <div class="form-group">
            <label for="actual_quantity">
                Actual Physical Quantity
            </label>

            <input
                type="number"
                id="actual_quantity"
                name="actual_quantity"
                class="form-control"
                min="0"
                step="0.01"
                value="{{ old('actual_quantity') }}"
                required
            >

            <small>
                Enter the quantity physically counted in the shop.
            </small>
        </div>

        <div class="form-group">
            <label>
                Adjustment
            </label>

            <input
                type="text"
                id="adjustment"
                class="form-control"
                value="—"
                readonly
            >
        </div>

        <div class="form-group">
            <label for="notes">
                Reason / Notes
            </label>

            <textarea
                id="notes"
                name="notes"
                class="form-control"
                rows="4"
                maxlength="1000"
                placeholder="Example: Damaged materials, missing stock, physical count correction"
            >{{ old('notes') }}</textarea>

            <small>
                Optional explanation for the stock adjustment.
            </small>
        </div>

        <button type="submit" class="btn btn-primary">
            Save Adjustment
        </button>

        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </form>

</div>

<script>
    const reserveType = document.getElementById('reserve_type');
    const currentQuantity = document.getElementById('current_quantity');
    const actualQuantity = document.getElementById('actual_quantity');
    const adjustment = document.getElementById('adjustment');

    function updateAdjustment() {
        const selectedOption =
            reserveType.options[reserveType.selectedIndex];

        if (!selectedOption || !selectedOption.dataset.quantity) {
            currentQuantity.value = '—';
            adjustment.value = '—';
            return;
        }

        const current = parseFloat(selectedOption.dataset.quantity);
        const actual = parseFloat(actualQuantity.value);

        currentQuantity.value = current.toFixed(2);

        if (Number.isNaN(actual)) {
            adjustment.value = '—';
            return;
        }

        const difference = actual - current;

        adjustment.value =
            difference > 0
                ? '+' + difference.toFixed(2)
                : difference.toFixed(2);
    }

    reserveType.addEventListener('change', updateAdjustment);
    actualQuantity.addEventListener('input', updateAdjustment);

    updateAdjustment();
</script>

@endsection