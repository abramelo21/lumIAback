<?php

use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AnalysisController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MetaController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\ResumeController;
use Illuminate\Support\Facades\Route;

Route::get('/meta', [MetaController::class, 'index']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'forgot']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'update']);

    Route::get('/resumes', [ResumeController::class, 'index']);
    Route::post('/resumes', [ResumeController::class, 'store']);
    Route::get('/resumes/{resume}', [ResumeController::class, 'show']);
    Route::delete('/resumes/{resume}', [ResumeController::class, 'destroy']);
    Route::get('/resumes/{resume}/status', [ResumeController::class, 'status']);
    Route::post('/resumes/{resume}/retry', [ResumeController::class, 'retry']);

    Route::get('/analyses/{analysis}', [AnalysisController::class, 'show']);
    Route::get('/reports/{analysis}', [AnalysisController::class, 'report']);
    Route::post('/analyses/{analysis}/feedback', [AnalysisController::class, 'feedback']);

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'show']);
        Route::get('/analyses', [AdminDashboardController::class, 'analyses']);
    });
});
