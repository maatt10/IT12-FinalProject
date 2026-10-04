

<?php $__env->startSection('title', 'Records'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <h1>Records</h1>
        <p>View transaction records and history.</p>
    </div>
</div>


<div class="record-grid">

    <a href="<?php echo e(route('reports.sales')); ?>" class="record-card">
        <div class="record-icon" style="background: #FCE4EC; color: #E85D75;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h3>Sales Records</h3>
        <p>All completed sales transactions.</p>
    </a>

    <a href="<?php echo e(route('orders.index')); ?>" class="record-card">
        <div class="record-icon" style="background: #E8F5E9; color: #2E5A3B;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <h3>Order Records</h3>
        <p>All recorded online bouquet orders.</p>
    </a>

    <a href="<?php echo e(route('purchases.index')); ?>" class="record-card">
        <div class="record-icon" style="background: #FFF8E1; color: #B8860B;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
        </div>
        <h3>Purchase Records</h3>
        <p>All recorded stock-in transactions.</p>
    </a>

    <a href="<?php echo e(route('production.index')); ?>" class="record-card">
        <div class="record-icon" style="background: #F1F5F9; color: #475569;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
        </div>
        <h3>Production Records</h3>
        <p>All recorded production activity.</p>
    </a>

    <a href="<?php echo e(route('customers.index')); ?>" class="record-card">
        <div class="record-icon" style="background: #FCE4EC; color: #D14A62;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <h3>Customer Records</h3>
        <p>All registered customers.</p>
    </a>

</div>

<style>
    .record-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
    }

    .record-card {
        display: block;
        padding: 22px;
        background: #FFFFFF;
        border: 1.5px solid #F0E6DD;
        border-radius: 14px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .record-card:hover {
        border-color: #E85D75;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(232, 93, 117, 0.12);
    }

    .record-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
    }

    .record-card h3 {
        font-size: 15px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 4px;
    }

    .record-card p {
        font-size: 13px;
        color: #64748B;
        line-height: 1.4;
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/records/index.blade.php ENDPATH**/ ?>