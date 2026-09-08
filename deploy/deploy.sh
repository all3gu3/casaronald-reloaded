#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# deploy/deploy.sh — Instala (primera vez) o actualiza Casa Ronald en el servidor.
#
#   sudo bash /var/www/casaronald/deploy/deploy.sh [APP_HOST]
#
# Primera ejecución (no existe .env): crea el .env de producción con secretos
# generados, siembra catálogos y cuenta maestra, y muestra la contraseña maestra.
# Ejecuciones siguientes: modo mantenimiento → git pull → composer → migraciones
# → cachés → recarga de PHP-FPM → fin del mantenimiento.
# ---------------------------------------------------------------------------
set -Eeuo pipefail

APP_USER=casaronald
APP_DIR=/var/www/casaronald
BRANCH=main

log() { printf '\n\033[1;34m==> %s\033[0m\n' "$*"; }
as_app() { sudo -u "$APP_USER" -H "$@"; }
imds() {
    local token
    token=$(curl -sf -X PUT http://169.254.169.254/latest/api/token -H 'X-aws-ec2-metadata-token-ttl-seconds: 60' 2>/dev/null || true)
    curl -sf -H "X-aws-ec2-metadata-token: $token" "http://169.254.169.254/latest/meta-data/$1" 2>/dev/null || true
}

[ "$(id -u)" -eq 0 ] || { echo "Ejecuta este script como root (sudo)." >&2; exit 1; }
cd "$APP_DIR"

PRIMERA_VEZ=0
[ -f .env ] || PRIMERA_VEZ=1

if [ "$PRIMERA_VEZ" -eq 0 ]; then
    log "Modo mantenimiento y actualización del código (rama $BRANCH)"
    as_app php artisan down --retry=15 || true
    trap 'as_app php artisan up || true' EXIT
    as_app git pull --ff-only origin "$BRANCH"
fi

log "Dependencias de producción (composer install --no-dev)"
as_app composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

if [ "$PRIMERA_VEZ" -eq 1 ]; then
    APP_HOST="${1:-$(imds public-ipv4)}"
    log "Creando .env de producción para https://$APP_HOST"
    DB_PASS="$(cat "/home/$APP_USER/.db_password")"
    MASTER_PASS="$(openssl rand -base64 24 | tr -d '/+=' | cut -c1-16)"
    (umask 077; sed -e "s|__APP_HOST__|$APP_HOST|g" \
                    -e "s|__DB_PASSWORD__|$DB_PASS|g" \
                    -e "s|__MASTER_PASSWORD__|$MASTER_PASS|g" \
                    deploy/env.production.example > .env)
    chown "$APP_USER:$APP_USER" .env
    as_app php artisan key:generate --force --no-interaction
fi

[ -L public/storage ] || as_app php artisan storage:link

log "Migraciones"
as_app php artisan migrate --force --no-interaction

if [ "$PRIMERA_VEZ" -eq 1 ]; then
    log "Semillas: catálogos, geografía y cuenta maestra"
    as_app php artisan db:seed --force --no-interaction
fi

log "Cachés de configuración, rutas, eventos y vistas"
as_app php artisan optimize --no-interaction

# Permisos defensivos: todo del usuario de la app; nginx (en el grupo) solo lee
chown -R "$APP_USER:$APP_USER" storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 2750 {} +
find storage bootstrap/cache -type f -exec chmod 640 {} +

systemctl reload php-fpm

if [ "$PRIMERA_VEZ" -eq 1 ]; then
    echo
    echo "======================================================================"
    echo " Casa Ronald instalada en https://$APP_HOST"
    echo "   Cuenta maestra:  $(grep '^MASTER_EMAIL=' .env | cut -d= -f2)"
    echo "   Contraseña:      $MASTER_PASS"
    echo " Cámbiala desde la aplicación tras el primer inicio de sesión."
    echo "======================================================================"
else
    trap - EXIT
    as_app php artisan up
    log "Despliegue terminado"
fi
