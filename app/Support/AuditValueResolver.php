<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Lookup;
use App\Models\Person;
use App\Models\Plan;
use App\Models\Property;
use App\Models\User;

/**
 * Resolves UUID foreign-key values in Spatie activity log properties
 * to their human-readable labels (lookup names, person full names, etc.).
 *
 * Usage: call warmup() once per request with all the logs to process,
 * then call resolveProperties() inside AuditResource::toArray().
 */
class AuditValueResolver
{
    /**
     * Fields whose values reference the `lookups` table.
     */
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

    /**
     * Fields whose values reference a specific model.
     * Maps field_name → resolver key used in the cache.
     */
    private const MODEL_FIELDS = [
        'property_id' => 'property',
        'person_id' => 'person',
        'legal_representative_id' => 'person',
        'person_attendant_id' => 'person',
        'company_id' => 'company',
        'user_id' => 'user',
        'plan_id' => 'plan',
    ];

    /** Per-request in-memory cache: "type:uuid" → display label */
    private static array $cache = [];

    /**
     * Batch-load all FK values found in the given activity-log records
     * to avoid N+1 queries when resolving properties inside AuditResource.
     *
     * @param  array<AuditLog>  $logs
     */
    public static function warmup(array $logs): void
    {
        self::$cache = [];

        $lookupIds = [];
        $personIds = [];
        $companyIds = [];
        $propertyIds = [];
        $userIds = [];
        $planIds = [];

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
                        $lookupIds[] = $value;
                    } elseif (isset(self::MODEL_FIELDS[$field])) {
                        match (self::MODEL_FIELDS[$field]) {
                            'person' => $personIds[] = $value,
                            'company' => $companyIds[] = $value,
                            'property' => $propertyIds[] = $value,
                            'user' => $userIds[] = $value,
                            'plan' => $planIds[] = $value,
                        };
                    }
                }
            }
        }

        if ($lookupIds) {
            Lookup::withTrashed()
                ->whereIn('id', array_unique($lookupIds))
                ->get(['id', 'name'])
                ->each(fn ($l) => self::$cache["lookup:{$l->id}"] = $l->name);
        }

        if ($personIds) {
            Person::withTrashed()
                ->whereIn('id', array_unique($personIds))
                ->get(['id', 'full_name', 'first_name', 'last_name', 'document_number'])
                ->each(function ($p) {
                    $display = $p->full_name ?? trim("{$p->first_name} {$p->last_name}");
                    if ($p->document_number) {
                        $display .= " ({$p->document_number})";
                    }
                    self::$cache["person:{$p->id}"] = $display;
                });
        }

        if ($companyIds) {
            Company::withTrashed()
                ->whereIn('id', array_unique($companyIds))
                ->get(['id', 'company_name', 'tradename'])
                ->each(fn ($c) => self::$cache["company:{$c->id}"] = $c->company_name ?? $c->tradename ?? $c->id);
        }

        if ($propertyIds) {
            Property::withTrashed()
                ->whereIn('id', array_unique($propertyIds))
                ->get(['id', 'code'])
                ->each(fn ($p) => self::$cache["property:{$p->id}"] = $p->code);
        }

        if ($userIds) {
            User::whereIn('id', array_unique($userIds))
                ->get(['id', 'email'])
                ->each(fn ($u) => self::$cache["user:{$u->id}"] = $u->email);
        }

        if ($planIds) {
            Plan::whereIn('id', array_unique($planIds))
                ->get(['id', 'name'])
                ->each(fn ($p) => self::$cache["plan:{$p->id}"] = $p->name);
        }
    }

    /**
     * Resolve a single FK field value to its human-readable label.
     * Returns the original value unchanged if the field is not a known FK
     * or if no matching record was found in the cache.
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
     * Handles both old/attributes diff format and attributes-only format.
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
}
