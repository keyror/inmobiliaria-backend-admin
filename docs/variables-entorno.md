# Variables de Entorno — Backend

## Cómo acceder a variables

```php
// NUNCA usar env() en código de aplicación — solo en config/
env('DB_CONNECTION');  // ❌ solo en config/database.php

// Siempre usar config()
config('database.default');  // ✅
```

## Variables principales

| Variable | Uso |
|---|---|
| `APP_URL` | URL base de la aplicación |
| `APP_KEY` | Clave de cifrado Laravel |
| `APP_ENV` | `local` / `production` |
| `DB_CONNECTION` | Driver de base de datos |
| `DB_DATABASE` | Nombre de la base de datos |
| `SANCTUM_STATEFUL_DOMAINS` | Dominios permitidos por Sanctum |
| `SESSION_DOMAIN` | Dominio de la cookie de sesión |

## Multi-tenancy

El proyecto usa tenancy. Las variables de tenant se configuran en la tabla `tenants` y se acceden mediante helpers de la librería de tenancy activa.

Para entender la estructura de tenants, inspeccionar:
- `database/migrations/tenant/`
- `routes/tenant.php`
- `app/Providers/`

## Secrets

Nunca hardcodear secrets en servicios o controllers. Usar siempre:
```php
config('services.some_api.key')  // definido en config/services.php desde .env
```
