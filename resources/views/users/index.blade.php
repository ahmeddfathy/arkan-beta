@extends('layouts.app')
<head>
    <style>
        .card{
            opacity: 1 !important;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
</head>
@section('content')
<div class="container-fluid px-4">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Search Form -->
    <div class="card search-card mb-4">
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Employee Name</label>
                        <input type="text" class="form-control search-input" name="employee_name"
                            value="{{ request('employee_name') }}" placeholder="Search by name..." list="employees_list">
                        <datalist id="employees_list">
                            @foreach ($employees as $employee)
                            <option value="{{ $employee->name }}">
                                @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Department</label>
                        <select class="form-select search-input" name="department">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->department }}" {{ request('department') == $dept->department ? 'selected' : '' }}>
                                {{ $dept->department }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select search-input" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">User Information</h4>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import"></i> Import Users
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Employee ID</th>
                            <th>Department</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->employee_id }}</td>
                            <td>{{ $user->department }}</td>
                            <td>{{ $user->phone_number }}</td>
                            <td>
                                <span class="badge bg-{{ $user->employee_status == 'active' ? 'success' : 'danger' }}">
                                    {{ $user->employee_status }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No users found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('user.import') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Choose Excel File</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
