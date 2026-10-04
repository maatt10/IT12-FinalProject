

<?php $__env->startSection('title', 'Inventory History'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Inventory History</h1>
        <p>
            Transaction history for
            <strong style="color: #E85D75;"><?php echo e($product->name); ?></strong>
            <?php if($product->variation): ?>
            — <?php echo e($product->variation); ?>

            <?php endif; ?>
        </p>
    </div>

    <a href="<?php echo e(route('inventory.index')); ?>" class="btn btn-secondary">
        ← Back to Inventory
    </a>
</div>

<div class="card">

    <?php if($transactions->isEmpty()): ?>

    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3>No transactions yet</h3>
        <p>This product has no inventory history.</p>
    </div>

    <?php else: ?>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Date &amp; Time</th>
                    <th>Stock Allocation</th>
                    <th>Transaction</th>
                    <th style="text-align: right;">Quantity Change</th>
                    <th>Recorded By</th>
                    <th>Notes</th>
                </tr>
            </thead>

            <tbody>
                <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                $change = (float) $transaction->quantity_change;
                ?>

                <tr>
                    <td style="color: #64748B; white-space: nowrap;">
                        <?php echo e($transaction->transaction_date ? $transaction->transaction_date->format('M d, Y h:i A') : '—'); ?>

                    </td>

                    <td>
                        <span class="alloc-tag alloc-<?php echo e($transaction->reserve_type); ?>">
                            <?php echo e(ucfirst($transaction->reserve_type)); ?>

                        </span>
                    </td>

                    <td style="color: #64748B;">
                        <?php echo e(ucwords(str_replace('_', ' ', $transaction->transaction_type))); ?>

                    </td>

                    <td style="text-align: right; font-weight: 700; color: <?php echo e($change > 0 ? '#2E5A3B' : ($change < 0 ? '#DC3545' : '#94A3B8')); ?>;">
                        <?php echo e($change > 0 ? '+' : ''); ?><?php echo e((float) $change); ?> <?php echo e($product->stock_unit); ?>

                    </td>

                    <td style="color: #64748B;">
                        <?php echo e($transaction->recordedBy ? $transaction->recordedBy->first_name . ' ' . $transaction->recordedBy->last_name : '—'); ?>

                    </td>

                    <td style="color: #64748B; max-width: 240px;">
                        <?php echo e($transaction->notes ?: '—'); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <?php endif; ?>

</div>

<style>
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

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748B;
    }

    .empty-state svg {
        color: #D4AF37;
        margin-bottom: 16px;
        opacity: 0.6;
    }

    .empty-state h3 {
        font-size: 18px;
        color: #212121;
        margin-bottom: 6px;
        font-weight: 700;
    }

    .empty-state p {
        font-size: 14px;
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/inventory/history.blade.php ENDPATH**/ ?>