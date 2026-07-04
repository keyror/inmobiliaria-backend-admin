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
            'company_setting.has_custom_smtp' => 'sometimes|boolean',
            'company_setting.smtp_host' => 'sometimes|nullable|string|max:255',
            'company_setting.smtp_port' => 'sometimes|nullable|integer|min:1|max:65535',
            'company_setting.smtp_encryption' => ['sometimes', 'nullable', Rule::in(['tls', 'ssl'])],
            'company_setting.smtp_username' => 'sometimes|nullable|string|max:255',
            'company_setting.smtp_password' => 'sometimes|nullable|string|max:255',
            'company_setting.smtp_from_email' => 'sometimes|nullable|email|max:255',
        ];
    }
}
