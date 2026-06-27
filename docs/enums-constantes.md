# Enums y Constantes — Backend

## Lookups (valores dinámicos de BD)

Los valores de tipo "estado", "tipo", "categoría" se almacenan en la tabla `lookups` y se referencian por UUID.

- Relaciones: `belongsTo(Lookup::class, 'status_id')`
- Validación: `'status_id' => 'required|uuid|exists:lookups,id'`
- Para listar opciones del frontend: `LookupService` / `LookupController`

## Helpers de soporte (`app/Support/`)

| Clase | Uso |
|---|---|
| `CacheKeys` | Claves de caché para `Cache::remember()` |
| `CalculateDV` | Cálculo de dígito de verificación NIT |
| `RealstateSiteTemplates` | Plantillas disponibles para el sitio público |

```php
// Uso de CacheKeys
$key = CacheKeys::forCompany($tenantId);
$data = Cache::remember($key, now()->addHour(), fn() => ...);
```

## Exportaciones (`app/Exports/`)

```
app/Exports/
├── excel/    # Clases que extienden Maatwebsite\Excel\Concerns\*
└── pdf/      # Clases que usan barryvdh/laravel-dompdf
```

## Enums PHP

El proyecto no usa PHP Enums nativos extensamente; los "enums" son en general Lookups en BD o constantes en clases de soporte. Si necesitas agregar un enum PHP:

```php
// app/Enums/PropertyStatusEnum.php (si se crea)
enum PropertyStatusEnum: string
{
    case AVAILABLE = 'available';
    case SOLD = 'sold';
    case RENTED = 'rented';
}
```

Verificar primero si ya existe como Lookup en la BD antes de crear un Enum PHP.
