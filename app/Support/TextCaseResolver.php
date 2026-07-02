<?php

namespace App\Support;

use App\Enums\TextCaseModeEnum;
use App\Models\CompanySetting;

class TextCaseResolver
{
    private static ?TextCaseModeEnum $cached = null;

    private static bool $loaded = false;

    public static function getMode(): TextCaseModeEnum
    {
        if (! static::$loaded) {
            static::$loaded = true;
            $setting = CompanySetting::query()->oldest()->first();
            static::$cached = $setting?->text_case_mode ?? TextCaseModeEnum::NONE;
        }

        return static::$cached ?? TextCaseModeEnum::NONE;
    }

    public static function reset(): void
    {
        static::$cached = null;
        static::$loaded = false;
    }
}
