<?php $__env->startSection('title', 'Staff Permissions'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .perm-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        margin-bottom: 16px;
        overflow: hidden;
    }
    .perm-card-header {
        background: #f0f6ff;
        padding: 10px 16px;
        font-weight: 700;
        color: #163a6f;
        font-size: 13px;
        border-bottom: 1px solid #d1dff5;
    }
    .perm-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #374151;
    }
    .perm-item:last-child { border-bottom: none; }
    .perm-desc { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .form-check-input { width: 40px !important; height: 22px; cursor: pointer; }
    .form-check-input:checked { background-color: #163a6f; border-color: #163a6f; }
    .sub-item { padding-left: 32px; background: #fafbff; }
    .staff-select-bar {
        background: #f0f6ff;
        border: 1px solid #d1dff5;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="form-card">
            <h5 class="fw-bold mb-1">
                <i class="bi bi-shield-lock me-2"></i>Staff Permissions
            </h5>
            <p class="text-muted mb-4" style="font-size:13px">
                Pehle staff member select karo phir permissions set karo.
            </p>

            <?php if($staffList->isEmpty()): ?>
            <div class="alert alert-warning">
                Koi staff member nahi mila!
                <a href="<?php echo e(route('staff.create')); ?>" class="btn btn-sm btn-primary ms-2">
                    + Add Staff
                </a>
            </div>
            <?php else: ?>

            <!-- Staff Select -->
            <div class="staff-select-bar">
                <label class="fw-bold" style="color:#163a6f;white-space:nowrap">
                    <i class="bi bi-person-gear me-1"></i> Select Staff:
                </label>
                <select class="form-select"
                        id="staffSelect"
                        onchange="loadStaff(this.value)"
                        style="max-width:300px">
                    <?php $__currentLoopData = $staffList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($staff->id); ?>"
                        <?php echo e($selectedId == $staff->id ? 'selected' : ''); ?>>
                        <?php echo e($staff->name); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($selectedId): ?>
                <span class="badge bg-primary">
                    <?php echo e($staffList->find($selectedId)?->name); ?>

                </span>
                <?php endif; ?>
            </div>

            <?php if($permissions): ?>
            <form action="<?php echo e(route('settings.permissions.update')); ?>"
                  method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <input type="hidden" name="user_id" value="<?php echo e($selectedId); ?>">

                <!-- Sale Point -->
                <div class="perm-card">
                    <div class="perm-card-header">🛒 Sale Point</div>
                    <div class="perm-item">
                        <div>
                            <div>Access</div>
                            <div class="perm-desc">Sale Point open kar sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="sale_access"
                                   <?php echo e($permissions->sale_access ? 'checked' : ''); ?>>
                        </div>
                    </div>
                    <div class="perm-item sub-item">
                        <div>
                            <div>View History</div>
                            <div class="perm-desc">Sale history dekh sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="sale_history"
                                   <?php echo e($permissions->sale_history ? 'checked' : ''); ?>>
                        </div>
                    </div>
                    <div class="perm-item sub-item">
                        <div>
                            <div>Return</div>
                            <div class="perm-desc">Sale return kar sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="sale_return"
                                   <?php echo e($permissions->sale_return ? 'checked' : ''); ?>>
                        </div>
                    </div>
                </div>

                <!-- Purchase Point -->
                <div class="perm-card">
                    <div class="perm-card-header">📦 Purchase Point</div>
                    <div class="perm-item">
                        <div>
                            <div>Access</div>
                            <div class="perm-desc">Purchase Point open kar sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="purchase_access"
                                   <?php echo e($permissions->purchase_access ? 'checked' : ''); ?>>
                        </div>
                    </div>
                    <div class="perm-item sub-item">
                        <div>
                            <div>View History</div>
                            <div class="perm-desc">Purchase history dekh sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="purchase_history"
                                   <?php echo e($permissions->purchase_history ? 'checked' : ''); ?>>
                        </div>
                    </div>
                    <div class="perm-item sub-item">
                        <div>
                            <div>Return</div>
                            <div class="perm-desc">Purchase return kar sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="purchase_return"
                                   <?php echo e($permissions->purchase_return ? 'checked' : ''); ?>>
                        </div>
                    </div>
                    <div class="perm-item sub-item">
                        <div>
                            <div>View Prices</div>
                            <div class="perm-desc">Purchase Price aur Sale Price dekh sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="purchase_price"
                                   <?php echo e($permissions->purchase_price ? 'checked' : ''); ?>>
                        </div>
                    </div>
                    <div class="perm-item sub-item">
                        <div>
                            <div>Rate Edit</div>
                            <div class="perm-desc">Purchase rates edit kar sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="purchase_rate_edit"
                                   <?php echo e($permissions->purchase_rate_edit ? 'checked' : ''); ?>>
                        </div>
                    </div>
                </div>

                <!-- Customers -->
                <div class="perm-card">
                    <div class="perm-card-header">👥 Customers</div>
                    <div class="perm-item">
                        <div>
                            <div>Access</div>
                            <div class="perm-desc">Customer list dekh sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="customer_access"
                                   <?php echo e($permissions->customer_access ? 'checked' : ''); ?>>
                        </div>
                    </div>
                    <div class="perm-item sub-item">
                        <div>
                            <div>View Ledger</div>
                            <div class="perm-desc">Customer ledger dekh sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="customer_ledger"
                                   <?php echo e($permissions->customer_ledger ? 'checked' : ''); ?>>
                        </div>
                    </div>
                    <div class="perm-item sub-item">
                        <div>
                            <div>Receive Payment</div>
                            <div class="perm-desc">Customer payment receive kar sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="customer_payment"
                                   <?php echo e($permissions->customer_payment ? 'checked' : ''); ?>>
                        </div>
                    </div>
                </div>

                <!-- Vendors -->
                <div class="perm-card">
                    <div class="perm-card-header">🏭 Vendors</div>
                    <div class="perm-item">
                        <div>
                            <div>Access</div>
                            <div class="perm-desc">Vendor list dekh sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="vendor_access"
                                   <?php echo e($permissions->vendor_access ? 'checked' : ''); ?>>
                        </div>
                    </div>
                    <div class="perm-item sub-item">
                        <div>
                            <div>View Ledger</div>
                            <div class="perm-desc">Vendor ledger dekh sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="vendor_ledger"
                                   <?php echo e($permissions->vendor_ledger ? 'checked' : ''); ?>>
                        </div>
                    </div>
                    <div class="perm-item sub-item">
                        <div>
                            <div>Send Payment</div>
                            <div class="perm-desc">Vendor payment send kar sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="vendor_payment"
                                   <?php echo e($permissions->vendor_payment ? 'checked' : ''); ?>>
                        </div>
                    </div>
                </div>

                <!-- Inventory -->
                <div class="perm-card">
                    <div class="perm-card-header">📊 Inventory</div>
                    <div class="perm-item">
                        <div>
                            <div>Access</div>
                            <div class="perm-desc">Inventory page dekh sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="inventory_access"
                                   <?php echo e($permissions->inventory_access ? 'checked' : ''); ?>>
                                   <div class="perm-item sub-item">
    <div>
        <div>View Stock Value Card</div>
        <div class="perm-desc">Total Stock Value card dekh sakta hai</div>
    </div>
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox"
               name="inventory_stock_value"
               <?php echo e($permissions->inventory_stock_value ? 'checked' : ''); ?>>
    </div>
</div>
                        </div>
                    </div>
                    <div class="perm-item sub-item">
                        <div>
                            <div>View Prices</div>
                            <div class="perm-desc">Purchase Price aur Sale Price dekh sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="inventory_prices"
                                   <?php echo e($permissions->inventory_prices ? 'checked' : ''); ?>>
                        </div>
                    </div>
                    <div class="perm-item sub-item">
                        <div>
                            <div>Edit Product</div>
                            <div class="perm-desc">Product edit kar sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="inventory_edit"
                                   <?php echo e($permissions->inventory_edit ? 'checked' : ''); ?>>
                        </div>
                    </div>
                    <div class="perm-item sub-item">
                        <div>
                            <div>Add Opening Stock</div>
                            <div class="perm-desc">Naya stock add kar sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="inventory_add_stock"
                                   <?php echo e($permissions->inventory_add_stock ? 'checked' : ''); ?>>
                        </div>
                    </div>
                </div>

                <!-- Daily Report -->
                <div class="perm-card">
                    <div class="perm-card-header">📈 Daily Report</div>
                    <div class="perm-item">
                        <div>
                            <div>Access</div>
                            <div class="perm-desc">Daily report dekh sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="report_access"
                                   <?php echo e($permissions->report_access ? 'checked' : ''); ?>>
                        </div>
                    </div>
                </div>

                <!-- Dashboard -->
                <div class="perm-card">
                    <div class="perm-card-header">🏠 Dashboard</div>
                    <div class="perm-item">
                        <div>
                            <div>Access</div>
                            <div class="perm-desc">Dashboard dekh sakta hai</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="dashboard_access"
                                   <?php echo e($permissions->dashboard_access ? 'checked' : ''); ?>>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Save Permissions
                </button>
            </form>
            <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function loadStaff(userId) {
    window.location.href = '<?php echo e(route("settings.permissions")); ?>?user_id=' + userId;
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\star_computer_accounting\resources\views/settings/permissions.blade.php ENDPATH**/ ?>