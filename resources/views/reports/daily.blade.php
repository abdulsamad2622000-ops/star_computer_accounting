@extends('layouts.app')
@section('title', 'Daily Report')

@push('styles')
<style>
    .filter-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto;
        gap: 12px;
        align-items: end;
    }
    .quick-btns {
        display: flex;
        gap: 6px;
        margin-top: 10px;
    }
    .quick-btn {
        padding: 5px 12px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        background: #f4f6fb;
        color: #163a6f;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }
    .quick-btn:hover { background: #163a6f; color: #fff; }
    .report-table thead th {
        position: relative;
    }
    .report-table thead th input {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        padding: 3px 6px;
        font-size: 11px;
        margin-top: 4px;
        font-weight: 400;
        color: #374151;
        background: #fff;
    }
    .report-table thead th input:focus {
        outline: none;
        border-color: #4f8ef7;
    }
    .col-filter-label {
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .summary-footer td {
        font-weight: 700;
        font-size: 13px;
    }


    .col-filter-select {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    padding: 3px 4px;
    font-size: 11px;
    margin-top: 4px;
    color: #374151;
    background: #fff;
    outline: none;
}
.col-filter-select:focus {
    border-color: #4f8ef7;
}
</style>
@endpush

@section('content')

<!-- Top Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff">
                <i class="bi bi-receipt" style="color:#4f8ef7"></i>
            </div>
            <div class="stat-value">Rs. {{ number_format($totalBill) }}</div>
            <div class="stat-label">Total Bill Amount</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4">
                <i class="bi bi-cash" style="color:#22c55e"></i>
            </div>
            <div class="stat-value">Rs. {{ number_format($totalReceived) }}</div>
            <div class="stat-label">Total Received</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef2f2">
                <i class="bi bi-clock-history" style="color:#ef4444"></i>
            </div>
            <div class="stat-value" style="color:#ef4444">
                Rs. {{ number_format($totalBalance) }}
            </div>
            <div class="stat-label">Total Balance</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-card">
    <form method="GET" action="{{ route('reports.daily') }}">
        <div class="filter-grid">
            <div>
                <label class="form-label">From Date</label>
                <input type="date" name="from"
                       class="form-control form-control-sm"
                       value="{{ $from }}">
            </div>
            <div>
                <label class="form-label">To Date</label>
                <input type="date" name="to"
                       class="form-control form-control-sm"
                       value="{{ $to }}">
            </div>
            <div>
                <label class="form-label">Customer Name</label>
                <input type="text" name="name"
                       class="form-control form-control-sm"
                       placeholder="Search by name..."
                       value="{{ request('name') }}">
            </div>
            <div>
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search me-1"></i> Search
                </button>
            </div>
        </div>

        <!-- Quick Buttons -->
        <div class="quick-btns">
            <a href="{{ route('reports.daily', ['from' => today()->format('Y-m-d'), 'to' => today()->format('Y-m-d')]) }}"
               class="quick-btn">Today</a>
            <a href="{{ route('reports.daily', ['from' => now()->startOfWeek()->format('Y-m-d'), 'to' => today()->format('Y-m-d')]) }}"
               class="quick-btn">This Week</a>
            <a href="{{ route('reports.daily', ['from' => now()->startOfMonth()->format('Y-m-d'), 'to' => today()->format('Y-m-d')]) }}"
               class="quick-btn">This Month</a>
            <a href="{{ route('reports.daily') }}"
               class="quick-btn" style="background:#fef2f2;color:#ef4444;border-color:#fecaca">
               Clear
            </a>
        </div>
    </form>
</div>

<!-- Report Table -->
<div class="table-card">
    <div class="table-card-header">
    <span class="table-card-title">
        <i class="bi bi-bar-chart me-2"></i>
        Sales Report
        <small class="text-muted fw-normal ms-2" style="font-size:.8rem">
            {{ \Carbon\Carbon::parse($from)->format('d M') }}
            —
            {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        </small>
    </span>
    <div class="d-flex gap-2">
        <button onclick="resetReportFilters()"
                class="btn btn-sm btn-outline-secondary">
            🔄 Reset Filters
        </button>
        <button onclick="window.print()"
                class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-printer"></i> Print
        </button>
        <a href="{{ route('reports.export.excel', request()->all()) }}"
           class="btn btn-sm btn-outline-success">
            <i class="bi bi-file-excel"></i> Excel
        </a>
        <a href="{{ route('reports.export.pdf', request()->all()) }}"
           class="btn btn-sm btn-outline-danger">
            <i class="bi bi-file-pdf"></i> PDF
        </a>
    </div>
</div>
    <div class="table-responsive">
        <table class="table mb-0 report-table" id="reportTable">
           <thead>
    <tr>
        <th>
            <div class="col-filter-label">Date</div>
            <select onchange="filterCol(this, 0)" class="col-filter-select">
                <option value="">All</option>
                @foreach($sales as $sale)
                    <option value="{{ \Carbon\Carbon::parse($sale->date)->format('d-m-Y') }}">
                        {{ \Carbon\Carbon::parse($sale->date)->format('d-m-Y') }}
                    </option>
                @endforeach
            </select>
        </th>
        <th>
            <div class="col-filter-label">Memo #</div>
            <select onchange="filterCol(this, 1)" class="col-filter-select">
                <option value="">All</option>
                @foreach($sales as $sale)
                    <option value="{{ $sale->memo_no }}">{{ $sale->memo_no }}</option>
                @endforeach
            </select>
        </th>
        <th>
            <div class="col-filter-label">Customer</div>
            <select onchange="filterCol(this, 2)" class="col-filter-select">
                <option value="">All</option>
                @foreach($sales->pluck('customer.name')->filter()->unique()->sort() as $name)
                    <option value="{{ $name }}">{{ $name }}</option>
                @endforeach
            </select>
        </th>
        <th>
            <div class="col-filter-label">Item</div>
            <select onchange="filterCol(this, 3)" class="col-filter-select">
                <option value="">All</option>
                @foreach($sales->flatMap->items->pluck('product.name')->filter()->unique()->sort() as $item)
                    <option value="{{ $item }}">{{ $item }}</option>
                @endforeach
            </select>
        </th>
        <th>
            <div class="col-filter-label">Stock Code</div>
            <select onchange="filterCol(this, 4)" class="col-filter-select">
                <option value="">All</option>
                @foreach($sales->flatMap->items->pluck('stock_code')->filter()->unique()->sort() as $code)
                    <option value="{{ $code }}">{{ $code }}</option>
                @endforeach
            </select>
        </th>
        <th>
            <div class="col-filter-label">Qty</div>
            <select onchange="filterCol(this, 5)" class="col-filter-select">
                <option value="">All</option>
                @foreach($sales->flatMap->items->pluck('qty')->unique()->sort() as $qty)
                    <option value="{{ $qty }}">{{ $qty }}</option>
                @endforeach
            </select>
        </th>
        <th>
            <div class="col-filter-label">Bill Amt</div>
            <select onchange="filterCol(this, 6)" class="col-filter-select">
                <option value="">All</option>
                @foreach($sales->flatMap->items->pluck('total')->unique()->sort() as $total)
                    <option value="Rs. {{ number_format($total) }}">
                        Rs. {{ number_format($total) }}
                    </option>
                @endforeach
            </select>
        </th>
        <th>
            <div class="col-filter-label">Received</div>
            <select onchange="filterCol(this, 7)" class="col-filter-select">
                <option value="">All</option>
                @foreach($sales->pluck('paid')->unique()->sort() as $paid)
                    <option value="Rs. {{ number_format($paid) }}">
                        Rs. {{ number_format($paid) }}
                    </option>
                @endforeach
            </select>
        </th>
        <th>
            <div class="col-filter-label">Description</div>
            <select onchange="filterCol(this, 8)" class="col-filter-select">
                <option value="">All</option>
                @foreach($sales->pluck('description')->filter()->unique()->sort() as $desc)
                    <option value="{{ $desc }}">{{ $desc }}</option>
                @endforeach
            </select>
        </th>
        <th>
            <div class="col-filter-label">Action</div>
        </th>
    </tr>
</thead>
            <tbody>
                @forelse($sales as $sale)
                    @foreach($sale->items as $item)
                    <tr>
                        <td>
                            {{ \Carbon\Carbon::parse($sale->date)
                                ->format('d-m-Y') }}
                        </td>
                        <td>
                            <span class="memo-no">{{ $sale->memo_no }}</span>
                        </td>
                        <td>{{ $sale->customer->name ?? '—' }}</td>
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
                                Rs. {{ number_format($sale->paid) }}
                            @else —
                            @endif
                        </td>
                        <td>{{ $item->description ?? '—' }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary"
                                    onclick="editTx(
                                        {{ $sale->id }},
                                        '{{ $sale->date }}',
                                        {{ $sale->paid }},
                                        '{{ $sale->description }}'
                                    )">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('reports.destroy', $sale) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete karein?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        Koi record nahi mila
                    </td>
                </tr>
                @endforelse
            </tbody>

            <!-- Summary Footer -->
            <tfoot>
                <tr class="summary-footer" style="background:#f4f6fb">
                    <td colspan="6" class="text-end">Total:</td>
                    <td>Rs. {{ number_format($totalBill) }}</td>
                    <td>Rs. {{ number_format($totalReceived) }}</td>
                    <td colspan="2"></td>
                </tr>
                <tr class="summary-footer" style="background:#fef2f2">
                    <td colspan="6" class="text-end">Balance:</td>
                    <td colspan="2">
                        <span class="badge-receivable" style="font-size:.9rem">
                            Rs. {{ number_format($totalBalance) }}
                        </span>
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Edit Transaction</h6>
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date"
                               id="edit_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Received Amount</label>
                        <input type="number" name="paid"
                               id="edit_paid" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_desc"
                                  class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button"
                            class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit"
                            class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function filterCol(select, colIndex) {
    const value = select.value.toLowerCase();
    const rows  = document.querySelectorAll('#reportTable tbody tr');

    rows.forEach(row => {
        checkAllFilters(row);
    });
    
}

function resetReportFilters() {
    document.querySelectorAll('#reportTable thead select')
        .forEach(select => {
            select.value = '';
        });
    document.querySelectorAll('#reportTable tbody tr')
        .forEach(row => {
            row.style.display = '';
        });
}

function checkAllFilters(row) {
    const selects = document.querySelectorAll('#reportTable thead select');
    let show = true;
    selects.forEach((select, i) => {
        const val  = select.value.toLowerCase();
        const cell = row.cells[i];
        if (cell && val && !cell.textContent.toLowerCase().includes(val)) {
            show = false;
        }
    });
    row.style.display = show ? '' : 'none';
}

function editTx(id, date, paid, desc) {
    document.getElementById('editForm').action = '/reports/' + id;
    document.getElementById('edit_date').value = date;
    document.getElementById('edit_paid').value = paid;
    document.getElementById('edit_desc').value = desc || '';
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>
@endpush