<?php

namespace App\Support;

class CacheKeys
{
    public static function publicCompany(): string
    {
        return self::tenantKey('public_company');
    }

    public static function publicRealstateSite(): string
    {
        return self::tenantKey('public_realstate_site');
    }

    /**
     * @param  array<int, string>  $categories
     */
    public static function lookupsByCategories(array $categories, int $version = 1): string
    {
        $normalizedCategories = collect($categories)
            ->map(fn (string $category): string => trim($category))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->implode('|');

        return self::tenantKey("lookups_v{$version}_categories_".sha1($normalizedCategories));
    }

    public static function colombiaLookups(int $version = 1): string
    {
        return self::tenantKey("lookups_v{$version}_colombia_departments_cities");
    }

    public static function lookupCategories(int $version = 1): string
    {
        return self::tenantKey("lookups_v{$version}_categories_list");
    }

    public static function lookupsVersion(): string
    {
        return self::tenantKey('lookups_version');
    }

    private static function tenantKey(string $key): string
    {
        return (tenant()?->getTenantKey() ?? 'central').':'.$key;
    }
}
