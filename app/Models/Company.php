<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasUuids, SoftDeletes;
    protected $fillable = [
        'company_name',
        'tradename',
        'nit',
        'logo_url',
        'legal_representative_id',
        'person_attendant_id',
    ];

    public function legalRepresentative(): BelongsTo
    {
        return $this->belongsTo(User::class, 'legal_representative_id');
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function contacts(): HasMany
    {
        return $this->HasMany(Contact::class);
    }

    public function addresses(): HasMany
    {
        return $this->HasMany(Address::class);
    }
}
