<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(match($report) {
        'sales' => 'Sales Report',
        'online-orders' => 'Online Orders Report',
        'stock' => 'Overall Stock Report',
        'stock-in' => 'Stock-in Report',
        'production' => 'Production Report',
        default => 'Report',
    }); ?> — <?php echo e($periodLabel); ?></title>

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
            padding: 40px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(46, 90, 59, 0.08);
        }

        /* HEADER */
        .report-header {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2E5A3B;
            margin-bottom: 24px;
        }
        .report-header-logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .report-header-info { flex: 1; }
        .report-header-store {
            font-size: 20px;
            font-weight: 700;
            color: #2E5A3B;
            letter-spacing: 0.3px;
        }
        .report-header-sub {
            font-size: 9px;
            font-weight: 700;
            color: #D4AF37;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .report-header-meta {
            font-size: 10px;
            color: #64748B;
            margin-top: 6px;
        }
        .report-header-right {
            text-align: right;
            font-size: 10px;
            color: #64748B;
        }
        .report-header-right strong {
            display: block;
            font-size: 11px;
            color: #212121;
            margin-bottom: 2px;
        }

        /* TITLE */
        .report-title {
            margin-bottom: 24px;
        }
        .report-title h1 {
            font-size: 18px;
            font-weight: 700;
            color: #212121;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
        }
        .report-title .period {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            color: #E85D75;
            background: #FCE4EC;
            padding: 3px 12px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }

        /* SECTION */
        .section-label {
            font-size: 10px;
            font-weight: 700;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 24px 0 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #F0E6DD;
        }

        /* SUMMARY */
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
        }
        .summary-tile-label {
            font-size: 9px;
            font-weight: 700;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .summary-tile-value {
            font-size: 16px;
            font-weight: 700;
            color: #212121;
        }

        /* TABLE */
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
        }
        .report-table thead th.num { text-align: right; }
        .report-table tbody td {
            padding: 9px 8px;
            border-bottom: 1px solid #F5EEE4;
            vertical-align: top;
        }
        .report-table tbody tr:nth-child(even) { background: #FEFCF9; }
        .report-table tbody td.num { text-align: right; white-space: nowrap; }
        .report-table tbody td.muted { color: #64748B; }
        .report-table tbody td.strong { font-weight: 700; color: #2E5A3B; }
        .report-table tbody td.mono {
            font-family: 'SF Mono', Consolas, monospace;
            color: #E85D75;
            font-weight: 600;
        }

        .low-tag {
            display: inline-block;
            font-size: 8px;
            font-weight: 700;
            color: #DC3545;
            background: #FDECEA;
            padding: 1px 4px;
            border-radius: 3px;
            margin-left: 3px;
            letter-spacing: 0.5px;
        }

        /* FOOTER */
        .report-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #F0E6DD;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            font-size: 10px;
            color: #64748B;
        }
        .report-footer strong {
            display: block;
            color: #212121;
            font-size: 11px;
            margin-bottom: 4px;
        }
        .signature-line {
            margin-top: 30px;
            padding-top: 4px;
            border-top: 1px solid #212121;
            min-width: 200px;
            text-align: center;
            color: #212121;
            font-size: 10px;
        }

        .empty-text {
            text-align: center;
            padding: 40px 20px;
            color: #94A3B8;
            font-size: 12px;
        }

        /* SCREEN BUTTONS */
        .screen-actions {
            max-width: 900px;
            margin: 20px auto 0;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .screen-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            font-family: inherit;
            transition: all 0.2s ease;
        }
        .screen-btn.primary {
            background: #E85D75;
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(232, 93, 117, 0.3);
        }
        .screen-btn.primary:hover { background: #D14A62; }
        .screen-btn.secondary {
            background: #FFFFFF;
            color: #64748B;
            border: 1.5px solid #F0E6DD;
        }
        .screen-btn.secondary:hover {
            background: #FEFCF9;
            border-color: #E85D75;
            color: #E85D75;
        }

        /* PRINT */
        @page { size: A4 portrait; margin: 15mm; }

        @media print {
            body { background: #FFFFFF; padding: 0; font-size: 10px; }
            .report-sheet {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
                max-width: 100%;
            }
            .screen-actions { display: none !important; }
            .report-table tbody tr:nth-child(even) { background: #FAFAFA; }
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
                <?php switch($report):
                    case ('sales'): ?> Sales Report <?php break; ?>
                    <?php case ('online-orders'): ?> Online Orders Report <?php break; ?>
                    <?php case ('stock'): ?> Overall Stock Report <?php break; ?>
                    <?php case ('stock-in'): ?> Stock-in Report <?php break; ?>
                    <?php case ('production'): ?> Production Report <?php break; ?>
                <?php endswitch; ?>
            </h1>
            <span class="period">
                <?php if($report === 'stock'): ?>
                    Snapshot as of <?php echo e(now()->format('F d, Y h:i A')); ?>

                <?php else: ?>
                    <?php echo e(ucfirst($period)); ?> · <?php echo e($periodLabel); ?>

                <?php endif; ?>
            </span>
        </div>

        
        <?php if($report === 'sales'): ?>
            <div class="section-label">Summary</div>
            <div class="summary-grid">
                <div class="summary-tile">
                    <div class="summary-tile-label">Orders</div>
                    <div class="summary-tile-value"><?php echo e($salesData['orders']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Items Sold</div>
                    <div class="summary-tile-value"><?php echo e((float) $salesData['itemsSold']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Gross Sales</div>
                    <div class="summary-tile-value">₱<?php echo e(number_format($salesData['gross'], 2)); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Discounts</div>
                    <div class="summary-tile-value">₱<?php echo e(number_format($salesData['discounts'], 2)); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Net Sales</div>
                    <div class="summary-tile-value">₱<?php echo e(number_format($salesData['net'], 2)); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Avg. Order</div>
                    <div class="summary-tile-value">₱<?php echo e(number_format($salesData['avgOrder'], 2)); ?></div>
                </div>
            </div>

            <div class="section-label">Sales Records</div>
            <?php if($salesRecords->isEmpty()): ?>
                <p class="empty-text">No walk-in sales recorded during this period.</p>
            <?php else: ?>
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
                                <td class="num muted">₱<?php echo e(number_format($sale->subtotal, 2)); ?></td>
                                <td class="num muted">₱<?php echo e(number_format($sale->discount_amount, 2)); ?></td>
                                <td class="num strong">₱<?php echo e(number_format($sale->total_amount, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        
        <?php if($report === 'online-orders'): ?>
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
                    <div class="summary-tile-value">₱<?php echo e(number_format($ordersData['net'], 2)); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Avg. Order</div>
                    <div class="summary-tile-value">₱<?php echo e(number_format($ordersData['avgOrder'], 2)); ?></div>
                </div>
            </div>

            <div class="section-label">Status Breakdown</div>
            <div class="summary-grid">
                <?php $__currentLoopData = $ordersData['statusCounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="summary-tile">
                        <div class="summary-tile-label"><?php echo e(ucfirst($status)); ?></div>
                        <div class="summary-tile-value"><?php echo e($count); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="section-label">Orders</div>
            <?php if($orderRecords->isEmpty()): ?>
                <p class="empty-text">No online orders recorded during this period.</p>
            <?php else: ?>
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
                                <td class="num strong">₱<?php echo e(number_format($order->total_amount, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        
        <?php if($report === 'stock'): ?>
            <div class="section-label">Summary</div>
            <div class="summary-grid">
                <div class="summary-tile">
                    <div class="summary-tile-label">Total Items</div>
                    <div class="summary-tile-value"><?php echo e($stockData['totalItems']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Products</div>
                    <div class="summary-tile-value"><?php echo e($stockData['totalProducts']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Materials</div>
                    <div class="summary-tile-value"><?php echo e($stockData['totalMaterials']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Low Stock</div>
                    <div class="summary-tile-value"><?php echo e($stockData['lowStockCount']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Out of Stock</div>
                    <div class="summary-tile-value"><?php echo e($stockData['outOfStockCount']); ?></div>
                </div>
            </div>

            <div class="section-label">Current Stock</div>
            <?php if($stockProducts->isEmpty()): ?>
                <p class="empty-text">No items in inventory.</p>
            <?php else: ?>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Type</th>
                            <th class="num">Retail</th>
                            <th class="num">Production</th>
                            <th class="num">Total</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $stockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="font-weight: 600;">
                                    <?php echo e($product->name); ?><?php if($product->variation): ?> — <?php echo e($product->variation); ?><?php endif; ?>
                                </td>
                                <td class="muted"><?php echo e($product->item_type === 'material' ? 'Material' : 'Product'); ?></td>
                                <td class="num muted">
                                    <?php echo e($product->retail_stock); ?>

                                    <?php if($product->retail_low): ?> <span class="low-tag">LOW</span> <?php endif; ?>
                                </td>
                                <td class="num muted">
                                    <?php echo e($product->production_stock); ?>

                                    <?php if($product->production_low): ?> <span class="low-tag">LOW</span> <?php endif; ?>
                                </td>
                                <td class="num strong"><?php echo e($product->total_stock); ?></td>
                                <td class="muted"><?php echo e($product->stock_unit); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        
        <?php if($report === 'stock-in'): ?>
            <div class="section-label">Summary</div>
            <div class="summary-grid">
                <div class="summary-tile">
                    <div class="summary-tile-label">Transactions</div>
                    <div class="summary-tile-value"><?php echo e($stockInData['transactions']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Items Received</div>
                    <div class="summary-tile-value"><?php echo e((float) $stockInData['totalItems']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Total Spent</div>
                    <div class="summary-tile-value">₱<?php echo e(number_format($stockInData['totalSpent'], 2)); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Avg. Transaction</div>
                    <div class="summary-tile-value">₱<?php echo e(number_format($stockInData['avgTransaction'], 2)); ?></div>
                </div>
            </div>

            <div class="section-label">Stock-in Records</div>
            <?php if($purchaseRecords->isEmpty()): ?>
                <p class="empty-text">No stock-in records during this period.</p>
            <?php else: ?>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Date &amp; Time</th>
                            <th>Supplier</th>
                            <th>Recorded By</th>
                            <th class="num">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $purchaseRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="muted"><?php echo e($purchase->purchase_date->format('M d, Y h:i A')); ?></td>
                                <td><?php echo e($purchase->supplier_name ?: '—'); ?></td>
                                <td class="muted"><?php echo e($purchase->user->full_name ?? 'Unknown'); ?></td>
                                <td class="num strong">₱<?php echo e(number_format($purchase->total_amount, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        
        <?php if($report === 'production'): ?>
            <div class="section-label">Summary</div>
            <div class="summary-grid">
                <div class="summary-tile">
                    <div class="summary-tile-label">Batches</div>
                    <div class="summary-tile-value"><?php echo e($productionData['batches']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Total Produced</div>
                    <div class="summary-tile-value"><?php echo e((float) $productionData['totalQuantity']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Unique Products</div>
                    <div class="summary-tile-value"><?php echo e($productionData['uniqueProducts']); ?></div>
                </div>
                <div class="summary-tile">
                    <div class="summary-tile-label">Avg. per Batch</div>
                    <div class="summary-tile-value"><?php echo e((float) $productionData['avgBatch']); ?></div>
                </div>
            </div>

            <div class="section-label">Production Records</div>
            <?php if($productionRecords->isEmpty()): ?>
                <p class="empty-text">No production activity during this period.</p>
            <?php else: ?>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Date &amp; Time</th>
                            <th>Product</th>
                            <th>Produced By</th>
                            <th class="num">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $productionRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $production): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="muted"><?php echo e($production->production_date->format('M d, Y h:i A')); ?></td>
                                <td style="font-weight: 600;"><?php echo e($production->product->display_name); ?></td>
                                <td class="muted"><?php echo e($production->producedBy->full_name ?? 'Unknown'); ?></td>
                                <td class="num strong">
                                    <?php echo e((float) $production->quantity_produced); ?> <?php echo e($production->product->stock_unit); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
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