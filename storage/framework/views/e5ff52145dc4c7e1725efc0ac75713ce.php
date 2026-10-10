

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <p>Welcome back, <strong style="color: #6B5B95;"><?php echo e(auth()->user()->full_name); ?></strong>.</p>
    </div>
</div>


<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon rose">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Today's Revenue</p>
            <p class="stat-value">₱<?php echo e(number_format($todayRevenue, 2)); ?></p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Transactions</p>
            <p class="stat-value"><?php echo e($todayTransactions); ?></p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon gold">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Items Sold</p>
            <p class="stat-value"><?php echo e((float) $todayItemsSold); ?></p>
        </div>
    </div>

    <?php if($isOwner): ?>
    <div class="stat-card">
        <div class="stat-icon berry">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Today's Orders</p>
            <p class="stat-value"><?php echo e($todayOnlineOrders); ?></p>
        </div>
    </div>
    <?php endif; ?>

</div>


<div class="card dash-card">
    <div class="dash-card-header">
        <div>
            <h2>Low Stock Alerts</h2>
            <p>Items at or below their reorder threshold.</p>
        </div>
        <a href="<?php echo e(route('products.index')); ?>" class="dash-text-link">View Inventory →</a>
    </div>

    <?php if($lowStockItems->isEmpty()): ?>
        <div class="dash-empty">
            All items are sufficiently stocked.
        </div>
    <?php else: ?>
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Type</th>
                    <th class="num">Retail</th>
                    <th class="num">Production</th>
                    <th>Unit</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $lowStockItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="font-weight: 600;">
                            <?php echo e($item->name); ?><?php if($item->variation): ?> — <?php echo e($item->variation); ?><?php endif; ?>
                        </td>
                        <td class="muted">
                            <?php echo e($item->item_type === 'material' ? 'Material' : 'Product'); ?>

                        </td>
                        <td class="num <?php echo e($item->retail_low ? 'danger' : 'muted'); ?>">
                            <?php if($item->has_retail): ?>
                                <?php echo e($item->retail_stock); ?>

                                <?php if($item->retail_low): ?> <span class="low-tag">LOW</span> <?php endif; ?>
                            <?php else: ?>
                                <span class="dash-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="num <?php echo e($item->production_low ? 'danger' : 'muted'); ?>">
                            <?php if($item->has_production): ?>
                                <?php echo e($item->production_stock); ?>

                                <?php if($item->production_low): ?> <span class="low-tag">LOW</span> <?php endif; ?>
                            <?php else: ?>
                                <span class="dash-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="muted"><?php echo e($item->stock_unit); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>


