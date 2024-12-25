@extends('layouts.app')

@section('content')
<head>
    <link rel="stylesheet" href="{{asset('css/dashboard.css')}}">
</head>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="text-3xl font-bold text-white mb-2">Welcome, {{ Auth::user()->name }}</h1>
        <p class="text-white/80">Manage your team's attendance and performance</p>
    </div>

    <div class="quick-actions">
        <a href="{{ route('users.index') }}" class="quick-action-btn">
            <i class="fas fa-users mb-2"></i>
            <span>Manage Users</span>
        </a>
        <a href="{{ route('salary-sheets.index') }}" class="quick-action-btn">
            <i class="fas fa-file-invoice-dollar mb-2"></i>
            <span>Salary Sheets</span>
        </a>
        <a href="{{ route('chat.index') }}" class="quick-action-btn">
            <i class="fas fa-comments mb-2"></i>
            <span>Team Chat</span>
        </a>
        <a href="{{ route('notifications') }}" class="quick-action-btn">
            <i class="fas fa-bell mb-2"></i>
            <span>Notifications</span>
        </a>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users text-white text-xl"></i>
            </div>
            <div class="stat-value">{{ $totalEmployees }}</div>
            <div class="stat-label">Total Employees</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-check text-white text-xl"></i>
            </div>
            <div class="stat-value">{{ $presentToday }}</div>
            <div class="stat-label">Present Today</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-clock text-white text-xl"></i>
            </div>
            <div class="stat-value">{{ $checkedOutToday }}</div>
            <div class="stat-label">On Leave</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line text-white text-xl"></i>
            </div>
            <div class="stat-value">{{ $attendanceRate }}%</div>
            <div class="stat-label">Attendance Rate</div>
        </div>
    </div>

    <!-- New Section: Today's Statistics -->
    <div class="stats-container mt-6">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-times text-white text-xl"></i>
            </div>
            <div class="stat-value">{{ $todayAbsenceRequests }}</div>
            <div class="stat-label">Absence Requests Today</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-door-open text-white text-xl"></i>
            </div>
            <div class="stat-value">{{ $todayPermissionRequests }}</div>
            <div class="stat-label">Permission Requests Today</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-business-time text-white text-xl"></i>
            </div>
            <div class="stat-value">{{ $todayOvertimeRequests }}</div>
            <div class="stat-label">Overtime Requests Today</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
            </div>
            <div class="stat-value">{{ $todayViolations }}</div>
            <div class="stat-label">Violations Today</div>
        </div>
    </div>

    <div class="requests-summary">
        <h2 class="text-xl font-semibold mb-4">Pending Requests</h2>
        <div class="request-stat">
            <i class="fas fa-clock mr-3"></i>
            <a href="{{ route('overtime-requests.index') }}" class="flex-1">
                Overtime Requests
            </a>
        </div>
        <div class="request-stat">
            <i class="fas fa-user-clock mr-3"></i>
            <a href="{{ route('absence-requests.index') }}" class="flex-1">
                Absence Requests
            </a>
        </div>
        <div class="request-stat">
            <i class="fas fa-door-open mr-3"></i>
            <a href="{{ route('permission-requests.index') }}" class="flex-1">
                Permission Requests
            </a>
        </div>
    </div>

    <div class="action-cards">
        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-clock text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold mb-3">Mark Attendance</h3>
            <a href="/attendances" class="btn-dashboard">
                Mark Attendance
            </a>
        </div>
        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-calendar-plus text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold mb-3">Mark Leave</h3>
            <a href="/leaves" class="btn-dashboard">
                Request Leave
            </a>
        </div>
    </div>

    <div class="action-cards mt-5">
        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-file-import text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold mb-3">Import Data</h3>
            <p class="text-gray-600 mb-4">Upload attendance records and user data</p>
            <div class="row g-2">
                <div class="col-6">
                    <a href="/attendance" class="btn-dashboard">Import Attendance</a>
                </div>
                <div class="col-6">
                    <a href="/users" class="btn-dashboard">Import Users</a>
                </div>
            </div>
        </div>
    </div>

    <div class="action-cards mt-5">
        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-file-alt text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold mb-3">Reports & Documents</h3>
            <p class="text-gray-600 mb-4">Access and manage important documents</p>
            <div class="space-y-2">
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('attendance.index') }}" class="btn-dashboard btn-block">Attendance Records</a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('salary-sheets.index') }}" class="btn-dashboard btn-block">Salary Sheets</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
