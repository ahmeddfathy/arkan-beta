<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Models\AttendanceRecord;

class AttendanceReportService
{
    public function generatePDF($employee_id)
    {
        $user = User::where('employee_id', $employee_id)->firstOrFail();

        $query = AttendanceRecord::where('employee_id', $employee_id);

        // الفلترة حسب الشهر (الشهر الحالي كديفولت)
        $month = request('month', now()->month);
        $year = request('year', now()->year);

        $query->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year);

        $attendanceRecords = $query->orderBy('attendance_date', 'desc')->get();

        // حساب الإحصائيات
        $stats = [
            'present' => $query->where('status', 'present')->count(),
            'absent' => $query->where('status', 'absent')->count(),
            'late' => $query->where('delay_minutes', '>', 0)->count(),
            'early_leave' => $query->where('early_minutes', '>', 0)->count(),
            'total_delay' => $query->sum('delay_minutes'),
            'total_early' => $query->sum('early_minutes'),
            'month_name' => \Carbon\Carbon::create()->month($month)->translatedFormat('F'),
            'year' => $year,
            'working_days' => $query->count(), // إجمالي أيام العمل
            'attendance_rate' => $query->count() > 0
                ? round(($query->where('status', 'present')->count() / $query->count()) * 100, 2)
                : 0
        ];

        $data = [
            'user' => $user,
            'attendanceRecords' => $attendanceRecords,
            'stats' => $stats
        ];

        $pdf = Pdf::loadView('pdf.attendance_report', $data);
        return $pdf->download("تقرير_حضور_{$user->name}_{$stats['month_name']}_{$year}.pdf");
    }

    public function previewAttendance($employee_id)
    {
        $user = User::where('employee_id', $employee_id)->firstOrFail();

        // تحديد الشهر الحالي كافتراضي
        $month = request('month', now()->month);
        $year = request('year', now()->year);

        // الاستعلام الأساسي
        $query = AttendanceRecord::query()
            ->where('employee_id', $employee_id)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year);

        // نسخة من الاستعلام للإحصائيات
        $statsQuery = clone $query;

        // حساب الإحصائيات
        $totalRecords = $statsQuery->count();
        $presentCount = $statsQuery->where('status', 'حضور')->count();

        // تصحيح حساب الإحصائيات
        $stats = [
            'month_name' => \Carbon\Carbon::create()->month($month)->translatedFormat('F'),
            'year' => $year,
            'working_days' => $totalRecords, // إجمالي أيام العمل
            'present' => $presentCount, // أيام الحضور (الفعلية)
            'absent' => 0, // لا يوجد غياب حيث أن كل الأيام حضور
            'late' => $statsQuery->where('delay_minutes', '>', 0)->count(), // عدد مرات التأخير
            'total_delay_minutes' => $statsQuery->sum('delay_minutes'), // مجموع دقائق التأخير
            'total_delay_hours' => round($statsQuery->sum('delay_minutes') / 60, 1), // مجموع ساعات التأخير
            'early_leave' => $statsQuery->where('early_minutes', '>', 0)->count(), // عدد مرات الخروج المبكر
            'total_early_minutes' => $statsQuery->sum('early_minutes'), // مجموع دقائق الخروج المبكر
            'attendance_rate' => 100 // نسبة الحضور 100% حيث أن كل الأيام حضور
        ];

        // الحصول على السجلات للعرض
        $attendanceRecords = $query->orderBy('attendance_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        // تحضير قائمة الشهور والسنوات للفلتر
        $months = collect(range(1, 12))->map(function ($month) {
            return [
                'value' => $month,
                'label' => \Carbon\Carbon::create()->month($month)->translatedFormat('F')
            ];
        });

        $years = collect(range(now()->year - 2, now()->year))->map(function ($year) {
            return [
                'value' => $year,
                'label' => $year
            ];
        });

        $filters = [
            'month' => $month,
            'year' => $year,
            'status' => request('status')
        ];

        return view('attendance.preview', compact('user', 'attendanceRecords', 'stats', 'months', 'years', 'filters'));
    }
}
