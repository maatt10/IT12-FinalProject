

<?php $__env->startSection('title', 'Record Online Order'); ?>

<?php $__env->startSection('content'); ?>

<div class="form-wrapper">

    <div class="page-header">
        <div>
            <h1>Record Online Order</h1>
            <p>Record a bouquet order received through Messenger.</p>
        </div>
        <a href="<?php echo e(route('pos.index')); ?>" class="btn btn-secondary">← Back to POS</a>
    </div>

    <?php if($errors->any()): ?>
    <div class="alert alert-error">
        <ul style="margin-left: 20px;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <form action="<?php echo e(route('orders.store')); ?>" method="POST" id="order-form">
        <?php echo csrf_field(); ?>

        <div class="order-sheet">

            
            <section class="sheet-section">
                <div class="section-body">
                    <h2 class="section-title">Customer</h2>

                    <div class="customer-grid">
                        <div class="form-group">
                            <label for="customer_select">Registered Customer</label>
                            <select id="customer_select" class="form-control">
                                <option value="">— Walk-in / Select —</option>
                                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($customer->customer_id); ?>"
                                    <?php echo e(old('customer_id') == $customer->customer_id ? 'selected' : ''); ?>>
                                    <?php echo e($customer->full_name); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <option value="__other__" <?php echo e(old('customer_name') ? 'selected' : ''); ?>>
                                    + Unregistered Customer
                                </option>
                            </select>
                            <input type="hidden" name="customer_id" id="customer_id_hidden" value="<?php echo e(old('customer_id')); ?>">
                        </div>

                        <div class="form-group" id="customer-name-group" style="display: none;">
                            <label for="customer_name">Unregistered Customer Name <span class="req">*</span></label>
                            <input type="text" id="customer_name" name="customer_name" class="form-control"
                                value="<?php echo e(old('customer_name')); ?>" maxlength="255"
                                placeholder="e.g. Juan Dela Cruz">
                        </div>
                    </div>
                </div>
            </section>

            
            <section class="sheet-section">
                <div class="section-body">
                    <h2 class="section-title">Receiver Information</h2>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="receiver_first_name">First Name <span class="req">*</span></label>
                            <input type="text" id="receiver_first_name" name="receiver_first_name"
                                class="form-control" value="<?php echo e(old('receiver_first_name')); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="receiver_middle_name">Middle Name</label>
                            <input type="text" id="receiver_middle_name" name="receiver_middle_name"
                                class="form-control" value="<?php echo e(old('receiver_middle_name')); ?>">
                        </div>
                        <div class="form-group">
                            <label for="receiver_last_name">Last Name <span class="req">*</span></label>
                            <input type="text" id="receiver_last_name" name="receiver_last_name"
                                class="form-control" value="<?php echo e(old('receiver_last_name')); ?>" required>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="receiver_contact">Mobile Number <span class="req">*</span></label>
                            <input type="text" id="receiver_contact" name="receiver_contact"
                                class="form-control" value="<?php echo e(old('receiver_contact')); ?>"
                                inputmode="numeric" maxlength="11"
                                placeholder="09XXXXXXXXX" required>
                            <div class="input-feedback" id="contact-feedback">
                                <span class="feedback-status" id="contact-status"></span>
                                <span class="feedback-count" id="contact-count"></span>
                            </div>
                            <small class="field-help">11 digits, starting with 09.</small>
                        </div>
                    </div>
                </div>
            </section>

            
            <section class="sheet-section">
                <div class="section-body">
                    <h2 class="section-title">Fulfillment</h2>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="fulfillment_type">Fulfillment Type <span class="req">*</span></label>
                            <select id="fulfillment_type" name="fulfillment_type" class="form-control" required>
                                <option value="">— Select —</option>
                                <option value="pickup" <?php echo e(old('fulfillment_type') === 'pickup' ? 'selected' : ''); ?>>Pickup</option>
                                <option value="delivery" <?php echo e(old('fulfillment_type') === 'delivery' ? 'selected' : ''); ?>>Delivery</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="delivery_datetime">Delivery / Pickup Date &amp; Time <span class="req">*</span></label>
                            <input type="datetime-local" id="delivery_datetime" name="delivery_datetime"
                                class="form-control" value="<?php echo e(old('delivery_datetime')); ?>"
                                min="<?php echo e(now()->format('Y-m-d')); ?>T08:00"
                                required>
                            <div class="input-feedback" id="datetime-feedback">
                                <span class="feedback-status" id="datetime-status"></span>
                                <span class="feedback-count"></span>
                            </div>
                            <small class="field-help">Store hours: 8:00 AM – 6:00 PM only.</small>
                        </div>
                    </div>

                    <div class="form-group" id="address-group" style="display: none;">
                        <label for="delivery_address">Delivery Address</label>
                        <textarea id="delivery_address" name="delivery_address" class="form-control" rows="2"
                            placeholder="Complete address for delivery"><?php echo e(old('delivery_address')); ?></textarea>
                    </div>
                </div>
            </section>

            
            <section class="sheet-section">
                <div class="section-body">
                    <h2 class="section-title">Bouquet Selection</h2>

                    <div class="radio-group">
                        <label class="radio-option <?php echo e(old('order_type', 'ready_made') === 'ready_made' ? 'active' : ''); ?>">
                            <input type="radio" name="order_type" value="ready_made"
                                <?php echo e(old('order_type', 'ready_made') === 'ready_made' ? 'checked' : ''); ?>>
                            <div>
                                <strong>Ready-Made Bouquet</strong>
                                <span>Select from existing bouquets in stock</span>
                            </div>
                        </label>
                        <label class="radio-option <?php echo e(old('order_type') === 'customized' ? 'active' : ''); ?>">
                            <input type="radio" name="order_type" value="customized"
                                <?php echo e(old('order_type') === 'customized' ? 'checked' : ''); ?>>
                            <div>
                                <strong>Customized Bouquet</strong>
                                <span>Describe the arrangement and set the price</span>
                            </div>
                        </label>
                    </div>

                    <div id="ready-made-section">
                        <div class="items-header">
                            <span>Bouquets</span>
                            <button type="button" class="btn-add-item" id="add-item">+ Add Bouquet</button>
                        </div>

                        <div id="order-items">
                            <div class="order-item-row">
                                <div class="form-group">
                                    <label>Bouquet <span class="req">*</span></label>
                                    <select class="product-select form-control">
                                        <option value="">— Select Bouquet —</option>
                                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($product->product_id); ?>"
                                            data-price="<?php echo e($product->selling_price); ?>"
                                            data-stock="<?php echo e($product->retail_stock); ?>"
                                            <?php echo e($product->retail_stock <= 0 ? 'disabled' : ''); ?>>
                                            <?php echo e($product->display_name); ?>

                                            — <?php echo e((float) $product->retail_stock); ?> available
                                            — ₱<?php echo e(number_format($product->selling_price, 2)); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="form-group qty-group">
                                    <label>Qty <span class="req">*</span></label>
                                    <input type="number" class="quantity-input form-control" min="1" step="1" value="1">
                                </div>

                                <div class="form-group total-group">
                                    <label>Line Total</label>
                                    <input type="text" class="line-total form-control readonly-field" value="₱0.00" readonly>
                                </div>

                                <button type="button" class="remove-row-btn remove-item" title="Remove">×</button>
                            </div>
                        </div>
                    </div>

                    <div id="customized-section" style="display: none;">
                        <div class="custom-item-card">
                            <div class="form-group">
                                <label for="custom_description">Bouquet Description <span class="req">*</span></label>
                                <textarea id="custom_description" name="custom_description" class="form-control"
                                    rows="3" maxlength="1000"
                                    placeholder="e.g. 6 red roses with baby's breath, wrapped in kraft paper, red ribbon"><?php echo e(old('custom_description')); ?></textarea>
                            </div>

                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label for="custom_quantity">Quantity <span class="req">*</span></label>
                                    <input type="number" id="custom_quantity" name="custom_quantity"
                                        class="form-control" min="1" step="1"
                                        value="<?php echo e(old('custom_quantity', 1)); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="custom_unit_price">Price per Bouquet <span class="req">*</span></label>
                                    <div class="input-with-prefix">
                                        <span class="prefix">₱</span>
                                        <input type="number" id="custom_unit_price" name="custom_unit_price"
                                            class="form-control" min="0" step="0.01"
                                            value="<?php echo e(old('custom_unit_price')); ?>" placeholder="0.00">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Subtotal</label>
                                    <input type="text" id="custom_subtotal" class="form-control readonly-field" value="₱0.00" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            
            <section class="sheet-section">
                <div class="section-body">
                    <h2 class="section-title">Payment</h2>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="delivery_fee">Delivery Fee</label>
                            <div class="input-with-prefix">
                                <span class="prefix">₱</span>
                                <input type="number" id="delivery_fee" name="delivery_fee"
                                    class="form-control readonly-field" value="<?php echo e(old('delivery_fee')); ?>"
                                    min="0" step="0.01" placeholder="0.00" readonly>
                            </div>
                            <small class="field-help" id="delivery-fee-help">Only applies for delivery orders.</small>
                        </div>

                        <div class="form-group">
                            <label for="payment_proof_reference">GCash Reference Number<span class="req">*</span></label>
                            <input type="text" id="payment_proof_reference" name="payment_proof_reference"
                                class="form-control" inputmode="numeric" maxlength="13"
                                value="<?php echo e(old('payment_proof_reference')); ?>"
                                placeholder="13-digit reference number" required>
                            <div class="input-feedback" id="ref-feedback">
                                <span class="feedback-status" id="ref-status"></span>
                                <span class="feedback-count" id="ref-count"></span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="form-group" style="margin-top: 20px;">
                        <label>Discount</label>
                        <div class="discount-radios">
                            <label class="discount-radio">
                                <input type="radio" name="discount_type" value="none" checked>
                                <span>None</span>
                            </label>
                            <label class="discount-radio">
                                <input type="radio" name="discount_type" value="pwd">
                                <span>PWD</span>
                            </label>
                            <label class="discount-radio">
                                <input type="radio" name="discount_type" value="senior">
                                <span>Senior Citizen</span>
                            </label>
                        </div>
                    </div>

                    <div id="discount-details" style="display: none;">
                        <div class="discount-detail-box">
                            <div class="form-group">
                                <label for="discount_name">Name on ID <span class="req">*</span></label>
                                <input type="text" id="discount_name" name="discount_name"
                                    class="form-control" maxlength="120"
                                    placeholder="Full name as shown on ID">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="discount_id_number">
                                    <span id="discount-id-label">ID Number</span>
                                    <span class="req">*</span>
                                </label>
                                <input type="text" id="discount_id_number" name="discount_id_number"
                                    class="form-control" placeholder="Enter ID number">
                                <small class="field-help" id="discount-id-hint">—</small>
                            </div>
                        </div>
                    </div>

                    <div id="discount-line" class="discount-line" style="display: none;">
                        <span>Discount (20%)</span>
                        <strong id="discount-display">− ₱0.00</strong>
                    </div>

                    <div class="total-row">
                        <span>Order Total</span>
                        <strong id="grand-total">₱0.00</strong>
                    </div>
                </div>
            </section>

        </div>

        <div class="form-actions">
            <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Record Order</button>
        </div>

    </form>

