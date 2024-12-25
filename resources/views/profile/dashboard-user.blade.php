@extends('layouts.app')

@section('content')

<head>
    <link rel="stylesheet" href="{{asset('css/dashboard.css')}}">
    <style>
        a {
            text-decoration: none;
        }
    </style>
</head>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="text-3xl font-bold mb-2">Welcome, {{ Auth::user()->name }}</h1>
        <p class="text-white/80">Manage your attendance, leaves, and more</p>
    </div>



    <div class="action-cards">
        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-file-alt text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold mb-3">Overtime Requests</h3>
            <a href="{{ route('overtime-requests.index') }}" class="btn-dashboard">
                View Overtime Requests
            </a>
        </div>
        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-user-clock text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold mb-3">Permission Requests</h3>
            <a href="{{ route('permission-requests.index') }}" class="btn-dashboard">
                Manage Permissions
            </a>
        </div>
    </div>

    <div class="action-cards">
        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-user-minus text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold mb-3">Absence Requests</h3>
            <a href="{{ route('absence-requests.index') }}" class="btn-dashboard">
                Manage Absences
            </a>
        </div>
        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-bell text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold mb-3">Notifications</h3>
            <a href="{{ route('notifications') }}" class="btn-dashboard">
                View Notifications
            </a>
        </div>
    </div>

    <div class="action-cards">
        <div class="action-cards">
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-envelope text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Chat</h3>
                <a href="{{ route('chat.index') }}" class="btn-dashboard">
                    Open Chat
                </a>
            </div>
        </div>

    </div>

    @if($salaryFiles->count() > 0)
    <div>

    </div>
<div class="container mt-5 ">
    <h3 class="text-center mb-4 ">Your Salary Sheets</h3>
    <div class="row ">
        @foreach($salaryFiles as $file)

        <div class="col-md-4 mb-4">
            <div class="card shadow-lg h-100 border-0">
                <div class="card-body text-center position-relative">
                    <div class="salary-icon bg-gradient-primary text-white mb-3 d-inline-flex align-items-center justify-content-center">
                        <i class="fas fa-file-invoice-dollar fa-2x"></i>
                    </div>
                    <h5 class="card-title text-dark mb-3">{{ $file->month }}</h5>
                    <a href="{{ url('/salary-sheet/' . $file->employee_id . '/' . $file->month . '/' . basename($file->file_path)) }}"
                        class="btn btn-primary btn-sm px-4 rounded-pill" target="_blank">
                        View Salary Sheet
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="container mt-5">
    <div class="alert alert-info text-center" role="alert">
        <p class="mb-0">No salary sheets available at the moment. Please check back later!</p>
    </div>
</div>
@endif

    @endsection
