<?php if($paginator->hasPages() || $paginator->total() > 0): ?>
    <div class="clean-pagination">
        <div class="pagination-info">
            Showing <strong><?php echo e($paginator->firstItem() ?? 0); ?></strong>–<strong><?php echo e($paginator->lastItem() ?? 0); ?></strong>
            of <strong><?php echo e($paginator->total()); ?></strong>
            <?php echo e($paginator->total() === 1 ? 'entry' : 'entries'); ?>

        </div>

        <?php if($paginator->hasPages()): ?>
            <div class="pagination-controls">
                
                <?php if($paginator->onFirstPage()): ?>
                    <button type="button" class="page-btn" disabled>‹ Prev</button>
                <?php else: ?>
                    <a href="<?php echo e($paginator->previousPageUrl()); ?>" class="page-btn">‹ Prev</a>
                <?php endif; ?>

                <span class="page-indicator">
                    <?php echo e($paginator->currentPage()); ?> / <?php echo e($paginator->lastPage()); ?>

                </span>

                
                <?php if($paginator->hasMorePages()): ?>
                    <a href="<?php echo e($paginator->nextPageUrl()); ?>" class="page-btn">Next ›</a>
                <?php else: ?>
                    <button type="button" class="page-btn" disabled>Next ›</button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/partials/pagination.blade.php ENDPATH**/ ?>