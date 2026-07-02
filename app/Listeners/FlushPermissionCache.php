<?php

namespace App\Listeners;

use Spatie\Permission\PermissionRegistrar;

class FlushPermissionCache
{
    public function handle(): void
    {
        $registrar = app(PermissionRegistrar::class);

        $tenantKey = tenant()?->getTenantKey();

        $registrar->cacheKey = $tenantKey
            ? config('permission.cache.key').'.tenant.'.$tenantKey
            : config('permission.cache.key');

        $registrar->forgetCachedPermissions();
    }
}
