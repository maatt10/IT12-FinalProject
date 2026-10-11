

<?php $__env->startSection('title', 'Records'); ?>

<?php
    $anyFilter = !empty($search) || !empty($from) || !empty($to)
        || !empty($payment) || !empty($status) || !empty($channel) || !empty($regular);

    // Build a URL preserving all current filters, overriding only what's passed
    $buildUrl = function (array $overrides = []) use (
        $tab, $sub, $search, $from, $to, $payment, $status, $channel, $regular
    ) {
        $params = ['tab' => $tab];
        if ($tab === 'sales') $params['sub'] = $sub;

        $defaults = [
            'search' => $search,
            'from' => $from,
            'to' => $to,
            'payment' => $payment,
            'status' => $status,
            'channel' => $channel,
            'regular' => $regular,
        ];

        $merged = array_merge($defaults, $overrides);

        foreach ($merged as $k => $v) {
            if ($v !== null && $v !== '') {
                $params[$k] = $v;
            }
        }

        return route('records.index', $params);
    };

    // Toggle a single-select filter (if already active, clear it)
    $toggleFilter = function (string $key, string $value) use ($buildUrl, $payment, $status, $channel, $regular) {
        $current = [
            'payment' => $payment,
            'status' => $status,
            'channel' => $channel,
            'regular' => $regular,
        ][$key] ?? null;

        return $buildUrl([$key => $current === $value ? '' : $value]);
    };

    $tabUrl = fn($newTab, $newSub = null) =>
        route('records.index', $newTab === 'sales'
            ? ['tab' => 'sales', 'sub' => $newSub ?? 'walk-in']
            : ['tab' => $newTab]);

    $subUrl = fn($newSub) => route('records.index', ['tab' => 'sales', 'sub' => $newSub]);
?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Records</h1>
        <p>View transaction records and historical data.</p>
    </div>
</div>


<div class="record-tabs">
    <a href="<?php echo e($tabUrl('sales', 'walk-in')); ?>" class="record-tab <?php echo e($tab === 'sales' ? 'active' : ''); ?>">Sales</a>
    <a href="<?php echo e($tabUrl('purchases')); ?>" class="record-tab <?php echo e($tab === 'purchases' ? 'active' : ''); ?>">Purchases</a>
    <a href="<?php echo e($tabUrl('production')); ?>" class="record-tab <?php echo e($tab === 'production' ? 'active' : ''); ?>">Production</a>
    <a href="<?php echo e($tabUrl('customers')); ?>" class="record-tab <?php echo e($tab === 'customers' ? 'active' : ''); ?>">Customers</a>
</div>


<?php if($tab === 'sales'): ?>
    <div class="record-sub-tabs">
        <a href="<?php echo e($subUrl('walk-in')); ?>" class="record-sub-tab <?php echo e($sub === 'walk-in' ? 'active' : ''); ?>">Walk-in Sales</a>
        <a href="<?php echo e($subUrl('online')); ?>" class="record-sub-tab <?php echo e($sub === 'online' ? 'active' : ''); ?>">Orders</a>
    </div>
<?php endif; ?>


<form method="GET" action="<?php echo e(route('records.index')); ?>" class="pos-search">
    <input type="hidden" name="tab" value="<?php echo e($tab); ?>">
    <?php if($tab === 'sales'): ?>
        <input type="hidden" name="sub" value="<?php echo e($sub); ?>">
    <?php endif; ?>
    <?php if($payment): ?> <input type="hidden" name="payment" value="<?php echo e($payment); ?>"> <?php endif; ?>
    <?php if($status): ?>  <input type="hidden" name="status"  value="<?php echo e($status); ?>">  <?php endif; ?>
    <?php if($channel): ?> <input type="hidden" name="channel" value="<?php echo e($channel); ?>"> <?php endif; ?>
    <?php if($regular): ?> <input type="hidden" name="regular" value="<?php echo e($regular); ?>"> <?php endif; ?>

    <div class="pos-search-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>
    <input type="text" name="search" id="records-search" class="pos-search-input"
           placeholder="<?php if($tab === 'sales' && $sub === 'walk-in'): ?>Search receipt no. or customer…<?php elseif($tab === 'sales' && $sub === 'online'): ?>Search order no. or customer…<?php elseif($tab === 'purchases'): ?>Search supplier…<?php elseif($tab === 'production'): ?>Search product, order, or notes…<?php else: ?> Search name, contact, or address…<?php endif; ?>"
           value="<?php echo e($search); ?>"
           autocomplete="off">
