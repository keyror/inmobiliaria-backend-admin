<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthenticationController::class, 'login'])->name('auth.login');
Route::post('auth/send-reset-email', [AuthenticationController::class, 'sendResetEmail']);
Route::post('auth/reset-password', [AuthenticationController::class, 'resetPassword']);
Route::middleware([
    'jwt'
])->group(function () {
    Route::post('auth/logout', [AuthenticationController::class, 'logout']);
    Route::post('auth/refresh', [AuthenticationController::class, 'refresh']);
    Route::post('auth/me', [AuthenticationController::class, 'me']);
});

