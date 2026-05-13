@extends('layouts.app')
@section('title', 'Staff Management')

@section('content')

<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">
            <i class="bi bi-person-gear me-2"></i>Staff Members
        </span>
        <a href="{{ route('staff.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Staff
        </a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staff as $i => $member)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar" style="width:32px;height:32px;font-size:.75rem">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <strong>{{ $member->name }}</strong>
                        </div>
                    </td>
                    <td>{{ $member->email }}</td>
                    <td>
                        <span class="badge-settled">Staff</span>
                    </td>
                    <td>{{ $member->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('staff.edit', $member) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('staff.destroy', $member) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Staff delete karein?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Koi staff member nahi mila
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection