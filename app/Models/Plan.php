<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasUuids;

    protected $table = 'plans';

    protected $connection = 'mysql';

    protected $fillable = [
        'id',
        'name',
        'description',
        'price',
        'max_users',
        'max_properties',
        'max_images_per_property',
        'is_active',
        'data',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_users' => 'integer',
        'max_properties' => 'integer',
        'max_images_per_property' => 'integer',
        'is_active' => 'boolean',
        'data' => 'array',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
