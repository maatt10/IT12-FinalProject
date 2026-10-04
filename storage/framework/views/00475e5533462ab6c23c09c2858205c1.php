

<?php
    $typeLabel = $itemType === 'material' ? 'Material' : 'Product';
    $tabParam = $itemType === 'material' ? 'material' : 'product';
?>

<?php $__env->startSection('title', 'Add ' . $typeLabel); ?>

<?php $__env->startSection('content'); ?>

<div class="form-page-wide">

    <div class="page-header">
        <div>
            <h1>Add <?php echo e($typeLabel); ?></h1>
            <p>Add a new <?php echo e(strtolower($typeLabel)); ?> to the system.</p>
        </div>

        <a href="<?php echo e(route('products.index', ['item_type' => $tabParam])); ?>" class="btn btn-secondary">
            ← Back to Inventory
        </a>
    </div>

    <div class="card">
        <form action="<?php echo e(route('products.store')); ?>" method="POST" id="product-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="item_type" value="<?php echo e($itemType); ?>">

            <div class="form-grid">

                <div class="form-group">
                    <label for="name"><?php echo e($typeLabel); ?> Name <span class="req">*</span></label>
                    <input type="text" id="name" name="name" class="form-control"
                        value="<?php echo e(old('name')); ?>"
                        placeholder="<?php echo e($itemType === 'material' ? 'e.g. Fuzzy Wire, Red Rose Stem' : 'e.g. Red Rose Bouquet'); ?>"
                        required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="variation">Variation</label>
                    <input type="text" id="variation" name="variation" class="form-control"
                        value="<?php echo e(old('variation')); ?>"
                        placeholder="e.g. Red, Blue, Small, Large">
                    <?php $__errorArgs = ['variation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group full-width">
                    <label for="stock_purpose">Stock Purpose <span class="req">*</span></label>
                    <select id="stock_purpose" name="stock_purpose" class="form-control" required>
                        <option value="">— Select how this item is used —</option>
                        <option value="retail" <?php echo e(old('stock_purpose') === 'retail' ? 'selected' : ''); ?>>Retail only — sold directly to customers</option>
                        <option value="production" <?php echo e(old('stock_purpose') === 'production' ? 'selected' : ''); ?>>Production only — used as a component</option>
                        <option value="both" <?php echo e(old('stock_purpose') === 'both' ? 'selected' : ''); ?>>Both — sold directly AND used as a component</option>
                    </select>
                    <small class="field-help">
                        Determines whether this item appears in POS as sellable, in BOM as a component, or both.
                    </small>
                    <?php $__errorArgs = ['stock_purpose'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group" id="selling-price-field" style="display: none;">
                    <label for="selling_price">Selling Price</label>
                    <div class="input-with-prefix">
                        <span class="prefix">₱</span>
                        <input type="number" id="selling_price" name="selling_price" class="form-control"
                            value="<?php echo e(old('selling_price')); ?>" min="0" step="0.01" placeholder="0.00">
                    </div>
                    <?php $__errorArgs = ['selling_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="stock_unit">Inventory Unit <span class="req">*</span></label>
                    <input type="text" id="stock_unit" name="stock_unit" class="form-control"
                        value="<?php echo e(old('stock_unit')); ?>"
                        placeholder="e.g. piece, stem, bundle"
                        required>
                    <small class="field-help">The unit used when counting this item's quantity.</small>
                    <?php $__errorArgs = ['stock_unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="low_stock_threshold">Low Stock Threshold</label>
                    <input type="number" id="low_stock_threshold" name="low_stock_threshold" class="form-control"
                        value="<?php echo e(old('low_stock_threshold', 10)); ?>" min="0" step="0.01">
                    <small class="field-help">
                        Alert when stock drops to this amount or below. Applied to both retail and production stock.
                    </small>
                    <?php $__errorArgs = ['low_stock_threshold'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <?php if($itemType === 'material'): ?>
                    <div class="purchase-section full-width">
                        <div class="section-heading">
                            <strong>Purchase Information</strong>
                            <span>How this material is bought from suppliers.</span>
                        </div>
                        <div class="purchase-grid">
                            <div class="form-group">
                                <label for="purchase_unit">How it is Purchased</label>
                                <input type="text" id="purchase_unit" name="purchase_unit" class="form-control"
                                    value="<?php echo e(old('purchase_unit')); ?>" placeholder="e.g. bundle, box, piece">
                                <?php $__errorArgs = ['purchase_unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-group">
                                <label for="units_per_purchase">Quantity per Purchase</label>
                                <input type="number" id="units_per_purchase" name="units_per_purchase" class="form-control"
                                    value="<?php echo e(old('units_per_purchase')); ?>" min="0.01" step="0.01" placeholder="e.g. 100">
                                <small class="field-help">Example: 1 bundle contains 100 pieces.</small>
                                <?php $__errorArgs = ['units_per_purchase'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <?php if($itemType === 'made_product'): ?>
                <div class="bom-section">
                    <div class="section-heading-row">
                        <div>
                            <strong>Bill of Materials</strong>
                            <span class="section-sub">Materials required to make one unit of this product.</span>
                        </div>
                        <button type="button" class="btn-add-bom" onclick="addBomRow()">+ Add Material</button>
                    </div>

                    <?php if($materials->isEmpty()): ?>
                        <p style="color: #94A3B8; font-size: 13px; padding: 20px 0;">
                            No materials available. Add materials with "Production" or "Both" stock purpose first.
                        </p>
                    <?php else: ?>
                        <div id="bom-rows"></div>
                        <p id="bom-empty" style="color: #94A3B8; font-size: 13px; padding: 10px 0;">
                            No materials added yet.
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <a href="<?php echo e(route('products.index', ['item_type' => $tabParam])); ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save <?php echo e($typeLabel); ?></button>
            </div>
        </form>
    </div>

</div>

<style>
    .form-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 18px 24px;
        margin-bottom: 24px;
    }

    .form-grid > .form-group {
        flex: 1 1 260px;
        min-width: 0;
        margin-bottom: 0;
    }

    .form-grid > .form-group.full-width {
        flex-basis: 100%;
    }

    .req { color: #E85D75; margin-left: 2px; }

    .field-help {
        display: block;
        margin-top: 6px;
        color: #94A3B8;
        font-size: 12px;
        line-height: 1.4;
    }

    .input-with-prefix { position: relative; }
    .input-with-prefix .prefix {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #94A3B8; font-weight: 600; font-size: 14px; pointer-events: none;
    }
    .input-with-prefix .form-control { padding-left: 32px; }

    .purchase-section, .bom-section {
        flex-basis: 100%;
        padding: 18px;
        background: #FAF7F3;
        border: 1px solid #F0E6DD;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .section-heading, .section-heading-row > div {
        display: flex; flex-direction: column; gap: 4px; margin-bottom: 16px;
    }
    .section-heading-row {
        display: flex; justify-content: space-between; align-items: flex-start;
        gap: 12px; margin-bottom: 16px;
    }
    .section-heading strong, .section-heading-row strong { color: #212121; font-size: 14px; }
    .section-heading span, .section-sub { color: #94A3B8; font-size: 12px; }

    .purchase-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px 24px; }

    .btn-add-bom {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px; border-radius: 8px;
        background: #E85D75; color: #FFFFFF;
        border: none; font-size: 13px; font-weight: 600;
        cursor: pointer; font-family: inherit;
    }
    .btn-add-bom:hover { background: #D14A62; }

    .bom-row {
        display: grid; grid-template-columns: 1fr 140px 40px; gap: 10px;
        align-items: center; padding: 10px;
        background: #FFFFFF; border: 1px solid #F0E6DD;
        border-radius: 8px; margin-bottom: 8px;
    }
    .bom-row select, .bom-row input {
        padding: 8px 10px; border: 1.5px solid #F0E6DD;
        border-radius: 6px; font-size: 13px; font-family: inherit; background: #FFFFFF;
    }
    .bom-row select:focus, .bom-row input:focus { outline: none; border-color: #E85D75; }
    .bom-remove {
        width: 32px; height: 32px; background: #FDECEA; color: #DC3545;
        border: none; border-radius: 6px; cursor: pointer;
        font-size: 16px; font-weight: 700; font-family: inherit;
    }
    .bom-remove:hover { background: #F8D7DA; }

    .form-actions {
        display: flex; justify-content: flex-end; gap: 10px;
        padding-top: 20px; border-top: 1px solid #F0E6DD;
    }

    @media (max-width: 650px) {
        .purchase-grid { grid-template-columns: 1fr; }
        .bom-row { grid-template-columns: 1fr 80px 40px; }
    }
</style>

<script>
    const MATERIALS = <?php echo json_encode($materials->map(fn($m) => ['id' => $m->product_id, 'name' => $m->display_name]), 512) ?>;

    document.addEventListener('DOMContentLoaded', function () {
        const stockPurpose = document.getElementById('stock_purpose');
        const sellingPriceField = document.getElementById('selling-price-field');

        function updateFieldVisibility() {
            const purpose = stockPurpose.value;
            const hasRetail = purpose === 'retail' || purpose === 'both';
            sellingPriceField.style.display = hasRetail ? 'block' : 'none';
        }
        stockPurpose.addEventListener('change', updateFieldVisibility);
        updateFieldVisibility();

        <?php if($itemType === 'made_product' && !$materials->isEmpty()): ?>
            addBomRow();
        <?php endif; ?>
    });

    function addBomRow() {
        const container = document.getElementById('bom-rows');
        const emptyMsg = document.getElementById('bom-empty');
        if (!container) return;

        const index = container.children.length;

        const row = document.createElement('div');
        row.className = 'bom-row';
        row.innerHTML = `
            <select name="bom[${index}][material_product_id]" required onchange="refreshBomOptions()">
                <option value="">— Select material —</option>
                ${MATERIALS.map(m => `<option value="${m.id}">${m.name}</option>`).join('')}
            </select>
            <input type="number" name="bom[${index}][quantity_required]" min="0.01" step="0.01" placeholder="Qty required" required>
            <button type="button" class="bom-remove" onclick="removeBomRow(this)">×</button>
        `;
        container.appendChild(row);

        if (emptyMsg) emptyMsg.style.display = 'none';
        refreshBomOptions();
    }

    function removeBomRow(btn) {
        btn.parentElement.remove();
        reindexBom();
        refreshBomOptions();
    }

    function refreshBomOptions() {
        const rows = document.querySelectorAll('.bom-row');
        if (!rows.length) return;

        const selectedIds = new Set();
        rows.forEach(row => {
            const sel = row.querySelector('select');
            if (sel.value) selectedIds.add(sel.value);
        });

        rows.forEach(row => {
            const sel = row.querySelector('select');
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

    function reindexBom() {
        const container = document.getElementById('bom-rows');
        if (!container) return;
        const rows = container.querySelectorAll('.bom-row');
        const emptyMsg = document.getElementById('bom-empty');

        rows.forEach((row, i) => {
            row.querySelector('select').name = `bom[${i}][material_product_id]`;
            row.querySelector('input').name = `bom[${i}][quantity_required]`;
        });

        if (emptyMsg) emptyMsg.style.display = rows.length === 0 ? 'block' : 'none';
    }
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/products/create.blade.php ENDPATH**/ ?>