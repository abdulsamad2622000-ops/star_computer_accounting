@extends('layouts.app')
@section('title', 'Inventory')

@push('styles')
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
</style>
@endpush

@section('content')

@php
    $isAdmin          = auth()->user()->role === 'admin';
    $staffPerms       = $isAdmin ? null : \App\Models\StaffPermission::where('user_id', auth()->id())->first();
    $canSeePrices     = $isAdmin || ($staffPerms?->inventory_prices ?? false);
    $canSeeStockValue = $isAdmin || ($staffPerms?->inventory_stock_value ?? false);
    $canEdit          = $isAdmin || ($staffPerms?->inventory_edit ?? false);
    $canAddStock      = $isAdmin || ($staffPerms?->inventory_add_stock ?? false);
@endphp

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="{{ $canSeeStockValue ? 'col-md-4' : 'col-md-6' }}">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff">
                <i class="bi bi-box-seam" style="color:#4f8ef7"></i>
            </div>
            <div class="stat-value">
                {{ $products->where('is_active', true)->count() }}
            </div>
            <div class="stat-label">Total Active Products</div>
        </div>
    </div>

    @if($canSeeStockValue)
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4">
                <i class="bi bi-graph-up" style="color:#22c55e"></i>
            </div>
            <div class="stat-value">
                <span style="font-size:0.9rem;color:#6b7280;font-weight:600">PKR</span>
                {{ number_format($totalValue) }}
            </div>
            <div class="stat-label">Total Stock Value</div>
        </div>
    </div>
    @endif

    <div class="{{ $canSeeStockValue ? 'col-md-4' : 'col-md-6' }}">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef2f2">
                <i class="bi bi-exclamation-triangle" style="color:#ef4444"></i>
            </div>
            <div class="stat-value" style="color:#ef4444">{{ $lowStock }}</div>
            <div class="stat-label">Low Stock Alert</div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">
            <i class="bi bi-box-seam me-2"></i>All Products
        </span>
        <div class="d-flex gap-2">
            @if($canAddStock)
            <button onclick="openStockModal()"
                    class="btn btn-sm btn-success">
                <i class="bi bi-plus-lg me-1"></i> Add Opening Stock
            </button>
            @endif
            <button onclick="resetProductFilters()"
                    class="btn btn-sm btn-outline-secondary">
                🔄 Reset
            </button>
            <button onclick="window.print()"
                    class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-printer"></i> Print
            </button>
            <a href="{{ route('products.export.excel') }}"
               class="btn btn-sm btn-outline-success">
                <i class="bi bi-file-excel"></i> Excel
            </a>
            <a href="{{ route('products.export.pdf') }}"
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
                            @foreach($products->pluck('stock_code')->filter()->unique()->sort() as $code)
                            <option value="{{ strtolower($code) }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Product</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All Products</option>
                            @foreach($products->pluck('name')->unique()->sort() as $name)
                            <option value="{{ strtolower($name) }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Vendor</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All Vendors</option>
                            @foreach($products->pluck('vendor.name')->filter()->unique()->sort() as $v)
                            <option value="{{ strtolower($v) }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Received</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All</option>
                            @foreach($products->pluck('received_qty')->unique()->sort() as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Sold</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All</option>
                            @foreach($products->pluck('sold_qty')->unique()->sort() as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Remaining</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All</option>
                            <option value="low">⚠️ Low Stock</option>
                            @foreach($products->pluck('remaining_qty')->unique()->sort() as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </th>
                    @if($canSeePrices)
                    <th>
                        <div class="col-filter-label">P. Price</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All</option>
                            @foreach($products->pluck('purchase_price')->unique()->sort() as $p)
                            <option value="rs. {{ number_format($p) }}">Rs. {{ number_format($p) }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">S. Price</div>
                        <select class="col-filter-select" onchange="filterProducts()">
                            <option value="">All</option>
                            @foreach($products->pluck('sale_price')->unique()->sort() as $s)
                            <option value="rs. {{ number_format($s) }}">Rs. {{ number_format($s) }}</option>
                            @endforeach
                        </select>
                    </th>
                    @endif
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
                @forelse($products as $i => $product)
                <tr class="{{ !$product->is_active ? 'inactive-row' : '' }}"
                    data-code="{{ strtolower($product->stock_code ?? '') }}"
                    data-name="{{ strtolower($product->name) }}"
                    data-vendor="{{ strtolower($product->vendor->name ?? '') }}"
                    data-received="{{ $product->received_qty }}"
                    data-sold="{{ $product->sold_qty }}"
                    data-remaining="{{ $product->remaining_qty }}"
                    data-pprice="rs. {{ strtolower(number_format($product->purchase_price)) }}"
                    data-sprice="rs. {{ strtolower(number_format($product->sale_price)) }}"
                    data-status="{{ $product->is_active ? 'active' : 'inactive' }}"
                    data-low="{{ $product->remaining_qty <= $product->alert_qty ? 'low' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if($product->stock_code)
                            <span class="memo-no">{{ $product->stock_code }}</span>
                        @else —
                        @endif
                    </td>
                    <td><strong>{{ $product->name }}</strong></td>
                    <td>{{ $product->vendor->name ?? '—' }}</td>
                    <td>{{ $product->received_qty }}</td>
                    <td>{{ $product->sold_qty }}</td>
                    <td>
                        @if($product->remaining_qty <= $product->alert_qty)
                            <span style="color:#ef4444;font-weight:700">
                                {{ $product->remaining_qty }}
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </span>
                        @else
                            {{ $product->remaining_qty }}
                        @endif
                    </td>
                    @if($canSeePrices)
                    <td>Rs. {{ number_format($product->purchase_price) }}</td>
                    <td>Rs. {{ number_format($product->sale_price) }}</td>
                    @endif
                    <td>
                        @if($product->is_active)
                            <span class="badge-active">✅ Active</span>
                        @else
                            <span class="badge-inactive">🚫 Inactive</span>
                        @endif
                    </td>
                    <td>
                        @if($canEdit)
                        <a href="{{ route('products.edit', $product) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endif
                        @if($isAdmin)
                        <form action="{{ route('products.destroy', $product) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('{{ $product->is_active ? 'Disable karein?' : 'Enable karein?' }}')">
                            @csrf @method('DELETE')
                            @if($product->is_active)
                                <button class="btn btn-sm btn-outline-danger" title="Disable">
                                    <i class="bi bi-slash-circle"></i>
                                </button>
                            @else
                                <button class="btn btn-sm btn-outline-success" title="Enable">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            @endif
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $canSeePrices ? 11 : 9 }}"
                        class="text-center text-muted py-4">
                        Koi product nahi mila
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Opening Stock Modal -->
@if($canAddStock)
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2"></i>Add Opening Stock
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('products.opening.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Product Name *</label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="e.g. RAM DDR4" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Stock Code
                                <small class="text-muted">(Optional)</small>
                            </label>
                            <input type="text" name="stock_code" class="form-control"
                                   placeholder="e.g. SC-001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Vendor
                                <small class="text-muted">(Optional)</small>
                            </label>
                            <select name="vendor_id" class="form-select">
                                <option value="">Select Vendor...</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Opening Qty *</label>
                            <input type="number" name="opening_qty" class="form-control"
                                   placeholder="0" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Purchase Price *</label>
                            <input type="number" name="purchase_price" class="form-control"
                                   placeholder="0.00" min="0" step="0.01" required>
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
@endif

@endsection

@push('scripts')
<script>
const CAN_SEE_PRICES = {{ $canSeePrices ? 'true' : 'false' }};

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
    filterProducts();
}

function openStockModal() {
    new bootstrap.Modal(document.getElementById('stockModal')).show();
}
</script>
@endpush