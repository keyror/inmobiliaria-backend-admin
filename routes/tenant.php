<?php

declare(strict_types=1);

use App\Http\Controllers\FiscalProfileController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoleController;
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

        Route::prefix('users')->name('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('{user}', [UserController::class, 'show'])->name('show');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::put('{user}', [UserController::class, 'update'])->name('update');
            Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::get('/export/excel', [UserController::class, 'exportExcel'])->name('excel');
            Route::get('/export/pdf', [UserController::class, 'exportPdf'])->name('pdf');
        });

        // Gestión de roles
        Route::prefix('roles')->name('roles')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::put('{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('{role}', [RoleController::class, 'destroy'])->name('destroy');

            // Asignar permisos a rol
            Route::post('assign-permissions/{role}', [RoleController::class, 'assignPermissions'])->name('assign-permissions');
        });

        // Gestión de permisos
        Route::prefix('permissions')->name('permissions')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
            Route::post('/', [PermissionController::class, 'store'])->name('store');
            Route::put('{permission}', [PermissionController::class, 'update'])->name('update');
            Route::delete('{permission}', [PermissionController::class, 'destroy'])->name('destroy');
        });

        // Gestión de Perfil Fiscal
        Route::prefix('fiscal-profiles')->name('fiscal-profiles')->group(function () {
            Route::post('/', [FiscalProfileController::class, 'store'])->name('store');
            Route::put('{fiscalProfile}', [FiscalProfileController::class, 'update'])->name('update');
            Route::delete('{fiscalProfile}', [FiscalProfileController::class, 'destroy'])->name('destroy');
        });

        // Gestión de Personas
        Route::prefix('people')->name('people')->group(function () {
            Route::get('/', [PersonController::class, 'index'])->name('index');
            Route::get('{person}', [PersonController::class, 'show'])->name('show');
            Route::post('/', [PersonController::class, 'store'])->name('store');
            Route::put('{person}', [PersonController::class, 'update'])->name('update');
            Route::delete('{person}', [PersonController::class, 'destroy'])->name('destroy');
        });

        // Desplegables
        Route::prefix('lookups')->name('lookups')->group(function () {
            Route::post('/', [LookupController::class, 'index'])->name('index');
            Route::get('/co', [LookupController::class, 'getColombiaWithDepartmentsAndCities'])->name('co');
        });

        Route::prefix('properties')->name('properties')->group(function () {
            Route::get('/', [PropertyController::class, 'index'])->name('index');
            Route::get('{property}', [PropertyController::class, 'show'])->name('show');
            Route::post('/', [PropertyController::class, 'store'])->name('store');
            Route::put('{property}', [PropertyController::class, 'update'])->name('update');
            Route::delete('{property}', [PropertyController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('images')->group(function () {
            Route::post('/', [ImageController::class, 'upload']);
            Route::delete('/{id}', [ImageController::class, 'delete']);
            Route::patch('/{id}/cover', [ImageController::class, 'setCover']);
        });
    });
});
