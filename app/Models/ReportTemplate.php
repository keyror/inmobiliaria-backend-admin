<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ReportTemplate extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('reports');
    }

    protected $fillable = [
        'company_id',
        'name',
        'columns',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'columns' => 'array',
            'is_default' => 'boolean',
            'created_at' => 'date:Y-m-d H:i:s',
        ];
    }
}
