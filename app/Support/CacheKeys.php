<?php

namespace App\Support;

class CacheKeys
{
    public static function publicCompany(): string
    {
        return self::tenantKey('public_company');
    }

    /**
     * @param  array<int, string>  $categories
     */
    public static function lookupsByCategories(array $categories): string
    {
        $normalizedCategories = collect($categories)
            ->map(fn (string $category): string => trim($category))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->implode('|');

        return self::tenantKey('lookups_categories_'.sha1($normalizedCategories));
    }

    public static function colombiaLookups(): string
    {
        return self::tenantKey('lookups_colombia_departments_cities');
    }

    private static function tenantKey(string $key): string
    {
        return (tenant()?->getTenantKey() ?? 'central').':'.$key;
    }
}
