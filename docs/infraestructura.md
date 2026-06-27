# Infraestructura — Docker

## Stack de servicios

```
Internet → Traefik (reverse proxy HTTPS)
               ├── /storage, /api  → nginx-storage → backend (PHP-FPM :9000)
               ├── /admin          → frontend-admin (:3000)
               └── /              → frontend-public (:3000)
```

## Servicios Docker

| Contenedor | Imagen/Build | Puerto interno | Propósito |
|---|---|---|---|
| `traefik` | traefik:v3.6.4 | 80, 443, 8080 | Reverse proxy + TLS |
| `backend` | `./Dockerfile` | 9000 (PHP-FPM) | Laravel API |
| `nginx-storage` | `./nginx/Dockerfile` | 80 | Sirve `/storage` y `/api` |
| `frontend-admin` | `../frontend/Dockerfile` | 3000 | Panel admin (Nuxt SPA) |
| `frontend-public` | `../frontend-public/Dockerfile` | 3000 | Sitio público (Nuxt SSR) |
| `mysql` | mysql:8.4 | 3306 | Base de datos |
| `redis` | redis:7-alpine | 6379 | Caché |

Red compartida: `inmobiliaria-network`

## Enrutamiento Traefik (por prioridad)

| Prioridad | Regla | Servicio |
|---|---|---|
| 300 | `PathPrefix(/storage)` + dominio | `nginx-storage` |
| 200 | `PathPrefix(/api)` + dominio | `nginx-storage` → backend |
| 150 | `PathPrefix(/admin)` + dominio | `frontend-admin` |
| 10 | dominio raíz | `frontend-public` |

Traefik maneja TLS automáticamente con certificados en `./certs/` y configuración dinámica en `./traefik/dynamic.yml`.

## Variables de entorno del backend (docker-compose)

```
APP_URL, APP_DOMAIN
DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
CORS_ALLOWED_ORIGINS
CACHE_STORE
REDIS_HOST, REDIS_PORT, REDIS_PASSWORD
```

`MYSQL_ROOT_PASSWORD` y `APP_DOMAIN_REGEX` también requeridos en el compose.

## Multi-tenancy y dominios

El sistema soporta **multi-tenant por subdominio**:
- Dominio principal: `${APP_DOMAIN}` → rutas normales
- Subdominios tenant: `${APP_DOMAIN_REGEX}` → mismas rutas, tenant detectado por subdominio

Traefik usa `HostRegexp` para capturar los subdominios de tenant.

## Healthchecks

- `backend`: espera hasta 30 intentos (5min) a que PHP-FPM responda en `:9000`
- `mysql`: `mysqladmin ping` cada 10s, hasta 20 intentos
- `redis`: `redis-cli ping` cada 10s
- `frontend-admin` y `frontend-public` dependen de `backend: service_healthy`

## Volúmenes persistentes

| Volumen | Contenido |
|---|---|
| `mysql_data` | Datos MySQL |
| `redis_data` | Datos Redis |
| `.:/var/www/html` | Código fuente del backend (bind mount) |
| `./storage` | Storage de Laravel (compartido entre backend y nginx-storage) |

## Archivos de entorno para Docker

Los `.env` del docker-compose están en `backend/dev/`:

| Archivo | Uso |
|---|---|
| `dev/.env` | Entorno local de desarrollo |
| `dev/.env.aws` | Entorno AWS (producción/staging) |
| `dev/env.example` | Plantilla con todas las variables requeridas |

Para levantar el stack, el `docker compose` debe apuntar a ese `.env`:

```bash
# Desde backend/
docker compose --env-file dev/.env up -d
```

## Comandos útiles

```bash
# Levantar todo
docker compose up -d

# Ver logs del backend
docker compose logs -f backend

# Ejecutar artisan dentro del contenedor
docker compose exec backend php artisan migrate

# Acceder a tinker
docker compose exec backend php artisan tinker

# Ver dashboard de Traefik
# http://localhost:8080
```
