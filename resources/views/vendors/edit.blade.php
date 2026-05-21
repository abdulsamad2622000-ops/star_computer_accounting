@extends('layouts.app')
@section('title', 'Edit Vendor')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="form-card">
            <h5 class="fw-bold mb-4">
                <i class="bi bi-shop me-2"></i>Edit Vendor
            </h5>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('vendors.update', $vendor->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name"
                               class="form-control" required
                               value="{{ old('name', $vendor->name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact 1</label>
                        <input type="text" name="contact1"
                               class="form-control"
                               value="{{ old('contact1', $vendor->contact1) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact 2</label>
                        <input type="text" name="contact2"
                               class="form-control"
                               value="{{ old('contact2', $vendor->contact2) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CNIC</label>
                        <input type="text" name="cnic"
                               class="form-control"
                               value="{{ old('cnic', $vendor->cnic) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control"
                                  rows="2">{{ old('address', $vendor->address) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Opening Balance (Payable)</label>
                        <input type="number" name="opening_balance"
                               class="form-control"
                               value="{{ old('opening_balance', $vendor->opening_balance) }}"
                               min="0" step="0.01">
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Update Vendor
                    </button>
                    <a href="{{ route('vendors.index') }}"
                       class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection