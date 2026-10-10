

<?php $__env->startSection('title', 'Edit User'); ?>

<?php $__env->startSection('content'); ?>

<div class="form-page">

    <div class="page-header">
        <div>
            <h1>Edit User</h1>
            <p>Update the account for <strong style="color: #6B5B95;"><?php echo e($user->full_name); ?></strong>.</p>
        </div>
        <a href="<?php echo e(route('users.index')); ?>" class="btn btn-secondary">← Back</a>
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
        <form action="<?php echo e(route('users.update', $user)); ?>" method="POST" id="user-form">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="form-grid">

                <div class="form-group full-width">
                    <label>Role <span class="req">*</span></label>
                    <div class="role-radios">
                        <label class="role-radio">
                            <input type="radio" name="role" value="staff"
                                   <?php echo e(old('role', $user->role) === 'staff' ? 'checked' : ''); ?>>
                            <div>
                                <strong>Staff</strong>
                                <span>Can record sales and view inventory</span>
                            </div>
                        </label>
                        <label class="role-radio">
                            <input type="radio" name="role" value="owner"
                                   <?php echo e(old('role', $user->role) === 'owner' ? 'checked' : ''); ?>>
                            <div>
                                <strong>Owner</strong>
                                <span>Full access to all features</span>
                            </div>
                        </label>
                    </div>
                    <?php if($user->user_id === auth()->id()): ?>
                        <small class="field-help" style="color: #B8860B;">
                            You are editing your own account. You cannot change your role if you are the last active owner.
                        </small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="first_name">First Name <span class="req">*</span></label>
                    <input type="text" id="first_name" name="first_name" class="form-control"
                           value="<?php echo e(old('first_name', $user->first_name)); ?>" required>
                </div>

                <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" class="form-control"
                           value="<?php echo e(old('middle_name', $user->middle_name)); ?>">
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name <span class="req">*</span></label>
                    <input type="text" id="last_name" name="last_name" class="form-control"
                           value="<?php echo e(old('last_name', $user->last_name)); ?>" required>
                </div>

                <div class="form-group full-width">
                    <label for="username">Username <span class="req">*</span></label>
                    <input type="text" id="username" name="username" class="form-control"
                           value="<?php echo e(old('username', $user->username)); ?>" required autocomplete="off">
                    <small class="field-help">Used to log in. Must be unique.</small>
                </div>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" class="form-control"
                           autocomplete="new-password">
                    <small class="field-help">Leave blank to keep the current password.</small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control" autocomplete="new-password">
                </div>

            </div>

            <div class="form-actions">
                <a href="<?php echo e(route('users.index')); ?>" class="action-btn-secondary">Cancel</a>
                <button type="submit" class="action-btn-primary">Update User</button>
            </div>
        </form>
    </div>

</div>


<div class="modal-overlay" id="confirm-modal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Confirm Update</h3>
            <p class="modal-subtitle">Review the changes before saving.</p>
        </div>
        <div class="modal-body" id="modal-body"></div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeConfirmModal()">Cancel</button>
            <button type="button" class="modal-btn modal-btn-primary" id="modal-confirm-btn">Update User</button>
        </div>
    </div>
</div>


<div class="modal-overlay" id="error-modal">
    <div class="modal-box modal-box-error">
        <div class="modal-error-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h3 class="modal-error-title">Something's not right</h3>
        <p class="modal-error-message" id="error-modal-message"></p>
        <div class="modal-error-footer">
            <button type="button" class="modal-btn modal-btn-primary" onclick="closeErrorModal()">Got it</button>
        </div>
    </div>
</div>

