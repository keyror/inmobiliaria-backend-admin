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
        'user_id',
        'legal_representative_id',
        'company_name',
        'tradename',
        'nit',
        'phone',
        'email',
        'address',
        'city',
        'department'
    ];

    public function legalRepresentative(): BelongsTo
    {
        return $this->belongsTo(User::class, 'legal_representative_id');
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }
}
