

<?php $__env->startSection('title', 'Inventory'); ?>

<?php
    $hasFilter = fn($key) => in_array($key, $filters ?? []);
    $buildUrl = function ($newFilters) use ($itemType) {
        return route('products.index', array_filter([
            'item_type' => $itemType,
            'filters' => $newFilters,
        ]));
    };
    $toggleFilter = function ($key) use ($filters, $buildUrl) {
        $current = $filters ?? [];
        if (in_array($key, $current)) {
            $current = array_values(array_diff($current, [$key]));
        } else {
            if ($key === 'archived') {
                $current = ['archived'];
            } else {
                $current = array_values(array_diff($current, ['archived']));
                $current[] = $key;
            }
        }
        return $buildUrl($current);
    };
    $isOwner = auth()->user()->role === 'owner';
?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Inventory</h1>
        <p>Manage products and materials used by the shop.</p>
    </div>
</div>


<div class="item-tabs">
    <a href="<?php echo e(route('products.index', ['item_type' => 'product'])); ?>"
       class="item-tab <?php echo e($itemType === 'product' ? 'active' : ''); ?>">
        Products
    </a>
    <a href="<?php echo e(route('products.index', ['item_type' => 'material'])); ?>"
       class="item-tab <?php echo e($itemType === 'material' ? 'active' : ''); ?>">
        Materials
    </a>
</div>


<div class="pos-search">
    <div class="pos-search-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>
    <input type="text" id="item-search" class="pos-search-input"
           placeholder="Search by name, variation, or reference code..."
           autocomplete="off">
</div>


<div class="item-actions">
    <?php if($itemType === 'product'): ?>
        <a href="<?php echo e(route('production.create')); ?>" class="btn btn-primary">Make Product</a>
        <a href="<?php echo e(route('products.create', ['item_type' => 'product'])); ?>" class="btn btn-secondary">+ Add Product</a>
    <?php else: ?>
        <?php if($isOwner): ?>
            <a href="<?php echo e(route('purchases.create')); ?>" class="btn btn-primary">Restock</a>
            <a href="<?php echo e(route('products.create', ['item_type' => 'material'])); ?>" class="btn btn-secondary">+ Add Material</a>
        <?php endif; ?>
    <?php endif; ?>
</div>


<div class="filter-chips">
    <span class="filter-label">Filters</span>

    <a href="<?php echo e($buildUrl([])); ?>" class="filter-chip <?php echo e(empty($filters) ? 'active' : ''); ?>">All</a>

    <a href="<?php echo e($toggleFilter('low_stock')); ?>" class="filter-chip <?php echo e($hasFilter('low_stock') ? 'active' : ''); ?>">
        Low Stock
    </a>

    <a href="<?php echo e($toggleFilter('sellable')); ?>" class="filter-chip <?php echo e($hasFilter('sellable') ? 'active' : ''); ?>">
        Sellable
    </a>

    <a href="<?php echo e($toggleFilter('production')); ?>" class="filter-chip <?php echo e($hasFilter('production') ? 'active' : ''); ?>">
        Production
    </a>

    <?php if($isOwner): ?>
        <a href="<?php echo e($toggleFilter('archived')); ?>" class="filter-chip archive <?php echo e($hasFilter('archived') ? 'active' : ''); ?>">
            Archived
        </a>
    <?php endif; ?>
</div>


