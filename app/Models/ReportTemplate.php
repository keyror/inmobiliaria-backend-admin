<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportTemplate extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
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