</form>


<form method="GET" action="<?php echo e(route('records.index')); ?>" class="filter-row">
    <input type="hidden" name="tab" value="<?php echo e($tab); ?>">
    <?php if($tab === 'sales'): ?>
        <input type="hidden" name="sub" value="<?php echo e($sub); ?>">
    <?php endif; ?>
    <?php if($search): ?> <input type="hidden" name="search" value="<?php echo e($search); ?>"> <?php endif; ?>

    <span class="filter-label">Filters</span>

    
    <?php if($tab === 'sales' && $sub === 'walk-in'): ?>
        <a href="<?php echo e($buildUrl(['payment' => ''])); ?>" class="filter-chip <?php echo e(empty($payment) ? 'active' : ''); ?>">All</a>
        <a href="<?php echo e($toggleFilter('payment', 'cash')); ?>"  class="filter-chip <?php echo e($payment === 'cash'  ? 'active' : ''); ?>">Cash</a>
        <a href="<?php echo e($toggleFilter('payment', 'gcash')); ?>" class="filter-chip <?php echo e($payment === 'gcash' ? 'active' : ''); ?>">GCash</a>
    <?php endif; ?>

    
    <?php if($tab === 'sales' && $sub === 'online'): ?>
        <a href="<?php echo e($buildUrl(['status' => ''])); ?>" class="filter-chip <?php echo e(empty($status) ? 'active' : ''); ?>">All</a>
        <a href="<?php echo e($toggleFilter('status', 'pending')); ?>"   class="filter-chip <?php echo e($status === 'pending'   ? 'active' : ''); ?>">Pending</a>
        <a href="<?php echo e($toggleFilter('status', 'completed')); ?>" class="filter-chip <?php echo e($status === 'completed' ? 'active' : ''); ?>">Completed</a>
        <a href="<?php echo e($toggleFilter('status', 'cancelled')); ?>" class="filter-chip <?php echo e($status === 'cancelled' ? 'active' : ''); ?>">Cancelled</a>

        <span class="filter-divider"></span>

        <a href="<?php echo e($buildUrl(['channel' => ''])); ?>" class="filter-chip <?php echo e(empty($channel) ? 'active' : ''); ?>">Any Channel</a>
        <a href="<?php echo e($toggleFilter('channel', 'online')); ?>"  class="filter-chip <?php echo e($channel === 'online'  ? 'active' : ''); ?>">Online</a>
        <a href="<?php echo e($toggleFilter('channel', 'walk_in')); ?>" class="filter-chip <?php echo e($channel === 'walk_in' ? 'active' : ''); ?>">Walk-in</a>
    <?php endif; ?>

    
    <?php if($tab === 'customers'): ?>
        <a href="<?php echo e($buildUrl(['regular' => ''])); ?>" class="filter-chip <?php echo e(empty($regular) ? 'active' : ''); ?>">All</a>
        <a href="<?php echo e($toggleFilter('regular', 'yes')); ?>" class="filter-chip <?php echo e($regular === 'yes' ? 'active' : ''); ?>">Regular</a>
        <a href="<?php echo e($toggleFilter('regular', 'no')); ?>"  class="filter-chip <?php echo e($regular === 'no'  ? 'active' : ''); ?>">Non-Regular</a>
    <?php endif; ?>

    
    <?php if($tab !== 'customers'): ?>
        <div class="date-range">
            <label>From</label>
            <input type="date" name="from" class="date-input" value="<?php echo e($from); ?>">
            <label>To</label>
            <input type="date" name="to" class="date-input" value="<?php echo e($to); ?>">
        </div>
    <?php endif; ?>

    <?php if($anyFilter): ?>
        <a href="<?php echo e($tabUrl($tab, $sub)); ?>" class="filter-clear" title="Clear all filters">×</a>
    <?php endif; ?>
</form>


