#!/usr/bin/env bash
# ==============================================================================
# Script de Aprovisionamiento Automatizado para Google Cloud Compute Engine
# Proyecto: Spotlight / Visory Shop (Laravel 12 + Vite + Tailwind v4)
# SO Soportado: Ubuntu 22.04 LTS / 24.04 LTS
# Uso: sudo bash setup_gcp_vm.sh tu-dominio.com https://github.com/usuario/repo.git
# ==============================================================================

set -euo pipefail

DOMAIN="${1:-}"
REPO_URL="${2:-}"
APP_DIR="/var/www/visory-shop"
PHP_VERSION="8.3"

if [[ -z "$DOMAIN" ]]; then
    echo "❌ Error: Debes indicar tu dominio de Hostinger como primer argumento."
    echo "👉 Ejemplo: sudo bash setup_gcp_vm.sh midominio.com https://github.com/tu-usuario/Visory_Shop.git"
    exit 1
fi

echo "🚀 [1/8] Actualizando paquetes del sistema e instalando utilidades base..."
export DEBIAN_FRONTEND=noninteractive
apt-get update -y && apt-get upgrade -y
apt-get install -y software-properties-common curl git unzip ufw acl supervisor sqlite3 certbot python3-certbot-nginx nginx

echo "🐘 [2/8] Instalando PHP ${PHP_VERSION} y extensiones requeridas por Laravel 12..."
add-apt-repository ppa:ondrej/php -y
apt-get update -y
apt-get install -y \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-common \
    php${PHP_VERSION}-sqlite3 \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-pgsql \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-intl

echo "📦 [3/8] Instalando Composer y Node.js 20 LTS..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt-get install -y nodejs
fi

echo "📂 [4/8] Configurando directorio de la aplicación en ${APP_DIR}..."
mkdir -p "$APP_DIR"
if [[ -n "$REPO_URL" ]] && [[ ! -d "$APP_DIR/.git" ]]; then
    git clone "$REPO_URL" "$APP_DIR"
fi

if [[ -f "$APP_DIR/composer.json" ]]; then
    echo "⚙️ [5/8] Instalando dependencias de Laravel y compilando frontend con Vite..."
    cd "$APP_DIR"

    if [[ ! -f ".env" ]]; then
        cp .env.example .env
        sed -i "s|APP_ENV=local|APP_ENV=production|g" .env
        sed -i "s|APP_DEBUG=true|APP_DEBUG=false|g" .env
        sed -i "s|APP_URL=http://localhost|APP_URL=https://${DOMAIN}|g" .env
    fi

    mkdir -p database
    touch database/database.sqlite

    COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction
    php artisan key:generate --force
    php artisan migrate --force
    php artisan storage:link || true

    npm ci || npm install
    npm run build

    php artisan optimize

    chown -R www-data:www-data "$APP_DIR"
    chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" "$APP_DIR/database"
else
    echo "⚠️ Aviso: Aún no se encontró el código en ${APP_DIR}. Sube o clona tu repositorio allí y ejecuta deploy/deploy_update.sh"
fi

echo "🌐 [6/8] Configurando VirtualHost de Nginx para ${DOMAIN}..."
cat > "/etc/nginx/sites-available/visory-shop" <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN} www.${DOMAIN};
    root ${APP_DIR}/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;
    client_max_body_size 32M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php${PHP_VERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -sf /etc/nginx/sites-available/visory-shop /etc/nginx/sites-enabled/visory-shop
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl restart php${PHP_VERSION}-fpm
systemctl restart nginx

echo "🔄 [7/8] Configurando Supervisor para la cola de trabajos (Queue Worker)..."
cat > "/etc/supervisor/conf.d/visory-worker.conf" <<EOF
[program:visory-worker]
process_name=%(program_name)s_%(process_num)02d
command=php ${APP_DIR}/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=${APP_DIR}/storage/logs/worker.log
stopwaitsecs=3600
EOF

supervisorctl reread
supervisorctl update
supervisorctl start all || true

echo "🔒 [8/8] Abriendo puertos HTTP/HTTPS en el firewall local..."
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

echo "=============================================================================="
echo "✅ ¡Servidor listo! Cuando el DNS de Hostinger apunte a la IP de esta VM,"
echo "ejecuta el siguiente comando para activar el certificado SSL gratuito (HTTPS):"
echo ""
echo "   sudo certbot --nginx -d ${DOMAIN} -d www.${DOMAIN}"
echo "=============================================================================="
