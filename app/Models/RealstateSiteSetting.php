<?php

namespace App\Models;

use App\Support\RealstateSiteTemplates;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RealstateSiteSetting extends Model
{
    use HasUuids;

    protected $fillable = [
        'template_set',
        'theme',
        'pages',
    ];

    protected $attributes = [
        'template_set' => RealstateSiteTemplates::DEFAULT_TEMPLATE_SET,
        'theme' => '{"primary":"#f35d43","secondary":"#f34451","accent":"#f35d43"}',
        'pages' => '{}',
    ];

    protected function casts(): array
    {
        return [
            'theme' => 'array',
            'pages' => 'array',
        ];
    }
}
