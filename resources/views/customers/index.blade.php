@extends('layouts.app')
@section('title', 'Customer Manager')

@section('content')

<!-- Top Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4">
                <i class="bi bi-arrow-down-circle" style="color:#22c55e"></i>
            </div>
            <div class="stat-value">Rs. {{ number_format($totalReceivable) }}</div>
            <div class="stat-label">Total Receivable</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff">
                <i class="bi bi-check-circle" style="color:#4f8ef7"></i>
            </div>
            <div class="stat-value">{{ $settledCount }}</div>
            <div class="stat-label">Settled Customers</div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-card">
    <div class="table-card-header">
        <div class="d-flex gap-2">
    <button onclick="window.print()"
            class="btn btn-sm btn-outline-secondary no-print">
        <i class="bi bi-printer"></i> Print
    </button>
    <a href="{{ route('customers.export.pdf') }}"
       class="btn btn-sm btn-outline-danger no-print">
        <i class="bi bi-file-pdf"></i> PDF
    </a>
</div>
        <span class="table-card-title">
            <i class="bi bi-people me-2"></i>All Customers
        </span>
        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Customer
        </a>
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
           <tbody>
    @forelse($customers as $i => $customer)
    <tr style="cursor:pointer"
        onclick="window.location='{{ route('customers.show', $customer) }}'">
        <td>{{ $i + 1 }}</td>
        <td><strong>{{ $customer->name }}</strong></td>
        <td>{{ $customer->contact1 ?? '—' }}</td>
        <td>{{ $customer->cnic ?? '—' }}</td>
        <td>
            <span class="memo-no">
                Rs. {{ number_format($customer->balance) }}
            </span>
        </td>
        <td>
            @if($customer->balance > 0)
                <span class="badge-receivable">🔴 Receivable</span>
            @else
                <span class="badge-settled">🟢 Settled</span>
            @endif
        </td>
        <td onclick="event.stopPropagation()">
            <a href="{{ route('customers.edit', $customer) }}"
               class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil"></i>
            </a>
            <form action="{{ route('customers.destroy', $customer) }}"
                  method="POST" class="d-inline"
                  onsubmit="return confirm('Customer delete karein?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="text-center text-muted py-4">
            Koi customer nahi mila
        </td>
    </tr>
    @endforelse
</tbody>
        </table>
    </div>
</div>

@endsection