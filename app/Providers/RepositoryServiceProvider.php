<?php

namespace App\Providers;

use App\Repositories\Implements\UserRepostiroy;
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
        $this->app->bind(IUserRepository::class, UserRepostiroy::class);
    }
}
