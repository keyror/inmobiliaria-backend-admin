<?php

namespace App\Providers;

use App\Repositories\Implements\TenantRepository;
use App\Repositories\Implements\UserRepository;
use App\Repositories\ITenantRepository;
use App\Repositories\IUserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(ITenantRepository::class, TenantRepository::class);
    }
}
