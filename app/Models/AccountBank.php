<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountBank extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'account_type_id',
        'bank_id',
        'account_number',
        'is_principal',
        'person_id'
    ];

    protected function casts(): array
    {
        return [
            'is_principal' => 'bool',
        ];
    }

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'account_type_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'bank_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

}
