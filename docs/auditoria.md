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
| `Rent` | `rents` | RentObligation, RentTenantCodebtor, Liability |
| `Document` | `documents` | — |
| `ReportTemplate` | `reports` | — (modelo simple, sin sub-modelos) |
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

| Parámetro | Tipo | Disponible para | Ejemplo |
|---|---|---|---|
| `log_name` | string | todos | `properties` |
| `event` | string | todos | `updated` |
| `causer_email` | string | todos | `admin@test.com` |
| `date_from` | `Y-m-d` | todos | `2026-01-01` |
| `date_to` | `Y-m-d` | todos | `2026-12-31` |
| `company_id` | uuid | solo `companies.view_all` | `uuid-sucursal` |

**Scoping automático por sucursal**: cuando el usuario no tiene `companies.view_all`, `ResolveBranchMiddleware` fija `current_company_id` y el repositorio lo aplica como filtro automático — el usuario solo ve logs de su sucursal sin necesidad de enviar `company_id` explícitamente. El parámetro `company_id` solo tiene efecto para usuarios `view_all` que quieren filtrar una sucursal específica.

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
| Lookup | `status_property_id`, `offer_type_id`, `property_type_id`, `stratum_id`, `garage_type_id`, `feature_type_id`, `area_type_id`, `area_unit_id`, `price_type_id`, `channel_id`, `account_type_id`, `bank_id`, `via_type_id`, `city_id`, `department_id`, `country_id`, `document_type_id`, `organization_type_id`, `gender_type_id`, `obligation_type_id`, `frequency_type_id`, `status_id`, `contract_type_id`, `increment_type_id`, `payment_bank_id`, `liability_type_id`, `status_type_id`, `document_category_id` | tabla `lookups` |
| Person | `person_id`, `legal_representative_id`, `person_attendant_id`, `tenant_id`, `codebtor_id` | `full_name (document_number)` |
| Company | `company_id`, `parent_company_id` | `company_name` |
| Property | `property_id` | `code` |
| User | `user_id`, `created_by` | `email` |
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

---

## Cómo implementar auditoría en un nuevo módulo con pestañas

Cuando un módulo tiene múltiples pestañas (sub-modelos que se guardan juntos), seguir estos pasos. El módulo `Rent` con pestañas Obligaciones / Arrendatarios / Cargos es el ejemplo de referencia.

### 1. Modelo principal — `log_name` propio

```php
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class NuevoModelo extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('nuevo-modulo'); // clave única del módulo
    }
}
```

### 2. Sub-modelos — mismo `log_name` que el padre

Cada sub-modelo (pestaña) debe tener `LogsActivity` con el **mismo `log_name`** que el modelo padre:

```php
class SubModelo extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('nuevo-modulo'); // igual que el padre
    }
}
```

### 3. Servicio — envolver en `LogBatch`

Tanto `create` como `update` deben tener `LogBatch`:

```php
use Spatie\Activitylog\Facades\LogBatch;

public function createNuevoModelo(Request $request): JsonResponse
{
    LogBatch::startBatch();
    DB::beginTransaction();
    try {
        $modelo = $this->repository->create($data);
        $modelo->syncHasMany('subModelos', $data['sub_modelos']);
        DB::commit();
        return response()->json(['status' => true], 201);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
    } finally {
        LogBatch::endBatch(); // SIEMPRE en finally
    }
}
```

### 4. `syncHasMany` — usar eliminación por Eloquent

**Regla crítica**: nunca usar query builder para eliminar sub-modelos si se quiere que el evento `deleted` dispare en Spatie. El patrón correcto en `syncHasMany`:

```php
// ❌ Incorrecto — no dispara eventos Eloquent
$this->$relation()->whereNotIn('id', $incomingIds)->delete();

// ✅ Correcto — carga y elimina individualmente
$this->$relation()->whereNotIn('id', $incomingIds)->get()->each->delete();
```

La misma regla aplica para operaciones manuales de delete en el servicio:

```php
// ❌ Incorrecto
$modelo->subModelos()->delete();

// ✅ Correcto
$modelo->subModelos->each->delete();
```

### 5. `AuditValueResolver` — registrar nuevos campos FK

En `app/Support/AuditValueResolver.php`:

- Si el campo referencia un Lookup: agregar a `LOOKUP_FIELDS`
- Si el campo referencia un modelo (Person, Property, etc.): agregar a `MODEL_FIELDS`

```php
private const LOOKUP_FIELDS = [
    // ... existentes ...
    'nuevo_tipo_id', // FK a Lookup
];

private const MODEL_FIELDS = [
    // ... existentes ...
    'nuevo_person_id' => 'person', // FK a Person
];
```

### 6. Traducción — `backend/lang/es/audit.php`

```php
'modules' => [
    // ...
    'nuevo-modulo' => 'Nombre legible del módulo',
],
```

### 7. Frontend — `audit/all.vue`

**`SUBJECT_LABELS`** — etiquetas de cada clase PHP en los tabs del modal:

```ts
const SUBJECT_LABELS: Record<string, string> = {
  // ...
  NuevoModelo: "Nombre legible",
  SubModelo: "Nombre de la pestaña",
};
```

**`MODULE_OPTIONS`** — para que aparezca en el filtro de módulo:

```ts
{ id: "nuevo-modulo", name: "Nombre legible del módulo", ... }
```

**`FISCAL_PROFILE_CHILDREN`** (o equivalente) — solo si varios sub-modelos deben agruparse en **una sola pestaña** en el modal de detalle. Ver ejemplo de `EconomicActivity` + `TaxeType` → tab "Perfil Fiscal".

### 8. Frontend — `AuditFieldLabels.ts`

Agregar etiquetas para todos los campos del `$fillable` de cada modelo del nuevo módulo:

```ts
// ── Nuevo módulo (NuevoModelo) ────────────────────────────────────────
campo_uno: "Etiqueta del campo uno",
campo_dos: "Etiqueta del campo dos",
```
