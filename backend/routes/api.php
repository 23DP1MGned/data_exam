<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::apiResource('users', UserController::class)->middleware('role:admin');

    Route::apiResource('groups', GroupController::class);
    Route::patch('/sessions/{session}/status', [SessionController::class, 'updateStatus'])->middleware('role:admin,coach');
    Route::apiResource('sessions', SessionController::class);
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::post('/attendance', [AttendanceController::class, 'store'])->middleware('role:admin,coach');
    Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->middleware('role:admin,coach');

    Route::get('/payments', [PaymentController::class, 'index']);
    Route::post('/payments', [PaymentController::class, 'store'])->middleware('role:admin,parent');
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::post('/payments/{payment}/refund', [PaymentController::class, 'refund'])->middleware('role:admin');

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{notification}', [NotificationController::class, 'update']);
});
