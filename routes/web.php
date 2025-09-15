<?php

// use App\Http\Controllers\ProfileController;
// use App\Http\Controllers\Admin\DashboardController;
// use App\Http\Controllers\Admin\KeywordSetController;
// use App\Http\Controllers\Admin\ApplicationController;
// use App\Http\Controllers\ApplicationSubmissionController;
// use Illuminate\Support\Facades\Route;

// /*
// |--------------------------------------------------------------------------
// | Web Routes
// |--------------------------------------------------------------------------
// */

// // Public application routes
// Route::get('/', [ApplicationSubmissionController::class, 'index'])->name('application.form');
// Route::post('/application', [ApplicationSubmissionController::class, 'store'])->name('application.store');
// Route::get('/application/success/{application}', [ApplicationSubmissionController::class, 'success'])->name('application.success');
// Route::get('/application/status/{application}', [ApplicationSubmissionController::class, 'status'])->name('application.status');

// // Authentication routes (provided by Breeze)
// require __DIR__.'/auth.php';

// // Admin routes (protected by auth and admin middleware)
// Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
//     // Dashboard
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
//     // Keyword Sets Management
//     Route::resource('keyword-sets', KeywordSetController::class);
    
//     // Applications Management
//     Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
//     Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
//     Route::post('/applications/{application}/reprocess', [ApplicationController::class, 'reprocess'])->name('applications.reprocess');
//     Route::get('/applications/{application}/download', [ApplicationController::class, 'downloadCV'])->name('applications.download');
//     Route::delete('/applications/{application}', [ApplicationController::class, 'destroy'])->name('applications.destroy');
//     Route::post('/applications/bulk-action', [ApplicationController::class, 'bulkAction'])->name('applications.bulk-action');
//     Route::get('/applications/export/qualified', [ApplicationController::class, 'exportQualified'])->name('applications.export-qualified');
    
// });

// // Redirect authenticated users to appropriate dashboard
// Route::get('/dashboard', function () {
//     if (auth()->user()->isAdmin() || auth()->user()->isHRManager()) {
//         return redirect()->route('admin.dashboard');
//     }
//     return redirect()->route('application.form');
// })->middleware(['auth'])->name('dashboard');

// Route::get('/health', function () {
//     return response()->json([
//         'status' => 'healthy',
//         'timestamp' => now(),
//         'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
//         'queue' => Cache::store('redis')->ping() ? 'connected' : 'disconnected'
//     ]);
// });



use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KeywordSetController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\ApplicationSubmissionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public application routes
Route::get('/', [ApplicationSubmissionController::class, 'index'])->name('application.form');
Route::post('/application', [ApplicationSubmissionController::class, 'store'])->name('application.store');
Route::get('/application/success/{application}', [ApplicationSubmissionController::class, 'success'])->name('application.success');
Route::get('/application/status/{application}', [ApplicationSubmissionController::class, 'status'])->name('application.status');

// Authentication routes (provided by Breeze)
require __DIR__.'/auth.php';

// Admin routes (protected by auth and admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Keyword Sets Management
    Route::resource('keyword-sets', KeywordSetController::class);
    
    // Applications Management
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/reprocess', [ApplicationController::class, 'reprocess'])->name('applications.reprocess');
    Route::get('/applications/{application}/cv', [ApplicationController::class, 'downloadCV'])->name('applications.cv.download');
    Route::delete('/applications/{application}', [ApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::post('/applications/bulk-action', [ApplicationController::class, 'bulkAction'])->name('applications.bulk-action');
    Route::get('/applications/export/qualified', [ApplicationController::class, 'exportQualified'])->name('applications.export-qualified');
    
});

// Redirect authenticated users to appropriate dashboard
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin() || auth()->user()->isHRManager()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('application.form');
})->middleware(['auth'])->name('dashboard');

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'queue' => Cache::store('redis')->ping() ? 'connected' : 'disconnected'
    ]);
});


