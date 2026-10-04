

<?php $__env->startSection('title', 'Inventory'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Inventory</h1>
        <p>View available retail and production stock.</p>
    </div>
</div>

<div class="card">

    <?php if($products->isEmpty()): ?>

        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <h3>No products found</h3>
            <p>Add products first to start tracking inventory.</p>
        </div>

    <?php else: ?>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Variation</th>
                        <th style="text-align: right;">Retail Stock</th>
                        <th style="text-align: right;">Production Stock</th>
                        <th>Stock Unit</th>
                        <th>Last Updated</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <?php
                            $retailInventory = $product->inventory->firstWhere('reserve_type', 'retail');
                            $productionInventory = $product->inventory->firstWhere('reserve_type', 'production');
                            $lastUpdated = $product->inventory
                                ->filter(fn ($item) => $item->last_updated)
                                ->sortByDesc('last_updated')
                                ->first();
                        ?>

                        <tr>
                            <td style="font-weight: 600; color: #212121;">
                                <?php echo e($product->name); ?>

                            </td>

                            <td style="color: #64748B;">
                                <?php echo e($product->variation ?: '—'); ?>

                            </td>

                            <td style="text-align: right; font-weight: 600; color: <?php echo e(($retailInventory && $retailInventory->current_quantity > 0) ? '#2E5A3B' : '#94A3B8'); ?>;">
                                <?php echo e($retailInventory ? (float) $retailInventory->current_quantity : '—'); ?>

                            </td>

                            <td style="text-align: right; font-weight: 600; color: <?php echo e(($productionInventory && $productionInventory->current_quantity > 0) ? '#B8860B' : '#94A3B8'); ?>;">
                                <?php echo e($productionInventory ? (float) $productionInventory->current_quantity : '—'); ?>

                            </td>

                            <td style="color: #64748B;">
                                <?php echo e($product->stock_unit); ?>

                            </td>

                            <td style="color: #64748B; white-space: nowrap;">
                                <?php echo e($lastUpdated ? $lastUpdated->last_updated->format('M d, Y h:i A') : '—'); ?>

                            </td>

                            <td style="text-align: right;">
                                <div class="action-buttons">
                                    <?php if($product->inventory->isEmpty()): ?>
                                        <a href="<?php echo e(route('inventory.initial-stock', $product)); ?>" class="action-btn primary">
                                            Set Initial Stock
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('inventory.adjustment', $product)); ?>" class="action-btn primary">
                                            Adjust
                                        </a>
                                        <a href="<?php echo e(route('inventory.history', $product)); ?>" class="action-btn view">
                                            History
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

</div>

<style>
    .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
    }

    .action-btn {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .action-btn.primary {
        background: #FCE4EC;
        color: #E85D75;
    }
    .action-btn.primary:hover {
        background: #F8BBD0;
        color: #D14A62;
    }
    .action-btn.view {
        background: #F1F5F9;
        color: #475569;
    }
    .action-btn.view:hover {
        background: #E2E8F0;
        color: #1E293B;
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/inventory/index.blade.php ENDPATH**/ ?>