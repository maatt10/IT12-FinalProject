@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1>Record Purchase</h1>
        <p>Record purchased products and add them to inventory.</p>
    </div>

    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
        Back to Purchases
    </a>
</div>

<div class="card">

    <form
        action="{{ route('purchases.store') }}"
        method="POST"
        id="purchase-form"
    >
        @csrf

        <div class="form-group">
            <label for="supplier_name">
                Supplier
            </label>

            <input
                type="text"
                id="supplier_name"
                name="supplier_name"
                value="{{ old('supplier_name') }}"
                placeholder="Optional"
            >
        </div>

        <div class="card-header">
            <h2>Purchased Items</h2>
        </div>

        <div id="purchase-items">

            <div class="purchase-item">

                <div class="form-row">

                    <div class="form-group">
                        <label>Product</label>

                        <select
                            name="items[0][product_id]"
                            class="product-select"
                            required
                        >
                            <option value="">
                                Select Product
                            </option>

                            @foreach($products as $product)

                                @if(
                                    $product->purchase_unit &&
                                    $product->units_per_purchase !== null
                                )

                                    <option
                                        value="{{ $product->product_id }}"
                                        data-purchase-unit="{{ $product->purchase_unit }}"
                                        data-stock-unit="{{ $product->stock_unit }}"
                                        data-units-per-purchase="{{ $product->units_per_purchase }}"
                                        data-price="{{ $product->selling_price ?? 0 }}"
                                    >
                                        {{ $product->display_name }}
                                    </option>

                                @endif

                            @endforeach

                        </select>
                    </div>

                    <div class="form-group">
                        <label>Quantity</label>

                        <input
                            type="number"
                            name="items[0][quantity]"
                            class="quantity-input"
                            min="0.01"
                            step="0.01"
                            value="1"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Purchase Unit</label>

                        <input
                            type="text"
                            class="purchase-unit-display"
                            readonly
                            placeholder="-"
                        >
                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group">
                        <label>Unit Cost</label>

                        <input
                            type="number"
                            name="items[0][unit_cost]"
                            class="unit-cost-input"
                            min="0"
                            step="0.01"
                            value="0"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Allocation</label>

                        <select
                            name="items[0][reserve_type]"
                            required
                        >
                            <option value="retail">
                                Retail
                            </option>

                            <option value="production">
                                Production
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Stock Quantity</label>

                        <input
                            type="text"
                            class="stock-quantity-display"
                            readonly
                            value="0"
                        >
                    </div>

                    <div class="form-group">
                        <label>Line Total</label>

                        <input
                            type="text"
                            class="line-total-display"
                            readonly
                            value="₱0.00"
                        >
                    </div>

                </div>

                <button
                    type="button"
                    class="btn btn-danger remove-item"
                >
                    Remove
                </button>

                <hr>

            </div>

        </div>

        <button
            type="button"
            id="add-item"
            class="btn btn-secondary"
        >
            Add Another Product
        </button>

        <div class="form-group" style="margin-top: 20px;">
            <strong>
                Total Amount:
            </strong>

            <span id="total-amount">
                ₱0.00
            </span>
        </div>

        <div style="margin-top: 20px;">

            <button
                type="submit"
                class="btn btn-primary"
            >
                Save Purchase
            </button>

            <a
                href="{{ route('purchases.index') }}"
                class="btn btn-secondary"
            >
                Cancel
            </a>

        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('purchase-items');
    const addButton = document.getElementById('add-item');
    const totalAmount = document.getElementById('total-amount');

    let itemIndex = 1;

    function updateItem(item) {

        const productSelect =
            item.querySelector('.product-select');

        const quantityInput =
            item.querySelector('.quantity-input');

        const unitCostInput =
            item.querySelector('.unit-cost-input');

        const purchaseUnitDisplay =
            item.querySelector('.purchase-unit-display');

        const stockQuantityDisplay =
            item.querySelector('.stock-quantity-display');

        const lineTotalDisplay =
            item.querySelector('.line-total-display');

        const selectedOption =
            productSelect.options[
                productSelect.selectedIndex
            ];

        if (!selectedOption || !selectedOption.value) {

            purchaseUnitDisplay.value = '';
            stockQuantityDisplay.value = '0';
            lineTotalDisplay.value = '₱0.00';

            calculateTotal();

            return;
        }

        const purchaseUnit =
            selectedOption.dataset.purchaseUnit || '';

        const unitsPerPurchase =
            parseFloat(
                selectedOption.dataset.unitsPerPurchase
            ) || 0;

        const quantity =
            parseFloat(quantityInput.value) || 0;

        const unitCost =
            parseFloat(unitCostInput.value) || 0;

        const stockUnit =
            selectedOption.dataset.stockUnit || '';

        const stockQuantity =
            quantity * unitsPerPurchase;

        const lineTotal =
            quantity * unitCost;

        purchaseUnitDisplay.value =
            purchaseUnit;

        stockQuantityDisplay.value =
            stockQuantity.toFixed(2) +
            ' ' +
            stockUnit;

        lineTotalDisplay.value =
            '₱' +
            lineTotal.toFixed(2);

        calculateTotal();
    }

    function calculateTotal() {

        let total = 0;

        document
            .querySelectorAll('.purchase-item')
            .forEach(function (item) {

                const quantity =
                    parseFloat(
                        item.querySelector(
                            '.quantity-input'
                        ).value
                    ) || 0;

                const unitCost =
                    parseFloat(
                        item.querySelector(
                            '.unit-cost-input'
                        ).value
                    ) || 0;

                total += quantity * unitCost;
            });

        totalAmount.textContent =
            '₱' + total.toFixed(2);
    }

    function attachEvents(item) {

        const productSelect =
            item.querySelector('.product-select');

        const quantityInput =
            item.querySelector('.quantity-input');

        const unitCostInput =
            item.querySelector('.unit-cost-input');

        productSelect.addEventListener(
            'change',
            function () {
                updateItem(item);
            }
        );

        quantityInput.addEventListener(
            'input',
            function () {
                updateItem(item);
            }
        );

        unitCostInput.addEventListener(
            'input',
            function () {
                updateItem(item);
            }
        );

        item.querySelector('.remove-item')
            .addEventListener('click', function () {

                const items =
                    document.querySelectorAll(
                        '.purchase-item'
                    );

                if (items.length === 1) {
                    return;
                }

                item.remove();

                calculateTotal();
            });

        updateItem(item);
    }

    attachEvents(
        document.querySelector('.purchase-item')
    );

    addButton.addEventListener(
        'click',
        function () {

            const firstItem =
                document.querySelector(
                    '.purchase-item'
                );

            const newItem =
                firstItem.cloneNode(true);

            newItem.querySelectorAll('input')
                .forEach(function (input) {

                    if (
                        input.classList.contains(
                            'quantity-input'
                        )
                    ) {
                        input.value = '1';

                    } else if (
                        input.classList.contains(
                            'unit-cost-input'
                        )
                    ) {
                        input.value = '0';

                    } else if (
                        input.classList.contains(
                            'purchase-unit-display'
                        )
                    ) {
                        input.value = '';

                    } else if (
                        input.classList.contains(
                            'stock-quantity-display'
                        )
                    ) {
                        input.value = '0';

                    } else if (
                        input.classList.contains(
                            'line-total-display'
                        )
                    ) {
                        input.value = '₱0.00';
                    }
                });

            newItem.querySelector(
                '.product-select'
            ).value = '';

            newItem.querySelectorAll(
                '[name]'
            ).forEach(function (element) {

                element.name =
                    element.name.replace(
                        /items\[\d+\]/,
                        'items[' + itemIndex + ']'
                    );
            });

            itemIndex++;

            container.appendChild(newItem);

            attachEvents(newItem);

            calculateTotal();
        });
});
</script>

@endsection