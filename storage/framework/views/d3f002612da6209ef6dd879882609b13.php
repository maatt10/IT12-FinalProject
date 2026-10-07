<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
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
        — <?php echo e($periodLabel); ?>

    </title>

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

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Instrument Sans', Arial, sans-serif;
            font-size: 12px;
            color: #212121;
            background: #F9F6F0;
            padding: 30px 20px;
            line-height: 1.5;
        }

        .report-sheet {
            max-width: 900px;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(46, 90, 59, 0.08);
        }

        .report-header {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2E5A3B;
            margin-bottom: 24px;
        }
        .report-header-logo { width: 64px; height: 64px; object-fit: contain; flex-shrink: 0; }
        .report-header-info { flex: 1; }
        .report-header-store { font-size: 20px; font-weight: 700; color: #2E5A3B; }
        .report-header-sub {
            font-size: 9px; font-weight: 700; color: #D4AF37;
            letter-spacing: 3px; text-transform: uppercase; margin-top: 2px;
        }
        .report-header-meta { font-size: 10px; color: #64748B; margin-top: 6px; }
        .report-header-right { text-align: right; font-size: 10px; color: #64748B; }
        .report-header-right strong {
            display: block; font-size: 11px; color: #212121; margin-bottom: 2px;
        }

        .report-title { margin-bottom: 24px; }
        .report-title h1 {
            font-size: 18px; font-weight: 700; color: #212121;
            text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px;
        }
        .report-title .period {
            display: inline-block; font-size: 11px; font-weight: 600;
            color: #E85D75; background: #FCE4EC;
            padding: 3px 12px; border-radius: 4px; letter-spacing: 0.5px;
        }

        .section-label {
            font-size: 10px; font-weight: 700; color: #94A3B8;
            text-transform: uppercase; letter-spacing: 1.5px;
            margin: 24px 0 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #F0E6DD;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .summary-tile {
            background: #FEFCF9;
            border: 1px solid #F0E6DD;
            border-radius: 8px;
            padding: 12px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .summary-tile-label {
            font-size: 9px; font-weight: 700; color: #94A3B8;
            text-transform: uppercase; letter-spacing: 0.8px;
            line-height: 1.3;
            min-width: 0;
            word-break: break-word;
        }
        .summary-tile-value {
            font-size: 14px; font-weight: 700; color: #212121;
            white-space: nowrap;
            text-align: right;
            font-variant-numeric: tabular-nums;
            flex-shrink: 0;
        }

        .table-scroll {
            width: 100%;
            overflow-x: auto;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .report-table thead th {
            text-align: left;
            padding: 10px 8px;
            background: #FDF2F4;
            color: #E85D75;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.8px;
            border-bottom: 2px solid #F8BBD0;
            white-space: nowrap;
        }
        .report-table thead th.num { text-align: right; }
        .report-table tbody td {
            padding: 9px 8px;
            border-bottom: 1px solid #F5EEE4;
            vertical-align: top;
        }
        .report-table tbody tr:nth-child(even) { background: #FEFCF9; }
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
            white-space: nowrap;
        }

        /* LEDGER TABLE */
        .ledger-table { table-layout: fixed; }
        .ledger-item-col { width: 34%; }
        .ledger-num-col { width: 16.5%; }

        .ledger-item-cell { word-break: break-word; }
        .ledger-item-name { font-weight: 600; color: #212121; line-height: 1.3; }
        .ledger-item-unit { font-size: 10px; color: #94A3B8; margin-top: 2px; }
        .ledger-in { color: #2E5A3B; font-weight: 600; }
        .ledger-out { color: #DC3545; font-weight: 600; }

        .report-table tfoot td {
            padding: 10px 8px;
            background: #FAF7F3;
            border-top: 2px solid #F8BBD0;
            font-weight: 700;
        }
        .report-table tfoot td.num {
            text-align: right;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }
        .ledger-total-label { font-weight: 700; color: #212121; }
        .ledger-remaining { color: #E85D75; font-size: 13px; }

        .report-footer {
            margin-top: 40px; padding-top: 20px;
            border-top: 2px solid #F0E6DD;
            display: flex; justify-content: space-between;
            align-items: flex-end; gap: 20px;
            font-size: 10px; color: #64748B;
        }
        .report-footer strong {
            display: block; color: #212121; font-size: 11px; margin-bottom: 4px;
        }
        .signature-line {
            margin-top: 30px; padding-top: 4px;
            border-top: 1px solid #212121;
            min-width: 200px; text-align: center;
            color: #212121; font-size: 10px;
        }

        .empty-text {
            text-align: center; padding: 40px 20px;
            color: #94A3B8; font-size: 12px;
        }

        .screen-actions {
            max-width: 900px; margin: 20px auto 0;
            display: flex; gap: 10px; justify-content: center;
        }
        .screen-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 24px; border: none; border-radius: 10px;
            font-size: 14px; font-weight: 600; cursor: pointer;
            text-decoration: none; font-family: inherit;
            transition: all 0.2s ease;
        }
        .screen-btn.primary {
            background: #E85D75; color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(232, 93, 117, 0.3);
        }
        .screen-btn.primary:hover { background: #D14A62; }
        .screen-btn.secondary {
            background: #FFFFFF; color: #64748B;
            border: 1.5px solid #F0E6DD;
        }
        .screen-btn.secondary:hover {
            background: #FEFCF9; border-color: #E85D75; color: #E85D75;
        }

        @page { size: A4 portrait; margin: 15mm; }

        @media print {
            body { background: #FFFFFF; padding: 0; font-size: 10px; }
            .report-sheet { box-shadow: none; border-radius: 0; padding: 0; max-width: 100%; }
            .screen-actions { display: none !important; }
            .report-table tbody tr:nth-child(even) { background: #FAFAFA; }
            .table-scroll { overflow: visible; }
        }
    </style>
</head>

<body>

    <div class="report-sheet">

        <div class="report-header">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Lara's Flowershop" class="report-header-logo">
            <div class="report-header-info">
                <div class="report-header-store">Lara's Flowershop</div>
                <div class="report-header-sub">Est. 2021</div>
                <div class="report-header-meta">Terminal POS-03</div>
            </div>
            <div class="report-header-right">
                <strong>Generated</strong>
                <?php echo e(now()->format('M d, Y')); ?><br>
                <?php echo e(now()->format('h:i A')); ?>

            </div>
        </div>

        <div class="report-title">
            <h1>
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
            </h1>
            <span class="period"><?php echo e(ucfirst($period)); ?> · <?php echo e($periodLabel); ?></span>
        </div>

        
        <?php if($report === 'sales' && $sub === 'walk-in'): ?>
            <div class="section-label">Summary</div>
            <div class="summary-grid">
                <div class="summary-tile">
                    <div class="summary-tile-label">Orders</div>
                    <div class="summary-tile-value"><?php echo e($salesData['orders']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Items Sold</div>
                    <div class="summary-tile-value"><?php echo e($fmtQty($salesData['itemsSold'])); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Gross</div>
                    <div class="summary-tile-value">₱<?php echo e($fmtMoney($salesData['gross'])); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Discounts</div>
                    <div class="summary-tile-value">₱<?php echo e($fmtMoney($salesData['discounts'])); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Net Sales</div>
                    <div class="summary-tile-value">₱<?php echo e($fmtMoney($salesData['net'])); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Avg Order</div>
                    <div class="summary-tile-value">₱<?php echo e($fmtMoney($salesData['avgOrder'])); ?></div>
                </div>
            </div>

            <div class="section-label">Sales Records</div>
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
            <div class="section-label">Summary</div>
            <div class="summary-grid">
                <div class="summary-tile">
                    <div class="summary-tile-label">Total Orders</div>
                    <div class="summary-tile-value"><?php echo e($ordersData['total']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Completed</div>
                    <div class="summary-tile-value"><?php echo e($ordersData['completed']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Cancelled</div>
                    <div class="summary-tile-value"><?php echo e($ordersData['cancelled']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Net Revenue</div>
                    <div class="summary-tile-value">₱<?php echo e($fmtMoney($ordersData['net'])); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Avg Order</div>
                    <div class="summary-tile-value">₱<?php echo e($fmtMoney($ordersData['avgOrder'])); ?></div>
                </div>
            </div>

            <div class="section-label">Orders</div>
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
            <div class="section-label">Summary</div>
            <div class="summary-grid">
                <div class="summary-tile">
                    <div class="summary-tile-label">Total Revenue</div>
                    <div class="summary-tile-value">₱<?php echo e($fmtMoney($overallData['totalRevenue'])); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Transactions</div>
                    <div class="summary-tile-value"><?php echo e($overallData['totalCount']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Avg Transaction</div>
                    <div class="summary-tile-value">₱<?php echo e($fmtMoney($overallData['avgTransaction'])); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Walk-in</div>
                    <div class="summary-tile-value">₱<?php echo e($fmtMoney($overallData['walkInRevenue'])); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Online</div>
                    <div class="summary-tile-value">₱<?php echo e($fmtMoney($overallData['onlineRevenue'])); ?></div>
                </div>
            </div>

            <div class="section-label">All Transactions</div>
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
            <div class="section-label">Summary</div>
            <div class="summary-grid">
                <div class="summary-tile">
                    <div class="summary-tile-label">Total Items</div>
                    <div class="summary-tile-value"><?php echo e(count($ledgerRows)); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Beginning</div>
                    <div class="summary-tile-value"><?php echo e($fmtQty($ledgerTotals['beginning'])); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Stock-in</div>
                    <div class="summary-tile-value"><?php echo e($fmtQty($ledgerTotals['stock_in'])); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Stock-out</div>
                    <div class="summary-tile-value"><?php echo e($fmtQty($ledgerTotals['stock_out'])); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Remaining</div>
                    <div class="summary-tile-value"><?php echo e($fmtQty($ledgerTotals['remaining'])); ?></div>
                </div>
            </div>

            <div class="section-label">Stock Ledger</div>
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

        <div class="report-footer">
            <div>
                <strong>Report Generated By</strong>
                <?php echo e(auth()->user()->full_name ?? 'System'); ?><br>
                <?php echo e(now()->format('F d, Y · h:i A')); ?>

            </div>
            <div>
                <div class="signature-line">Authorized Signature</div>
            </div>
        </div>

    </div>

    <div class="screen-actions">
        <button onclick="window.print()" class="screen-btn primary">Save as PDF / Print</button>
        <a href="<?php echo e(route('reports.index', request()->query())); ?>" class="screen-btn secondary">← Back to Reports</a>
    </div>

</body>
</html><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/reports/print.blade.php ENDPATH**/ ?>