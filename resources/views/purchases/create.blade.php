@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1>Record Purchase</h1>
        <p>Record purchased items and add them to inventory.</p>
    </div>

    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
        Back to Purchases
    </a>
</div>

<form
    action="{{ route('purchases.store') }}"
    method="POST"
    id="purchase-form"
>
    @csrf

    <div class="card">

        <h2>Purchase Information</h2>

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

    </div>

    <div class="card">

        <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
            <h2>Purchased Items</h2>

            <button
                type="button"
                class="btn btn-secondary"
                id="add-item"
            >
                Add Item
            </button>
        </div>

        <div id="items-container">

            <div class="purchase-item" data-index="0">

                <div class="form-grid">

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
                                        data-name="{{ $product->display_name }}"
                                    >
                                        {{ $product->display_name }}
                                    </option>

                                @endif

                            @endforeach

                        </select>
                    </div>

                    <div class="form-group">
                        <label>Purchase Quantity</label>

                        <input
                            type="number"
                            name="items[0][quantity]"
                            class="quantity-input"
                            min="0.01"
                            step="0.01"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Purchase Unit</label>

                        <input
                            type="text"
                            class="purchase-unit"
                            readonly
                            placeholder="-"
                        >
                    </div>

                    <div class="form-group">
                        <label>Unit Cost</label>

                        <input
                            type="number"
                            name="items[0][unit_cost]"
                            class="unit-cost-input"
                            min="0"
                            step="0.01"
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
                        <label>Stock Added</label>

                        <input
                            type="text"
                            class="stock-quantity"
                            readonly
                            value="-"
                        >
                    </div>

                    <div class="form-group">
                        <label>Line Total</label>

                        <input
                            type="text"
                            class="line-total"
                            readonly
                            value="₱0.00"
                        >
                    </div>

                </div>

                <button
                    type="button"
                    class="btn btn-secondary remove-item"
                >
                    Remove Item
                </button>

                <hr>

            </div>

        </div>

        <div style="text-align: right; margin-top: 20px;">
            <strong>
                Total Amount:
                ₱<span id="total-amount">0.00</span>
            </strong>
        </div>

    </div>

    <div style="margin-top: 20px;">
        <button
            type="submit"
            class="btn btn-primary"
        >
            Save Purchase
        </button>
    </div>

</form>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('items-container');
    const addItemButton = document.getElementById('add-item');
    const totalAmount = document.getElementById('total-amount');

    let itemIndex = 1;

    function updateItem(item) {

        const productSelect =
            item.querySelector('.product-select');

        const quantityInput =
            item.querySelector('.quantity-input');

        const unitCostInput =
            item.querySelector('.unit-cost-input');

        const purchaseUnit =
            item.querySelector('.purchase-unit');

        const stockQuantity =
            item.querySelector('.stock-quantity');

        const lineTotal =
            item.querySelector('.line-total');

        const option =
            productSelect.options[
                productSelect.selectedIndex
            ];

        if (!option || !option.value) {

            purchaseUnit.value = '';
            stockQuantity.value = '-';
            lineTotal.value = '₱0.00';

            calculateTotal();

            return;
        }

        const purchaseUnitValue =
            option.dataset.purchaseUnit;

        const stockUnit =
            option.dataset.stockUnit;

        const unitsPerPurchase =
            parseFloat(
                option.dataset.unitsPerPurchase
            ) || 0;

        const quantity =
            parseFloat(quantityInput.value) || 0;

        const unitCost =
            parseFloat(unitCostInput.value) || 0;

        purchaseUnit.value =
            purchaseUnitValue;

        const stockAdded =
            quantity * unitsPerPurchase;

        const itemTotal =
            quantity * unitCost;

        stockQuantity.value =
            stockAdded.toFixed(2) +
            ' ' +
            stockUnit;

        lineTotal.value =
            '₱' +
            itemTotal.toFixed(2);

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
            total.toFixed(2);
    }

    function attachItemEvents(item) {

        const productSelect =
            item.querySelector('.product-select');

        const quantityInput =
            item.querySelector('.quantity-input');

        const unitCostInput =
            item.querySelector('.unit-cost-input');

        const removeButton =
            item.querySelector('.remove-item');

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

        removeButton.addEventListener(
            'click',
            function () {

                const items =
                    document.querySelectorAll(
                        '.purchase-item'
                    );

                if (items.length === 1) {
                    return;
                }

                item.remove();

                calculateTotal();
            }
        );
    }

    attachItemEvents(
        document.querySelector('.purchase-item')
    );

    addItemButton.addEventListener(
        'click',
        function () {

            const template =
                document
                    .querySelector('.purchase-item')
                    .cloneNode(true);

            template.dataset.index =
                itemIndex;

            template
                .querySelectorAll('input, select')
                .forEach(function (element) {

                    if (
                        element.classList.contains(
                            'purchase-unit'
                        ) ||
                        element.classList.contains(
                            'stock-quantity'
                        ) ||
                        element.classList.contains(
                            'line-total'
                        )
                    ) {
                        element.value =
                            element.classList.contains(
                                'line-total'
                            )
                                ? '₱0.00'
                                : '-';

                        return;
                    }

                    if (
                        element.tagName === 'SELECT'
                    ) {
                        element.selectedIndex = 0;
                    } else {
                        element.value = '';
                    }

                    if (element.name) {
                        element.name =
                            element.name.replace(
                                /items\[\d+\]/,
                                'items[' +
                                itemIndex +
                                ']'
                            );
                    }
                });

            container.appendChild(template);

            attachItemEvents(template);

            itemIndex++;
        }
    );

});
</script>

@endsection