<style>
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px 24px;
        margin-bottom: 24px;
    }
    .form-grid > .form-group.full-width { grid-column: 1 / -1; }

    .req { color: #6B5B95; margin-left: 2px; }

    .field-help {
        display: block;
        margin-top: 6px;
        color: #94A3B8;
        font-size: 12px;
    }

    .role-radios {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 10px;
    }
    .role-radio {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        border: 1.5px solid #F0E6DD;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #FFFFFF;
    }
    .role-radio:hover { border-color: #D5C9E8; background: #FEFCF9; }
    .role-radio:has(input[type="radio"]:checked) {
        border-color: #6B5B95;
        background: #EFEBF7;
    }
    .role-radio input[type="radio"] {
        margin-top: 2px;
        accent-color: #6B5B95;
        cursor: pointer;
    }
    .role-radio strong {
        display: block;
        font-size: 14px;
        color: #212121;
        margin-bottom: 2px;
    }
    .role-radio span {
        font-size: 12px;
        color: #64748B;
        line-height: 1.4;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 20px;
        border-top: 1px solid #F0E6DD;
        align-items: center;
    }
    .form-actions .action-btn-primary,
    .form-actions .action-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        height: 44px;
        min-height: 44px;
        min-width: 140px;
        padding: 0 22px;
        font-size: 14px;
        font-weight: 600;
        line-height: 1;
        border-radius: 8px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .action-btn-primary {
        background: #6B5B95;
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(107, 91, 149, 0.3);
    }
    .action-btn-primary:hover { background: #594B7D; }
    .action-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
    .action-btn-secondary { background: #F0E6DD; color: #212121; }
    .action-btn-secondary:hover { background: #E5D5C5; }

    /* MODALS */
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(33, 33, 33, 0.55); backdrop-filter: blur(3px);
        z-index: 1000; align-items: center; justify-content: center;
        padding: 20px;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: #FFFFFF; border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        width: 100%; max-width: 480px; max-height: 90vh;
        display: flex; flex-direction: column; overflow: hidden;
    }
    .modal-header { padding: 22px 26px 16px; border-bottom: 1px solid #F0E6DD; }
    .modal-title { font-size: 18px; font-weight: 700; color: #212121; margin: 0; }
    .modal-subtitle { font-size: 13px; color: #64748B; margin-top: 4px; }
    .modal-body { padding: 20px 26px; overflow-y: auto; }
    .modal-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 8px 0; font-size: 14px;
        border-bottom: 1px dashed #F0E6DD; gap: 12px;
    }
    .modal-row:last-child { border-bottom: none; }
    .modal-row-label { color: #64748B; }
    .modal-row-value { color: #212121; font-weight: 600; text-align: right; }
    .modal-footer {
        padding: 16px 26px 22px; display: flex; gap: 10px;
        justify-content: flex-end; border-top: 1px solid #F0E6DD; background: #FEFCF9;
    }
    .modal-btn {
        padding: 11px 22px; border-radius: 8px; font-size: 14px;
        font-weight: 600; cursor: pointer; font-family: inherit;
        border: none; min-width: 120px;
    }
    .modal-btn-primary {
        background: #6B5B95; color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(107, 91, 149, 0.3);
    }
    .modal-btn-primary:hover { background: #594B7D; }
    .modal-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
    .modal-btn-secondary { background: #F0E6DD; color: #212121; }
    .modal-btn-secondary:hover { background: #E5D5C5; }

    .modal-box-error { max-width: 420px; text-align: center; padding: 30px 30px 24px; }
    .modal-error-icon {
        width: 64px; height: 64px; border-radius: 50%;
        background: #FDECEA; color: #DC3545;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;
    }
    .modal-error-title { font-size: 18px; font-weight: 700; color: #212121; margin-bottom: 8px; }
    .modal-error-message {
        font-size: 14px; color: #64748B; line-height: 1.5;
        margin-bottom: 24px; word-break: break-word; white-space: pre-line;
    }
    .modal-error-footer { display: flex; justify-content: center; }
    .modal-error-footer .modal-btn { min-width: 120px; }

    @media (max-width: 640px) {
        .modal-box { max-width: 100%; }
        .form-actions .action-btn-primary,
        .form-actions .action-btn-secondary { flex: 1; min-width: 0; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('user-form');
    const confirmModal = document.getElementById('confirm-modal');
    const confirmBody = document.getElementById('modal-body');
    const confirmBtn = document.getElementById('modal-confirm-btn');
    const errorModal = document.getElementById('error-modal');
    const errorMessage = document.getElementById('error-modal-message');

    window.closeConfirmModal = function () {
        confirmModal.classList.remove('open');
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Update User';
    };
    window.closeErrorModal = function () {
        errorModal.classList.remove('open');
    };
    function showError(msg) {
        errorMessage.textContent = msg;
        errorModal.classList.add('open');
    }

    confirmModal.addEventListener('click', e => { if (e.target === confirmModal) closeConfirmModal(); });
    errorModal.addEventListener('click', e => { if (e.target === errorModal) closeErrorModal(); });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeConfirmModal(); closeErrorModal(); }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const role = document.querySelector('input[name="role"]:checked')?.value;
        const first = document.getElementById('first_name').value.trim();
        const middle = document.getElementById('middle_name').value.trim();
        const last = document.getElementById('last_name').value.trim();
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirmation').value;

        if (!role) { showError('Please select a role.'); return; }
        if (!first) { showError('Please enter the first name.'); return; }
        if (!last) { showError('Please enter the last name.'); return; }
        if (!username) { showError('Please enter a username.'); return; }

        if (password.length > 0 && password.length < 6) {
            showError('New password must be at least 6 characters.');
            return;
        }
        if (password.length > 0 && password !== confirm) {
            showError('New passwords do not match.');
            return;
        }

        const fullName = [first, middle, last].filter(Boolean).join(' ');
        const roleLabel = role === 'owner' ? 'Owner' : 'Staff';

        let bodyHtml = `
            <div class="modal-row">
                <span class="modal-row-label">Role</span>
                <span class="modal-row-value">${roleLabel}</span>
            </div>
            <div class="modal-row">
                <span class="modal-row-label">Full Name</span>
                <span class="modal-row-value">${fullName}</span>
            </div>
            <div class="modal-row">
                <span class="modal-row-label">Username</span>
                <span class="modal-row-value">@${username}</span>
            </div>
        `;

        if (password.length > 0) {
            bodyHtml += `
                <div class="modal-row">
                    <span class="modal-row-label">Password</span>
                    <span class="modal-row-value" style="color: #B8860B;">Will be changed</span>
                </div>
            `;
        }

        confirmBody.innerHTML = bodyHtml;
        confirmModal.classList.add('open');
    });

    confirmBtn.addEventListener('click', function () {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Saving...';
        form.submit();
    });
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/users/edit.blade.php ENDPATH**/ ?>