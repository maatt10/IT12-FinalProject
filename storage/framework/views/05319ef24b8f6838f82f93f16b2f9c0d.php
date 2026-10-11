

<?php $__env->startSection('title', 'Audit Trail'); ?>

<?php
    $anyFilter = !empty($search) || !empty($from) || !empty($to) || !empty($action);

    $buildUrl = function (array $overrides = []) use ($search, $from, $to, $action) {
        $defaults = [
            'search' => $search,
            'from' => $from,
            'to' => $to,
            'action' => $action,
        ];
        $merged = array_merge($defaults, $overrides);

        $params = [];
        foreach ($merged as $k => $v) {
            if ($v !== null && $v !== '') $params[$k] = $v;
        }
        return route('audit.index', $params);
    };

    $toggleAction = fn($value) => $buildUrl(['action' => $action === $value ? '' : $value]);
?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Audit Trail</h1>
        <p>Review important changes made to the system.</p>
    </div>
</div>


<form method="GET" action="<?php echo e(route('audit.index')); ?>" class="pos-search">
    <?php if($action): ?> <input type="hidden" name="action" value="<?php echo e($action); ?>"> <?php endif; ?>

    <div class="pos-search-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>
    <input type="text" name="search" id="audit-search" class="pos-search-input"
           placeholder="Search user, table, or details…"
           value="<?php echo e($search); ?>"
           autocomplete="off">
</form>


<form method="GET" action="<?php echo e(route('audit.index')); ?>" class="filter-row">
    <?php if($search): ?> <input type="hidden" name="search" value="<?php echo e($search); ?>"> <?php endif; ?>

    <span class="filter-label">Filters</span>

    <a href="<?php echo e($buildUrl(['action' => ''])); ?>" class="filter-chip <?php echo e(empty($action) ? 'active' : ''); ?>">All</a>
    <a href="<?php echo e($toggleAction('create')); ?>" class="filter-chip <?php echo e($action === 'create' ? 'active' : ''); ?>">Create</a>
    <a href="<?php echo e($toggleAction('update')); ?>" class="filter-chip <?php echo e($action === 'update' ? 'active' : ''); ?>">Update</a>
    <a href="<?php echo e($toggleAction('delete')); ?>" class="filter-chip <?php echo e($action === 'delete' ? 'active' : ''); ?>">Delete</a>

    <div class="date-range">
        <label>From</label>
        <input type="date" name="from" class="date-input" value="<?php echo e($from); ?>">
        <label>To</label>
        <input type="date" name="to" class="date-input" value="<?php echo e($to); ?>">
    </div>

    <?php if($anyFilter): ?>
        <a href="<?php echo e(route('audit.index')); ?>" class="filter-clear" title="Clear all filters">×</a>
    <?php endif; ?>
</form>

<div class="card">

    <?php if($logs->isEmpty()): ?>

        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <h3><?php echo e($anyFilter ? 'No matching audit records' : 'No audit records found'); ?></h3>
            <p><?php echo e($anyFilter ? 'Try changing your filters.' : 'System activity will appear here as users make changes.'); ?></p>
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
                            <td class="muted-cell nowrap"><?php echo e($log->action_timestamp?->format('M d, Y h:i A')); ?></td>
                            <td style="font-weight: 600; color: #212121;"><?php echo e($log->user?->full_name ?? 'Unknown'); ?></td>
                            <td>
                                <span class="action-tag action-<?php echo e(strtolower($log->action_type)); ?>">
                                    <?php echo e(ucfirst($log->action_type)); ?>

                                </span>
                            </td>
                            <td class="muted-cell"><?php echo e($log->table_affected); ?></td>
                            <td class="muted-sub">#<?php echo e($log->record_id); ?></td>
                            <td class="muted-cell details-cell"><?php echo e($log->details ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php echo $__env->make('partials.pagination', ['paginator' => $logs], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php endif; ?>

</div>

<style>
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

    /* FILTER ROW */
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
    .filter-clear:hover { background: #F8D7DA; color: #B02A37; }

    @media (max-width: 768px) {
        .date-range { margin-left: 0; width: 100%; }
        .date-input { flex: 1; }
    }

    /* CELLS */
    .muted-cell { color: #64748B; }
    .muted-sub { color: #94A3B8; font-size: 12px; }
    .nowrap { white-space: nowrap; }
    .details-cell { max-width: 320px; }

    /* ACTION TAG */
    .action-tag {
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        cursor: default;
        user-select: none;
    }
    .action-create { color: #2E5A3B; }
    .action-update { color: #B8860B; }
    .action-delete { color: #DC3545; }

    /* EMPTY */
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
    .empty-state p { font-size: 14px; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('audit-search');
        if (!searchInput) return;

        let timeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            if (this.value === <?php echo json_encode($search, 15, 512) ?>) return;
            timeout = setTimeout(() => this.form.submit(), 500);
        });
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                clearTimeout(timeout);
                this.form.submit();
            }
        });

        document.querySelectorAll('.date-input').forEach(input => {
            input.addEventListener('change', function () {
                this.form.submit();
            });
        });
    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/audit/index.blade.php ENDPATH**/ ?>