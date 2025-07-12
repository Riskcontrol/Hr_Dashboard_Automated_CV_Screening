<?php

use App\Http\Controllers\Api\ApplicationApiController;
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
});