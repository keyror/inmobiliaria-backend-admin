#!/bin/bash
set -e

echo "============================================"
echo "  Iniciando contenedor backend..."
echo "============================================"

# Recrear storage link (el volumen lo pisa en build time)
echo "→ Recreando storage link..."
rm -rf /var/www/html/public/storage
php artisan storage:link

# Ejecutar migraciones
echo "→ Ejecutando migraciones..."
php artisan migrate --force

#php artisan migrate:refresh --seed

# Cachear config/rutas/vistas para producción
echo "→ Cacheando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "============================================"
echo "  Todo listo. Iniciando Supervisor..."
echo "============================================"

exec supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
