<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Account</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            padding-top: 50px;
        }

        .form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 32px;
            font-weight: 600;
            color: #007bff;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.25rem rgba(38, 143, 255, 0.25);
        }

        .btn {
            background-color: #007bff;
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
        }

        .btn:hover {
            background-color: #0056b3;
            color: white;
        }

        .form-footer a {
            color: #007bff;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="form-container mx-auto col-lg-8 col-md-10 col-sm-12">
            <h1>{{ __('Create an Account') }}</h1>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <label for="name" class="form-label">{{ __('Name') }}</label>
                        <x-input id="name" class="form-control" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <x-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <x-input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" />
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                        <x-input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" />
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label for="employee_number" class="form-label">Employee Number</label>
                        <input id="employee_number" class="form-control" type="text" name="employee_id" value="{{ old('employee_number') }}" required placeholder="Enter your employee number">
                    </div>

                    <div class="col-md-6">
                        <label for="age" class="form-label">Age</label>
                        <input id="age" class="form-control" type="number" name="age" value="{{ old('age') }}" required placeholder="Enter your age">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input id="date_of_birth" class="form-control" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="national_id_number" class="form-label">National ID Number</label>
                        <input id="national_id_number" class="form-control" type="text" name="national_id_number" value="{{ old('national_id_number') }}" required placeholder="Enter your national ID number">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input id="phone_number" class="form-control" type="text" name="phone_number" value="{{ old('phone_number') }}" required placeholder="Enter your phone number">
                    </div>

                    <div class="col-md-6">
                        <label for="start_date_of_employment" class="form-label">Start Date of Employment</label>
                        <input id="start_date_of_employment" class="form-control" type="date" name="start_date_of_employment" value="{{ old('start_date_of_employment') }}" required>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label for="last_contract_start_date" class="form-label">Last Contract Start Date</label>
                        <input id="last_contract_start_date" class="form-control" type="date" name="last_contract_start_date" value="{{ old('last_contract_start_date') }}">
                    </div>

                    <div class="col-md-6">
                        <label for="last_contract_end_date" class="form-label">Last Contract End Date</label>
                        <input id="last_contract_end_date" class="form-control" type="date" name="last_contract_end_date" value="{{ old('last_contract_end_date') }}">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label for="job_progression" class="form-label">Job Progression</label>
                        <input id="job_progression" class="form-control" type="text" name="job_progression" value="{{ old('job_progression') }}" placeholder="Enter your job progression">
                    </div>

                    <div class="col-md-6">
                        <label for="department" class="form-label">Department</label>
                        <input id="department" class="form-control" type="text" name="department" value="{{ old('department') }}" placeholder="Enter your department">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label for="gender" class="form-label">Gender</label>
                        <select id="gender" class="form-control" name="gender" required>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="address" class="form-label">Address</label>
                        <input id="address" class="form-control" type="text" name="address" value="{{ old('address') }}" placeholder="Enter your address">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label for="education_level" class="form-label">Education Level</label>
                        <input id="education_level" class="form-control" type="text" name="education_level" value="{{ old('education_level') }}" placeholder="Enter your education level">
                    </div>

                    <div class="col-md-6">
                        <label for="marital_status" class="form-label">Marital Status</label>
                        <select id="marital_status" class="form-control" name="marital_status">
                            <option value="" {{ old('marital_status') == '' ? 'selected' : '' }}>Select status</option>
                            <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Single</option>
                            <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Married</option>
                            <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label for="number_of_children" class="form-label">Number of Children</label>
                        <input id="number_of_children" class="form-control" type="number" name="number_of_children" value="{{ old('number_of_children') }}" placeholder="Enter number of children">
                    </div>

                    <div class="col-md-6">
                        <label for="employee_status" class="form-label">Employee Status</label>
                        <select id="employee_status" class="form-control" name="employee_status">
                            <option value="" {{ old('employee_status') == '' ? 'selected' : '' }}>Select status</option>
                            <option value="active" {{ old('employee_status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('employee_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="on_leave" {{ old('employee_status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        </select>
                    </div>
                </div>


                <div class="d-flex justify-content-between align-items-center mt-4 form-footer">
                    <a href="{{ route('login') }}">{{ __('Already registered? Login here') }}</a>
                    <x-button class="btn">
                        {{ __('Register') }}
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
