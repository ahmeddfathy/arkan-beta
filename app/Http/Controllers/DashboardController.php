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
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\AttendanceReportService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get total employees
        $totalEmployees = User::count();

        // Get present employees today
        $presentToday = Attendance::whereDate('check_in_time', Carbon::today())->count();

        // Get employees who have checked out today
        $checkedOutToday = Leave::whereDate('check_out_time', Carbon::today())->count();

        // Calculate attendance rate
        $attendanceRate = $totalEmployees > 0 ? ($presentToday / $totalEmployees) * 100 : 0;

        // Get today's statistics
        $todayAbsenceRequests = AbsenceRequest::whereDate('absence_date', Carbon::today())->count();
        $todayPermissionRequests = PermissionRequest::whereDate('created_at', Carbon::today())->count();
        $todayOvertimeRequests = OverTimeRequests::whereDate('overtime_date', Carbon::today())->count();
        $todayViolations = Violation::whereDate('created_at', Carbon::today())->count();

        // Get salary files for employee
        $salaryFiles = SalarySheet::where('employee_id', $user->employee_id)->get();

        // Return appropriate view based on user role
        if ($user->role == 'manager') {
            return view('dashboard', compact(
                'totalEmployees',
                'presentToday',
                'checkedOutToday',
                'attendanceRate',
                'todayAbsenceRequests',
                'todayPermissionRequests',
                'todayOvertimeRequests',
                'todayViolations'
            ));
        } elseif ($user->role == 'employee') {
            return view('profile.dashboard-user', compact('salaryFiles'));
        }

        return view('welcome');
    }

    public function generateAttendancePDF($employee_id, AttendanceReportService $reportService)
    {
        return $reportService->generatePDF($employee_id);
    }

    public function previewAttendance($employee_id, AttendanceReportService $reportService)
    {
        return $reportService->previewAttendance($employee_id);
    }
}
