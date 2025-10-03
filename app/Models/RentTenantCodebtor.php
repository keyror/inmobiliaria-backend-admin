<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentTenantCodebtor extends Model
{
    use HasUuids;

    protected $table = 'rent_tenant_codebtor';

    protected $fillable = [
        'rent_id',
        'tenant_id',
        'codebtor_id'
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
