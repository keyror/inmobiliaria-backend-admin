<?php

namespace App\Validation;

use App\Enums\TextCaseModeEnum;
use Illuminate\Validation\Rule;

class CompanySettingRules
{
    public static function rules(): array
    {
        return [
            'company_setting' => 'sometimes|nullable|array',
            'company_setting.text_case_mode' => [
                'sometimes',
                'nullable',
                Rule::enum(TextCaseModeEnum::class),
            ],
        ];
    }
}
