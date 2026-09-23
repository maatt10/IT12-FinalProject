@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1>Sales / POS</h1>
        <p>Process walk-in and customer sales.</p>
    </div>
</div>

@if($errors->any())
<div class="alert alert-error">
    <ul style="margin-left: 20px;">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="card">

    <h2 style="margin-bottom: 20px;">
        Customer
    </h2>

    <div class="form-group">
        <label for="customer_id">
            Customer
        </label>

        <select
            id="customer_id"
            class="form-control">
            <option value="">
                Walk-in Customer
            </option>

            @foreach($customers as $customer)
            <option value="{{ $customer->customer_id }}">
                {{ $customer->last_name }},
                {{ $customer->first_name }}
            </option>
            @endforeach
        </select>

        <small>
            Customer selection is optional.
        </small>
    </div>

</div>

<div class="card">

    <h2 style="margin-bottom: 20px;">
        Add Product
    </h2>

    <div class="form-group">
        <label for="product_id">
            Product
        </label>

        <select
            id="product_id"
            class="form-control">
            <option value="">
                Select a product
            </option>

            @foreach($products as $product)
            <option
                value="{{ $product->product_id }}"
                data-price="{{ $product->selling_price }}"
                data-unit="{{ $product->stock_unit }}">
                {{ $product->display_name }}
                — ₱{{ number_format($product->selling_price, 2) }}
                / {{ $product->stock_unit }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="quantity">
            Quantity
        </label>

        <input
            type="number"
            id="quantity"
            class="form-control"
            min="0.01"
            step="0.01"
            value="1">
    </div>

    <button
        type="button"
        id="add-to-cart"
        class="btn btn-primary">
        Add to Cart
    </button>

</div>

<div class="card">

    <h2 style="margin-bottom: 20px;">
        Cart
    </h2>

    <div style="overflow-x: auto;">
        <table class="data-table">

            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Line Total</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="cart-body">

                <tr id="empty-cart">
                    <td colspan="5" style="text-align: center;">
                        No products added yet.
                    </td>
                </tr>

            </tbody>

        </table>
    </div>

    <div style="margin-top: 20px; text-align: right;">

        <p style="font-size: 18px;">
            Subtotal:
            <strong id="subtotal">
                ₱0.00
            </strong>
        </p>

    </div>

</div>

<div class="card">

    <h2 style="margin-bottom: 20px;">
        Sale Summary
    </h2>

    <div class="form-group">
        <label>
            Subtotal
        </label>

        <input
            type="text"
            id="summary-subtotal"
            class="form-control"
            value="₱0.00"
            readonly>
    </div>

    <div class="form-group">
        <label for="discount_amount">
            Discount Amount
        </label>

        <input
            type="number"
            id="discount_amount"
            class="form-control"
            min="0"
            step="0.01"
            value="0">

        <small>
            Enter the applicable discount amount, if any.
        </small>
    </div>

    <div class="form-group">
        <label>
            Total Amount
        </label>

        <input
            type="text"
            id="total_amount"
            class="form-control"
            value="₱0.00"
            readonly>
    </div>

</div>

<div class="card">

    <h2 style="margin-bottom: 20px;">
        Payment
    </h2>

    <div class="form-group">

        <label>
            Payment Method
        </label>

        <div>
            <label>
                <input
                    type="radio"
                    name="payment_method"
                    value="cash"
                    checked>
                Cash
            </label>
        </div>

        <div>
            <label>
                <input
                    type="radio"
                    name="payment_method"
                    value="gcash">
                GCash
            </label>
        </div>

        <div>
            <label>
                <input
                    type="radio"
                    name="payment_method"
                    value="bank_transfer">
                Bank Transfer
            </label>
        </div>

    </div>

    <div class="form-group">

        <label>
            Receipt
        </label>

        <div>
            <label>
                <input
                    type="checkbox"
                    id="receipt_issued">
                Issue Receipt
            </label>
        </div>

    </div>

    <form
        id="sale-form"
        action="{{ route('sales.store') }}"
        method="POST">
        @csrf

        <input
            type="hidden"
            name="customer_id"
            id="form-customer-id">

        <input
            type="hidden"
            name="discount_amount"
            id="form-discount"
            value="0">

        <input
            type="hidden"
            name="payment_method"
            id="form-payment-method"
            value="cash">

        <input
            type="hidden"
            name="receipt_issued"
            id="form-receipt-issued"
            value="0">

        <div id="cart-inputs"></div>

        <button
            type="submit"
            class="btn btn-primary">
            Process Sale
        </button>
    </form>
</div>

<script>
    const summarySubtotal =
        document.getElementById('summary-subtotal');
    const discountInput =
        document.getElementById('discount_amount');
    const totalAmount =
        document.getElementById('total_amount');
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const addToCartButton = document.getElementById('add-to-cart');
    const cartBody = document.getElementById('cart-body');
    const subtotalDisplay = document.getElementById('subtotal');

    let cart = [];

    addToCartButton.addEventListener('click', function() {

        const selectedOption =
            productSelect.options[productSelect.selectedIndex];

        if (!selectedOption || !selectedOption.value) {
            alert('Please select a product.');
            return;
        }

        const productId = selectedOption.value;
        const productName = selectedOption.text.split(' — ')[0];
        const unitPrice = parseFloat(selectedOption.dataset.price);
        const stockUnit = selectedOption.dataset.unit;
        const quantity = parseFloat(quantityInput.value);

        if (!quantity || quantity <= 0) {
            alert('Please enter a valid quantity.');
            return;
        }

        const existingItem = cart.find(
            item => item.productId === productId
        );

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({
                productId: productId,
                productName: productName,
                quantity: quantity,
                unitPrice: unitPrice,
                stockUnit: stockUnit
            });
        }

        renderCart();

        productSelect.value = '';
        quantityInput.value = '1';
    });

    function renderCart() {

        cartBody.innerHTML = '';

        if (cart.length === 0) {

            cartBody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center;">
                    No products added yet.
                </td>
            </tr>
        `;

            subtotalDisplay.textContent = '₱0.00';
            summarySubtotal.value = '₱0.00';
            totalAmount.value = '₱0.00';

            return;
        }

        let subtotal = 0;

        cart.forEach(function(item, index) {

            const lineTotal =
                item.quantity * item.unitPrice;

            subtotal += lineTotal;

            const row = document.createElement('tr');

            row.innerHTML = `
            <td>
                ${item.productName}
            </td>

            <td>
                ${item.quantity.toFixed(2)}
                ${item.stockUnit}
            </td>

            <td>
                ₱${item.unitPrice.toFixed(2)}
            </td>

            <td>
                ₱${lineTotal.toFixed(2)}
            </td>

            <td>
                <button
                    type="button"
                    class="btn btn-secondary"
                    onclick="removeFromCart(${index})"
                >
                    Remove
                </button>
            </td>
        `;

            cartBody.appendChild(row);
        });

        subtotalDisplay.textContent =
            '₱' + subtotal.toFixed(2);

        summarySubtotal.value =
            '₱' + subtotal.toFixed(2);

        updateTotal();
    }

    function removeFromCart(index) {

        cart.splice(index, 1);

        renderCart();
    }

    function updateTotal() {

        let subtotal = 0;

        cart.forEach(function(item) {
            subtotal +=
                item.quantity * item.unitPrice;
        });

        let discount =
            parseFloat(discountInput.value) || 0;

        if (discount < 0) {
            discount = 0;
            discountInput.value = '0';
        }

        if (discount > subtotal) {
            discount = subtotal;
            discountInput.value = subtotal.toFixed(2);
        }

        const total = subtotal - discount;

        totalAmount.value =
            '₱' + total.toFixed(2);
    }

    discountInput.addEventListener(
        'input',
        updateTotal
    );

    const saleForm =
        document.getElementById('sale-form');

    const customerSelect =
        document.getElementById('customer_id');

    const cartInputs =
        document.getElementById('cart-inputs');

    const formCustomerId =
        document.getElementById('form-customer-id');

    const formDiscount =
        document.getElementById('form-discount');

    const formPaymentMethod =
        document.getElementById('form-payment-method');

    const formReceiptIssued =
        document.getElementById('form-receipt-issued');

    saleForm.addEventListener('submit', function(event) {

        if (cart.length === 0) {
            event.preventDefault();

            alert('Please add at least one product to the cart.');

            return;
        }

        formCustomerId.value =
            customerSelect.value;

        formDiscount.value =
            parseFloat(discountInput.value) || 0;

        const selectedPayment =
            document.querySelector(
                'input[name="payment_method"]:checked'
            );

        formPaymentMethod.value =
            selectedPayment ?
            selectedPayment.value :
            'cash';

        formReceiptIssued.value =
            document.getElementById('receipt_issued').checked ?
            '1' :
            '0';

        cartInputs.innerHTML = '';

        cart.forEach(function(item, index) {

            const productInput =
                document.createElement('input');

            productInput.type = 'hidden';
            productInput.name =
                `items[${index}][product_id]`;
            productInput.value =
                item.productId;

            const quantityInput =
                document.createElement('input');

            quantityInput.type = 'hidden';
            quantityInput.name =
                `items[${index}][quantity]`;
            quantityInput.value =
                item.quantity;

            cartInputs.appendChild(productInput);
            cartInputs.appendChild(quantityInput);
        });
    });
</script>

@endsection