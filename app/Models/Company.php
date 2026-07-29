<?php

namespace App\Models;

use App\Models\Concerns\TransformsTextCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use HasUuids, LogsActivity, SoftDeletes, TransformsTextCase;

    protected array $transformTextCase = ['company_name', 'tradename'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('companies');
    }

    protected $fillable = [
        'company_name',
        'tradename',
        'nit',
        'legal_representative_id',
        'person_attendant_id',
        'fiscal_profile_id',
        'parent_company_id',
        'branch_code',
        'is_active',
        'uses_branches',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'uses_branches' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'parent_company_id');
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Company::class, 'parent_company_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot('is_default')
            ->withTimestamps();
    }

    /** @param Builder<Company> $query */
    public function scopeForCompany(Builder $query, string $companyId): Builder
    {
        return $query->where('id', $companyId);
    }

    public function isHeadquarters(): bool
    {
        return $this->parent_company_id === null;
    }

    public function legalRepresentative(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'legal_representative_id');
    }

    public function personAttendant(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_attendant_id');
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function fiscalProfile(): BelongsTo
    {
        return $this->belongsTo(FiscalProfile::class, 'fiscal_profile_id');
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function accountBanks(): MorphMany
    {
        return $this->morphMany(AccountBank::class, 'accountable');
    }

    public function logo(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function publishChannels(): HasMany
    {
        return $this->hasMany(PublishChannel::class, 'company_id');
    }

    public function setting(): HasOne
    {
        return $this->hasOne(CompanySetting::class);
    }

    public function syncHasMany(
        string $relation,
        array $items,
        string $foreignKey = 'company_id'
    ): void {
        $ids = collect($items)->pluck('id')->filter();

        $this->{$relation}()
            ->whereNotIn('id', $ids)
            ->delete();

        foreach ($items as $item) {
            $item[$foreignKey] = $this->id;
            $id = $item['id'] ?? null;

            if ($id) {
                $existing = $this->{$relation}()->getRelated()->withTrashed()->find($id);
                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }
                    $existing->fill($item)->save();

                    continue;
                }
            }

            $this->{$relation}()->create($item);
        }
    }
}
