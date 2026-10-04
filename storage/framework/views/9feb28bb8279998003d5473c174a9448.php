

<?php $__env->startSection('title', 'Add BOM Component'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">

```
<div>
    <h1>Add BOM Component</h1>

    <p>
        Add a material required to produce
        <strong style="color: #E85D75;"><?php echo e($product->display_name); ?></strong>.
    </p>
</div>

<a href="<?php echo e(route('products.show', $product)); ?>" class="btn btn-secondary">
    ← Back to Product
</a>
```

</div>

<div class="card" style="max-width: 640px; border-top: 4px solid #D4AF37;">

```
<form action="<?php echo e(route('products.components.store', $product)); ?>" method="POST">
    <?php echo csrf_field(); ?>

    <div class="form-group">

        <label for="material_product_id">
            Material <span class="req">*</span>
        </label>

        <select
            id="material_product_id"
            name="material_product_id"
            class="form-control"
            required
        >
            <option value="">— Select a material —</option>

            <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option
                    value="<?php echo e($material->product_id); ?>"
                    <?php echo e(old('material_product_id') == $material->product_id ? 'selected' : ''); ?>

                >
                    <?php echo e($material->display_name); ?>

                    — <?php echo e($material->stock_unit); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <?php if($materials->isEmpty()): ?>
            <div class="error">
                No active materials are currently available to add to this BOM.
            </div>
        <?php endif; ?>

        <?php $__errorArgs = ['material_product_id'];
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

        <label for="quantity_required">
            Quantity Required <span class="req">*</span>
        </label>

        <input
            type="number"
            id="quantity_required"
            name="quantity_required"
            class="form-control"
            value="<?php echo e(old('quantity_required')); ?>"
            min="0.01"
            step="0.01"
            placeholder="e.g. 8"
            required
        >

        <?php $__errorArgs = ['quantity_required'];
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

    <div class="form-actions">

        <a
            href="<?php echo e(route('products.show', $product)); ?>"
            class="btn btn-secondary"
        >
            Cancel
        </a>

        <button
            type="submit"
            class="btn btn-primary"
            <?php echo e($materials->isEmpty() ? 'disabled' : ''); ?>

        >
            Add Component
        </button>

    </div>

</form>
```

</div>

<style>

    .req {
        color: #E85D75;
        margin-left: 2px;
    }

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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/products/components/create.blade.php ENDPATH**/ ?>