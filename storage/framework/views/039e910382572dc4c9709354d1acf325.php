<?php $__env->startSection('title', 'Vendor Manager'); ?>

<?php $__env->startSection('content'); ?>

<!-- Top Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff7ed">
                <i class="bi bi-arrow-up-circle" style="color:#f59e0b"></i>
            </div>
            <div class="stat-value">Rs. <?php echo e(number_format($totalPayable)); ?></div>
            <div class="stat-label">Total Payable</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff">
                <i class="bi bi-check-circle" style="color:#4f8ef7"></i>
            </div>
            <div class="stat-value"><?php echo e($settledCount); ?></div>
            <div class="stat-label">Settled Vendors</div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-card">
    <div class="table-card-header">
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary no-print">
                <i class="bi bi-printer"></i> Print
            </button>
          <a href="<?php echo e(route('vendors.export.pdf')); ?>" class="btn btn-sm btn-outline-danger no-print">

                <i class="bi bi-file-pdf"></i> PDF
            </a>
        </div>
        <span class="table-card-title">
            <i class="bi bi-shop me-2"></i>All Vendors
        </span>
        <a href="<?php echo e(route('vendors.create')); ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Vendor
        </a>
    </div>

    
    <div class="px-3 pt-3 pb-2 no-print" style="position:relative;">
        <div style="position:relative;">
            <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;"></i>
            <input
                type="text"
                id="vendorSearch"
                class="form-control"
                placeholder="Vendor name ya contact se search karein..."
                autocomplete="off"
                style="padding-left:36px;"
                oninput="filterVendors()"
                onkeydown="handleVendorKey(event)"
            >
            <div id="vendorDropdown" style="
                display:none;
                position:absolute;
                top:calc(100% + 4px);
                left:0;right:0;
                background:#fff;
                border:1px solid #e5e7eb;
                border-radius:8px;
                z-index:999;
                box-shadow:0 4px 16px rgba(0,0,0,0.08);
                overflow:hidden;
            "></div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>CNIC</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="vendorTableBody">
                <?php $__empty_1 = true; $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="vendor-row"
                    style="cursor:pointer"
                    data-name="<?php echo e(strtolower($vendor->name)); ?>"
                    data-contact="<?php echo e(strtolower($vendor->contact1 ?? '')); ?>"
                    data-href="<?php echo e(route('vendors.show', $vendor)); ?>">
                    <td><?php echo e($i + 1); ?></td>
                    <td><strong><?php echo e($vendor->name); ?></strong></td>
                    <td><?php echo e($vendor->contact1 ?? '—'); ?></td>
                    <td><?php echo e($vendor->cnic ?? '—'); ?></td>
                    <td>
                        <span class="memo-no">Rs. <?php echo e(number_format($vendor->balance)); ?></span>
                    </td>
                    <td>
                        <?php if($vendor->balance > 0): ?>
                            <span class="badge-payable">🟡 Payable</span>
                        <?php else: ?>
                            <span class="badge-settled">🟢 Settled</span>
                        <?php endif; ?>
                    </td>
                    <td onclick="event.stopPropagation()">
                        <a href="<?php echo e(route('vendors.edit', $vendor)); ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="<?php echo e(route('vendors.destroy', $vendor)); ?>" method="POST" class="d-inline"
                              onsubmit="return confirm('Vendor delete karein?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Koi vendor nahi mila</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
const allVendors = <?php echo json_encode($vendorsJson); ?>;

let vendHlIdx = -1;
let filteredVendors = [];

