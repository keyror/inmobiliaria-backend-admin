# Ejemplo: Crear reglas de validación

## Cuándo crear `XxxRules.php`

- Cuando las reglas son compartidas entre `StoreXxxRequest` y `UpdateXxxRequest`
- Cuando una entidad tiene subobjetos anidados (ej: `property.address.*`)
- Cuando las reglas se reusan en múltiples requests

## Estructura de `XxxRules.php`

```php
<?php

namespace App\Validation;

use Illuminate\Validation\Rule;

class ContractRules
{
    // Para creación: todas las reglas requeridas
    public static function store(): array
    {
        return [
            'contract.property_id' => 'required|uuid|exists:properties,id',
            'contract.type_id' => 'required|uuid|exists:lookups,id',
            'contract.start_date' => 'required|date',
            'contract.amount' => 'required|numeric|min:0',
        ];
    }

    // Para actualización: las reglas llevan 'sometimes|'
    public static function update(string $contractId): array
    {
        return [
            'contract.property_id' => [
                'sometimes', 'required', 'uuid',
                Rule::exists('properties', 'id'),
            ],
            'contract.start_date' => 'sometimes|required|date',
            'contract.amount' => 'sometimes|required|numeric|min:0',
        ];
    }
}
```

## Integrar en FormRequest

```php
// StoreContractRequest.php
public function rules(): array
{
    return array_merge(
        ContractRules::store(),
        AddressRules::store(),   // reglas de entidad relacionada
    );
}

// UpdateContractRequest.php
public function rules(): array
{
    return array_merge(
        ContractRules::update($this->route('contract')->id),
        AddressRules::update($this->route('contract')->address?->id),
    );
}
```

## Reglas de unicidad con exclusión (update)

```php
// Regla unique que ignora el registro actual
'contract.code' => [
    'sometimes', 'nullable', 'string', 'max:255',
    Rule::unique('contracts', 'code')->ignore($contractId),
],
```

## Validación condicional

```php
// Campo requerido solo si otro campo tiene cierto valor
'contract.end_date' => 'required_if:contract.type,fixed|nullable|date',
'contract.renewal_months' => 'required_unless:contract.type,fixed|nullable|integer',
```

## Nomenclatura

Los campos usan notación de punto con el nombre del modelo en singular:
- `property.code` (no `code` suelto)
- `contract.start_date` (no `start_date` suelto)

Esto evita colisiones cuando se hace `array_merge()` de múltiples reglas.
