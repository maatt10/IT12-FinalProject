

<?php $__env->startSection('title', 'Restock'); ?>

<?php $__env->startSection('content'); ?>

<div class="form-page-wide">

    <div class="page-header">
        <div>
            <h1>Restock</h1>
            <p>Record materials and products received from suppliers.</p>
        </div>

        <a href="<?php echo e(route('products.index', ['item_type' => 'material'])); ?>" class="btn btn-secondary">
            ← Back to Inventory
        </a>
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

    <form action="<?php echo e(route('purchases.store')); ?>" method="POST" id="purchase-form">
        <?php echo csrf_field(); ?>

        
        <div class="card" style="margin-bottom: 20px;">
            <h2 class="card-heading">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#6B5B95" stroke-width="2">
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
                    value="<?php echo e(old('supplier_name')); ?>"
                    placeholder="Optional">
            </div>
        </div>

        
        <div class="card" style="margin-bottom: 20px;">
            <div class="items-header">
                <h2 class="card-heading" style="margin-bottom: 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Purchased Items
                </h2>

                <button type="button" class="btn-add-item" id="add-item">+ Add Item</button>
            </div>

            <div id="items-container">
                <div class="purchase-item" data-index="0">
                    <div class="form-grid">

                        <div class="form-group">
                            <label>Product <span class="req">*</span></label>
                            <select name="items[0][product_id]" class="product-select form-control" required>
                                <option value="">— Select Product —</option>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option
                                        value="<?php echo e($product->product_id); ?>"
                                        data-purchase-unit="<?php echo e($product->purchase_unit ?? ''); ?>"
                                        data-stock-unit="<?php echo e($product->stock_unit); ?>"
                                        data-units-per-purchase="<?php echo e($product->units_per_purchase ?? 1); ?>"
                                        data-name="<?php echo e($product->display_name); ?>">
                                        <?php echo e($product->display_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                            <select name="items[0][reserve_type]" class="form-control allocation-select" required>
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

                    <div class="item-actions-row">
                        <button type="button" class="action-btn remove-item">Remove Item</button>
                    </div>
                </div>
            </div>

            <div class="purchase-total">
                <span>Total Amount</span>
                <strong>₱<span id="total-amount">0.00</span></strong>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?php echo e(route('products.index', ['item_type' => 'material'])); ?>" class="action-btn-secondary">
                Cancel
            </a>
            <button type="submit" class="action-btn-primary">
                Save Purchase
            </button>
        </div>

    </form>

</div>


<div class="modal-overlay" id="confirm-modal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Confirm Purchase</h3>
            <p class="modal-subtitle">Review the items below before saving.</p>
        </div>

        <div class="modal-body">
            <div class="modal-row">
                <span class="modal-row-label">Supplier</span>
                <span class="modal-row-value" id="modal-supplier">—</span>
            </div>
            <div class="modal-row">
                <span class="modal-row-label">Total Items</span>
                <span class="modal-row-value" id="modal-item-count">0</span>
            </div>

            <div class="modal-section-title" style="margin-top: 16px;">Line Items</div>
            <div id="modal-items-list" class="modal-items-list"></div>

            <div class="modal-row modal-row-total">
                <span class="modal-row-label">Total Amount</span>
                <span class="modal-row-value modal-value-total" id="modal-total">₱0.00</span>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeConfirmModal()">
                Cancel
            </button>
            <button type="button" class="modal-btn modal-btn-primary" id="modal-confirm-btn">
                Confirm & Save
            </button>
        </div>
    </div>
</div>


<div class="modal-overlay" id="error-modal">
    <div class="modal-box modal-box-error">
        <div class="modal-error-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h3 class="modal-error-title">Something's not right</h3>
        <p class="modal-error-message" id="error-modal-message"></p>
        <div class="modal-error-footer">
            <button type="button" class="modal-btn modal-btn-primary" onclick="closeErrorModal()">
                Got it
            </button>
        </div>
    </div>
</div>

<style>
    .card-heading {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .items-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .btn-add-item {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 16px;
        background: #EFEBF7;
        color: #6B5B95;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s ease;
        height: 38px;
    }
    .btn-add-item:hover { background: #D5C9E8; }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px 20px;
    }

    .req {
        color: #6B5B95;
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
        background: #FEFCF9;
        border: 1px solid #F0E6DD;
        border-radius: 12px;
        margin-bottom: 14px;
    }

    .purchase-item:last-child {
        margin-bottom: 0;
    }

    .item-actions-row {
        display: flex;
        justify-content: flex-end;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed #F0E6DD;
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
        background: #FDECEA;
        color: #DC3545;
    }

    .action-btn:hover {
        background: #F8D7DA;
        color: #B02A37;
    }

    .purchase-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 20px;
        margin-top: 20px;
        border-top: 2px solid #D5C9E8;
    }

    .purchase-total span {
        font-size: 15px;
        font-weight: 600;
        color: #212121;
    }

    .purchase-total strong {
        font-size: 24px;
        font-weight: 700;
        color: #6B5B95;
    }

    /* ===============================
       CONSISTENT FORM ACTIONS
       =============================== */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 20px;
        border-top: 1px solid #F0E6DD;
        align-items: center;
    }

    .form-actions .action-btn-primary,
    .form-actions .action-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        height: 44px;
        min-height: 44px;
        min-width: 140px;
        padding: 0 22px;
        font-size: 14px;
        font-weight: 600;
        line-height: 1;
        border-radius: 8px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .action-btn-primary {
        background: #6B5B95;
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(107, 91, 149, 0.3);
    }
    .action-btn-primary:hover {
        background: #594B7D;
        box-shadow: 0 4px 10px rgba(107, 91, 149, 0.4);
    }
    .action-btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .action-btn-secondary {
        background: #F0E6DD;
        color: #212121;
    }
    .action-btn-secondary:hover {
        background: #E5D5C5;
    }

    /* =========================================
       MODALS
       ========================================= */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(33, 33, 33, 0.55);
        backdrop-filter: blur(3px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fade-in 0.15s ease;
    }
    .modal-overlay.open { display: flex; }

    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-box {
        background: #FFFFFF;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        width: 100%;
        max-width: 520px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: slide-up 0.2s ease;
    }

    @keyframes slide-up {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        padding: 22px 26px 16px;
        border-bottom: 1px solid #F0E6DD;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: #212121;
        margin: 0;
    }

    .modal-subtitle {
        font-size: 13px;
        color: #64748B;
        margin-top: 4px;
    }

    .modal-body {
        padding: 20px 26px;
        overflow-y: auto;
        flex: 1;
        min-height: 0;
    }

    .modal-section-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94A3B8;
        margin-bottom: 10px;
        padding-bottom: 6px;
        border-bottom: 1px dashed #F0E6DD;
    }

    .modal-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 14px;
        border-bottom: 1px dashed #F0E6DD;
        gap: 12px;
    }
    .modal-row:last-child { border-bottom: none; }

    .modal-row-label { color: #64748B; }
    .modal-row-value {
        color: #212121;
        font-weight: 600;
        text-align: right;
    }

    .modal-row-total {
        padding-top: 12px;
        margin-top: 6px;
        border-top: 2px solid #D5C9E8;
        border-bottom: none;
    }

    .modal-value-total {
        font-size: 22px;
        font-weight: 700;
        color: #6B5B95;
    }

    .modal-items-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .modal-item {
        padding: 10px 12px;
        background: #FDFBFF;
        border-radius: 8px;
        font-size: 13px;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 8px;
        align-items: center;
    }

    .modal-item-name {
        color: #212121;
        font-weight: 600;
        line-height: 1.3;
    }
    .modal-item-meta {
        font-size: 11px;
        color: #64748B;
        margin-top: 2px;
        font-weight: 500;
    }
    .modal-item-total {
        color: #2E5A3B;
        font-weight: 700;
        white-space: nowrap;
        text-align: right;
    }

    .modal-footer {
        padding: 16px 26px 22px;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        border-top: 1px solid #F0E6DD;
        background: #FEFCF9;
    }

    .modal-btn {
        padding: 11px 22px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        border: none;
        transition: all 0.15s ease;
        min-width: 120px;
    }

    .modal-btn-primary {
        background: #6B5B95;
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(107, 91, 149, 0.3);
    }
    .modal-btn-primary:hover { background: #594B7D; }
    .modal-btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .modal-btn-secondary {
        background: #F0E6DD;
        color: #212121;
    }
    .modal-btn-secondary:hover { background: #E5D5C5; }

    /* ERROR MODAL */
    .modal-box-error {
        max-width: 420px;
        text-align: center;
        padding: 30px 30px 24px;
    }

    .modal-error-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #FDECEA;
        color: #DC3545;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }

    .modal-error-title {
        font-size: 18px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 8px;
    }

    .modal-error-message {
        font-size: 14px;
        color: #64748B;
        line-height: 1.5;
        margin-bottom: 24px;
        word-break: break-word;
        white-space: pre-line;
    }

    .modal-error-footer { display: flex; justify-content: center; }
    .modal-error-footer .modal-btn { min-width: 120px; }

    @media (max-width: 640px) {
        .modal-box { max-width: 100%; }
        .form-actions .action-btn-primary,
        .form-actions .action-btn-secondary { flex: 1; min-width: 0; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('items-container');
    const addItemButton = document.getElementById('add-item');
    const totalAmount = document.getElementById('total-amount');
    const form = document.getElementById('purchase-form');

    const confirmModal = document.getElementById('confirm-modal');
    const confirmBtn = document.getElementById('modal-confirm-btn');
    const errorModal = document.getElementById('error-modal');
    const errorMessage = document.getElementById('error-modal-message');

    let itemIndex = 1;

    /* ============================================
       HELPERS
       ============================================ */
    function formatQty(n) {
        const num = parseFloat(n || 0);
        return parseFloat(num.toFixed(2)).toString();
    }

    window.closeConfirmModal = function () {
        confirmModal.classList.remove('open');
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Confirm & Save';
    };
    window.closeErrorModal = function () {
        errorModal.classList.remove('open');
    };
    function showError(message) {
        errorMessage.textContent = message;
        errorModal.classList.add('open');
    }

    /* ============================================
       REFRESH PRODUCT OPTIONS (no duplicate)
       ============================================ */
    function refreshProductOptions() {
        const rows = document.querySelectorAll('.purchase-item');
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

    /* ============================================
       UPDATE ITEM ROW
       ============================================ */
    function updateItem(item) {
        const productSelect = item.querySelector('.product-select');
        const quantityInput = item.querySelector('.quantity-input');
        const unitCostInput = item.querySelector('.unit-cost-input');
        const purchaseUnit = item.querySelector('.purchase-unit');
        const stockQuantity = item.querySelector('.stock-quantity');
        const lineTotal = item.querySelector('.line-total');
        const allocationSelect = item.querySelector('.allocation-select');

        const option = productSelect.options[productSelect.selectedIndex];

        if (!option || !option.value) {
            purchaseUnit.value = '—';
            stockQuantity.value = '—';
            lineTotal.value = '₱0.00';
            if (allocationSelect) allocationSelect.disabled = false;
            calculateTotal();
            return;
        }

        const purchaseUnitValue = option.dataset.purchaseUnit || '';
        const stockUnit = option.dataset.stockUnit || '';
        const unitsPerPurchase = parseFloat(option.dataset.unitsPerPurchase) || 1;

        const quantity = parseFloat(quantityInput.value) || 0;
        const unitCost = parseFloat(unitCostInput.value) || 0;

        purchaseUnit.value = purchaseUnitValue || '—';

        const stockAdded = quantity * unitsPerPurchase;
        const itemTotal = quantity * unitCost;

        stockQuantity.value = formatQty(stockAdded) + ' ' + stockUnit;
        lineTotal.value = '₱' + itemTotal.toFixed(2);

        // Auto-select allocation based on stock purpose — but data attributes don't include it, so leave to user
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;

        document.querySelectorAll('.purchase-item').forEach(function (item) {
            const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
            const unitCost = parseFloat(item.querySelector('.unit-cost-input').value) || 0;
            total += quantity * unitCost;
        });

        totalAmount.textContent = total.toFixed(2);
    }

    /* ============================================
       ATTACH LISTENERS
       ============================================ */
    function attachItemEvents(item) {
        const productSelect = item.querySelector('.product-select');
        const quantityInput = item.querySelector('.quantity-input');
        const unitCostInput = item.querySelector('.unit-cost-input');
        const removeBtn = item.querySelector('.remove-item');

        productSelect.addEventListener('change', function () {
            // Duplicate check
            const currentValue = this.value;
            let duplicates = 0;
            document.querySelectorAll('.purchase-item .product-select').forEach(s => {
                if (s.value === currentValue && currentValue) duplicates++;
            });
            if (duplicates > 1) {
                showError('This product is already in the purchase list.');
                this.value = '';
                refreshProductOptions();
                updateItem(item);
                return;
            }
            refreshProductOptions();
            updateItem(item);
        });

        quantityInput.addEventListener('input', () => updateItem(item));
        unitCostInput.addEventListener('input', () => updateItem(item));

        removeBtn.addEventListener('click', function () {
            const items = document.querySelectorAll('.purchase-item');
            if (items.length === 1) {
                // Clear instead of remove
                productSelect.value = '';
                quantityInput.value = '';
                unitCostInput.value = '';
                item.querySelector('.purchase-unit').value = '—';
                item.querySelector('.stock-quantity').value = '—';
                item.querySelector('.line-total').value = '₱0.00';
                refreshProductOptions();
                updateItem(item);
                return;
            }
            item.remove();
            refreshProductOptions();
            calculateTotal();
        });
    }

    /* ============================================
       ADD ITEM
       ============================================ */
    attachItemEvents(document.querySelector('.purchase-item'));
    refreshProductOptions();

    addItemButton.addEventListener('click', function () {
        const template = document.querySelector('.purchase-item').cloneNode(true);
        template.dataset.index = itemIndex;

        template.querySelectorAll('input, select').forEach(function (element) {
            if (
                element.classList.contains('purchase-unit') ||
                element.classList.contains('stock-quantity')
            ) {
                element.value = '—';
                return;
            }
            if (element.classList.contains('line-total')) {
                element.value = '₱0.00';
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
        refreshProductOptions();
        itemIndex++;
    });

    /* ============================================
       OVERLAY + ESC
       ============================================ */
    confirmModal.addEventListener('click', function (e) {
        if (e.target === confirmModal) closeConfirmModal();
    });
    errorModal.addEventListener('click', function (e) {
        if (e.target === errorModal) closeErrorModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeConfirmModal();
            closeErrorModal();
        }
    });

    /* ============================================
       SUBMIT → VALIDATE → CONFIRM MODAL
       ============================================ */
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const rows = document.querySelectorAll('.purchase-item');
        if (!rows.length) {
            showError('Please add at least one item.');
            return;
        }

        const validItems = [];
        let hasError = false;

        rows.forEach((row, idx) => {
            const sel = row.querySelector('.product-select');
            const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const cost = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
            const allocation = row.querySelector('.allocation-select').value;

            if (!sel.value) {
                showError(`Please select a product for item #${idx + 1}.`);
                hasError = true;
                return;
            }

            if (qty <= 0) {
                showError(`Please enter a valid quantity for item #${idx + 1}.`);
                hasError = true;
                return;
            }

            if (cost < 0) {
                showError(`Unit cost cannot be negative for item #${idx + 1}.`);
                hasError = true;
                return;
            }

            const option = sel.options[sel.selectedIndex];
            const unitsPerPurchase = parseFloat(option.dataset.unitsPerPurchase) || 1;
            const stockUnit = option.dataset.stockUnit || '';

            validItems.push({
                name: option.dataset.name || option.textContent.trim(),
                qty: qty,
                cost: cost,
                allocation: allocation,
                stockAdded: qty * unitsPerPurchase,
                stockUnit: stockUnit,
                lineTotal: qty * cost,
            });
        });

        if (hasError) return;

        // Populate modal
        const supplier = document.getElementById('supplier_name').value.trim() || '—';
        document.getElementById('modal-supplier').textContent = supplier;
        document.getElementById('modal-item-count').textContent = validItems.length;

        const listEl = document.getElementById('modal-items-list');
        listEl.innerHTML = '';

        validItems.forEach(item => {
            const el = document.createElement('div');
            el.className = 'modal-item';
            el.innerHTML = `
                <div>
                    <div class="modal-item-name">${item.name}</div>
                    <div class="modal-item-meta">
                        ${formatQty(item.qty)} × ₱${item.cost.toFixed(2)} · ${item.allocation === 'retail' ? 'Retail' : 'Production'} · adds ${formatQty(item.stockAdded)} ${item.stockUnit}
                    </div>
                </div>
                <div class="modal-item-total">₱${item.lineTotal.toFixed(2)}</div>
            `;
            listEl.appendChild(el);
        });

        const total = validItems.reduce((s, i) => s + i.lineTotal, 0);
        document.getElementById('modal-total').textContent = '₱' + total.toFixed(2);

        confirmModal.classList.add('open');
    });

    confirmBtn.addEventListener('click', function () {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Saving...';
        form.submit();
    });
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/purchases/create.blade.php ENDPATH**/ ?>