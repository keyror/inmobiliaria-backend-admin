<?php

use App\Providers\AppServiceProvider;
use App\Providers\FiltersApiServiceProvider;
use App\Providers\RepositoryServiceProvider;
use App\Providers\TenancyServiceProvider;

return [
    AppServiceProvider::class,
    FiltersApiServiceProvider::class,
    RepositoryServiceProvider::class,
    TenancyServiceProvider::class,
];
