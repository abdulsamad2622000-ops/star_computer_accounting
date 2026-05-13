@extends('layouts.app')
@section('title', 'Vendor Manager')

@section('content')

<!-- Top Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff7ed">
                <i class="bi bi-arrow-up-circle" style="color:#f59e0b"></i>
            </div>
            <div class="stat-value">Rs. {{ number_format($totalPayable) }}</div>
            <div class="stat-label">Total Payable</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff">
                <i class="bi bi-check-circle" style="color:#4f8ef7"></i>
            </div>
            <div class="stat-value">{{ $settledCount }}</div>
            <div class="stat-label">Settled Vendors</div>
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
            <i class="bi bi-shop me-2"></i>All Vendors
        </span>
        <a href="{{ route('vendors.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Vendor
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
                @forelse($vendors as $i => $vendor)
                <tr style="cursor:pointer"
                    onclick="window.location='{{ route('vendors.show', $vendor) }}'">
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $vendor->name }}</strong></td>
                    <td>{{ $vendor->contact1 ?? '—' }}</td>
                    <td>{{ $vendor->cnic ?? '—' }}</td>
                    <td>
                        <span class="memo-no">
                            Rs. {{ number_format($vendor->balance) }}
                        </span>
                    </td>
                    <td>
                        @if($vendor->balance > 0)
                            <span class="badge-payable">🟡 Payable</span>
                        @else
                            <span class="badge-settled">🟢 Settled</span>
                        @endif
                    </td>
                    <td onclick="event.stopPropagation()">
                        <a href="{{ route('vendors.edit', $vendor) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('vendors.destroy', $vendor) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Vendor delete karein?')">
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
                        Koi vendor nahi mila
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection