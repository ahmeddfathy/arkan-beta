<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AbsenceRequestController;
use App\Http\Controllers\PermissionRequestController;
use App\Http\Controllers\OverTimeRequestsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\MacAddressController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SalarySheetController;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExampleMail;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\OnlineStatusController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\EmployeeStatisticsController;

Route::get('/send-mail', function () {
    $data = [
        'name' => 'Recipient Name',
        'message' => 'This is a test email.'
    ];

    Mail::to('ahmeddfathy087@gmail.com')->send(new ExampleMail($data));

    return 'Email Sent Successfully!';
});




// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('/');

Route::get('/welcome', function () {
    return "hello";
})->name('welcome');

Route::get('/mac-addresses', [MacAddressController::class, 'getMacAddresses']);

// Authentication routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Employee routes
Route::middleware(['auth'])->group(function () {

    Route::post('/absence-requests/{absenceRequest}/status', [AbsenceRequestController::class, 'updateStatus'])
        ->name('absence-requests.updateStatus');
});

// Manager routes
Route::middleware(['auth', 'role:manager'])->group(function () {



    Route::patch('/absence-requests/{absenceRequest}/reset-status', [AbsenceRequestController::class, 'resetStatus'])
        ->name('absence-requests.reset-status');
    Route::patch('/absence-requests/{id}/modify', [AbsenceRequestController::class, 'modifyResponse'])
        ->name('absence-requests.modify');
    Route::post('/permission-requests/{permissionRequest}/update-status', [PermissionRequestController::class, 'updateStatus'])
        ->name('permission-requests.update-status');
    Route::patch('/permission-requests/{permissionRequest}/reset-status', [PermissionRequestController::class, 'resetStatus'])
        ->name('permission-requests.reset-status');
    Route::patch('/permission-requests/{permissionRequest}/modify', [PermissionRequestController::class, 'modifyResponse'])
        ->name('permission-requests.modify');
    Route::patch('/permission-requests/{permissionRequest}/return-status', [PermissionRequestController::class, 'updateReturnStatus'])
        ->name('permission-requests.update-return-status');


    Route::patch('/overtime-requests/{overTimeRequest}/respond', [OverTimeRequestsController::class, 'updateStatus'])
        ->name('overtime-requests.respond');
    Route::patch('/overtime-requests/{overTimeRequest}/reset-status', [OverTimeRequestsController::class, 'resetStatus'])
        ->name('overtime-requests.reset-status');
    Route::patch('/overtime-requests/{overTimeRequest}/modify', [OverTimeRequestsController::class, 'modifyResponse'])
        ->name('overtime-requests.modify');
    Route::patch('/overtime-requests/{id}', [OverTimeRequestsController::class, 'update']);
    Route::delete('/overtime-requests/{overtimeRequest}', [OverTimeRequestsController::class, 'destroy'])->name('overtime-requests.destroy');


    Route::get('/attendance', [AttendanceRecordController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/import', [AttendanceRecordController::class, 'import'])->name('attendance.import');


    Route::resource('/attendances', AttendanceController::class);
    Route::resource('/leaves', LeaveController::class);

    Route::resource('admin/notifications', AdminNotificationController::class, [
        'as' => 'admin'
    ]);
});

Route::resource('users', UserController::class);
Route::post('user/import', [UserController::class, 'import'])->name('user.import');
// Shared routes (Manager & Employee)
Route::middleware(['auth', 'role:manager,employee'])->group(function () {
    // Attendance
    Route::resource('overtime-requests', OverTimeRequestsController::class);

    // Leave
    Route::get('/absence-requests', [AbsenceRequestController::class, 'index'])->name('absence-requests.index');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    Route::get('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');

    Route::get('/user/{employee_id}/attendance-preview', [DashboardController::class, 'previewAttendance'])
        ->name('user.previewAttendance');


    Route::get('/user/{employee_id}/attendance-report', [DashboardController::class, 'generateAttendancePDF'])
        ->name('user.downloadAttendanceReport');


    Route::get('/salary-sheet/{userId}/{month}/{filename}', function ($employee_id, $month, $filename) {
        $user = Auth::user();
        if ($user->employee_id != $employee_id && $user->role != 'manager') {
            abort(403, 'Unauthorized access');
        }
        $filePath = storage_path("app/private/salary_sheets/{$employee_id}/{$month}/{$filename}");
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }
        return response()->file($filePath);
    })->middleware('auth');



    Route::resource('/permission-requests', PermissionRequestController::class);
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages/{receiver}', [ChatController::class, 'getMessages']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::post('/chat/mark-seen', [ChatController::class, 'markAsSeen']);
    Route::post('/status/update', [OnlineStatusController::class, 'updateStatus']);
    Route::get('/status/user/{userId}', [OnlineStatusController::class, 'getUserStatus']);
});
Route::get('/salary-sheets', [SalarySheetController::class, 'index'])->name('salary-sheets.index');
Route::post('/salary-sheets/upload', [SalarySheetController::class, 'upload'])->name('salary-sheets.upload');

// إضافة راوت جديد لتصدير PDF
Route::get('/attendance/{employee_id}/pdf', [DashboardController::class, 'generateAttendancePDF'])
    ->name('attendance.pdf')
    ->middleware(['auth', 'role:manager']);

// مسارات الحضور والانصراف
Route::middleware(['auth'])->group(function () {
    // عرض سجلات الحضور
    Route::get('/attendance/records', [AttendanceRecordController::class, 'index'])
        ->name('attendance.index');

    // استيراد سجلات الحضور
    Route::post('/attendance/import', [AttendanceRecordController::class, 'import'])
        ->name('attendance.import');

    // عرض تفاصيل حضور موظف معين
    Route::get('/attendance/preview/{employee_id}', [DashboardController::class, 'previewAttendance'])
        ->name('attendance.preview');
});

// مسارات مشتركة للجميع (موظف، مدير، ليدر)
Route::middleware(['auth'])->group(function () {
    // الصفحة الرئيسية لطلبات الغياب - متاحة للجميع

    // عمليات CRUD الأساسية للموظف
    Route::post('/absence-requests', [AbsenceRequestController::class, 'store'])->name('absence-requests.store');
    Route::get('/absence-requests/create', [AbsenceRequestController::class, 'create'])->name('absence-requests.create');
    Route::get('/absence-requests/{absenceRequest}/edit', [AbsenceRequestController::class, 'edit'])->name('absence-requests.edit');
    Route::put('/absence-requests/{absenceRequest}', [AbsenceRequestController::class, 'update'])->name('absence-requests.update');
    Route::delete('/absence-requests/{absenceRequest}', [AbsenceRequestController::class, 'destroy'])->name('absence-requests.destroy');
});

// مسارات مشتركة للمدير والليدر
Route::middleware(['auth', 'role:manager,leader'])->group(function () {
    // مسارات خاصة بإدارة الطلبات
    Route::get('/absence-requests', [AbsenceRequestController::class, 'index'])->name('absence-requests.index');

    Route::patch('/absence-requests/{absenceRequest}/reset-status', [AbsenceRequestController::class, 'resetStatus'])
        ->name('absence-requests.reset-status');
    Route::patch('/absence-requests/{id}/modify', [AbsenceRequestController::class, 'modifyResponse'])
        ->name('absence-requests.modify');
    Route::post('/absence-requests/{absenceRequest}/status', [AbsenceRequestController::class, 'updateStatus'])
        ->name('absence-requests.update-status');
    Route::resource('/absence-requests', AbsenceRequestController::class);

    Route::get('/employee-statistics', [EmployeeStatisticsController::class, 'index'])
        ->name('employee-statistics.index');
    Route::get('/employee-statistics/{id}', [EmployeeStatisticsController::class, 'getEmployeeDetails'])
        ->name('employee-statistics.details');
});

// مسارات خاصة بالمدير فقط
Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::post('/attendance/import', [AttendanceRecordController::class, 'import'])->name('attendance.import');
    // ... باقي المسارات الخاصة بالمدير
});

Route::get('/import-users', function () {
    return view('users.import');
})->name('users.import');