<div class="card">
    <?php if($items->count() > 0): ?>
        <div style="overflow-x: auto;">
            <table id="items-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th><?php echo e($itemType === 'material' ? 'Material Name' : 'Product Name'); ?></th>
                        <th>Variation</th>
                        <th style="text-align: right;">Retail Stock</th>
                        <th style="text-align: right;">Production Stock</th>
                        <th style="text-align: right;">Total</th>
                        <th>Unit</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="clickable-row" onclick="window.location='<?php echo e(route('products.show', $item)); ?>';">
                            <td style="font-family: 'SF Mono', Consolas, monospace; font-size: 12px; color: #64748B; font-weight: 600;">
                                <?php echo e($item->reference_code); ?>

                            </td>

                            <td style="font-weight: 600; color: #212121;">
                                <?php echo e($item->name); ?>

                                <?php if(!$item->is_active): ?>
                                    <span class="archived-tag">Archived</span>
                                <?php endif; ?>
                            </td>

                            <td style="color: #64748B;"><?php echo e($item->variation ?? '—'); ?></td>

                            <td style="text-align: right; <?php echo e($item->retail_low ? 'color: #DC3545; font-weight: 700;' : 'color: #2E5A3B; font-weight: 600;'); ?>">
                                <?php if($item->retail_stock === null): ?>
                                    <span style="color: #CBD5E1;">—</span>
                                <?php else: ?>
                                    <?php echo e((float) $item->retail_stock); ?><?php if($item->retail_low): ?> <span class="low-indicator">LOW</span><?php endif; ?>
                                <?php endif; ?>
                            </td>

                            <td style="text-align: right; <?php echo e($item->production_low ? 'color: #DC3545; font-weight: 700;' : 'color: #2E5A3B; font-weight: 600;'); ?>">
                                <?php if($item->production_stock === null): ?>
                                    <span style="color: #CBD5E1;">—</span>
                                <?php else: ?>
                                    <?php echo e((float) $item->production_stock); ?><?php if($item->production_low): ?> <span class="low-indicator">LOW</span><?php endif; ?>
                                <?php endif; ?>
                            </td>

                            <td style="text-align: right; font-weight: 700; color: #212121;">
                                <?php echo e((float) $item->total_stock); ?>

                            </td>

                            <td style="color: #64748B;"><?php echo e($item->stock_unit); ?></td>

                            <td onclick="event.stopPropagation();">
                                <div class="action-buttons">
                                    <?php if($item->is_active): ?>
                                        <?php if($item->inventory->isEmpty()): ?>
                                            <a href="<?php echo e(route('inventory.initial-stock', $item)); ?>" class="action-btn adjust">Set Stock</a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('inventory.adjustment', $item)); ?>" class="action-btn adjust">Adjust</a>
                                        <?php endif; ?>

                                        <?php if($isOwner): ?>
                                            <form action="<?php echo e(route('products.destroy', $item)); ?>"
                                                  method="POST"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Archive this item?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="action-btn archive">Archive</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($isOwner): ?>
                                            <form action="<?php echo e(route('products.unarchive', $item)); ?>"
                                                  method="POST"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Restore this item?');">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="action-btn unarchive">Unarchive</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            
            <div id="search-empty" style="display: none; text-align: center; padding: 40px 20px; color: #94A3B8;">
                <p style="font-size: 14px;">No items match your search.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <?php if($hasFilter('archived')): ?>
                <h3>No archived items</h3>
                <p>Archived items will appear here.</p>
            <?php elseif(!empty($filters)): ?>
                <h3>No items match the current filters</h3>
                <p>Try removing some filters or switching tabs.</p>
                <a href="<?php echo e(route('products.index', ['item_type' => $itemType])); ?>" class="btn btn-secondary">Clear Filters</a>
            <?php else: ?>
                <h3>No items yet</h3>
                <p>Add a new <?php echo e($itemType === 'material' ? 'material' : 'product'); ?> to get started.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    /* TABS */
    .item-tabs {
        display: flex;
        gap: 28px;
        margin-bottom: 20px;
        border-bottom: 1.5px solid #F0E6DD;
    }
    .item-tab {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 2px; text-decoration: none;
        font-size: 15px; font-weight: 600; color: #94A3B8;
        border-bottom: 2px solid transparent;
        margin-bottom: -1.5px; transition: all 0.15s ease;
    }
    .item-tab:hover { color: #E85D75; }
    .item-tab.active {
        color: #E85D75;
        border-bottom-color: #E85D75;
    }
    .item-tab-count {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 22px; height: 20px; padding: 0 7px;
        border-radius: 10px; background: #F1F5F9;
        font-size: 11px; font-weight: 700; color: #64748B;
    }
    .item-tab.active .item-tab-count {
        background: #FCE4EC; color: #E85D75;
    }

    /* SEARCH */
    .pos-search { position: relative; margin-bottom: 18px; }
    .pos-search-icon {
        position: absolute; left: 8px; top: 50%; transform: translateY(-50%);
        width: 38px; height: 38px; border-radius: 50%;
        background: #E85D75; color: #FFFFFF;
        display: flex; align-items: center; justify-content: center;
        pointer-events: none;
    }
    .pos-search-input {
        width: 100%; height: 54px;
        padding: 0 20px 0 60px;
        border: 1.5px solid #F0E6DD; border-radius: 27px;
        font-size: 15px; background: #FFFFFF; color: #212121;
        transition: all 0.2s ease; font-family: inherit;
    }
    .pos-search-input:focus {
        outline: none; border-color: #E85D75;
        box-shadow: 0 0 0 4px rgba(232, 93, 117, 0.1);
    }
    .pos-search-input::placeholder { color: #B0A99F; }

    /* ACTIONS */
    .item-actions {
        display: flex; gap: 10px; margin-bottom: 18px; flex-wrap: wrap;
    }

    /* FILTER CHIPS */
    .filter-chips {
        display: flex; gap: 8px; margin-bottom: 20px;
        flex-wrap: wrap; align-items: center;
    }
    .filter-label {
        font-size: 11px; color: #94A3B8; font-weight: 700;
        text-transform: uppercase; letter-spacing: 1.5px; margin-right: 6px;
    }
    .filter-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; border-radius: 20px;
        text-decoration: none; font-size: 12px; font-weight: 600;
        color: #64748B; background: #FFFFFF;
        border: 1.5px solid #F0E6DD;
        transition: all 0.15s ease;
    }
    .filter-chip:hover {
        border-color: #F8BBD0; background: #FEFCF9; color: #E85D75;
    }
    .filter-chip.active {
        background: #E85D75; color: #FFFFFF; border-color: #E85D75;
    }
    .filter-chip.archive.active {
        background: #A16207; border-color: #A16207;
    }

    /* ROWS */
    .clickable-row { cursor: pointer; transition: background 0.15s ease; }
    .clickable-row:hover { background: #FFF9FB; }

    .archived-tag {
        display: inline-block; margin-left: 6px;
        padding: 2px 8px; border-radius: 4px;
        background: #F1F5F9; color: #64748B;
        font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .low-indicator {
        display: inline-block; margin-left: 4px;
        padding: 1px 5px; font-size: 9px; font-weight: 700;
        background: #FDECEA; color: #DC3545;
        border-radius: 3px; letter-spacing: 0.5px;
    }

    /* ACTION BUTTONS */
    .action-buttons { display: flex; gap: 6px; justify-content: flex-end; }
    .action-btn {
        display: inline-block; padding: 5px 12px;
        border-radius: 6px; font-size: 12px; font-weight: 600;
        text-decoration: none; cursor: pointer; border: none;
        transition: all 0.15s ease; white-space: nowrap; font-family: inherit;
    }
    .action-btn.adjust { background: #EEF5F0; color: #2E5A3B; }
    .action-btn.adjust:hover { background: #D8E9DF; color: #1E3D28; }
    .action-btn.archive { background: #FFF4E5; color: #A16207; }
    .action-btn.archive:hover { background: #FDE9C7; color: #854D0E; }
    .action-btn.unarchive { background: #E8F5E9; color: #2E5A3B; }
    .action-btn.unarchive:hover { background: #D4EBD6; color: #1E3D28; }

    /* EMPTY */
    .empty-state {
        text-align: center; padding: 50px 20px; color: #64748B;
    }
    .empty-state h3 {
        font-size: 18px; color: #212121; margin-bottom: 6px; font-weight: 700;
    }
    .empty-state p { font-size: 14px; margin-bottom: 20px; }

    @media (max-width: 640px) {
        .item-tabs { gap: 20px; }
        .item-actions .btn { flex: 1; text-align: center; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('item-search');
        const table = document.getElementById('items-table');
        const emptyState = document.getElementById('search-empty');
        if (!searchInput || !table) return;

        const rows = table.querySelectorAll('.clickable-row');

        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase().trim();
            let visible = 0;

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const matches = term === '' || text.includes(term);

                row.style.display = matches ? '' : 'none';
                if (matches) visible++;
            });

            if (emptyState) {
                emptyState.style.display = (visible === 0 && term !== '') ? 'block' : 'none';
            }
        });
    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/products/index.blade.php ENDPATH**/ ?>