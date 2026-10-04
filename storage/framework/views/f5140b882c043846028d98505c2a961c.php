<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt <?php echo e($sale->reference_code); ?> — Lara's Flowershop</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'SF Mono', 'Courier New', Consolas, monospace;
            font-size: 12px;
            color: #212121;
            background: #F9F6F0;
            padding: 20px;
            line-height: 1.5;
        }

        .receipt {
            width: 80mm;
            max-width: 100%;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 20px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(46, 90, 59, 0.08);
        }

        /* HEADER */
        .header {
            text-align: center;
            padding-bottom: 12px;
            border-bottom: 2px dashed #F0E6DD;
            margin-bottom: 12px;
        }

        .logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 6px;
        }

        .store-name {
            font-size: 18px;
            font-weight: 700;
            color: #2E5A3B;
            letter-spacing: 0.5px;
            font-family: 'Instrument Sans', Arial, sans-serif;
        }

        .store-sub {
            font-size: 9px;
            font-weight: 600;
            color: #D4AF37;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 2px;
            margin-bottom: 6px;
        }

        .store-info {
            font-size: 10px;
            color: #64748B;
            line-height: 1.6;
        }

        /* REFERENCE BADGE */
        .reference-badge {
            text-align: center;
            margin: 14px 0 14px;
            padding: 10px;
            background: #FCE4EC;
            border-radius: 6px;
        }

        .ref-label {
            display: block;
            font-size: 9px;
            font-weight: 700;
            color: #E85D75;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .ref-value {
            display: block;
            font-size: 16px;
            font-weight: 700;
            color: #E85D75;
            letter-spacing: 2px;
            font-family: 'Instrument Sans', Arial, sans-serif;
        }

        /* META */
        .meta {
            font-size: 10px;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #F0E6DD;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .meta-label {
            color: #94A3B8;
        }

        .meta-value {
            color: #212121;
            font-weight: 600;
            text-align: right;
        }

        /* ITEMS TABLE */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }

        .items-table thead th {
            text-align: left;
            padding: 6px 2px;
            ;
            color: #E85D75;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-size: 9px;
        }

        .items-table thead th.right {
            text-align: right;
        }

        .items-table tbody td {
            padding: 5px 2px;
            border-bottom: 1px solid #F5EEE4;
            vertical-align: top;
        }

        .items-table tbody td.right {
            text-align: right;
        }

        .items-table tbody td.qty {
            text-align: center;
        }

        .item-name {
            font-weight: 600;
            color: #212121;
            line-height: 1.3;
        }

        /* TOTALS */
        .totals {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px dashed #F0E6DD;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 11px;
        }

        .total-row .label {
            color: #64748B;
        }

        .total-row .value {
            color: #212121;
            font-weight: 600;
        }

        .total-row.grand {
            margin-top: 6px;
            padding-top: 8px;
            border-top: 1px solid #F0E6DD;
        }

        .total-row.grand .label {
            font-size: 12px;
            font-weight: 700;
            color: #212121;
        }

        .total-row.grand .value {
            font-size: 16px;
            font-weight: 700;
            color: #E85D75;
        }

        .total-row.change .value {
            color: #2E5A3B;
        }

        .total-row.discount-line .label {
            color: #B8860B;
            font-weight: 600;
        }

        .total-row.discount-line .value {
            color: #B8860B;
            font-weight: 700;
        }

        /* DISCOUNT INFO BOX */
        .discount-info {
            margin-top: 14px;
            padding: 10px 12px;
            background: #FFF8E1;
            solid #D4AF37;
            border-radius: 4px;
            font-size: 10px;
        }

        .discount-info-title {
            font-size: 9px;
            font-weight: 700;
            color: #B8860B;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .discount-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .discount-info-row span:first-child {
            color: #94A3B8;
        }

        .discount-info-row span:last-child {
            color: #212121;
            font-weight: 600;
            text-align: right;
            max-width: 60%;
            word-break: break-word;
        }

        /* FOOTER */
        .footer {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 2px dashed #F0E6DD;
            text-align: center;
        }

        .thank-you {
            font-size: 12px;
            font-weight: 700;
            color: #2E5A3B;
            margin-bottom: 4px;
        }

        .powered-by {
            font-size: 9px;
            color: #94A3B8;
        }

        /* PRINT */
        @media print {
            body {
                background: #FFFFFF;
                padding: 0;
            }

            .receipt {
                box-shadow: none;
                border-radius: 0;
                padding: 10px;
            }

            .no-print {
                display: none !important;
            }
        }

        /* SCREEN BUTTONS */
        .print-button {
            display: block;
            width: 80mm;
            max-width: 100%;
            margin: 16px auto 0;
            padding: 12px;
            background: #E85D75;
            color: #FFFFFF;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            font-family: 'Instrument Sans', Arial, sans-serif;
        }

        .print-button:hover {
            background: #D14A62;
        }

        .back-link {
            display: block;
            width: 80mm;
            max-width: 100%;
            margin: 10px auto 0;
            text-align: center;
            font-size: 12px;
            color: #64748B;
            text-decoration: none;
            font-family: 'Instrument Sans', Arial, sans-serif;
        }

        .back-link:hover {
            color: #E85D75;
        }
    </style>
