@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">User Profile</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Name</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Employee Number</th>
                            <td>{{ $user->employee_id }}</td>
                        </tr>
                        <tr>
                            <th>Age</th>
                            <td>{{ $user->age }}</td>
                        </tr>
                        <tr>
                            <th>Date of Birth</th>
                            <td>{{ $user->date_of_birth }}</td>
                        </tr>
                        <tr>
                            <th>National ID Number</th>
                            <td>{{ $user->national_id_number }}</td>
                        </tr>
                        <tr>
                            <th>Phone Number</th>
                            <td>{{ $user->phone_number }}</td>
                        </tr>
                        <tr>
                            <th>Start Date of Employment</th>
                            <td>{{ $user->start_date_of_employment }}</td>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td>{{ $user->department }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Back to Users List</a>
            </div>
        </div>
    </div>
</div>
@endsection
