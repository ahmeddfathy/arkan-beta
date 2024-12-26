<?php

namespace App\Imports;

use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AttendanceImport implements ToModel
{
    public function model(array $row)
    {
        // تخطي الصف إذا كان فارغاً تماماً
        if (empty($row[1])) {
            return null;
        }

        // Extract only the employee number from "name[number]" format
        $employeeNumber = $this->extractNumber($row[1]);

        // Format attendance date
        $attendanceDate = $this->formatDate($row[2]);

        // معالجة الحالة (status)
        $status = $this->formatStatus($row[4]);

        // معالجة أوقات الدخول والخروج
        $entryTime = null;
        $exitTime = null;

        // إذا كانت الحالة حضور، نقوم بمعالجة الأوقات
        if ($status === 'حضـور') {
            $entryTime = $this->formatTime($row[7]);
            $exitTime = $this->formatTime($row[8]);
        }

        return new AttendanceRecord([
            'employee_id'  => $employeeNumber,
            'attendance_date'  => $attendanceDate ?? now(),
            'day'              => $row[3] ?? null,
            'status'           => $status,
            'shift'            => $row[5] ?? null,
            'shift_hours'      => isset($row[6]) ? (int)$row[6] : 0,
            'entry_time'       => $entryTime,
            'exit_time'        => $exitTime,
            'delay_minutes'    => isset($row[9]) ? (int)$row[9] : 0,
            'early_minutes'    => isset($row[10]) ? (int)$row[10] : 0,
            'working_hours'    => isset($row[11]) ? (int)$row[11] : 0,
            'overtime_hours'   => isset($row[12]) ? (int)$row[12] : 0,
            'penalty'          => $row[13] ?? null,
            'notes'            => $row[14] ?? null,
        ]);
    }

    private function extractNumber($string)
    {
        preg_match('/\[(\d+)\]/', $string, $matches);
        return $matches[1] ?? null;
    }

    private function formatDate($excelDate)
    {
        if (empty($excelDate)) {
            return now();
        }

        // If the input is already in d/m/y format
        if (is_string($excelDate) && preg_match('/^\d{2}\/\d{2}\/\d{2}$/', $excelDate)) {
            try {
                return Carbon::createFromFormat('d/m/y', $excelDate);
            } catch (\Exception $e) {
                return now();
            }
        }

        // If the input is an Excel numeric date
        if (is_numeric($excelDate)) {
            try {
                return Carbon::instance(Date::excelToDateTimeObject($excelDate));
            } catch (\Exception $e) {
                return now();
            }
        }

        return now();
    }

    private function formatTime($timeString)
    {
        if (empty($timeString)) {
            return null;
        }

        // If it's a string in HH:mm:ss format
        if (is_string($timeString) && preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $timeString)) {
            return date('H:i:s', strtotime($timeString));
        }

        // For Excel time values
        if (is_numeric($timeString)) {
            $totalSeconds = round($timeString * 86400);
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $seconds = $totalSeconds % 60;
            $hours = $hours % 24;

            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return null;
    }

    private function formatStatus($status)
    {
        if (empty($status)) {
            return 'غيــاب';
        }

        // تنظيف النص من المسافات الزائدة
        $status = trim($status);

        // قائمة الحالات المقبولة مع معالجة الحالات المختلفة للعطلة الأسبوعية
        $statusMap = [
            'حضـور' => 'حضـور',
            'حضور' => 'حضـور',
            'غيــاب' => 'غيــاب',
            'غياب' => 'غيــاب',
            'عطلة اسبوعية' => 'عطلة اسبوعية',
            'عطلة أسبوعية' => 'عطلة اسبوعية',
            'عطله اسبوعيه' => 'عطلة اسبوعية',
            'عطلة' => 'عطلة اسبوعية',
            'اجازة رسمية' => 'اجازة رسمية',
            'إجازة رسمية' => 'اجازة رسمية',
            'مأمورية' => 'مأمورية',
            'مامورية' => 'مأمورية',
            'اذن' => 'اذن',
            'إذن' => 'اذن'
        ];

        // البحث عن الحالة في القائمة
        foreach ($statusMap as $key => $value) {
            if (strcasecmp($status, $key) === 0) {
                return $value;
            }
        }

        // التحقق من إذا كان النص يحتوي على "عطلة" أو "عطله"
        if (preg_match('/(عطلة|عطله)/i', $status)) {
            return 'عطلة اسبوعية';
        }

        // إذا كانت الحالة غير معروفة
        return 'غيــاب';
    }
}
