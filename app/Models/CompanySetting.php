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
    ];

    protected function casts(): array
    {
        return [
            'text_case_mode' => TextCaseModeEnum::class,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
