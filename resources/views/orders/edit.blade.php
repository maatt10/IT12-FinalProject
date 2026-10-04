@extends('layouts.app')

@section('title', 'Edit Order ' . $order->reference_code)

@section('content')

<div class="form-wrapper">

    <div class="page-header">
        <div>
            <h1>Edit Order</h1>
            <p>
                Editing
                <strong style="color: #E85D75; font-family: 'SF Mono', Consolas, monospace;">{{ $order->reference_code }}</strong>
            </p>
        </div>
        <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">← Back to Order</a>
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

    <div class="edit-note">
        <strong>Note:</strong> Items and order type cannot be changed. To change items, cancel this order and create a new one.
    </div>

    <form action="{{ route('orders.update', $order) }}" method="POST" id="order-form">
        @csrf
        @method('PUT')

        {{-- CUSTOMER --}}
        <div class="sheet-section">
            <h2 class="section-title">Customer</h2>

            <div class="customer-grid">
                <div class="form-group">
                    <label for="customer_select">Registered Customer</label>
                    <select id="customer_select" class="form-control">
                        <option value="">— Walk-in / Select —</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->customer_id }}"
                                    {{ $order->customer_id == $customer->customer_id ? 'selected' : '' }}>
                                {{ $customer->full_name }}
                            </option>
                        @endforeach
                        <option value="__other__" {{ !$order->customer_id && $order->customer_name ? 'selected' : '' }}>
                            + Unregistered Customer
                        </option>
                    </select>
                    <input type="hidden" name="customer_id" id="customer_id_hidden" value="{{ $order->customer_id }}">
                </div>

                <div class="form-group" id="customer-name-group"
                     style="display: {{ !$order->customer_id && $order->customer_name ? 'block' : 'none' }};">
                    <label for="customer_name">Unregistered Customer Name</label>
                    <input type="text" id="customer_name" name="customer_name" class="form-control"
                           value="{{ old('customer_name', $order->customer_name) }}" maxlength="255">
                </div>
            </div>
        </div>

        {{-- RECEIVER --}}
        <div class="sheet-section">
            <h2 class="section-title">Receiver Information</h2>

            <div class="form-grid-3">
                <div class="form-group">
                    <label for="receiver_first_name">First Name <span class="req">*</span></label>
                    <input type="text" id="receiver_first_name" name="receiver_first_name"
                           class="form-control" value="{{ old('receiver_first_name', $order->receiver_first_name) }}" required>
                </div>
                <div class="form-group">
                    <label for="receiver_middle_name">Middle Name</label>
                    <input type="text" id="receiver_middle_name" name="receiver_middle_name"
                           class="form-control" value="{{ old('receiver_middle_name', $order->receiver_middle_name) }}">
                </div>
                <div class="form-group">
                    <label for="receiver_last_name">Last Name <span class="req">*</span></label>
                    <input type="text" id="receiver_last_name" name="receiver_last_name"
                           class="form-control" value="{{ old('receiver_last_name', $order->receiver_last_name) }}" required>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="receiver_contact">Mobile Number <span class="req">*</span></label>
                    <input type="text" id="receiver_contact" name="receiver_contact"
                           class="form-control" value="{{ old('receiver_contact', $order->receiver_contact) }}"
                           inputmode="numeric" maxlength="11" required>
                </div>
            </div>
        </div>

        {{-- FULFILLMENT --}}
        <div class="sheet-section">
            <h2 class="section-title">Fulfillment</h2>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="fulfillment_type">Fulfillment Type <span class="req">*</span></label>
                    <select id="fulfillment_type" name="fulfillment_type" class="form-control" required>
                        <option value="pickup" {{ old('fulfillment_type', $order->fulfillment_type) === 'pickup' ? 'selected' : '' }}>Pickup</option>
                        <option value="delivery" {{ old('fulfillment_type', $order->fulfillment_type) === 'delivery' ? 'selected' : '' }}>Delivery</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="delivery_datetime">Delivery / Pickup Date &amp; Time <span class="req">*</span></label>
                    <input type="datetime-local" id="delivery_datetime" name="delivery_datetime"
                           class="form-control"
                           value="{{ old('delivery_datetime', $order->delivery_datetime?->format('Y-m-d\TH:i')) }}" required>
                    <small class="field-help">Store hours: 8:00 AM – 6:00 PM only.</small>
                </div>
            </div>

            <div class="form-group" id="address-group" style="display: {{ old('fulfillment_type', $order->fulfillment_type) === 'delivery' ? 'block' : 'none' }};">
                <label for="delivery_address">Delivery Address</label>
                <textarea id="delivery_address" name="delivery_address" class="form-control" rows="2">{{ old('delivery_address', $order->delivery_address) }}</textarea>
            </div>
        </div>

        {{-- PAYMENT --}}
        <div class="sheet-section">
            <h2 class="section-title">Payment</h2>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="delivery_fee">Delivery Fee</label>
                    <div class="input-with-prefix">
                        <span class="prefix">₱</span>
                        <input type="number" id="delivery_fee" name="delivery_fee"
                               class="form-control" value="{{ old('delivery_fee', $order->delivery_fee) }}"
                               min="0" step="0.01" placeholder="0.00"
                               {{ old('fulfillment_type', $order->fulfillment_type) === 'delivery' ? '' : 'readonly' }}>
                    </div>
                </div>

                <div class="form-group">
                    <label for="payment_proof_reference">GCash Reference Number <span class="req">*</span></label>
                    <input type="text" id="payment_proof_reference" name="payment_proof_reference"
                           class="form-control" inputmode="numeric" maxlength="13"
                           value="{{ old('payment_proof_reference', $order->payment_proof_reference) }}" required>
                    <small class="field-help">13 digits, numbers only.</small>
                </div>
            </div>

            {{-- DISCOUNT --}}
            <div class="form-group">
                <label>Discount</label>
                <div class="discount-radios">
                    <label class="discount-radio">
                        <input type="radio" name="discount_type" value="none"
                               {{ old('discount_type', $order->discount_type) === 'none' ? 'checked' : '' }}>
                        <span>None</span>
                    </label>
                    <label class="discount-radio">
                        <input type="radio" name="discount_type" value="pwd"
                               {{ old('discount_type', $order->discount_type) === 'pwd' ? 'checked' : '' }}>
                        <span>PWD</span>
                    </label>
                    <label class="discount-radio">
                        <input type="radio" name="discount_type" value="senior"
                               {{ old('discount_type', $order->discount_type) === 'senior' ? 'checked' : '' }}>
                        <span>Senior Citizen</span>
                    </label>
                </div>
            </div>

            <div id="discount-details" style="display: {{ old('discount_type', $order->discount_type) !== 'none' ? 'block' : 'none' }};">
                <div class="discount-detail-box">
                    <div class="form-group">
                        <label for="discount_name">Name on ID <span class="req">*</span></label>
                        <input type="text" id="discount_name" name="discount_name" class="form-control"
                               value="{{ old('discount_name', $order->discount_name) }}" maxlength="120">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="discount_id_number">
                            <span id="discount-id-label">ID Number</span>
                            <span class="req">*</span>
                        </label>
                        <input type="text" id="discount_id_number" name="discount_id_number"
                               class="form-control"
                               value="{{ old('discount_id_number', $order->discount_id_number) }}">
                        <small class="field-help" id="discount-id-hint">—</small>
                    </div>
                </div>
            </div>

            <div class="summary-row">
                <span>Subtotal (locked)</span>
                <strong>₱{{ number_format($order->subtotal, 2) }}</strong>
            </div>
            <div id="discount-line" class="discount-line" style="display: {{ $order->discount_amount > 0 ? 'flex' : 'none' }};">
                <span>Discount (20%)</span>
                <strong id="discount-display">− ₱{{ number_format($order->discount_amount, 2) }}</strong>
            </div>
            <div class="total-row">
                <span>Order Total</span>
                <strong id="grand-total">₱{{ number_format($order->total_amount, 2) }}</strong>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>

    </form>

