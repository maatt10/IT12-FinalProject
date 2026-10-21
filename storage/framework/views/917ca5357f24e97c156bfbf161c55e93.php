

<?php $__env->startSection('title', 'Record Production'); ?>

<?php $__env->startSection('content'); ?>

<div class="form-page-wide">

    <?php
        $productionProducts = $products->map(function ($product) {
            return [
                'id' => $product->product_id,
                'name' => $product->display_name,
                'stock_unit' => $product->stock_unit,
                'components' => $product->parentComponents->map(function ($component) {
                    return [
                        'name' => $component->materialProduct->display_name,
                        'quantity_required' => (float) $component->quantity_required,
                        'stock_unit' => $component->materialProduct->stock_unit,
                        'available' => (float) $component->material_available,
                    ];
                })->values()->all(),
            ];
        })->values()->all();
    ?>

    <div class="page-header">
        <div>
            <h1>Record Production</h1>
            <p>Record one or more productions. Required materials are calculated from each product's BOM.</p>
        </div>

        <a href="<?php echo e(route('products.index', ['item_type' => 'product'])); ?>" class="btn btn-secondary">
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

    <form action="<?php echo e(route('production.store')); ?>" method="POST" id="production-form">
        <?php echo csrf_field(); ?>

        <div class="form-card">
            <div class="items-header">
                <h2 class="section-title">Production Batch</h2>
                <button type="button" class="btn-add-item" id="add-production">+ Add Production</button>
            </div>

            <div id="productions-container">

                <div class="production-row" data-index="0">
                    <div class="production-row-main">

                        <div class="form-group product-group">
                            <label>Product <span class="req">*</span></label>
                            <select name="items[0][product_id]" class="product-select form-control" required>
                                <option value="">— Select Product —</option>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($product->product_id); ?>">
                                        <?php echo e($product->display_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="form-group qty-group">
                            <label>Quantity <span class="req">*</span></label>
                            <input type="number"
                                   name="items[0][quantity_produced]"
                                   class="quantity-input form-control"
                                   min="1" step="1" value="1" required>
                        </div>

                        <div class="row-actions">
                            <button type="button" class="toggle-bom-btn" style="display: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="chevron">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                                <span class="toggle-label">Show materials</span>
                                <span class="shortage-badge" style="display: none;">⚠</span>
                            </button>
                            <button type="button" class="remove-row-btn remove-production" title="Remove">×</button>
                        </div>
                    </div>

                    <div class="bom-preview" style="display: none;">
                        <div class="bom-preview-title">
                            Required Materials
                            <span class="bom-preview-note">Deducted from production stock</span>
                        </div>

                        <div class="table-scroll">
                            <table class="bom-table">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th class="num">Per Product</th>
                                        <th class="num">Required</th>
                                        <th class="num">Available</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody class="bom-body"></tbody>
                            </table>
                        </div>

                        <div class="bom-warning" style="display: none;">
                            <strong>⚠ Insufficient stock for some materials</strong>
                            <span>Recording this production will push inventory negative.</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="form-actions">
            <a href="<?php echo e(route('products.index', ['item_type' => 'product'])); ?>" class="action-btn-secondary">Cancel</a>
            <button type="submit" class="action-btn-primary">Complete Production</button>
        </div>

    </form>

</div>


<div class="modal-overlay" id="confirm-modal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Confirm Production</h3>
            <p class="modal-subtitle">Review the batch below before recording.</p>
        </div>

        <div class="modal-body" id="modal-body"></div>

        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeConfirmModal()">
                Cancel
            </button>
            <button type="button" class="modal-btn modal-btn-primary" id="modal-confirm-btn">
                Confirm & Record
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
        <p class="modal-error-message" id="error-modal-message" style="font-size: 15px; font-weight: 500; color: #212121;"></p>
        <div class="modal-error-footer" style="margin-top: 20px;">
            <button type="button" class="modal-btn modal-btn-primary" onclick="closeErrorModal()">
                Got it
            </button>
        </div>
    </div>
</div>

<style>
    .form-page-wide {
        max-width: 900px;
        margin: 0 auto;
    }

    .form-card {
        background: #FFFFFF;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        padding: 24px 28px;
        margin-bottom: 16px;
    }

    .items-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .section-title {
        font-size: 15px;
        font-weight: 700;
        color: #212121;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #EFEBF7;
    }

    .btn-add-item {
        padding: 7px 14px;
        background: #EFEBF7;
        color: #6B5B95;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
    }
    .btn-add-item:hover { background: #D5C9E8; }

    /* PRODUCTION ROW */
    .production-row {
        padding: 16px;
        background: #FEFCF9;
        border: 1px solid #F0E6DD;
        border-radius: 10px;
        margin-bottom: 12px;
    }

    .production-row-main {
        display: grid;
        grid-template-columns: 1fr 110px auto;
        gap: 12px;
        align-items: end;
    }

    .production-row .form-group { margin-bottom: 0; }

    .row-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        height: 42px;
    }

    .toggle-bom-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        background: #FFFFFF;
        color: #6B5B95;
        border: 1.5px solid #D5C9E8;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s ease;
        height: 38px;
        white-space: nowrap;
    }
    .toggle-bom-btn:hover {
        background: #FDFBFF;
        border-color: #6B5B95;
    }
    .toggle-bom-btn .chevron {
        transition: transform 0.2s ease;
    }
    .toggle-bom-btn.expanded .chevron {
        transform: rotate(180deg);
    }
    .toggle-bom-btn.expanded {
        background: #EFEBF7;
    }

    .shortage-badge {
        display: inline-block;
        padding: 1px 6px;
        background: #FDECEA;
        color: #DC3545;
        font-size: 10px;
        font-weight: 700;
        border-radius: 4px;
        letter-spacing: 0.5px;
    }

    .remove-row-btn {
        width: 38px;
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
        flex-shrink: 0;
    }
    .remove-row-btn:hover { background: #F8D7DA; }

    /* BOM PREVIEW */
    .bom-preview {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px dashed #F0E6DD;
    }

    .bom-preview-title {
        font-size: 12px;
        font-weight: 700;
        color: #6B5B95;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
    }

    .bom-preview-note {
        font-size: 10px;
        font-weight: 600;
        color: #94A3B8;
        letter-spacing: 0.5px;
        text-transform: none;
    }

    .table-scroll {
        width: 100%;
        overflow-x: auto;
    }

    .bom-table {
        width: 100%;
        border-collapse: collapse;
    }

    .bom-table thead th {
        text-align: left;
        padding: 8px 10px;
        font-size: 10px;
        font-weight: 700;
        color: #6B5B95;
        text-transform: uppercase;
        letter-spacing: 1px;
        background: #EFEBF7;
        border-bottom: 2px solid #D5C9E8;
    }
    .bom-table thead th:first-child { border-top-left-radius: 6px; }
    .bom-table thead th:last-child { border-top-right-radius: 6px; }
    .bom-table thead th.num { text-align: right; }

    .bom-table tbody td {
        padding: 8px 10px;
        font-size: 12px;
        color: #212121;
        border-bottom: 1px solid #F5EEE4;
    }
    .bom-table tbody tr:last-child td { border-bottom: none; }
    .bom-table tbody td.num { text-align: right; white-space: nowrap; }
    .bom-table tbody td.muted { color: #64748B; }
    .bom-table tbody td.strong { font-weight: 700; color: #2E5A3B; }
    .bom-table tbody td.short { color: #DC3545; font-weight: 700; }

    .bom-warning {
        margin-top: 10px;
        padding: 10px 14px;
        background: #FFF8E1;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .bom-warning strong {
        font-size: 12px;
        color: #B8860B;
        font-weight: 700;
    }
    .bom-warning span {
        font-size: 11px;
        color: #7C5A0E;
        line-height: 1.4;
    }

    /* FORM ACTIONS */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 20px 0;
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
    .action-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

    .action-btn-secondary {
        background: #F0E6DD;
        color: #212121;
    }
    .action-btn-secondary:hover { background: #E5D5C5; }

    /* MODALS */
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
    }
    .modal-overlay.open { display: flex; }

    .modal-box {
        background: #FFFFFF;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .modal-header {
        padding: 22px 26px 16px;
        border-bottom: 1px solid #F0E6DD;
    }
    .modal-title { font-size: 18px; font-weight: 700; color: #212121; margin: 0; }
    .modal-subtitle { font-size: 13px; color: #64748B; margin-top: 4px; }

    .modal-body { padding: 20px 26px; overflow-y: auto; flex: 1; min-height: 0; }

    .modal-section { margin-bottom: 20px; }
    .modal-section:last-child { margin-bottom: 0; }

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
        padding: 6px 0;
        font-size: 14px;
        gap: 12px;
    }
    .modal-row-label { color: #64748B; }
    .modal-row-value { color: #212121; font-weight: 600; text-align: right; }

    .modal-production-block {
        padding: 14px 16px;
        background: #FDFBFF;
        border-radius: 10px;
        margin-bottom: 12px;
    }
    .modal-production-block:last-child { margin-bottom: 0; }

    .modal-production-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #E2E8F0;
    }
    .modal-production-name { font-size: 14px; font-weight: 700; color: #212121; }
    .modal-production-qty { font-size: 13px; font-weight: 600; color: #6B5B95; }

    .modal-materials-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .modal-material-row {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 10px;
        align-items: center;
        font-size: 12px;
        padding: 4px 0;
    }
    .modal-material-name { color: #212121; font-weight: 500; }
    .modal-material-qty { color: #64748B; white-space: nowrap; }
    .modal-material-status {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        white-space: nowrap;
    }
    .modal-material-status.ok {
        background: #E8F5E9;
        color: #2E5A3B;
    }
    .modal-material-status.short {
        background: #FDECEA;
        color: #DC3545;
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
    .modal-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

    .modal-btn-secondary { background: #F0E6DD; color: #212121; }
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
    .modal-error-title { font-size: 18px; font-weight: 700; color: #212121; margin-bottom: 8px; }
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
        .form-card { padding: 18px; }
        .production-row-main {
            grid-template-columns: 1fr 100px;
        }
        .row-actions {
            grid-column: 1 / -1;
            justify-content: flex-end;
        }
        .modal-box { max-width: 100%; }
        .form-actions .action-btn-primary,
        .form-actions .action-btn-secondary { flex: 1; min-width: 0; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const PRODUCTS = <?php echo json_encode($productionProducts, 15, 512) ?>;

    const container = document.getElementById('productions-container');
    const addButton = document.getElementById('add-production');
    const form = document.getElementById('production-form');
    const confirmModal = document.getElementById('confirm-modal');
    const confirmBtn = document.getElementById('modal-confirm-btn');
    const errorModal = document.getElementById('error-modal');
    const errorMessage = document.getElementById('error-modal-message');

    let rowIndex = 1;

    /* ============================================
       MODAL HELPERS
       ============================================ */
    window.closeConfirmModal = function () {
        confirmModal.classList.remove('open');
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Confirm & Record';
    };
    window.closeErrorModal = function () {
        errorModal.classList.remove('open');
    };
    function showError(message) {
        errorMessage.textContent = message;
        errorModal.classList.add('open');
    }

    confirmModal.addEventListener('click', (e) => { if (e.target === confirmModal) closeConfirmModal(); });
    errorModal.addEventListener('click', (e) => { if (e.target === errorModal) closeErrorModal(); });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') { closeConfirmModal(); closeErrorModal(); }
    });

    /* ============================================
       HELPERS
       ============================================ */
    function formatQty(val) {
        return parseFloat(Number(val).toFixed(2)).toString();
    }

    function findProduct(id) {
        return PRODUCTS.find((p) => Number(p.id) === Number(id));
    }

    /* ============================================
       BOM PREVIEW + SHORTAGE DETECTION
       ============================================ */
    function updateRow(row) {
        const select = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.quantity-input');
        const preview = row.querySelector('.bom-preview');
        const bomBody = row.querySelector('.bom-body');
        const warning = row.querySelector('.bom-warning');
        const toggleBtn = row.querySelector('.toggle-bom-btn');
        const toggleLabel = row.querySelector('.toggle-label');
        const shortageBadge = row.querySelector('.shortage-badge');

        const productId = select.value;
        const quantity = parseFloat(qtyInput.value) || 0;

        bomBody.innerHTML = '';
        warning.style.display = 'none';
        shortageBadge.style.display = 'none';

        // No product or qty → hide everything
        if (!productId || quantity <= 0) {
            toggleBtn.style.display = 'none';
            preview.style.display = 'none';
            return;
        }

        const product = findProduct(productId);
        if (!product || !product.components || product.components.length === 0) {
            toggleBtn.style.display = 'none';
            preview.style.display = 'none';
            return;
        }

        // Show toggle
        toggleBtn.style.display = 'inline-flex';

        let hasShortage = false;

        product.components.forEach((component) => {
            const totalRequired = component.quantity_required * quantity;
            const isShort = totalRequired > component.available;
            if (isShort) hasShortage = true;

            const tr = document.createElement('tr');

            const nameTd = document.createElement('td');
            nameTd.style.fontWeight = '600';
            nameTd.style.color = '#212121';
            nameTd.textContent = component.name;

            const perTd = document.createElement('td');
            perTd.className = 'num muted';
            perTd.textContent = formatQty(component.quantity_required);

            const reqTd = document.createElement('td');
            reqTd.className = 'num ' + (isShort ? 'short' : 'strong');
            reqTd.textContent = formatQty(totalRequired) + (isShort ? ' ⚠' : '');

            const availTd = document.createElement('td');
            availTd.className = 'num ' + (isShort ? 'short' : 'muted');
            availTd.textContent = formatQty(component.available);

            const unitTd = document.createElement('td');
            unitTd.className = 'muted';
            unitTd.textContent = component.stock_unit;

            tr.appendChild(nameTd);
            tr.appendChild(perTd);
            tr.appendChild(reqTd);
            tr.appendChild(availTd);
            tr.appendChild(unitTd);

            bomBody.appendChild(tr);
        });

        if (hasShortage) {
            shortageBadge.style.display = 'inline-block';
            warning.style.display = 'flex';
        }
    }

    /* ============================================
       BOM TOGGLE
       ============================================ */
    function attachRowEvents(row) {
        const select = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.quantity-input');
        const toggleBtn = row.querySelector('.toggle-bom-btn');
        const toggleLabel = row.querySelector('.toggle-label');
        const preview = row.querySelector('.bom-preview');
        const removeBtn = row.querySelector('.remove-production');

        select.addEventListener('change', () => updateRow(row));
        qtyInput.addEventListener('input', () => updateRow(row));

        toggleBtn.addEventListener('click', () => {
            const isOpen = preview.style.display === 'block';

            if (isOpen) {
                preview.style.display = 'none';
                toggleBtn.classList.remove('expanded');
                toggleLabel.textContent = 'Show materials';
            } else {
                preview.style.display = 'block';
                toggleBtn.classList.add('expanded');
                toggleLabel.textContent = 'Hide materials';
            }
        });

        removeBtn.addEventListener('click', () => {
            const rows = document.querySelectorAll('.production-row');
            if (rows.length === 1) {
                // Clear the row instead of removing the only one
                select.value = '';
                qtyInput.value = 1;
                preview.style.display = 'none';
                toggleBtn.classList.remove('expanded');
                updateRow(row);
                return;
            }

            row.remove();
            reindexRows();
        });
    }

    function reindexRows() {
        const rows = document.querySelectorAll('.production-row');
        rows.forEach((row, i) => {
            row.dataset.index = i;

            const select = row.querySelector('.product-select');
            const qtyInput = row.querySelector('.quantity-input');

            select.name = `items[${i}][product_id]`;
            qtyInput.name = `items[${i}][quantity_produced]`;
        });
    }

    /* ============================================
       ADD PRODUCTION ROW
       ============================================ */
    addButton.addEventListener('click', () => {
        const first = document.querySelector('.production-row');
        const clone = first.cloneNode(true);

        // Reset clone
        clone.dataset.index = rowIndex;

        const select = clone.querySelector('.product-select');
        const qtyInput = clone.querySelector('.quantity-input');
        const preview = clone.querySelector('.bom-preview');
        const toggleBtn = clone.querySelector('.toggle-bom-btn');
        const toggleLabel = clone.querySelector('.toggle-label');

        select.value = '';
        qtyInput.value = 1;
        preview.style.display = 'none';
        toggleBtn.style.display = 'none';
        toggleBtn.classList.remove('expanded');
        toggleLabel.textContent = 'Show materials';

        container.appendChild(clone);
        attachRowEvents(clone);
        reindexRows();

        rowIndex++;
    });

    /* ============================================
       INIT
       ============================================ */
    document.querySelectorAll('.production-row').forEach(attachRowEvents);

    /* ============================================
       SUBMIT — VALIDATE → CONFIRM MODAL
       ============================================ */
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const rows = document.querySelectorAll('.production-row');
        const items = [];

        let hasError = false;

        rows.forEach((row, i) => {
            const select = row.querySelector('.product-select');
            const qtyInput = row.querySelector('.quantity-input');
            const quantity = parseFloat(qtyInput.value);

            if (!select.value) {
                showError(`Please select a product for row #${i + 1}.`);
                hasError = true;
                return;
            }

            if (Number.isNaN(quantity) || quantity <= 0) {
                showError(`Please enter a valid quantity for row #${i + 1}.`);
                hasError = true;
                return;
            }

            const product = findProduct(select.value);
            if (!product || !product.components || product.components.length === 0) {
                showError(`"${product?.name ?? 'Product'}" has no Bill of Materials.`);
                hasError = true;
                return;
            }

            items.push({ product, quantity });
        });

        if (hasError) return;

        // Build the modal body
        const modalBody = document.getElementById('modal-body');
        modalBody.innerHTML = '';

        items.forEach((item) => {
            const block = document.createElement('div');
            block.className = 'modal-production-block';

            const header = document.createElement('div');
            header.className = 'modal-production-header';
            header.innerHTML = `
                <span class="modal-production-name">${item.product.name}</span>
                <span class="modal-production-qty">× ${formatQty(item.quantity)}</span>
            `;
            block.appendChild(header);

            const list = document.createElement('div');
            list.className = 'modal-materials-list';

            item.product.components.forEach((component) => {
                const totalRequired = component.quantity_required * item.quantity;
                const isShort = totalRequired > component.available;

                const rowEl = document.createElement('div');
                rowEl.className = 'modal-material-row';
                rowEl.innerHTML = `
                    <span class="modal-material-name">${component.name}</span>
                    <span class="modal-material-qty">${formatQty(totalRequired)} ${component.stock_unit}</span>
                    <span class="modal-material-status ${isShort ? 'short' : 'ok'}">
                        ${isShort ? 'Short' : 'OK'}
                    </span>
                `;
                list.appendChild(rowEl);
            });

            block.appendChild(list);
            modalBody.appendChild(block);
        });

        confirmModal.classList.add('open');
    });

    /* ============================================
       CONFIRM → ACTUALLY SUBMIT
       ============================================ */
    confirmBtn.addEventListener('click', () => {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Recording...';
        closeConfirmModal();
        form.submit();
    });
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/production/create.blade.php ENDPATH**/ ?>