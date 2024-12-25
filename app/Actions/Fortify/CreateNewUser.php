<?php
namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // Validate the input data
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'employee_id' => ['required', 'string', 'max:255', Rule::unique(User::class)],
            'age' => ['required', 'integer', 'min:18'],
            'date_of_birth' => ['required', 'date'],
            'national_id_number' => ['required', 'string', 'max:255', Rule::unique(User::class)],
            'phone_number' => ['required', 'string', 'max:15'],
            'start_date_of_employment' => ['required', 'date'],
            'last_contract_start_date' => ['nullable', 'date'],
            'last_contract_end_date' => ['nullable', 'date'],
            'job_progression' => ['nullable', 'string'],
            'department' => ['nullable', 'string'],
            'gender' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'education_level' => ['nullable', 'string'],
            'marital_status' => ['nullable', 'string'],
            'number_of_children' => ['nullable', 'integer'],
            'employee_status' => ['nullable', 'string'],
        ])->validate();

        // Create a new user record with the validated data
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'employee_id' => $input['employee_id'],
            'age' => $input['age'],
            'date_of_birth' => $input['date_of_birth'],
            'national_id_number' => $input['national_id_number'],
            'phone_number' => $input['phone_number'],
            'start_date_of_employment' => $input['start_date_of_employment'],
            'last_contract_start_date' => $input['last_contract_start_date'] ?? null,
            'last_contract_end_date' => $input['last_contract_end_date'] ?? null,
            'job_progression' => $input['job_progression'] ?? null,
            'department' => $input['department'] ?? null,
            'gender' => $input['gender'] ?? null,
            'address' => $input['address'] ?? null,
            'education_level' => $input['education_level'] ?? null,
            'marital_status' => $input['marital_status'] ?? null,
            'number_of_children' => $input['number_of_children'] ?? null,
            'employee_status' => $input['employee_status'] ?? null,
        ]);
    }
}
