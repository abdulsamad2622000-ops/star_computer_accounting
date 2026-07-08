@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="cursor:pointer" onclick="openPayableModal()">
            <div class="stat-icon" style="background:#fef2f2"><i class="bi bi-arrow-up-circle" style="color:#ef4444"></i></div>
            <div class="stat-value">Rs. {{ number_format($totalPayable) }}</div>
            <div class="stat-label">Total Payable (Vendors)</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="cursor:pointer" onclick="openReceivableModal()">
            <div class="stat-icon" style="background:#f0fdf4"><i class="bi bi-arrow-down-circle" style="color:#22c55e"></i></div>
            <div class="stat-value">Rs. {{ number_format($totalReceivable) }}</div>
            <div class="stat-label">Total Receivable (Customers)</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff"><i class="bi bi-people" style="color:#4f8ef7"></i></div>
            <div class="stat-value">{{ $totalCustomers }}</div>
            <div class="stat-label">Total Customers</div>
        </div>
    </div>
    <div class="col-md-3">
    <div class="stat-card" style="cursor:pointer;background:#f0fdf4;border:1px solid #bbf7d0" onclick="openSalesModal('this_month')">
        <div class="stat-icon" style="background:#dcfce7"><i class="bi bi-graph-up-arrow" style="color:#16a34a"></i></div>
        <div class="stat-value" style="color:#16a34a">Rs. {{ number_format($monthTotal) }}</div>
        <div class="stat-label" style="color:#16a34a">
            📊 This Month Sales
            <span style="font-size:10px;display:block;margin-top:2px">
                {{ $monthInvoices }} invoices | {{ now()->format('F Y') }} <i class="bi bi-eye ms-1"></i>
            </span>
        </div>
    </div>
</div>
</div>

<div class="card mb-4" style="border:2px solid #163a6f;border-radius:10px;padding:16px;background:#e7f1ff">
    <div style="font-size:12px;color:#3e5a7a;font-weight:600;margin-bottom:8px">💰 Business Balance</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:12px;margin-bottom:10px">
        <div style="color:#3e5a7a">Stock Value</div>
        <div style="font-weight:700;color:#163a6f">Rs. {{ number_format($totalStockValue) }}</div>
        <div style="color:#3e5a7a">Receivable (Customer)</div>
        <div style="font-weight:700;color:#163a6f">Rs. {{ number_format($totalReceivable) }}</div>
        <div style="color:#3e5a7a">Payable (Vendor)</div>
        <div style="font-weight:700;color:#ef4444">Rs. {{ number_format($totalPayable) }}</div>
    </div>
    <div style="border-top:2px dashed #5a7ca8;padding-top:10px;display:flex;justify-content:space-between;align-items:center">
        <span style="font-weight:700;color:#163a6f;font-size:13px">= Net Balance</span>
        <div style="text-align:right">
            <div style="font-weight:900;font-size:20px;color:{{ $businessBalance >= 0 ? '#163a6f' : '#ef4444' }}">
                Rs. {{ number_format(abs($businessBalance)) }}
            </div>
            @if($businessBalance >= 0)
                <div style="font-size:11px;font-weight:700;color:#16a34a;background:#dcfce7;border-radius:20px;padding:2px 10px;display:inline-block;margin-top:3px">✅ Profit</div>
            @else
                <div style="font-size:11px;font-weight:700;color:#dc2626;background:#fee2e2;border-radius:20px;padding:2px 10px;display:inline-block;margin-top:3px">❌ Loss</div>
            @endif
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff"><i class="bi bi-box-seam" style="color:#4f8ef7"></i></div>
            <div class="stat-value">{{ $totalProducts }}</div>
            <div class="stat-label">Total Active Products</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4"><i class="bi bi-graph-up" style="color:#22c55e"></i></div>
            <div class="stat-value"><span style="font-size:0.8rem;color:#6b7280">PKR</span> {{ number_format($totalStockValue) }}</div>
            <div class="stat-label">Total Stock Value</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef2f2"><i class="bi bi-exclamation-triangle" style="color:#ef4444"></i></div>
            <div class="stat-value" style="color:#ef4444">{{ $lowStockCount }}</div>
            <div class="stat-label">Low Stock Alert</div>
        </div>
    </div>
    @if($totalLoss > 0)
    <div class="col-md-4">
        <div class="stat-card" style="background:#fef2f2;border:1px solid #fecaca;cursor:pointer" onclick="openLossModal('this_month')">
            <div class="stat-icon" style="background:#fee2e2"><i class="bi bi-graph-down-arrow" style="color:#ef4444"></i></div>
            <div class="stat-value" style="color:#ef4444">Rs. {{ number_format($totalLoss) }}</div>
            <div class="stat-label" style="color:#dc2626">
                📉 Below Cost Loss
                <span style="font-size:10px;display:block;margin-top:2px;color:#ef4444">
                    {{ now()->format('F Y') }} <i class="bi bi-eye ms-1"></i>
                </span>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff7ed"><i class="bi bi-receipt" style="color:#f59e0b"></i></div>
            <div class="stat-value">{{ $todayInvoices }}</div>
            <div class="stat-label">Today's Invoices</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4"><i class="bi bi-cash" style="color:#22c55e"></i></div>
            <div class="stat-value">Rs. {{ number_format($todayCash) }}</div>
            <div class="stat-label">Today's Cash</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef2f2"><i class="bi bi-clock-history" style="color:#ef4444"></i></div>
            <div class="stat-value">Rs. {{ number_format($todayCredit) }}</div>
            <div class="stat-label">Today's Credit</div>
        </div>
    </div>
