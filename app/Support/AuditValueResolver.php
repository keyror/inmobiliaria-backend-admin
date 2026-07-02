<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Lookup;
use App\Models\Person;
use App\Models\Plan;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Resolves UUID foreign-key values in Spatie activity log properties
 * to their human-readable labels (lookup names, person full names, etc.).
 *
 * Two-layer cache strategy:
 *   1. Static array: per-request (zero overhead after warmup within same request)
 *   2. Laravel Cache: cross-request (avoids DB hits on repeated views of the same data)
 *
 * Usage: call warmup() once per request with all the logs to process,
 * then call resolveProperties() inside AuditResource::toArray().
 */
class AuditValueResolver
{
    /** Fields whose values reference the `lookups` table. */
    private const LOOKUP_FIELDS = [
        'status_property_id',
        'offer_type_id',
        'property_type_id',
        'stratum_id',
        'garage_type_id',
        'parking_type_id',
        'feature_type_id',
        'area_type_id',
        'area_unit_id',
        'price_type_id',
        'channel_id',
        'account_type_id',
        'bank_id',
        'via_type_id',
        'city_id',
        'department_id',
        'country_id',
        'letra1_id',
        'letra2_id',
        'orientation1_id',
        'orientation2_id',
        'document_type_id',
        'document_from_id',
        'organization_type_id',
        'gender_type_id',
        'obligation_type_id',
        'frequency_type_id',
        'status_id',
        'responsible_for_vat_type_id',
    ];

    /** Fields whose values reference a specific model. */
    private const MODEL_FIELDS = [
        'property_id' => 'property',
        'person_id' => 'person',
        'legal_representative_id' => 'person',
        'person_attendant_id' => 'person',
        'company_id' => 'company',
        'user_id' => 'user',
        'plan_id' => 'plan',
    ];

    /** TTLs (seconds) per data type — how long cross-request cache entries live. */
    private const TTL = [
        'lookup' => 21600, // 6 h — lookups change very rarely
        'property' => 21600, // 6 h — property codes are stable
        'user' => 21600, // 6 h — emails rarely change
        'plan' => 21600, // 6 h — plan names are stable
        'person' => 7200,  // 2 h — names/docs change occasionally
        'company' => 7200,  // 2 h
    ];

    /** Per-request in-memory cache: "type:uuid" → display label */
    private static array $cache = [];

    /**
     * Batch-load all FK values needed by the given logs.
     * Hits the persistent cache first; only queries DB for cache misses.
     *
     * @param  array<AuditLog>  $logs
     */
    public static function warmup(array $logs): void
    {
        self::$cache = [];

        $needed = [
            'lookup' => [],
            'person' => [],
            'company' => [],
            'property' => [],
            'user' => [],
            'plan' => [],
        ];

        foreach ($logs as $log) {
            $props = is_array($log->properties)
                ? $log->properties
                : ($log->properties?->toArray() ?? []);

            foreach (['old', 'attributes'] as $section) {
                foreach ((array) ($props[$section] ?? []) as $field => $value) {
                    if (! $value || ! is_string($value)) {
                        continue;
                    }

                    if (in_array($field, self::LOOKUP_FIELDS)) {
                        $needed['lookup'][] = $value;
                    } elseif (isset(self::MODEL_FIELDS[$field])) {
                        $needed[self::MODEL_FIELDS[$field]][] = $value;
                    }
                }
            }
        }

        self::loadLookups(array_unique($needed['lookup']));
        self::loadPersons(array_unique($needed['person']));
        self::loadCompanies(array_unique($needed['company']));
        self::loadSimple('property', array_unique($needed['property']), fn (array $ids) => Property::withTrashed()->whereIn('id', $ids)->pluck('code', 'id'));
        self::loadSimple('user', array_unique($needed['user']), fn (array $ids) => User::whereIn('id', $ids)->pluck('email', 'id'));
        self::loadSimple('plan', array_unique($needed['plan']), fn (array $ids) => Plan::whereIn('id', $ids)->pluck('name', 'id'));
    }

