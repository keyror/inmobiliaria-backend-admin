<?php

namespace App\Providers;

use App\Repositories\IFiscalProfileRepository;
use App\Repositories\Implements\FiscalProfileRepository;
use App\Repositories\Implements\PersonRepository;
use App\Repositories\Implements\UserRepository;
use App\Repositories\IPersonRepository;
use App\Repositories\IUserRepository;
use App\Services\IAuthenticationService;
use App\Services\IFiscalProfileService;
use App\Services\Implements\AuthenticationService;
use App\Services\Implements\FiscalProfileService;
use App\Services\Implements\PermissionService;
use App\Services\Implements\PersonService;
use App\Services\Implements\RoleService;
use App\Services\Implements\TenantService;
use App\Services\Implements\UserService;
use App\Services\IPermissionService;
use App\Services\IPersonService;
use App\Services\IRoleService;
use App\Services\ITenantService;
use App\Services\IUserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(IAuthenticationService::class, AuthenticationService::class);
        $this->app->bind(IUserService::class, UserService::class);
        $this->app->bind(ITenantService::class, TenantService::class);
        $this->app->bind(IRoleService::class, RoleService::class);
        $this->app->bind(IPermissionService::class, PermissionService::class);
        $this->app->bind(IPersonService::class, PersonService::class);
        $this->app->bind(IFiscalProfileService::class, FiscalProfileService::class);
    }
}
