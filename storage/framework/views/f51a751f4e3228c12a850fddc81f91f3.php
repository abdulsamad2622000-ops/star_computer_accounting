<?php $__env->startSection('title', 'My Profile'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="form-card">
            <h5 class="fw-bold mb-4">
                <i class="bi bi-person-circle me-2"></i>My Profile
            </h5>

            <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div><?php echo e($e); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('profile.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name"
                           class="form-control"
                           value="<?php echo e($user->name); ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email"
                           class="form-control"
                           value="<?php echo e($user->email); ?>"
                           readonly
                           style="background:#f4f6fb">
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <input type="text"
                           class="form-control"
                           value="<?php echo e(ucfirst($user->role)); ?>"
                           readonly
                           style="background:#f4f6fb">
                </div>

                <hr>
                <p class="fw-bold text-muted" style="font-size:.85rem">
                    Password Change
                </p>

                <div class="mb-3">
                    <label class="form-label">Current Password *</label>
                    <input type="password"
                           name="current_password"
                           class="form-control"
                           placeholder="Purana password..."
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password"
                           name="new_password"
                           class="form-control"
                           placeholder="Naya password (optional)..."
                           minlength="6">
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password"
                           name="new_password_confirmation"
                           class="form-control"
                           placeholder="Dobara naya password...">
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check-lg me-1"></i> Update Profile
                </button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\star_computer_accounting\resources\views/profile/edit.blade.php ENDPATH**/ ?>