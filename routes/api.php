<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\KurtiController;
use App\Http\Controllers\UserDeviceController;
use App\Http\Controllers\Api\WeeklyReportController;


Route::post('login', [AuthController::class, 'login']);
Route::get('/', [AuthController::class, 'test']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::post('/kurtis', [KurtiController::class, 'store']);
    Route::get('/kurtis/{muridId}/{groupId}', [KurtiController::class, 'show']);
    Route::put('/kurtis/{id}/catatan', [KurtiController::class, 'updateCatatan']);
    Route::post('/save-fcm-token', [UserDeviceController::class, 'store']);
    Route::get('/weekly-reports', [WeeklyReportController::class, 'index']);
    Route::get('/weekly-reports/students', [WeeklyReportController::class, 'students']);
    Route::post('/weekly-reports', [WeeklyReportController::class, 'store']);
    Route::get('/weekly-reports/{weeklyReport}', [WeeklyReportController::class, 'show']);
    Route::put('/weekly-reports/{weeklyReport}/feedback', [WeeklyReportController::class, 'feedback']);
});
