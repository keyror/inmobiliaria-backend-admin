<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\Central\CompanyController as CentralCompanyController;
use App\Http\Controllers\Central\DashboardController as CentralDashboardController;
use App\Http\Controllers\Central\PersonController as CentralPersonController;
use App\Http\Controllers\Central\PlanController as CentralPlanController;
use App\Http\Controllers\Central\SiteManagementController as CentralSiteManagementController;
use App\Http\Controllers\Central\TenantController as CentralTenantController;
use App\Http\Controllers\Central\TenantUserController as CentralTenantUserController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Public\PublicCompanyController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () use ($domain) {

        // Info empresa + estructura de site para el frontend público central
        Route::get('public/central/site', [PublicCompanyController::class, 'showCentralSite'])->middleware('throttle:lookups')->name($domain.'public.central.site');

        // Formulario de contacto del sitio central SaaS
        Route::post('public/central/contact', [PublicCompanyController::class, 'sendCentralContact'])->middleware('throttle:public-company-contact')->name($domain.'public.central.contact');

        // Info empresa básica para el admin frontend en dominio central
        Route::get('public/company', [PublicCompanyController::class, 'showCentral'])->middleware('throttle:lookups')->name($domain.'public.company.show');

        Route::post('auth/login', [AuthenticationController::class, 'login'])->middleware('throttle:login')->name($domain.'auth.login');
        Route::post('auth/send-reset-email', [AuthenticationController::class, 'sendResetEmail'])->middleware('throttle:password-reset')->name($domain.'auth.reset.email');
        Route::post('auth/reset-password', [AuthenticationController::class, 'resetPassword'])->middleware('throttle:password-reset')->name($domain.'auth.reset.pass');

        // Desplegables
        Route::prefix('lookups')->middleware('throttle:lookups')->name($domain.'lookups.')->group(function () {
            Route::post('/', [LookupController::class, 'index'])->name('index');
            Route::get('/co', [LookupController::class, 'getColombiaWithDepartmentsAndCities'])->name('co');
        });

        // Plans select (for tenant create form — requires auth)
        Route::get('plans/select', [CentralPlanController::class, 'select'])->middleware(['jwt', 'permission:tenants.view'])->name($domain.'plans.select');

        // Public plans — no auth, used by SaaS landing page
        Route::get('public/plans', [CentralPlanController::class, 'publicIndex'])->middleware('throttle:lookups')->name($domain.'public.plans.index');

        Route::post('auth/refresh', [AuthenticationController::class, 'refresh'])->middleware('throttle:token-refresh')->name($domain.'auth.refresh');

        Route::middleware(['jwt', 'throttle:authenticated-api'])->group(function () use ($domain) {
            Route::post('auth/logout', [AuthenticationController::class, 'logout'])->name($domain.'auth.logout');
            Route::get('auth/me', [AuthenticationController::class, 'me'])->name($domain.'auth.me');

            Route::middleware('check.subscription')->group(function () use ($domain) {

                // Dashboard SaaS (métricas de tenants)
                Route::get('dashboard', [CentralDashboardController::class, 'index'])->middleware('permission:dashboard.view')->name($domain.'dashboard.index');

                // Gestión del tema del sitio central SaaS
                Route::get('central/site-theme', [CentralSiteManagementController::class, 'showTheme'])->middleware('permission:site-settings.theme-view')->name($domain.'central.site-theme.show');
                Route::put('central/site-theme', [CentralSiteManagementController::class, 'updateTheme'])->middleware('permission:site-settings.edit')->name($domain.'central.site-theme.update');

                // Personas (gerente y responsable de la empresa SaaS — sin resolve.branch)
                Route::prefix('people')->name($domain.'people.')->group(function () {
                    Route::get('/', [CentralPersonController::class, 'index'])->middleware('permission:people.view')->name('index');
                    Route::get('{person}', [CentralPersonController::class, 'show'])->middleware('permission:people.view')->name('show');
                    Route::post('/', [CentralPersonController::class, 'store'])->middleware('permission:people.create')->name('store');
                    Route::put('{person}', [CentralPersonController::class, 'update'])->middleware('permission:people.edit')->name('update');
                    Route::delete('{person}', [CentralPersonController::class, 'destroy'])->middleware('permission:people.delete')->name('destroy');
                });

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
                    Route::post('assign-permissions/{role}', [RoleController::class, 'assignPermissions'])->middleware('permission:roles.assign-permissions')->name('assign-permissions');
                });

                // Gestión de permisos
                Route::prefix('permissions')->name($domain.'permissions.')->group(function () {
                    Route::get('/', [PermissionController::class, 'index'])->middleware('permission:permissions.view')->name('index');
                    Route::post('/', [PermissionController::class, 'store'])->middleware('permission:permissions.create')->name('store');
                    Route::put('{permission}', [PermissionController::class, 'update'])->middleware('permission:permissions.edit')->name('update');
                    Route::delete('{permission}', [PermissionController::class, 'destroy'])->middleware('permission:permissions.delete')->name('destroy');
                });

                // Perfil de la empresa SaaS
                Route::prefix('companies')->name($domain.'companies.')->group(function () {
                    Route::get('current', [CentralCompanyController::class, 'show'])->middleware('permission:companies.view')->name('current');
                    Route::put('/', [CentralCompanyController::class, 'update'])->middleware('permission:companies.edit')->name('update');
                });

                // Gestión de tenants
                Route::prefix('tenants')->name($domain.'tenants.')->group(function () {
                    Route::get('/', [CentralTenantController::class, 'index'])->middleware('permission:tenants.view')->name('index');
                    Route::post('/', [CentralTenantController::class, 'store'])->middleware('permission:tenants.create')->name('store');
                    Route::get('{tenant}', [CentralTenantController::class, 'show'])->middleware('permission:tenants.view')->name('show');
                    Route::put('{tenant}', [CentralTenantController::class, 'update'])->middleware('permission:tenants.edit')->name('update');
                    Route::delete('{tenant}', [CentralTenantController::class, 'destroy'])->middleware('permission:tenants.delete')->name('destroy');
                    Route::patch('{tenant}/activate', [CentralTenantController::class, 'activate'])->middleware('permission:tenants.activate')->name('activate');
                    Route::patch('{tenant}/deactivate', [CentralTenantController::class, 'deactivate'])->middleware('permission:tenants.deactivate')->name('deactivate');

                    // Usuarios del tenant gestionados desde central
                    Route::prefix('{tenant}/users')->middleware('permission:tenants.users.view')->name('tenant-users.')->group(function () {
                        Route::get('/', [CentralTenantUserController::class, 'index'])->name('index');
                        Route::get('{userId}', [CentralTenantUserController::class, 'show'])->name('show');
                        Route::post('/', [CentralTenantUserController::class, 'store'])->middleware('permission:tenants.users.create')->name('store');
                        Route::put('{userId}', [CentralTenantUserController::class, 'update'])->middleware('permission:tenants.users.edit')->name('update');
                        Route::delete('{userId}', [CentralTenantUserController::class, 'destroy'])->middleware('permission:tenants.users.delete')->name('destroy');
                    });
                    Route::get('{tenant}/roles', [CentralTenantUserController::class, 'roles'])->middleware('permission:tenants.users.view')->name('tenant-roles.index');
                    Route::get('{tenant}/statuses', [CentralTenantUserController::class, 'statuses'])->middleware('permission:tenants.users.view')->name('tenant-statuses.index');
                });

                // Planes SaaS
                Route::prefix('plans')->name($domain.'plans.')->group(function () {
                    Route::get('/', [CentralPlanController::class, 'index'])->middleware('permission:plans.view')->name('index');
                    Route::post('/', [CentralPlanController::class, 'store'])->middleware('permission:plans.create')->name('store');
                    Route::get('{plan}', [CentralPlanController::class, 'show'])->middleware('permission:plans.view')->name('show');
                    Route::put('{plan}', [CentralPlanController::class, 'update'])->middleware('permission:plans.edit')->name('update');
                    Route::delete('{plan}', [CentralPlanController::class, 'destroy'])->middleware('permission:plans.delete')->name('destroy');
                });

            }); // end check.subscription
        });
    });
}
