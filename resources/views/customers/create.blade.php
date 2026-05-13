@extends('layouts.app')
@section('title', 'Add Customer')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="form-card">
            <h5 class="fw-bold mb-4">
                <i class="bi bi-person-plus me-2"></i>Add New Customer
            </h5>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name"
                               class="form-control" required
                               value="{{ old('name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact 1</label>
                        <input type="text" name="contact1"
                               class="form-control"
                               value="{{ old('contact1') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact 2</label>
                        <input type="text" name="contact2"
                               class="form-control"
                               value="{{ old('contact2') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CNIC</label>
                        <input type="text" name="cnic"
                               class="form-control"
                               value="{{ old('cnic') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control"
                                  rows="2">{{ old('address') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Opening Balance (Receivable)</label>
                        <input type="number" name="opening_balance"
                               class="form-control" value="{{ old('opening_balance', 0) }}"
                               min="0" step="0.01">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Save Customer
                    </button>
                    <a href="{{ route('customers.index') }}"
                       class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection