# Autenticación y Permisos — Backend

## Stack de autenticación
- Laravel Sanctum (tokens)
- Spatie Laravel Permission (roles y permisos)
- Guard: `web` (por defecto)

## Middleware en rutas

```php
// Rutas protegidas (requieren token Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {
    // ...
});

// Rutas con permiso específico
Route::middleware(['auth:sanctum', 'permission:manage properties'])->group(function () {
    // ...
});

// Rutas públicas (sin auth)
// app/Http/Controllers/Public/ — sin middleware auth
```

## Patrón de roles y permisos

- Los **usuarios tienen roles**, los **roles tienen permisos**
- Las verificaciones usan `$user->can('permission-name')` — NO `hasRole()`
- Super Admin: definido en `AppServiceProvider` con `Gate::before()`

```php
// Verificar en service/controller
if (!auth()->user()->can('manage properties')) {
    return response()->json(['status' => false, 'message' => 'No autorizado'], 403);
}
```

## Skill para permisos

Para agregar/modificar roles, permisos o middleware de autorización, activar siempre:
`backend/.claude/skills/laravel-permission-development`

## Flujo de login

Ver `app/Services/Implements/AuthenticationService.php` y `app/Http/Controllers/AuthenticationController.php`.

El token Sanctum se retorna al frontend en el login y se incluye en cada request como `Authorization: Bearer {token}`.

## Guards

- El proyecto usa `auth:sanctum` como guard principal para la API
- No hay guard separado para el sitio público — las rutas públicas no llevan middleware de auth