</div>

<style>
    .form-wrapper {
        max-width: 900px;
        margin: 0 auto;
    }

    .order-sheet {
        background: #FFFFFF;
        border: 1px solid #F0E6DD;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .sheet-section {
        border-bottom: 1px dashed #F0E6DD;
    }

    .sheet-section:last-child {
        border-bottom: none;
    }

    .section-body {
        padding: 24px 28px;
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

    .customer-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px 20px;
    }

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

    .req {
        color: #E85D75;
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

    .radio-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 10px;
        margin-bottom: 20px;
    }

    .radio-option {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        border: 1.5px solid #F0E6DD;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #FFFFFF;
    }

    .radio-option:hover {
        border-color: #F8BBD0;
        background: #FEFCF9;
    }

    .radio-option.active {
        border-color: #E85D75;
        background: #FCE4EC;
    }

    .radio-option input[type="radio"] {
        margin-top: 2px;
        accent-color: #E85D75;
        cursor: pointer;
    }

    .radio-option strong {
        display: block;
        font-size: 14px;
        color: #212121;
        margin-bottom: 2px;
    }

    .radio-option span {
        font-size: 12px;
        color: #64748B;
        line-height: 1.4;
    }

    .items-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .items-header span {
        font-size: 13px;
        font-weight: 700;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-add-item {
        padding: 7px 14px;
        background: #FCE4EC;
        color: #E85D75;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
    }

    .btn-add-item:hover {
        background: #F8BBD0;
    }

    .order-item-row {
        display: grid;
        grid-template-columns: 1fr 100px 130px 36px;
        gap: 12px;
        align-items: end;
        padding: 14px;
        background: #FEFCF9;
        border: 1px solid #F0E6DD;
        border-radius: 10px;
        margin-bottom: 10px;
    }

    .order-item-row .form-group {
        margin-bottom: 0;
    }

    .remove-row-btn {
        width: 36px;
        height: 38px;
        background: #FDECEA;
        color: #DC3545;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 18px;
        font-weight: 700;
        font-family: inherit;
        line-height: 1;
    }

    .remove-row-btn:hover {
        background: #F8D7DA;
    }

    .custom-item-card {
        padding: 18px;
        background: #FEFCF9;
        border: 1px solid #F0E6DD;
        border-radius: 10px;
    }

    /* DISCOUNT */
    .discount-radios {
        display: flex;
        gap: 8px;
    }

    .discount-radio {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
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

    .discount-radio input[type="radio"] {
        display: none;
    }

    .discount-radio:hover {
        border-color: #F8BBD0;
        background: #FEFCF9;
    }

    .discount-radio:has(input:checked) {
        border-color: #E85D75;
        background: #FCE4EC;
        color: #E85D75;
    }

    .discount-detail-box {
        padding: 14px;
        margin-bottom: 14px;
        background: #FEFCF9;
        border: 1px dashed #F0E6DD;
        border-radius: 10px;
        margin-top: 12px;
    }

    .discount-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        margin-top: 10px;
        background: #FFF8E1;
        border-radius: 8px;
        font-size: 13px;
        color: #B8860B;
        font-weight: 600;
    }

    .discount-line strong {
        color: #B8860B;
        font-weight: 700;
    }

    /* TOTAL */
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 18px;
        margin-top: 18px;
        border-top: 2px solid #F8BBD0;
    }

    .total-row span {
        font-size: 15px;
        font-weight: 700;
        color: #212121;
    }

    .total-row strong {
        font-size: 26px;
        font-weight: 700;
        color: #E85D75;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 20px 0;
    }

    /* FEEDBACK PILLS */
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

    @media (max-width: 640px) {
        .section-body {
            padding: 18px;
        }

        .customer-grid {
            grid-template-columns: 1fr;
        }

        .order-item-row {
            grid-template-columns: 1fr 1fr;
        }

        .order-item-row .remove-row-btn {
            grid-column: span 2;
            justify-self: end;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        /* ELEMENTS */
        const customerSelect = document.getElementById('customer_select');
        const customerHidden = document.getElementById('customer_id_hidden');
        const customerNameGroup = document.getElementById('customer-name-group');

        const fulfillmentType = document.getElementById('fulfillment_type');
        const addressGroup = document.getElementById('address-group');
        const deliveryFee = document.getElementById('delivery_fee');
        const deliveryFeeHelp = document.getElementById('delivery-fee-help');
        const deliveryDatetime = document.getElementById('delivery_datetime');
        const datetimeFeedback = document.getElementById('datetime-feedback');
        const datetimeStatus = document.getElementById('datetime-status');

        const orderTypeRadios = document.querySelectorAll('input[name="order_type"]');
        const readyMadeSection = document.getElementById('ready-made-section');
        const customizedSection = document.getElementById('customized-section');

        const orderItems = document.getElementById('order-items');
        const addItemButton = document.getElementById('add-item');

        const customQty = document.getElementById('custom_quantity');
        const customPrice = document.getElementById('custom_unit_price');
        const customSubtotal = document.getElementById('custom_subtotal');

        const grandTotal = document.getElementById('grand-total');
        const discountLine = document.getElementById('discount-line');
        const discountDisplay = document.getElementById('discount-display');
        const discountDetails = document.getElementById('discount-details');
        const discountNameInput = document.getElementById('discount_name');
        const discountIdInput = document.getElementById('discount_id_number');
        const discountIdLabel = document.getElementById('discount-id-label');
        const discountIdHint = document.getElementById('discount-id-hint');

        const contactInput = document.getElementById('receiver_contact');
        const contactFeedback = document.getElementById('contact-feedback');
        const contactStatus = document.getElementById('contact-status');
        const contactCount = document.getElementById('contact-count');

        const refInput = document.getElementById('payment_proof_reference');
        const refFeedback = document.getElementById('ref-feedback');
        const refStatus = document.getElementById('ref-status');
        const refCount = document.getElementById('ref-count');

        const form = document.getElementById('order-form');

        let activeDiscount = 'none';

        /* ============================================
           CUSTOMER
           ============================================ */
        function updateCustomerField() {
            const val = customerSelect.value;
            if (val === '__other__') {
                customerNameGroup.style.display = 'block';
                customerHidden.value = '';
            } else {
                customerNameGroup.style.display = 'none';
                customerHidden.value = val;
            }
        }
        customerSelect.addEventListener('change', updateCustomerField);
        updateCustomerField();

        /* ============================================
           FULFILLMENT
           ============================================ */
        function updateFulfillment() {
            if (fulfillmentType.value === 'delivery') {
                addressGroup.style.display = 'block';
                deliveryFee.readOnly = false;
                deliveryFee.classList.remove('readonly-field');
                deliveryFeeHelp.textContent = 'Enter the delivery fee charged.';
            } else {
                addressGroup.style.display = 'none';
                deliveryFee.value = '';
                deliveryFee.readOnly = true;
                deliveryFee.classList.add('readonly-field');
                deliveryFeeHelp.textContent = 'Pickup orders have no delivery fee.';
            }
            updateTotals();
        }
        fulfillmentType.addEventListener('change', updateFulfillment);
        deliveryFee.addEventListener('input', updateTotals);

        /* ============================================
           STORE HOURS
           ============================================ */
        function validateDatetime() {
            const val = deliveryDatetime.value;
            if (!val) {
                datetimeFeedback.classList.remove('visible', 'valid', 'invalid', 'neutral');
                return true;
            }

            const timePart = val.split('T')[1];
            if (!timePart) return true;

            const [hours, minutes] = timePart.split(':').map(Number);
            const totalMinutes = hours * 60 + minutes;
            const openMinutes = 8 * 60;
            const closeMinutes = 18 * 60;

            if (totalMinutes < openMinutes || totalMinutes > closeMinutes) {
                datetimeFeedback.classList.remove('valid', 'neutral');
                datetimeFeedback.classList.add('visible', 'invalid');
                datetimeStatus.textContent = 'Time must be between 8:00 AM and 6:00 PM.';
                return false;
            }

            datetimeFeedback.classList.remove('invalid', 'neutral');
            datetimeFeedback.classList.add('visible', 'valid');
            datetimeStatus.textContent = 'Within store hours.';
            return true;
        }
        deliveryDatetime.addEventListener('change', validateDatetime);
        deliveryDatetime.addEventListener('input', validateDatetime);

        /* ============================================
           ORDER TYPE
           ============================================ */
        orderTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.radio-option').forEach(el => el.classList.remove('active'));
                this.closest('.radio-option').classList.add('active');
                updateOrderType();
            });
        });

        function updateOrderType() {
            const type = document.querySelector('input[name="order_type"]:checked')?.value;
            if (type === 'customized') {
                readyMadeSection.style.display = 'none';
                customizedSection.style.display = 'block';
            } else {
                readyMadeSection.style.display = 'block';
                customizedSection.style.display = 'none';
            }
            updateTotals();
        }
        updateOrderType();

        /* ============================================
           READY-MADE ITEMS
           ============================================ */
        function refreshProductOptions() {
            const rows = document.querySelectorAll('.order-item-row');
            if (!rows.length) return;

            const selectedIds = new Set();
            rows.forEach(row => {
                const sel = row.querySelector('.product-select');
                if (sel.value) selectedIds.add(sel.value);
            });

            rows.forEach(row => {
                const sel = row.querySelector('.product-select');
                const currentValue = sel.value;

                Array.from(sel.options).forEach(opt => {
                    if (!opt.value) return;
                    if (opt.value === currentValue) {
                        opt.disabled = false;
                    } else if (selectedIds.has(opt.value)) {
                        opt.disabled = true;
                    } else {
                        opt.disabled = false;
                    }
                });
            });
        }

        function hasDuplicateSelection(row) {
            const sel = row.querySelector('.product-select');
            if (!sel.value) return false;

            let count = 0;
            document.querySelectorAll('.order-item-row .product-select').forEach(s => {
                if (s.value === sel.value) count++;
            });
            return count > 1;
        }

        function attachItemListeners(row) {
            row.querySelector('.product-select').addEventListener('change', function() {
                if (hasDuplicateSelection(row)) {
                    alert('This bouquet is already in the order.');
                    this.value = '';
                    refreshProductOptions();
                    updateTotals();
                    return;
                }

                const stock = parseInt(this.selectedOptions[0]?.dataset.stock || 0);
                const qtyInput = row.querySelector('.quantity-input');
                qtyInput.max = stock;
                if (parseInt(qtyInput.value) > stock) {
                    qtyInput.value = stock;
                }

                refreshProductOptions();
                updateTotals();
            });

            row.querySelector('.quantity-input').addEventListener('input', function() {
                const sel = row.querySelector('.product-select');
                const stock = parseInt(sel.selectedOptions[0]?.dataset.stock || 0);
                const qty = parseInt(this.value || 0);

                if (sel.value && qty > stock) {
                    alert(`Only ${stock} in stock for this bouquet.`);
                    this.value = stock;
                }

                updateTotals();
            });

            row.querySelector('.remove-item').addEventListener('click', function() {
                const rows = document.querySelectorAll('.order-item-row');
                if (rows.length === 1) {
                    row.querySelector('.product-select').value = '';
                    row.querySelector('.quantity-input').value = 1;
                    row.querySelector('.quantity-input').removeAttribute('max');
                    row.querySelector('.line-total').value = '₱0.00';
                    refreshProductOptions();
                    updateTotals();
                    return;
                }
                row.remove();
                refreshProductOptions();
                updateTotals();
            });
        }

        addItemButton.addEventListener('click', function() {
            const first = document.querySelector('.order-item-row');
            const newRow = first.cloneNode(true);
            newRow.querySelector('.product-select').value = '';
            newRow.querySelector('.quantity-input').value = 1;
            newRow.querySelector('.quantity-input').removeAttribute('max');
            newRow.querySelector('.line-total').value = '₱0.00';
            orderItems.appendChild(newRow);
            attachItemListeners(newRow);
            refreshProductOptions();
            updateTotals();
        });

        document.querySelectorAll('.order-item-row').forEach(attachItemListeners);
        refreshProductOptions();

        /* ============================================
           DISCOUNT
           ============================================ */
        document.querySelectorAll('input[name="discount_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                activeDiscount = this.value;
                handleDiscountChange();
            });
        });

        function handleDiscountChange() {
            if (activeDiscount === 'none') {
                discountDetails.style.display = 'none';
                discountNameInput.value = '';
                discountIdInput.value = '';
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

            if (customerSelect.value && customerSelect.value !== '__other__') {
                const opt = customerSelect.selectedOptions[0];
                if (opt) {
                    discountNameInput.value = opt.text.trim();
                }
            }

            updateTotals();
        }

        discountIdInput.addEventListener('input', function() {
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

        discountIdInput.addEventListener('keypress', function(e) {
            if (activeDiscount === 'pwd' && !/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        /* ============================================
           TOTALS
           ============================================ */
        function updateTotals() {
            let subtotal = 0;
            const type = document.querySelector('input[name="order_type"]:checked')?.value;

            if (type === 'ready_made') {
                document.querySelectorAll('.order-item-row').forEach(row => {
                    const sel = row.querySelector('.product-select');
                    const qty = parseFloat(row.querySelector('.quantity-input').value || 0);
                    const price = parseFloat(sel.selectedOptions[0]?.dataset.price || 0);
                    const line = qty * price;
                    row.querySelector('.line-total').value = '₱' + line.toFixed(2);
                    subtotal += line;
                });
            } else {
                const qty = parseFloat(customQty.value || 0);
                const price = parseFloat(customPrice.value || 0);
                const line = qty * price;
                customSubtotal.value = '₱' + line.toFixed(2);
                subtotal += line;
            }

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

        customQty.addEventListener('input', updateTotals);
        customPrice.addEventListener('input', updateTotals);
        updateTotals();

        /* ============================================
           MOBILE NUMBER
           ============================================ */
        function showFeedback(el, statusEl, countEl, state, message, countText) {
            el.classList.remove('visible', 'valid', 'invalid', 'neutral');
            el.classList.add('visible', state);
            statusEl.textContent = message;
            if (countEl) countEl.textContent = countText || '';
        }

        contactInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 11);
            const len = this.value.length;

            if (len === 0) {
                contactFeedback.classList.remove('visible', 'valid', 'invalid', 'neutral');
            } else if (len < 11) {
                showFeedback(contactFeedback, contactStatus, contactCount, 'neutral', 'Incomplete', `${len} / 11 digits`);
            } else if (!this.value.startsWith('09')) {
                showFeedback(contactFeedback, contactStatus, contactCount, 'invalid', 'Must start with 09', `${len} / 11 digits`);
            } else {
                showFeedback(contactFeedback, contactStatus, contactCount, 'valid', 'Valid mobile number', `${len} / 11 digits`);
            }
        });

        contactInput.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
                showFeedback(contactFeedback, contactStatus, contactCount, 'invalid', 'Numbers only', '11 digits required');
                setTimeout(() => contactInput.dispatchEvent(new Event('input')), 1200);
            }
        });

        /* ============================================
           GCASH REFERENCE
           ============================================ */
        refInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 13);
            const len = this.value.length;

            if (len === 0) {
                refFeedback.classList.remove('visible', 'valid', 'invalid', 'neutral');
            } else if (len < 13) {
                showFeedback(refFeedback, refStatus, refCount, 'neutral', 'Incomplete', `${len} / 13 digits`);
            } else {
                showFeedback(refFeedback, refStatus, refCount, 'valid', 'Valid reference', `${len} / 13 digits`);
            }
        });

        refInput.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
                showFeedback(refFeedback, refStatus, refCount, 'invalid', 'Numbers only', '13 digits required');
                setTimeout(() => refInput.dispatchEvent(new Event('input')), 1200);
            }
        });

        /* ============================================
           SUBMIT
           ============================================ */
        form.addEventListener('submit', function(e) {

            // 1. Validate store hours
            if (!validateDatetime()) {
                e.preventDefault();
                deliveryDatetime.focus();
                return;
            }

            // 2. Validate GCash reference (13 digits, required)
            const ref = refInput.value.trim();
            if (!/^\d{13}$/.test(ref)) {
                e.preventDefault();
                showFeedback(refFeedback, refStatus, refCount, 'invalid', 'Invalid reference', 'Must be 13 digits');
                refInput.focus();
                return;
            }

            // 3. Validate contact (mobile, 09XXXXXXXXX)
            const contact = contactInput.value.trim();
            if (!/^09\d{9}$/.test(contact)) {
                e.preventDefault();
                showFeedback(contactFeedback, contactStatus, contactCount, 'invalid', 'Invalid mobile number', 'Must be 09XXXXXXXXX');
                contactInput.focus();
                return;
            }

            // 4. Validate PWD
            if (activeDiscount === 'pwd') {
                const name = discountNameInput.value.trim();
                const digits = discountIdInput.value.replace(/\D/g, '');
                if (!name) {
                    e.preventDefault();
                    alert('Please enter the name on the PWD ID.');
                    discountNameInput.focus();
                    return;
                }
                if (digits.length !== 16) {
                    e.preventDefault();
                    alert('PWD ID must contain exactly 16 digits.');
                    discountIdInput.focus();
                    return;
                }
            }

            // 5. Validate Senior
            if (activeDiscount === 'senior') {
                const name = discountNameInput.value.trim();
                const id = discountIdInput.value.trim();
                if (!name) {
                    e.preventDefault();
                    alert('Please enter the name on the Senior Citizen ID.');
                    discountNameInput.focus();
                    return;
                }
                if (id.length < 4) {
                    e.preventDefault();
                    alert('Senior Citizen ID must be at least 4 characters.');
                    discountIdInput.focus();
                    return;
                }
            }

            // 6. Build hidden item inputs for ready-made orders
            const type = document.querySelector('input[name="order_type"]:checked')?.value;
            document.querySelectorAll('input[data-generated="1"]').forEach(el => el.remove());

            if (type === 'ready_made') {
                let idx = 0;
                document.querySelectorAll('.order-item-row').forEach(row => {
                    const pid = row.querySelector('.product-select').value;
                    const qty = row.querySelector('.quantity-input').value;
                    if (!pid || !qty) return;

                    const i1 = document.createElement('input');
                    i1.type = 'hidden';
                    i1.name = `items[${idx}][product_id]`;
                    i1.value = pid;
                    i1.setAttribute('data-generated', '1');

                    const i2 = document.createElement('input');
                    i2.type = 'hidden';
                    i2.name = `items[${idx}][quantity]`;
                    i2.value = qty;
                    i2.setAttribute('data-generated', '1');

                    form.appendChild(i1);
                    form.appendChild(i2);
                    idx++;
                });
            }
        });
    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/orders/create.blade.php ENDPATH**/ ?>