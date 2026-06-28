<?php

namespace App\Support;

class RealstateSiteTemplates
{
    public const string DEFAULT_TEMPLATE_SET = 'template1';

    public const array DEFAULT_THEME = [
        'primary' => '#f35d43',
        'secondary' => '#f34451',
        'accent' => '#f35d43',
    ];

    public const array TEMPLATE_SETS = [
        'template1',
        'template2',
    ];

    public const array PAGES = [
        'home',
        'propertyList',
        'propertyDetail',
        'about',
        'services',
        'contact',
    ];

    public const array EDITABLE_PAGES = [
        'home',
        'propertyList',
        'propertyDetail',
        'about',
        'services',
        'contact',
    ];

    public static function templatesForSet(string $templateSet): array
    {
        $templateSet = self::isValidTemplateSet($templateSet)
            ? $templateSet
            : self::DEFAULT_TEMPLATE_SET;

        return collect(self::PAGES)
            ->mapWithKeys(fn (string $page): array => [$page => $templateSet])
            ->all();
    }

    public static function defaultPages(string $templateSet = self::DEFAULT_TEMPLATE_SET): array
    {
        return collect(self::PAGES)
            ->mapWithKeys(fn (string $page): array => [
                $page => [
                    'is_active' => true,
                    'template' => $templateSet,
                    'content' => self::defaultContentForPage($page),
                ],
            ])
            ->all();
    }

    public static function defaultContentForPage(string $page): array
    {
        return match ($page) {
            'home' => [
                'background_image_url' => null,
                'hero_slides' => [],
                'featured_sections' => [],
            ],
            'propertyList' => [
                'banner_image_url' => null,
                'title' => null,
                'subtitle' => null,
            ],
            'propertyDetail' => [
                'contact_title' => null,
                'contact_description' => null,
                'show_related_properties' => true,
                'related_title' => null,
                'gallery_fallback' => [],
            ],
            'services' => [
                'banner_image_url' => null,
                'hero' => [
                    'title' => null,
                    'description' => null,
                    'image' => null,
                    'button_text' => null,
                    'button_link' => null,
                ],
                'provided_services' => [],
                'property_services' => [],
            ],
            'about' => [
                'banner_image_url' => null,
                'intro' => [
                    'title' => null,
                    'description' => null,
                    'images' => [],
                ],
                'history' => null,
                'mission' => null,
                'vision' => null,
                'why_choose_us' => [],
            ],
            'contact' => [
                'banner_image_url' => null,
                'title' => null,
                'description' => null,
                'image' => null,
            ],
            default => [],
        };
    }

    /**
     * @param  array<string, mixed>|null  $theme
     * @return array{primary: string, secondary: string, accent: string}
     */
    public static function normalizeTheme(?array $theme): array
    {
        $theme = array_replace(self::DEFAULT_THEME, $theme ?? []);

        return [
            'primary' => is_string($theme['primary'] ?? null) ? $theme['primary'] : self::DEFAULT_THEME['primary'],
            'secondary' => is_string($theme['secondary'] ?? null) ? $theme['secondary'] : self::DEFAULT_THEME['secondary'],
            'accent' => is_string($theme['accent'] ?? null) ? $theme['accent'] : self::DEFAULT_THEME['accent'],
        ];
    }

    public static function isValidTemplateSet(string $templateSet): bool
    {
        return in_array($templateSet, self::TEMPLATE_SETS, true);
    }

    public static function isValidPage(string $page): bool
    {
        return in_array($page, self::PAGES, true);
    }

    public static function isEditablePage(string $page): bool
    {
        return in_array($page, self::EDITABLE_PAGES, true);
    }
}
