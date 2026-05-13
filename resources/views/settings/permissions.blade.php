@extends('layouts.app')
@section('title', 'Staff Permissions')

@push('styles')
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
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="form-card">
            <h5 class="fw-bold mb-1">
                <i class="bi bi-shield-lock me-2"></i>Staff Permissions
            </h5>
            <p class="text-muted mb-4" style="font-size:13px">
                Pehle staff member select karo phir permissions set karo.
            </p>

            @if($staffList->isEmpty())
            <div class="alert alert-warning">
                Koi staff member nahi mila!
                <a href="{{ route('staff.create') }}" class="btn btn-sm btn-primary ms-2">
                    + Add Staff
                </a>
            </div>
            @else

            <!-- Staff Select -->
            <div class="staff-select-bar">
                <label class="fw-bold" style="color:#163a6f;white-space:nowrap">
                    <i class="bi bi-person-gear me-1"></i> Select Staff:
                </label>
                <select class="form-select"
                        id="staffSelect"
                        onchange="loadStaff(this.value)"
                        style="max-width:300px">
                    @foreach($staffList as $staff)
                    <option value="{{ $staff->id }}"
                        {{ $selectedId == $staff->id ? 'selected' : '' }}>
                        {{ $staff->name }}
                    </option>
                    @endforeach
                </select>
                @if($selectedId)
                <span class="badge bg-primary">
                    {{ $staffList->find($selectedId)?->name }}
                </span>
                @endif
            </div>

            @if($permissions)
            <form action="{{ route('settings.permissions.update') }}"
                  method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="user_id" value="{{ $selectedId }}">

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
                                   {{ $permissions->sale_access ? 'checked' : '' }}>
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
                                   {{ $permissions->sale_history ? 'checked' : '' }}>
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
                                   {{ $permissions->sale_return ? 'checked' : '' }}>
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
                                   {{ $permissions->purchase_access ? 'checked' : '' }}>
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
                                   {{ $permissions->purchase_history ? 'checked' : '' }}>
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
                                   {{ $permissions->purchase_return ? 'checked' : '' }}>
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
                                   {{ $permissions->purchase_price ? 'checked' : '' }}>
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
                                   {{ $permissions->purchase_rate_edit ? 'checked' : '' }}>
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
                                   {{ $permissions->customer_access ? 'checked' : '' }}>
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
                                   {{ $permissions->customer_ledger ? 'checked' : '' }}>
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
                                   {{ $permissions->customer_payment ? 'checked' : '' }}>
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
                                   {{ $permissions->vendor_access ? 'checked' : '' }}>
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
                                   {{ $permissions->vendor_ledger ? 'checked' : '' }}>
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
                                   {{ $permissions->vendor_payment ? 'checked' : '' }}>
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
                                   {{ $permissions->inventory_access ? 'checked' : '' }}>
                                   <div class="perm-item sub-item">
    <div>
        <div>View Stock Value Card</div>
        <div class="perm-desc">Total Stock Value card dekh sakta hai</div>
    </div>
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox"
               name="inventory_stock_value"
               {{ $permissions->inventory_stock_value ? 'checked' : '' }}>
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
                                   {{ $permissions->inventory_prices ? 'checked' : '' }}>
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
                                   {{ $permissions->inventory_edit ? 'checked' : '' }}>
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
                                   {{ $permissions->inventory_add_stock ? 'checked' : '' }}>
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
                                   {{ $permissions->report_access ? 'checked' : '' }}>
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
                                   {{ $permissions->dashboard_access ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Save Permissions
                </button>
            </form>
            @endif
            @endif

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function loadStaff(userId) {
    window.location.href = '{{ route("settings.permissions") }}?user_id=' + userId;
}
</script>
@endpush