<?php if($isOwner): ?>

    
    <div class="card dash-card">
        <div class="dash-card-header">
            <div>
                <h2>Sales Overview</h2>
                <p>Walk-in sales and orders combined.</p>
            </div>

            <div class="range-toggle">
                <a href="<?php echo e(route('dashboard', ['range' => 7])); ?>"
                   class="range-btn <?php echo e($range === 7 ? 'active' : ''); ?>">7 Days</a>
                <a href="<?php echo e(route('dashboard', ['range' => 30])); ?>"
                   class="range-btn <?php echo e($range === 30 ? 'active' : ''); ?>">30 Days</a>
            </div>
        </div>

        
        <div class="metric-row">
            <div class="metric-tile">
                <span class="metric-label">Total Revenue</span>
                <span class="metric-value">₱<?php echo e(number_format($rangeRevenue, 2)); ?></span>
            </div>
            <div class="metric-tile">
                <span class="metric-label">Best Day</span>
                <span class="metric-value">
                    <?php echo e($bestDay['label']); ?> · ₱<?php echo e(number_format($bestDay['revenue'], 2)); ?>

                </span>
            </div>
            <div class="metric-tile">
                <span class="metric-label">Daily Average</span>
                <span class="metric-value">₱<?php echo e(number_format($rangeAverage, 2)); ?></span>
            </div>
        </div>

        
        <div class="chart-wrap range-<?php echo e($range); ?>">
            <div class="chart-gridlines">
                <div class="gridline-label"><span>₱<?php echo e(number_format($maxRevenue, 0)); ?></span></div>
                <div class="gridline-label"><span>₱<?php echo e(number_format($maxRevenue * 0.5, 0)); ?></span></div>
                <div class="gridline-label"><span>₱0</span></div>
            </div>

            <div class="chart-bars">
                <?php $__currentLoopData = $chartData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $height = $maxRevenue > 0 ? ($day['revenue'] / $maxRevenue) * 100 : 0;
                        $isToday = $index === $chartData->count() - 1;
                        $showLabel = $range <= 7 || $index % 5 === 0 || $isToday;
                    ?>
                    <div class="chart-col <?php echo e($isToday ? 'today' : ''); ?>">
                        <div class="chart-tooltip">
                            <?php echo e($day['full_label']); ?><br>
                            <strong>₱<?php echo e(number_format($day['revenue'], 2)); ?></strong>
                            <?php if($day['walk_in'] > 0 || $day['online'] > 0): ?>
                                <div class="tooltip-split">
                                    Walk-in: ₱<?php echo e(number_format($day['walk_in'], 2)); ?><br>
                                    Online: ₱<?php echo e(number_format($day['online'], 2)); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="chart-bar-track">
                            <div class="chart-bar <?php echo e($day['revenue'] > 0 ? '' : 'empty'); ?>"
                                 style="height: <?php echo e(max($height, 2)); ?>%;">
                            </div>
                        </div>
                        <div class="chart-col-label">
                            <?php echo e($showLabel ? $day['label'] : ''); ?>

                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    
    <div class="card dash-card">
        <div class="dash-card-header">
            <div>
                <h2>Order Status</h2>
                <p>Current distribution of bouquet orders.</p>
            </div>
            <a href="<?php echo e(route('records.index', ['tab' => 'sales', 'sub' => 'online'])); ?>" class="dash-text-link">View Orders →</a>
        </div>

        <div class="status-grid">
            <div class="status-tile status-pending">
                <div class="status-label">Pending</div>
                <div class="status-value"><?php echo e($orderStatusCounts['pending']); ?></div>
            </div>
            <div class="status-tile status-completed">
                <div class="status-label">Completed</div>
                <div class="status-value"><?php echo e($orderStatusCounts['completed']); ?></div>
            </div>
            <div class="status-tile status-cancelled">
                <div class="status-label">Cancelled</div>
                <div class="status-value"><?php echo e($orderStatusCounts['cancelled']); ?></div>
            </div>
        </div>
    </div>


