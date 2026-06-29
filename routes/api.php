<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FiscalProfileController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\Public\PublicCompanyController;
use App\Http\Controllers\Public\PublicPropertyController;
use App\Http\Controllers\Public\PublicRealstateSiteController;
use App\Http\Controllers\RealstateTemplateManagementController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () use ($domain) {

        Route::post('auth/login', [AuthenticationController::class, 'login'])->middleware('throttle:login')->name($domain.'auth.login');
        Route::post('auth/send-reset-email', [AuthenticationController::class, 'sendResetEmail'])->middleware('throttle:password-reset')->name($domain.'auth.reset.email');
        Route::post('auth/reset-password', [AuthenticationController::class, 'resetPassword'])->middleware('throttle:password-reset')->name($domain.'auth.reset.pass');

        Route::get('public/company', [PublicCompanyController::class, 'show'])->middleware('throttle:lookups')->name($domain.'public.company.show');
        Route::get('public/realstate/site', [PublicRealstateSiteController::class, 'show'])->middleware('throttle:lookups')->name($domain.'public.realstate.site.show');
        Route::post('public/realstate/site/contact', [PublicRealstateSiteController::class, 'sendContact'])->middleware('throttle:public-company-contact')->name($domain.'public.realstate.site.contact');
        Route::get('public/properties', [PublicPropertyController::class, 'index'])->middleware('throttle:public-properties')->name($domain.'public.properties.index');
        Route::get('public/properties/{property}', [PublicPropertyController::class, 'show'])->middleware('throttle:public-property-show')->name($domain.'public.properties.show');
        Route::post('public/properties/{property}/contact', [PublicPropertyController::class, 'sendContact'])->middleware('throttle:public-property-contact')->name($domain.'public.properties.contact');
        // Desplegables
        Route::prefix('lookups')->middleware('throttle:lookups')->name($domain.'lookups.')->group(function () {
            Route::post('/', [LookupController::class, 'index'])->name('index');
            Route::get('/co', [LookupController::class, 'getColombiaWithDepartmentsAndCities'])->name('co');
        });

        // Plans select endpoint (accessible without subscription check for tenant form)
        Route::get('plans/select', [PlanController::class, 'select'])->middleware(['auth:api', 'jwt', 'permission:tenants.view'])->name($domain.'plans.select');

        Route::middleware(['auth:api', 'jwt', 'throttle:authenticated-api'])->group(function () use ($domain) {
            Route::post('auth/logout', [AuthenticationController::class, 'logout'])->name($domain.'auth.logout');
            Route::post('auth/refresh', [AuthenticationController::class, 'refresh'])->name($domain.'auth.refresh');
            Route::get('auth/me', [AuthenticationController::class, 'me'])->name($domain.'auth.me');

            Route::middleware('check.subscription')->group(function () use ($domain) {
                Route::get('dashboard', [DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name($domain.'dashboard.index');
                Route::get('search/global', [SearchController::class, 'global'])->name($domain.'search.global');

                Route::prefix('lookups')->middleware('throttle:lookups')->name($domain.'lookups.manage.')->group(function () {
                    Route::get('/', [LookupController::class, 'manage'])->middleware('permission:lookups.view')->name('index');
                    Route::get('categories', [LookupController::class, 'categories'])->middleware('permission:lookups.view')->name('categories');
                    Route::post('manage', [LookupController::class, 'store'])->middleware('permission:lookups.create')->name('store');
                    Route::get('{lookup}', [LookupController::class, 'show'])->middleware('permission:lookups.view')->name('show');
                    Route::put('{lookup}', [LookupController::class, 'update'])->middleware('permission:lookups.edit')->name('update');
                    Route::delete('{lookup}', [LookupController::class, 'destroy'])->middleware('permission:lookups.delete')->name('destroy');
                });

                Route::prefix('users')->name($domain.'users.')->group(function () {
                    Route::get('/', [UserController::class, 'index'])->middleware('permission:users.view')->name('index');
                    Route::get('{user}', [UserController::class, 'show'])->middleware('permission:users.view')->name('show');
                    Route::post('/', [UserController::class, 'store'])->middleware('permission:users.create')->name('store');
                    Route::put('{user}', [UserController::class, 'update'])->middleware('permission:users.edit')->name('update');
                    Route::delete('{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('destroy');
                    Route::get('/export/excel', [UserController::class, 'exportExcel'])->middleware('permission:users.export')->name('excel');
                    Route::get('/export/pdf', [UserController::class, 'exportPdf'])->middleware('permission:users.export')->name('pdf');
                });

                // Gestión de roles
                Route::prefix('roles')->name($domain.'roles.')->group(function () {
                    Route::get('/', [RoleController::class, 'index'])->middleware('permission:roles.view')->name('index');
                    Route::post('/', [RoleController::class, 'store'])->middleware('permission:roles.create')->name('store');
                    Route::put('{role}', [RoleController::class, 'update'])->middleware('permission:roles.edit')->name('update');
                    Route::delete('{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete')->name('destroy');

                    // Asignar permisos a rol
                    Route::post('assign-permissions/{role}', [RoleController::class, 'assignPermissions'])->middleware('permission:roles.assign-permissions')->name('assign-permissions');
                });

                // Gestión de permisos
                Route::prefix('permissions')->name($domain.'permissions.')->group(function () {
                    Route::get('/', [PermissionController::class, 'index'])->middleware('permission:permissions.view')->name('index');
                    Route::post('/', [PermissionController::class, 'store'])->middleware('permission:permissions.create')->name('store');
                    Route::put('{permission}', [PermissionController::class, 'update'])->middleware('permission:permissions.edit')->name('update');
                    Route::delete('{permission}', [PermissionController::class, 'destroy'])->middleware('permission:permissions.delete')->name('destroy');
                });

                // Gestión de empresa
                Route::prefix('companies')->name($domain.'companies.')->group(function () {
                    Route::get('current', [CompanyController::class, 'show'])->middleware('permission:companies.view')->name('current');
                    Route::post('/', [CompanyController::class, 'store'])->middleware('permission:companies.create')->name('store');
                    Route::put('/', [CompanyController::class, 'update'])->middleware('permission:companies.edit')->name('update');
                });

                Route::prefix('admin/realstate')->name($domain.'admin.realstate.')->group(function () {
                    Route::get('site-template', [RealstateTemplateManagementController::class, 'showTemplate'])->middleware('permission:site-settings.theme-view')->name('site-template.show');
                    Route::put('site-template', [RealstateTemplateManagementController::class, 'updateTemplate'])->middleware('permission:site-settings.edit')->name('site-template.update');
                    Route::get('site-pages', [RealstateTemplateManagementController::class, 'pages'])->middleware('permission:site-settings.view')->name('site-pages.index');
                    Route::put('site-pages/{page}', [RealstateTemplateManagementController::class, 'updatePage'])->middleware('permission:site-settings.edit')->name('site-pages.update');
                });

                // Gestión de Perfil Fiscal
                Route::prefix('fiscal-profiles')->name($domain.'fiscal-profiles.')->group(function () {
                    Route::post('/', [FiscalProfileController::class, 'store'])->middleware('permission:fiscal-profiles.create')->name('store');
                    Route::put('{fiscalProfile}', [FiscalProfileController::class, 'update'])->middleware('permission:fiscal-profiles.edit')->name('update');
                    Route::delete('{fiscalProfile}', [FiscalProfileController::class, 'destroy'])->middleware('permission:fiscal-profiles.delete')->name('destroy');
                });

                // Gestión de Personas
                Route::prefix('people')->name($domain.'people.')->group(function () {
                    Route::get('/', [PersonController::class, 'index'])->middleware('permission:people.view')->name('index');
                    Route::get('{person}', [PersonController::class, 'show'])->middleware('permission:people.view')->name('show');
                    Route::post('/', [PersonController::class, 'store'])->middleware('permission:people.create')->name('store');
                    Route::put('{person}', [PersonController::class, 'update'])->middleware('permission:people.edit')->name('update');
                    Route::delete('{person}', [PersonController::class, 'destroy'])->middleware('permission:people.delete')->name('destroy');
                });

                Route::prefix('properties')->name($domain.'properties.')->group(function () {
                    Route::get('/', [PropertyController::class, 'index'])->middleware('permission:properties.view')->name('index');
                    Route::get('{property}', [PropertyController::class, 'show'])->middleware('permission:properties.view')->name('show');
                    Route::post('/', [PropertyController::class, 'store'])->middleware('permission:properties.create')->name('store');
                    Route::put('{property}', [PropertyController::class, 'update'])->middleware('permission:properties.edit')->name('update');
                    Route::delete('{property}', [PropertyController::class, 'destroy'])->middleware('permission:properties.delete')->name('destroy');
                });

                Route::prefix('images')->middleware('throttle:image-uploads')->group(function () {
                    Route::post('/', [ImageController::class, 'upload'])->middleware('permission:images.create');
                    Route::delete('/{id}', [ImageController::class, 'delete'])->middleware('permission:images.delete');
                    Route::patch('/{id}/cover', [ImageController::class, 'setCover'])->middleware('permission:images.edit');
                });

                // Gestión de tenants
                Route::prefix('tenants')->name($domain.'tenants.')->group(function () {

                    Route::get('/', [TenantController::class, 'index'])->middleware('permission:tenants.view')->name('index');
                    Route::post('/', [TenantController::class, 'store'])->middleware('permission:tenants.create')->name('store');
                    Route::get('{tenant}', [TenantController::class, 'show'])->middleware('permission:tenants.view')->name('show');
                    Route::put('{tenant}', [TenantController::class, 'update'])->middleware('permission:tenants.edit')->name('update');
                    Route::delete('{tenant}', [TenantController::class, 'destroy'])->middleware('permission:tenants.delete')->name('destroy');

                    // Acciones específicas de tenants
                    Route::patch('{tenant}/activate', [TenantController::class, 'activate'])->middleware('permission:tenants.activate')->name('activate');
                    Route::patch('{tenant}/deactivate', [TenantController::class, 'deactivate'])->middleware('permission:tenants.deactivate')->name('deactivate');
                });

                // Gestión de planes SaaS
                Route::prefix('plans')->name($domain.'plans.')->group(function () {
                    Route::get('/', [PlanController::class, 'index'])->middleware('permission:plans.view')->name('index');
                    Route::post('/', [PlanController::class, 'store'])->middleware('permission:plans.create')->name('store');
                    Route::get('{plan}', [PlanController::class, 'show'])->middleware('permission:plans.view')->name('show');
                    Route::put('{plan}', [PlanController::class, 'update'])->middleware('permission:plans.edit')->name('update');
                    Route::delete('{plan}', [PlanController::class, 'destroy'])->middleware('permission:plans.delete')->name('destroy');
                });

            }); // end check.subscription
        });
    });
}