    /**
     * Resolve a single FK field value to its human-readable label.
     */
    public static function resolve(string $field, mixed $value): mixed
    {
        if ($value === null || $value === '' || ! is_string($value)) {
            return $value;
        }

        if (in_array($field, self::LOOKUP_FIELDS)) {
            return self::$cache["lookup:{$value}"] ?? $value;
        }

        if (isset(self::MODEL_FIELDS[$field])) {
            $type = self::MODEL_FIELDS[$field];

            return self::$cache["{$type}:{$value}"] ?? $value;
        }

        return $value;
    }

    /**
     * Resolve all known FK fields inside a Spatie activity log `properties` structure.
     */
    public static function resolveProperties(mixed $properties): mixed
    {
        if (! $properties) {
            return $properties;
        }

        $data = is_array($properties) ? $properties : $properties->toArray();

        foreach (['old', 'attributes'] as $section) {
            if (! isset($data[$section]) || ! is_array($data[$section])) {
                continue;
            }

            foreach ($data[$section] as $field => $value) {
                $resolved = self::resolve((string) $field, $value);
                if ($resolved !== $value) {
                    $data[$section][$field] = $resolved;
                }
            }
        }

        return $data;
    }

    // ── Private loaders ────────────────────────────────────────────────────

    /**
     * Generic loader: checks persistent cache, queries DB only for misses,
     * then populates both the persistent cache and the static per-request cache.
     *
     * @param  \Closure(array<string>): Collection  $fetcher
     */
    private static function loadSimple(string $type, array $ids, \Closure $fetcher): void
    {
        if (empty($ids)) {
            return;
        }

        $missing = [];

        foreach ($ids as $id) {
            $hit = Cache::get("audit:label:{$type}:{$id}");
            if ($hit !== null) {
                self::$cache["{$type}:{$id}"] = $hit;
            } else {
                $missing[] = $id;
            }
        }

        if (! empty($missing)) {
            $fetcher($missing)->each(function ($label, $id) use ($type) {
                Cache::put("audit:label:{$type}:{$id}", $label, self::TTL[$type]);
                self::$cache["{$type}:{$id}"] = $label;
            });
        }
    }

    private static function loadLookups(array $ids): void
    {
        self::loadSimple('lookup', $ids, fn (array $missing) => Lookup::withTrashed()->whereIn('id', $missing)->pluck('name', 'id'));
    }

    private static function loadPersons(array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        $missing = [];

        foreach ($ids as $id) {
            $hit = Cache::get("audit:label:person:{$id}");
            if ($hit !== null) {
                self::$cache["person:{$id}"] = $hit;
            } else {
                $missing[] = $id;
            }
        }

        if (! empty($missing)) {
            Person::withTrashed()
                ->whereIn('id', $missing)
                ->get(['id', 'full_name', 'first_name', 'last_name', 'document_number'])
                ->each(function ($p) {
                    $display = $p->full_name ?? trim("{$p->first_name} {$p->last_name}");
                    if ($p->document_number) {
                        $display .= " ({$p->document_number})";
                    }
                    Cache::put("audit:label:person:{$p->id}", $display, self::TTL['person']);
                    self::$cache["person:{$p->id}"] = $display;
                });
        }
    }

    private static function loadCompanies(array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        $missing = [];

        foreach ($ids as $id) {
            $hit = Cache::get("audit:label:company:{$id}");
            if ($hit !== null) {
                self::$cache["company:{$id}"] = $hit;
            } else {
                $missing[] = $id;
            }
        }

        if (! empty($missing)) {
            Company::withTrashed()
                ->whereIn('id', $missing)
                ->get(['id', 'company_name', 'tradename'])
                ->each(function ($c) {
                    $display = $c->company_name ?? $c->tradename ?? $c->id;
                    Cache::put("audit:label:company:{$c->id}", $display, self::TTL['company']);
                    self::$cache["company:{$c->id}"] = $display;
                });
        }
    }
}
