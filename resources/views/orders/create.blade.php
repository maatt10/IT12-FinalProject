@extends('layouts.app')

@section('title', 'Record Online Order')

@section('content')

<div class="page-header">
    <div>
        <h1>Record Online Order</h1>
        <p>Record a bouquet order received through Messenger.</p>
    </div>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
        ← Back to Orders
    </a>
</div>

<form action="{{ route('orders.store') }}" method="POST" id="order-form">
    @csrf

    {{-- ORDER INFORMATION --}}
    <div class="card" style="margin-bottom: 20px;">
        <h2 class="card-heading">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Order Information
        </h2>

        <div class="form-grid">

            <div class="form-group">
                <label for="customer_id">Customer <span class="req">*</span></label>
                <select id="customer_id" name="customer_id" class="form-control" required>
                    <option value="">— Select Customer —</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->customer_id }}" {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                            {{ $customer->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="order_type">Order Type <span class="req">*</span></label>
                <select id="order_type" name="order_type" class="form-control" required>
                    <option value="">— Select Order Type —</option>
                    <option value="ready_made" {{ old('order_type') === 'ready_made' ? 'selected' : '' }}>Ready-Made</option>
                    <option value="customized" {{ old('order_type') === 'customized' ? 'selected' : '' }}>Customized</option>
                </select>
            </div>

            <div class="form-group">
                <label for="fulfillment_type">Fulfillment Type <span class="req">*</span></label>
                <select id="fulfillment_type" name="fulfillment_type" class="form-control" required>
                    <option value="">— Select Fulfillment —</option>
                    <option value="pickup" {{ old('fulfillment_type') === 'pickup' ? 'selected' : '' }}>Pickup</option>
                    <option value="delivery" {{ old('fulfillment_type') === 'delivery' ? 'selected' : '' }}>Delivery</option>
                </select>
            </div>

            <div class="form-group">
                <label for="delivery_timing">Delivery / Pickup Timing <span class="req">*</span></label>
                <input
                    type="text"
                    id="delivery_timing"
                    name="delivery_timing"
                    class="form-control"
                    value="{{ old('delivery_timing') }}"
                    placeholder="e.g. September 25, 2026 - 3:00 PM"
                    required>
            </div>

            <div class="form-group">
                <label for="delivery_fee">Delivery Fee</label>
                <div class="input-with-prefix">
                    <span class="prefix">₱</span>
                    <input
                        type="number"
                        id="delivery_fee"
                        name="delivery_fee"
                        class="form-control"
                        value="{{ old('delivery_fee', 0) }}"
                        min="0"
                        step="0.01">
                </div>
            </div>

            <div class="form-group">
                <label for="payment_proof_reference">Payment Proof Reference</label>
                <input
                    type="text"
                    id="payment_proof_reference"
                    name="payment_proof_reference"
                    class="form-control"
                    value="{{ old('payment_proof_reference') }}"
                    placeholder="e.g. GCash reference number">
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="delivery_address">Delivery Address</label>
                <textarea id="delivery_address" name="delivery_address" class="form-control" rows="3">{{ old('delivery_address') }}</textarea>
            </div>

        </div>
    </div>

    {{-- RECEIVER INFORMATION --}}
    <div class="card" style="margin-bottom: 20px;">
        <h2 class="card-heading">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Receiver Information
        </h2>

        <div class="form-grid">

            <div class="form-group">
                <label for="receiver_first_name">Receiver First Name <span class="req">*</span></label>
                <input
                    type="text"
                    id="receiver_first_name"
                    name="receiver_first_name"
                    class="form-control"
                    value="{{ old('receiver_first_name') }}"
                    required>
            </div>

            <div class="form-group">
                <label for="receiver_middle_name">Receiver Middle Name</label>
                <input
                    type="text"
                    id="receiver_middle_name"
                    name="receiver_middle_name"
                    class="form-control"
                    value="{{ old('receiver_middle_name') }}">
            </div>

            <div class="form-group">
                <label for="receiver_last_name">Receiver Last Name <span class="req">*</span></label>
                <input
                    type="text"
                    id="receiver_last_name"
                    name="receiver_last_name"
                    class="form-control"
                    value="{{ old('receiver_last_name') }}"
                    required>
            </div>

            <div class="form-group">
                <label for="receiver_contact">Receiver Contact <span class="req">*</span></label>
                <input
                    type="text"
                    id="receiver_contact"
                    name="receiver_contact"
                    class="form-control"
                    value="{{ old('receiver_contact') }}"
                    placeholder="e.g. 0917 123 4567"
                    required>
            </div>

        </div>
    </div>

    {{-- ORDER ITEMS --}}
    <div class="card" style="margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 18px; flex-wrap: wrap;">
            <h2 class="card-heading" style="margin-bottom: 0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Order Items
            </h2>

            <button type="button" class="btn btn-secondary" id="add-item" style="padding: 8px 16px; font-size: 13px;">
                + Add Item
            </button>
        </div>

        <div id="order-items">

            <div class="order-item">

                <div class="form-grid">

                    <div class="form-group" style="grid-column: span 2;">
                        <label>Product <span class="req">*</span></label>
                        <select class="product-select form-control" required>
                            <option value="">— Select Bouquet —</option>
                            @foreach($products as $product)
                                <option value="{{ $product->product_id }}" data-price="{{ $product->selling_price }}">
                                    {{ $product->display_name }} — ₱{{ number_format($product->selling_price, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Quantity <span class="req">*</span></label>
                        <input type="number" class="quantity-input form-control" min="1" step="1" value="1" required>
                    </div>

                    <div class="form-group">
                        <label>Customization Details</label>
                        <input type="text" class="customization-input form-control" placeholder="Optional">
                    </div>

                    <div class="form-group">
                        <label>Line Total</label>
                        <input type="text" class="line-total form-control readonly-field" value="₱0.00" readonly>
                    </div>

                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 8px;">
                    <button type="button" class="action-btn delete remove-item">
                        Remove Item
                    </button>
                </div>

            </div>

        </div>

        {{-- ORDER TOTAL --}}
        <div class="order-summary">
            <div class="summary-line">
                <span>Subtotal</span>
                <strong id="subtotal">₱0.00</strong>
            </div>
            <div class="summary-line total">
                <span>Total</span>
                <strong id="total">₱0.00</strong>
            </div>
        </div>

    </div>

    {{-- ACTIONS --}}
    <div class="form-actions">
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Record Order</button>
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
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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

    .order-item {
        padding: 20px;
        background: #FFFFFF;
        border: 1px solid #F0E6DD;
        border-left: 3px solid #E85D75;
        border-radius: 12px;
        margin-bottom: 14px;
    }
    .order-item:last-child {
        margin-bottom: 0;
    }

    .action-btn {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
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

    .order-summary {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid #F8BBD0;
    }
    .summary-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 14px;
        color: #64748B;
    }
    .summary-line strong {
        font-size: 16px;
        color: #212121;
        font-weight: 700;
    }
    .summary-line.total {
        margin-top: 6px;
        padding-top: 12px;
        border-top: 1px dashed #F0E6DD;
    }
    .summary-line.total span {
        font-size: 15px;
        font-weight: 600;
        color: #212121;
    }
    .summary-line.total strong {
        font-size: 24px;
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
document.addEventListener('DOMContentLoaded', function () {

    const orderItems = document.getElementById('order-items');
    const addItemButton = document.getElementById('add-item');
    const subtotalElement = document.getElementById('subtotal');
    const totalElement = document.getElementById('total');
    const fulfillmentType = document.getElementById('fulfillment_type');
    const deliveryFee = document.getElementById('delivery_fee');
    const form = document.getElementById('order-form');

    function updateTotals() {
        let subtotal = 0;

        document.querySelectorAll('.order-item').forEach(function (row) {
            const productSelect = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');
            const lineTotal = row.querySelector('.line-total');
            const selectedOption = productSelect.options[productSelect.selectedIndex];

            const price = parseFloat(selectedOption?.dataset.price || 0);
            const quantity = parseFloat(quantityInput.value || 0);
            const lineAmount = price * quantity;

            lineTotal.value = '₱' + lineAmount.toFixed(2);
            subtotal += lineAmount;
        });

        let fee = parseFloat(deliveryFee.value || 0);
        if (fulfillmentType.value === 'pickup') fee = 0;

        subtotalElement.textContent = '₱' + subtotal.toFixed(2);
        totalElement.textContent = '₱' + (subtotal + fee).toFixed(2);
    }

    function addRowListeners(row) {
        row.querySelector('.product-select').addEventListener('change', updateTotals);
        row.querySelector('.quantity-input').addEventListener('input', updateTotals);
        row.querySelector('.customization-input').addEventListener('input', updateTotals);

        row.querySelector('.remove-item').addEventListener('click', function () {
            const rows = document.querySelectorAll('.order-item');
            if (rows.length === 1) return;
            row.remove();
            updateTotals();
        });
    }

    addItemButton.addEventListener('click', function () {
        const firstRow = document.querySelector('.order-item');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelector('.product-select').value = '';
        newRow.querySelector('.quantity-input').value = 1;
        newRow.querySelector('.customization-input').value = '';
        newRow.querySelector('.line-total').value = '₱0.00';

        orderItems.appendChild(newRow);
        addRowListeners(newRow);
        updateTotals();
    });

    fulfillmentType.addEventListener('change', function () {
        if (this.value === 'pickup') {
            deliveryFee.value = 0;
            deliveryFee.readOnly = true;
            deliveryFee.classList.add('readonly-field');
        } else {
            deliveryFee.readOnly = false;
            deliveryFee.classList.remove('readonly-field');
        }
        updateTotals();
    });

    deliveryFee.addEventListener('input', updateTotals);

    form.addEventListener('submit', function () {
        document.querySelectorAll('.order-item').forEach(function (row, index) {
            const productId = row.querySelector('.product-select').value;
            const quantity = row.querySelector('.quantity-input').value;
            const customization = row.querySelector('.customization-input').value;

            const productInput = document.createElement('input');
            productInput.type = 'hidden';
            productInput.name = `items[${index}][product_id]`;
            productInput.value = productId;

            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `items[${index}][quantity]`;
            quantityInput.value = quantity;

            const customizationInput = document.createElement('input');
            customizationInput.type = 'hidden';
            customizationInput.name = `items[${index}][customization_details]`;
            customizationInput.value = customization;

            form.appendChild(productInput);
            form.appendChild(quantityInput);
            form.appendChild(customizationInput);
        });
    });

    document.querySelectorAll('.order-item').forEach(addRowListeners);
    updateTotals();
});
</script>

@endsection