<?php else: ?>

    
    <div class="card dash-card">
        <div class="dash-card-header">
            <div>
                <h2>Recent Sales</h2>
                <p>Latest recorded sales transactions.</p>
            </div>
            <a href="<?php echo e(route('pos.index')); ?>" class="dash-text-link">Open POS →</a>
        </div>

        <?php if($recentSales->isEmpty()): ?>
            <div class="dash-empty">
                No sales have been recorded yet.
            </div>
        <?php else: ?>
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Receipt No.</th>
                        <th>Date &amp; Time</th>
                        <th>Customer</th>
                        <th>Payment</th>
                        <th class="num">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="mono"><?php echo e($sale->reference_code); ?></td>
                            <td class="muted"><?php echo e($sale->sale_date->format('M d, Y h:i A')); ?></td>
                            <td><?php echo e($sale->customer->full_name ?? 'Walk-in'); ?></td>
                            <td class="muted"><?php echo e(str_replace('_', ' ', $sale->payment_method)); ?></td>
                            <td class="num strong">₱<?php echo e(number_format($sale->total_amount, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

<?php endif; ?>


<div class="card dash-card">
    <div class="dash-card-header">
        <div>
            <h2>Quick Actions</h2>
            <p>Frequently used tasks.</p>
        </div>
    </div>

    <div class="quick-actions">

        <a href="<?php echo e(route('pos.index')); ?>" class="quick-action">
            <div class="qa-icon" style="background: #EFEBF7; color: #6B5B95;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <p class="qa-title">POS</p>
            <p class="qa-desc">Process a sale</p>
        </a>

        <?php if($isOwner): ?>
        <a href="<?php echo e(route('orders.create')); ?>" class="quick-action">
            <div class="qa-icon" style="background: #FCE8EF; color: #D14A7A;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <p class="qa-title">Record Order</p>
            <p class="qa-desc">Log a new bouquet order</p>
        </a>
        <?php endif; ?>

        <a href="<?php echo e(route('products.index')); ?>" class="quick-action">
            <div class="qa-icon" style="background: #E8F5E9; color: #2E5A3B;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <p class="qa-title">Inventory</p>
            <p class="qa-desc">View stock and items</p>
        </a>

        <a href="<?php echo e(route('reports.index')); ?>" class="quick-action">
            <div class="qa-icon" style="background: #FFF8E1; color: #B8860B;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <p class="qa-title">Reports</p>
            <p class="qa-desc">View sales and stock</p>
        </a>

        <?php if($isOwner): ?>
        <a href="<?php echo e(route('records.index')); ?>" class="quick-action">
            <div class="qa-icon" style="background: #F1F5F9; color: #475569;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="qa-title">Records</p>
            <p class="qa-desc">Browse all transactions</p>
        </a>
        <?php endif; ?>

    </div>
</div>

<style>
    /* ===============================
       STAT CARDS
       =============================== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #FFFFFF;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        display: flex;
        align-items: center;
        gap: 16px;
        cursor: default;
        user-select: none;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon.rose { background: #EFEBF7; color: #6B5B95; }
    .stat-icon.green { background: #E8F5E9; color: #2E5A3B; }
    .stat-icon.gold { background: #FFF8E1; color: #B8860B; }
    .stat-icon.berry { background: #EEF2FF; color: #4F46E5; }

    .stat-content {
        flex: 1;
        min-width: 0;
        text-align: right;
    }
    .stat-label {
        font-size: 11px;
        font-weight: 700;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 4px;
    }
    .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #212121;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ===============================
       DASH CARDS
       =============================== */
    .dash-card { margin-bottom: 24px; }

    .dash-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }
    .dash-card-header h2 {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin: 0;
    }
    .dash-card-header p {
        font-size: 13px;
        color: #64748B;
        margin: 4px 0 0;
    }

    .dash-text-link {
        font-size: 13px;
        font-weight: 600;
        color: #6B5B95;
        text-decoration: none;
        transition: all 0.15s ease;
        white-space: nowrap;
        align-self: center;
    }
    .dash-text-link:hover { color: #594B7D; }

    .dash-empty {
        text-align: center;
        padding: 30px 20px;
        color: #94A3B8;
        font-size: 13px;
        background: #FEFCF9;
        border-radius: 10px;
    }

    /* ===============================
       RANGE TOGGLE
       =============================== */
    .range-toggle {
        display: inline-flex;
        background: #FAF7F3;
        border-radius: 8px;
        padding: 4px;
        gap: 2px;
        align-self: center;
    }
    .range-btn {
        padding: 7px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #64748B;
        text-decoration: none;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .range-btn:hover { color: #6B5B95; }
    .range-btn.active {
        background: #FFFFFF;
        color: #6B5B95;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    /* ===============================
       3-TILE METRIC ROW
       =============================== */
    .metric-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .metric-tile {
        background: #FAF7F3;
        border-radius: 10px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .metric-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        white-space: nowrap;
    }

    .metric-value {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        white-space: nowrap;
        text-align: right;
    }

    /* ===============================
       CHART
       =============================== */
    .chart-wrap {
        display: flex;
        gap: 12px;
        height: 220px;
        padding-top: 8px;
    }

    .chart-gridlines {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding-bottom: 26px;
        width: 60px;
        flex-shrink: 0;
    }
    .gridline-label {
        display: flex;
        justify-content: flex-end;
        position: relative;
    }
    .gridline-label span {
        font-size: 10px;
        font-weight: 600;
        color: #94A3B8;
        background: #FFFFFF;
        padding-right: 6px;
    }

    .chart-bars {
        flex: 1;
        display: flex;
        gap: 12px;
        padding-bottom: 26px;
        position: relative;
        overflow: hidden;
    }

    .chart-bars::before,
    .chart-bars::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        height: 1px;
        background: #F0E6DD;
        pointer-events: none;
        z-index: 0;
    }
    .chart-bars::before { top: 0; }
    .chart-bars::after { bottom: 26px; }

    .range-30 .chart-bars { gap: 3px; }

    .chart-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        position: relative;
        z-index: 1;
        min-width: 0;
    }

    .chart-bar-track {
        width: 100%;
        height: 194px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }

    .chart-bar {
        width: 100%;
        max-width: 44px;
        min-height: 3px;
        background: linear-gradient(180deg, #6B5B95 0%, #D5C9E8 100%);
        border-radius: 6px 6px 0 0;
        transition: all 0.2s ease;
    }
    .chart-bar.empty { background: #F0E6DD; }
    .chart-col.today .chart-bar {
        background: #6B5B95;
        box-shadow: 0 4px 12px rgba(107, 91, 149, 0.4);
    }
    .chart-col:hover .chart-bar { filter: brightness(1.05); }

    .chart-tooltip {
        position: absolute;
        top: -8px;
        left: 50%;
        transform: translateX(-50%) translateY(-100%);
        background: #212121;
        color: #FFFFFF;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
        text-align: center;
        line-height: 1.5;
        opacity: 0;
        pointer-events: none;
        transition: all 0.15s ease;
        z-index: 5;
    }
    .chart-tooltip strong { color: #D5C9E8; font-weight: 700; font-size: 13px; }
    .tooltip-split {
        margin-top: 6px;
        padding-top: 6px;
        border-top: 1px dashed rgba(255, 255, 255, 0.2);
        color: #CBD5E1;
        font-size: 10px;
    }
    .chart-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 4px solid transparent;
        border-top-color: #212121;
    }
    .chart-col:hover .chart-tooltip {
        opacity: 1;
        top: 0;
    }

    .chart-col-label {
        position: absolute;
        bottom: 0;
        font-size: 10px;
        font-weight: 700;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .chart-col.today .chart-col-label { color: #6B5B95; }

    /* ===============================
       TABLES
       =============================== */
    .dash-table { width: 100%; border-collapse: collapse; }
    .dash-table thead th {
        text-align: left;
        padding: 10px 12px;
        font-size: 10px;
        font-weight: 700;
        color: #6B5B95;
        text-transform: uppercase;
        letter-spacing: 1px;
        background: #EFEBF7;
        border-bottom: 2px solid #D5C9E8;
    }
    .dash-table thead th:first-child { border-top-left-radius: 8px; }
    .dash-table thead th:last-child { border-top-right-radius: 8px; }
    .dash-table thead th.num { text-align: right; }

    .dash-table tbody td {
        padding: 10px 12px;
        font-size: 13px;
        color: #212121;
        border-bottom: 1px solid #F5EEE4;
    }
    .dash-table tbody tr:last-child td { border-bottom: none; }
    .dash-table tbody tr:hover { background: #FFF9FB; }
    .dash-table tbody td.num { text-align: right; white-space: nowrap; }
    .dash-table tbody td.muted { color: #64748B; }
    .dash-table tbody td.strong { font-weight: 700; color: #2E5A3B; }
    .dash-table tbody td.danger { color: #DC3545; font-weight: 700; }
    .dash-table tbody td.mono {
        font-family: 'SF Mono', Consolas, monospace;
        color: #6B5B95;
        font-weight: 600;
        font-size: 12px;
    }

    .dash-muted { color: #CBD5E1; }

    .low-tag {
        display: inline-block;
        font-size: 9px;
        font-weight: 700;
        color: #DC3545;
        background: #FDECEA;
        padding: 1px 5px;
        border-radius: 3px;
        margin-left: 4px;
        letter-spacing: 0.5px;
    }

    /* ===============================
       ORDER STATUS
       =============================== */
    .status-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
    }

    .status-tile {
        border-radius: 10px;
        padding: 18px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }
    .status-tile.status-pending {
        background: #FFF8E1;
        border: 1px solid #FFECB3;
    }
    .status-tile.status-pending .status-label,
    .status-tile.status-pending .status-value { color: #B8860B; }

    .status-tile.status-completed {
        background: #E8F5E9;
        border: 1px solid #C8E6C9;
    }
    .status-tile.status-completed .status-label,
    .status-tile.status-completed .status-value { color: #2E5A3B; }

    .status-tile.status-cancelled {
        background: #FDECEA;
        border: 1px solid #F8D7DA;
    }
    .status-tile.status-cancelled .status-label,
    .status-tile.status-cancelled .status-value { color: #DC3545; }

    .status-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .status-value {
        font-size: 24px;
        font-weight: 700;
    }

    /* ===============================
       QUICK ACTIONS
       =============================== */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }

    .quick-action {
        display: block;
        padding: 16px;
        border: 1.5px solid #F0E6DD;
        border-radius: 10px;
        text-decoration: none;
        background: #FFFFFF;
        transition: all 0.2s ease;
    }
    .quick-action:hover {
        background: #FEFCF9;
        border-color: #6B5B95;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(107, 91, 149, 0.12);
    }

    .qa-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }

    .qa-title {
        font-size: 14px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 2px;
    }
    .qa-desc {
        font-size: 12px;
        color: #64748B;
    }

    /* ===============================
       RESPONSIVE
       =============================== */
    @media (max-width: 640px) {
        .chart-bars { gap: 6px; }
        .range-30 .chart-bars { gap: 2px; }
        .chart-gridlines { width: 44px; }
        .gridline-label span { font-size: 9px; }
        .chart-tooltip { font-size: 10px; padding: 6px 8px; }
        .metric-tile { padding: 12px 14px; }
        .metric-value { font-size: 14px; }
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/dashboard.blade.php ENDPATH**/ ?>