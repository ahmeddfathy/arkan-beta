@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Messages -->
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @elseif(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <head>
        <link rel="stylesheet" href="{{asset('css/user.css')}}">


    </head>

    <!-- Search Form -->
    <div class="card mb-4 mt-5">
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET" class="form-inline">
                <div class="input-group w-100">
                    <div class="col-md-4">
                        <label for="employee_name" class="form-label">Employee Name</label>
                        <input type="text" class="form-control" style="height: 60%; border-style: dashed" id="employee_name" name="employee_name"
                            value="{{ request('employee_name') }}" placeholder="Search by name..." list="employees_list">

                        <!-- Datalist for employee names -->
                        <datalist id="employees_list">
                            @foreach ($employees as $employee)

                            <optio value="{{$employee->name}}" />
                            @endforeach
                        </datalist>
                    </div>

            </form>
        </div>
    </div>

    <!-- Import Button -->
    <div class="d-flex mb-4 mt-5">
        <button type="button" class="btn btn-primary mr-3" data-bs-toggle="modal" data-bs-target="#importModal">
            Import Users
        </button>
    </div>



    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">User Information</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Employee Number</th>
                            <th>Age</th>
                            <th>Date of Birth</th>
                            <th>National ID Number</th>
                            <th>Phone Number</th>
                            <th>Start Date of Employment</th>
                            <th>Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->employee_id }}</td>
                            <td>{{ $user->age }}</td>
                            <td>{{ $user->date_of_birth }}</td>
                            <td>{{ $user->national_id_number }}</td>
                            <td>{{ $user->phone_number }}</td>
                            <td>{{ $user->start_date_of_employment }}</td>
                            <td>{{ $user->department }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No users available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

   <!-- Import Modal -->
   <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('user.import') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fileInput" class="form-label">Choose File</label>
                            <input type="file" id="fileInput" name="file" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



@endsection
