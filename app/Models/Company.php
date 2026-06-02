<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasUuids, SoftDeletes;

    public const DEFAULT_THEME = [
        'colors' => [
            'primary' => '#f35d43',
            'secondary' => '#f34451',
        ],
    ];

    protected $fillable = [
        'company_name',
        'tradename',
        'nit',
        'theme',
        'legal_representative_id',
        'person_attendant_id',
        'fiscal_profile_id',
    ];

    protected $attributes = [
        'theme' => '{"colors":{"primary":"#f35d43","secondary":"#f34451"}}',
    ];

    protected function casts(): array
    {
        return [
            'theme' => 'array',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $theme
     * @return array<string, mixed>
     */
    public static function normalizeTheme(?array $theme): array
    {
        $normalizedTheme = array_replace_recursive(self::DEFAULT_THEME, $theme ?? []);

        if (! is_array($normalizedTheme['colors'] ?? null)) {
            $normalizedTheme['colors'] = self::DEFAULT_THEME['colors'];
        }

        return $normalizedTheme;
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

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function logo(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
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

            $this->{$relation}()->updateOrCreate(
                ['id' => $item['id'] ?? null],
                $item
            );
        }
    }
}
