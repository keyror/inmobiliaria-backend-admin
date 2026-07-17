<?php

namespace App\Support;

use App\Models\Rent;

class ReportVariables
{
    /**
     * Catálogo completo de variables disponibles, agrupadas por entidad.
     */
    public static function catalog(): array
    {
        return [
            [
                'key' => 'contrato',
                'label' => 'Contrato',
                'variables' => [
                    ['key' => 'property.code', 'label' => 'Código'],
                    ['key' => 'rent.contract_number', 'label' => 'N° Contrato'],
                    ['key' => 'rent.start_date', 'label' => 'Fecha inicio'],
                    ['key' => 'rent.end_date', 'label' => 'Fecha fin'],
                    ['key' => 'rent.duration', 'label' => 'Duración (meses)'],
                    ['key' => 'rent.canon', 'label' => 'Canon mensual'],
                    ['key' => 'rent.iva', 'label' => 'IVA (%)'],
                    ['key' => 'rent.status', 'label' => 'Estado'],
                    ['key' => 'rent.signed_city', 'label' => 'Ciudad de firma'],
                    ['key' => 'rent.contract_type', 'label' => 'Tipo de contrato'],
                    ['key' => 'rent.destination', 'label' => 'Destino'],
                    ['key' => 'rent.adjustment_date', 'label' => 'Fecha de ajuste'],
                ],
            ],
            [
                'key' => 'propiedad',
                'label' => 'Propiedad',
                'variables' => [
                    ['key' => 'property.title', 'label' => 'Título'],
                    ['key' => 'property.address', 'label' => 'Dirección'],
                    ['key' => 'property.sector', 'label' => 'Sector / Barrio'],
                    ['key' => 'property.city', 'label' => 'Ciudad'],
                    ['key' => 'property.type', 'label' => 'Tipo inmueble'],
                    ['key' => 'property.cadastral', 'label' => 'Matrícula inmobiliaria'],
                ],
            ],
            [
                'key' => 'propietario',
                'label' => 'Propietario',
                'variables' => [
                    ['key' => 'owner.name', 'label' => 'Nombre propietario'],
                    ['key' => 'owner.document', 'label' => 'Cédula / NIT propietario'],
                    ['key' => 'owner.phone', 'label' => 'Teléfono propietario'],
                    ['key' => 'owner.mobile', 'label' => 'Celular propietario'],
                    ['key' => 'owner.email', 'label' => 'Email propietario'],
                ],
            ],
            [
                'key' => 'inquilino',
                'label' => 'Arrendatario',
                'variables' => [
                    ['key' => 'tenant.name', 'label' => 'Nombre arrendatario'],
                    ['key' => 'tenant.document', 'label' => 'Cédula arrendatario'],
                    ['key' => 'tenant.phone', 'label' => 'Teléfono arrendatario'],
                    ['key' => 'tenant.mobile', 'label' => 'Celular arrendatario'],
                    ['key' => 'tenant.email', 'label' => 'Email arrendatario'],
                ],
            ],
        ];
    }

    /**
     * Relaciones que se deben eager-cargar según las keys usadas.
     */
    public static function requiredLoads(array $keys): array
    {
        $loads = ['property:id,code,title,property_type_id,cadastral_number', 'contractType:id,name'];

        $needsOwners = false;
        $needsTenants = false;
        $needsAddresses = false;
        $needsPropertyType = false;

        foreach ($keys as $key) {
            if (str_starts_with($key, 'owner.')) {
                $needsOwners = true;
            }
            if (str_starts_with($key, 'tenant.')) {
                $needsTenants = true;
            }
            if (in_array($key, ['property.address', 'property.sector', 'property.city'])) {
                $needsAddresses = true;
            }
            if ($key === 'property.type') {
                $needsPropertyType = true;
            }
        }

        if ($needsOwners) {
            $loads[] = 'property.owners';
            $loads[] = 'property.owners.contacts';
        }
        if ($needsTenants) {
            $loads[] = 'tenants';
            $loads[] = 'tenants.contacts';
        }
        if ($needsAddresses) {
            $loads[] = 'property.addresses';
        }
        if ($needsPropertyType) {
            $loads[] = 'property.propertyType:id,name';
        }

        return array_unique($loads);
    }

