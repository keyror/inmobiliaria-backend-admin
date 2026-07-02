<?php

namespace App\Models\Concerns;

use App\Enums\TextCaseModeEnum;
use App\Support\TextCaseResolver;
use App\Support\TextCaseTransformer;

trait TransformsTextCase
{
    public static function bootTransformsTextCase(): void
    {
        $apply = function (self $model): void {
            $mode = TextCaseResolver::getMode();

            if ($mode === TextCaseModeEnum::NONE) {
                return;
            }

            foreach ($model->transformTextCase as $field) {
                $raw = $model->attributes[$field] ?? null;

                if (is_string($raw) && $raw !== '') {
                    $model->attributes[$field] = TextCaseTransformer::transform($raw, $mode);
                }
            }
        };

        static::creating($apply);
        static::updating($apply);
    }
}
