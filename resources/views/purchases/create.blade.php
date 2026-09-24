@extends('layouts.app')

@section('title', 'Record Purchase')

@section('content')

<div class="page-header">
    <div>
        <h1>Record Purchase</h1>
        <p>Record purchased items and add them to inventory.</p>
    </div>

    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
        ← Back to Purchases
    </a>
</div>

<form action="{{ route('purchases.store') }}" method="POST" id="purchase-form">
    @csrf

    {{-- SUPPLIER --}}
    <div class="card" style="margin-bottom: 20px;">
        <h2 class="card-heading">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            Purchase Information
        </h2>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="supplier_name">Supplier</label>
            <input
                type="text"
                id="supplier_name"
                name="supplier_name"
                class="form-control"
                value="{{ old('supplier_name') }}"
                placeholder="Optional">
        </div>
    </div>

    {{-- ITEMS --}}
    <div class="card" style="margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 18px; flex-wrap: wrap;">
            <h2 class="card-heading" style="margin-bottom: 0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Purchased Items
            </h2>

            <button type="button" class="btn btn-secondary" id="add-item" style="padding: 8px 16px; font-size: 13px;">
                + Add Item
            </button>
        </div>

        <div id="items-container">
            {{-- Item template --}}
            <div class="purchase-item" data-index="0">

                <div class="form-grid">

                    <div class="form-group">
                        <label>Product <span class="req">*</span></label>
                        <select name="items[0][product_id]" class="product-select form-control" required>
                            <option value="">— Select Product —</option>
                            @foreach($products as $product)
                            @if($product->purchase_unit && $product->units_per_purchase !== null)
                            <option
                                value="{{ $product->product_id }}"
                                data-purchase-unit="{{ $product->purchase_unit }}"
                                data-stock-unit="{{ $product->stock_unit }}"
                                data-units-per-purchase="{{ $product->units_per_purchase }}"
                                data-name="{{ $product->display_name }}">
                                {{ $product->display_name }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Purchase Qty <span class="req">*</span></label>
                        <input
                            type="number"
                            name="items[0][quantity]"
                            class="quantity-input form-control"
                            min="0.01"
                            step="0.01"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Purchase Unit</label>
                        <input type="text" class="purchase-unit form-control readonly-field" readonly placeholder="—">
                    </div>

                    <div class="form-group">
                        <label>Unit Cost <span class="req">*</span></label>
                        <div class="input-with-prefix">
                            <span class="prefix">₱</span>
                            <input
                                type="number"
                                name="items[0][unit_cost]"
                                class="unit-cost-input form-control"
                                min="0"
                                step="0.01"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Allocation <span class="req">*</span></label>
                        <select name="items[0][reserve_type]" class="form-control" required>
                            <option value="retail">Retail</option>
                            <option value="production">Production</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Stock Added</label>
                        <input type="text" class="stock-quantity form-control readonly-field" readonly value="—">
                    </div>

                    <div class="form-group">
                        <label>Line Total</label>
                        <input type="text" class="line-total form-control readonly-field" readonly value="₱0.00">
                    </div>

                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 8px;">
                    <button type="button" class="action-btn delete remove-item">
                        Remove Item
                    </button>
                </div>

            </div>
        </div>

        {{-- Total --}}
        <div class="purchase-total">
            <span>Total Amount</span>
            <strong>₱<span id="total-amount">0.00</span></strong>
        </div>

    </div>

    {{-- Actions --}}
    <div class="form-actions">
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
            Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            Save Purchase
        </button>
    </div>

</form>

<style>
    .card-heading {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px 20px;
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

    .purchase-item {
        padding: 20px;
        background: #FFFFFF;
        border: 1px solid #F0E6DD;
        border-left: 3px solid #E85D75;
        border-radius: 12px;
        margin-bottom: 14px;
    }

    .purchase-item:last-child {
        margin-bottom: 0;
    }

    .action-btn {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.15s ease;
        white-space: nowrap;
        font-family: inherit;
    }

    .action-btn.delete {
        background: #FDECEA;
        color: #DC3545;
    }

    .action-btn.delete:hover {
        background: #F8D7DA;
        color: #B02A37;
    }

    .purchase-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 20px;
        margin-top: 20px;
        border-top: 2px solid #F8BBD0;
    }

    .purchase-total span {
        font-size: 15px;
        font-weight: 600;
        color: #212121;
    }

    .purchase-total strong {
        font-size: 24px;
        font-weight: 700;
        color: #E85D75;
        cursor: default;
        user-select: none;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 20px;
        border-top: 1px solid #F0E6DD;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const container = document.getElementById('items-container');
        const addItemButton = document.getElementById('add-item');
        const totalAmount = document.getElementById('total-amount');

        let itemIndex = 1;

        function formatQty(n) {
            return parseFloat(Number(n || 0).toFixed(2)).toString();
        }

        function updateItem(item) {
            const productSelect = item.querySelector('.product-select');
            const quantityInput = item.querySelector('.quantity-input');
            const unitCostInput = item.querySelector('.unit-cost-input');
            const purchaseUnit = item.querySelector('.purchase-unit');
            const stockQuantity = item.querySelector('.stock-quantity');
            const lineTotal = item.querySelector('.line-total');

            const option = productSelect.options[productSelect.selectedIndex];

            if (!option || !option.value) {
                purchaseUnit.value = '';
                stockQuantity.value = '—';
                lineTotal.value = '₱0.00';
                calculateTotal();
                return;
            }

            const purchaseUnitValue = option.dataset.purchaseUnit;
            const stockUnit = option.dataset.stockUnit;
            const unitsPerPurchase = parseFloat(option.dataset.unitsPerPurchase) || 0;

            const quantity = parseFloat(quantityInput.value) || 0;
            const unitCost = parseFloat(unitCostInput.value) || 0;

            purchaseUnit.value = purchaseUnitValue;

            const stockAdded = quantity * unitsPerPurchase;
            const itemTotal = quantity * unitCost;

            stockQuantity.value = formatQty(stockAdded) + ' ' + stockUnit;
            lineTotal.value = '₱' + itemTotal.toFixed(2);

            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;

            document.querySelectorAll('.purchase-item').forEach(function(item) {
                const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
                const unitCost = parseFloat(item.querySelector('.unit-cost-input').value) || 0;
                total += quantity * unitCost;
            });

            totalAmount.textContent = total.toFixed(2);
        }

        function attachItemEvents(item) {
            item.querySelector('.product-select').addEventListener('change', () => updateItem(item));
            item.querySelector('.quantity-input').addEventListener('input', () => updateItem(item));
            item.querySelector('.unit-cost-input').addEventListener('input', () => updateItem(item));

            item.querySelector('.remove-item').addEventListener('click', function() {
                const items = document.querySelectorAll('.purchase-item');
                if (items.length === 1) return;
                item.remove();
                calculateTotal();
            });
        }

        attachItemEvents(document.querySelector('.purchase-item'));

        addItemButton.addEventListener('click', function() {
            const template = document.querySelector('.purchase-item').cloneNode(true);

            template.dataset.index = itemIndex;

            template.querySelectorAll('input, select').forEach(function(element) {
                if (
                    element.classList.contains('purchase-unit') ||
                    element.classList.contains('stock-quantity') ||
                    element.classList.contains('line-total')
                ) {
                    element.value = element.classList.contains('line-total') ? '₱0.00' : '—';
                    return;
                }

                if (element.tagName === 'SELECT') {
                    element.selectedIndex = 0;
                } else {
                    element.value = '';
                }

                if (element.name) {
                    element.name = element.name.replace(/items\[\d+\]/, 'items[' + itemIndex + ']');
                }
            });

            container.appendChild(template);
            attachItemEvents(template);
            itemIndex++;
        });

    });
</script>

@endsection