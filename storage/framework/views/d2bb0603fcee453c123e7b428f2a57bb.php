

<?php
$typeLabel = $product->item_type === 'material' ? 'Material' : 'Product';
$tabParam = $product->item_type === 'material' ? 'material' : 'product';
$isArchived = !$product->is_active;
$isOwner = auth()->user()->role === 'owner';

$fmtQty = function ($val) {
$formatted = number_format((float) $val, 2, '.', ',');
$trimmed = rtrim(rtrim($formatted, '0'), '.');
return $trimmed === '' ? '0' : $trimmed;
};
?>

<?php $__env->startSection('title', $product->name); ?>

<?php $__env->startSection('content'); ?>


<div class="page-header">
    <div>
        <h1>
            <?php echo e($product->name); ?>

            <?php if($product->variation): ?>
            <span style="color: #94A3B8; font-weight: 500;">— <?php echo e($product->variation); ?></span>
            <?php endif; ?>
            <?php if($isArchived): ?>
            <span class="archived-tag">Archived</span>
            <?php endif; ?>
        </h1>
        <p><?php echo e($typeLabel); ?> details and Bill of Materials.</p>
    </div>

    <div class="page-actions">
        <?php if($isOwner): ?>
        <?php if($isArchived): ?>
        <form action="<?php echo e(route('products.unarchive', $product)); ?>"
            method="POST" style="display: inline;"
            onsubmit="return confirm('Restore this item?');">
            <?php echo csrf_field(); ?>
            <button type="submit" class="action-btn-primary">Unarchive</button>
        </form>
        <?php else: ?>
        <a href="<?php echo e(route('products.edit', $product)); ?>" class="action-btn-primary">Edit <?php echo e($typeLabel); ?></a>
        <?php endif; ?>
        <?php endif; ?>

        <a href="<?php echo e(route('products.index', ['item_type' => $tabParam])); ?>" class="action-btn-secondary">
            ← Back to Inventory
        </a>
    </div>
</div>


<div class="card" style="margin-bottom: 20px;">
    <h2 class="card-heading">Item Information</h2>

    <div class="info-grid">
        <div class="info-item">
            <span class="info-label">Reference Code</span>
            <span class="info-value mono"><?php echo e($product->reference_code); ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Type</span>
            <span class="info-value"><?php echo e($typeLabel); ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Stock Purpose</span>
            <span class="info-value">
                <?php if($product->stock_purpose === 'retail'): ?> Retail only
                <?php elseif($product->stock_purpose === 'production'): ?> Production only
                <?php else: ?> Both
                <?php endif; ?>
            </span>
        </div>
        <?php if($product->selling_price !== null): ?>
        <div class="info-item">
            <span class="info-label">Selling Price</span>
            <span class="info-value price num">₱<?php echo e(number_format($product->selling_price, 2)); ?></span>
        </div>
        <?php endif; ?>
        <div class="info-item">
            <span class="info-label">Inventory Unit</span>
            <span class="info-value"><?php echo e($product->stock_unit); ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Low Stock Threshold</span>
            <span class="info-value num"><?php echo e($fmtQty($product->low_stock_threshold)); ?></span>
        </div>
        <?php if($product->item_type === 'material' && $product->purchase_unit): ?>
        <div class="info-item">
            <span class="info-label">Purchase Unit</span>
            <span class="info-value"><?php echo e($product->purchase_unit); ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Units per Purchase</span>
            <span class="info-value num"><?php echo e($fmtQty($product->units_per_purchase)); ?></span>
        </div>
        <?php endif; ?>
    </div>
</div>


<div class="card" style="margin-bottom: 20px;">
    <h2 class="card-heading">Current Stock</h2>

    <?php
    $retail = $product->inventory->firstWhere('reserve_type', 'retail');
    $production = $product->inventory->firstWhere('reserve_type', 'production');
    $threshold = (float) $product->low_stock_threshold;
    ?>

    <div class="stock-grid">

        
        <div class="stock-box <?php echo e($retail && $retail->current_quantity <= $threshold ? 'low' : ''); ?>">
            <div class="stock-box-header">
                <span class="stock-label">Retail Stock</span>
                <?php if($retail && $retail->current_quantity <= $threshold): ?>
                    <span class="stock-badge low">Low</span>
                    <?php endif; ?>
            </div>

            <div class="stock-primary">
                <?php if($retail): ?>
                <span class="stock-value <?php echo e($retail->current_quantity <= $threshold ? 'low' : ''); ?>">
                    <?php echo e($fmtQty($retail->current_quantity)); ?>

                </span>
                <span class="stock-unit"><?php echo e($product->stock_unit); ?></span>
                <?php else: ?>
                <span class="stock-empty">—</span>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="stock-box <?php echo e($production && $production->current_quantity <= $threshold ? 'low' : ''); ?>">
            <div class="stock-box-header">
                <span class="stock-label">Production Stock</span>
                <?php if($production && $production->current_quantity <= $threshold): ?>
                    <span class="stock-badge low">Low</span>
                    <?php endif; ?>
            </div>

            <div class="stock-primary">
                <?php if($production): ?>
                <span class="stock-value <?php echo e($production->current_quantity <= $threshold ? 'low' : ''); ?>">
                    <?php echo e($fmtQty($production->current_quantity)); ?>

                </span>
                <span class="stock-unit"><?php echo e($product->stock_unit); ?></span>
                <?php else: ?>
                <span class="stock-empty">—</span>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>


