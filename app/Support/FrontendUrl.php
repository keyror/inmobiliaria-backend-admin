<?php

namespace App\Support;

class FrontendUrl
{
    public static function resolve(string $path = ''): string
    {
        $base = self::base();

        return $path ? rtrim($base, '/').'/'.ltrim($path, '/') : $base;
    }

    private static function base(): string
    {
        $tenant = tenant();

        if ($tenant) {
            $scheme = app()->environment('production') ? 'https' : 'http';

            return "{$scheme}://{$tenant->domain}";
        }

        return app()->environment('production')
            ? config('app.url')
            : config('app.frontend_url', config('app.url'));
    }
}