</div>

{{-- PAYABLE MODAL --}}
<div class="modal fade" id="payableModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:12px;border:none;box-shadow:0 8px 32px rgba(0,0,0,0.15)">
            <div class="modal-header" style="background:#fef2f2;border-radius:12px 12px 0 0;border-bottom:1px solid #fecaca">
                <h6 class="modal-title fw-bold" style="color:#ef4444">📤 Vendors — Jinhe Pay Karna Hai</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                @php $unsettledVendors = \App\Models\Vendor::where('balance', '>', 0)->orderByDesc('balance')->get(); @endphp
                @if($unsettledVendors->count() > 0)
                <div style="padding:10px 16px;background:#fff7f7;border-bottom:1px solid #fecaca;font-size:12px;color:#ef4444;font-weight:600">
                    ⚠️ {{ $unsettledVendors->count() }} vendors ka payment pending hai
                </div>
                @endif
                <table class="table table-hover mb-0" style="font-size:13px">
                    <thead style="background:#f9fafb">
                        <tr>
                            <th class="px-3 py-2">#</th>
                            <th class="px-3 py-2">Vendor Name</th>
                            <th class="px-3 py-2">Contact</th>
                            <th class="px-3 py-2">Payable</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unsettledVendors as $i => $vendor)
                        <tr>
                            <td class="px-3 py-2 text-muted">{{ $i + 1 }}</td>
                            <td class="px-3 py-2 fw-bold">{{ $vendor->name }}</td>
                            <td class="px-3 py-2 text-muted">{{ $vendor->contact1 ?? '—' }}</td>
                            <td class="px-3 py-2"><span style="color:#ef4444;font-weight:700;font-size:14px">Rs. {{ number_format($vendor->balance) }}</span></td>
                            <td class="px-3 py-2"><a href="{{ route('vendors.show', $vendor) }}" class="btn btn-sm btn-outline-danger" style="font-size:11px;border-radius:6px">View Ledger →</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-check-circle" style="font-size:24px;color:#22c55e"></i><br><span style="font-size:13px">✅ Sab vendors settled hain!</span></td></tr>
                        @endforelse
                    </tbody>
                    @if($unsettledVendors->count() > 0)
                    <tfoot style="background:#fef2f2">
                        <tr>
                            <td colspan="3" class="px-3 py-2 fw-bold text-end" style="color:#374151">Total Payable:</td>
                            <td colspan="2" class="px-3 py-2 fw-bold" style="color:#ef4444;font-size:15px">Rs. {{ number_format($unsettledVendors->sum('balance')) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

{{-- RECEIVABLE MODAL --}}
<div class="modal fade" id="receivableModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:12px;border:none;box-shadow:0 8px 32px rgba(0,0,0,0.15)">
            <div class="modal-header" style="background:#f0fdf4;border-radius:12px 12px 0 0;border-bottom:1px solid #bbf7d0">
                <h6 class="modal-title fw-bold" style="color:#16a34a">📥 Customers — Jinse Lena Hai</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                @php $unsettledCustomers = \App\Models\Customer::where('balance', '>', 0)->orderByDesc('balance')->get(); @endphp
                @if($unsettledCustomers->count() > 0)
                <div style="padding:10px 16px;background:#f0fff4;border-bottom:1px solid #bbf7d0;font-size:12px;color:#16a34a;font-weight:600">
                    💰 {{ $unsettledCustomers->count() }} customers ka payment pending hai
                </div>
                @endif
                <table class="table table-hover mb-0" style="font-size:13px">
                    <thead style="background:#f9fafb">
                        <tr>
                            <th class="px-3 py-2">#</th>
                            <th class="px-3 py-2">Customer Name</th>
                            <th class="px-3 py-2">Contact</th>
                            <th class="px-3 py-2">Receivable</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unsettledCustomers as $i => $customer)
                        <tr>
                            <td class="px-3 py-2 text-muted">{{ $i + 1 }}</td>
                            <td class="px-3 py-2 fw-bold">{{ $customer->name }}</td>
                            <td class="px-3 py-2 text-muted">{{ $customer->contact1 ?? '—' }}</td>
                            <td class="px-3 py-2"><span style="color:#16a34a;font-weight:700;font-size:14px">Rs. {{ number_format($customer->balance) }}</span></td>
                            <td class="px-3 py-2"><a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-success" style="font-size:11px;border-radius:6px">View Ledger →</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-check-circle" style="font-size:24px;color:#22c55e"></i><br><span style="font-size:13px">✅ Sab customers settled hain!</span></td></tr>
                        @endforelse
                    </tbody>
                    @if($unsettledCustomers->count() > 0)
                    <tfoot style="background:#f0fdf4">
                        <tr>
                            <td colspan="3" class="px-3 py-2 fw-bold text-end" style="color:#374151">Total Receivable:</td>
                            <td colspan="2" class="px-3 py-2 fw-bold" style="color:#16a34a;font-size:15px">Rs. {{ number_format($unsettledCustomers->sum('balance')) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>




{{-- SALES MODAL --}}
<div class="modal fade" id="salesModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border-radius:12px;border:none;box-shadow:0 8px 32px rgba(0,0,0,0.15)">
            <div class="modal-header" style="background:#f0fdf4;border-radius:12px 12px 0 0;border-bottom:1px solid #bbf7d0">
                <h6 class="modal-title fw-bold" style="color:#16a34a">📊 Sales Report — Customer Wise</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div style="padding:10px 16px;background:#f0fff4;border-bottom:1px solid #bbf7d0;display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                <small style="color:#166534;font-weight:600">Filter:</small>
                <button onclick="loadSalesData('this_month')" id="sbtn_this_month"
                        class="btn btn-sm" style="font-size:11px;background:#16a34a;color:#fff;border-radius:20px">
                    📅 This Month
                </button>
                <button onclick="loadSalesData('last_month')" id="sbtn_last_month"
                        class="btn btn-sm btn-outline-success" style="font-size:11px;border-radius:20px">
                    📅 Last Month
                </button>
                <button onclick="loadSalesData('all')" id="sbtn_all"
                        class="btn btn-sm btn-outline-secondary" style="font-size:11px;border-radius:20px">
                    📅 All Time
                </button>
                <input type="date" id="sales_from" class="form-control form-control-sm" style="width:130px;font-size:11px">
                <span style="font-size:11px;color:#166534">to</span>
                <input type="date" id="sales_to" class="form-control form-control-sm" style="width:130px;font-size:11px">
                <button onclick="loadSalesData('custom')" class="btn btn-sm btn-outline-success" style="font-size:11px;border-radius:20px">Go</button>
            </div>
            <div id="salesSummaryBar" style="padding:10px 16px;background:#f9fafb;border-bottom:1px solid #e5e7eb;display:none">
            </div>
            <div class="modal-body p-0" id="salesModalBody">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-hourglass-split" style="font-size:24px"></i><br>
                    <span style="font-size:13px">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- LOSS MODAL --}}
<div class="modal fade" id="lossModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border-radius:12px;border:none;box-shadow:0 8px 32px rgba(0,0,0,0.15)">
            <div class="modal-header" style="background:#fef2f2;border-radius:12px 12px 0 0;border-bottom:1px solid #fecaca">
                <h6 class="modal-title fw-bold" style="color:#ef4444">📉 Below Cost Sales — Customer Wise List</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div style="padding:10px 16px;background:#fff7f7;border-bottom:1px solid #fecaca;display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                <small style="color:#92400e;font-weight:600">Filter:</small>
                <button onclick="loadLossData('this_month')" id="btn_this_month"
                        class="btn btn-sm" style="font-size:11px;background:#ef4444;color:#fff;border-radius:20px">
                    📅 This Month
                </button>
                <button onclick="loadLossData('last_month')" id="btn_last_month"
                        class="btn btn-sm btn-outline-danger" style="font-size:11px;border-radius:20px">
                    📅 Last Month
                </button>
                <button onclick="loadLossData('all')" id="btn_all"
                        class="btn btn-sm btn-outline-secondary" style="font-size:11px;border-radius:20px">
                    📅 All Time
                </button>
                <input type="date" id="loss_from" class="form-control form-control-sm" style="width:130px;font-size:11px">
                <span style="font-size:11px;color:#92400e">to</span>
                <input type="date" id="loss_to" class="form-control form-control-sm" style="width:130px;font-size:11px">
                <button onclick="loadLossData('custom')" class="btn btn-sm btn-outline-danger" style="font-size:11px;border-radius:20px">Go</button>
            </div>
            <div class="modal-body p-0" id="lossModalBody">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-hourglass-split" style="font-size:24px"></i><br>
                    <span style="font-size:13px">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openPayableModal() { new bootstrap.Modal(document.getElementById('payableModal')).show(); }
function openReceivableModal() { new bootstrap.Modal(document.getElementById('receivableModal')).show(); }

function openLossModal(filter) {
    new bootstrap.Modal(document.getElementById('lossModal')).show();
    loadLossData(filter || 'this_month');
}
function openSalesModal(filter) {
    new bootstrap.Modal(document.getElementById('salesModal')).show();
    loadSalesData(filter || 'this_month');
}

function loadSalesData(filter) {
    // Button active states
    ['this_month','last_month','all'].forEach(f => {
        const btn = document.getElementById('sbtn_' + f);
        if(btn) {
            btn.style.background = f === filter ? '#16a34a' : '';
            btn.style.color = f === filter ? '#fff' : '';
            btn.className = f === filter
                ? 'btn btn-sm'
                : 'btn btn-sm btn-outline-' + (f === 'all' ? 'secondary' : 'success');
            btn.style.borderRadius = '20px';
            btn.style.fontSize = '11px';
        }
    });

    const body    = document.getElementById('salesModalBody');
    const summary = document.getElementById('salesSummaryBar');
    body.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-success" style="width:2rem;height:2rem"></div></div>';
    summary.style.display = 'none';

    let url = '/dashboard/sales-data?filter=' + filter;
    if(filter === 'custom') {
        const from = document.getElementById('sales_from').value;
        const to   = document.getElementById('sales_to').value;
        if(!from || !to) { alert('Date range select karo!'); return; }
        url += '&from=' + from + '&to=' + to;
    }

    fetch(url, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(data => {
        // Summary bar
        summary.innerHTML = `
            <div style="display:flex;gap:20px;flex-wrap:wrap;font-size:12px">
                <div><span style="color:#6b7280">Total Sales:</span> <strong style="color:#16a34a">Rs. ${data.summary.total.toLocaleString()}</strong></div>
                <div><span style="color:#6b7280">Cash:</span> <strong style="color:#163a6f">Rs. ${data.summary.cash.toLocaleString()}</strong></div>
                <div><span style="color:#6b7280">Online:</span> <strong style="color:#4f8ef7">Rs. ${data.summary.online.toLocaleString()}</strong></div>
                <div><span style="color:#6b7280">Credit:</span> <strong style="color:#ef4444">Rs. ${data.summary.credit.toLocaleString()}</strong></div>
                <div><span style="color:#6b7280">Invoices:</span> <strong>${data.summary.invoices}</strong></div>
            </div>`;
        summary.style.display = 'block';

        if(!data.customers || data.customers.length === 0) {
            body.innerHTML = '<div class="text-center py-5 text-muted"><i class="bi bi-inbox" style="font-size:24px"></i><br><span style="font-size:13px">Is period mein koi sale nahi!</span></div>';
            return;
        }

        let html = '<table class="table table-hover mb-0" style="font-size:12px">';
        html += `<thead style="background:#f9fafb"><tr>
            <th class="px-3 py-2">#</th>
            <th class="px-3 py-2">Customer</th>
            <th class="px-3 py-2">Invoices</th>
            <th class="px-3 py-2">Total Sale</th>
            <th class="px-3 py-2">Cash</th>
            <th class="px-3 py-2">Online</th>
            <th class="px-3 py-2">Credit</th>
            <th class="px-3 py-2">Action</th>
        </tr></thead><tbody>`;

        data.customers.forEach((c, i) => {
            html += `<tr>
                <td class="px-3 py-2 text-muted">${i+1}</td>
                <td class="px-3 py-2 fw-bold">👤 ${c.name}</td>
                <td class="px-3 py-2">${c.invoices}</td>
                <td class="px-3 py-2 fw-bold" style="color:#16a34a">Rs. ${c.total.toLocaleString()}</td>
                <td class="px-3 py-2" style="color:#163a6f">Rs. ${c.cash.toLocaleString()}</td>
                <td class="px-3 py-2" style="color:#4f8ef7">Rs. ${c.online.toLocaleString()}</td>
                <td class="px-3 py-2" style="color:#ef4444">Rs. ${c.credit.toLocaleString()}</td>
                <td class="px-3 py-2">
                    ${c.cust_id ? `<a href="/customers/${c.cust_id}" class="btn btn-sm btn-outline-success" style="font-size:11px;border-radius:6px">Ledger →</a>` : '—'}
                </td>
            </tr>`;
        });

        html += '</tbody></table>';
        body.innerHTML = html;
    })
    .catch(() => {
        body.innerHTML = '<div class="text-center py-5 text-muted">Error loading data</div>';
    });
}
function loadLossData(filter) {
    // Button active states
    ['this_month','last_month','all'].forEach(f => {
        const btn = document.getElementById('btn_' + f);
        if(btn) {
            btn.style.background = f === filter ? '#ef4444' : '';
            btn.style.color = f === filter ? '#fff' : '';
            btn.className = f === filter
                ? 'btn btn-sm'
                : 'btn btn-sm btn-outline-' + (f === 'all' ? 'secondary' : 'danger');
            btn.style.borderRadius = '20px';
            btn.style.fontSize = '11px';
        }
    });

    const body = document.getElementById('lossModalBody');
    body.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-danger" style="width:2rem;height:2rem"></div></div>';

    let url = '/dashboard/loss-data?filter=' + filter;
    if(filter === 'custom') {
        const from = document.getElementById('loss_from').value;
        const to   = document.getElementById('loss_to').value;
        if(!from || !to) { alert('Date range select karo!'); return; }
        url += '&from=' + from + '&to=' + to;
    }

    fetch(url, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(data => {
        if(!data.items || data.items.length === 0) {
            body.innerHTML = '<div class="text-center py-5 text-muted"><i class="bi bi-check-circle" style="font-size:24px;color:#22c55e"></i><br><span style="font-size:13px">✅ Is period mein koi below cost sale nahi!</span></div>';
            return;
        }

        let html = '';
        data.items.forEach(group => {
            html += `
                <div style="padding:10px 16px;background:#fff7f7;border-bottom:2px solid #fecaca;display:flex;justify-content:space-between;align-items:center">
                    <span class="fw-bold" style="color:#dc2626;font-size:13px">👤 ${group.customer}</span>
                    <span style="color:#ef4444;font-weight:700;font-size:13px">Total Loss: Rs. ${group.total_loss.toLocaleString()}</span>
                </div>
                <table class="table table-hover mb-0" style="font-size:12px">
                    <thead style="background:#f9fafb">
                        <tr>
                            <th class="px-3 py-2">Invoice</th>
                            <th class="px-3 py-2">Date</th>
                            <th class="px-3 py-2">Product</th>
                            <th class="px-3 py-2">Qty</th>
                            <th class="px-3 py-2">Purchase Price</th>
                            <th class="px-3 py-2">Sale Price</th>
                            <th class="px-3 py-2">Loss/Unit</th>
                            <th class="px-3 py-2">Total Loss</th>
                        </tr>
                    </thead>
                    <tbody>`;
            group.sales.forEach(s => {
                html += `<tr>
                    <td class="px-3 py-2"><a href="/sales/${s.sale_id}/invoice" style="color:#4f8ef7;font-size:11px">${s.memo_no}</a></td>
                    <td class="px-3 py-2 text-muted">${s.date}</td>
                    <td class="px-3 py-2 fw-bold">${s.product}</td>
                    <td class="px-3 py-2">${s.qty}</td>
                    <td class="px-3 py-2 text-muted">Rs. ${s.purchase_price.toLocaleString()}</td>
                    <td class="px-3 py-2" style="color:#ef4444">Rs. ${s.sale_price.toLocaleString()}</td>
                    <td class="px-3 py-2" style="color:#ef4444">Rs. ${s.loss_per_unit.toLocaleString()}</td>
                    <td class="px-3 py-2 fw-bold" style="color:#dc2626">Rs. ${s.total_loss.toLocaleString()}</td>
                </tr>`;
            });
            html += `</tbody></table>`;
        });
        body.innerHTML = html;
    })
    .catch(() => {
        body.innerHTML = '<div class="text-center py-5 text-muted">Error loading data</div>';
    });
}
</script>
@endpush