<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Person extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'full_name',
        'company_name',
        'document_type_id',
        'document_number',
        'document_from',
        'organization_type_id',
        'birth_date',
        'gender',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'document_type_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function organizationType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'organization_type_id');
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
