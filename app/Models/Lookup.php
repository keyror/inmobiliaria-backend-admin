<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lookup extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'category',
        'name'
    ];

    public function people(): HasMany
    {
        return $this->hasMany(Person::class, 'document_type_id');
    }
}
