

<?php $__env->startSection('title', 'Items'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Items</h1>
        <p>Manage the products and materials used by the shop.</p>
    </div>

    <a href="<?php echo e(route('products.create', ['item_type' => $itemType === 'material' ? 'material' : 'product'])); ?>"
       class="btn btn-primary">
        + Add <?php echo e($itemType === 'material' ? 'Material' : 'Product'); ?>

    </a>
</div>


<div class="item-tabs">

    <a href="<?php echo e(route('products.index', ['item_type' => 'product'])); ?>"
       class="item-tab <?php echo e($itemType === 'product' ? 'active' : ''); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        Products
        <span class="item-tab-count"><?php echo e($productCount); ?></span>
    </a>

    <a href="<?php echo e(route('products.index', ['item_type' => 'material'])); ?>"
       class="item-tab <?php echo e($itemType === 'material' ? 'active' : ''); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        Materials
        <span class="item-tab-count"><?php echo e($materialCount); ?></span>
    </a>

</div>


<div class="card">

    <?php if($items->count() > 0): ?>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php echo e($itemType === 'material' ? 'Material' : 'Product Name'); ?></th>

                        <?php if($itemType === 'product'): ?>
                            <th>Type</th>
                        <?php endif; ?>

                        <th>Variation</th>
                        <th>Sellable</th>

                        <?php if($itemType === 'product'): ?>
                            <th>Selling Price</th>
                        <?php endif; ?>

                        <th>Inventory Unit</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td style="color: #94A3B8; font-size: 12px;">
                                #<?php echo e($item->product_id); ?>

                            </td>

                            <td style="font-weight: 600; color: #212121;">
                                <?php echo e($item->name); ?>

                            </td>

                            <?php if($itemType === 'product'): ?>
                                <td>
                                    <?php if($item->item_type === 'made_product'): ?>
                                        <span class="type-badge made">Made Product</span>
                                    <?php else: ?>
                                        <span class="type-badge retail">Retail Product</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>

                            <td style="color: #64748B;">
                                <?php echo e($item->variation ?? '—'); ?>

                            </td>

                            <td>
                                <?php if($item->is_sellable): ?>
                                    <span class="sellable-yes">✓ Yes</span>
                                <?php else: ?>
                                    <span class="sellable-no">No</span>
                                <?php endif; ?>
                            </td>

                            <?php if($itemType === 'product'): ?>
                                <td style="font-weight: 600; color: #2E5A3B;">
                                    <?php if($item->selling_price !== null): ?>
                                        ₱<?php echo e(number_format($item->selling_price, 2)); ?>

                                    <?php else: ?>
                                        <span style="color: #94A3B8;">—</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>

                            <td style="color: #64748B;">
                                <?php echo e($item->stock_unit); ?>

                            </td>

                            <td>
                                <div class="action-buttons">
                                    <a href="<?php echo e(route('products.show', $item)); ?>" class="action-btn view">
                                        View
                                    </a>

                                    <a href="<?php echo e(route('products.edit', $item)); ?>" class="action-btn edit">
                                        Edit
                                    </a>

                                    <form action="<?php echo e(route('products.destroy', $item)); ?>"
                                          method="POST"
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to archive this <?php echo e($itemType === 'material' ? 'material' : 'product'); ?>? It will no longer appear in the active list.');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>

                                        <button type="submit" class="action-btn archive">
                                            Archive
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>

        <div class="empty-state">
            <?php if($itemType === 'material'): ?>
                <h3>No materials yet</h3>
                <p>Add materials such as flowers, ribbons, beads, or fuzzy wires.</p>
                <a href="<?php echo e(route('products.create', ['item_type' => 'material'])); ?>" class="btn btn-primary">
                    + Add Material
                </a>
            <?php else: ?>
                <h3>No products yet</h3>
                <p>Add a retail product or handmade product to the system.</p>
                <a href="<?php echo e(route('products.create', ['item_type' => 'product'])); ?>" class="btn btn-primary">
                    + Add Product
                </a>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</div>

<style>
    /* TABS */
    .item-tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 20px;
        background: #FFFFFF;
        padding: 6px;
        border-radius: 12px;
        border: 1px solid #F0E6DD;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        width: fit-content;
    }

    .item-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        color: #64748B;
        transition: all 0.15s ease;
    }

    .item-tab:hover {
        background: #FEFCF9;
        color: #E85D75;
    }

    .item-tab.active {
        background: #E85D75;
        color: #FFFFFF;
        box-shadow: 0 2px 8px rgba(232, 93, 117, 0.3);
    }

    .item-tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 20px;
        padding: 0 7px;
        border-radius: 10px;
        background: rgba(0, 0, 0, 0.06);
        font-size: 11px;
        font-weight: 700;
    }

    .item-tab.active .item-tab-count {
        background: rgba(255, 255, 255, 0.25);
    }

    /* ACTION BUTTONS */
    .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
    }

    .action-btn {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .action-btn.view {
        background: #F1F5F9;
        color: #475569;
    }
    .action-btn.view:hover {
        background: #E2E8F0;
        color: #1E293B;
    }

    .action-btn.edit {
        background: #FCE4EC;
        color: #E85D75;
    }
    .action-btn.edit:hover {
        background: #F8BBD0;
        color: #D14A62;
    }

    .action-btn.archive {
        background: #FFF4E5;
        color: #A16207;
    }
    .action-btn.archive:hover {
        background: #FDE9C7;
        color: #854D0E;
    }

    /* TYPE BADGES */
    .type-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
    }
    .type-badge.made {
        background: #FCE4EC;
        color: #C4455D;
    }
    .type-badge.retail {
        background: #EEF5F0;
        color: #2E5A3B;
    }

    /* SELLABLE */
    .sellable-yes {
        color: #2E5A3B;
        font-weight: 600;
        font-size: 13px;
        cursor: default;
        user-select: none;
    }
    .sellable-no {
        color: #94A3B8;
        font-weight: 500;
        font-size: 13px;
        font-style: italic;
        cursor: default;
        user-select: none;
    }

    /* EMPTY STATE */
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #64748B;
    }
    .empty-state h3 {
        font-size: 18px;
        color: #212121;
        margin-bottom: 6px;
        font-weight: 700;
    }
    .empty-state p {
        font-size: 14px;
        margin-bottom: 20px;
    }

    @media (max-width: 640px) {
        .item-tabs {
            width: 100%;
        }
        .item-tab {
            flex: 1;
            justify-content: center;
            padding: 10px 12px;
            font-size: 13px;
        }
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/products/index.blade.php ENDPATH**/ ?>