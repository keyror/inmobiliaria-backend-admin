<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RentTenantCodebtor extends Pivot
{
    use HasUuids;

    protected $table = 'rent_tenant_codebtor';

    protected $fillable = [
        'rent_id',
        'tenant_id',
        'codebtor_id',
        'percentage',
    ];

    public function rent(): BelongsTo
    {
        return $this->belongsTo(Rent::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'tenant_id');
    }

    public function codebtor(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'codebtor_id');
    }
}