</head>

<body>

    <div class="receipt">

        
        <div class="header">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Lara's Flowershop" class="logo">
            <div class="store-name">Lara's Flowershop</div>
            <div class="store-sub">Est. 2021</div>
            <div class="store-info">
                Terminal POS-03
            </div>
        </div>

        
        <div class="reference-badge">
            <span class="ref-label">Receipt No.</span>
            <span class="ref-value"><?php echo e($sale->reference_code); ?></span>
        </div>

        
        <div class="meta">
            <div class="meta-row">
                <span class="meta-label">Date:</span>
                <span class="meta-value"><?php echo e($sale->sale_date->format('M d, Y')); ?></span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Time:</span>
                <span class="meta-value"><?php echo e($sale->sale_date->format('h:i A')); ?></span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Cashier:</span>
                <span class="meta-value"><?php echo e($sale->user->full_name ?? 'Cashier'); ?></span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Customer:</span>
                <span class="meta-value"><?php echo e($sale->customer->full_name ?? 'Walk-in'); ?></span>
            </div>
        </div>

        
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 44%;">Item</th>
                    <th style="width: 12%;" class="qty">Qty</th>
                    <th style="width: 20%;" class="right">Price</th>
                    <th style="width: 24%;" class="right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                $lineTotal = $item->quantity * $item->unit_price;
                ?>
                <tr>
                    <td>
                        <div class="item-name"><?php echo e($item->product->display_name); ?></div>
                    </td>
                    <td class="qty"><?php echo e((float) $item->quantity); ?></td>
                    <td class="right">₱<?php echo e(number_format($item->unit_price, 2)); ?></td>
                    <td class="right" style="font-weight: 600;">₱<?php echo e(number_format($lineTotal, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php for($i = 0; $i < max(0, 8 - $sale->items->count()); $i++): ?>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="qty">&nbsp;</td>
                        <td class="right">&nbsp;</td>
                        <td class="right">&nbsp;</td>
                    </tr>
                    <?php endfor; ?>
            </tbody>
        </table>

        
        <div class="totals">

            <div class="total-row">
                <span class="label">Subtotal</span>
                <span class="value">₱<?php echo e(number_format($sale->subtotal, 2)); ?></span>
            </div>

            <?php if($sale->discount_amount > 0): ?>
            <div class="total-row discount-line">
                <span class="label">
                    Discount (<?php echo e($sale->discount_type === 'pwd' ? 'PWD 20%' : 'Senior 20%'); ?>)
                </span>
                <span class="value">− ₱<?php echo e(number_format($sale->discount_amount, 2)); ?></span>
            </div>
            <?php endif; ?>

            <div class="total-row grand">
                <span class="label">TOTAL</span>
                <span class="value">₱<?php echo e(number_format($sale->total_amount, 2)); ?></span>
            </div>

            <?php if($sale->payment_method === 'cash'): ?>
            <div class="total-row">
                <span class="label">Cash</span>
                <span class="value">₱<?php echo e(number_format($sale->total_amount, 2)); ?></span>
            </div>
            <div class="total-row change">
                <span class="label">Change</span>
                <span class="value">₱<?php echo e(number_format(0, 2)); ?></span>
            </div>
            <?php elseif($sale->payment_method === 'gcash'): ?>
            <div class="total-row">
                <span class="label">GCash</span>
                <span class="value">₱<?php echo e(number_format($sale->total_amount, 2)); ?></span>
            </div>
            <div class="total-row">
                <span class="label">Ref No.</span>
                <span class="value"><?php echo e($sale->gcash_reference ?? '—'); ?></span>
            </div>
            <?php endif; ?>

        </div>

        
        <?php if($sale->discount_type && $sale->discount_type !== 'none'): ?>
        <div class="discount-info">
            <div class="discount-info-title">
                <?php echo e($sale->discount_type === 'pwd' ? 'PWD Discount Applied' : 'Senior Citizen Discount Applied'); ?>

            </div>
            <div class="discount-info-row">
                <span>Name:</span>
                <span><?php echo e($sale->discount_name ?: '—'); ?></span>
            </div>
            <div class="discount-info-row">
                <span>ID No:</span>
                <span><?php echo e($sale->discount_id_number ?: '—'); ?></span>
            </div>
        </div>
        <?php endif; ?>

        
        <div class="footer">
            <div class="thank-you">Thank you! Come again!</div>
            <div class="powered-by">
                Lara's Flowershop
            </div>
        </div>

    </div>

    <button onclick="window.print()" class="print-button no-print">
        Print Receipt
    </button>
    <?php if(request('from') === 'records'): ?>
    <a href="<?php echo e(route('records.index', ['tab' => 'sales', 'sub' => 'walk-in'])); ?>" class="back-link no-print">
        ← Back to Sales Records
    </a>
    <?php else: ?>
    <a href="<?php echo e(route('sales.create')); ?>" class="back-link no-print">
        ← Start New Sale
    </a>
    <?php endif; ?>

</body>

</html><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/sales/print.blade.php ENDPATH**/ ?>