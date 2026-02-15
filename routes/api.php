<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\FiscalProfileController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () use ($domain) {

        Route::post('auth/login', [AuthenticationController::class, 'login'])->name($domain.'auth.login');
        Route::post('auth/send-reset-email', [AuthenticationController::class, 'sendResetEmail'])->name($domain.'auth.reset.email');
        Route::post('auth/reset-password', [AuthenticationController::class, 'resetPassword'])->name($domain.'auth.reset.pass');

        Route::middleware(['jwt'])->group(function () use ($domain) {
            Route::post('auth/logout', [AuthenticationController::class, 'logout'])->name($domain.'auth.logout');
            Route::post('auth/refresh', [AuthenticationController::class, 'refresh'])->name($domain.'auth.refresh');
            Route::get('auth/me', [AuthenticationController::class, 'me'])->name($domain.'auth.me');

            Route::prefix('users')->name($domain.'users.')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('{user}', [UserController::class, 'show'])->name('show');
                Route::post('/', [UserController::class, 'store'])->name('store');
                Route::put('{user}', [UserController::class, 'update'])->name('update');
                Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
                Route::get('/export/excel', [UserController::class, 'exportExcel'])->name('excel');
                Route::get('/export/pdf', [UserController::class, 'exportPdf'])->name('pdf');
            });

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

            // Gestión de Perfil Fiscal
            Route::prefix('fiscal-profiles')->name($domain.'fiscal-profiles.')->group(function () {
                Route::post('/', [FiscalProfileController::class, 'store'])->name('store');
                Route::put('{fiscalProfile}', [FiscalProfileController::class, 'update'])->name('update');
                Route::delete('{fiscalProfile}', [FiscalProfileController::class, 'destroy'])->name('destroy');
            });

            // Gestión de Personas
            Route::prefix('people')->name($domain.'people.')->group(function () {
                Route::get('/', [PersonController::class, 'index'])->name('index');
                Route::get('{person}', [PersonController::class, 'show'])->name('show');
                Route::post('/', [PersonController::class, 'store'])->name('store');
                Route::put('{person}', [PersonController::class, 'update'])->name('update');
                Route::delete('{person}', [PersonController::class, 'destroy'])->name('destroy');
            });

            // Desplegables
            Route::prefix('lookups')->name($domain.'lookups.')->group(function () {
                Route::post('/', [LookupController::class, 'index'])->name('index');
                Route::get('/co', [LookupController::class, 'getColombiaWithDepartmentsAndCities'])->name('co');
            });

            Route::prefix('properties')->name($domain.'properties.')->group(function () {
                Route::post('/', [PropertyController::class, 'index'])->name('index');
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


