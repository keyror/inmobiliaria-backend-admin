<?php

namespace App\Support;

use App\Enums\TextCaseModeEnum;

class TextCaseTransformer
{
    public static function transform(string $value, TextCaseModeEnum $mode): string
    {
        if ($value === '') {
            return $value;
        }

        return match ($mode) {
            TextCaseModeEnum::NONE => $value,
            TextCaseModeEnum::UPPER => mb_strtoupper($value),
            TextCaseModeEnum::LOWER => mb_strtolower($value),
            TextCaseModeEnum::CAPITALIZE => mb_convert_case($value, MB_CASE_TITLE),
            TextCaseModeEnum::SENTENCE => mb_strtoupper(mb_substr($value, 0, 1)).mb_strtolower(mb_substr($value, 1)),
        };
    }
}
