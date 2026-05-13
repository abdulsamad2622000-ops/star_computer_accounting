@extends('layouts.app')
@section('title', 'Vendor Ledger')

@push('styles')
<style>
    .pay-form-wrap {
        background: #f0f6ff;
        border: 1px solid #5a7ca8;
        border-radius: 10px;
        padding: 12px 16px;
        margin-top: 20px;
    }
    .pay-form-wrap .form-control,
    .pay-form-wrap .form-select {
        font-size: 12px;
        padding: 5px 8px;
        height: 34px;
    }
    .pay-form-wrap label {
        font-size: 11px;
        font-weight: 600;
        color: #3e5a7a;
        margin-bottom: 3px;
    }
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
    .payment-row { background: #f0fdf4 !important; }
    .badge-purchase {
        background:#eff6ff;color:#163a6f;
        font-size:10px;padding:2px 7px;
        border-radius:4px;font-weight:700;
    }
    .badge-payment {
        background:#f0fdf4;color:#166534;
        font-size:10px;padding:2px 7px;
        border-radius:4px;font-weight:700;
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

<!-- Vendor Info -->
<div class="form-card mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div class="row g-3 flex-grow-1">
            <div class="col-md-2">
                <div class="stat-label">Name</div>
                <div class="fw-bold">{{ $vendor->name }}</div>
            </div>
            <div class="col-md-2">
                <div class="stat-label">Contact 1</div>
                <div>{{ $vendor->contact1 ?? '—' }}</div>
            </div>
            <div class="col-md-2">
                <div class="stat-label">Contact 2</div>
                <div>{{ $vendor->contact2 ?? '—' }}</div>
            </div>
            <div class="col-md-2">
                <div class="stat-label">CNIC</div>
                <div>{{ $vendor->cnic ?? '—' }}</div>
            </div>
            <div class="col-md-2">
                <div class="stat-label">Address</div>
                <div>{{ $vendor->address ?? '—' }}</div>
            </div>
            <div class="col-md-2">
                <div class="stat-label">Opening Balance</div>
                <div class="memo-no">Rs. {{ number_format($vendor->opening_balance) }}</div>
            </div>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            <button onclick="window.print()"
                    class="btn btn-sm btn-outline-secondary no-print">
                <i class="bi bi-printer"></i> Print
            </button>
            <a href="{{ route('vendors.ledger.pdf', $vendor) }}"
               class="btn btn-sm btn-outline-danger no-print">
                <i class="bi bi-file-pdf"></i> PDF
            </a>
            <a href="{{ route('vendors.ledger.excel', $vendor) }}"
               class="btn btn-sm btn-outline-success no-print">
                <i class="bi bi-file-excel"></i> Excel
            </a>
            <form action="{{ route('vendors.ledger.delete', $vendor) }}"
                  method="POST"
                  onsubmit="return confirm('Poora ledger delete karein?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger no-print">
                    <i class="bi bi-trash"></i> Delete Ledger
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef2f2">
                <i class="bi bi-receipt" style="color:#ef4444"></i>
            </div>
            <div class="stat-value">Rs. {{ number_format($totalBill) }}</div>
            <div class="stat-label">Total Bill</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4">
                <i class="bi bi-cash" style="color:#22c55e"></i>
            </div>
            <div class="stat-value">Rs. {{ number_format($cashTotal) }}</div>
            <div class="stat-label">💵 Cash Paid</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff">
                <i class="bi bi-phone" style="color:#4f8ef7"></i>
            </div>
            <div class="stat-value">Rs. {{ number_format($onlineTotal) }}</div>
            <div class="stat-label">🏦 Online Paid</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff7ed">
                <i class="bi bi-file-text" style="color:#f59e0b"></i>
            </div>
            <div class="stat-value">Rs. {{ number_format($chequeTotal) }}</div>
            <div class="stat-label">📝 Cheque Paid</div>
        </div>
    </div>
</div>

<!-- Balance Card -->
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="stat-card" style="background:#fff7ed;border:1px solid #fed7aa">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label" style="color:#92400e">Balance (Payable)</div>
                    <div class="stat-value" style="color:#f59e0b;font-size:1.8rem">
                        Rs. {{ number_format($balance) }}
                    </div>
                </div>
                <i class="bi bi-wallet2" style="color:#f59e0b;font-size:2rem"></i>
            </div>
        </div>
    </div>
</div>

@if($totalLoss > 0)
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="stat-card" style="background:#fef2f2;border:1px solid #fecaca">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label" style="color:#dc2626">
                        ⚠️ Total Loss (Sale Price &lt; Purchase Price)
                    </div>
                    <div class="stat-value" style="color:#ef4444;font-size:1.8rem">
                        Rs. {{ number_format($totalLoss) }}
                    </div>
                </div>
                <i class="bi bi-graph-down-arrow" style="color:#ef4444;font-size:2rem"></i>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Ledger Table -->
<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">
            <i class="bi bi-journal-text me-2"></i>Ledger — {{ $vendor->name }}
        </span>
        <button onclick="resetLedgerFilters()"
                class="btn btn-sm btn-outline-secondary no-print">
            🔄 Reset Filters
        </button>
    </div>
    <div class="table-responsive">
        <table class="table mb-0" id="ledgerTable">
            <thead>
                <tr>
                    <th>
                        <div class="col-filter-label">Date</div>
                        <select class="col-filter-select" onchange="filterLedger(this,0)">
                            <option value="">All</option>
                            @php
                                $allDates = $purchases->pluck('date')
                                    ->merge($payments->pluck('date'))
                                    ->unique()->sort();
                            @endphp
                            @foreach($allDates as $d)
                            <option value="{{ \Carbon\Carbon::parse($d)->format('d-m-Y') }}">
                                {{ \Carbon\Carbon::parse($d)->format('d-m-Y') }}
                            </option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Memo #</div>
                        <select class="col-filter-select" onchange="filterLedger(this,1)">
                            <option value="">All</option>
                            @foreach($purchases->pluck('memo_no')->unique() as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Type</div>
                        <select class="col-filter-select" onchange="filterLedger(this,2)">
                            <option value="">All</option>
                            <option value="Purchase">Purchase</option>
                            <option value="Payment">Payment</option>
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Item</div>
                        <select class="col-filter-select" onchange="filterLedger(this,3)">
                            <option value="">All</option>
                            @foreach($purchases->flatMap->items->pluck('product.name')->filter()->unique()->sort() as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Stock Code</div>
                        <select class="col-filter-select" onchange="filterLedger(this,4)">
                            <option value="">All</option>
                            @foreach($purchases->flatMap->items->pluck('stock_code')->filter()->unique()->sort() as $code)
                            <option value="{{ $code }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Qty</div>
                        <select class="col-filter-select" onchange="filterLedger(this,5)">
                            <option value="">All</option>
                            @foreach($purchases->flatMap->items->pluck('qty')->unique()->sort() as $q)
                            <option value="{{ $q }}">{{ $q }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Bill Amt</div>
                        <select class="col-filter-select" onchange="filterLedger(this,6)">
                            <option value="">All</option>
                            @foreach($purchases->flatMap->items->pluck('total')->unique()->sort() as $t)
                            <option value="Rs. {{ number_format($t) }}">Rs. {{ number_format($t) }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Paid</div>
                        <select class="col-filter-select" onchange="filterLedger(this,7)">
                            <option value="">All</option>
                            @foreach($payments->pluck('amount')->unique()->sort() as $a)
                            <option value="Rs. {{ number_format($a) }}">Rs. {{ number_format($a) }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Method</div>
                        <select class="col-filter-select" onchange="filterLedger(this,8)">
                            <option value="">All</option>
                            <option value="Cash">Cash</option>
                            <option value="Online">Online</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Reference</div>
                        <select class="col-filter-select" onchange="filterLedger(this,9)">
                            <option value="">All</option>
                            @foreach($payments->pluck('platform')->filter()->merge($payments->pluck('bank_name')->filter())->unique()->sort() as $ref)
                            <option value="{{ $ref }}">{{ $ref }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <div class="col-filter-label">Description</div>
                        <select class="col-filter-select" onchange="filterLedger(this,10)">
                            <option value="">All</option>
                            @foreach($purchases->flatMap->items->pluck('description')->filter()->unique()->sort() as $desc)
                            <option value="{{ $desc }}">{{ $desc }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th><div class="col-filter-label">Action</div></th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchases as $purchase)
                    @foreach($purchase->items as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($purchase->date)->format('d-m-Y') }}</td>
                        <td><span class="memo-no">{{ $purchase->memo_no }}</span></td>
                        <td><span class="badge-purchase">Purchase</span></td>
                        <td>{{ $item->product->name ?? '—' }}</td>
                        <td>
                            @if($item->stock_code)
                                <span class="memo-no">{{ $item->stock_code }}</span>
                            @else —
                            @endif
                        </td>
                        <td>{{ $item->qty }}</td>
                        <td>Rs. {{ number_format($item->total) }}</td>
                        <td>
                            @if($loop->first)
                                Rs. {{ number_format($purchase->paid) }}
                            @else —
                            @endif
                        </td>
                        <td>—</td>
                        <td>—</td>
                        <td>{{ $item->description ?? '—' }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary no-print"
                                    onclick="editPurchase({{ $purchase->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('reports.destroy', $purchase) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete karein?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger no-print">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                @endforeach

                @foreach($payments as $payment)
                <tr class="payment-row">
                    <td>{{ \Carbon\Carbon::parse($payment->date)->format('d-m-Y') }}</td>
                    <td><span class="memo-no">PAY</span></td>
                    <td><span class="badge-payment">Payment</span></td>
                    <td>—</td><td>—</td><td>—</td><td>—</td>
                    <td style="color:#22c55e;font-weight:700">
                        Rs. {{ number_format($payment->amount) }}
                    </td>
                    <td>
                        <span class="badge
                            @if($payment->method=='cash') bg-success
                            @elseif($payment->method=='online') bg-primary
                            @else bg-warning text-dark @endif"
                            style="font-size:10px">
                            {{ ucfirst($payment->method ?? 'cash') }}
                        </span>
                    </td>
                    <td style="font-size:11px">
                        @if($payment->method == 'online')
                            {{ $payment->platform }}<br>
                            <small>{{ $payment->account_number }}</small>
                            @if($payment->account_title)
                                <br><small>{{ $payment->account_title }}</small>
                            @endif
                        @elseif($payment->method == 'cheque')
                            {{ $payment->bank_name }} #{{ $payment->cheque_no }}<br>
                            <small>Date: {{ $payment->cheque_date
                                ? \Carbon\Carbon::parse($payment->cheque_date)->format('d-m-Y')
                                : '—' }}</small>
                        @else —
                        @endif
                    </td>
                    <td>{{ $payment->note ?? '—' }}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary no-print"
                                onclick="editPayment(
                                    {{ $payment->id }},
                                    '{{ $payment->date }}',
                                    {{ $payment->amount }},
                                    '{{ $payment->method ?? 'cash' }}',
                                    '{{ $payment->platform ?? '' }}',
                                    '{{ $payment->account_number ?? '' }}',
                                    '{{ $payment->account_title ?? '' }}',
                                    '{{ $payment->cheque_no ?? '' }}',
                                    '{{ $payment->cheque_date ?? '' }}',
                                    '{{ $payment->bank_name ?? '' }}',
                                    '{{ $payment->note ?? '' }}'
                                )">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @if($payment->method == 'cheque')
                        <button class="btn btn-sm btn-outline-warning no-print"
                                onclick="reschedule({{ $payment->id }},'{{ $payment->cheque_date }}')"
                                title="Reschedule">📅</button>
                        @endif
                        <form action="{{ route('vendors.payment.delete', $payment) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Payment delete karein?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger no-print">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach

                @if($purchases->isEmpty() && $payments->isEmpty())
                <tr>
                    <td colspan="12" class="text-center text-muted py-4">
                        Koi transaction nahi mili
                    </td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr style="background:#f4f6fb;font-weight:700">
                    <td colspan="6" class="text-end">Total:</td>
                    <td>Rs. {{ number_format($totalBill) }}</td>
                    <td>Rs. {{ number_format($totalPaid) }}</td>
                    <td colspan="4"></td>
                </tr>
                <tr style="background:#fff7ed;font-weight:700">
                    <td colspan="6" class="text-end">Balance (Payable):</td>
                    <td colspan="2" style="color:#f59e0b">
                        Rs. {{ number_format($balance) }}
                    </td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Send Payment Form -->
<div class="pay-form-wrap no-print">
    <div class="fw-bold mb-2" style="color:#163a6f;font-size:13px">💳 Send Payment</div>
    <form action="{{ route('vendors.payment.store', $vendor) }}" method="POST">
        @csrf
        <div class="d-flex gap-2 align-items-end flex-wrap">
            <div>
                <label>Date *</label>
                <input type="date" name="date" class="form-control"
                       value="{{ date('Y-m-d') }}" style="width:140px" required>
            </div>
            <div>
                <label>Amount *</label>
                <input type="number" name="amount" class="form-control"
                       placeholder="Rs. 0" style="width:130px" min="1" required>
            </div>
            <div>
                <label>Method *</label>
                <select name="method" id="payMethod" class="form-select"
                        style="width:130px" onchange="toggleFields()" required>
                    <option value="cash">💵 Cash</option>
                    <option value="online">🏦 Online</option>
                    <option value="cheque">📝 Cheque</option>
                </select>
            </div>
            <div id="onlineFields" style="display:none !important"
                 class="d-flex gap-2 align-items-end flex-wrap">
                <div>
                    <label>Platform</label>
                    <input type="text" name="platform" id="platformSel"
                           class="form-control" list="bankList"
                           placeholder="e.g. Meezan Bank" style="width:150px">
                    <datalist id="bankList">
                        @foreach(\App\Models\BusinessBank::all() as $bank)
                        <option value="{{ $bank->bank_name }}"
                                data-account="{{ $bank->account_number }}"
                                data-title="{{ $bank->account_title }}">
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label>Account Number</label>
                    <input type="text" name="account_number" id="accNumber"
                           class="form-control" placeholder="03XX-XXXXXXX"
                           style="width:150px">
                </div>
                <div>
                    <label>Account Title</label>
                    <input type="text" name="account_title" id="accTitle"
                           class="form-control" placeholder="Name..."
                           style="width:130px">
                </div>
            </div>
            <div id="chequeFields" style="display:none !important"
                 class="d-flex gap-2 align-items-end flex-wrap">
                <div>
                    <label>Cheque No *</label>
                    <input type="text" name="cheque_no" id="chequeNo"
                           class="form-control" placeholder="Cheque No."
                           style="width:130px">
                </div>
                <div>
                    <label>Cheque Date *</label>
                    <input type="date" name="cheque_date" id="chequeDt"
                           class="form-control" style="width:140px">
                </div>
                <div>
                    <label>Bank Name *</label>
                    <input type="text" name="bank_name" id="bankNm"
                           class="form-control" placeholder="Bank Name"
                           style="width:130px">
                </div>
            </div>
            <div>
                <label>Note <small>(Optional)</small></label>
                <input type="text" name="note" class="form-control"
                       placeholder="Optional..." style="width:150px">
            </div>
            <button type="submit" class="btn btn-success btn-sm"
                    style="height:34px;padding:0 16px">✅ Save</button>
        </div>
    </form>
</div>

<!-- Edit Purchase Modal -->
<div class="modal fade" id="editPurchaseModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">✏️ Edit Purchase Transaction</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Date *</label>
                        <input type="date" id="ep2_date" class="form-control" required>
                    </div>
                    <div class="col-md-3" style="position:relative">
                        <label class="form-label fw-bold">Vendor *</label>
                        <input type="text" id="ep2_vendor_search"
                               class="form-control"
                               placeholder="Type to search..."
                               autocomplete="off"
                               oninput="searchEditVendor(this.value)">
                        <input type="hidden" id="ep2_vendor_id">
                        <div id="ep2_vendor_list"
                             style="display:none;position:absolute;top:100%;left:0;right:0;
                                    background:#fff;border:1px solid #5a7ca8;border-radius:6px;
                                    max-height:160px;overflow-y:auto;z-index:9999;
                                    box-shadow:0 4px 12px rgba(0,0,0,.1)"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Payment Type</label>
                        <select id="ep2_payment_type" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="credit">Credit</option>
                            <option value="partial">Partial</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Discount</label>
                        <input type="number" id="ep2_discount" class="form-control"
                               value="0" min="0" oninput="recalcEditPurchaseTotals()">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Cash Paid</label>
                        <input type="number" id="ep2_paid" class="form-control"
                               value="0" min="0" oninput="recalcEditPurchaseTotals()">
                    </div>
                </div>
                <table class="table table-sm" id="editPurchaseItemsTable">
                    <thead>
                        <tr style="background:#f0f6ff">
                            <th>#</th>
                            <th>Product *</th>
                            <th>Description</th>
                            <th>Stock Code</th>
                            <th style="width:80px">Qty *</th>
                            <th style="width:110px">Purchase Price *</th>
                            <th style="width:110px">Sale Price</th>
                            <th style="width:110px">Amount</th>
                            <th style="width:40px"></th>
                        </tr>
                    </thead>
                    <tbody id="editPurchaseItemsBody"></tbody>
                    <tfoot>
                        <tr style="background:#f4f8ff;font-weight:700">
                            <td colspan="7" class="text-end">Total:</td>
                            <td id="ep2_total_cell">0</td><td></td>
                        </tr>
                        <tr style="background:#fff7ed;font-weight:700">
                            <td colspan="7" class="text-end">Net Total:</td>
                            <td id="ep2_net_total">0</td><td></td>
                        </tr>
                        <tr style="background:#fef2f2;font-weight:700">
                            <td colspan="7" class="text-end">Balance:</td>
                            <td id="ep2_balance">0</td><td></td>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-sm btn-outline-success"
                        onclick="addEditPurchaseRow()">➕ Add Item</button>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary btn-sm"
                        data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm"
                        id="saveEditPurchaseBtn" onclick="saveEditPurchase()">
                    💾 Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Payment Modal -->
<div class="modal fade" id="editPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">✏️ Edit Transaction</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPaymentForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Date *</label>
                            <input type="date" name="date" id="ep_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Amount *</label>
                            <input type="number" name="amount" id="ep_amount" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Method *</label>
                            <select name="method" id="ep_method" class="form-select" onchange="toggleEditFields()">
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                        <div id="ep_online_fields" class="col-12" style="display:none">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Platform</label>
                                    <input type="text" name="platform" id="ep_platform"
                                           class="form-control" list="ep_bankList"
                                           placeholder="e.g. Meezan Bank">
                                    <datalist id="ep_bankList">
                                        @foreach(\App\Models\BusinessBank::all() as $bank)
                                        <option value="{{ $bank->bank_name }}">
                                        @endforeach
                                    </datalist>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" name="account_number"
                                           id="ep_account_number" class="form-control"
                                           placeholder="03XX-XXXXXXX">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Account Title</label>
                                    <input type="text" name="account_title"
                                           id="ep_account_title" class="form-control"
                                           placeholder="Name...">
                                </div>
                            </div>
                        </div>
                        <div id="ep_cheque_fields" class="col-12" style="display:none">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Cheque No</label>
                                    <input type="text" name="cheque_no" id="ep_cheque_no"
                                           class="form-control" placeholder="Cheque No.">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Cheque Date</label>
                                    <input type="date" name="cheque_date" id="ep_cheque_date"
                                           class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Bank Name</label>
                                    <input type="text" name="bank_name" id="ep_bank_name"
                                           class="form-control" placeholder="Bank Name">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note <small class="text-muted">(Optional)</small></label>
                            <input type="text" name="note" id="ep_note"
                                   class="form-control" placeholder="Optional...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">💾 Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">📅 Reschedule Cheque</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rescheduleForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <label class="form-label">New Cheque Date *</label>
                    <input type="date" name="cheque_date" id="new_cheque_date"
                           class="form-control" required>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning btn-sm">📅 Reschedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const allProductsForEdit  = @json(\App\Models\Product::where('is_active', true)->get());
const allVendorsForEdit   = @json(\App\Models\Vendor::orderBy('name')->get());
let currentEditPurchaseId = null;
let editPurchaseRowCounter = 0;

// ── Send Payment Toggle ───────────────────────────────────
function toggleFields() {
    const method = document.getElementById('payMethod').value;
    const online = document.getElementById('onlineFields');
    const cheque = document.getElementById('chequeFields');
    online.setAttribute('style', 'display:none !important');
    cheque.setAttribute('style', 'display:none !important');
    ['chequeNo','chequeDt','bankNm'].forEach(id => {
        document.getElementById(id)?.removeAttribute('required');
    });
    if (method === 'online') {
        online.setAttribute('style', 'display:flex !important');
    } else if (method === 'cheque') {
        cheque.setAttribute('style', 'display:flex !important');
        document.getElementById('chequeNo').setAttribute('required','required');
        document.getElementById('chequeDt').setAttribute('required','required');
        document.getElementById('bankNm').setAttribute('required','required');
    }
}

// ── Edit Payment Toggle ──────────────────────────────────
function toggleEditFields() {
    const method = document.getElementById('ep_method').value;
    document.getElementById('ep_online_fields').style.display =
        method === 'online' ? 'block' : 'none';
    document.getElementById('ep_cheque_fields').style.display =
        method === 'cheque' ? 'block' : 'none';
}

// ── Ledger Filter ────────────────────────────────────────
function filterLedger(select, colIndex) {
    document.querySelectorAll('#ledgerTable tbody tr')
        .forEach(row => checkAllLedgerFilters(row));
}

function checkAllLedgerFilters(row) {
    const selects = document.querySelectorAll('#ledgerTable thead select');
    let show = true;
    selects.forEach((select, i) => {
        const val  = select.value.toLowerCase();
        const cell = row.cells[i];
        if (cell && val && !cell.textContent.toLowerCase().includes(val)) show = false;
    });
    row.style.display = show ? '' : 'none';
}

function resetLedgerFilters() {
    document.querySelectorAll('#ledgerTable thead select')
        .forEach(s => { s.value = ''; });
    document.querySelectorAll('#ledgerTable tbody tr')
        .forEach(r => { r.style.display = ''; });
}

// ── Vendor Search (Edit Modal) ───────────────────────────
function searchEditVendor(query) {
    const list = document.getElementById('ep2_vendor_list');
    if (!query) { list.style.display = 'none'; return; }
    const filtered = allVendorsForEdit.filter(v =>
        v.name.toLowerCase().includes(query.toLowerCase()) ||
        (v.contact1 && v.contact1.includes(query))
    );
    if (!filtered.length) { list.style.display = 'none'; return; }
    list.innerHTML = filtered.map(v => `
        <div onclick="selectEditVendor(${v.id},'${v.name}')"
             style="padding:7px 10px;font-size:12px;cursor:pointer;
                    border-bottom:1px solid #f0f0f0;color:#0e2a4f"
             onmouseover="this.style.background='#e7f1ff'"
             onmouseout="this.style.background='#fff'">
            <strong>${v.name}</strong>
            ${v.contact1 ? `<span style="color:#5a7ca8;margin-left:6px">${v.contact1}</span>` : ''}
        </div>`).join('');
    list.style.display = 'block';
}

function selectEditVendor(id, name) {
    document.getElementById('ep2_vendor_id').value     = id;
    document.getElementById('ep2_vendor_search').value = name;
    document.getElementById('ep2_vendor_list').style.display = 'none';
}

// ── Edit Purchase ────────────────────────────────────────
async function editPurchase(purchaseId) {
    currentEditPurchaseId  = purchaseId;
    editPurchaseRowCounter = 0;

    document.getElementById('editPurchaseItemsBody').innerHTML = `
        <tr><td colspan="9" class="text-center py-3">
            <div class="spinner-border spinner-border-sm"></div> Loading...
        </td></tr>`;

    new bootstrap.Modal(document.getElementById('editPurchaseModal')).show();

    const res  = await fetch(`/purchases/${purchaseId}/edit-data`);
    const data = await res.json();

    if (!data.success) { alert('❌ Data load nahi hua!'); return; }

    const purchase = data.sale;

    document.getElementById('ep2_date').value         = purchase.date;
    document.getElementById('ep2_vendor_id').value    = purchase.vendor_id;
    document.getElementById('ep2_payment_type').value = purchase.payment_type;
    document.getElementById('ep2_discount').value     = purchase.discount ?? 0;
    document.getElementById('ep2_paid').value         = purchase.paid     ?? 0;

    const vend = allVendorsForEdit.find(v => v.id == purchase.vendor_id);
    document.getElementById('ep2_vendor_search').value = vend?.name ?? '';

    document.getElementById('editPurchaseItemsBody').innerHTML = '';
    purchase.items.forEach(item => { addEditPurchaseRow(item); });

    recalcEditPurchaseTotals();
}

function getEditPurchaseRowTemplate(rowId, item = null) {
    return `
        <tr id="eprow_${rowId}">
            <td>${rowId}</td>
            <td style="position:relative">
                <input type="text" id="epprod_name_${rowId}"
                       class="form-control form-control-sm"
                       placeholder="Type to search..." autocomplete="off"
                       value="${item ? (allProductsForEdit.find(p => p.id == item.product_id)?.name ?? '') : ''}"
                       oninput="searchEditPurchaseProduct(this.value,${rowId})"
                       style="min-width:180px">
                <input type="hidden" id="epprod_${rowId}" value="${item?.product_id ?? ''}">
                <div id="eplist_${rowId}"
                     style="display:none;position:absolute;top:100%;left:0;right:0;
                            background:#fff;border:1px solid #5a7ca8;border-radius:6px;
                            max-height:160px;overflow-y:auto;z-index:9999;
                            box-shadow:0 4px 12px rgba(0,0,0,.1)"></div>
            </td>
            <td>
                <input type="text" id="epdesc_${rowId}" class="form-control form-control-sm"
                       value="${item?.description ?? ''}" placeholder="Optional">
            </td>
            <td>
                <input type="text" id="epcode_${rowId}" class="form-control form-control-sm"
                       value="${item?.stock_code ?? ''}" style="width:90px">
            </td>
            <td>
                <input type="number" id="epqty_${rowId}" class="form-control form-control-sm"
                       value="${item?.qty ?? 1}" min="1"
                       oninput="recalcEditPurchaseRow(${rowId})" style="width:70px">
            </td>
            <td>
                <input type="number" id="eprate_${rowId}" class="form-control form-control-sm"
                       value="${item?.rate ?? 0}" min="0" step="0.01"
                       oninput="recalcEditPurchaseRow(${rowId})" style="width:100px">
            </td>
            <td>
                <input type="number" id="epsp_${rowId}" class="form-control form-control-sm"
                       value="${item ? (allProductsForEdit.find(p => p.id == item.product_id)?.sale_price ?? 0) : 0}"
                       min="0" step="0.01" style="width:100px">
            </td>
            <td>
                <input type="number" id="epamt_${rowId}" class="form-control form-control-sm"
                       value="${item ? (item.qty * item.rate).toFixed(2) : 0}"
                       readonly style="width:100px">
            </td>
            <td>
                <button onclick="removeEditPurchaseRow(${rowId})"
                        class="btn btn-sm btn-outline-danger">✕</button>
            </td>
        </tr>`;
}

function searchEditPurchaseProduct(query, rowId) {
    const list = document.getElementById(`eplist_${rowId}`);
    if (!query) { list.style.display = 'none'; return; }
    const filtered = allProductsForEdit.filter(p =>
        p.name.toLowerCase().includes(query.toLowerCase()) ||
        (p.stock_code && p.stock_code.toLowerCase().includes(query.toLowerCase()))
    );
    if (!filtered.length) { list.style.display = 'none'; return; }
    list.innerHTML = filtered.map(p => `
        <div onclick="selectEditPurchaseProduct(${p.id},'${p.name}','${p.stock_code ?? ''}',${p.purchase_price},${p.sale_price},${rowId})"
             style="padding:7px 10px;font-size:12px;cursor:pointer;
                    border-bottom:1px solid #f0f0f0;color:#0e2a4f"
             onmouseover="this.style.background='#e7f1ff'"
             onmouseout="this.style.background='#fff'">
            <strong>${p.name}</strong>
            ${p.stock_code ? `<span style="color:#5a7ca8;margin-left:6px">${p.stock_code}</span>` : ''}
            <span style="color:#22c55e;float:right">Stock: ${p.remaining_qty}</span>
        </div>`).join('');
    list.style.display = 'block';
}

function selectEditPurchaseProduct(id, name, code, pp, sp, rowId) {
    document.getElementById(`epprod_${rowId}`).value      = id;
    document.getElementById(`epprod_name_${rowId}`).value = name;
    document.getElementById(`epcode_${rowId}`).value      = code;
    document.getElementById(`eprate_${rowId}`).value      = pp;
    document.getElementById(`epsp_${rowId}`).value        = sp;
    document.getElementById(`eplist_${rowId}`).style.display = 'none';
    recalcEditPurchaseRow(rowId);
}

function addEditPurchaseRow(item = null) {
    editPurchaseRowCounter++;
    document.getElementById('editPurchaseItemsBody')
        .insertAdjacentHTML('beforeend',
            getEditPurchaseRowTemplate(editPurchaseRowCounter, item));
    recalcEditPurchaseTotals();
}

function removeEditPurchaseRow(rowId) {
    document.getElementById(`eprow_${rowId}`)?.remove();
    recalcEditPurchaseTotals();
}

function recalcEditPurchaseRow(rowId) {
    const qty  = parseFloat(document.getElementById(`epqty_${rowId}`)?.value  || 0);
    const rate = parseFloat(document.getElementById(`eprate_${rowId}`)?.value || 0);
    document.getElementById(`epamt_${rowId}`).value = (qty * rate).toFixed(2);
    recalcEditPurchaseTotals();
}

function recalcEditPurchaseTotals() {
    let total = 0;
    document.querySelectorAll('[id^="epamt_"]').forEach(el => {
        total += parseFloat(el.value || 0);
    });
    const discount = parseFloat(document.getElementById('ep2_discount')?.value || 0);
    const paid     = parseFloat(document.getElementById('ep2_paid')?.value     || 0);
    const net      = Math.max(total - discount, 0);
    const balance  = Math.max(net - paid, 0);
    document.getElementById('ep2_total_cell').textContent = 'Rs. ' + total.toFixed(2);
    document.getElementById('ep2_net_total').textContent  = 'Rs. ' + net.toFixed(2);
    document.getElementById('ep2_balance').textContent    = 'Rs. ' + balance.toFixed(2);
}

async function saveEditPurchase() {
    const items = [];
    document.querySelectorAll('#editPurchaseItemsBody tr').forEach(row => {
        const rowId     = row.id.replace('eprow_', '');
        const productId = document.getElementById(`epprod_${rowId}`)?.value;
        const qty       = parseInt(document.getElementById(`epqty_${rowId}`)?.value  || 0);
        const rate      = parseFloat(document.getElementById(`eprate_${rowId}`)?.value || 0);
        const sp        = parseFloat(document.getElementById(`epsp_${rowId}`)?.value   || 0);
        const code      = document.getElementById(`epcode_${rowId}`)?.value;
        const desc      = document.getElementById(`epdesc_${rowId}`)?.value;
        if (!productId || qty < 1) return;
        items.push({ product_id: productId, qty, rate, sale_price: sp, stock_code: code, description: desc });
    });

    if (!items.length) { alert('Koi item nahi!'); return; }

    const btn = document.getElementById('saveEditPurchaseBtn');
    btn.disabled = true; btn.textContent = '⏳ Saving...';

    try {
        const res = await fetch(`/purchases/${currentEditPurchaseId}/update`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                date:         document.getElementById('ep2_date').value,
                vendor_id:    document.getElementById('ep2_vendor_id').value,
                payment_type: document.getElementById('ep2_payment_type').value,
                discount:     document.getElementById('ep2_discount').value,
                paid:         document.getElementById('ep2_paid').value,
                items
            })
        });
        const data = await res.json();
        if (data.success) {
            bootstrap.Modal.getInstance(
                document.getElementById('editPurchaseModal')
            ).hide();
            alert('✅ Purchase update ho gaya!');
            location.reload();
        } else { alert('❌ ' + data.message); }
    } catch(e) { alert('❌ Server error!'); }

    btn.disabled = false; btn.textContent = '💾 Save Changes';
}

// ── Edit Payment ─────────────────────────────────────────
function editPayment(id, date, amount, method, platform,
                      accNum, accTitle, chequeNo, chequeDate, bank, note) {
    document.getElementById('editPaymentForm').action = '/vendor-payments/' + id;
    document.getElementById('ep_date').value           = date;
    document.getElementById('ep_amount').value         = amount;
    document.getElementById('ep_method').value         = method;
    document.getElementById('ep_platform').value       = platform   || '';
    document.getElementById('ep_account_number').value = accNum     || '';
    document.getElementById('ep_account_title').value  = accTitle   || '';
    document.getElementById('ep_cheque_no').value      = chequeNo   || '';
    document.getElementById('ep_cheque_date').value    = chequeDate || '';
    document.getElementById('ep_bank_name').value      = bank       || '';
    document.getElementById('ep_note').value           = note       || '';
    toggleEditFields();
    new bootstrap.Modal(document.getElementById('editPaymentModal')).show();
}

// ── Reschedule ───────────────────────────────────────────
function reschedule(id, currentDate) {
    document.getElementById('rescheduleForm').action =
        '/vendor-payments/' + id + '/reschedule';
    document.getElementById('new_cheque_date').value = currentDate || '';
    new bootstrap.Modal(document.getElementById('rescheduleModal')).show();
}

document.addEventListener('click', function(e) {
    ['ep2_vendor_list'].forEach(id => {
        const el = document.getElementById(id);
        if (el && !el.parentElement?.contains(e.target)) el.style.display = 'none';
    });
    document.querySelectorAll('[id^="eplist_"]').forEach(list => {
        if (!list.parentElement?.contains(e.target)) list.style.display = 'none';
    });
});
</script>
@endpush