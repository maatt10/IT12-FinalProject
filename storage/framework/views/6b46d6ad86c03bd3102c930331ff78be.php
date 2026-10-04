

<?php $__env->startSection('title', 'Transfer Stock'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Transfer Stock</h1>
        <p>Move existing stock between retail and production allocations.</p>
    </div>

    <a href="<?php echo e(route('products.index', ['item_type' => $product->item_type === 'material' ? 'material' : 'product'])); ?>"
       class="btn btn-secondary">
        ← Back to Inventory
    </a>
</div>

<div class="card" style="max-width: 720px;">

    <h2 class="card-heading">
        <?php echo e($product->name); ?>

        <?php if($product->variation): ?>
            <span style="color: #94A3B8; font-weight: 500;">— <?php echo e($product->variation); ?></span>
        <?php endif; ?>
    </h2>

    <p style="font-size: 13px; color: #64748B; margin-top: -10px; margin-bottom: 20px;">
        Stock Unit: <strong style="color: #212121;"><?php echo e($product->stock_unit); ?></strong>
    </p>

    <?php if($errors->any()): ?>
        <div class="alert alert-error">
            <ul style="margin-left: 20px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <div class="stock-summary">
        <?php
            $retail = $product->inventory->firstWhere('reserve_type', 'retail');
            $production = $product->inventory->firstWhere('reserve_type', 'production');
        ?>

        <div class="stock-box">
            <span class="stock-label">Retail Stock</span>
            <span class="stock-value">
                <?php echo e($retail ? (float) $retail->current_quantity : 0); ?>

                <span class="stock-unit"><?php echo e($product->stock_unit); ?></span>
            </span>
        </div>

        <div class="stock-box">
            <span class="stock-label">Production Stock</span>
            <span class="stock-value">
                <?php echo e($production ? (float) $production->current_quantity : 0); ?>

                <span class="stock-unit"><?php echo e($product->stock_unit); ?></span>
            </span>
        </div>
    </div>

    <form action="<?php echo e(route('inventory.transfer.store', $product)); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="form-grid">

            <div class="form-group">
                <label for="from">Transfer From <span class="req">*</span></label>
                <select id="from" name="from" class="form-control" required>
                    <option value="">— Select source —</option>
                    <option value="retail" <?php echo e(old('from') === 'retail' ? 'selected' : ''); ?>>
                        Retail Stock (<?php echo e($retail ? (float) $retail->current_quantity : 0); ?> <?php echo e($product->stock_unit); ?>)
                    </option>
                    <option value="production" <?php echo e(old('from') === 'production' ? 'selected' : ''); ?>>
                        Production Stock (<?php echo e($production ? (float) $production->current_quantity : 0); ?> <?php echo e($product->stock_unit); ?>)
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="to">Transfer To <span class="req">*</span></label>
                <select id="to" name="to" class="form-control" required>
                    <option value="">— Select destination —</option>
                    <option value="retail" <?php echo e(old('to') === 'retail' ? 'selected' : ''); ?>>Retail Stock</option>
                    <option value="production" <?php echo e(old('to') === 'production' ? 'selected' : ''); ?>>Production Stock</option>
                </select>
            </div>

        </div>

        <div class="form-group">
            <label for="quantity">Quantity to Transfer <span class="req">*</span></label>
            <input type="number" id="quantity" name="quantity" class="form-control"
                   min="0.01" step="0.01" value="<?php echo e(old('quantity')); ?>"
                   placeholder="Enter quantity" required>
            <small style="color: #94A3B8; font-size: 12px;">
                Must not exceed the available stock in the source allocation.
            </small>
        </div>

        <div class="form-group">
            <label for="notes">Reason / Notes</label>
            <textarea id="notes" name="notes" class="form-control" rows="3" maxlength="1000"
                      placeholder="e.g. Allocating reserved materials for upcoming orders"><?php echo e(old('notes')); ?></textarea>
        </div>

        <div class="form-actions">
            <a href="<?php echo e(route('products.index', ['item_type' => $product->item_type === 'material' ? 'material' : 'product'])); ?>"
               class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Transfer Stock</button>
        </div>

    </form>

</div>

<style>
    .card-heading {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .stock-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stock-box {
        padding: 16px 20px;
        background: #FEFCF9;
        border: 1.5px solid #F0E6DD;
        border-radius: 10px;
    }
    .stock-label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 6px;
    }
    .stock-value {
        font-size: 22px;
        font-weight: 700;
        color: #2E5A3B;
    }
    .stock-unit {
        font-size: 13px;
        font-weight: 500;
        color: #64748B;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px 24px;
        margin-bottom: 8px;
    }

    .req { color: #E85D75; margin-left: 2px; }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 20px;
        margin-top: 10px;
        border-top: 1px solid #F0E6DD;
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/inventory/transfer.blade.php ENDPATH**/ ?>