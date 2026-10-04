

<?php $__env->startSection('title', 'Edit Customer'); ?>

<?php
    $backUrl = request('from') === 'records'
        ? route('records.index', ['tab' => 'customers'])
        : route('customers.index');
?>

<?php $__env->startSection('content'); ?>

<div style="max-width: 720px; margin: 0 auto;">

    <div class="page-header">
        <div>
            <h1>Edit Customer</h1>
            <p>Update info for <strong style="color: #E85D75;"><?php echo e($customer->full_name); ?></strong>.</p>
        </div>

        <a href="<?php echo e($backUrl); ?>" class="btn btn-secondary">← Back</a>
    </div>

    <?php if($errors->any()): ?>
        <div class="alert alert-error">
            <ul style="margin-left: 20px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="<?php echo e(route('customers.update', $customer)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <?php if(request('from') === 'records'): ?>
                <input type="hidden" name="from" value="records">
            <?php endif; ?>

            <div class="form-grid">
                <div class="form-group">
                    <label for="first_name">First Name <span class="req">*</span></label>
                    <input type="text" id="first_name" name="first_name" class="form-control"
                           value="<?php echo e(old('first_name', $customer->first_name)); ?>" required>
                    <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" class="form-control"
                           value="<?php echo e(old('middle_name', $customer->middle_name)); ?>">
                    <?php $__errorArgs = ['middle_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name <span class="req">*</span></label>
                    <input type="text" id="last_name" name="last_name" class="form-control"
                           value="<?php echo e(old('last_name', $customer->last_name)); ?>" required>
                    <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="contact_number">Contact Number <span class="req">*</span></label>
                    <input type="text" id="contact_number" name="contact_number" class="form-control"
                           value="<?php echo e(old('contact_number', $customer->contact_number)); ?>" required>
                    <?php $__errorArgs = ['contact_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" class="form-control"
                           value="<?php echo e(old('address', $customer->address)); ?>">
                    <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            
            <div class="form-group">
                <label class="toggle-option" for="is_regular">
                    <div class="toggle-info">
                        <div class="toggle-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <div class="toggle-text">
                            <div class="toggle-title">Regular Customer</div>
                            <div class="toggle-desc">Mark this customer as a regular</div>
                        </div>
                    </div>
                    <span class="toggle-switch">
                        <input type="hidden" name="is_regular" value="0">
                        <input type="checkbox" id="is_regular" name="is_regular" value="1"
                               <?php echo e(old('is_regular', $customer->is_regular) ? 'checked' : ''); ?>>
                        <span class="toggle-slider"></span>
                    </span>
                </label>
            </div>

            <div class="form-actions">
                <a href="<?php echo e($backUrl); ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Customer</button>
            </div>
        </form>
    </div>

</div>

<style>
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px 24px;
        margin-bottom: 20px;
    }

    .req { color: #E85D75; margin-left: 2px; }

    /* Toggle switch */
    .toggle-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border: 1.5px solid #F0E6DD;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #FFFFFF;
        margin-bottom: 20px;
    }
    .toggle-option:hover { border-color: #F8BBD0; background: #FEFCF9; }
    .toggle-option:has(input[type="checkbox"]:checked) {
        border-color: #E85D75;
        background: #FCE4EC;
    }

    .toggle-info {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .toggle-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #FCE4EC;
        color: #E85D75;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.15s ease;
    }
    .toggle-option:has(input[type="checkbox"]:checked) .toggle-icon {
        background: #E85D75;
        color: #FFFFFF;
    }

    .toggle-text { min-width: 0; }
    .toggle-title { font-size: 14px; font-weight: 700; color: #212121; }
    .toggle-desc { font-size: 12px; color: #94A3B8; margin-top: 2px; }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
        margin-left: auto;
    }
    .toggle-switch input[type="hidden"] { display: none; }
    .toggle-switch input[type="checkbox"] {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #E2E8F0;
        transition: 0.2s;
        border-radius: 24px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.2s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
    }
    .toggle-switch input[type="checkbox"]:checked + .toggle-slider {
        background-color: #E85D75;
    }
    .toggle-switch input[type="checkbox"]:checked + .toggle-slider:before {
        transform: translateX(20px);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 20px;
        border-top: 1px solid #F0E6DD;
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/customers/edit.blade.php ENDPATH**/ ?>