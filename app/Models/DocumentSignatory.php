<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DocumentSignatory extends Model
{
    use HasUuids, LogsActivity;

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
        'consent_accepted_at',
        'consent_text',
        'document_hash_at_signing',
        'view_ip_address',
        'view_user_agent',
        'sent_at',
        'document_read_at',
    ];

    protected function casts(): array
    {
        return [
            'token_expires_at' => 'datetime',
            'viewed_at' => 'datetime',
            'signed_at' => 'datetime',
            'consent_accepted_at' => 'datetime',
            'sent_at' => 'datetime',
            'document_read_at' => 'datetime',
            'order' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'viewed_at', 'signed_at', 'consent_accepted_at', 'rejection_reason', 'sent_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('document_signatories');
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
