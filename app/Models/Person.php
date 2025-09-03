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
        'company_id',
        'first_name',
        'last_name',
        'document_type_id',
        'document_number',
        'person_type',
        'birth_date',
        'gender',
        'phone',
        'mobile',
        'email',
        'address',
        'city',
        'department',
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
}
