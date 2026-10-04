

<?php $__env->startSection('title', 'Edit Product'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Edit Product</h1>
        <p>
            Update the information for
            <strong style="color: #E85D75;"><?php echo e($product->name); ?></strong>.
        </p>
    </div>

```
<a href="<?php echo e(route('products.index')); ?>" class="btn btn-secondary">
    ← Back to Products
</a>
```

</div>

<div class="card" style="max-width: 900px;">
    <form action="<?php echo e(route('products.update', $product)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

```
    <div class="form-grid">

        <div class="form-group">
            <label for="item_type">
                Item Type <span class="req">*</span>
            </label>

            <select id="item_type" name="item_type" class="form-control" required>
                <option value="material"
                    <?php echo e(old('item_type', $product->item_type) === 'material' ? 'selected' : ''); ?>>
                    Material
                </option>

                <option value="retail_product"
                    <?php echo e(old('item_type', $product->item_type) === 'retail_product' ? 'selected' : ''); ?>>
                    Retail Product
                </option>

                <option value="made_product"
                    <?php echo e(old('item_type', $product->item_type) === 'made_product' ? 'selected' : ''); ?>>
                    Made Product
                </option>
            </select>

            <small class="field-help">
                Choose whether this item is a material, a purchased retail item, or a product made by the shop.
            </small>

            <?php $__errorArgs = ['item_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="error"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="name">
                Product Name <span class="req">*</span>
            </label>

            <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                value="<?php echo e(old('name', $product->name)); ?>"
                required>

            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="error"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="variation">Variation</label>

            <input
                type="text"
                id="variation"
                name="variation"
                class="form-control"
                value="<?php echo e(old('variation', $product->variation)); ?>"
                placeholder="e.g. Red, Blue, Small, Large">

            <?php $__errorArgs = ['variation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="error"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="is_sellable">
                Sellable <span class="req">*</span>
            </label>

            <select id="is_sellable" name="is_sellable" class="form-control" required>
                <option value="1"
                    <?php echo e(old('is_sellable', $product->is_sellable) == '1' ? 'selected' : ''); ?>>
                    Yes — can be sold to customers
                </option>

                <option value="0"
                    <?php echo e(old('is_sellable', $product->is_sellable) == '0' ? 'selected' : ''); ?>>
                    No — not sold directly
                </option>
            </select>

            <?php $__errorArgs = ['is_sellable'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="error"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="selling_price">Selling Price</label>

            <div class="input-with-prefix">
                <span class="prefix">₱</span>

                <input
                    type="number"
                    id="selling_price"
                    name="selling_price"
                    class="form-control"
                    value="<?php echo e(old('selling_price', $product->selling_price)); ?>"
                    min="0"
                    step="0.01"
                    placeholder="0.00">
            </div>

            <?php $__errorArgs = ['selling_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="error"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="stock_unit">
                Inventory Unit <span class="req">*</span>
            </label>

            <input
                type="text"
                id="stock_unit"
                name="stock_unit"
                class="form-control"
                value="<?php echo e(old('stock_unit', $product->stock_unit)); ?>"
                required>

            <small class="field-help">
                The unit used when counting this item's quantity in the shop.
            </small>

            <?php $__errorArgs = ['stock_unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="error"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div id="purchase-fields" class="purchase-section">

            <div class="section-heading">
                <strong>Purchase Information</strong>
                <span>Only used for materials and retail products.</span>
            </div>

            <div class="purchase-grid">

                <div class="form-group">
                    <label for="purchase_unit">
                        How it is Purchased
                    </label>

                    <input
                        type="text"
                        id="purchase_unit"
                        name="purchase_unit"
                        class="form-control"
                        value="<?php echo e(old('purchase_unit', $product->purchase_unit)); ?>"
                        placeholder="e.g. bundle, box, piece">

                    <?php $__errorArgs = ['purchase_unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="error"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="units_per_purchase">
                        Quantity per Purchase
                    </label>

                    <input
                        type="number"
                        id="units_per_purchase"
                        name="units_per_purchase"
                        class="form-control"
                        value="<?php echo e(old('units_per_purchase', $product->units_per_purchase)); ?>"
                        min="0.01"
                        step="0.01">

                    <small class="field-help">
                        Example: 1 bundle contains 100 pieces.
                    </small>

                    <?php $__errorArgs = ['units_per_purchase'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="error"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

            </div>
        </div>

    </div>

    <div class="form-actions">
        <a href="<?php echo e(route('products.index')); ?>" class="btn btn-secondary">
            Cancel
        </a>

        <button type="submit" class="btn btn-primary">
            Update Product
        </button>
    </div>
</form>
```

</div>

<style>
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 18px 24px;
        margin-bottom: 24px;
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

    .purchase-section {
        grid-column: 1 / -1;
        padding: 18px;
        background: #FAF7F3;
        border: 1px solid #F0E6DD;
        border-radius: 8px;
    }

    .section-heading {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-bottom: 16px;
    }

    .section-heading strong {
        color: #212121;
        font-size: 14px;
    }

    .section-heading span {
        color: #94A3B8;
        font-size: 12px;
    }

    .purchase-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px 24px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 20px;
        border-top: 1px solid #F0E6DD;
    }

    @media (max-width: 650px) {
        .purchase-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemType = document.getElementById('item_type');
        const purchaseFields = document.getElementById('purchase-fields');
        const purchaseUnit = document.getElementById('purchase_unit');
        const unitsPerPurchase = document.getElementById('units_per_purchase');

        function updatePurchaseFields() {
            const isMadeProduct = itemType.value === 'made_product';

            purchaseFields.style.display = isMadeProduct ? 'none' : 'block';

            if (isMadeProduct) {
                purchaseUnit.value = '';
                unitsPerPurchase.value = '';
            }
        }

        itemType.addEventListener('change', updatePurchaseFields);

        updatePurchaseFields();
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/products/edit.blade.php ENDPATH**/ ?>