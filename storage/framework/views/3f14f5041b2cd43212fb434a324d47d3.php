

<?php $__env->startSection('title', 'Backup & Recovery'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Backup &amp; Recovery</h1>
        <p>Download a complete snapshot of the shop's database.</p>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-error"><?php echo e(session('error')); ?></div>
<?php endif; ?>


<div class="card backup-info-card">
    <div class="backup-info-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
    </div>
    <div class="backup-info-content">
        <h2>How backups work</h2>
        <p>
            Clicking <strong>Download Backup</strong> generates a full <code>.sql</code> file containing every
            record in the system and sends it directly to your computer. The file is
            <strong>not stored on this server</strong> — that way if the machine ever fails, your backup is safe
            on your local drive.
        </p>
        <ul class="backup-info-list">
            <li>Save the file somewhere safe (external drive, cloud, email it to yourself).</li>
            <li>To restore, import the <code>.sql</code> file via phpMyAdmin.</li>
            <li>Back up regularly — the more recent the file, the less data you might lose.</li>
        </ul>
    </div>
</div>


<div class="card backup-download-card">
    <div class="backup-download-header">
        <div>
            <h2>Generate Backup</h2>
            <p>Produces a complete SQL dump of the current database.</p>
        </div>
    </div>

    <div class="backup-actions">
        <a href="<?php echo e(route('backup.download')); ?>" class="backup-download-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
            </svg>
            Download Backup
        </a>

        <?php if($lastBackup): ?>
            <div class="backup-last">
                <span class="backup-last-label">Last backup</span>
                <span class="backup-last-value">
                    <?php echo e(\Carbon\Carbon::parse($lastBackup['last_backup_at'])->format('M d, Y · h:i A')); ?>

                </span>
                <span class="backup-last-meta">
                    by <?php echo e($lastBackup['last_backup_by'] ?? 'Unknown'); ?>

                    · <?php echo e(number_format(($lastBackup['size_bytes'] ?? 0) / 1024, 1)); ?> KB
                </span>
            </div>
        <?php else: ?>
            <div class="backup-last">
                <span class="backup-last-label">Last backup</span>
                <span class="backup-last-value empty">No backups yet</span>
            </div>
        <?php endif; ?>
    </div>
</div>


<div class="card backup-restore-card">
    <h2>How to restore a backup</h2>
    <ol class="backup-restore-list">
        <li>Open <strong>phpMyAdmin</strong> and select the <code>lf_db</code> database.</li>
        <li>Click the <strong>Import</strong> tab.</li>
        <li>Choose the downloaded <code>.sql</code> file and click <strong>Import</strong>.</li>
        <li>Wait for the import to finish, then verify key data (users, products, sales).</li>
    </ol>
</div>

<style>
    .backup-info-card {
        display: flex;
        gap: 20px;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .backup-info-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: #EFEBF7;
        color: #6B5B95;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .backup-info-content h2 {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin: 0 0 8px;
    }

    .backup-info-content p {
        font-size: 13px;
        color: #64748B;
        line-height: 1.6;
        margin-bottom: 10px;
    }

    .backup-info-content code {
        font-family: 'SF Mono', Consolas, monospace;
        background: #F1F5F9;
        color: #212121;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 12px;
    }

    .backup-info-list {
        font-size: 13px;
        color: #64748B;
        line-height: 1.6;
        margin: 0;
        padding-left: 20px;
    }

    .backup-download-card { margin-bottom: 20px; }

    .backup-download-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .backup-download-header h2 {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin: 0 0 4px;
    }

    .backup-download-header p {
        font-size: 13px;
        color: #64748B;
        margin: 0;
    }

    .backup-actions {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .backup-download-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 26px;
        background: #6B5B95;
        color: #FFFFFF;
        text-decoration: none;
        font-size: 15px;
        font-weight: 600;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(107, 91, 149, 0.3);
        transition: all 0.15s ease;
        border: none;
        cursor: pointer;
        font-family: inherit;
    }

    .backup-download-btn:hover {
        background: #594B7D;
        box-shadow: 0 4px 14px rgba(107, 91, 149, 0.4);
        transform: translateY(-1px);
    }

    .backup-last {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .backup-last-label {
        font-size: 10px;
        font-weight: 700;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .backup-last-value {
        font-size: 14px;
        font-weight: 700;
        color: #212121;
    }
    .backup-last-value.empty {
        color: #94A3B8;
        font-style: italic;
        font-weight: 500;
    }

    .backup-last-meta {
        font-size: 12px;
        color: #94A3B8;
    }

    .backup-restore-card h2 {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin: 0 0 14px;
    }

    .backup-restore-list {
        font-size: 13px;
        color: #64748B;
        line-height: 1.8;
        margin: 0;
        padding-left: 22px;
    }

    .backup-restore-list code {
        font-family: 'SF Mono', Consolas, monospace;
        background: #F1F5F9;
        color: #212121;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 12px;
    }

    @media (max-width: 640px) {
        .backup-info-card { flex-direction: column; }
        .backup-actions { flex-direction: column; align-items: stretch; }
        .backup-download-btn { justify-content: center; }
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/backup/index.blade.php ENDPATH**/ ?>