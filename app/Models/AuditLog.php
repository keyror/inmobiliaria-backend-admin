<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity;

class AuditLog extends Activity
{
    protected $table = 'activity_log';

    protected static function booted(): void
    {
        static::creating(function (self $activity) {
            if (! $activity->company_id) {
                $activity->company_id = request()->attributes->get('current_company_id');
            }
        });
    }
}
