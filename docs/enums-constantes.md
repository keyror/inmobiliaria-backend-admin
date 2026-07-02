# Enums y Constantes — Backend

## Lookups (valores dinámicos de BD)

Los valores de tipo "estado", "tipo", "categoría" se almacenan en la tabla `lookups` y se referencian por UUID. **No crear Enums PHP para estos valores** — ya están en BD.

```php
// Relación en modelo
public function status(): BelongsTo
{
    return $this->belongsTo(Lookup::class, 'status_id');
}

// Validación
'status_id' => 'required|uuid|exists:lookups,id'

// Listar opciones al frontend
// → LookupController / LookupService
```

---

## Helpers de soporte (`app/Support/`)

| Clase | Propósito |
|---|---|
| `AuditValueResolver` | Resuelve UUIDs de FK en logs de auditoría a nombres legibles. Usa caché en dos capas (estático por request + Laravel Cache por TTL). Ver [auditoria.md](./auditoria.md). |
| `CacheKeys` | Centraliza las claves de caché con `Cache::remember()` |
| `CalculateDV` | Calcula el dígito de verificación del NIT colombiano |
| `RealstateSiteTemplates` | Constantes de plantillas disponibles para el sitio público |
| `TextCaseResolver` | Determina la transformación de texto (mayúsculas/minúsculas) para un modelo |
| `TextCaseTransformer` | Aplica la transformación de texto definida por `TextCaseResolver` |

### Uso de `CacheKeys`

```php
$key  = CacheKeys::forCompany($tenantId);
$data = Cache::remember($key, now()->addHour(), fn() => ...);
```

### Uso de `AuditValueResolver`

```php
// 1. Cargar el caché antes de construir resources (evita N+1)
AuditValueResolver::warmup($paginator->items());

// 2. Resolver en el resource
'properties' => AuditValueResolver::resolveProperties($this->properties),
```

---

## Exportaciones (`app/Exports/`)

```
app/Exports/
├── excel/    # Clases que extienden Maatwebsite\Excel\Concerns\*
└── pdf/      # Clases que usan barryvdh/laravel-dompdf
```

---

## Enums PHP

El proyecto **no usa PHP Enums nativos** extensamente — los valores dinámicos son Lookups en BD. Si en algún caso se necesita un enum de dominio fijo (no configurable por el usuario):

```php
// app/Enums/SomeEnum.php
enum SomeEnum: string
{
    case OPTION_A = 'option_a';
    case OPTION_B = 'option_b';
}
```

Verificar primero si el valor ya existe como Lookup antes de crear un Enum PHP.
