@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="form-card">
            <h5 class="fw-bold mb-4">
                <i class="bi bi-person-circle me-2"></i>My Profile
            </h5>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name"
                           class="form-control"
                           value="{{ $user->name }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email"
                           class="form-control"
                           value="{{ $user->email }}"
                           readonly
                           style="background:#f4f6fb">
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <input type="text"
                           class="form-control"
                           value="{{ ucfirst($user->role) }}"
                           readonly
                           style="background:#f4f6fb">
                </div>

                <hr>
                <p class="fw-bold text-muted" style="font-size:.85rem">
                    Password Change
                </p>

                <div class="mb-3">
                    <label class="form-label">Current Password *</label>
                    <input type="password"
                           name="current_password"
                           class="form-control"
                           placeholder="Purana password..."
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password"
                           name="new_password"
                           class="form-control"
                           placeholder="Naya password (optional)..."
                           minlength="6">
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password"
                           name="new_password_confirmation"
                           class="form-control"
                           placeholder="Dobara naya password...">
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check-lg me-1"></i> Update Profile
                </button>
            </form>
        </div>
    </div>
</div>
@endsection