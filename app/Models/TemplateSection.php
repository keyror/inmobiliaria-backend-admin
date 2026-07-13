<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TemplateSection extends Model
{
    /** Tipos válidos de sección */
    public const SECTION_TYPES = [
        'clause',
        'observation',
        'header',
        'party_info',
        'property_info',
        'contract_info',
        'signature',
        'table',
        'separator',
    ];

    protected $fillable = [
        'template_key',
        'section_key',
        'section_type',
        'heading',
        'body',
        'content_json',
        'section_config',
        'sort_order',
        'is_active',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'integer',
            'content_json' => 'array',
            'section_config' => 'array',
        ];
    }

    public function scopeForTemplate(Builder $query, string $templateKey): Builder
    {
        return $query->where('template_key', $templateKey)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
