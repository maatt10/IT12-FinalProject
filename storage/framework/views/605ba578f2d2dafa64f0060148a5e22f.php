

<?php $__env->startSection('title', 'Reports'); ?>

<?php $__env->startSection('content'); ?>


<div class="page-header">
    <div>
        <h1>Reports</h1>
        <p>View summarized reports for the shop's sales, stock, and operations.</p>
    </div>
</div>

<div class="reports-page">

    
    <form action="<?php echo e(route('reports.index')); ?>" method="GET" class="filter-bar">
        <div class="filter-group">
            <label for="report">Report</label>
            <select id="report" name="report" class="filter-input" onchange="this.form.submit()">
                <option value="sales" <?php echo e($report === 'sales' ? 'selected' : ''); ?>>Sales</option>
                <option value="online-orders" <?php echo e($report === 'online-orders' ? 'selected' : ''); ?>>Online Orders</option>
                <option value="stock" <?php echo e($report === 'stock' ? 'selected' : ''); ?>>Overall Stock</option>
                <option value="stock-in" <?php echo e($report === 'stock-in' ? 'selected' : ''); ?>>Stock-in</option>
                <option value="production" <?php echo e($report === 'production' ? 'selected' : ''); ?>>Production</option>
            </select>
        </div>

        <?php if($report !== 'stock'): ?>
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
        <?php endif; ?>

        <div class="filter-actions">
            <a href="<?php echo e(route('reports.export', request()->query())); ?>" class="btn-outline">Export Excel</a>
            <a href="<?php echo e(route('reports.print', request()->query())); ?>" target="_blank" class="btn-primary">Export PDF</a>
        </div>
    </form>

    <?php if($report !== 'stock'): ?>
        <p class="period-label"><?php echo e($periodLabel); ?></p>
    <?php endif; ?>

    
    <div class="report-block">

        
        <div class="report-header">
            <h2>
                <?php switch($report):
                    case ('sales'): ?> Sales Report <?php break; ?>
                    <?php case ('online-orders'): ?> Online Orders Report <?php break; ?>
                    <?php case ('stock'): ?> Overall Stock Report <?php break; ?>
                    <?php case ('stock-in'): ?> Stock-in Report <?php break; ?>
                    <?php case ('production'): ?> Production Report <?php break; ?>
                <?php endswitch; ?>
            </h2>
            <p>
                <?php if($report === 'stock'): ?>
                    Snapshot · <?php echo e(now()->format('F d, Y h:i A')); ?>

                <?php else: ?>
                    <?php echo e(ucfirst($period)); ?> · <?php echo e($periodLabel); ?>

                <?php endif; ?>
            </p>
        </div>

        
        <div class="report-body">

            
            <?php if($report === 'sales'): ?>
                <h3 class="section-label">Summary</h3>
                <div class="summary-grid">
                    <div class="tile">
                        <span class="tile-label">Orders</span>
                        <span class="tile-value"><?php echo e($salesData['orders']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Items Sold</span>
                        <span class="tile-value"><?php echo e((float) $salesData['itemsSold']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Gross</span>
                        <span class="tile-value">₱<?php echo e(number_format($salesData['gross'], 2)); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Discounts</span>
                        <span class="tile-value">₱<?php echo e(number_format($salesData['discounts'], 2)); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Net Sales</span>
                        <span class="tile-value">₱<?php echo e(number_format($salesData['net'], 2)); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Avg. Order</span>
                        <span class="tile-value">₱<?php echo e(number_format($salesData['avgOrder'], 2)); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Cash</span>
                        <span class="tile-value">₱<?php echo e(number_format($salesData['paymentCash'], 2)); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">GCash</span>
                        <span class="tile-value">₱<?php echo e(number_format($salesData['paymentGcash'], 2)); ?></span>
                    </div>
                </div>

                <h3 class="section-label">Sales Records</h3>
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
                        <span class="tile-value">₱<?php echo e(number_format($ordersData['net'], 2)); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Avg. Order</span>
                        <span class="tile-value">₱<?php echo e(number_format($ordersData['avgOrder'], 2)); ?></span>
                    </div>
                </div>

                <h3 class="section-label">Status Breakdown</h3>
                <div class="summary-grid">
                    <?php $__currentLoopData = $ordersData['statusCounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="tile">
                            <span class="tile-label"><?php echo e(ucfirst($status)); ?></span>
                            <span class="tile-value"><?php echo e($count); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <h3 class="section-label">Orders</h3>
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
                <h3 class="section-label">Summary</h3>
                <div class="summary-grid">
                    <div class="tile">
                        <span class="tile-label">Total Items</span>
                        <span class="tile-value"><?php echo e($stockData['totalItems']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Products</span>
                        <span class="tile-value"><?php echo e($stockData['totalProducts']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Materials</span>
                        <span class="tile-value"><?php echo e($stockData['totalMaterials']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Low Stock</span>
                        <span class="tile-value"><?php echo e($stockData['lowStockCount']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Out of Stock</span>
                        <span class="tile-value"><?php echo e($stockData['outOfStockCount']); ?></span>
                    </div>
                </div>

                <h3 class="section-label">Current Stock</h3>
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
                <h3 class="section-label">Summary</h3>
                <div class="summary-grid">
                    <div class="tile">
                        <span class="tile-label">Transactions</span>
                        <span class="tile-value"><?php echo e($stockInData['transactions']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Items Received</span>
                        <span class="tile-value"><?php echo e((float) $stockInData['totalItems']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Total Spent</span>
                        <span class="tile-value">₱<?php echo e(number_format($stockInData['totalSpent'], 2)); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Avg. Transaction</span>
                        <span class="tile-value">₱<?php echo e(number_format($stockInData['avgTransaction'], 2)); ?></span>
                    </div>
                </div>

                <h3 class="section-label">Stock-in Records</h3>
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
                <h3 class="section-label">Summary</h3>
                <div class="summary-grid">
                    <div class="tile">
                        <span class="tile-label">Batches</span>
                        <span class="tile-value"><?php echo e($productionData['batches']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Total Produced</span>
                        <span class="tile-value"><?php echo e((float) $productionData['totalQuantity']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Unique Products</span>
                        <span class="tile-value"><?php echo e($productionData['uniqueProducts']); ?></span>
                    </div>
                    <div class="tile">
                        <span class="tile-label">Avg. per Batch</span>
                        <span class="tile-value"><?php echo e((float) $productionData['avgBatch']); ?></span>
                    </div>
                </div>

                <h3 class="section-label">Production Records</h3>
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

        </div>
    </div>

</div>

<style>
    /* ===============================
       FILTER BAR
       =============================== */
    .reports-page {
        max-width: 1100px;
    }

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

    .filter-actions {
        display: flex;
        gap: 8px;
        margin-left: auto;
    }

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
    .btn-primary:hover {
        background: #D14A62;
        border-color: #D14A62;
    }

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
    .btn-outline:hover {
        background: #FCE4EC;
    }

    .period-label {
        font-size: 13px;
        color: #64748B;
        margin-bottom: 16px;
        padding-left: 4px;
    }

    /* ===============================
       REPORT BLOCK
       =============================== */
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

    .report-body {
        padding: 24px;
    }

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
    .report-table + .section-label,
    .summary-grid + .section-label {
        margin-top: 28px;
    }

    /* ===============================
       SUMMARY TILES
       =============================== */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
    }

    .tile {
        background: #FAF7F3;
        border-radius: 10px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .tile-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        white-space: nowrap;
    }

    .tile-value {
        font-size: 18px;
        font-weight: 700;
        color: #212121;
        white-space: nowrap;
        text-align: right;
    }

    /* ===============================
       TABLE
       =============================== */
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
    }
    .report-table thead th:first-child { border-top-left-radius: 8px; }
    .report-table thead th:last-child { border-top-right-radius: 8px; }
    .report-table thead th.num { text-align: right; }

    .report-table tbody td {
        padding: 12px 14px;
        font-size: 13px;
        color: #212121;
        border-bottom: 1px solid #F5EEE4;
    }
    .report-table tbody tr:last-child td { border-bottom: none; }
    .report-table tbody tr:hover { background: #FFF9FB; }
    .report-table tbody td.num { text-align: right; white-space: nowrap; }
    .report-table tbody td.muted { color: #64748B; }
    .report-table tbody td.strong { font-weight: 700; color: #2E5A3B; }
    .report-table tbody td.mono {
        font-family: 'SF Mono', Consolas, monospace;
        color: #E85D75;
        font-weight: 600;
        font-size: 12px;
    }

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

    .empty-text {
        text-align: center;
        padding: 40px 20px;
        color: #94A3B8;
        font-size: 13px;
    }

    /* ===============================
       RESPONSIVE
       =============================== */
    @media (max-width: 768px) {
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-group { min-width: 0; }
        .filter-input { min-width: 0; }
        .filter-actions { margin-left: 0; }
        .btn-primary, .btn-outline { flex: 1; text-align: center; }
        .summary-grid { grid-template-columns: 1fr; }
    }

    /* ===============================
       PRINT
       =============================== */
    @media print {
        .filter-bar,
        .filter-actions,
        .page-header { display: none !important; }

        body { background: #FFFFFF; }

        .report-block {
            box-shadow: none;
        }
        .report-header {
            background: #FFFFFF;
            border-bottom: 2px solid #E85D75;
        }
        .report-body { padding: 0; }
        .tile { background: #FFFFFF; border: 1px solid #E5E5E5; }
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/reports/index.blade.php ENDPATH**/ ?>