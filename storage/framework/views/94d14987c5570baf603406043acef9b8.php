

<?php $__env->startSection('title', 'Order ' . $order->reference_code); ?>

<?php
$backUrl = request('from') === 'records'
? route('records.index', ['tab' => 'sales', 'sub' => 'online'])
: route('orders.index');
?>

<?php $__env->startSection('content'); ?>

<div class="form-page-wide">

    <div class="page-header">
        <div>
            <h1>
                Order Details
                <span class="ref-badge"><?php echo e($order->reference_code); ?></span>
            </h1>
            <p>View the complete information for this online bouquet order.</p>
        </div>

        <a href="<?php echo e($backUrl); ?>" class="btn btn-secondary">← Back</a>
    </div>

    
    <div class="card">
        <h2 class="card-heading">Order Information</h2>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Order Number</span>
                <span class="info-value mono"><?php echo e($order->reference_code); ?></span>
            </div>

            <div class="info-item">
                <span class="info-label">Order Date</span>
                <span class="info-value"><?php echo e($order->order_date->format('M d, Y h:i A')); ?></span>
            </div>

            <div class="info-item">
                <span class="info-label">Customer</span>
                <span class="info-value">
                    <?php echo e($order->customer->full_name ?? $order->customer_name ?? 'Unregistered Customer'); ?>

                </span>
            </div>

            <div class="info-item">
                <span class="info-label">Recorded By</span>
                <span class="info-value"><?php echo e($order->user->full_name ?? 'Unknown'); ?></span>
            </div>

            <div class="info-item">
                <span class="info-label">Order Type</span>
                <span class="info-value">
                    <?php echo e($order->order_type === 'ready_made' ? 'Ready-Made' : 'Customized'); ?>

                </span>
            </div>

            <div class="info-item">
                <span class="info-label">Current Status</span>
                <span class="status-text status-<?php echo e($order->order_status); ?>">
                    <?php echo e(ucfirst($order->order_status)); ?>

                </span>
            </div>
        </div>

        <?php if(in_array($order->order_status, ['completed', 'cancelled'])): ?>
        <div class="status-update">
            <div class="locked-note">
                This order is <strong><?php echo e($order->order_status); ?></strong>. Status can no longer be changed.
            </div>
        </div>
        <?php else: ?>
        <div class="status-update">
            <form action="<?php echo e(route('orders.status.update', $order)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="status-form-row">
                    <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                        <label for="order_status">Update Status</label>
                        <select id="order_status" name="order_status" class="form-control" required>
                            <option value="pending" <?php echo e($order->order_status === 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="confirmed" <?php echo e($order->order_status === 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                            <option value="preparing" <?php echo e($order->order_status === 'preparing' ? 'selected' : ''); ?>>Preparing</option>
                            <option value="ready" <?php echo e($order->order_status === 'ready' ? 'selected' : ''); ?>>Ready</option>
                            <option value="completed" <?php echo e($order->order_status === 'completed' ? 'selected' : ''); ?>>Completed</option>
                            <option value="cancelled" <?php echo e($order->order_status === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="card">
        <h2 class="card-heading">Receiver Information</h2>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Receiver Name</span>
                <span class="info-value"><?php echo e($order->receiver_full_name); ?></span>
            </div>

            <div class="info-item">
                <span class="info-label">Mobile Number</span>
                <span class="info-value"><?php echo e($order->receiver_contact); ?></span>
            </div>

            <div class="info-item">
                <span class="info-label">Delivery / Pickup Schedule</span>
                <span class="info-value">
                    <?php echo e($order->delivery_datetime ? $order->delivery_datetime->format('M d, Y h:i A') : '—'); ?>

                </span>
            </div>

            <div class="info-item">
                <span class="info-label">Fulfillment</span>
                <span class="info-value"><?php echo e(ucfirst($order->fulfillment_type)); ?></span>
            </div>

            <?php if($order->fulfillment_type === 'delivery'): ?>
            <div class="info-item" style="grid-column: 1 / -1;">
                <span class="info-label">Delivery Address</span>
                <span class="info-value"><?php echo e($order->delivery_address ?: '—'); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="card">
        <h2 class="card-heading">Payment Information</h2>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">GCash Reference Number</span>
                <span class="info-value mono-soft"><?php echo e($order->payment_proof_reference ?: '—'); ?></span>
            </div>

            <div class="info-item">
                <span class="info-label">Delivery Fee</span>
                <span class="info-value">₱<?php echo e(number_format($order->delivery_fee, 2)); ?></span>
            </div>
        </div>
    </div>

    
    <div class="card">
        <h2 class="card-heading">Order Items</h2>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="num">Quantity</th>
                        <th class="num">Unit Price</th>
                        <th class="num">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <?php if($item->product): ?>
                            <div style="font-weight: 600; color: #212121;">
                                <?php echo e($item->product->display_name); ?>

                            </div>
                            <?php else: ?>
                            <div style="font-weight: 600; color: #212121; font-style: italic;">
                                Custom Bouquet
                            </div>
                            <?php if($item->customization_details): ?>
                            <div style="font-size: 12px; color: #64748B; margin-top: 3px; line-height: 1.4;">
                                <?php echo e($item->customization_details); ?>

                            </div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </td>

                        <td class="num" style="color: #212121;">
                            <?php echo e((float) $item->quantity); ?>

                        </td>

                        <td class="num" style="color: #64748B;">
                            ₱<?php echo e(number_format($item->unit_price, 2)); ?>

                        </td>

                        <td class="num" style="font-weight: 700; color: #2E5A3B;">
                            ₱<?php echo e(number_format($item->line_total, 2)); ?>

                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php if($order->discount_type && $order->discount_type !== 'none'): ?>
        <div class="discount-info">
            <div class="discount-info-title">
                <?php echo e($order->discount_type === 'pwd' ? 'PWD Discount Applied' : 'Senior Citizen Discount Applied'); ?>

            </div>
            <div class="discount-info-row">
                <span>Name:</span>
                <span><?php echo e($order->discount_name ?: '—'); ?></span>
            </div>
            <div class="discount-info-row">
                <span>ID No:</span>
                <span><?php echo e($order->discount_id_number ?: '—'); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="order-summary">
            <div class="summary-line">
                <span>Subtotal</span>
                <strong>₱<?php echo e(number_format($order->subtotal, 2)); ?></strong>
            </div>

            <?php if($order->discount_amount > 0): ?>
            <div class="summary-line discount-line">
                <span>Discount (20%)</span>
                <strong>− ₱<?php echo e(number_format($order->discount_amount, 2)); ?></strong>
            </div>
            <?php endif; ?>

            <?php if($order->fulfillment_type === 'delivery' && $order->delivery_fee > 0): ?>
            <div class="summary-line">
                <span>Delivery Fee</span>
                <strong>₱<?php echo e(number_format($order->delivery_fee, 2)); ?></strong>
            </div>
            <?php endif; ?>

            <div class="summary-line total">
                <span>Total</span>
                <strong>₱<?php echo e(number_format($order->total_amount, 2)); ?></strong>
            </div>
        </div>
    </div>

</div>

<style>
    .form-page-wide {
        max-width: 900px;
        margin: 0 auto;
    }

    .card {
        margin-bottom: 20px;
    }

    .ref-badge {
        font-family: 'SF Mono', Consolas, monospace;
        font-size: 15px;
        color: #94A3B8;
        font-weight: 600;
        margin-left: 8px;
        vertical-align: middle;
    }

    .card-heading {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 18px;
        padding-bottom: 8px;
        border-bottom: 2px solid #FCE4EC;
        display: inline-block;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px 24px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding-bottom: 14px;
        border-bottom: 1px dashed #F0E6DD;
    }

    .info-label {
        font-size: 11px;
        font-weight: 600;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .info-value {
        font-size: 14px;
        color: #212121;
        font-weight: 500;
        word-break: break-word;
    }

    .info-value.mono {
        font-family: 'SF Mono', Consolas, monospace;
        font-weight: 700;
        color: #E85D75;
        letter-spacing: 0.5px;
    }

    .info-value.mono-soft {
        font-family: 'SF Mono', Consolas, monospace;
        font-weight: 700;
        color: #212121;
        letter-spacing: 0.5px;
    }

    .status-text {
        font-size: 14px;
        font-weight: 700;
        text-transform: capitalize;
        cursor: default;
        user-select: none;
    }

    .status-pending {
        color: #64748B;
    }

    .status-confirmed {
        color: #E85D75;
    }

    .status-preparing {
        color: #B8860B;
    }

    .status-ready {
        color: #D14A62;
    }

    .status-completed {
        color: #2E5A3B;
    }

    .status-cancelled {
        color: #DC3545;
    }

    .status-update {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #F0E6DD;
    }

    .status-form-row {
        display: flex;
        gap: 10px;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .discount-info {
        margin-top: 20px;
        padding: 14px 16px;
        background: #FFF8E1;
        border-left: 4px solid #D4AF37;
        border-radius: 8px;
    }

    .discount-info-title {
        font-size: 11px;
        font-weight: 700;
        color: #B8860B;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .discount-info-row {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        font-size: 13px;
    }

    .discount-info-row span:first-child {
        color: #94A3B8;
    }

    .discount-info-row span:last-child {
        color: #212121;
        font-weight: 600;
        text-align: right;
        max-width: 65%;
        word-break: break-word;
    }

    .order-summary {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 2px solid #F8BBD0;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .summary-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        max-width: 320px;
        padding: 6px 0;
        font-size: 14px;
        color: #64748B;
    }

    .summary-line strong {
        font-size: 15px;
        color: #212121;
        font-weight: 600;
    }

    .summary-line.discount-line span,
    .summary-line.discount-line strong {
        color: #B8860B;
        font-weight: 600;
    }

    .summary-line.total {
        margin-top: 10px;
        padding-top: 14px;
        border-top: 1px dashed #F0E6DD;
    }

    .summary-line.total span {
        font-size: 15px;
        font-weight: 700;
        color: #212121;
    }

    .summary-line.total strong {
        font-size: 24px;
        color: #E85D75;
        font-weight: 700;
    }

    .locked-note {
        padding: 14px 18px;
        background: #F1F5F9;
        color: #64748B;
        font-size: 13px;
        border-radius: 10px;
        text-align: center;
    }

    .locked-note strong {
        text-transform: capitalize;
        color: #212121;
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/orders/show.blade.php ENDPATH**/ ?>