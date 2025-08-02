<?php

namespace App\Providers;

use App\filter\FiltersApiQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use ReflectionException;

class FiltersApiServiceProvider extends ServiceProvider
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
     * @throws ReflectionException
     */
    public function boot(): void
    {
        Builder::mixin(new FiltersApiQueryBuilder());
    }
}