    /**
     * Keys cuyo valor es monetario (pesos colombianos, sin decimales).
     * Usado por el export Excel para aplicar formato numérico correcto.
     */
    public static function monetaryKeys(): array
    {
        return ['rent.canon'];
    }

    /**
     * Resuelve el valor de una variable a partir de un contrato cargado.
     *
     * @param  bool  $raw  true → devuelve valores numéricos crudos (para Excel).
     *                     false → devuelve strings formateados para UI (default).
     */
    public static function resolve(Rent $rent, string $key, bool $raw = false): mixed
    {
        $owner = $rent->relationLoaded('property') && $rent->property?->relationLoaded('owners')
            ? $rent->property->owners->sortByDesc(fn ($o) => $o->pivot->is_principal_owner)->first()
            : null;

        $tenant = $rent->relationLoaded('tenants')
            ? $rent->tenants->first()
            : null;

        $address = $rent->relationLoaded('property') && $rent->property?->relationLoaded('addresses')
            ? $rent->property->addresses->first()
            : null;

        return match ($key) {
            'property.code' => $rent->property?->code,
            'property.title' => $rent->property?->title,
            'property.address' => $address?->address,
            'property.sector' => $address?->sector,
            'property.city' => $address?->city?->name,
            'property.type' => $rent->property?->propertyType?->name,
            'property.cadastral' => $rent->property?->cadastral_number,

            'rent.contract_number' => $rent->contract_number,
            'rent.start_date' => $rent->start_date?->format('Y-m-d'),
            'rent.end_date' => $rent->end_date?->format('Y-m-d'),
            'rent.duration' => $rent->duration,
            'rent.canon' => $rent->canon
                ? ($raw ? (float) $rent->canon : number_format((float) $rent->canon, 0, ',', '.'))
                : null,
            'rent.iva' => $rent->iva,
            'rent.status' => $rent->status,
            'rent.signed_city' => $rent->signed_city,
            'rent.contract_type' => $rent->contractType?->name,
            'rent.destination' => $rent->destination,
            'rent.adjustment_date' => $rent->adjustment_date?->format('Y-m-d'),

            'owner.name' => $owner?->full_name ?? $owner?->company_name,
            'owner.document' => $owner?->document_number,
            'owner.phone' => $owner?->contacts->first()?->phone,
            'owner.mobile' => $owner?->contacts->first()?->mobile,
            'owner.email' => $owner?->contacts->first()?->email,

            'tenant.name' => $tenant?->full_name ?? $tenant?->company_name,
            'tenant.document' => $tenant?->document_number,
            'tenant.phone' => $tenant?->contacts->first()?->phone,
            'tenant.mobile' => $tenant?->contacts->first()?->mobile,
            'tenant.email' => $tenant?->contacts->first()?->email,

            default => null,
        };
    }

    /**
     * Las 17 columnas que replican exactamente el Excel "Contratos 2026".
     */
    public static function defaultColumns(): array
    {
        return [
            ['key' => 'property.code', 'label' => 'code'],
            ['key' => 'owner.name', 'label' => 'Propietario'],
            ['key' => 'owner.document', 'label' => 'PTcédula'],
            ['key' => 'owner.phone', 'label' => 'PTTeléfono'],
            ['key' => 'owner.mobile', 'label' => 'PTcelular'],
            ['key' => 'owner.email', 'label' => 'PTemail'],
            ['key' => 'tenant.name', 'label' => 'Inquilino'],
            ['key' => 'tenant.document', 'label' => 'Cédula'],
            ['key' => 'tenant.phone', 'label' => 'Teléfono'],
            ['key' => 'tenant.mobile', 'label' => 'celular'],
            ['key' => 'tenant.email', 'label' => 'email'],
            ['key' => 'property.address', 'label' => 'Dirección'],
            ['key' => 'property.sector', 'label' => 'sector'],
            ['key' => 'rent.start_date', 'label' => 'inicio'],
            ['key' => 'rent.duration', 'label' => 'Duración'],
            ['key' => 'rent.end_date', 'label' => 'Final'],
            ['key' => 'rent.canon', 'label' => 'canon'],
        ];
    }
}
