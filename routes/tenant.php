<?php

declare(strict_types=1);

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FiscalProfileController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\Public\PublicCompanyController;
use App\Http\Controllers\Public\PublicPropertyController;
use App\Http\Controllers\Public\PublicRealstateSiteController;
use App\Http\Controllers\RealstateTemplateManagementController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::name('api.')->prefix('api')->middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::post('auth/login', [AuthenticationController::class, 'login'])->middleware('throttle:login');
    Route::post('auth/send-reset-email', [AuthenticationController::class, 'sendResetEmail'])->middleware('throttle:password-reset');
    Route::post('auth/reset-password', [AuthenticationController::class, 'resetPassword'])->middleware('throttle:password-reset');

    Route::get('public/company', [PublicCompanyController::class, 'show'])->middleware('throttle:lookups')->name('public.company.show');
    Route::get('public/realstate/site', [PublicRealstateSiteController::class, 'show'])->middleware('throttle:lookups')->name('public.realstate.site.show');
    Route::post('public/realstate/site/contact', [PublicRealstateSiteController::class, 'sendContact'])->middleware('throttle:public-company-contact')->name('public.realstate.site.contact');
    Route::get('public/properties', [PublicPropertyController::class, 'index'])->middleware('throttle:public-properties')->name('public.properties.index');
    Route::get('public/properties/{property}', [PublicPropertyController::class, 'show'])->middleware('throttle:public-property-show')->name('public.properties.show');
    Route::post('public/properties/{property}/contact', [PublicPropertyController::class, 'sendContact'])->middleware('throttle:public-property-contact')->name('public.properties.contact');

    // Desplegables
    Route::prefix('lookups')->middleware('throttle:lookups')->name('lookups.')->group(function () {
        Route::post('/', [LookupController::class, 'index'])->name('index');
        Route::get('/co', [LookupController::class, 'getColombiaWithDepartmentsAndCities'])->name('co');
    });

    Route::middleware(['jwt', 'throttle:authenticated-api'])->group(function () {

        Route::post('auth/logout', [AuthenticationController::class, 'logout']);
        Route::post('auth/refresh', [AuthenticationController::class, 'refresh']);
        Route::get('auth/me', [AuthenticationController::class, 'me']);

        Route::prefix('lookups')->middleware('throttle:lookups')->name('lookups.manage.')->group(function () {
            Route::get('/', [LookupController::class, 'manage'])->name('index');
            Route::get('categories', [LookupController::class, 'categories'])->name('categories');
            Route::post('manage', [LookupController::class, 'store'])->name('store');
            Route::get('{lookup}', [LookupController::class, 'show'])->name('show');
            Route::put('{lookup}', [LookupController::class, 'update'])->name('update');
            Route::delete('{lookup}', [LookupController::class, 'destroy'])->name('destroy');
        });

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

        // Gestión de empresa
        Route::prefix('companies')->name('companies')->group(function () {
            Route::get('current', [CompanyController::class, 'show'])->name('current');
            Route::post('/', [CompanyController::class, 'store'])->name('store');
            Route::put('/', [CompanyController::class, 'update'])->name('update');
        });

        Route::prefix('admin/realstate')->name('admin.realstate.')->group(function () {
            Route::get('site-template', [RealstateTemplateManagementController::class, 'showTemplate'])->name('site-template.show');
            Route::put('site-template', [RealstateTemplateManagementController::class, 'updateTemplate'])->name('site-template.update');
            Route::get('site-pages', [RealstateTemplateManagementController::class, 'pages'])->name('site-pages.index');
            Route::put('site-pages/{page}', [RealstateTemplateManagementController::class, 'updatePage'])->name('site-pages.update');
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

        Route::prefix('properties')->name('properties')->group(function () {
            Route::get('/', [PropertyController::class, 'index'])->name('index');
            Route::get('{property}', [PropertyController::class, 'show'])->name('show');
            Route::post('/', [PropertyController::class, 'store'])->name('store');
            Route::put('{property}', [PropertyController::class, 'update'])->name('update');
            Route::delete('{property}', [PropertyController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('images')->middleware('throttle:image-uploads')->group(function () {
            Route::post('/', [ImageController::class, 'upload']);
            Route::delete('/{id}', [ImageController::class, 'delete']);
            Route::patch('/{id}/cover', [ImageController::class, 'setCover']);
        });
    });
});