<?php if($product->item_type === 'made_product'): ?>
<div class="card">
    <h2 class="card-heading">Bill of Materials</h2>
    <p style="font-size: 13px; color: #64748B; margin-top: -10px; margin-bottom: 18px;">
        Materials required to make one unit of this product.
    </p>

    <?php if($product->parentComponents->isEmpty()): ?>
    <p style="color: #94A3B8; font-size: 13px; padding: 20px 0;">
        No materials defined for this product.
    </p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Material</th>
                <th class="num">Quantity Required</th>
                <th>Unit</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $product->parentComponents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="font-weight: 600;"><?php echo e($component->materialProduct->display_name); ?></td>
                <td class="num"><?php echo e($fmtQty($component->quantity_required)); ?></td>
                <td style="color: #64748B;"><?php echo e($component->materialProduct->stock_unit); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
<?php endif; ?>


<?php if($product->item_type === 'material' && $product->usedAsComponent->count() > 0): ?>
<div class="card" style="margin-top: 20px;">
    <h2 class="card-heading">Used In</h2>
    <p style="font-size: 13px; color: #64748B; margin-top: -10px; margin-bottom: 18px;">
        Products that use this material.
    </p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="num">Quantity Required</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $product->usedAsComponent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="font-weight: 600;"><?php echo e($component->parentProduct->display_name); ?></td>
                <td class="num"><?php echo e($fmtQty($component->quantity_required)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<style>
    .card-heading {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 18px;
    }

    /* =========================================
       PAGE ACTIONS — aligned with Back button
       ========================================= */
    .page-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .page-actions .action-btn-primary,
    .page-actions .action-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        height: 42px;
        min-height: 42px;
        min-width: 140px;
        padding: 0 20px;
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
        text-decoration: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .page-actions .action-btn-primary {
        background: #6B5B95;
        color: #FFFFFF;
        border: none;
        box-shadow: 0 2px 6px rgba(107, 91, 149, 0.3);
    }

    .page-actions .action-btn-primary:hover {
        background: #594B7D;
        box-shadow: 0 4px 10px rgba(107, 91, 149, 0.4);
    }

    .page-actions .action-btn-secondary {
        background: #FFFFFF;
        color: #212121;
        border: 1.5px solid #F0E6DD;
    }

    .page-actions .action-btn-secondary:hover {
        background: #FEFCF9;
        border-color: #6B5B95;
        color: #6B5B95;
    }

    @media (max-width: 640px) {
        .page-actions {
            width: 100%;
        }

        .page-actions .action-btn-primary,
        .page-actions .action-btn-secondary {
            flex: 1;
            min-width: 0;
        }
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 18px 24px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding-bottom: 14px;
        border-bottom: 1px dashed #F0E6DD;
    }

    .info-label {
        font-size: 11px;
        font-weight: 600;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .info-value {
        font-size: 14px;
        color: #212121;
        font-weight: 500;
    }

    .info-value.num {
        font-variant-numeric: tabular-nums;
    }

    .info-value.mono {
        font-family: 'SF Mono', Consolas, monospace;
        font-weight: 700;
        color: #6B5B95;
        letter-spacing: 0.5px;
    }

    .info-value.price {
        font-weight: 700;
        color: #2E5A3B;
        font-size: 16px;
    }

    table td.num,
    table th.num {
        text-align: right;
        white-space: nowrap;
    }

    .stock-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
    }

    .stock-box {
        padding: 20px;
        background: #FEFCF9;
        border: 1.5px solid #F0E6DD;
        border-radius: 12px;
    }

    .stock-box.low {
        background: #FFF8E1;
        border-color: #FFECB3;
    }

    .stock-box-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .stock-label {
        font-size: 11px;
        font-weight: 700;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .stock-badge {
        font-size: 9px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stock-badge.low {
        background: #FFECB3;
        color: #B8860B;
    }

    .stock-primary {
        display: block;
        text-align: right;
        line-height: 1;
    }

    .stock-value {
        font-size: 32px;
        font-weight: 700;
        color: #2E5A3B;
        font-variant-numeric: tabular-nums;
    }

    .stock-value.low {
        color: #B8860B;
    }

    .stock-unit {
        font-size: 13px;
        font-weight: 500;
        color: #64748B;
        margin-left: 6px;
    }

    .stock-empty {
        font-size: 32px;
        font-weight: 700;
        color: #CBD5E1;
        line-height: 1;
    }

    .archived-tag {
        display: inline-block;
        margin-left: 8px;
        vertical-align: middle;
        padding: 3px 10px;
        border-radius: 6px;
        background: #F1F5F9;
        color: #64748B;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/products/show.blade.php ENDPATH**/ ?>