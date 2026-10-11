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

    <style>
        .clean-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-top: 16px;
            margin-top: 20px;
            border-top: 1px solid #F0E6DD;
            flex-wrap: wrap;
            font-family: 'Instrument Sans', Arial, sans-serif;
        }

        .clean-pagination .pagination-info {
            font-size: 13px;
            color: #64748B;
            font-weight: 400;
            line-height: 1.4;
        }

        .clean-pagination .pagination-info strong {
            color: #212121;
            font-weight: 700;
            font-size: 13px;
        }

        .clean-pagination .pagination-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .clean-pagination .page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 14px;
            border-radius: 6px;
            background: #FFFFFF;
            border: 1.5px solid #F0E6DD;
            color: #64748B;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Instrument Sans', Arial, sans-serif;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
            line-height: 1;
        }

        .clean-pagination .page-btn:hover:not(:disabled) {
            border-color: #6B5B95;
            color: #6B5B95;
            background: #FDFBFF;
        }

        .clean-pagination .page-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .clean-pagination .page-indicator {
            font-size: 13px;
            font-weight: 700;
            color: #212121;
            padding: 0 8px;
            min-width: 50px;
            text-align: center;
            font-variant-numeric: tabular-nums;
        }

        @media (max-width: 640px) {
            .clean-pagination {
                justify-content: center;
            }
            .clean-pagination .pagination-info {
                width: 100%;
                text-align: center;
            }
        }
    </style>
<?php endif; ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/partials/pagination.blade.php ENDPATH**/ ?>