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
            $scheme = config('app.scheme', 'https');

            return "{$scheme}://{$tenant->domain}";
        }

        return config('app.frontend_url', config('app.url'));
    }
}
