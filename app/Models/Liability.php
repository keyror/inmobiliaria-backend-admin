<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Liability extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'rent_id',
        'liability_type_id',
        'fee',
    ];

    protected function casts(): array
    {
        return [
            'fee' => 'decimal:2',
        ];
    }

    public function rent(): BelongsTo
    {
        return $this->belongsTo(Rent::class);
    }

    public function liabilityType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'liability_type_id');
    }
}
