<?php

namespace App\Models;

use App\Enums\TextCaseModeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends Model
{
    use HasUuids;

    protected $fillable = [
        'company_id',
        'text_case_mode',
        'has_custom_smtp',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'smtp_username',
        'smtp_password',
        'smtp_from_email',
    ];

    protected function casts(): array
    {
        return [
            'text_case_mode' => TextCaseModeEnum::class,
            'has_custom_smtp' => 'boolean',
            'smtp_port' => 'integer',
            'smtp_password' => 'encrypted',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
