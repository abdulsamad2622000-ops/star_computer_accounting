@extends('layouts.app')
@section('title', 'Edit Staff')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="form-card">
            <h5 class="fw-bold mb-4">
                <i class="bi bi-person-gear me-2"></i>Edit Staff
            </h5>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('staff.update', $staff) }}"
                  method="POST">
                @csrf @method('PUT')
               <div class="row g-3">
    <div class="col-12">
        <label class="form-label">Full Name *</label>
        <input type="text" name="name"
               class="form-control" required
               value="{{ $staff->name }}">
    </div>
    <div class="col-12">
        <label class="form-label">Username * <small class="text-muted">(Login ke liye)</small></label>
        <input type="text" name="username"
               class="form-control" required
               value="{{ $staff->username }}">
    </div>
    <div class="col-12">
        <label class="form-label">Email <small class="text-muted">(Optional)</small></label>
        <input type="email" name="email"
               class="form-control"
               value="{{ $staff->email }}"
               placeholder="Optional...">
    </div>
    <div class="col-12">
        <label class="form-label">
            New Password
            <small class="text-muted">(khali choro agar nahi badalna)</small>
        </label>
        <input type="password" name="password"
               class="form-control"
               minlength="6"
               placeholder="New password...">
    </div>
</div>
@endsection
