<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\SalarySheet;
use App\Models\AbsenceRequest;
use App\Models\PermissionRequest;
use App\Models\OverTimeRequests;
use App\Models\Violation;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\AttendanceReportService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // تهيئة المتغير بقيم افتراضية
        $attendanceStats = [
            'present_days' => 0,
            'absent_days' => 0,
            'violation_days' => 0,
            'late_days' => 0,
            'total_delay_minutes' => 0,
            'avg_delay_minutes' => 0,
            'max_delay_minutes' => 0
        ];

        // إذا كان المستخدم موظف، نقوم بحساب الإحصائيات
        if ($user && $user->employee_id) {
            // تحديد فترة آخر 3 شهور
            $startDate = now()->subMonths(2)->startOfMonth(); // م������ بداية الشهر قبل الماضي
            $endDate = now()->endOfMonth(); // حتى نهاية الشهر الحالي

            $statsQuery = AttendanceRecord::where('employee_id', $user->employee_id)
                ->whereBetween('attendance_date', [$startDate, $endDate]);

            // حساب أيام الحضور
            $attendanceStats['present_days'] = (clone $statsQuery)
                ->where('status', 'حضـور')
                ->whereNotNull('entry_time')
                ->count();

            // حساب أيام الغياب
            $attendanceStats['absent_days'] = (clone $statsQuery)
                ->where('status', 'غيــاب')
                ->count();

            // حساب المخالفات
            $attendanceStats['violation_days'] = (clone $statsQuery)
                ->where('penalty', '>', 0)
                ->count();

            // حساب التأخير
            $lateRecords = (clone $statsQuery)
                ->where('delay_minutes', '>', 0)
                ->whereNotNull('entry_time')
                ->get();

            $attendanceStats['late_days'] = $lateRecords->count();
            $attendanceStats['total_delay_minutes'] = $lateRecords->sum('delay_minutes');
            $attendanceStats['avg_delay_minutes'] = $lateRecords->count() > 0
                ? round($lateRecords->average('delay_minutes'), 1)
                : 0;
            $attendanceStats['max_delay_minutes'] = $lateRecords->max('delay_minutes') ?? 0;

            // إضافة معلومات الفترة الزمنية
            $attendanceStats['period'] = [
                'start' => $startDate->translatedFormat('F Y'),
                'end' => $endDate->translatedFormat('F Y')
            ];
        }

        // Get today's statistics
        $todayAbsenceRequests = AbsenceRequest::whereDate('absence_date', Carbon::today())->count();
        $todayPermissionRequests = PermissionRequest::whereDate('created_at', Carbon::today())->count();
        $todayOvertimeRequests = OverTimeRequests::whereDate('overtime_date', Carbon::today())->count();
        $todayViolations = Violation::whereDate('created_at', Carbon::today())->count();

        return view('dashboard', compact(
            'attendanceStats',
            'todayAbsenceRequests',
            'todayPermissionRequests',
            'todayOvertimeRequests',
            'todayViolations'
        ));
    }

    public function previewAttendance($employee_id, AttendanceReportService $reportService)
    {
        // تهيئة المتغير بقيم افتراضية
        $attendanceStats = [
            'present_days' => 0,
            'absent_days' => 0,
            'violation_days' => 0,
            'late_days' => 0,
            'total_delay_minutes' => 0,
            'avg_delay_minutes' => 0,
            'max_delay_minutes' => 0
        ];

        // تحديد الشهر والسنة من الطلب أو استخدام القيم الافتراضية
        $month = is_numeric(request('month')) ? (int)request('month') : now()->month;
        $year = is_numeric(request('year')) ? (int)request('year') : now()->year;
        $status = request('status');

        // حساب الإحصائيات للموظف
        $statsQuery = AttendanceRecord::where('employee_id', $employee_id);

        // تطبيق الفلترة حسب الشهر والسنة
        if ($month) {
            $statsQuery->whereMonth('attendance_date', $month);
        }
        if ($year) {
            $statsQuery->whereYear('attendance_date', $year);
        }

        // تطبيق فلتر الحالة
        if ($status) {
            $statsQuery->where('status', $status);
        }

        // حساب أيام الحضور
        $attendanceStats['present_days'] = (clone $statsQuery)
            ->where('status', 'حضـور')
            ->whereNotNull('entry_time')
            ->count();

        // حساب أيام الغياب
        $attendanceStats['absent_days'] = (clone $statsQuery)
            ->where('status', 'غيــاب')
            ->count();

        // حساب المخالفات
        $attendanceStats['violation_days'] = (clone $statsQuery)
            ->where('penalty', '>', 0)
            ->count();

        // حساب التأخير
        $lateRecords = (clone $statsQuery)
            ->where('delay_minutes', '>', 0)
            ->whereNotNull('entry_time')
            ->get();

        $attendanceStats['late_days'] = $lateRecords->count();
        $attendanceStats['total_delay_minutes'] = $lateRecords->sum('delay_minutes');
        $attendanceStats['avg_delay_minutes'] = $lateRecords->count() > 0
            ? round($lateRecords->average('delay_minutes'), 1)
            : 0;
        $attendanceStats['max_delay_minutes'] = $lateRecords->max('delay_minutes') ?? 0;

        // إضافة معلومات الفترة الزمنية
        $attendanceStats['period'] = [
            'month' => $month ? Carbon::create()->month($month)->translatedFormat('F') : 'كل الش��ور',
            'year' => $year ?: 'كل السنوات'
        ];

        return $reportService->previewAttendance($employee_id, $attendanceStats);
    }
}
