<?php $__env->startSection('title', 'Inventory'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .col-filter-label {
        font-size: 10px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
    }
    .col-filter-select {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        padding: 2px 4px;
        font-size: 11px;
        margin-top: 3px;
        color: #374151;
        background: #fff;
        outline: none;
    }
    .col-filter-select:focus { border-color: #4f8ef7; }
    .badge-active   { background:#f0fdf4;color:#166534;font-size:10px;padding:2px 8px;border-radius:4px;font-weight:700; }
    .badge-inactive { background:#fef2f2;color:#ef4444;font-size:10px;padding:2px 8px;border-radius:4px;font-weight:700; }
    tr.inactive-row { opacity: 0.6; background: #fafafa !important; }

    /* ✅ Search styles */
    .search-wrapper { position: relative; }
    #productSearchInput {
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        padding: 8px 14px 8px 36px;
        font-size: 14px;
        width: 100%;
        outline: none;
        transition: border-color 0.2s;
    }
    #productSearchInput:focus { border-color: #4f8ef7; box-shadow: 0 0 0 3px #eff6ff; }
    .search-icon {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 15px;
    }
    #searchSuggestions {
        position: absolute;
        top: calc(100% + 4px);
        left: 0; right: 0;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.10);
        z-index: 9999;
        max-height: 320px;
        overflow-y: auto;
        display: none;
    }
    .suggestion-item {
        padding: 10px 14px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s;
    }
    .suggestion-item:last-child { border-bottom: none; }
    .suggestion-item:hover { background: #eff6ff; }
    .suggestion-name { font-weight: 700; font-size: 13px; color: #111827; }
    .suggestion-meta { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .suggestion-badge {
        font-size: 10px;
        padding: 1px 7px;
        border-radius: 10px;
        font-weight: 700;
        margin-left: 6px;
    }
    .badge-qty-ok  { background:#f0fdf4; color:#166534; }
    .badge-qty-low { background:#fef2f2; color:#ef4444; }
    .no-suggestion { padding: 14px; text-align:center; color:#9ca3af; font-size:13px; }

    /* ✅ Duplicate alert in modal */
    #duplicateAlert {
        display: none;
        background: #fffbeb;
        border: 1px solid #fbbf24;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 8px;
        font-size: 13px;
        color: #92400e;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<?php
    $isAdmin          = auth()->user()->role === 'admin';
    $staffPerms       = $isAdmin ? null : \App\Models\StaffPermission::where('user_id', auth()->id())->first();
    $canSeePrices     = $isAdmin || ($staffPerms?->inventory_prices ?? false);
    $canSeeStockValue = $isAdmin || ($staffPerms?->inventory_stock_value ?? false);
    $canEdit          = $isAdmin || ($staffPerms?->inventory_edit ?? false);
    $canAddStock      = $isAdmin || ($staffPerms?->inventory_add_stock ?? false);
?>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="<?php echo e($canSeeStockValue ? 'col-md-4' : 'col-md-6'); ?>">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff">
                <i class="bi bi-box-seam" style="color:#4f8ef7"></i>
            </div>
            <div class="stat-value">
                <?php echo e($products->where('is_active', true)->count()); ?>

            </div>
            <div class="stat-label">Total Active Products</div>
        </div>
    </div>

    <?php if($canSeeStockValue): ?>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4">
                <i class="bi bi-graph-up" style="color:#22c55e"></i>
            </div>
            <div class="stat-value">
                <span style="font-size:0.9rem;color:#6b7280;font-weight:600">PKR</span>
                <?php echo e(number_format($totalValue)); ?>

            </div>
            <div class="stat-label">Total Stock Value</div>
        </div>
    </div>
    <?php endif; ?>

    <div class="<?php echo e($canSeeStockValue ? 'col-md-4' : 'col-md-6'); ?>">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef2f2">
                <i class="bi bi-exclamation-triangle" style="color:#ef4444"></i>
            </div>
            <div class="stat-value" style="color:#ef4444"><?php echo e($lowStock); ?></div>
            <div class="stat-label">Low Stock Alert</div>
        </div>
    </div>
</div>

<!-- ✅ Search Bar -->
<div class="mb-3">
    <div class="search-wrapper">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="productSearchInput"
               placeholder="Product name ya stock code se search karein..."
               autocomplete="off">
        <div id="searchSuggestions"></div>
    </div>
</div>

<!-- Table Card -->
<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">
            <i class="bi bi-box-seam me-2"></i>All Products
        </span>
        <div class="d-flex gap-2">
            <?php if($canAddStock): ?>
            <button onclick="openStockModal()"
                    class="btn btn-sm btn-success">
                <i class="bi bi-plus-lg me-1"></i> Add Opening Stock
            </button>
            <?php endif; ?>
            <button onclick="resetProductFilters()"
                    class="btn btn-sm btn-outline-secondary">
                🔄 Reset
            </button>
            <button onclick="window.print()"
                    class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-printer"></i> Print
            </button>
            <a href="<?php echo e(route('products.export.excel')); ?>"
               class="btn btn-sm btn-outline-success">
                <i class="bi bi-file-excel"></i> Excel
            </a>
            <a href="<?php echo e(route('products.export.pdf')); ?>"
               class="btn btn-sm btn-outline-danger">
                <i class="bi bi-file-pdf"></i> PDF
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table mb-0" id="productsTable">
            <thead>
                <tr>
                    <th><div class="col-filter-label">#</div></th>
                    <th>
                        <div class="col-filter-label">Stock Code</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All Codes</option>
                            <?php $__currentLoopData = $products->pluck('stock_code')->filter()->unique()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e(strtolower($code)); ?>"><?php echo e($code); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Product</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All Products</option>
                            <?php $__currentLoopData = $products->pluck('name')->unique()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e(strtolower($name)); ?>"><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Vendor</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All Vendors</option>
                            <?php $__currentLoopData = $products->pluck('vendor.name')->filter()->unique()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e(strtolower($v)); ?>"><?php echo e($v); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Received</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All</option>
                            <?php $__currentLoopData = $products->pluck('received_qty')->unique()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($r); ?>"><?php echo e($r); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Sold</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All</option>
                            <?php $__currentLoopData = $products->pluck('sold_qty')->unique()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s); ?>"><?php echo e($s); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Remaining</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All</option>
                            <option value="low">⚠️ Low Stock</option>
                            <?php $__currentLoopData = $products->pluck('remaining_qty')->unique()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($r); ?>"><?php echo e($r); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </th>
                    <?php if($canSeePrices): ?>
                    <th>
                        <div class="col-filter-label">P. Price</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All</option>
                            <?php $__currentLoopData = $products->pluck('purchase_price')->unique()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="rs. <?php echo e(number_format($p)); ?>">Rs. <?php echo e(number_format($p)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">S. Price</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All</option>
                            <?php $__currentLoopData = $products->pluck('sale_price')->unique()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="rs. <?php echo e(number_format($s)); ?>">Rs. <?php echo e(number_format($s)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </th>
                    <?php endif; ?>
                    <th>
                        <div class="col-filter-label">Status</div>
                        <select class="col-filter-select" id="statusFilter" onchange="filterProducts()">
                            <option value="all">All</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive Only</option>
                        </select>
                    </th>
                    <th><div class="col-filter-label">Action</div></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="<?php echo e(!$product->is_active ? 'inactive-row' : ''); ?>"
                    data-code="<?php echo e(strtolower($product->stock_code ?? '')); ?>"
                    data-name="<?php echo e(strtolower($product->name)); ?>"
                    data-vendor="<?php echo e(strtolower($product->vendor->name ?? '')); ?>"
                    data-received="<?php echo e($product->received_qty); ?>"
                    data-sold="<?php echo e($product->sold_qty); ?>"
                    data-remaining="<?php echo e($product->remaining_qty); ?>"
                    data-pprice="rs. <?php echo e(strtolower(number_format($product->purchase_price))); ?>"
                    data-sprice="rs. <?php echo e(strtolower(number_format($product->sale_price))); ?>"
                    data-status="<?php echo e($product->is_active ? 'active' : 'inactive'); ?>"
                    data-low="<?php echo e($product->remaining_qty <= $product->alert_qty ? 'low' : ''); ?>">
                    <td><?php echo e($i + 1); ?></td>
                    <td>
                        <?php if($product->stock_code): ?>
                            <span class="memo-no"><?php echo e($product->stock_code); ?></span>
                        <?php else: ?> —
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo e($product->name); ?></strong></td>
                    <td><?php echo e($product->vendor->name ?? '—'); ?></td>
                    <td><?php echo e($product->received_qty); ?></td>
                    <td><?php echo e($product->sold_qty); ?></td>
                    <td>
                        <?php if($product->remaining_qty <= $product->alert_qty): ?>
                            <span style="color:#ef4444;font-weight:700">
                                <?php echo e($product->remaining_qty); ?>

                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </span>
                        <?php else: ?>
                            <?php echo e($product->remaining_qty); ?>

                        <?php endif; ?>
                    </td>
                    <?php if($canSeePrices): ?>
                    <td>Rs. <?php echo e(number_format($product->purchase_price)); ?></td>
                    <td>Rs. <?php echo e(number_format($product->sale_price)); ?></td>
                    <?php endif; ?>
                    <td>
                        <?php if($product->is_active): ?>
                            <span class="badge-active">✅ Active</span>
                        <?php else: ?>
                            <span class="badge-inactive">🚫 Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($canEdit): ?>
                        <a href="<?php echo e(route('products.edit', $product)); ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php endif; ?>
                        <?php if($isAdmin): ?>
                        <form action="<?php echo e(route('products.destroy', $product)); ?>"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('<?php echo e($product->is_active ? 'Disable karein?' : 'Enable karein?'); ?>')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <?php if($product->is_active): ?>
                                <button class="btn btn-sm btn-outline-danger" title="Disable">
                                    <i class="bi bi-slash-circle"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-success" title="Enable">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            <?php endif; ?>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="<?php echo e($canSeePrices ? 11 : 9); ?>"
                        class="text-center text-muted py-4">
                        Koi product nahi mila
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Opening Stock Modal -->
<?php if($canAddStock): ?>
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2"></i>Add Opening Stock
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('products.opening.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">

                    <!-- ✅ Duplicate Warning Box -->
                    <div id="duplicateAlert">
                        <strong>⚠️ Yeh product already exist karta hai!</strong><br>
                        <span id="duplicateInfo"></span><br>
                        <small>Stock add karne par <strong>qty increase</strong> hogi aur <strong>average price</strong> calculate hoga.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Product Name *</label>
                            <!-- ✅ Search input with suggestions -->
                            <div class="search-wrapper">
                                <i class="bi bi-search search-icon" style="top:38px"></i>
                                <input type="text" name="name" id="modalProductName"
                                       class="form-control ps-4" style="padding-left:32px!important"
                                       placeholder="e.g. RAM DDR4" required autocomplete="off">
                                <div id="modalSuggestions" style="position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,0.10);z-index:9999;display:none;max-height:220px;overflow-y:auto;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Stock Code
                                <small class="text-muted">(Optional)</small>
                            </label>
                            <input type="text" name="stock_code" id="modalStockCode" class="form-control"
                                   placeholder="e.g. SC-001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Vendor
                                <small class="text-muted">(Optional)</small>
                            </label>
                            <select name="vendor_id" class="form-select">
                                <option value="">Select Vendor...</option>
                                <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($vendor->id); ?>"><?php echo e($vendor->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Opening Qty *</label>
                            <input type="number" name="opening_qty" class="form-control"
                                   placeholder="0" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Purchase Price *</label>
                            <input type="number" name="purchase_price" id="modalPurchasePrice"
                                   class="form-control" placeholder="0.00" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Sale Price
                                <small class="text-muted">(Optional)</small>
                            </label>
                            <input type="number" name="sale_price" class="form-control"
                                   placeholder="0.00" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Alert Qty
                                <small class="text-muted">(Default: 5)</small>
                            </label>
                            <input type="number" name="alert_qty" class="form-control"
                                   placeholder="5" min="1" value="5">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-check-lg me-1"></i>Add Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const CAN_SEE_PRICES = <?php echo e($canSeePrices ? 'true' : 'false'); ?>;
const SEARCH_URL     = "<?php echo e(route('products.search')); ?>";

// =========================================================
// ✅ TOP SEARCH BAR
// =========================================================
let searchTimer;
const searchInput       = document.getElementById('productSearchInput');
const searchSuggestions = document.getElementById('searchSuggestions');

if (searchInput) {
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        const q = this.value.trim();
if (q.length < 1) {
    searchSuggestions.style.display = 'none';
    // ✅ Sari rows wapas dikhao
    document.querySelectorAll('#productsTable tbody tr').forEach(r => r.style.display = '');
    return;
}
        searchTimer = setTimeout(() => {
          fetch(SEARCH_URL + '?q=' + encodeURIComponent(q), {
    headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    credentials: 'same-origin'
})
.then(r => r.json())
                .then(data => {
                    if (!data.length) {
                        searchSuggestions.innerHTML = '<div class="no-suggestion">Koi product nahi mila 😕</div>';
                    } else {
                        searchSuggestions.innerHTML = data.map(p => `
                            <div class="suggestion-item" onclick="highlightProduct('${p.name.replace(/'/g,"\\'")}')">
                                <div class="suggestion-name">
                                    ${p.name}
                                    ${p.stock_code ? '<span style="color:#9ca3af;font-size:11px"> · ' + p.stock_code + '</span>' : ''}
                                    <span class="suggestion-badge ${p.remaining_qty <= 0 ? 'badge-qty-low' : 'badge-qty-ok'}">
                                        Qty: ${p.remaining_qty}
                                    </span>
                                </div>
                                <div class="suggestion-meta">Vendor: ${p.vendor}</div>
                            </div>
                        `).join('');
                    }
                    searchSuggestions.style.display = 'block';
                });
        }, 250);
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('.search-wrapper')) {
            searchSuggestions.style.display = 'none';
        }
    });
}

function highlightProduct(name) {
    searchSuggestions.style.display = 'none';
    searchInput.value = name;

    // Filter table
    const rows = document.querySelectorAll('#productsTable tbody tr');
    rows.forEach(row => {
        row.style.display = row.dataset.name === name.toLowerCase() ? '' : 'none';
    });
}

// =========================================================
// ✅ MODAL SEARCH + DUPLICATE DETECTION
// =========================================================
let modalTimer;
const modalNameInput    = document.getElementById('modalProductName');
const modalSuggestions  = document.getElementById('modalSuggestions');
const duplicateAlert    = document.getElementById('duplicateAlert');
const duplicateInfo     = document.getElementById('duplicateInfo');

if (modalNameInput) {
    modalNameInput.addEventListener('input', function () {
        clearTimeout(modalTimer);
        const q = this.value.trim();
        duplicateAlert.style.display = 'none';

        if (q.length < 1) { modalSuggestions.style.display = 'none'; return; }

        modalTimer = setTimeout(() => {
            fetch(SEARCH_URL + '?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(data => {
                    if (!data.length) {
                        modalSuggestions.style.display = 'none';
                        return;
                    }

                    modalSuggestions.innerHTML = data.map(p => `
                        <div class="suggestion-item" onclick="selectModalProduct(${JSON.stringify(p).replace(/"/g,'&quot;')})">
                            <div class="suggestion-name">
                                ${p.name}
                                ${p.stock_code ? '<span style="color:#9ca3af;font-size:11px"> · ' + p.stock_code + '</span>' : ''}
                                <span class="suggestion-badge ${p.remaining_qty <= 0 ? 'badge-qty-low' : 'badge-qty-ok'}">
                                    Stock: ${p.remaining_qty}
                                </span>
                            </div>
                            <div class="suggestion-meta">Vendor: ${p.vendor} | P.Price: Rs. ${Number(p.purchase_price).toLocaleString()}</div>
                        </div>
                    `).join('');
                    modalSuggestions.style.display = 'block';

                    // Exact match check → show duplicate warning
                    const exact = data.find(p => p.name.toLowerCase() === q.toLowerCase());
                    if (exact) {
                        duplicateInfo.innerHTML = `<strong>${exact.name}</strong> — Stock: ${exact.remaining_qty} | Current Price: Rs. ${Number(exact.purchase_price).toLocaleString()}`;
                        duplicateAlert.style.display = 'block';
                    }
                });
        }, 300);
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('#stockModal')) {
            if (modalSuggestions) modalSuggestions.style.display = 'none';
        }
    });
}

function selectModalProduct(product) {
    modalNameInput.value = product.name;
    if (document.getElementById('modalStockCode') && product.stock_code) {
        document.getElementById('modalStockCode').value = product.stock_code;
    }
    if (document.getElementById('modalPurchasePrice')) {
        document.getElementById('modalPurchasePrice').value = product.purchase_price;
    }
    modalSuggestions.style.display = 'none';

    // Show duplicate warning
    duplicateInfo.innerHTML = `<strong>${product.name}</strong> — Stock: ${product.remaining_qty} | Current Price: Rs. ${Number(product.purchase_price).toLocaleString()}`;
    duplicateAlert.style.display = 'block';
}

// =========================================================
// ✅ TABLE FILTERS
// =========================================================
function filterProducts() {
    const selects   = document.querySelectorAll('#productsTable thead select');
    const rows      = document.querySelectorAll('#productsTable tbody tr');
    const status    = document.getElementById('statusFilter').value;

    const code      = selects[1].value.toLowerCase();
    const name      = selects[2].value.toLowerCase();
    const vendor    = selects[3].value.toLowerCase();
    const received  = selects[4].value;
    const sold      = selects[5].value;
    const remaining = selects[6].value;
    const pprice    = CAN_SEE_PRICES ? selects[7]?.value.toLowerCase() : '';
    const sprice    = CAN_SEE_PRICES ? selects[8]?.value.toLowerCase() : '';

    rows.forEach(row => {
        let show = true;

        if (code     && !row.dataset.code.includes(code))     show = false;
        if (name     && !row.dataset.name.includes(name))     show = false;
        if (vendor   && !row.dataset.vendor.includes(vendor)) show = false;
        if (received && row.dataset.received !== received)     show = false;
        if (sold     && row.dataset.sold !== sold)             show = false;
        if (pprice   && !row.dataset.pprice.includes(pprice)) show = false;
        if (sprice   && !row.dataset.sprice.includes(sprice)) show = false;

        if (remaining === 'low') {
            if (!row.dataset.low) show = false;
        } else if (remaining) {
            if (row.dataset.remaining !== remaining) show = false;
        }

        if (status === 'active'   && row.dataset.status !== 'active')   show = false;
        if (status === 'inactive' && row.dataset.status !== 'inactive') show = false;

        row.style.display = show ? '' : 'none';
    });
}

function resetProductFilters() {
    document.querySelectorAll('#productsTable thead select')
        .forEach(select => { select.value = ''; });
    document.getElementById('statusFilter').value = 'all';
    if (searchInput) searchInput.value = '';
    filterProducts();
    // Show all rows
    document.querySelectorAll('#productsTable tbody tr').forEach(r => r.style.display = '');
}

function openStockModal() {
    if (duplicateAlert) duplicateAlert.style.display = 'none';
    new bootstrap.Modal(document.getElementById('stockModal')).show();
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\star_computer_accounting\resources\views/products/index.blade.php ENDPATH**/ ?>