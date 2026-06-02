<?php

use App\Http\Controllers\Api\V1\AttendanceApiController;
use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\LeaveApiController;
use App\Http\Controllers\Api\V1\MeApiController;
use App\Http\Controllers\Api\V1\NotificationApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [MeApiController::class, 'show']);
        Route::get('/attendance', [AttendanceApiController::class, 'index']);
        Route::get('/leave-balances', [LeaveApiController::class, 'balances']);
        Route::post('/leave-requests', [LeaveApiController::class, 'store']);
        Route::get('/notifications', [NotificationApiController::class, 'index']);
    });
});
