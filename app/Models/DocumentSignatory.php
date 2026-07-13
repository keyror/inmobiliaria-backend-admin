<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSignatory extends Model
{
    use HasUuids;

    protected $fillable = [
        'document_id',
        'person_id',
        'name',
        'email',
        'role',
        'order',
        'token',
        'token_expires_at',
        'status',
        'viewed_at',
        'signed_at',
        'signature_type',
        'signature_path',
        'ip_address',
        'user_agent',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'token_expires_at' => 'datetime',
            'viewed_at' => 'datetime',
            'signed_at' => 'datetime',
            'order' => 'integer',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
