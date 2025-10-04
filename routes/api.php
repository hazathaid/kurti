<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\KurtiController;
use App\Http\Controllers\UserDeviceController;
use App\Http\Controllers\Api\NotificationController;


Route::post('login', [AuthController::class, 'login']);
Route::get('/', [AuthController::class, 'test']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::post('/kurtis', [KurtiController::class, 'store']);
    Route::get('/kurtis/{muridId}/{groupId}', [KurtiController::class, 'show']);
    Route::put('/kurtis/{id}/catatan', [KurtiController::class, 'updateCatatan']);
    Route::post('/save-fcm-token', [UserDeviceController::class, 'store']);
});
Route::get('/test-push', function () {
    $expoToken = "ExponentPushToken[KWqsWhIeinEkSvvziKTC83]";

    $notif = new \App\Http\Controllers\Api\NotificationController();
    $response = $notif->sendExpoPush($expoToken, "Hello 👋", "You have a new message");

    return $response;
});
