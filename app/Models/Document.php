<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Document extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('documents');
    }

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'document_type_id',
        'document_category_id',
        'title',
        'description',
        'number',
        'template_key',
        'content',
        'file_name',
        'file_path',
        'file_extension',
        'mime_type',
        'file_size',
        'document_date',
        'expiry_date',
        'generated_at',
        'signed_at',
        'status_id',
        'notes',
        'created_by',
        'parent_document_id',
        'sort_order',
        'is_public',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'document_date' => 'date:Y-m-d',
            'expiry_date' => 'date:Y-m-d',
            'generated_at' => 'datetime',
            'signed_at' => 'datetime',
            'content' => 'array',
            'is_public' => 'boolean',
            'is_verified' => 'boolean',
            'file_size' => 'integer',
            'sort_order' => 'integer',
            'created_at' => 'date:Y-m-d H:i:s',
        ];
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'document_type_id');
    }

    public function documentCategory(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'document_category_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_document_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_document_id');
    }

    public function signatories(): HasMany
    {
        return $this->hasMany(DocumentSignatory::class);
    }
}
