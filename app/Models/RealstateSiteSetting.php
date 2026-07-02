<?php

namespace App\Models;

use App\Support\RealstateSiteTemplates;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RealstateSiteSetting extends Model
{
    use HasUuids, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['template_set', 'theme'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('site-settings');
    }

    protected $fillable = [
        'template_set',
        'theme',
        'pages',
        'backup_template_set',
        'backup_theme',
        'backup_pages',
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
            'backup_theme' => 'array',
            'backup_pages' => 'array',
        ];
    }
}
