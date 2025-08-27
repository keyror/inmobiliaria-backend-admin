<?php

namespace App\Providers;

use App\Services\IAuthenticationService;
use App\Services\Implements\AuthenticationService;
use App\Services\Implements\TenantService;
use App\Services\Implements\UserService;
use App\Services\ITenantService;
use App\Services\IUserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(IAuthenticationService::class, AuthenticationService::class);
        $this->app->bind(IUserService::class, UserService::class);
        $this->app->bind(ITenantService::class, TenantService::class);
    }
}
