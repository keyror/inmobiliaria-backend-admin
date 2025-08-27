<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\DatabaseConfig;


class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'domain',
        'plan',
        'status',
        'subscription_ends_at',
        'data',
        'tenancy_db_name'
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subscription_ends_at' => 'datetime',
        'data' => 'array'
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'domain',
            'plan',
            'status',
            'subscription_ends_at',
            'data'
        ];
    }
}
