@extends('layouts.app')
@section('title', 'Purchase Point')

@push('styles')
<style>
    :root {
        --brand-blue: #e7f1ff;
        --border: #5a7ca8;
        --text: #0e2a4f;
        --muted: #3e5a7a;
        --accent: #163a6f;
    }
    body { background: #f7fbff !important; }
    .receipt { width:860px; margin:0 auto 24px; background:var(--brand-blue); border:2px solid var(--border); border-radius:10px; padding:18px 18px 12px; box-shadow:0 4px 18px rgba(22,58,111,0.12); }
    .header { display:grid; grid-template-columns:1.3fr 1fr; gap:12px; align-items:start; border-bottom:2px dashed var(--border); padding-bottom:12px; margin-bottom:10px; }
    .brand-block h1 { margin:0; font-size:26px; letter-spacing:0.8px; color:var(--accent); font-weight:900; }
    .brand-block .subtitle { margin:4px 0 10px; font-size:13px; color:var(--muted); }
    .contact-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:6px; margin-top:6px; }
    .contact-card { border:1px solid var(--border); border-radius:6px; padding:6px 8px; background:#f0f6ff; font-size:12px; line-height:1.35; }
    .contact-card .cname { font-weight:600; color:var(--accent); }
    .contact-card .clabel { color:var(--muted); font-size:11px; }
    .bank-block { border:1px solid var(--border); border-radius:8px; padding:10px 12px; background:#f4f8ff; font-size:12px; }
    .bank-title { font-weight:700; color:var(--accent); margin-bottom:6px; }
    .bank-row { display:grid; grid-template-columns:130px 1fr; gap:8px; margin:3px 0; }
    .bank-label { color:var(--muted); }
    .bank-value { font-weight:600; color:var(--text); }
    .address-row { display:flex; gap:8px; margin-top:10px; font-size:12px; color:var(--muted); }
    .addr-label { font-weight:600; color:var(--accent); }
    .meta { display:grid; grid-template-columns:repeat(6,1fr); gap:8px; margin-bottom:10px; }
    .field { border:1px solid var(--border); border-radius:6px; background:#f0f6ff; padding:6px 8px; }
    .field label { display:block; font-size:11px; color:var(--muted); margin-bottom:3px; font-weight:600; }
    .field input, .field select { width:100%; border:none; outline:none; background:transparent; font-weight:600; color:var(--text); font-size:12px; }
    .items-table { width:100%; border-collapse:collapse; margin-top:6px; background:#ffffff; border:1px solid var(--border); border-radius:8px; overflow:hidden; }
    .items-table thead th { background:#eaf2ff; color:var(--accent); text-align:left; font-size:12px; padding:8px 10px; border-bottom:1px solid var(--border); font-weight:700; }
    .items-table tbody td { font-size:12px; padding:6px 8px; border-bottom:1px dashed #c9d7ea; }
    .items-table tbody tr:last-child td { border-bottom:none; }
    .items-table tbody td input { width:100%; border:none; outline:none; background:transparent; font-size:12px; color:var(--text); font-weight:500; }
    .items-table tfoot td { background:#f4f8ff; font-weight:700; color:var(--accent); padding:8px 10px; border-top:1px solid var(--border); font-size:13px; }
    .totals { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin:12px 0 8px; }
    .totals .block { border:1px solid var(--border); border-radius:8px; background:#f0f6ff; padding:10px 12px; }
    .totals .trow { display:grid; grid-template-columns:150px 1fr; gap:10px; margin:5px 0; font-size:13px; align-items:center; }
    .totals .tlabel { color:var(--muted); font-size:12px; }
    .totals .tvalue input, .totals .tvalue select { width:100%; border:none; outline:none; background:#ffffff; border-radius:6px; padding:6px 8px; font-weight:700; color:var(--accent); font-size:13px; }
    .note-box { font-size:12px; color:var(--muted); line-height:1.6; background:#f4f8ff; border:1px dashed var(--border); border-radius:8px; padding:10px 12px; margin-top:8px; }
    .footer-box { border-top:2px dashed var(--border); margin-top:10px; padding-top:8px; display:flex; justify-content:space-between; font-size:12px; color:var(--muted); }
    .submit-btn { width:100%; padding:12px; background:var(--accent); color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; margin-top:12px; }
    .submit-btn:hover { background:#0e2a4f; }
    .submit-btn:disabled { background:#6b7280; cursor:not-allowed; }
    .sale-alert { margin-top:8px; font-size:13px; }
    .history-section { width:860px; margin:0 auto 40px; background:#fff; border:1px solid var(--border); border-radius:10px; overflow:hidden; }
    .history-header { background:#eaf2ff; padding:12px 16px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); }
    .history-header h6 { margin:0; font-weight:700; color:var(--accent); font-size:13px; }
    .history-table { width:100%; border-collapse:collapse; font-size:12px; }
    .history-table thead th { background:#f4f8ff; padding:8px 12px; color:var(--muted); font-weight:700; font-size:11px; text-transform:uppercase; border-bottom:1px solid var(--border); text-align:left; }
    .history-table tbody td { padding:8px 12px; border-bottom:1px solid #f0f0f0; color:var(--text); }
    .history-table tbody tr:hover td { background:#f7fbff; }
    .memo-badge { font-family:monospace; background:#e7f1ff; color:var(--accent); padding:2px 8px; border-radius:4px; font-weight:700; font-size:11px; }
    .type-badge { padding:2px 8px; border-radius:10px; font-size:11px; font-weight:600; }
    .type-cash { background:#f0fdf4; color:#22c55e; }
    .type-credit { background:#fef2f2; color:#ef4444; }
    .type-partial { background:#fff7ed; color:#f59e0b; }
    .type-bank_transfer { background:#eff6ff; color:#3b82f6; }
    .print-btn { background:#eaf2ff; border:1px solid var(--border); color:var(--accent); padding:4px 10px; border-radius:6px; font-size:11px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; }
    .print-btn:hover { background:var(--accent); color:#fff; }
    .actions-bar { width:860px; margin:16px auto 8px; display:flex; gap:8px; }
    .act-btn { padding:7px 14px; border-radius:8px; border:1px solid var(--border); background:#ffffff; color:var(--accent); font-weight:600; cursor:pointer; font-size:12px; }
    .act-btn:hover { background:#f0f6ff; }
    .autocomplete-wrapper { position:relative; }
    .autocomplete-list { position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid var(--border); border-radius:6px; max-height:180px; overflow-y:auto; z-index:999; box-shadow:0 4px 12px rgba(0,0,0,.1); }
    .autocomplete-item { padding:7px 10px; font-size:12px; cursor:pointer; color:var(--text); border-bottom:1px solid #f0f0f0; }
    .autocomplete-item:hover { background:#e7f1ff; color:var(--accent); }
    .filter-bar { display:grid; grid-template-columns:1fr 1fr 1fr 1fr auto; gap:8px; padding:12px 16px; border-bottom:1px solid #e5e7eb; }
    .filter-bar input { border:1px solid var(--border); border-radius:6px; padding:6px 10px; font-size:12px; width:100%; outline:none; color:var(--text); }
    .filter-btn { padding:4px 8px; border-radius:6px; border:1px solid var(--border); font-size:11px; cursor:pointer; font-weight:600; }
</style>
@endpush

@section('content')
@php
    $s        = App\Models\BusinessSetting::first();
    $contacts = App\Models\BusinessContact::all();
    $banks    = App\Models\BusinessBank::all();
@endphp

<script>
    const IS_ADMIN = {{ auth()->user()->role == 'admin' ? 'true' : 'false' }};
</script>

<div class="actions-bar">
    <button class="act-btn" onclick="addRow()">➕ Add Item</button>
    <button class="act-btn" onclick="clearItems()">🗑️ Clear Items</button>
</div>

<div class="receipt">
    <div class="header">
        <div class="brand-block">
            <h1>{{ $s->business_name ?? 'STAR COMPUTER' }}</h1>
            <div class="subtitle"><strong>{{ $s->tagline ?? '' }}</strong></div>
            <div class="contact-grid">
                @foreach($contacts as $contact)
                <div class="contact-card">
                    <div class="cname">{{ $contact->name }}</div>
                    <div class="clabel">Contact</div>
                    <div>{{ $contact->phone }}</div>
                </div>
                @endforeach
            </div>
            @if($s && $s->ntn)
            <div style="font-size:11px;color:#3e5a7a;margin-top:6px">
                <strong>NTN:</strong> {{ $s->ntn }}
            </div>
            @endif
            @if($s && $s->address)
            <div class="address-row">
                <div class="addr-label">Office:</div>
                <div>{{ $s->address }}</div>
            </div>
            @endif
        </div>
        <div>
            @foreach($banks as $bank)
            <div class="bank-block" style="margin-bottom:6px">
                <div class="bank-title">{{ $bank->bank_name }} — {{ $bank->account_title }}</div>
                <div class="bank-row">
                    <div class="bank-label">Account Title</div>
                    <div class="bank-value">{{ $bank->account_title }}</div>
                </div>
                <div class="bank-row">
                    <div class="bank-label">Account Number</div>
                    <div class="bank-value">{{ $bank->account_number }}</div>
                </div>
                @if($bank->iban)
                <div class="bank-row">
                    <div class="bank-label">IBAN</div>
                    <div class="bank-value">{{ $bank->iban }}</div>
                </div>
                @endif
                @if($bank->qr_code)
                <div style="margin-top:6px">
                    <img src="{{ asset('storage/'.$bank->qr_code) }}"
                         style="width:60px;height:60px;object-fit:contain" alt="QR">
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="meta">
        <div class="field">
            <label>Memo No.</label>
            <input type="text" id="memo_display" placeholder="Auto" readonly>
        </div>
        <div class="field">
            <label>Date</label>
            <input type="date" id="purchase_date" value="{{ date('Y-m-d') }}">
        </div>
        <div class="field" style="position:relative">
            <label>Vendor *</label>
            <input type="text" id="vendor_search"
                   placeholder="Type to search..."
                   autocomplete="off"
                   oninput="searchVendor(this.value)"
                   style="width:100%;border:none;outline:none;background:transparent;font-weight:600;color:#0e2a4f;font-size:12px">
            <input type="hidden" id="vendor_id">
            <div id="vendor_list"
                 style="display:none;position:absolute;top:100%;left:0;right:0;
                        background:#fff;border:1px solid #5a7ca8;border-radius:6px;
                        max-height:180px;overflow-y:auto;z-index:999;
                        box-shadow:0 4px 12px rgba(0,0,0,.1)"></div>
        </div>
        <div class="field">
            <label>Mobile</label>
            <input type="text" id="vendor_mobile" readonly placeholder="Auto">
        </div>
        <div class="field">
            <label>CNIC</label>
            <input type="text" id="vendor_cnic" readonly placeholder="Auto">
        </div>
        <div class="field">
            <label>Staff *</label>
            <select id="user_id">
                <option value="">Select...</option>
                @foreach($staff as $st)
                <option value="{{ $st->id }}">{{ $st->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <table class="items-table" id="itemsTable">
        <thead>
            <tr>
                <th style="width:40px">S/N</th>
                <th>Product Name</th>
                <th style="width:120px">Description</th>
                <th style="width:95px">Stock Code</th>
                <th style="width:65px">Qty</th>
                @if(auth()->user()->role == 'admin')
                <th style="width:105px">Purchase Price</th>
                <th style="width:105px">Sale Price</th>
                <th style="width:105px">Amount</th>
                @endif
                <th style="width:30px" class="no-print"></th>
            </tr>
        </thead>
        <tbody id="itemsBody">
            <tr id="row_1">
                <td><input type="text" value="1" readonly></td>
                <td>
                    <div class="autocomplete-wrapper">
                        <input type="text" id="name_1" placeholder="Type to search..."
                               oninput="searchProduct(this.value,1)" autocomplete="off">
                        <div id="list_1" class="autocomplete-list" style="display:none"></div>
                    </div>
                </td>
                <td><input type="text" id="desc_1" placeholder="Optional..."></td>
                <td><input type="text" id="code_1" placeholder="Optional"></td>
                <td><input type="number" id="qty_1" value="1" min="1" oninput="recalcRow(1)"></td>
                @if(auth()->user()->role == 'admin')
                <td><input type="number" id="pp_1" value="0" min="0" step="0.01" oninput="recalcRow(1)"></td>
                <td><input type="number" id="sp_1" value="0" min="0" step="0.01"></td>
                <td><input type="number" id="amt_1" value="0" readonly></td>
                @else
                <td style="display:none"><input type="number" id="pp_1" value="0"></td>
                <td style="display:none"><input type="number" id="sp_1" value="0"></td>
                <td style="display:none"><input type="number" id="amt_1" value="0"></td>
                @endif
                <td class="no-print">
                    <button onclick="removeRow(1)"
                            style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:14px">✕</button>
                </td>
            </tr>
        </tbody>
        @if(auth()->user()->role == 'admin')
        <tfoot>
            <tr>
                <td colspan="7">Total</td>
                <td id="totalCell">0</td>
                <td class="no-print"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    @if(auth()->user()->role == 'admin')
    <div class="totals">
        <div class="block">
            <div class="trow">
                <div class="tlabel">Total amount</div>
                <div class="tvalue"><input type="number" id="totalAmount" value="0" readonly></div>
            </div>
            <div class="trow">
                <div class="tlabel">Discount</div>
                <div class="tvalue"><input type="number" id="discountAmount" value="0" min="0" oninput="recalcTotals()"></div>
            </div>
            <div class="trow">
                <div class="tlabel">Net total</div>
                <div class="tvalue"><input type="number" id="netTotal" value="0" readonly></div>
            </div>
            <div class="trow">
                <div class="tlabel">Cash Paid</div>
                <div class="tvalue"><input type="number" id="paidAmount" value="0" min="0" oninput="recalcBalance()"></div>
            </div>
            <div class="trow">
                <div class="tlabel">Payable Balance</div>
                <div class="tvalue"><input type="number" id="balanceAmount" value="0" readonly></div>
            </div>
        </div>
        <div class="block">
            <div class="trow">
                <div class="tlabel">Payment method</div>
                <div class="tvalue">
                    <select id="payment_type">
                        <option value="cash">Cash</option>
                        <option value="credit">Credit</option>
                        <option value="partial">Partial</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
            </div>
            <div class="trow">
                <div class="tlabel">Reference / Cheque</div>
                <div class="tvalue"><input type="text" id="payment_ref" placeholder="Optional"></div>
            </div>
        </div>
    </div>
    @endif

    @if($s && $s->notes)
    <div class="note-box">
        <strong>Note:</strong><br>
        @foreach(explode("\n", $s->notes) as $note)
            @if(trim($note))• {{ trim($note) }}<br>@endif
        @endforeach
    </div>
    @endif

    <div class="footer-box">
        <div>{{ $s->thank_you_message ?? 'Thank you for your business.' }}</div>
        <div>Powered by STAR COMPUTER POS</div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 130px;gap:10px;margin-top:12px">
        <button class="submit-btn" id="submitBtn" onclick="submitPurchase()">✅ Submit Purchase</button>
        <button class="submit-btn" id="printBtn" onclick="printLastBill()" disabled
                style="background:#6b7280;cursor:not-allowed;opacity:0.7">🖨️ Print</button>
    </div>
    <div id="purchaseAlert" class="sale-alert"></div>
</div>

<!-- Purchase History -->
<div class="history-section">
    <div class="history-header"><h6>🕐 Purchase History</h6></div>
    <div class="filter-bar">
        <input type="text" id="search_memo" placeholder="🔍 Memo #..." oninput="filterHistory()">
        <input type="text" id="search_vendor" placeholder="🔍 Vendor..." oninput="filterHistory()">
        <input type="date" id="search_from" value="{{ date('Y-m-d') }}" onchange="filterHistory()">
        <input type="date" id="search_to" value="{{ date('Y-m-d') }}" onchange="filterHistory()">
        <div style="display:flex;gap:4px;align-items:center">
            <button class="filter-btn" onclick="setToday()" style="background:#f0f6ff;color:var(--accent)">Today</button>
            <button class="filter-btn" onclick="setThisMonth()" style="background:#f0f6ff;color:var(--accent)">Month</button>
            <button class="filter-btn" onclick="clearFilter()" style="background:#fef2f2;color:#ef4444">All</button>
        </div>
    </div>
    <table class="history-table">
        <thead>
            <tr>
                <th>Memo#</th><th>Date</th><th>Vendor</th>
                <th>Mobile</th><th>Staff</th><th>Total</th>
                <th>Type</th><th>Actions</th>
            </tr>
        </thead>
        <tbody id="historyBody">
            @forelse($purchases as $purchase)
            <tr>
                <td><span class="memo-badge">{{ $purchase->memo_no }}</span></td>
                <td>{{ \Carbon\Carbon::parse($purchase->date)->format('d-m-Y') }}</td>
                <td>{{ $purchase->vendor->name ?? '—' }}</td>
                <td>{{ $purchase->vendor->contact1 ?? '—' }}</td>
                <td>{{ $purchase->salesperson->name ?? '—' }}</td>
                <td>Rs. {{ number_format($purchase->total) }}</td>
                <td>
                    <span class="type-badge type-{{ $purchase->payment_type }}">
                        {{ ucfirst($purchase->payment_type) }}
                    </span>
                </td>
                <td style="display:flex;gap:4px;align-items:center;flex-wrap:wrap">
                    <button class="print-btn"
                        onclick="openTransferModal({{ $purchase->id }},'{{ $purchase->memo_no }}')">
                        🔄 Transfer
                    </button>
                    <button class="print-btn"
                        onclick="openPurchaseReturn({{ $purchase->id }},'{{ $purchase->memo_no }}')">
                        ↩️ Return
                    </button>
                    @if(auth()->user()->role == 'admin')
                    <button class="print-btn" onclick="editPurchaseRate({{ $purchase->id }})">✏️ Rate</button>
                    @endif
                    <button class="print-btn"
                        onclick="window.open('/purchases/{{ $purchase->id }}/invoice','_blank')">
                        🖨️ Print
                    </button>
                    <a href="/purchases/{{ $purchase->id }}/invoice/pdf"
                       class="print-btn" target="_blank" style="text-decoration:none">📄 PDF</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:20px;color:#6b7280">Koi purchase nahi mila</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Purchase Return Modal -->
<div class="modal fade" id="purchaseReturnModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">↩️ Purchase Return — Memo# <span id="pReturnMemoNo"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Return Date *</label>
                        <input type="date" id="pReturnDate" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Reason <small class="text-muted">(Optional)</small></label>
                        <input type="text" id="pReturnReason" class="form-control" placeholder="e.g. Defective product">
                    </div>
                </div>
                <div id="pReturnItemsBody">
                    <div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div> Loading...</div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning btn-sm" id="submitPReturnBtn" onclick="submitPurchaseReturn()">↩️ Process Return</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Rate Modal -->
<div class="modal fade" id="editRateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">✏️ Edit Purchase Rates</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="editRateBody">
                <div class="text-center py-3">Loading...</div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="saveRates()">💾 Save Rates</button>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">🔄 Transfer Purchase — Memo# <span id="transferMemoNo"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" style="font-size:13px">
                    ⚠️ Yeh purchase kisi aur vendor ke naam transfer hoga. Old vendor ki ledger update ho jayegi.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">New Vendor Select Karein *</label>
                    <div style="position:relative">
                        <input type="text" id="transfer_vendor_search"
                               class="form-control"
                               placeholder="Type vendor name..."
                               autocomplete="off"
                               oninput="searchTransferVendor(this.value)">
                        <div id="transfer_vendor_list"
                             style="display:none;position:absolute;top:100%;left:0;right:0;
                                    background:#fff;border:1px solid #5a7ca8;border-radius:6px;
                                    max-height:180px;overflow-y:auto;z-index:999;
                                    box-shadow:0 4px 12px rgba(0,0,0,.1)"></div>
                    </div>
                    <input type="hidden" id="transfer_vendor_id">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="submitTransferBtn" onclick="submitTransfer()">
                    🔄 Transfer Purchase
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let rowCounter             = 1;
let lastPurchaseId         = null;
let currentPurchaseId      = null;
let currentPReturnId       = null;
let currentTransferPurchId = null;

const allProducts = @json($products ?? []);
const allVendors  = @json($vendors  ?? []);

// ── Vendor Search ────────────────────────────────────────
function searchVendor(query) {
    const list = document.getElementById('vendor_list');
    if (!query) { list.style.display = 'none'; document.getElementById('vendor_id').value = ''; return; }
    const filtered = allVendors.filter(v =>
        v.name.toLowerCase().includes(query.toLowerCase()) ||
        (v.contact1 && v.contact1.includes(query))
    );
    if (!filtered.length) { list.style.display = 'none'; return; }
    list.innerHTML = filtered.map(v => `
        <div onclick="selectVendor(${v.id},'${v.name}','${v.contact1 ?? ''}','${v.cnic ?? ''}')"
             style="padding:7px 10px;font-size:12px;cursor:pointer;border-bottom:1px solid #f0f0f0;color:#0e2a4f"
             onmouseover="this.style.background='#e7f1ff'"
             onmouseout="this.style.background='#fff'">
            <strong>${v.name}</strong>
            ${v.contact1 ? `<span style="color:#5a7ca8;margin-left:6px">${v.contact1}</span>` : ''}
        </div>`).join('');
    list.style.display = 'block';
}

function selectVendor(id, name, mobile, cnic) {
    document.getElementById('vendor_id').value     = id;
    document.getElementById('vendor_search').value = name;
    document.getElementById('vendor_mobile').value = mobile;
    document.getElementById('vendor_cnic').value   = cnic;
    document.getElementById('vendor_list').style.display = 'none';
}

// ── Transfer Vendor Search ───────────────────────────────
function searchTransferVendor(query) {
    const list = document.getElementById('transfer_vendor_list');
    if (!query) { list.style.display = 'none'; return; }
    const filtered = allVendors.filter(v =>
        v.name.toLowerCase().includes(query.toLowerCase()) ||
        (v.contact1 && v.contact1.includes(query))
    );
    if (!filtered.length) { list.style.display = 'none'; return; }
    list.innerHTML = filtered.map(v => `
        <div onclick="selectTransferVendor(${v.id},'${v.name}')"
             style="padding:7px 10px;font-size:12px;cursor:pointer;border-bottom:1px solid #f0f0f0;color:#0e2a4f"
             onmouseover="this.style.background='#e7f1ff'"
             onmouseout="this.style.background='#fff'">
            <strong>${v.name}</strong>
            ${v.contact1 ? `<span style="color:#5a7ca8;margin-left:6px">${v.contact1}</span>` : ''}
        </div>`).join('');
    list.style.display = 'block';
}

function selectTransferVendor(id, name) {
    document.getElementById('transfer_vendor_id').value     = id;
    document.getElementById('transfer_vendor_search').value = name;
    document.getElementById('transfer_vendor_list').style.display = 'none';
}

document.addEventListener('click', function(e) {
    ['vendor_list','transfer_vendor_list'].forEach(id => {
        const el = document.getElementById(id);
        if (el && !el.parentElement.contains(e.target)) el.style.display = 'none';
    });
    document.querySelectorAll('.autocomplete-list').forEach(list => {
        if (!list.parentElement.contains(e.target)) list.style.display = 'none';
    });
});

// ── Product Search ───────────────────────────────────────
function searchProduct(query, rowId) {
    const list = document.getElementById(`list_${rowId}`);
    if (!query) { list.style.display = 'none'; return; }
    const filtered = allProducts.filter(p =>
        p.name.toLowerCase().includes(query.toLowerCase()) ||
        (p.stock_code && p.stock_code.toLowerCase().includes(query.toLowerCase()))
    );
    if (!filtered.length) { list.style.display = 'none'; return; }
    list.innerHTML = filtered.map(p => `
        <div class="autocomplete-item" onclick='selectProduct(${JSON.stringify(p)},${rowId})'>
            <strong>${p.name}</strong>
            ${p.stock_code ? `<span style="color:#5a7ca8;margin-left:6px">${p.stock_code}</span>` : ''}
            <span style="color:#22c55e;float:right">Stock: ${p.remaining_qty}</span>
        </div>`).join('');
    list.style.display = 'block';
}

function selectProduct(product, rowId) {
    document.getElementById(`name_${rowId}`).value = product.name;
    document.getElementById(`code_${rowId}`).value = product.stock_code || '';
    if (IS_ADMIN) {
        document.getElementById(`pp_${rowId}`).value = product.purchase_price || 0;
        document.getElementById(`sp_${rowId}`).value = product.sale_price     || 0;
    }
    document.getElementById(`list_${rowId}`).style.display = 'none';
    if (IS_ADMIN) recalcRow(rowId);
}

// ── Calc ─────────────────────────────────────────────────
function recalcRow(rowId) {
    if (!IS_ADMIN) return;
    const qty = parseFloat(document.getElementById(`qty_${rowId}`)?.value || 0);
    const pp  = parseFloat(document.getElementById(`pp_${rowId}`)?.value  || 0);
    document.getElementById(`amt_${rowId}`).value = (qty * pp).toFixed(2);
    recalcTotals();
}

function recalcTotals() {
    if (!IS_ADMIN) return;
    let total = 0;
    document.querySelectorAll('[id^="amt_"]').forEach(el => { total += parseFloat(el.value || 0); });
    const discount = parseFloat(document.getElementById('discountAmount')?.value || 0);
    const net = Math.max(total - discount, 0);
    document.getElementById('totalCell').textContent = total.toFixed(2);
    document.getElementById('totalAmount').value     = total.toFixed(2);
    document.getElementById('netTotal').value        = net.toFixed(2);
    recalcBalance();
}

function recalcBalance() {
    if (!IS_ADMIN) return;
    const net  = parseFloat(document.getElementById('netTotal')?.value   || 0);
    const paid = parseFloat(document.getElementById('paidAmount')?.value || 0);
    document.getElementById('balanceAmount').value = Math.max(net - paid, 0).toFixed(2);
}

// ── Row Management ───────────────────────────────────────
function getRowTemplate(rid, sn) {
    return `
        <tr id="row_${rid}">
            <td><input type="text" value="${sn}" readonly></td>
            <td>
                <div class="autocomplete-wrapper">
                    <input type="text" id="name_${rid}" placeholder="Type to search..."
                           oninput="searchProduct(this.value,${rid})" autocomplete="off">
                    <div id="list_${rid}" class="autocomplete-list" style="display:none"></div>
                </div>
            </td>
            <td><input type="text" id="desc_${rid}" placeholder="Optional..."></td>
            <td><input type="text" id="code_${rid}" placeholder="Optional"></td>
            <td><input type="number" id="qty_${rid}" value="1" min="1" oninput="recalcRow(${rid})"></td>
            <td style="${IS_ADMIN ? '' : 'display:none'}">
                <input type="number" id="pp_${rid}" value="0" min="0" step="0.01" oninput="recalcRow(${rid})">
            </td>
            <td style="${IS_ADMIN ? '' : 'display:none'}">
                <input type="number" id="sp_${rid}" value="0" min="0" step="0.01">
            </td>
            <td style="${IS_ADMIN ? '' : 'display:none'}">
                <input type="number" id="amt_${rid}" value="0" readonly>
            </td>
            <td class="no-print">
                <button onclick="removeRow(${rid})"
                        style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:14px">✕</button>
            </td>
        </tr>`;
}

function renumberRows() {
    [...document.getElementById('itemsBody').rows].forEach((row, i) => {
        const input = row.querySelector('input[type="text"]');
        if (input) input.value = i + 1;
    });
}

function addRow() {
    rowCounter++;
    const tbody = document.getElementById('itemsBody');
    tbody.insertAdjacentHTML('beforeend', getRowTemplate(rowCounter, tbody.rows.length + 1));
    document.getElementById(`name_${rowCounter}`).focus();
    if (IS_ADMIN) recalcTotals();
}

function removeRow(rid) {
    const tbody = document.getElementById('itemsBody');
    if (tbody.rows.length <= 1) return;
    document.getElementById(`row_${rid}`)?.remove();
    renumberRows();
    if (IS_ADMIN) recalcTotals();
}

function clearItems() {
    const tbody = document.getElementById('itemsBody');
    [...tbody.rows].forEach(row => {
        const amt = parseFloat(row.querySelector('[id^="amt_"]')?.value || 0);
        if (amt === 0 && tbody.rows.length > 1) row.remove();
    });
    renumberRows();
    if (IS_ADMIN) recalcTotals();
}

document.getElementById('itemsBody').addEventListener('keydown', function(e) {
    if (e.key !== 'Tab' || e.shiftKey) return;
    const lastRow   = this.rows[this.rows.length - 1];
    const lastInput = lastRow.querySelector('[id^="amt_"]');
    if (e.target === lastInput) { e.preventDefault(); addRow(); }
});

// ── Print ────────────────────────────────────────────────
function printLastBill() {
    if (!lastPurchaseId) { alert('Pehle purchase submit karein!'); return; }
    let iframe = document.getElementById('printFrame');
    if (!iframe) {
        iframe = document.createElement('iframe');
        iframe.id = 'printFrame';
        iframe.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:0;height:0;border:0';
        document.body.appendChild(iframe);
    }
    iframe.src = `/purchases/${lastPurchaseId}/invoice`;
    iframe.onload = function() { iframe.contentWindow.focus(); iframe.contentWindow.print(); };
}

// ── Submit Purchase ──────────────────────────────────────
async function submitPurchase() {
    const vendorId = document.getElementById('vendor_id').value;
    const userId   = document.getElementById('user_id').value;
    const date     = document.getElementById('purchase_date').value;
    const discount = IS_ADMIN ? (parseFloat(document.getElementById('discountAmount')?.value) || 0) : 0;
    const paid     = IS_ADMIN ? (parseFloat(document.getElementById('paidAmount')?.value)     || 0) : 0;
    const payType  = IS_ADMIN ? (document.getElementById('payment_type')?.value || 'credit')  : 'credit';

    if (!vendorId) { alert('Vendor select karein!'); return; }
    if (!userId)   { alert('Staff select karein!');  return; }

    const items  = [];
    let hasItems = false;

    document.querySelectorAll('#itemsBody tr').forEach(row => {
        const rid  = row.id.replace('row_', '');
        const name = document.getElementById(`name_${rid}`)?.value?.trim();
        const qty  = parseInt(document.getElementById(`qty_${rid}`)?.value   || 0);
        const pp   = IS_ADMIN ? parseFloat(document.getElementById(`pp_${rid}`)?.value || 0) : 0;
        const sp   = IS_ADMIN ? parseFloat(document.getElementById(`sp_${rid}`)?.value || 0) : 0;
        const code = document.getElementById(`code_${rid}`)?.value?.trim();
        const desc = document.getElementById(`desc_${rid}`)?.value?.trim();

        if (!name || qty < 1) return;
        hasItems = true;
        items.push({ name, stock_code: code || null, qty, purchase_price: pp, sale_price: sp, description: desc || null, alert_qty: 5 });
    });

    if (!hasItems) { alert('Koi item add nahi kiya!'); return; }

    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.textContent = '⏳ Processing...';

    try {
        const res = await fetch('{{ route("purchases.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ vendor_id: vendorId, user_id: userId, date, discount, paid, payment_type: payType, items })
        });
        const data = await res.json();

        if (data.success) {
            lastPurchaseId = data.purchase_id;
            const printBtn = document.getElementById('printBtn');
            printBtn.removeAttribute('disabled');
            printBtn.style.opacity    = '1';
            printBtn.style.background = '#22c55e';
            printBtn.style.cursor     = 'pointer';

            document.getElementById('purchaseAlert').innerHTML = `
                <div style="background:#f0fdf4;color:#166534;padding:10px 14px;border-radius:8px;font-weight:600;margin-top:8px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                    <span>✅ Purchase ho gaya! Memo: <strong>${data.memo_no}</strong></span>
                    <a href="/purchases/${data.purchase_id}/invoice" target="_blank"
                       style="background:#163a6f;color:#fff;padding:5px 14px;border-radius:6px;text-decoration:none;font-size:12px;font-weight:700">🖨️ Print</a>
                    <a href="/purchases/${data.purchase_id}/invoice/pdf" target="_blank"
                       style="background:#5a7ca8;color:#fff;padding:5px 14px;border-radius:6px;text-decoration:none;font-size:12px;font-weight:700">📄 PDF</a>
                </div>`;

            document.getElementById('vendor_id').value     = '';
            document.getElementById('vendor_search').value = '';
            document.getElementById('user_id').value       = '';
            document.getElementById('vendor_mobile').value = '';
            document.getElementById('vendor_cnic').value   = '';
            if (IS_ADMIN) {
                document.getElementById('discountAmount').value  = 0;
                document.getElementById('paidAmount').value      = 0;
                document.getElementById('balanceAmount').value   = 0;
                document.getElementById('totalAmount').value     = 0;
                document.getElementById('netTotal').value        = 0;
                document.getElementById('totalCell').textContent = '0';
            }
            document.getElementById('itemsBody').innerHTML = getRowTemplate(1, 1);
            rowCounter = 1;
            filterHistory();
        } else {
            document.getElementById('purchaseAlert').innerHTML = `
                <div style="background:#fef2f2;color:#ef4444;padding:10px 14px;border-radius:8px;font-weight:600;margin-top:8px">❌ ${data.message}</div>`;
        }
    } catch(e) {
        document.getElementById('purchaseAlert').innerHTML = `
            <div style="background:#fef2f2;color:#ef4444;padding:10px 14px;border-radius:8px;font-weight:600;margin-top:8px">❌ Server error!</div>`;
    }

    btn.disabled = false; btn.textContent = '✅ Submit Purchase';
}

// ── Filter History ───────────────────────────────────────
async function filterHistory() {
    const params = new URLSearchParams({
        memo_no:     document.getElementById('search_memo').value,
        vendor_name: document.getElementById('search_vendor').value,
        from:        document.getElementById('search_from')?.value || '',
        to:          document.getElementById('search_to')?.value   || '',
    });

    const res       = await fetch(`{{ route('purchases.history') }}?${params}`);
    const purchases = await res.json();
    const tbody     = document.getElementById('historyBody');

    if (!purchases.length) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:20px;color:#6b7280">Koi purchase nahi mila</td></tr>`;
        return;
    }

    tbody.innerHTML = purchases.map(p => `
        <tr>
            <td><span class="memo-badge">${p.memo_no}</span></td>
            <td>${p.date}</td>
            <td>${p.vendor?.name ?? '—'}</td>
            <td>${p.vendor?.contact1 ?? '—'}</td>
            <td>${p.salesperson?.name ?? '—'}</td>
            <td>Rs. ${Number(p.total).toLocaleString()}</td>
            <td><span class="type-badge type-${p.payment_type}">${p.payment_type}</span></td>
            <td style="display:flex;gap:4px;align-items:center;flex-wrap:wrap">
                <button class="print-btn" onclick="openTransferModal(${p.id},'${p.memo_no}')">🔄 Transfer</button>
                <button class="print-btn" onclick="openPurchaseReturn(${p.id},'${p.memo_no}')">↩️ Return</button>
                ${IS_ADMIN ? `<button class="print-btn" onclick="editPurchaseRate(${p.id})">✏️ Rate</button>` : ''}
                <button class="print-btn" onclick="window.open('/purchases/${p.id}/invoice','_blank')">🖨️ Print</button>
                <a href="/purchases/${p.id}/invoice/pdf" class="print-btn" target="_blank" style="text-decoration:none;display:inline-block">📄 PDF</a>
            </td>
        </tr>`).join('');
}

function setToday() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('search_from').value = today;
    document.getElementById('search_to').value   = today;
    filterHistory();
}

function setThisMonth() {
    const now   = new Date();
    const first = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
    document.getElementById('search_from').value = first;
    document.getElementById('search_to').value   = now.toISOString().split('T')[0];
    filterHistory();
}

function clearFilter() {
    document.getElementById('search_from').value   = '';
    document.getElementById('search_to').value     = '';
    document.getElementById('search_memo').value   = '';
    document.getElementById('search_vendor').value = '';
    filterHistory();
}

// ── Purchase Return ──────────────────────────────────────
async function openPurchaseReturn(purchaseId, memoNo) {
    currentPReturnId = purchaseId;
    document.getElementById('pReturnMemoNo').textContent = memoNo;
    document.getElementById('pReturnItemsBody').innerHTML = `<div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div> Loading...</div>`;
    new bootstrap.Modal(document.getElementById('purchaseReturnModal')).show();

    const res  = await fetch(`/purchases/${purchaseId}/return-items`);
    const data = await res.json();

    if (!data.items || !data.items.length) {
        document.getElementById('pReturnItemsBody').innerHTML = `<div class="alert alert-warning">Koi returnable item nahi hai!</div>`;
        return;
    }

    let html = `<table class="table table-sm"><thead><tr>
        <th>Product</th><th>Original Qty</th><th>Already Returned</th>
        <th>Returnable</th><th>Return Qty</th><th>Rate</th>
    </tr></thead><tbody>`;
    data.items.forEach(item => {
        html += `<tr>
            <td>${item.product?.name ?? '—'}</td>
            <td>${item.qty}</td><td>${item.returned_qty}</td>
            <td><strong>${item.returnable_qty}</strong></td>
            <td><input type="number" class="form-control form-control-sm" id="preturn_qty_${item.id}" value="0" min="0" max="${item.returnable_qty}" style="width:80px"></td>
            <td>Rs. ${Number(item.rate).toLocaleString()}</td>
        </tr>`;
    });
    html += `</tbody></table>`;
    document.getElementById('pReturnItemsBody').innerHTML = html;
}

async function submitPurchaseReturn() {
    const items = [];
    document.querySelectorAll('#pReturnItemsBody input[id^="preturn_qty_"]').forEach(input => {
        const qty = parseInt(input.value || 0);
        if (qty > 0) items.push({ id: input.id.replace('preturn_qty_', ''), qty });
    });

    if (!items.length) { alert('Koi item select nahi kiya!'); return; }

    const btn = document.getElementById('submitPReturnBtn');
    btn.disabled = true; btn.textContent = '⏳ Processing...';

    try {
        const res  = await fetch(`/purchases/${currentPReturnId}/return`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ items, date: document.getElementById('pReturnDate').value, reason: document.getElementById('pReturnReason').value })
        });
        const data = await res.json();
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('purchaseReturnModal')).hide();
            alert('✅ Purchase return process ho gaya!');
            filterHistory();
        } else { alert('❌ ' + data.message); }
    } catch(e) { alert('❌ Server error!'); }

    btn.disabled = false; btn.textContent = '↩️ Process Return';
}

// ── Edit Rate ────────────────────────────────────────────
async function editPurchaseRate(purchaseId) {
    currentPurchaseId = purchaseId;
    const res  = await fetch(`/purchases/${purchaseId}/items`);
    const data = await res.json();

    let html = `<table class="table table-sm"><thead><tr>
        <th>Product</th><th>Qty</th><th>Purchase Price</th><th>Sale Price</th>
    </tr></thead><tbody>`;
    data.items.forEach(item => {
        html += `<tr>
            <td>${item.product?.name ?? '—'}</td><td>${item.qty}</td>
            <td><input type="number" class="form-control form-control-sm" id="pp_item_${item.id}" value="${item.rate}" min="0" step="0.01" style="width:120px"></td>
            <td><input type="number" class="form-control form-control-sm" id="sp_item_${item.id}" value="${item.product?.sale_price ?? 0}" min="0" step="0.01" style="width:120px"></td>
        </tr>`;
    });
    html += `</tbody></table>`;
    document.getElementById('editRateBody').innerHTML = html;
    new bootstrap.Modal(document.getElementById('editRateModal')).show();
}

async function saveRates() {
    const inputs = document.querySelectorAll('#editRateBody input[id^="pp_item_"]');
    const items  = [];
    inputs.forEach(input => {
        const itemId = input.id.replace('pp_item_', '');
        items.push({
            id: itemId,
            purchase_price: parseFloat(input.value || 0),
            sale_price:     parseFloat(document.getElementById(`sp_item_${itemId}`)?.value || 0)
        });
    });

    const res  = await fetch(`/purchases/${currentPurchaseId}/update-rates`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ items })
    });
    const data = await res.json();
    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('editRateModal')).hide();
        filterHistory();
        alert('✅ Rates update ho gayi!');
    } else { alert('❌ ' + data.message); }
}

// ── Transfer ─────────────────────────────────────────────
function openTransferModal(purchaseId, memoNo) {
    currentTransferPurchId = purchaseId;
    document.getElementById('transferMemoNo').textContent = memoNo;
    document.getElementById('transfer_vendor_search').value = '';
    document.getElementById('transfer_vendor_id').value     = '';
    document.getElementById('transfer_vendor_list').style.display = 'none';
    new bootstrap.Modal(document.getElementById('transferModal')).show();
}

async function submitTransfer() {
    const vendorId = document.getElementById('transfer_vendor_id').value;
    if (!vendorId) { alert('Vendor select karein!'); return; }

    const btn = document.getElementById('submitTransferBtn');
    btn.disabled = true; btn.textContent = '⏳ Processing...';

    try {
        const res  = await fetch(`/purchases/${currentTransferPurchId}/transfer`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ new_vendor_id: vendorId })
        });
        const data = await res.json();
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('transferModal')).hide();
            alert('✅ Purchase transfer ho gaya!');
            filterHistory();
        } else { alert('❌ ' + data.message); }
    } catch(e) { alert('❌ Server error!'); }

    btn.disabled = false; btn.textContent = '🔄 Transfer Purchase';
}
</script>
@endpush