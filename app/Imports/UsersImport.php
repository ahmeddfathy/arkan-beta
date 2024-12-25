<?php
namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;

class UsersImport implements ToModel
{


    public function model(array $row)
    {
        if (empty($row[1]) || empty($row[6]) || empty($row[10])) {
            return null;
        }

        // Validate and format the date_of_birth
        $dateOfBirth = null;
        if (!empty($row[8]) && is_numeric($row[8])) {
            try {
                $dateOfBirth = Carbon::createFromFormat('Y-m-d', Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[8]))->format('Y-m-d'));
            } catch (\Exception $e) {
                $dateOfBirth = null; // Fallback for invalid dates
            }
        }

        // Validate and convert Excel serial number to date for start_date_of_employment
        $startDateOfEmployment = null;
        if (!empty($row[16]) && is_numeric($row[16])) {
            $startDateOfEmployment = Carbon::createFromFormat('Y-m-d', Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[16]))->format('Y-m-d'));
        }





        $age = 0; // Default age
        if ($dateOfBirth) {
            $now = Carbon::now();
            $age = abs($now->diffInYears($dateOfBirth));
            if ($now->month < $dateOfBirth->month || ($now->month == $dateOfBirth->month && $now->day < $dateOfBirth->day)) {
                $age--;
            }
        }






        return new User([
            'employee_id' => $row[2],
            'name' => $row[1] ?? null,
            'gender' => $row[4] ?? null,
            'password' => bcrypt($row[11] ?? 'default_password'),
            'address' => $row[5] ?? null,
            'phone_number' => $row[6] ?? null,
            'email' => $row[7] ?? null,
            'date_of_birth' => $dateOfBirth ?? '1900-01-01',
            'age' => intval($age) ?? null,
            'national_id_number' => $row[11] ?? null,
            'education_level' => $row[12] ?? null,
            'marital_status' => $row[13] ?? null,
            'number_of_children' => isset($row[14]) && is_numeric($row[14]) ? (int)$row[14] : 0,
            'department' => $row[15] ?? null,
            'start_date_of_employment' => $startDateOfEmployment ?? '1900-01-01', // Default if invalid
            'employee_status' => $row[20] ?? 'active',
            'last_contract_start_date' => null,
            'last_contract_end_date' => null,
            'job_progression' => null,
            
        ]);
    }


}
