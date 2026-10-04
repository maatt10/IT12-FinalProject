

<?php $__env->startSection('title', 'Online Orders Report'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Online Orders Report</h1>
        <p>Review online bouquet orders for a selected date range.</p>
    </div>
</div>


<div class="card" style="margin-bottom: 24px;">
    <h2 class="card-heading">Report Period</h2>

    <form action="<?php echo e(route('reports.online-orders')); ?>" method="GET">
        <div class="filter-row">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="from">From</label>
                <input type="date" id="from" name="from" class="form-control" value="<?php echo e($from); ?>" required>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="to">To</label>
                <input type="date" id="to" name="to" class="form-control" value="<?php echo e($to); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary filter-btn">Generate Report</button>
        </div>

        <div class="action-row">
            <button type="submit"
                    formaction="<?php echo e(route('reports.online-orders.print')); ?>"
                    formtarget="_blank"
                    class="btn btn-secondary action-btn">
                Print Report
            </button>

            <button type="submit"
                    formaction="<?php echo e(route('reports.online-orders.export')); ?>"
                    class="btn btn-secondary action-btn">
                Export CSV
            </button>
        </div>
    </form>
</div>


<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="color: #E85D75;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div style="text-align: right;">
            <p class="stat-label">Total Revenue</p>
            <p class="stat-value">₱<?php echo e(number_format($totalRevenue, 2)); ?></p>
        </div>
    </div>

    <div class="stat-card green-accent">
        <div class="stat-icon" style="color: #2E5A3B;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <div style="text-align: right;">
            <p class="stat-label">Total Orders</p>
            <p class="stat-value"><?php echo e($orderCount); ?></p>
        </div>
    </div>

    <div class="stat-card gold-accent">
        <div class="stat-icon" style="color: #B8860B;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div style="text-align: right;">
            <p class="stat-label">Completed</p>
            <p class="stat-value"><?php echo e($completedCount); ?></p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="color: #E85D75;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
        </div>
        <div style="text-align: right;">
            <p class="stat-label">Avg. Order Value</p>
            <p class="stat-value">₱<?php echo e(number_format($avgOrderValue, 2)); ?></p>
        </div>
    </div>
</div>


<div class="card" style="margin-bottom: 24px;">
    <h2 class="card-heading">Status Breakdown</h2>

    <div class="status-breakdown">
        <?php
            $statusColors = [
                'pending' => ['#64748B', '#F1F5F9'],
                'confirmed' => ['#E85D75', '#FCE4EC'],
                'preparing' => ['#B8860B', '#FFF8E1'],
                'ready' => ['#D14A62', '#FCE4EC'],
                'completed' => ['#2E5A3B', '#E8F5E9'],
                'cancelled' => ['#DC3545', '#FDECEA'],
            ];
        ?>

        <?php $__currentLoopData = $statusCounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                [$textColor, $bgColor] = $statusColors[$status];
            ?>
            <div class="status-item" style="border-left-color: <?php echo e($textColor); ?>;">
                <span class="status-name" style="color: <?php echo e($textColor); ?>;"><?php echo e(ucfirst($status)); ?></span>
                <span class="status-count" style="background: <?php echo e($bgColor); ?>; color: <?php echo e($textColor); ?>;">
                    <?php echo e($count); ?>

                </span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>


<div class="card">
    <h2 class="card-heading">Orders</h2>

    <?php if($orders->isEmpty()): ?>
        <div class="empty-state">
            <h3>No orders found</h3>
            <p>No online orders were recorded during the selected period.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Order No.</th>
                        <th>Date &amp; Time</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Fulfillment</th>
                        <th class="num">Total</th>
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
                            <td>
                                <span style="font-size: 12px; font-weight: 600; text-transform: capitalize; color: <?php echo e($statusColors[$order->order_status][0] ?? '#64748B'); ?>;">
                                    <?php echo e(str_replace('_', ' ', $order->order_status)); ?>

                                </span>
                            </td>
                            <td style="color: #64748B;">
                                <?php echo e(ucfirst($order->fulfillment_type)); ?>

                            </td>
                            <td class="num" style="font-weight: 700; color: #2E5A3B;">
                                ₱<?php echo e(number_format($order->total_amount, 2)); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
    .card-heading {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 18px;
        padding-bottom: 8px;
        border-bottom: 2px solid #FCE4EC;
        display: inline-block;
    }

    .filter-row {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 16px;
        align-items: end;
    }

    .filter-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 20px;
        font-size: 14px;
        white-space: nowrap;
    }

    .action-row {
        display: flex;
        gap: 8px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px dashed #F0E6DD;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        font-size: 13px;
        white-space: nowrap;
    }

    @media (max-width: 640px) {
        .filter-row { grid-template-columns: 1fr; }
        .filter-btn, .action-btn { width: 100%; justify-content: center; }
        .action-row { flex-direction: column; }
    }

    /* STATS */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #FFFFFF;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #F0E6DD;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        border-top: 4px solid #E85D75;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        cursor: default;
        user-select: none;
    }
    .stat-card.green-accent { border-top-color: #2E5A3B; }
    .stat-card.gold-accent { border-top-color: #D4AF37; }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #FCE4EC;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-card.green-accent .stat-icon { background: #E8F5E9; }
    .stat-card.gold-accent .stat-icon { background: #FFF8E1; }

    .stat-label {
        font-size: 11px;
        font-weight: 600;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #212121;
        margin-top: 4px;
    }

    /* STATUS BREAKDOWN */
    .status-breakdown {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }

    .status-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        background: #FEFCF9;
        border: 1px solid #F5EEE4;
        border-left: 4px solid #64748B;
        border-radius: 8px;
    }

    .status-name {
        font-size: 13px;
        font-weight: 600;
    }

    .status-count {
        font-size: 14px;
        font-weight: 700;
        padding: 2px 10px;
        border-radius: 12px;
    }

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
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/reports/online-orders.blade.php ENDPATH**/ ?>