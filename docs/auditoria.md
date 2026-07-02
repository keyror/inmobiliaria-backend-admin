# Módulo de Auditoría

## Resumen

El sistema registra automáticamente todos los cambios (crear / actualizar / eliminar) sobre los modelos auditados usando **`spatie/laravel-activitylog` v4.12**. Los registros quedan en la tabla `activity_log` y se exponen via API con resolución de FK y caché en dos capas.

---

## Modelos auditados

| Modelo | `log_name` | Sub-modelos del mismo batch |
|---|---|---|
| `Property` | `properties` | PropertyFeature, PropertyPerson, PropertyObligation, PropertyArea, PropertyPrice, PublishChannel |
| `Person` | `people` | Address, Contact, AccountBank (via FiscalProfile: EconomicActivity, TaxeType) |
| `Company` | `companies` | Address, Contact, AccountBank |
| `User` | `users` | — |
| `Role` | `roles` | — |
| `Lookup` | `lookups` | — |
| `Plan` | `plans` | — |
| `RealstateSiteSetting` | `site-settings` | — |
| `Address` | hereda `log_name` del padre | — |
| `Contact` | hereda `log_name` del padre | — |
| `AccountBank` | `people` | — |

### Configuración estándar en cada modelo

```php
public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logFillable()       // registra solo campos en $fillable
        ->logOnlyDirty()      // solo si el valor realmente cambió
        ->dontSubmitEmptyLogs() // descarta si no hubo cambios netos
        ->useLogName('xxx');
}
```

---

## LogBatch — operaciones multi-modelo

Cuando un `save()` de nivel superior dispara varios modelos relacionados (ej: guardar Persona + Direcciones + Contactos), todos los logs deben compartir un `batch_uuid` para que el frontend pueda mostrarlos como una sola operación con tabs.

```php
// PersonService / CompanyService / PropertyService
LogBatch::startBatch();
DB::beginTransaction();
try {
    // ...operaciones que afectan múltiples modelos...
    DB::commit();
} catch (Exception $e) {
    DB::rollBack();
    return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
} finally {
    LogBatch::endBatch(); // SIEMPRE se ejecuta, incluso en excepción
}
```

**Regla**: servicios de modelo único (UserService, RoleService, LookupService, PlanService) **no necesitan** `LogBatch` — generan exactamente un registro.

---

## syncHasMany — patrón para sub-modelos

El método `syncHasMany()` en `Person` y `Company` sincroniza colecciones de sub-modelos correctamente para que Spatie registre `updated` (no `created`) en ítems existentes.

El bug clásico: `updateOrCreate(['id' => null])` nunca hace match contra UUIDs → siempre inserta → siempre genera evento `created`.

**Patrón correcto** (implementado en `Person::syncHasMany()` y `Company::syncHasMany()`):

```php
foreach ($items as $item) {
    $id = $item['id'] ?? null;
    if ($id) {
        $existing = $this->$relation()->getRelated()->withTrashed()->find($id);
        if ($existing) {
            if ($existing->trashed()) $existing->restore();
            $existing->fill($item)->save(); // → evento "updated"
            continue;
        }
    }
    $this->$relation()->create($item); // → evento "created" (nuevo)
}
```

---

## API

```
GET  /audit              → lista paginada (un registro por batch)
GET  /audit/batch/{uuid} → todos los logs de un batch (para el modal)
```

### Filtros disponibles en `GET /audit`

| Parámetro | Tipo | Ejemplo |
|---|---|---|
| `log_name` | string | `properties` |
| `event` | string | `updated` |
| `causer_email` | string | `admin@test.com` |
| `date_from` | `Y-m-d` | `2026-01-01` |
| `date_to` | `Y-m-d` | `2026-12-31` |

### Un registro por batch

`AuditRepository::getAuditLogs()` usa `MIN(id) GROUP BY batch_uuid` para mostrar solo el registro primario de cada operación:

```php
->where(function ($q) {
    $q->whereNull('batch_uuid')
      ->orWhereIn('id', function ($sub) {
          $sub->selectRaw('MIN(id)')
              ->from('activity_log')
              ->whereNotNull('batch_uuid')
              ->groupBy('batch_uuid');
      });
})
```

---

## AuditValueResolver

Ubicación: `app/Support/AuditValueResolver.php`

Resuelve UUIDs de campos FK en `properties.old` y `properties.attributes` a nombres legibles antes de enviar la respuesta, sin N+1 queries.

### Campos resueltos

| Tipo | Campos | Fuente |
|---|---|---|
| Lookup | `status_property_id`, `offer_type_id`, `property_type_id`, `stratum_id`, `garage_type_id`, `feature_type_id`, `area_type_id`, `area_unit_id`, `price_type_id`, `channel_id`, `account_type_id`, `bank_id`, `via_type_id`, `city_id`, `department_id`, `country_id`, `document_type_id`, `organization_type_id`, `gender_type_id`, `obligation_type_id`, `frequency_type_id`, `status_id`, y más | tabla `lookups` |
| Person | `person_id`, `legal_representative_id`, `person_attendant_id` | `full_name (document_number)` |
| Company | `company_id` | `company_name` |
| Property | `property_id` | `code` |
| User | `user_id` | `email` |
| Plan | `plan_id` | `name` |

### Flujo de uso

```php
// En AuditService::index()
$paginator = $this->auditRepository->getAuditLogs();
AuditValueResolver::warmup($paginator->items()); // carga todo en batch
return response()->json(['data' => $paginator->through(fn($l) => new AuditResource($l))]);

// En AuditRepository::getLogsByBatch()
AuditValueResolver::warmup($logs->all());

// En AuditResource::toArray()
'properties' => AuditValueResolver::resolveProperties($this->properties),
```

---

## Estrategia de caché

Dos capas:

### 1. `Cache::rememberForever("audit:batch:{uuid}")` — batch logs

Los logs de un `batch_uuid` son **inmutables**: una vez cerrada la transacción, nunca cambian. Se cachean permanentemente. La primera apertura del modal cuesta ~100ms de DB; las siguientes ~0.5ms del caché.

### 2. `Cache::put("audit:label:{type}:{uuid}", $label, $ttl)` — nombres FK

| Tipo | TTL | Razón |
|---|---|---|
| `lookup` | 6 h | Los lookups cambian muy raramente |
| `property` | 6 h | El código de propiedad es estable |
| `user` | 6 h | El email rara vez cambia |
| `plan` | 6 h | Nombres de planes son estables |
| `person` | 2 h | Nombres/documentos cambian con más frecuencia |
| `company` | 2 h | Ídem |

`warmup()` solo consulta la DB para los IDs que no están en caché. En una segunda petición sobre los mismos datos, el `warmup` es **87% más rápido**.

### Invalidación

- **Batch cache**: nunca se invalida (inmutable por diseño).
- **Label cache**: expira por TTL. Si un nombre cambia en BD, el caché se refresca en máximo 2-6h sin acción manual.

---

## Permisos

| Permiso | Acción |
|---|---|
| `audit.view` | Ver listado y detalle |
| `audit.export` | Exportar (si se implementa) |