<?php if($tab === 'customers'): ?>
    <div class="tab-action-row">
        <a href="<?php echo e(route('customers.create', ['from' => 'records'])); ?>" class="action-primary">
            + Add Customer
        </a>
    </div>
<?php endif; ?>


<div class="card">
    <?php if($tab === 'sales' && $sub === 'walk-in'): ?>

        <?php if($sales->isEmpty()): ?>
            <div class="empty-state">
                <h3><?php echo e($anyFilter ? 'No matching sales' : 'No walk-in sales yet'); ?></h3>
                <p><?php echo e($anyFilter ? 'Try changing your filters.' : 'Sales recorded at the POS will appear here.'); ?></p>
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
                                <td class="mono-cell"><?php echo e($sale->reference_code); ?></td>
                                <td class="muted-cell nowrap"><?php echo e($sale->sale_date->format('M d, Y h:i A')); ?></td>
                                <td style="font-weight: 500;"><?php echo e($sale->customer->full_name ?? 'Walk-in'); ?></td>
                                <td class="pay-tag <?php echo e($sale->payment_method === 'cash' ? 'pay-cash' : 'pay-gcash'); ?>">
                                    <?php echo e(str_replace('_', ' ', $sale->payment_method)); ?>

                                </td>
                                <td class="num strong">₱<?php echo e(number_format($sale->total_amount, 2)); ?></td>
                                <td class="num">
                                    <a href="<?php echo e(route('sales.print', $sale)); ?>?from=records" class="action-btn view">View Receipt</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php echo $__env->make('partials.pagination', ['paginator' => $sales], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

    <?php elseif($tab === 'sales' && $sub === 'online'): ?>

        <?php if($orders->isEmpty()): ?>
            <div class="empty-state">
                <h3><?php echo e($anyFilter ? 'No matching orders' : 'No orders yet'); ?></h3>
                <p><?php echo e($anyFilter ? 'Try changing your filters.' : 'Orders recorded will appear here.'); ?></p>
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
                                <td class="mono-cell"><?php echo e($order->reference_code); ?></td>
                                <td class="muted-cell nowrap"><?php echo e($order->order_date->format('M d, Y h:i A')); ?></td>
                                <td style="font-weight: 500;"><?php echo e($order->customer->full_name ?? $order->customer_name ?? 'Unregistered'); ?></td>
                                <td class="muted-cell"><?php echo e($order->channel === 'walk_in' ? 'Walk-in' : 'Online'); ?></td>
                                <td class="status-tag status-<?php echo e($order->order_status); ?>"><?php echo e($order->order_status); ?></td>
                                <td class="num strong">₱<?php echo e(number_format($order->total_amount, 2)); ?></td>
                                <td class="num">
                                    <div class="action-group">
                                        <a href="<?php echo e(route('orders.show', ['order' => $order, 'from' => 'records'])); ?>" class="action-btn view">View</a>
                                        <a href="<?php echo e(route('orders.print', $order)); ?>?from=records" class="action-btn receipt">Receipt</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php echo $__env->make('partials.pagination', ['paginator' => $orders], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

    <?php elseif($tab === 'purchases'): ?>

        <?php if($purchases->isEmpty()): ?>
            <div class="empty-state">
                <h3><?php echo e($anyFilter ? 'No matching purchases' : 'No stock-in records yet'); ?></h3>
                <p><?php echo e($anyFilter ? 'Try changing your filters.' : 'Restocking entries will appear here.'); ?></p>
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
                                <td class="muted-cell nowrap"><?php echo e($purchase->purchase_date->format('M d, Y h:i A')); ?></td>
                                <td style="font-weight: 500;"><?php echo e($purchase->supplier_name ?: '—'); ?></td>
                                <td class="muted-cell"><?php echo e($purchase->user->full_name ?? 'Unknown'); ?></td>
                                <td class="num strong">₱<?php echo e(number_format($purchase->total_amount, 2)); ?></td>
                                <td class="num">
                                    <a href="<?php echo e(route('purchases.show', $purchase)); ?>" class="action-btn view">View</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php echo $__env->make('partials.pagination', ['paginator' => $purchases], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

    <?php elseif($tab === 'production'): ?>

        <?php if($productions->isEmpty()): ?>
            <div class="empty-state">
                <h3><?php echo e($anyFilter ? 'No matching production records' : 'No production records yet'); ?></h3>
                <p><?php echo e($anyFilter ? 'Try changing your filters.' : 'Production entries will appear here.'); ?></p>
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
                                <td class="muted-cell nowrap"><?php echo e($production->production_date->format('M d, Y h:i A')); ?></td>
                                <td style="font-weight: 600;">
                                    <?php if($production->product): ?>
                                        <?php echo e($production->product->display_name); ?>

                                    <?php else: ?>
                                        <span style="font-style: italic; color: #64748B;">Custom Bouquet</span>
                                        <?php if($production->order): ?>
                                            <div class="mono-sub"><?php echo e($production->order->reference_code); ?></div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="muted-cell"><?php echo e($production->order_id && !$production->product_id ? 'Custom' : 'Standard'); ?></td>
                                <td class="muted-cell"><?php echo e($production->producedBy->full_name ?? 'Unknown'); ?></td>
                                <td class="num strong">
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
            <?php echo $__env->make('partials.pagination', ['paginator' => $productions], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

    <?php elseif($tab === 'customers'): ?>

        <?php if($customers->isEmpty()): ?>
            <div class="empty-state">
                <h3><?php echo e($anyFilter ? 'No matching customers' : 'No customers yet'); ?></h3>
                <p><?php echo e($anyFilter ? 'Try changing your filters.' : 'Click "+ Add Customer" to register the first one.'); ?></p>
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
                                <td style="font-weight: 600;"><?php echo e($customer->full_name); ?></td>
                                <td class="muted-cell"><?php echo e($customer->contact_number); ?></td>
                                <td class="muted-cell"><?php echo e($customer->address ?: '—'); ?></td>
                                <td>
                                    <?php if($customer->is_regular): ?>
                                        <span class="regular-tag yes">Yes</span>
                                    <?php else: ?>
                                        <span class="regular-tag no">No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="num">
                                    <a href="<?php echo e(route('customers.edit', ['customer' => $customer, 'from' => 'records'])); ?>" class="action-btn view">View</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php echo $__env->make('partials.pagination', ['paginator' => $customers], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

    <?php endif; ?>
</div>

<style>
    /* TABS */
    .record-tabs {
        display: flex;
        gap: 28px;
        margin-bottom: 20px;
        border-bottom: 1.5px solid #F0E6DD;
    }
    .record-tab {
        padding: 10px 2px;
        text-decoration: none;
        font-size: 15px;
        font-weight: 600;
        color: #94A3B8;
        border-bottom: 2px solid transparent;
        margin-bottom: -1.5px;
        transition: all 0.15s ease;
    }
    .record-tab:hover { color: #6B5B95; }
    .record-tab.active { color: #6B5B95; border-bottom-color: #6B5B95; }

    .record-sub-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
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
    .record-sub-tab:hover { background: #FEFCF9; color: #6B5B95; }
    .record-sub-tab.active { background: #EFEBF7; color: #6B5B95; }

    /* SEARCH — matches inventory */
    .pos-search { position: relative; margin-bottom: 14px; display: block; }
    .pos-search-icon {
        position: absolute; left: 8px; top: 50%; transform: translateY(-50%);
        width: 38px; height: 38px; border-radius: 50%;
        background: #6B5B95; color: #FFFFFF;
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
        outline: none; border-color: #6B5B95;
        box-shadow: 0 0 0 4px rgba(107, 91, 149, 0.1);
    }
    .pos-search-input::placeholder { color: #B0A99F; }

    /* FILTER ROW — matches inventory's filter chips */
    .filter-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .filter-label {
        font-size: 11px; color: #94A3B8; font-weight: 700;
        text-transform: uppercase; letter-spacing: 1.5px;
        margin-right: 6px;
    }

    .filter-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; border-radius: 20px;
        text-decoration: none; font-size: 12px; font-weight: 600;
        color: #64748B; background: #FFFFFF;
        border: 1.5px solid #F0E6DD;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .filter-chip:hover {
        border-color: #D5C9E8; background: #FEFCF9; color: #6B5B95;
    }
    .filter-chip.active {
        background: #6B5B95; color: #FFFFFF; border-color: #6B5B95;
    }

    .filter-divider {
        display: inline-block;
        width: 1px;
        height: 20px;
        background: #F0E6DD;
        margin: 0 6px;
    }

    .date-range {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-left: auto;
    }
    .date-range label {
        font-size: 10px; color: #94A3B8; font-weight: 700;
        text-transform: uppercase; letter-spacing: 1.2px;
    }
    .date-input {
        height: 32px;
        padding: 0 10px;
        border: 1.5px solid #F0E6DD;
        border-radius: 8px;
        font-size: 12px;
        background: #FFFFFF;
        color: #212121;
        font-family: inherit;
        transition: all 0.15s ease;
    }
    .date-input:focus {
        outline: none;
        border-color: #6B5B95;
        box-shadow: 0 0 0 3px rgba(107, 91, 149, 0.1);
    }

    .filter-clear {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        margin-left: 6px;
        background: #FDECEA;
        color: #DC3545;
        border-radius: 50%;
        text-decoration: none;
        font-size: 18px;
        font-weight: 700;
        line-height: 1;
        transition: all 0.15s ease;
    }
    .filter-clear:hover {
        background: #F8D7DA;
        color: #B02A37;
    }

    @media (max-width: 768px) {
        .date-range { margin-left: 0; width: 100%; }
        .date-input { flex: 1; }
    }

    /* TAB ACTION */
        /* TAB ACTION */
    .tab-action-row {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 16px;
    }

    .tab-action-row .action-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 42px;
        min-height: 42px;
        padding: 0 20px;
        background: #6B5B95;
        color: #FFFFFF;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        font-family: inherit;
        box-shadow: 0 2px 6px rgba(107, 91, 149, 0.3);
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .tab-action-row .action-primary:hover {
        background: #594B7D;
        box-shadow: 0 4px 10px rgba(107, 91, 149, 0.4);
    }

    /* CELLS */
    .mono-cell {
        font-family: 'SF Mono', Consolas, monospace;
        font-size: 12px;
        color: #64748B;
        font-weight: 600;
    }
    .mono-sub {
        font-size: 11px;
        color: #94A3B8;
        font-family: 'SF Mono', Consolas, monospace;
        margin-top: 2px;
    }
    .muted-cell { color: #64748B; }
    .nowrap { white-space: nowrap; }
    .strong { font-weight: 700; color: #2E5A3B; }

    .pay-tag {
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        cursor: default;
        user-select: none;
    }
    .pay-cash  { color: #6B5B95; }
    .pay-gcash { color: #2E5A3B; }

    .status-tag {
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        cursor: default;
        user-select: none;
    }
    .status-pending   { color: #B8860B; }
    .status-completed { color: #2E5A3B; }
    .status-cancelled { color: #DC3545; }

    .regular-tag {
        font-size: 13px;
        font-weight: 600;
    }
    .regular-tag.yes { color: #2E5A3B; }
    .regular-tag.no  { color: #94A3B8; font-style: italic; font-weight: 500; }

    /* ACTIONS */
    .action-group { display: inline-flex; gap: 6px; justify-content: flex-end; }
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
    .action-btn.view { background: #EFEBF7; color: #6B5B95; }
    .action-btn.view:hover { background: #D5C9E8; color: #594B7D; }
    .action-btn.receipt { background: #F1F5F9; color: #475569; }
    .action-btn.receipt:hover { background: #E2E8F0; color: #1E293B; }

    /* EMPTY */
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
    .empty-state p { font-size: 14px; }

    @media (max-width: 640px) {
        .record-tabs { gap: 20px; overflow-x: auto; }
        .record-tab { font-size: 13px; white-space: nowrap; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('records-search');
        if (!searchInput) return;

        let timeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            const value = this.value;
            if (value === <?php echo json_encode($search, 15, 512) ?>) return;
            timeout = setTimeout(() => this.form.submit(), 500);
        });
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                clearTimeout(timeout);
                this.form.submit();
            }
        });

        // Auto-submit date range form when dates change
        document.querySelectorAll('.date-input').forEach(input => {
            input.addEventListener('change', function () {
                this.form.submit();
            });
        });
    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/records/index.blade.php ENDPATH**/ ?>