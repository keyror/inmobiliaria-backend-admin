<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentObligation extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'rent_id',
        'obligation_type_id',
        'amount',
        'total',
        'frequency_type_id',
        'expiration_date',
        'status_id',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'total' => 'decimal:2',
            'expiration_date' => 'date:Y-m-d',
        ];
    }

    public function rent(): BelongsTo
    {
        return $this->belongsTo(Rent::class);
    }

    public function obligationType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'obligation_type_id');
    }

    public function frequencyType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'frequency_type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_id');
    }
}
