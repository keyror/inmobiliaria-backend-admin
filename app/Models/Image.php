<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use SoftDeletes, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'imageable_id',
        'imageable_type',
        'image_type_id',
        'title',
        'description',
        'file_name',
        'file_path',
        'file_extension',
        'mime_type',
        'file_size',
        'width',
        'height',
        'sort_order',
        'is_cover',
        'is_public'
    ];

    protected $appends = ['url'];

    public function imageable()
    {
        return $this->morphTo();
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::disk($this->disk ?? 'public')->url($this->file_path)
        );
    }
}
