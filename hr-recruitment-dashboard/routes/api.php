<?php

use App\Http\Controllers\Api\ApplicationApiController;
use App\Http\Controllers\CVFileController;
use App\Http\Controllers\CVProcessingCallbackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('api')->group(function () {
    // Public API endpoints
    Route::get('/job-positions', [ApplicationApiController::class, 'getJobPositions']);
    Route::post('/applications', [ApplicationApiController::class, 'submitApplication']);
    Route::get('/applications/{id}/status', [ApplicationApiController::class, 'getApplicationStatus']);
    
    // CV file serving endpoint (for GitHub Actions)
    Route::get('/cv/file/{encodedPath}', [CVFileController::class, 'serveFile']);
    
    // CV processing callback (for GitHub Actions)
    Route::post('/cv/processing/callback', [CVProcessingCallbackController::class, 'handleCallback'])
        ->name('cv.processing.callback');
});