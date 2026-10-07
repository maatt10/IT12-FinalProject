

<?php
    $fmtQty = function ($val) {
        if ($val === null || $val === '') return '—';
        $num = (float) $val;
        $formatted = number_format($num, 2, '.', ',');
        $trimmed = rtrim(rtrim($formatted, '0'), '.');
        return $trimmed === '' ? '0' : $trimmed;
    };

    $fmtMoney = fn($val) => number_format((float) $val, 2);
?>

<?php $__env->startSection('title', 'Reports'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Reports</h1>
        <p>View summarized reports for the shop's sales and stock.</p>
    </div>
</div>

<div class="reports-page">

    
    <form action="<?php echo e(route('reports.index')); ?>" method="GET" class="filter-bar">
        <input type="hidden" name="sub" value="<?php echo e($sub); ?>">

        <div class="filter-group">
            <label for="report">Report</label>
            <select id="report" name="report" class="filter-input" onchange="this.form.submit()">
                <option value="sales" <?php echo e($report === 'sales' ? 'selected' : ''); ?>>Sales</option>
                <option value="stocks" <?php echo e($report === 'stocks' ? 'selected' : ''); ?>>Stocks</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="period">Period</label>
            <select id="period" name="period" class="filter-input" onchange="this.form.submit()">
                <option value="daily" <?php echo e($period === 'daily' ? 'selected' : ''); ?>>Daily</option>
                <option value="weekly" <?php echo e($period === 'weekly' ? 'selected' : ''); ?>>Weekly</option>
                <option value="monthly" <?php echo e($period === 'monthly' ? 'selected' : ''); ?>>Monthly</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" class="filter-input"
                   value="<?php echo e($date); ?>" onchange="this.form.submit()">
        </div>

        <div class="filter-actions">
            <a href="<?php echo e(route('reports.export', request()->query())); ?>" class="btn-outline">Export Excel</a>
            <a href="<?php echo e(route('reports.print', request()->query())); ?>" target="_blank" class="btn-primary">Export PDF</a>
        </div>
    </form>

    <p class="period-label"><?php echo e($periodLabel); ?></p>

    
    <div class="sub-tabs">
        <?php if($report === 'sales'): ?>
            <a href="<?php echo e(route('reports.index', array_merge(request()->query(), ['report' => 'sales', 'sub' => 'walk-in']))); ?>"
               class="sub-tab <?php echo e($sub === 'walk-in' ? 'active' : ''); ?>">Walk-in</a>
            <a href="<?php echo e(route('reports.index', array_merge(request()->query(), ['report' => 'sales', 'sub' => 'online']))); ?>"
               class="sub-tab <?php echo e($sub === 'online' ? 'active' : ''); ?>">Online Orders</a>
            <a href="<?php echo e(route('reports.index', array_merge(request()->query(), ['report' => 'sales', 'sub' => 'overall']))); ?>"
               class="sub-tab <?php echo e($sub === 'overall' ? 'active' : ''); ?>">Overall Sales</a>
        <?php else: ?>
            <a href="<?php echo e(route('reports.index', array_merge(request()->query(), ['report' => 'stocks', 'sub' => 'materials']))); ?>"
               class="sub-tab <?php echo e($sub === 'materials' ? 'active' : ''); ?>">Materials</a>
            <a href="<?php echo e(route('reports.index', array_merge(request()->query(), ['report' => 'stocks', 'sub' => 'products']))); ?>"
               class="sub-tab <?php echo e($sub === 'products' ? 'active' : ''); ?>">Products</a>
            <a href="<?php echo e(route('reports.index', array_merge(request()->query(), ['report' => 'stocks', 'sub' => 'overall']))); ?>"
               class="sub-tab <?php echo e($sub === 'overall' ? 'active' : ''); ?>">Overall Stock</a>
        <?php endif; ?>
    </div>

    
    <div class="report-block">

        <div class="report-header">
            <h2>
                <?php if($report === 'sales'): ?>
                    <?php switch($sub):
                        case ('walk-in'): ?> Walk-in Sales Report <?php break; ?>
                        <?php case ('online'): ?> Online Orders Report <?php break; ?>
                        <?php case ('overall'): ?> Overall Sales Report <?php break; ?>
                    <?php endswitch; ?>
                <?php else: ?>
                    <?php switch($sub):
                        case ('materials'): ?> Materials Stock Report <?php break; ?>
                        <?php case ('products'): ?> Products Stock Report <?php break; ?>
                        <?php case ('overall'): ?> Overall Stock Report <?php break; ?>
                    <?php endswitch; ?>
                <?php endif; ?>
            </h2>
            <p><?php echo e(ucfirst($period)); ?> · <?php echo e($periodLabel); ?></p>
        </div>

        <div class="report-body">

            
            <?php if($report === 'sales' && $sub === 'walk-in'): ?>
                <h3 class="section-label">Summary</h3>
                <div class="summary-grid">
                    <div class="tile">
                        <span class="tile-label">Orders</span>
                        <span class="tile-value"><?php echo e($salesData['orders']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Items Sold</span>
                        <span class="tile-value"><?php echo e($fmtQty($salesData['itemsSold'])); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Gross</span>
                        <span class="tile-value">₱<?php echo e($fmtMoney($salesData['gross'])); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Discounts</span>
                        <span class="tile-value">₱<?php echo e($fmtMoney($salesData['discounts'])); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Net Sales</span>
                        <span class="tile-value">₱<?php echo e($fmtMoney($salesData['net'])); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Avg Order</span>
                        <span class="tile-value">₱<?php echo e($fmtMoney($salesData['avgOrder'])); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Cash</span>
                        <span class="tile-value">₱<?php echo e($fmtMoney($salesData['paymentCash'])); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">GCash</span>
                        <span class="tile-value">₱<?php echo e($fmtMoney($salesData['paymentGcash'])); ?></span>
                    </div>
                </div>

                <h3 class="section-label">Sales Records</h3>
                <?php if($salesRecords->isEmpty()): ?>
                    <p class="empty-text">No walk-in sales recorded during this period.</p>
                <?php else: ?>
                    <div class="table-scroll">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Receipt No.</th>
                                    <th>Date &amp; Time</th>
                                    <th>Customer</th>
                                    <th>Payment</th>
                                    <th class="num">Subtotal</th>
                                    <th class="num">Discount</th>
                                    <th class="num">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $salesRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="mono"><?php echo e($sale->reference_code); ?></td>
                                        <td class="muted"><?php echo e($sale->sale_date->format('M d, Y h:i A')); ?></td>
                                        <td><?php echo e($sale->customer->full_name ?? 'Walk-in'); ?></td>
                                        <td class="muted"><?php echo e(str_replace('_', ' ', $sale->payment_method)); ?></td>
                                        <td class="num muted">₱<?php echo e($fmtMoney($sale->subtotal)); ?></td>
                                        <td class="num muted">₱<?php echo e($fmtMoney($sale->discount_amount)); ?></td>
                                        <td class="num strong">₱<?php echo e($fmtMoney($sale->total_amount)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            
            <?php if($report === 'sales' && $sub === 'online'): ?>
                <h3 class="section-label">Summary</h3>
                <div class="summary-grid">
                    <div class="tile">
                        <span class="tile-label">Total Orders</span>
                        <span class="tile-value"><?php echo e($ordersData['total']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Completed</span>
                        <span class="tile-value"><?php echo e($ordersData['completed']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Cancelled</span>
                        <span class="tile-value"><?php echo e($ordersData['cancelled']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Net Revenue</span>
                        <span class="tile-value">₱<?php echo e($fmtMoney($ordersData['net'])); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Avg Order</span>
                        <span class="tile-value">₱<?php echo e($fmtMoney($ordersData['avgOrder'])); ?></span>
                    </div>
                </div>

                <h3 class="section-label">Orders</h3>
                <?php if($orderRecords->isEmpty()): ?>
                    <p class="empty-text">No online orders recorded during this period.</p>
                <?php else: ?>
                    <div class="table-scroll">
                        <table class="report-table">
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
                                <?php $__currentLoopData = $orderRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="mono"><?php echo e($order->reference_code); ?></td>
                                        <td class="muted"><?php echo e($order->order_date->format('M d, Y h:i A')); ?></td>
                                        <td><?php echo e($order->customer->full_name ?? $order->customer_name ?? 'Unregistered'); ?></td>
                                        <td class="muted"><?php echo e(ucfirst($order->order_status)); ?></td>
                                        <td class="muted"><?php echo e(ucfirst($order->fulfillment_type)); ?></td>
                                        <td class="num strong">₱<?php echo e($fmtMoney($order->total_amount)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            
            <?php if($report === 'sales' && $sub === 'overall'): ?>
                <h3 class="section-label">Summary</h3>
                <div class="summary-grid">
                    <div class="tile">
                        <span class="tile-label">Total Revenue</span>
                        <span class="tile-value">₱<?php echo e($fmtMoney($overallData['totalRevenue'])); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Transactions</span>
                        <span class="tile-value"><?php echo e($overallData['totalCount']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Avg Transaction</span>
                        <span class="tile-value">₱<?php echo e($fmtMoney($overallData['avgTransaction'])); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Walk-in</span>
                        <span class="tile-value">₱<?php echo e($fmtMoney($overallData['walkInRevenue'])); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Online</span>
                        <span class="tile-value">₱<?php echo e($fmtMoney($overallData['onlineRevenue'])); ?></span>
                    </div>
                </div>

                <h3 class="section-label">All Transactions</h3>
                <?php if($overallRecords->isEmpty()): ?>
                    <p class="empty-text">No transactions recorded during this period.</p>
                <?php else: ?>
                    <div class="table-scroll">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Channel</th>
                                    <th>Reference</th>
                                    <th>Date &amp; Time</th>
                                    <th>Customer</th>
                                    <th class="num">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $overallRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="muted"><?php echo e($record['source']); ?></td>
                                        <td class="mono"><?php echo e($record['reference']); ?></td>
                                        <td class="muted"><?php echo e($record['date']->format('M d, Y h:i A')); ?></td>
                                        <td><?php echo e($record['customer']); ?></td>
                                        <td class="num strong">₱<?php echo e($fmtMoney($record['total'])); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            
            <?php if($report === 'stocks'): ?>
                <h3 class="section-label">Summary</h3>
                <div class="summary-grid">
                    <div class="tile">
                        <span class="tile-label">Total Items</span>
                        <span class="tile-value"><?php echo e(count($ledgerRows)); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Beginning</span>
                        <span class="tile-value"><?php echo e($fmtQty($ledgerTotals['beginning'])); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Stock-in</span>
                        <span class="tile-value"><?php echo e($fmtQty($ledgerTotals['stock_in'])); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Stock-out</span>
                        <span class="tile-value"><?php echo e($fmtQty($ledgerTotals['stock_out'])); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Remaining</span>
                        <span class="tile-value"><?php echo e($fmtQty($ledgerTotals['remaining'])); ?></span>
                    </div>
                </div>

                <h3 class="section-label">Stock Ledger</h3>
                <?php if(empty($ledgerRows)): ?>
                    <p class="empty-text">No items to display.</p>
                <?php else: ?>
                    <div class="table-scroll">
                        <table class="report-table ledger-table">
                            <thead>
                                <tr>
                                    <th class="ledger-item-col">Items</th>
                                    <th class="num ledger-num-col">Beginning</th>
                                    <th class="num ledger-num-col">Stock-in</th>
                                    <th class="num ledger-num-col">Stock-out</th>
                                    <th class="num ledger-num-col">Remaining</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $ledgerRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="ledger-item-cell">
                                            <div class="ledger-item-name"><?php echo e($row['name']); ?></div>
                                            <div class="ledger-item-unit"><?php echo e($row['unit']); ?></div>
                                        </td>
                                        <td class="num muted"><?php echo e($fmtQty($row['beginning'])); ?></td>
                                        <td class="num ledger-in">
                                            <?php echo e($row['stock_in'] > 0 ? '+' . $fmtQty($row['stock_in']) : '—'); ?>

                                        </td>
                                        <td class="num ledger-out">
                                            <?php echo e($row['stock_out'] > 0 ? '−' . $fmtQty($row['stock_out']) : '—'); ?>

                                        </td>
                                        <td class="num strong"><?php echo e($fmtQty($row['remaining'])); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="ledger-item-cell ledger-total-label">Total</td>
                                    <td class="num ledger-total"><?php echo e($fmtQty($ledgerTotals['beginning'])); ?></td>
                                    <td class="num ledger-total ledger-in">
                                        <?php echo e($ledgerTotals['stock_in'] > 0 ? '+' . $fmtQty($ledgerTotals['stock_in']) : '—'); ?>

                                    </td>
                                    <td class="num ledger-total ledger-out">
                                        <?php echo e($ledgerTotals['stock_out'] > 0 ? '−' . $fmtQty($ledgerTotals['stock_out']) : '—'); ?>

                                    </td>
                                    <td class="num ledger-total ledger-remaining"><?php echo e($fmtQty($ledgerTotals['remaining'])); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>

</div>

<style>
    .reports-page { max-width: 1100px; }

    /* FILTER BAR */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-end;
        margin-bottom: 12px;
        background: #FFFFFF;
        padding: 18px 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 160px;
    }

    .filter-group label {
        font-size: 10px;
        font-weight: 700;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .filter-input {
        padding: 10px 14px;
        border: 1.5px solid #F0E6DD;
        border-radius: 8px;
        font-size: 14px;
        background: #FFFFFF;
        color: #212121;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.15s ease;
        min-width: 160px;
    }
    .filter-input:focus {
        outline: none;
        border-color: #E85D75;
        box-shadow: 0 0 0 3px rgba(232, 93, 117, 0.1);
    }

    .filter-actions { display: flex; gap: 8px; margin-left: auto; }

    .btn-primary {
        padding: 10px 20px;
        background: #E85D75;
        color: #FFFFFF;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        border: 1.5px solid #E85D75;
        text-decoration: none;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .btn-primary:hover { background: #D14A62; border-color: #D14A62; }

    .btn-outline {
        padding: 10px 20px;
        border: 1.5px solid #E85D75;
        background: #FFFFFF;
        color: #E85D75;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .btn-outline:hover { background: #FCE4EC; }

    .period-label {
        font-size: 13px;
        color: #64748B;
        margin-bottom: 12px;
        padding-left: 4px;
    }

    /* SUB TABS */
    .sub-tabs {
        display: flex;
        gap: 28px;
        margin-bottom: 16px;
        border-bottom: 1.5px solid #F0E6DD;
        padding-left: 4px;
    }
    .sub-tab {
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
    .sub-tab:hover { color: #E85D75; }
    .sub-tab.active {
        color: #E85D75;
        border-bottom-color: #E85D75;
    }

    /* REPORT BLOCK */
    .report-block {
        background: #FFFFFF;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .report-header {
        background: #FCE4EC;
        padding: 18px 24px;
    }
    .report-header h2 {
        font-size: 17px;
        font-weight: 700;
        color: #E85D75;
        margin: 0;
    }
    .report-header p {
        font-size: 12px;
        color: #D14A62;
        margin: 4px 0 0;
        font-weight: 500;
    }

    .report-body { padding: 24px; }

    .section-label {
        font-size: 11px;
        font-weight: 700;
        color: #E85D75;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin: 0 0 12px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-label::before {
        content: '';
        display: inline-block;
        width: 3px;
        height: 12px;
        background: #E85D75;
        border-radius: 2px;
    }
    .summary-grid + .section-label,
    .table-scroll + .section-label,
    .report-table + .section-label { margin-top: 28px; }

    /* SUMMARY TILES — flexible labels */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
    }
    .tile {
        background: #FAF7F3;
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .tile-label {
        font-size: 10px;
        font-weight: 700;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1.3;
        min-width: 0;
        word-break: break-word;
    }
    .tile-value {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        white-space: nowrap;
        text-align: right;
        font-variant-numeric: tabular-nums;
        flex-shrink: 0;
    }

    /* TABLE WRAPPER */
    .table-scroll {
        width: 100%;
        overflow-x: auto;
    }

    /* REPORT TABLE */
    .report-table {
        width: 100%;
        border-collapse: collapse;
    }
    .report-table thead th {
        text-align: left;
        padding: 12px 14px;
        font-size: 10px;
        font-weight: 700;
        color: #E85D75;
        text-transform: uppercase;
        letter-spacing: 1px;
        background: #FCE4EC;
        border-bottom: 2px solid #F8BBD0;
        white-space: nowrap;
    }
    .report-table thead th:first-child { border-top-left-radius: 8px; }
    .report-table thead th:last-child { border-top-right-radius: 8px; }
    .report-table thead th.num { text-align: right; }

    .report-table tbody td {
        padding: 12px 14px;
        font-size: 13px;
        color: #212121;
        border-bottom: 1px solid #F5EEE4;
        vertical-align: top;
    }
    .report-table tbody tr:last-child td { border-bottom: none; }
    .report-table tbody tr:hover { background: #FFF9FB; }
    .report-table tbody td.num {
        text-align: right;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }
    .report-table tbody td.muted { color: #64748B; }
    .report-table tbody td.strong { font-weight: 700; color: #2E5A3B; }
    .report-table tbody td.mono {
        font-family: 'SF Mono', Consolas, monospace;
        color: #E85D75;
        font-weight: 600;
        font-size: 12px;
        white-space: nowrap;
    }

    /* LEDGER TABLE */
    .ledger-table { table-layout: fixed; }
    .ledger-item-col { width: 34%; }
    .ledger-num-col { width: 16.5%; }

    .ledger-item-cell { word-break: break-word; }
    .ledger-item-name { font-weight: 600; color: #212121; line-height: 1.3; }
    .ledger-item-unit { font-size: 11px; color: #94A3B8; margin-top: 2px; }
    .ledger-in { color: #2E5A3B; font-weight: 600; }
    .ledger-out { color: #DC3545; font-weight: 600; }

    /* FOOTER */
    .report-table tfoot td {
        padding: 12px 14px;
        font-size: 13px;
        background: #FAF7F3;
        border-top: 2px solid #F8BBD0;
        vertical-align: middle;
    }
    .report-table tfoot td.num {
        text-align: right;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }
    .ledger-total-label { font-weight: 700; color: #212121; }
    .ledger-total { font-weight: 700; }
    .ledger-remaining { color: #E85D75; font-size: 15px; }

    .empty-text {
        text-align: center;
        padding: 40px 20px;
        color: #94A3B8;
        font-size: 13px;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-group { min-width: 0; }
        .filter-input { min-width: 0; }
        .filter-actions { margin-left: 0; }
        .btn-primary, .btn-outline { flex: 1; text-align: center; }
        .summary-grid { grid-template-columns: 1fr; }
        .sub-tabs { gap: 20px; overflow-x: auto; }
        .sub-tab { font-size: 13px; white-space: nowrap; }
    }

    /* PRINT */
    @media print {
        .filter-bar, .filter-actions, .page-header, .sub-tabs { display: none !important; }
        body { background: #FFFFFF; }
        .report-block { box-shadow: none; }
        .report-header { background: #FFFFFF; border-bottom: 2px solid #E85D75; }
        .report-body { padding: 0; }
        .tile { background: #FFFFFF; border: 1px solid #E5E5E5; }
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/reports/index.blade.php ENDPATH**/ ?>