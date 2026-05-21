<?php $__env->startSection('title', 'Customer Manager'); ?>

<?php $__env->startSection('content'); ?>

<!-- Top Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4">
                <i class="bi bi-arrow-down-circle" style="color:#22c55e"></i>
            </div>
            <div class="stat-value">Rs. <?php echo e(number_format($totalReceivable)); ?></div>
            <div class="stat-label">Total Receivable</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff">
                <i class="bi bi-check-circle" style="color:#4f8ef7"></i>
            </div>
            <div class="stat-value"><?php echo e($settledCount); ?></div>
            <div class="stat-label">Settled Customers</div>
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
            <a href="<?php echo e(route('customers.export.pdf')); ?>" class="btn btn-sm btn-outline-danger no-print">
                <i class="bi bi-file-pdf"></i> PDF
            </a>
        </div>
        <span class="table-card-title">
            <i class="bi bi-people me-2"></i>All Customers
        </span>
        <a href="<?php echo e(route('customers.create')); ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Customer
        </a>
    </div>

    
    <div class="px-3 pt-3 pb-2 no-print" style="position:relative;">
        <div style="position:relative;">
            <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;"></i>
            <input
                type="text"
                id="customerSearch"
                class="form-control"
                placeholder="Customer name ya contact se search karein..."
                autocomplete="off"
                style="padding-left:36px;"
                oninput="filterCustomers()"
                onkeydown="handleCustomerKey(event)"
            >
            <div id="customerDropdown" style="
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
            <tbody id="customerTableBody">
                <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="customer-row"
                    style="cursor:pointer"
                    data-name="<?php echo e(strtolower($customer->name)); ?>"
                    data-contact="<?php echo e(strtolower($customer->contact1 ?? '')); ?>"
                    data-href="<?php echo e(route('customers.show', $customer)); ?>">
                    <td><?php echo e($i + 1); ?></td>
                    <td><strong><?php echo e($customer->name); ?></strong></td>
                    <td><?php echo e($customer->contact1 ?? '—'); ?></td>
                    <td><?php echo e($customer->cnic ?? '—'); ?></td>
                    <td>
                        <span class="memo-no">Rs. <?php echo e(number_format($customer->balance)); ?></span>
                    </td>
                    <td>
                        <?php if($customer->balance > 0): ?>
                            <span class="badge-receivable">🔴 Receivable</span>
                        <?php else: ?>
                            <span class="badge-settled">🟢 Settled</span>
                        <?php endif; ?>
                    </td>
                    <td onclick="event.stopPropagation()">
                        <a href="<?php echo e(route('customers.edit', $customer)); ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="<?php echo e(route('customers.destroy', $customer)); ?>" method="POST" class="d-inline"
                              onsubmit="return confirm('Customer delete karein?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Koi customer nahi mila</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
const allCustomers = <?php echo json_encode($customersJson); ?>;

let custHlIdx = -1;
let filteredCustomers = [];

function filterCustomers() {
    const q = document.getElementById('customerSearch').value.trim().toLowerCase();
    const dropdown = document.getElementById('customerDropdown');
    const rows = document.querySelectorAll('.customer-row');
    custHlIdx = -1;

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

    filteredCustomers = allCustomers.filter(function(c) {
        return c.name.toLowerCase().includes(q) || c.contact.toLowerCase().includes(q);
    }).slice(0, 6);

    if (filteredCustomers.length === 0) {
        dropdown.innerHTML = '<div style="padding:12px 16px;font-size:13px;color:#9ca3af;text-align:center;">Koi customer nahi mila</div>';
    } else {
        dropdown.innerHTML = filteredCustomers.map(function(c, i) {
            const badgeStyle = c.status === 'receivable'
                ? 'background:#fde8e8;color:#b91c1c;'
                : 'background:#e7f7ee;color:#166534;';
            const badgeText = c.status === 'receivable' ? '🔴 Receivable' : '🟢 Settled';
            const nameHl    = highlight(c.name, q);
            const contHl    = highlight(c.contact, q);
            const bal       = Number(c.balance).toLocaleString();

            return '<div class="cust-suggestion" data-idx="' + i + '" onmousedown="selectCustomer(' + i + ')"'
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

function selectCustomer(idx) {
    const c = filteredCustomers[idx];
    document.getElementById('customerSearch').value = c.name;
    document.getElementById('customerDropdown').style.display = 'none';

    const rows = document.querySelectorAll('.customer-row');
    rows.forEach(r => {
        r.style.display = r.dataset.name === c.name.toLowerCase() ? '' : 'none';
    });
}

function handleCustomerKey(e) {
    const dropdown = document.getElementById('customerDropdown');
    const items = dropdown.querySelectorAll('.cust-suggestion');
    if (!items.length) return;

    if (e.key === 'ArrowDown') {
        custHlIdx = Math.min(custHlIdx + 1, items.length - 1);
    } else if (e.key === 'ArrowUp') {
        custHlIdx = Math.max(custHlIdx - 1, 0);
    } else if (e.key === 'Enter' && custHlIdx >= 0) {
        selectCustomer(custHlIdx);
        return;
    } else if (e.key === 'Escape') {
        dropdown.style.display = 'none';
        return;
    }

    items.forEach(function(el, i) {
        el.style.background = i === custHlIdx ? '#f3f4f6' : '';
    });
}

document.querySelectorAll('.customer-row').forEach(function(row) {
    row.addEventListener('click', function() {
        window.location = this.dataset.href;
    });
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('#customerSearch') && !e.target.closest('#customerDropdown')) {
        document.getElementById('customerDropdown').style.display = 'none';
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\star_computer_accounting\resources\views/customers/index.blade.php ENDPATH**/ ?>