<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\UserController;

Route::name('api.')->prefix('api')->middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::post('auth/login', [AuthenticationController::class, 'login']);
    Route::post('auth/send-reset-email', [AuthenticationController::class, 'sendResetEmail']);
    Route::post('auth/reset-password', [AuthenticationController::class, 'resetPassword']);

    Route::middleware(['jwt'])->group(function () {

        Route::post('auth/logout', [AuthenticationController::class, 'logout']);
        Route::post('auth/refresh', [AuthenticationController::class, 'refresh']);
        Route::get('auth/me', [AuthenticationController::class, 'me']);

        Route::get('users', [UserController::class, 'index']);
        Route::get('users/export/excel', [UserController::class, 'exportExcel']);
        Route::get('users/export/pdf', [UserController::class, 'exportPdf']);
    });
});
