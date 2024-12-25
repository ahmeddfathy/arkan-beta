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
        if (empty($row[1]) || empty($row[2]) || empty($row[6])) {
            return null;
        }

        // Extract only the employee number from "name[number]" format
        $employeeNumber = $this->extractNumber($row[1]);


        // Format attendance date

        $attendanceDate = $this->formatDate($row[2]);
        

        // Entry and exit times
        $entryTime = $this->formatTime($row[7]);
        $exitTime = $this->formatTime($row[8]);

        return new AttendanceRecord([
            'employee_id'  => $employeeNumber ?? null,
            'attendance_date'  => $attendanceDate ?? '2001-9-14',
            'day'              => $row[3] ?? null,
            'status'           => $row[4] ?? null,
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

    // Helper to extract the number from "name[number]"
    private function extractNumber($string)
    {
        preg_match('/\[(\d+)\]/', $string, $matches);
        return $matches[1] ?? null; // Return the number if found, otherwise null
    }

    // Helper to format date from Excel serial number
    private function formatDate($excelDate)
    {
        // If the input is already in d/m/y format
        if (is_string($excelDate) && preg_match('/^\d{2}\/\d{2}\/\d{2}$/', $excelDate)) {
            try {
                return Carbon::createFromFormat('d/m/y', $excelDate);
            } catch (\Exception $e) {
                return null;
            }
        }

        // If the input is an Excel numeric date
        if (!empty($excelDate) && is_numeric($excelDate)) {
            try {
                return Carbon::instance(Date::excelToDateTimeObject($excelDate));
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    // Helper to format time from Excel
    private function formatTime($timeString)
    {
        // If empty, return null
        if (empty($timeString)) {
            return null;
        }

        // If it's a string in HH:mm:ss format
        if (is_string($timeString) && preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $timeString)) {
            return date('H:i:s', strtotime($timeString));
        }

        // For Excel time values (which are decimal numbers representing fractions of a day)
        if (is_numeric($timeString)) {
            // Convert decimal hours to seconds
            $totalSeconds = round($timeString * 86400); // 86400 seconds in a day

            // Calculate hours, minutes, and seconds
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $seconds = $totalSeconds % 60;

            // Ensure hours don't exceed 24
            $hours = $hours % 24;

            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return null;
    }

    // Helper to format time from Excel

}
