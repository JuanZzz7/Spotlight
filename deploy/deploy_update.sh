#!/usr/bin/env bash
# ==============================================================================
# Script de Actualización Rápida en Producción (Google Cloud VM)
# Uso: sudo bash deploy/deploy_update.sh
# ==============================================================================

set -euo pipefail

APP_DIR="/var/www/visory-shop"
PHP_VERSION="8.3"

cd "$APP_DIR"

echo "⏳ Activando modo mantenimiento..."
php artisan down || true

echo "⬇️ Descargando últimos cambios desde Git..."
git pull origin main

echo "📦 Actualizando paquetes PHP (Composer)..."
COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction

echo "🎨 Compilando assets con Vite..."
npm ci || npm install
npm run build

echo "🗄️ Ejecutando migraciones de base de datos..."
php artisan migrate --force

echo "⚡ Limpiando y reconstruyendo caché de producción..."
php artisan optimize:clear
php artisan optimize

echo "🔐 Ajustando permisos para Nginx (www-data)..."
chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" "$APP_DIR/database"
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" "$APP_DIR/database"

echo "🔄 Reiniciando PHP-FPM y Workers de cola..."
systemctl reload php${PHP_VERSION}-fpm
supervisorctl restart visory-worker:* || true

echo "✅ Desactivando modo mantenimiento..."
php artisan up

echo "🎉 ¡Despliegue completado con éxito!"