</div>

<style>
    .form-wrapper { max-width: 900px; margin: 0 auto; }

    .edit-note {
        background: #FFF8E1;
        border-left: 4px solid #D4AF37;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 13px;
        color: #7C5A0E;
        margin-bottom: 20px;
    }

    .sheet-section {
        padding: 24px 28px;
        background: #FFFFFF;
        border: 1px solid #F0E6DD;
        border-radius: 12px;
        margin-bottom: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .section-title {
        font-size: 15px;
        font-weight: 700;
        color: #212121;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 18px;
        padding-bottom: 8px;
        border-bottom: 2px solid #FCE4EC;
        display: inline-block;
    }

    .customer-grid,
    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px 20px;
    }
    .form-grid-3 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px 20px;
    }

    .req { color: #E85D75; margin-left: 2px; }

    .field-help {
        display: block;
        margin-top: 6px;
        color: #94A3B8;
        font-size: 12px;
    }

    .input-with-prefix { position: relative; }
    .input-with-prefix .prefix {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #94A3B8; font-weight: 600; font-size: 14px; pointer-events: none;
    }
    .input-with-prefix .form-control { padding-left: 32px; }

    .discount-radios { display: flex; gap: 8px; }
    .discount-radio {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 8px;
        border: 1.5px solid #F0E6DD;
        border-radius: 10px;
        cursor: pointer;
        background: #FFFFFF;
        font-size: 13px;
        font-weight: 600;
        color: #64748B;
        transition: all 0.15s ease;
    }
    .discount-radio input[type="radio"] { display: none; }
    .discount-radio:hover { border-color: #F8BBD0; background: #FEFCF9; }
    .discount-radio:has(input:checked) {
        border-color: #E85D75;
        background: #FCE4EC;
        color: #E85D75;
    }

    .discount-detail-box {
        padding: 14px;
        margin-bottom: 14px;
        margin-top: 12px;
        background: #FEFCF9;
        border: 1px dashed #F0E6DD;
        border-radius: 10px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-size: 14px;
        color: #64748B;
        border-top: 1px solid #F0E6DD;
        margin-top: 12px;
    }
    .summary-row strong { color: #212121; }

    .discount-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        margin-top: 8px;
        background: #FFF8E1;
        border-radius: 8px;
        font-size: 13px;
        color: #B8860B;
        font-weight: 600;
    }
    .discount-line strong { color: #B8860B; font-weight: 700; }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        margin-top: 12px;
        border-top: 2px solid #F8BBD0;
    }
    .total-row span { font-size: 15px; font-weight: 700; color: #212121; }
    .total-row strong { font-size: 26px; font-weight: 700; color: #E85D75; }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 20px 0;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const customerSelect = document.getElementById('customer_select');
    const customerHidden = document.getElementById('customer_id_hidden');
    const customerNameGroup = document.getElementById('customer-name-group');

    const fulfillmentType = document.getElementById('fulfillment_type');
    const addressGroup = document.getElementById('address-group');
    const deliveryFee = document.getElementById('delivery_fee');

    const discountRadios = document.querySelectorAll('input[name="discount_type"]');
    const discountDetails = document.getElementById('discount-details');
    const discountNameInput = document.getElementById('discount_name');
    const discountIdInput = document.getElementById('discount_id_number');
    const discountIdLabel = document.getElementById('discount-id-label');
    const discountIdHint = document.getElementById('discount-id-hint');
    const discountLine = document.getElementById('discount-line');
    const discountDisplay = document.getElementById('discount-display');

    const subtotal = {{ (float) $order->subtotal }};
    const grandTotal = document.getElementById('grand-total');

    function updateCustomer() {
        const val = customerSelect.value;
        if (val === '__other__') {
            customerNameGroup.style.display = 'block';
            customerHidden.value = '';
        } else {
            customerNameGroup.style.display = 'none';
            customerHidden.value = val;
        }
    }
    customerSelect.addEventListener('change', updateCustomer);
    updateCustomer();

    function updateFulfillment() {
        if (fulfillmentType.value === 'delivery') {
            addressGroup.style.display = 'block';
            deliveryFee.readOnly = false;
        } else {
            addressGroup.style.display = 'none';
            deliveryFee.value = '';
            deliveryFee.readOnly = true;
        }
        updateTotals();
    }
    fulfillmentType.addEventListener('change', updateFulfillment);
    deliveryFee.addEventListener('input', updateTotals);

    let activeDiscount = '{{ old('discount_type', $order->discount_type) }}';

    discountRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            activeDiscount = this.value;
            handleDiscountChange();
        });
    });

    function handleDiscountChange() {
        if (activeDiscount === 'none') {
            discountDetails.style.display = 'none';
            updateTotals();
            return;
        }

        discountDetails.style.display = 'block';

        if (activeDiscount === 'pwd') {
            discountIdLabel.textContent = 'PWD ID Number';
            discountIdHint.textContent = 'Format: RR-PPMM-BBB-NNNNNNN (16 digits). Hyphens auto-insert.';
            discountIdInput.placeholder = '12-3456-789-0123456';
            discountIdInput.setAttribute('maxlength', '19');
        } else {
            discountIdLabel.textContent = 'Senior Citizen ID Number';
            discountIdHint.textContent = 'Formats vary by LGU (e.g. 12345, QC-12345, 2024-0012).';
            discountIdInput.placeholder = 'e.g. QC-12345';
            discountIdInput.setAttribute('maxlength', '30');
        }

        updateTotals();
    }

    discountIdInput.addEventListener('input', function () {
        if (activeDiscount === 'pwd') {
            let digits = this.value.replace(/\D/g, '').slice(0, 16);
            let formatted = '';
            for (let i = 0; i < digits.length; i++) {
                if (i === 2 || i === 6 || i === 9) formatted += '-';
                formatted += digits[i];
            }
            this.value = formatted;
        } else if (activeDiscount === 'senior') {
            this.value = this.value.replace(/[^A-Za-z0-9\-\/\s]/g, '').toUpperCase().slice(0, 30);
        }
    });

    function updateTotals() {
        let discountAmount = 0;

        if (activeDiscount !== 'none' && subtotal > 0) {
            discountAmount = subtotal * 0.20;
            discountLine.style.display = 'flex';
            discountDisplay.textContent = '− ₱' + discountAmount.toFixed(2);
        } else {
            discountLine.style.display = 'none';
        }

        let fee = 0;
        if (fulfillmentType.value === 'delivery') {
            fee = parseFloat(deliveryFee.value || 0);
        }

        const total = subtotal - discountAmount + fee;
        grandTotal.textContent = '₱' + total.toFixed(2);
    }

    handleDiscountChange();
    updateFulfillment();
});
</script>

@endsection