<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;


foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::post('auth/login', [AuthenticationController::class, 'login'])->name('auth.login');
        Route::middleware([
            'jwt'
        ])->group(function () {
            Route::post('auth/logout', [AuthenticationController::class, 'logout']);
            Route::post('auth/refresh', [AuthenticationController::class, 'refresh']);
            Route::get('auth/me', [AuthenticationController::class, 'me']);
        });

        // Gestión de tenants
        Route::prefix('tenants')->name('tenants.')->group(function () {

            Route::get('/', [TenantController::class, 'index'])->name('index');
            Route::post('/', [TenantController::class, 'store'])->name('store');
            Route::get('{tenant}', [TenantController::class, 'show'])->name('show');
            Route::put('{tenant}', [TenantController::class, 'update'])->name('update');
            Route::delete('{tenant}', [TenantController::class, 'destroy'])->name('destroy');

            // Acciones específicas de tenants
            Route::patch('{tenant}/activate', [TenantController::class, 'activate'])->name('activate');
            Route::patch('{tenant}/deactivate', [TenantController::class, 'deactivate'])->name('deactivate');
        });
    });
}


