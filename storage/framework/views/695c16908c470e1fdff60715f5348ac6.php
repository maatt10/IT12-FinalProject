

<?php $__env->startSection('title', 'Purchase Details'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Stock-in Record</h1>
        <p>Review the recorded purchase and its items.</p>
    </div>

    <a href="<?php echo e(route('records.index', ['tab' => 'purchases'])); ?>" class="btn btn-secondary">
        ← Back to Records
    </a>
</div>


<div class="card" style="margin-bottom: 20px;  #E85D75;">

    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Purchase Information
    </h2>

    <div class="info-grid">

        <div class="info-item">
            <span class="info-label">Purchase Date &amp; Time</span>
            <span class="info-value"><?php echo e($purchase->purchase_date->format('M d, Y h:i A')); ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">Supplier</span>
            <span class="info-value"><?php echo e($purchase->supplier_name ?: '—'); ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">Recorded By</span>
            <span class="info-value"><?php echo e($purchase->user->full_name ?? 'Unknown'); ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">Total Amount</span>
            <span class="info-value price">₱<?php echo e(number_format($purchase->total_amount, 2)); ?></span>
        </div>

    </div>

</div>


<div class="card" style=" #D4AF37;">

    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        Purchased Items
    </h2>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align: right;">Quantity</th>
                    <th>Purchase Unit</th>
                    <th style="text-align: right;">Unit Cost</th>
                    <th>Allocation</th>
                    <th style="text-align: right;">Stock Added</th>
                    <th style="text-align: right;">Line Total</th>
                </tr>
            </thead>

            <tbody>
                <?php $__currentLoopData = $purchase->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                $product = $item->product;
                $stockQuantity = (float) $item->quantity * (float) $product->units_per_purchase;
                ?>

                <tr>
                    <td style="font-weight: 600; color: #212121;">
                        <?php echo e($product->display_name); ?>

                    </td>

                    <td style="text-align: right; color: #212121; font-weight: 500;">
                        <?php echo e((float) $item->quantity); ?>

                    </td>

                    <td style="color: #64748B;">
                        <?php echo e($product->purchase_unit); ?>

                    </td>

                    <td style="text-align: right; color: #64748B;">
                        ₱<?php echo e(number_format($item->unit_cost, 2)); ?>

                    </td>

                    <td>
                        <span class="alloc-tag alloc-<?php echo e($item->reserve_type); ?>">
                            <?php echo e(ucfirst($item->reserve_type)); ?>

                        </span>
                    </td>

                    <td style="text-align: right; font-weight: 600; color: #2E5A3B;">
                        <?php echo e((float) $stockQuantity); ?> <?php echo e($product->stock_unit); ?>

                    </td>

                    <td style="text-align: right; font-weight: 700; color: #2E5A3B;">
                        ₱<?php echo e(number_format($item->quantity * $item->unit_cost, 2)); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

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
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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

    .info-value.price {
        font-weight: 700;
        color: #2E5A3B;
        font-size: 16px;
    }

    /* Allocation tag — plain colored text, non-clickable */
    .alloc-tag {
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        cursor: default;
        user-select: none;
    }

    .alloc-retail {
        color: #E85D75;
    }

    .alloc-production {
        color: #B8860B;
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/purchases/show.blade.php ENDPATH**/ ?>