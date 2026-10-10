

<?php $__env->startSection('title', 'Records'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Records</h1>
        <p>View transaction records and historical data.</p>
    </div>
</div>


<div class="record-tabs">
    <a href="<?php echo e(route('records.index', ['tab' => 'sales'])); ?>"
        class="record-tab <?php echo e($tab === 'sales' ? 'active' : ''); ?>">
        Sales
    </a>
    <a href="<?php echo e(route('records.index', ['tab' => 'purchases'])); ?>"
        class="record-tab <?php echo e($tab === 'purchases' ? 'active' : ''); ?>">
        Purchases
    </a>
    <a href="<?php echo e(route('records.index', ['tab' => 'production'])); ?>"
        class="record-tab <?php echo e($tab === 'production' ? 'active' : ''); ?>">
        Production
    </a>
    <a href="<?php echo e(route('records.index', ['tab' => 'customers'])); ?>"
        class="record-tab <?php echo e($tab === 'customers' ? 'active' : ''); ?>">
        Customers
    </a>
</div>


<?php if($tab === 'sales'): ?>
<div class="record-sub-tabs">
    <a href="<?php echo e(route('records.index', ['tab' => 'sales', 'sub' => 'walk-in'])); ?>"
        class="record-sub-tab <?php echo e($sub === 'walk-in' ? 'active' : ''); ?>">
        Walk-in Sales
    </a>
    <a href="<?php echo e(route('records.index', ['tab' => 'sales', 'sub' => 'online'])); ?>"
        class="record-sub-tab <?php echo e($sub === 'online' ? 'active' : ''); ?>">
        Orders
    </a>
</div>
<?php endif; ?>


<?php if($tab === 'customers'): ?>
<div class="tab-action-row">
    <a href="<?php echo e(route('customers.create', ['from' => 'records'])); ?>" class="btn btn-primary">
        + Add Customer
    </a>
</div>
<?php endif; ?>

<div class="card">

    
    <?php if($tab === 'sales' && $sub === 'walk-in'): ?>

    <?php if($sales->isEmpty()): ?>
    <div class="empty-state">
        <h3>No walk-in sales yet</h3>
        <p>Sales recorded at the POS will appear here.</p>
    </div>
    <?php else: ?>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Receipt No.</th>
                    <th>Date &amp; Time</th>
                    <th>Customer</th>
                    <th>Payment</th>
                    <th class="num">Total</th>
                    <th class="num">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="font-family: 'SF Mono', Consolas, monospace; font-size: 12px; color: #64748B; font-weight: 600;">
                        <?php echo e($sale->reference_code); ?>

                    </td>
                    <td style="color: #64748B; white-space: nowrap;">
                        <?php echo e($sale->sale_date->format('M d, Y h:i A')); ?>

                    </td>
                    <td style="font-weight: 500;">
                        <?php echo e($sale->customer->full_name ?? 'Walk-in'); ?>

                    </td>
                    <td>
                        <span style="font-size: 12px; font-weight: 600; text-transform: capitalize; color: <?php echo e($sale->payment_method === 'cash' ? '#6B5B95' : '#2E5A3B'); ?>;">
                            <?php echo e(str_replace('_', ' ', $sale->payment_method)); ?>

                        </span>
                    </td>
                    <td class="num" style="font-weight: 700; color: #2E5A3B;">
                        ₱<?php echo e(number_format($sale->total_amount, 2)); ?>

                    </td>
                    <td class="num">
                        <a href="<?php echo e(route('sales.print', $sale)); ?>?from=records"
                            class="action-btn view">
                            View Receipt
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <?php echo $__env->make('partials.pagination', ['paginator' => $sales->appends(['tab' => 'sales', 'sub' => 'walk-in'])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    
    <?php elseif($tab === 'sales' && $sub === 'online'): ?>

    <?php if($orders->isEmpty()): ?>
    <div class="empty-state">
        <h3>No orders yet</h3>
        <p>Orders recorded will appear here.</p>
    </div>
    <?php else: ?>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Order No.</th>
                    <th>Date &amp; Time</th>
                    <th>Customer</th>
                    <th>Channel</th>
                    <th>Status</th>
                    <th class="num">Total</th>
                    <th class="num">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="font-family: 'SF Mono', Consolas, monospace; font-size: 12px; color: #64748B; font-weight: 600;">
                        <?php echo e($order->reference_code); ?>

                    </td>
                    <td style="color: #64748B; white-space: nowrap;">
                        <?php echo e($order->order_date->format('M d, Y h:i A')); ?>

                    </td>
                    <td style="font-weight: 500;">
                        <?php echo e($order->customer->full_name ?? $order->customer_name ?? 'Unregistered'); ?>

                    </td>
                    <td style="color: #64748B;">
                        <?php echo e($order->channel === 'walk_in' ? 'Walk-in' : 'Online'); ?>

                    </td>
                    <td>
                        <span style="font-size: 12px; font-weight: 600; text-transform: capitalize;
                                        color: <?php switch($order->order_status):
                                            case ('pending'): ?> #B8860B <?php break; ?>
                                            <?php case ('completed'): ?> #2E5A3B <?php break; ?>
                                            <?php case ('cancelled'): ?> #DC3545 <?php break; ?>
                                            <?php default: ?> #64748B
                                        <?php endswitch; ?>;">
                            <?php echo e(str_replace('_', ' ', $order->order_status)); ?>

                        </span>
                    </td>
                    <td class="num" style="font-weight: 700; color: #2E5A3B;">
                        ₱<?php echo e(number_format($order->total_amount, 2)); ?>

                    </td>
                    <td class="num">
                        <div class="action-group">
                            <a href="<?php echo e(route('orders.show', ['order' => $order, 'from' => 'records'])); ?>" class="action-btn view">
                                View
                            </a>
                            <a href="<?php echo e(route('orders.print', $order)); ?>?from=records" class="action-btn receipt">
                                Receipt
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <?php echo $__env->make('partials.pagination', ['paginator' => $orders->appends(['tab' => 'sales', 'sub' => 'online'])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    
    <?php elseif($tab === 'purchases'): ?>

    <?php if($purchases->isEmpty()): ?>
    <div class="empty-state">
        <h3>No stock-in records yet</h3>
        <p>Restocking entries will appear here.</p>
    </div>
    <?php else: ?>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Date &amp; Time</th>
                    <th>Supplier</th>
                    <th>Recorded By</th>
                    <th class="num">Total Amount</th>
                    <th class="num">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="color: #64748B; white-space: nowrap;">
                        <?php echo e($purchase->purchase_date->format('M d, Y h:i A')); ?>

                    </td>
                    <td style="font-weight: 500;">
                        <?php echo e($purchase->supplier_name ?: '—'); ?>

                    </td>
                    <td style="color: #64748B;">
                        <?php echo e($purchase->user->full_name ?? 'Unknown'); ?>

                    </td>
                    <td class="num" style="font-weight: 700; color: #2E5A3B;">
                        ₱<?php echo e(number_format($purchase->total_amount, 2)); ?>

                    </td>
                    <td class="num">
                        <a href="<?php echo e(route('purchases.show', $purchase)); ?>" class="action-btn view">
                            View
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <?php echo $__env->make('partials.pagination', ['paginator' => $purchases->appends(['tab' => 'purchases'])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    
    <?php elseif($tab === 'production'): ?>

    <?php if($productions->isEmpty()): ?>
    <div class="empty-state">
        <h3>No production records yet</h3>
        <p>Production entries will appear here.</p>
    </div>
    <?php else: ?>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Date &amp; Time</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Produced By</th>
                    <th class="num">Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $productions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $production): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="color: #64748B; white-space: nowrap;">
                        <?php echo e($production->production_date->format('M d, Y h:i A')); ?>

                    </td>
                    <td style="font-weight: 600;">
                        <?php if($production->product): ?>
                        <?php echo e($production->product->display_name); ?>

                        <?php else: ?>
                        <span style="font-style: italic; color: #64748B;">Custom Bouquet</span>
                        <?php if($production->order): ?>
                        <div style="font-size: 11px; color: #94A3B8; font-family: 'SF Mono', Consolas, monospace; margin-top: 2px;">
                            <?php echo e($production->order->reference_code); ?>

                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td style="color: #64748B;">
                        <?php if($production->order_id && !$production->product_id): ?>
                        Custom
                        <?php else: ?>
                        Standard
                        <?php endif; ?>
                    </td>
                    <td style="color: #64748B;">
                        <?php echo e($production->producedBy->full_name ?? 'Unknown'); ?>

                    </td>
                    <td class="num" style="font-weight: 700; color: #2E5A3B;">
                        <?php if($production->product): ?>
                        <?php echo e((float) $production->quantity_produced); ?> <?php echo e($production->product->stock_unit); ?>

                        <?php else: ?>
                        <?php echo e((float) $production->quantity_produced); ?> bouquet<?php echo e($production->quantity_produced > 1 ? 's' : ''); ?>

                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <?php echo $__env->make('partials.pagination', ['paginator' => $productions->appends(['tab' => 'production'])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    
    <?php elseif($tab === 'customers'): ?>

    <?php if($customers->isEmpty()): ?>
    <div class="empty-state">
        <h3>No customers yet</h3>
        <p>Click "+ Add Customer" to register the first one.</p>
    </div>
    <?php else: ?>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact Number</th>
                    <th>Address</th>
                    <th>Regular</th>
                    <th class="num">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="font-weight: 600;">
                        <?php echo e($customer->full_name); ?>

                    </td>
                    <td style="color: #64748B;">
                        <?php echo e($customer->contact_number); ?>

                    </td>
                    <td style="color: #64748B;">
                        <?php echo e($customer->address ?: '—'); ?>

                    </td>
                    <td>
                        <?php if($customer->is_regular): ?>
                        <span style="color: #2E5A3B; font-weight: 600; font-size: 13px;">Yes</span>
                        <?php else: ?>
                        <span style="color: #94A3B8; font-style: italic; font-size: 13px;">No</span>
                        <?php endif; ?>
                    </td>
                    <td class="num">
                        <a href="<?php echo e(route('customers.edit', ['customer' => $customer, 'from' => 'records'])); ?>"
                            class="action-btn view">
                            View
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <?php echo $__env->make('partials.pagination', ['paginator' => $customers->appends(['tab' => 'customers'])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <?php endif; ?>

</div>

<style>
    .record-tabs {
        display: flex;
        gap: 28px;
        margin-bottom: 20px;
        border-bottom: 1.5px solid #F0E6DD;
    }

    .record-tab {
        display: inline-flex;
        align-items: center;
        padding: 10px 2px;
        text-decoration: none;
        font-size: 15px;
        font-weight: 600;
        color: #94A3B8;
        border-bottom: 2px solid transparent;
        margin-bottom: -1.5px;
        transition: all 0.15s ease;
    }

    .record-tab:hover {
        color: #6B5B95;
    }

    .record-tab.active {
        color: #6B5B95;
        border-bottom-color: #6B5B95;
    }

    .record-sub-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        background: #FFFFFF;
        padding: 4px;
        border-radius: 10px;
        border: 1px solid #F0E6DD;
        width: fit-content;
    }

    .record-sub-tab {
        padding: 8px 18px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        color: #64748B;
        transition: all 0.15s ease;
    }

    .record-sub-tab:hover {
        background: #FEFCF9;
        color: #6B5B95;
    }

    .record-sub-tab.active {
        background: #EFEBF7;
        color: #6B5B95;
    }

    .tab-action-row {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 16px;
    }

    .action-group {
        display: inline-flex;
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
        font-family: inherit;
    }

    .action-btn.view {
        background: #EFEBF7;
        color: #6B5B95;
    }

    .action-btn.view:hover {
        background: #D5C9E8;
        color: #594B7D;
    }

    .action-btn.receipt {
        background: #F1F5F9;
        color: #475569;
    }

    .action-btn.receipt:hover {
        background: #E2E8F0;
        color: #1E293B;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
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
    }

    /* ===============================
       CLEAN PAGINATION
       =============================== */
    .clean-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 16px;
        margin-top: 20px;
        border-top: 1px solid #F0E6DD;
        flex-wrap: wrap;
    }

    .clean-pagination .pagination-info {
        font-size: 12px;
        color: #64748B;
        font-weight: 400;
    }

    .clean-pagination .pagination-info strong {
        color: #212121;
        font-weight: 700;
        font-size: 12px;
    }

    .clean-pagination .pagination-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .clean-pagination .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 14px;
        border-radius: 6px;
        background: #FFFFFF;
        border: 1.5px solid #F0E6DD;
        color: #64748B;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .clean-pagination .page-btn:hover:not(:disabled) {
        border-color: #6B5B95;
        color: #6B5B95;
    }

    .clean-pagination .page-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .clean-pagination .page-indicator {
        font-size: 12px;
        font-weight: 700;
        color: #212121;
        padding: 0 6px;
        min-width: 50px;
        text-align: center;
    }

    @media (max-width: 640px) {
        .record-tabs {
            gap: 20px;
            overflow-x: auto;
        }

        .record-tab {
            font-size: 13px;
            white-space: nowrap;
        }

        .clean-pagination {
            justify-content: center;
        }

        .clean-pagination .pagination-info {
            width: 100%;
            text-align: center;
        }
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/records/index.blade.php ENDPATH**/ ?>