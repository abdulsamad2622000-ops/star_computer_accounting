@extends('layouts.app')
@section('title', 'Add Staff')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="form-card">
            <h5 class="fw-bold mb-4">
                <i class="bi bi-person-plus me-2"></i>Add New Staff
            </h5>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('staff.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name"
                               class="form-control"
                               placeholder="e.g. Ali Ahmed"
                               value="{{ old('name') }}"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username * (Login ke liye)</label>
                        <input type="text" name="username"
                               class="form-control"
                               placeholder="e.g. ali123"
                               value="{{ old('username') }}"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email (Optional)</label>
                        <input type="email" name="email"
                               class="form-control"
                               placeholder="Optional..."
                               value="{{ old('email') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password"
                               class="form-control"
                               placeholder="Min 6 characters"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="Repeat password"
                               required>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Add Staff
                    </button>
                    <a href="{{ route('staff.index') }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
                
            </form>
        </div>
    </div>
</div>
@endsection