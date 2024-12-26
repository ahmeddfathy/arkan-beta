@extends('layouts.app')

<head>
    <style>
        .card {
            opacity: 1 !important;
        }
    </style>
</head>
@section('content')
<div class="container-fluid px-4">
    <!-- Header Section with Enhanced Design -->
    <div class="header-section mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-title position-relative">
                    <div class="title-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="text-gradient mb-2">تقرير الحضور والانصراف</h3>
                    <p class="text-muted mb-0">تفاصيل سجل الحضور والانصراف للموظف</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-md-end mt-3 mt-md-0">
                    <!-- Filter Section -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <form action="" method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">الشهر</label>
                                    <select name="month" class="form-select">
                                        <option value="">كل الشهور</option>
                                        @foreach(range(1, 12) as $monthNumber)
                                        @php
                                        $monthName = Carbon\Carbon::create()->month($monthNumber)->translatedFormat('F');
                                        @endphp
                                        <option value="{{ $monthNumber }}" {{ request('month') == $monthNumber ? 'selected' : '' }}>
                                            {{ $monthName }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">السنة</label>
                                    <select name="year" class="form-select">
                                        <option value="">كل السنوات</option>
                                        @foreach(range(now()->year - 2, now()->year) as $year)
                                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">الحالة</label>
                                    <select name="status" class="form-select">
                                        <option value="">كل الحالات</option>
                                        <option value="حضـور" {{ request('status') == 'حضـور' ? 'selected' : '' }}>حضور</option>
                                        <option value="غيــاب" {{ request('status') == 'غيــاب' ? 'selected' : '' }}>غياب</option>
                                        <option value="عطلة اسبوعية" {{ request('status') == 'عطلة اسبوعية' ? 'selected' : '' }}>عطلة أسبوعية</option>
                                        <option value="اجازة رسمية" {{ request('status') == 'اجازة رسمية' ? 'selected' : '' }}>إجازة رسمية</option>
                                        <option value="مأمورية" {{ request('status') == 'مأمورية' ? 'selected' : '' }}>مأمورية</option>

                                    </select>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter me-1"></i>
                                            تصفية
                                        </button>
                                        @if(request()->hasAny(['month', 'year', 'status']))
                                        <a href="{{ route('attendance.preview', ['employee_id' => $user->employee_id]) }}" class="btn btn-light">
                                            <i class="fas fa-times me-1"></i>
                                            مسح الفلتر
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Employee Info Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card employee-card border-0 shadow-hover mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="avatar-circle-lg">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="col">
                            <h4 class="mb-2 employee-name">{{ $user->name }}</h4>
                            <div class="employee-info d-flex align-items-center flex-wrap">
                                <span class="badge bg-primary me-3 employee-badge">
                                    <i class="fas fa-id-card me-1"></i>
                                    {{ $user->employee_id }}
                                </span>
                                <span class="department me-3">
                                    <i class="fas fa-building me-1"></i>
                                    {{ $user->department }}
                                </span>
                                <span class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    تاريخ التعيين: {{ $user->start_date_of_employment }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- قسم الإحصائيات الرئيسية -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h6 class="mb-2">
                            إحصائيات
                            {{ $attendanceStats['period']['month'] }}
                            {{ $attendanceStats['period']['year'] }}
                            @if(request('status'))
                            - {{ request('status') }}
                            @endif
                        </h6>
                        <div class="row text-center g-3">
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h4 class="mb-0">{{ $attendanceStats['present_days'] + $attendanceStats['absent_days'] }}</h4>
                                    <small>إجمالي أيام العمل</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h4 class="mb-0">{{ $attendanceStats['present_days'] }}</h4>
                                    <small>أيام الحضور</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border-end">
                                    <h4 class="mb-0">{{ $attendanceStats['absent_days'] }}</h4>
                                    <small>أيام الغياب</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div>
                                    <h4 class="mb-0">
                                        @php
                                        $totalDays = $attendanceStats['present_days'] + $attendanceStats['absent_days'];
                                        $attendanceRate = $totalDays > 0
                                        ? round(($attendanceStats['present_days'] / $totalDays) * 100, 1)
                                        : 0;
                                        @endphp
                                        {{ $attendanceRate }}%
                                    </h4>
                                    <small>نسبة الحضور</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- تفاصيل التأخير -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="stat-icon bg-warning-subtle">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">مرات التأخير</h6>
                                    <h3 class="mb-0">{{ $stats['late'] }}</h3>
                                    <small class="text-muted">
                                        إجمالي {{ $stats['total_delay_hours'] }} ساعة تأخير
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقات الإحصائيات التفصيلية -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card border-0 shadow-hover">
                        <div class="stat-icon bg-success-subtle pulse-success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="stat-details">
                            <h6>أيام الحضور</h6>
                            <h3>{{ $attendanceStats['present_days'] }}</h3>
                            <small class="text-muted">من أصل {{ $attendanceStats['present_days'] + $attendanceStats['absent_days'] }} يوم</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card border-0 shadow-hover">
                        <div class="stat-icon bg-danger-subtle pulse-danger">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="stat-details">
                            <h6>أيام الغياب</h6>
                            <h3>{{ $attendanceStats['absent_days'] }}</h3>
                            <small class="text-muted">من أصل {{ $attendanceStats['present_days'] + $attendanceStats['absent_days'] }} يوم</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card border-0 shadow-hover">
                        <div class="stat-icon bg-warning-subtle pulse-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-details">
                            <h6>مرات التأخير</h6>
                            <h3>{{ $attendanceStats['late_days'] }}</h3>
                            <small class="text-muted">{{ $attendanceStats['total_delay_minutes'] }} دقيقة تأخير</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card border-0 shadow-hover">
                        <div class="stat-icon bg-info-subtle pulse-info">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-details">
                            <h6>المخالفات</h6>
                            <h3>{{ $attendanceStats['violation_days'] }}</h3>
                            <small class="text-muted">عدد أيام المخالفات</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول سجلات الحضور -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">سجل الحضور والانصراف</h5>
                    <div class="btn-group">
                        <button class="btn btn-light btn-sm">
                            <i class="fas fa-filter"></i>
                            تصفية
                        </button>
                        <button class="btn btn-light btn-sm">
                            <i class="fas fa-sort"></i>
                            ترتيب
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mt-5">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>اليوم</th>
                                    <th>الحالة</th>
                                    <th>الوردية</th>
                                    <th>ساعات الوردية</th>
                                    <th>وقت الحضور</th>
                                    <th>وقت الانصراف</th>
                                    <th>التأخير (دقيقة)</th>
                                    <th>الخروج المبكر (دقيقة)</th>
                                    <th>ساعات العمل</th>
                                    <th>الوقت الإضافي</th>
                                    <th>الجزاء</th>
                                    <th>ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendanceRecords as $record)
                                <tr>
                                    <td>{{ $record->attendance_date }}</td>
                                    <td>{{ $record->day }}</td>
                                    <td>
                                        <span>
                                            {{ $record->status }}
                                        </span>
                                    </td>
                                    <td>{{ $record->shift }}</td>
                                    <td>{{ $record->shift_hours }}</td>
                                    <td>{{ $record->entry_time ?? '-' }}</td>
                                    <td>{{ $record->exit_time ?? '-' }}</td>
                                    <td>
                                        @if($record->delay_minutes > 0)
                                        <span class="text-danger">{{ $record->delay_minutes }}</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->early_minutes > 0)
                                        <span class="text-warning">{{ $record->early_minutes }}</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>{{ $record->working_hours ?? '-' }}</td>
                                    <td>
                                        @if($record->overtime_hours > 0)
                                        <span class="text-success">{{ $record->overtime_hours }}</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->penalty > 0)
                                        <span class="text-danger">{{ $record->penalty }}</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->notes)
                                        <span class="text-muted">{{ $record->notes }}</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- إضافة Pagination -->
                        <div class="d-flex justify-content-center py-3">
                            {{ $attendanceRecords->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ألوان اللوجو والثيم */
    :root {
        --primary-blue: #33B5E5;
        --dark-gray: #2C3E50;
        --light-blue: #E3F2FD;
        --success: #4CAF50;
        --danger: #F44336;
        --warning: #FFC107;
    }

    /* تنسيق عام */
    body {
        background-color: #f8f9fa;
    }

    .text-gradient {
        background: linear-gradient(45deg, var(--primary-blue), #2196F3);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 700;
    }

    /* تنسيق البطاقات */
    .card {
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.2s;
    }

    .employee-card {
        background: linear-gradient(120deg, #fff, var(--light-blue));
    }

    /* تنسيق دائرة الأفاتار */
    .avatar-circle-lg {
        width: 80px;
        height: 80px;
        background: linear-gradient(45deg, var(--primary-blue), #2196F3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 32px;
        font-weight: bold;
        box-shadow: 0 4px 20px rgba(51, 181, 229, 0.2);
    }

    /* بطاقات الإحصائيات */
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-details h6 {
        color: var(--dark-gray);
        margin-bottom: 5px;
        font-size: 14px;
    }

    .stat-details h3 {
        margin: 0;
        font-weight: 600;
        color: var(--primary-blue);
    }

    /* تنسيق الجدول */
    .table {
        margin-bottom: 100px !important;

    }

    .table thead th {
        background-color: var(--light-blue);
        color: var(--dark-gray);
        font-weight: 600;
        padding: 15px;
        border: none;
    }

    .table tbody td {
        padding: 15px;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* تنسيق شارات الحالة */
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }

    .status-badge.success {
        background-color: #E8F5E9;
        color: var(--success);
    }

    .status-badge.danger {
        background-color: #FFEBEE;
        color: var(--danger);
    }

    .status-badge.warning {
        background-color: #FFF8E1;
        color: var(--warning);
    }

    /* تنسيق الأزرار */
    .btn-outline-primary {
        border-color: var(--primary-blue);
        color: var(--primary-blue);
    }

    .btn-outline-primary:hover {
        background-color: var(--primary-blue);
        color: white;
    }

    /* تنسيق الترقيم */
    .pagination {
        margin-bottom: 0;
    }

    .page-link {
        color: var(--primary-blue);
        border: none;
        padding: 8px 16px;
        margin: 0 3px;
        border-radius: 8px;
        font-weight: 500;
    }

    .page-item.active .page-link {
        background-color: var(--primary-blue);
        color: white;
    }

    .page-link:hover {
        background-color: var(--light-blue);
        color: var(--primary-blue);
    }

    /* تأثيرات إضافية */
    .note-tooltip {
        cursor: pointer;
    }

    .shadow-hover {
        transition: box-shadow 0.3s;
    }

    .shadow-hover:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* تنسيق الأيقونات */
    .fas {
        width: 16px;
        text-align: center;
    }

    /* تحسينات للموبايل */
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 15px;
        }

        .table-responsive {
            border-radius: 15px;
        }
    }

    /* Enhanced Header Section */
    .header-section {
        position: relative;
        padding: 20px 0;
    }

    .title-icon {
        position: absolute;
        left: -40px;
        top: 50%;
        transform: translateY(-50%);
        width: 30px;
        height: 30px;
        background: linear-gradient(45deg, var(--primary-blue), #2196F3);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
    }

    /* Enhanced Employee Badge */
    .employee-badge {
        padding: 8px 15px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: 500;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 10px rgba(51, 181, 229, 0.15);
    }

    /* Custom Select Styling */
    .custom-select {
        border: 2px solid var(--light-blue);
        border-radius: 20px;
        padding: 8px 15px;
        font-size: 14px;
        color: var(--dark-gray);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .custom-select:focus {
        border-color: var(--primary-blue);
        box-shadow: none;
    }

    /* Pulse Animation for Stat Icons */
    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    .pulse-success {
        animation: pulse 2s infinite;
        background-color: #E8F5E9;
    }

    .pulse-danger {
        animation: pulse 2s infinite;
        background-color: #FFEBEE;
    }

    .pulse-warning {
        animation: pulse 2s infinite;
        background-color: #FFF8E1;
    }

    .pulse-info {
        animation: pulse 2s infinite;
        background-color: #E3F2FD;
    }

    /* Enhanced Card Hover Effects */
    .shadow-hover {
        transition: all 0.3s ease;
    }

    .shadow-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    }

    /* Enhanced Table Design */
    .table thead th {
        background: linear-gradient(45deg, var(--light-blue), #fff);
        border-bottom: 2px solid var(--primary-blue);
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background-color: var(--light-blue) !important;
        transform: scale(1.01);
    }

    /* Responsive Improvements */
    @media (max-width: 768px) {
        .title-icon {
            display: none;
        }

        .header-section {
            text-align: center;
        }

        .employee-info {
            justify-content: center;
        }
    }

    /* تنسيق Pagination */
    .pagination {
        margin-bottom: 0;
    }

    .page-link {
        padding: 0.5rem 0.75rem;
        color: var(--primary-blue);
        background-color: #fff;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        z-index: 2;
        color: #0056b3;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }

    /* تحسين مظهر Pagination على الشاشات الصغيرة */
    @media (max-width: 768px) {
        .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }

        .page-link {
            margin: 2px;
            border-radius: 4px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // تفعيل tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
