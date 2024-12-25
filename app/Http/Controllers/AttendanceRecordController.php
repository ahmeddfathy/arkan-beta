<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AttendanceImport;
use App\Models\AttendanceRecord;
use App\Models\User;

class AttendanceRecordController extends Controller
{
    public function index(Request $request)
    {
        // Get employees with both ID and name for the datalist
        $employees = User::select('id', 'name', 'employee_id')
            ->orderBy('name')
            ->get();

        // Query builder for attendance records
        $query = AttendanceRecord::query()
            ->join('users', 'attendance_records.employee_id', '=', 'users.employee_id')
            ->select('attendance_records.*', 'users.name as employee_name');

        // Get selected month and year (default to current month)
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $startOfMonth = \Carbon\Carbon::parse($selectedMonth)->startOfMonth();
        $endOfMonth = \Carbon\Carbon::parse($selectedMonth)->endOfMonth();

        // Apply employee filter if provided
        if ($request->has('employee_filter') && !empty($request->employee_filter)) {
            $query->where('attendance_records.employee_id', $request->employee_filter);
        }

        // Apply date filter
        $query->whereBetween('attendance_date', [$startOfMonth, $endOfMonth]);

        // Get paginated results
        $records = $query->orderBy('attendance_date', 'desc')
            ->paginate(10)
            ->appends($request->except('page'));

        // حساب إحصائيات الحضور والغياب
        $attendanceStats = [
            'present_days' => 0,
            'absent_days' => 0,
            'violation_days' => 0,
            'late_days' => 0,
            'total_delay_minutes' => 0
        ];

        if ($request->has('employee_filter') && !empty($request->employee_filter)) {
            $statsQuery = AttendanceRecord::where('employee_id', $request->employee_filter)
                ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth]);

            $attendanceStats['present_days'] = (clone $statsQuery)
                ->where('status', 'like', '%حضور%')
                ->count();

            $attendanceStats['absent_days'] = (clone $statsQuery)
                ->where('status', 'like', '%غياب%')
                ->count();

            $attendanceStats['violation_days'] = (clone $statsQuery)
                ->where('penalty', '>', 0)
                ->count();

            $lateRecords = (clone $statsQuery)
                ->where('delay_minutes', '>', 0)
                ->get();

            $attendanceStats['late_days'] = $lateRecords->count();
            $attendanceStats['total_delay_minutes'] = $lateRecords->sum('delay_minutes');
            $attendanceStats['avg_delay_minutes'] = $lateRecords->count() > 0
                ? round($lateRecords->average('delay_minutes'), 1)
                : 0;
            $attendanceStats['max_delay_minutes'] = $lateRecords->max('delay_minutes') ?? 0;
        }

        // Get the selected employee name for displaying in input
        $selectedEmployeeName = '';
        if ($request->has('employee_filter') && !empty($request->employee_filter)) {
            $selectedEmployee = $employees->firstWhere('employee_id', $request->employee_filter);
            $selectedEmployeeName = $selectedEmployee ? $selectedEmployee->name : '';
        }

        return view('attendancesRecord.index', compact(
            'records',
            'employees',
            'selectedEmployeeName',
            'attendanceStats',
            'selectedMonth'
        ));
    }



    public function import(Request $request)
    {
        Excel::import(new AttendanceImport, $request->file('file'));
        return redirect()->route('attendance.index')->with('success', 'Records imported successfully');
    }
}
