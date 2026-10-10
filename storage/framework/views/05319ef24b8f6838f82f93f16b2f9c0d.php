

<?php $__env->startSection('title', 'Audit Trail'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Audit Trail</h1>
        <p>Review important changes made to the system.</p>
    </div>
</div>

<div class="card">

    <?php if($logs->isEmpty()): ?>

    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
        <h3>No audit records found</h3>
        <p>System activity will appear here as users make changes.</p>
    </div>

    <?php else: ?>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Date &amp; Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Table</th>
                    <th>Record</th>
                    <th>Details</th>
                </tr>
            </thead>

            <tbody>
                <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="color: #64748B; white-space: nowrap;">
                        <?php echo e($log->action_timestamp?->format('M d, Y h:i A')); ?>

                    </td>

                    <td style="font-weight: 600; color: #212121;">
                        <?php echo e($log->user?->full_name ?? 'Unknown'); ?>

                    </td>

                    <td>
                        <span class="action-tag action-<?php echo e(strtolower($log->action_type)); ?>">
                            <?php echo e(ucfirst($log->action_type)); ?>

                        </span>
                    </td>

                    <td style="color: #64748B;">
                        <?php echo e($log->table_affected); ?>

                    </td>

                    <td style="color: #94A3B8; font-size: 12px;">
                        #<?php echo e($log->record_id); ?>

                    </td>

                    <td style="color: #64748B; max-width: 320px;">
                        <?php echo e($log->details ?? '—'); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <?php endif; ?>

</div>

<style>
    /* Action tag — plain colored text, non-clickable */
    .action-tag {
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        cursor: default;
        user-select: none;
    }

    .action-create {
        color: #2E5A3B;
    }

    .action-created {
        color: #2E5A3B;
    }

    .action-update {
        color: #B8860B;
    }

    .action-updated {
        color: #B8860B;
    }

    .action-delete {
        color: #DC3545;
    }

    .action-deleted {
        color: #DC3545;
    }

    .action-login {
        color: #6B5B95;
    }

    .action-logout {
        color: #64748B;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748B;
    }

    .empty-state svg {
        color: #D4AF37;
        margin-bottom: 16px;
        opacity: 0.6;
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
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/audit/index.blade.php ENDPATH**/ ?>