function filterVendors() {
    const q = document.getElementById('vendorSearch').value.trim().toLowerCase();
    const dropdown = document.getElementById('vendorDropdown');
    const rows = document.querySelectorAll('.vendor-row');
    vendHlIdx = -1;

    if (!q) {
        dropdown.style.display = 'none';
        rows.forEach(r => r.style.display = '');
        let n = 1;
        rows.forEach(r => { r.cells[0].textContent = n++; });
        return;
    }

    let visibleCount = 0;
    rows.forEach(r => {
        const match = r.dataset.name.includes(q) || r.dataset.contact.includes(q);
        r.style.display = match ? '' : 'none';
        if (match) {
            visibleCount++;
            r.cells[0].textContent = visibleCount;
        }
    });

    filteredVendors = allVendors.filter(v =>
        v.name.toLowerCase().includes(q) || v.contact.toLowerCase().includes(q)
    ).slice(0, 6);

    if (filteredVendors.length === 0) {
        dropdown.innerHTML = '<div style="padding:12px 16px;font-size:13px;color:#9ca3af;text-align:center;">Koi vendor nahi mila</div>';
    } else {
        dropdown.innerHTML = filteredVendors.map(function(v, i) {
            const badgeStyle = v.status === 'payable'
                ? 'background:#fff7e6;color:#b45309;'
                : 'background:#e7f7ee;color:#166534;';
            const badgeText = v.status === 'payable' ? '🟡 Payable' : '🟢 Settled';
            const nameHl    = highlight(v.name, q);
            const contHl    = highlight(v.contact, q);
            const bal       = Number(v.balance).toLocaleString();

            return '<div class="vend-suggestion" data-idx="' + i + '" onmousedown="selectVendor(' + i + ')"'
                + ' style="display:flex;align-items:center;justify-content:space-between;'
                + 'padding:10px 16px;cursor:pointer;font-size:14px;'
                + 'border-bottom:0.5px solid #f3f4f6;">'
                + '<div>'
                + '<div style="font-weight:500;">' + nameHl + '</div>'
                + '<div style="font-size:12px;color:#6b7280;">' + contHl + ' &nbsp;•&nbsp; Rs. ' + bal + '</div>'
                + '</div>'
                + '<span style="font-size:11px;padding:2px 10px;border-radius:99px;font-weight:500;' + badgeStyle + '">'
                + badgeText
                + '</span>'
                + '</div>';
        }).join('');
    }

    dropdown.style.display = 'block';
}

function highlight(text, q) {
    if (!q) return text;
    const re = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
    return text.replace(re, '<mark style="background:#fef9c3;color:#92400e;border-radius:2px;padding:0 1px;">$1</mark>');
}

function selectVendor(idx) {
    const v = filteredVendors[idx];
    document.getElementById('vendorSearch').value = v.name;
    document.getElementById('vendorDropdown').style.display = 'none';

    const rows = document.querySelectorAll('.vendor-row');
    rows.forEach(r => {
        r.style.display = r.dataset.name === v.name.toLowerCase() ? '' : 'none';
    });
}

function handleVendorKey(e) {
    const dropdown = document.getElementById('vendorDropdown');
    const items = dropdown.querySelectorAll('.vend-suggestion');
    if (!items.length) return;

    if (e.key === 'ArrowDown') {
        vendHlIdx = Math.min(vendHlIdx + 1, items.length - 1);
    } else if (e.key === 'ArrowUp') {
        vendHlIdx = Math.max(vendHlIdx - 1, 0);
    } else if (e.key === 'Enter' && vendHlIdx >= 0) {
        selectVendor(vendHlIdx);
        return;
    } else if (e.key === 'Escape') {
        dropdown.style.display = 'none';
        return;
    }

    items.forEach(function(el, i) {
        el.style.background = i === vendHlIdx ? '#f3f4f6' : '';
    });
}

document.querySelectorAll('.vendor-row').forEach(function(row) {
    row.addEventListener('click', function() {
        window.location = this.dataset.href;
    });
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('#vendorSearch') && !e.target.closest('#vendorDropdown')) {
        document.getElementById('vendorDropdown').style.display = 'none';
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\star_computer_accounting\resources\views/vendors/index.blade.php ENDPATH**/ ?>