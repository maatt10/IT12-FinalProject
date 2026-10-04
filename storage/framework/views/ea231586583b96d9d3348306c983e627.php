

<?php $__env->startSection('title', 'POS'); ?>

<?php $__env->startSection('content'); ?>

<div class="pos-hub-wrapper">

    <div class="pos-hub-header">
        <h1>POS</h1>
        <p>Select the type of transaction to record.</p>
    </div>

    <div class="pos-hub">

        <a href="<?php echo e(route('sales.create')); ?>" class="pos-hub-card">
            <div class="pos-hub-icon" style="background: #FCE4EC; color: #E85D75;">
                <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h3>Walk-in Sale</h3>
            <p>Record a purchase made by a customer in the shop.</p>
            <span class="pos-hub-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        </a>

        <a href="<?php echo e(route('orders.create')); ?>" class="pos-hub-card">
            <div class="pos-hub-icon" style="background: #E8F5E9; color: #2E5A3B;">
                <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h3>Online Order</h3>
            <p>Record a bouquet order received through Messenger.</p>
            <span class="pos-hub-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        </a>

    </div>

</div>

<style>
    .pos-hub-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 180px);
        padding: 20px;
    }

    .pos-hub-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .pos-hub-header h1 {
        font-size: 32px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 6px;
    }

    .pos-hub-header p {
        font-size: 15px;
        color: #64748B;
    }

    .pos-hub {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
        max-width: 900px;
        width: 100%;
    }

    .pos-hub-card {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 48px 32px;
        background: #FFFFFF;
        border: 1.5px solid #F0E6DD;
        border-radius: 20px;
        text-decoration: none;
        transition: all 0.25s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .pos-hub-card:hover {
        border-color: #E85D75;
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(232, 93, 117, 0.18);
    }

    .pos-hub-icon {
        width: 88px;
        height: 88px;
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-bottom: 24px;
        transition: transform 0.25s ease;
    }

    .pos-hub-card:hover .pos-hub-icon {
        transform: scale(1.08);
    }

    .pos-hub-card h3 {
        font-size: 22px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 10px;
    }

    .pos-hub-card p {
        font-size: 14px;
        color: #64748B;
        line-height: 1.5;
        max-width: 260px;
    }

    .pos-hub-arrow {
        position: absolute;
        top: 24px;
        right: 24px;
        color: #CBD5E1;
        transition: all 0.2s ease;
    }

    .pos-hub-card:hover .pos-hub-arrow {
        color: #E85D75;
        transform: translateX(3px);
    }

    @media (max-width: 700px) {
        .pos-hub {
            grid-template-columns: 1fr;
            max-width: 400px;
        }
        .pos-hub-card {
            padding: 36px 24px;
        }
        .pos-hub-icon {
            width: 72px;
            height: 72px;
        }
        .pos-hub-icon svg {
            width: 34px;
            height: 34px;
        }
        .pos-hub-header h1 {
            font-size: 26px;
        }
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/pos/index.blade.php ENDPATH**/ ?>