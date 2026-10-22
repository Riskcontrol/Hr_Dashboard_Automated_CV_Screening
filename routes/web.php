<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KeywordSetController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\StaffProfileController;
use App\Http\Controllers\Admin\LeaveRequestController;
use App\Http\Controllers\Admin\ComplianceController;
use App\Http\Controllers\Admin\KpiController;
use App\Http\Controllers\Admin\AppraisalController;
use App\Http\Controllers\Admin\AppraisalScheduleController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ExitReportController;
use App\Http\Controllers\Admin\EmployeeRulesController;
use App\Http\Controllers\Admin\PensionController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ApplicationSubmissionController;
use App\Http\Controllers\StaffPortalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

// -----------------------------------------------------------------------
// PUBLIC ROUTES
// -----------------------------------------------------------------------
Route::get('/', [ApplicationSubmissionController::class, 'index'])->name('application.form');
Route::post('/application', [ApplicationSubmissionController::class, 'store'])->name('application.store');
Route::get('/application/success/{application}', [ApplicationSubmissionController::class, 'success'])->name('application.success');
Route::get('/application/status/{application}', [ApplicationSubmissionController::class, 'status'])->name('application.status');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// -----------------------------------------------------------------------
// STAFF PORTAL ROUTES
// -----------------------------------------------------------------------
Route::middleware(['auth'])->prefix('my')->name('my.')->group(function () {

    Route::get('/', [StaffPortalController::class, 'dashboard'])->name('dashboard');

    // My Leave
    Route::get('/leave', [StaffPortalController::class, 'leaveIndex'])->name('leave.index');
    Route::get('/leave/create', [StaffPortalController::class, 'leaveCreate'])->name('leave.create');
    Route::post('/leave', [StaffPortalController::class, 'leaveStore'])->name('leave.store');
    Route::get('/leave/{leave}', [StaffPortalController::class, 'leaveShow'])->name('leave.show');

    // My Appraisals
    Route::get('/appraisals', [StaffPortalController::class, 'appraisalIndex'])->name('appraisals.index');
    Route::get('/appraisals/{appraisal}', [StaffPortalController::class, 'appraisalShow'])->name('appraisals.show');
    Route::get('/appraisals/{appraisal}/fill', [StaffPortalController::class, 'appraisalFill'])->name('appraisals.fill');
    Route::post('/appraisals/{appraisal}/save', [StaffPortalController::class, 'appraisalSave'])->name('appraisals.save');

    // Employee Rules PDF
    Route::get('/employee-rules', [StaffPortalController::class, 'employeeRules'])->name('employee-rules');
    Route::get('/employee-rules/{employeeRule}/download', [StaffPortalController::class, 'employeeRulesDownload'])->name('employee-rules.download');

    // Staff changes their own password
    Route::get('/password', function () { return view('my.change-password'); })->name('password');
    Route::post('/password', [UserManagementController::class, 'updateOwnPassword'])->name('update-password');
});

// -----------------------------------------------------------------------
// ADMIN ROUTES
// -----------------------------------------------------------------------
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Job Positions
    Route::resource('keyword-sets', KeywordSetController::class);
    Route::patch('/keyword-sets/{keywordSet}/toggle-status', [KeywordSetController::class, 'toggleStatus'])
        ->name('keyword-sets.toggle-status');

    // Module 1: Staff Profiles
    Route::resource('staff', StaffProfileController::class);

    // Module 2: Annual Leave Tracking
    Route::resource('leave', LeaveRequestController::class);
    Route::post('/leave/{leave}/approve', [LeaveRequestController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/{leave}/reject',  [LeaveRequestController::class, 'reject'])->name('leave.reject');

    // Module 3: KPI Tracking
    Route::resource('kpis', KpiController::class);

    // Module 4: Compliance Tracking
    Route::resource('compliance', ComplianceController::class);

    // Module 5: Probation & Appraisals
    Route::resource('appraisals', AppraisalController::class);
    Route::post('/appraisals/{appraisal}/send', [AppraisalController::class, 'sendForm'])->name('appraisals.send');

    // Appraisal Schedule
    Route::get('/appraisal-schedule', [AppraisalScheduleController::class, 'index'])->name('appraisal-schedule.index');
    Route::post('/appraisal-schedule', [AppraisalScheduleController::class, 'store'])->name('appraisal-schedule.store');
    Route::delete('/appraisal-schedule/{appraisalSchedule}', [AppraisalScheduleController::class, 'destroy'])->name('appraisal-schedule.destroy');

    // Module 6: Absenteeism Tracking
    Route::resource('attendance', AttendanceController::class);
    Route::get('/attendance/staff/{staff}', [AttendanceController::class, 'staffReport'])
        ->name('attendance.staff-report');

    // Module 7: Exit Reports
    Route::resource('exit-reports', ExitReportController::class);

    // Employee Rules PDF
    Route::get('/employee-rules', [EmployeeRulesController::class, 'index'])->name('employee-rules.index');
    Route::post('/employee-rules', [EmployeeRulesController::class, 'store'])->name('employee-rules.store');
    Route::get('/employee-rules/{employeeRule}/download', [EmployeeRulesController::class, 'download'])->name('employee-rules.download');
    Route::post('/employee-rules/{employeeRule}/notify', [EmployeeRulesController::class, 'notify'])->name('employee-rules.notify');
    Route::delete('/employee-rules/{employeeRule}', [EmployeeRulesController::class, 'destroy'])->name('employee-rules.destroy');

    // Pension Records
    Route::resource('pension', PensionController::class);

    // Activity Log
    Route::get('/activity-log', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');

    // User Accounts — approve registrations, change passwords
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/suspend', [UserManagementController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/reactivate', [UserManagementController::class, 'reactivate'])->name('users.reactivate');
    Route::get('/users/own-password', [UserManagementController::class, 'editOwnPassword'])->name('users.own-password');
    Route::post('/users/own-password', [UserManagementController::class, 'updateOwnPassword'])->name('users.update-own-password');
    Route::get('/users/{user}/password', [UserManagementController::class, 'editPassword'])->name('users.edit-password');
    Route::post('/users/{user}/password', [UserManagementController::class, 'updatePassword'])->name('users.update-password');

    // CV Applications
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/reprocess', [ApplicationController::class, 'reprocess'])->name('applications.reprocess');
    Route::get('/applications/{application}/cv', [ApplicationController::class, 'downloadCV'])->name('applications.cv.download');
    Route::delete('/applications/{application}', [ApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::post('/applications/bulk-action', [ApplicationController::class, 'bulkAction'])->name('applications.bulk-action');
    Route::get('/applications/export/qualified', [ApplicationController::class, 'exportQualified'])->name('applications.export-qualified');
});

// Redirect after login
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin() || auth()->user()->isHRManager()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('my.dashboard');
})->middleware(['auth'])->name('dashboard');

// Health check
Route::get('/health', function () {
    return response()->json([
        'status'    => 'healthy',
        'timestamp' => now(),
        'database'  => DB::connection()->getPdo() ? 'connected' : 'disconnected',
    ]);
});