<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class AttendanceReportService
{
    public function previewAttendance($employee_id, $attendanceStats)
    {
        $user = User::where('employee_id', $employee_id)->firstOrFail();

        // تحديد الشهر والسنة من الطلب
        $month = is_numeric(request('month')) ? (int)request('month') : now()->month;
        $year = is_numeric(request('year')) ? (int)request('year') : now()->year;

        // الاستعلام الأساسي
        $query = AttendanceRecord::query()
            ->where('employee_id', $employee_id)
            ->when(is_numeric(request('month')), function ($q) use ($month) {
                return $q->whereMonth('attendance_date', $month);
            })
            ->when(is_numeric(request('year')), function ($q) use ($year) {
                return $q->whereYear('attendance_date', $year);
            })
            ->when(request('status'), function ($q) {
                return $q->where('status', request('status'));
            });

        // حساب الإحصائيات للفترة المحددة
        $stats = [
            'month_name' => Carbon::create()->month($month)->translatedFormat('F'),
            'year' => $year,
            'working_days' => $query->count(),
            'present' => (clone $query)->where('status', 'حضـور')->count(),
            'late' => (clone $query)->where('delay_minutes', '>', 0)->count(),
            'total_delay_hours' => round((clone $query)->sum('delay_minutes') / 60, 1)
        ];

        // الحصول على السجلات مع الترقيم
        $attendanceRecords = $query->orderBy('attendance_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        // تحضير قائمة الشهور للفلتر
        $months = collect(range(1, 12))->map(function ($month) {
            return [
                'value' => $month,
                'label' => Carbon::create()->month($month)->translatedFormat('F')
            ];
        });

        // تحضير قائمة السنوات للفلتر
        $years = collect(range(now()->year - 2, now()->year))->map(function ($year) {
            return [
                'value' => $year,
                'label' => $year
            ];
        });

        return view('attendance.preview', compact(
            'user',
            'attendanceRecords',
            'stats',
            'months',
            'years',
            'attendanceStats'
        ));
    }
}
