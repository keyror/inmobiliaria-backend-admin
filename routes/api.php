<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () use ($domain) {

        Route::post('auth/login', [AuthenticationController::class, 'login'])->name('auth.loing'.$domain);
        Route::post('auth/send-reset-email', [AuthenticationController::class, 'sendResetEmail']);
        Route::post('auth/reset-password', [AuthenticationController::class, 'resetPassword']);

        Route::middleware(['jwt'])->group(function () use ($domain) {
            Route::post('auth/logout', [AuthenticationController::class, 'logout']);
            Route::post('auth/refresh', [AuthenticationController::class, 'refresh']);
            Route::get('auth/me', [AuthenticationController::class, 'me']);

            Route::get('users', [UserController::class, 'index']);
            Route::get('users/export/excel', [UserController::class, 'exportExcel']);
            Route::get('users/export/pdf', [UserController::class, 'exportPdf']);

            // Gestión de roles
            Route::prefix('roles')->name($domain.'roles.')->group(function () {
                Route::get('/', [RoleController::class, 'index'])->name('index');
                Route::post('/', [RoleController::class, 'store'])->name('store');
                Route::put('{role}', [RoleController::class, 'update'])->name('update');
                Route::delete('{role}', [RoleController::class, 'destroy'])->name('destroy');

                // Asignar permisos a rol
                Route::post('assign-permissions/{role}', [RoleController::class, 'assignPermissions'])->name('assign-permissions');
            });

            // Gestión de permisos
            Route::prefix('permissions')->name($domain.'permissions.')->group(function () {
                Route::get('/', [PermissionController::class, 'index'])->name('index');
                Route::post('/', [PermissionController::class, 'store'])->name('store');
                Route::put('{permission}', [PermissionController::class, 'update'])->name('update');
                Route::delete('{permission}', [PermissionController::class, 'destroy'])->name('destroy');
            });

            // Gestión de tenants
            Route::prefix('tenants')->name($domain.'tenants.')->group(function () {

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
    });
}


