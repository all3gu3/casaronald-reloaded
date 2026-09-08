#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# deploy/provision.sh — Prepara un servidor Amazon Linux 2023 recién creado
# para correr Casa Ronald (nginx + PHP-FPM 8.5 + MariaDB 11.4 LTS).
#
# Es idempotente: se puede volver a ejecutar sin romper nada.
#
#   Uso (como root):   APP_HOST=mi-dominio.org bash deploy/provision.sh
#   Sin APP_HOST usa la IP pública de la instancia.
#
# Al terminar imprime la llave pública de despliegue que hay que registrar
# en GitHub (Settings → Deploy keys, solo lectura) para poder clonar el repo.
# ---------------------------------------------------------------------------
set -Eeuo pipefail

AQUI="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_USER=casaronald
APP_DIR=/var/www/casaronald
DB_NAME=casaronald
DB_USER=casaronald
PHP=php8.5
MARIADB=mariadb114-server
TZ_NAME=America/Mexico_City
SWAP_MB=2048

log() { printf '\n\033[1;34m==> %s\033[0m\n' "$*"; }
imds() {
    local token
    token=$(curl -sf -X PUT http://169.254.169.254/latest/api/token -H 'X-aws-ec2-metadata-token-ttl-seconds: 60' 2>/dev/null || true)
    curl -sf -H "X-aws-ec2-metadata-token: $token" "http://169.254.169.254/latest/meta-data/$1" 2>/dev/null || true
}

[ "$(id -u)" -eq 0 ] || { echo "Ejecuta este script como root (sudo)." >&2; exit 1; }
grep -q 'platform:al2023' /etc/os-release || echo "Aviso: pensado para Amazon Linux 2023; otras distribuciones pueden necesitar ajustes."

PUBLIC_IP="$(imds public-ipv4)"
PUBLIC_DNS="$(imds public-hostname)"
APP_HOST="${APP_HOST:-${PUBLIC_IP:-localhost}}"

# ---------------------------------------------------------------- 1. sistema
log "Zona horaria $TZ_NAME y swap de ${SWAP_MB} MB (la instancia tiene menos de 1 GB de RAM)"
timedatectl set-timezone "$TZ_NAME"
if ! swapon --show --noheadings | grep -q '^/swapfile'; then
    dd if=/dev/zero of=/swapfile bs=1M count="$SWAP_MB" status=none
    chmod 600 /swapfile
    mkswap /swapfile >/dev/null
    swapon /swapfile
fi
grep -q '^/swapfile' /etc/fstab || echo '/swapfile none swap sw 0 0' >> /etc/fstab
cat > /etc/sysctl.d/90-casaronald.conf <<'SYSCTL'
vm.swappiness = 10
vm.vfs_cache_pressure = 50
SYSCTL
sysctl -q --system

# --------------------------------------------------------------- 2. paquetes
log "Actualizando el sistema e instalando nginx, $PHP, $MARIADB, certbot y utilerías"
dnf -y -q update
dnf -y -q install git nginx "$MARIADB" \
    "$PHP-cli" "$PHP-fpm" "$PHP-common" "$PHP-pdo" "$PHP-mysqlnd" "$PHP-gd" \
    "$PHP-mbstring" "$PHP-xml" "$PHP-zip" "$PHP-bcmath" "$PHP-intl" "$PHP-sodium" "$PHP-process" \
    certbot python3-certbot-nginx dnf-automatic unzip tar gzip

# --------------------------------------------------------------- 3. composer
if ! command -v composer >/dev/null; then
    log "Instalando Composer (verificando la firma del instalador)"
    esperado="$(curl -fsSL https://composer.github.io/installer.sig)"
    curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php
    php -r "exit(hash_file('sha384', '/tmp/composer-setup.php') === '$esperado' ? 0 : 1);" \
        || { echo "Instalador de Composer corrupto; abortando." >&2; rm -f /tmp/composer-setup.php; exit 1; }
    php /tmp/composer-setup.php --quiet --install-dir=/usr/local/bin --filename=composer
    rm -f /tmp/composer-setup.php
fi

# ----------------------------------------------- 4. usuario de la aplicación
log "Usuario de sistema '$APP_USER' (dueño del código y de PHP-FPM) y llave de despliegue"
id -u "$APP_USER" >/dev/null 2>&1 \
    || useradd --system --create-home --home-dir "/home/$APP_USER" --shell /sbin/nologin "$APP_USER"
usermod -aG "$APP_USER" nginx          # nginx solo necesita leer public/ y storage/app/public
install -d -m 755 /var/www
install -d -m 750 -o "$APP_USER" -g "$APP_USER" "$APP_DIR"
install -d -m 700 -o "$APP_USER" -g "$APP_USER" "/home/$APP_USER/.ssh"
if [ ! -f "/home/$APP_USER/.ssh/id_ed25519" ]; then
    sudo -u "$APP_USER" ssh-keygen -q -t ed25519 -N '' -C "deploy@casaronald" -f "/home/$APP_USER/.ssh/id_ed25519"
fi
# Llaves de host de GitHub, obtenidas por HTTPS de la API oficial (no por ssh-keyscan)
curl -fsSL https://api.github.com/meta \
    | python3 -c 'import json,sys; [print("github.com "+k) for k in json.load(sys.stdin)["ssh_keys"]]' \
    > "/home/$APP_USER/.ssh/known_hosts"
chown "$APP_USER:$APP_USER" "/home/$APP_USER/.ssh/known_hosts"
chmod 600 "/home/$APP_USER/.ssh/known_hosts"
git config --system --add safe.directory "$APP_DIR" 2>/dev/null || true

# -------------------------------------------------------------------- 5. PHP
log "Configurando PHP ($PHP) y el pool de PHP-FPM"
install -m 644 "$AQUI/php/99-casaronald.ini" /etc/php.d/99-casaronald.ini
install -m 644 "$AQUI/php/www.conf" /etc/php-fpm.d/www.conf
systemctl enable -q --now php-fpm
systemctl restart php-fpm

# ---------------------------------------------------------------- 6. MariaDB
log "Configurando MariaDB: endurecimiento, base '$DB_NAME' y usuario '$DB_USER'"
install -m 644 "$AQUI/mariadb/casaronald.cnf" /etc/my.cnf.d/99-casaronald.cnf
systemctl enable -q --now mariadb
systemctl restart mariadb
for _ in $(seq 1 30); do mariadb -e 'SELECT 1' >/dev/null 2>&1 && break; sleep 1; done
mariadb <<SQL
DELETE FROM mysql.global_priv WHERE User='';
DELETE FROM mysql.global_priv WHERE User='root' AND Host NOT IN ('localhost','127.0.0.1','::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
FLUSH PRIVILEGES;
SQL
PASS_FILE="/home/$APP_USER/.db_password"
if [ ! -s "$PASS_FILE" ]; then
    (umask 077; openssl rand -base64 30 | tr -d '/+=' | cut -c1-28 > "$PASS_FILE")
    chown "$APP_USER:$APP_USER" "$PASS_FILE"
fi
DB_PASS="$(cat "$PASS_FILE")"
mariadb <<SQL
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
ALTER USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SQL

# ------------------------------------------------------------------ 7. nginx
log "Configurando nginx para $APP_HOST (HTTPS con certificado autofirmado mientras no haya dominio)"
install -d -m 700 /etc/nginx/ssl
if [ ! -f /etc/nginx/ssl/casaronald.crt ]; then
    san="IP:127.0.0.1"
    [ -n "$PUBLIC_IP" ] && san="$san,IP:$PUBLIC_IP"
    [ -n "$PUBLIC_DNS" ] && san="$san,DNS:$PUBLIC_DNS"
    if [[ "$APP_HOST" =~ ^[0-9.]+$ ]]; then [[ "$san" == *"IP:$APP_HOST"* ]] || san="$san,IP:$APP_HOST"
    else [[ "$san" == *"DNS:$APP_HOST"* ]] || san="$san,DNS:$APP_HOST"; fi
    openssl req -x509 -nodes -newkey rsa:2048 -days 3650 -sha256 \
        -keyout /etc/nginx/ssl/casaronald.key -out /etc/nginx/ssl/casaronald.crt \
        -subj "/CN=$APP_HOST/O=Casa Ronald McDonald Puebla" -addext "subjectAltName=$san" 2>/dev/null
    chmod 600 /etc/nginx/ssl/casaronald.key
fi
install -m 644 "$AQUI/nginx/nginx.conf" /etc/nginx/nginx.conf
sed "s/__APP_HOST__/$APP_HOST/g" "$AQUI/nginx/casaronald.conf" > /etc/nginx/conf.d/casaronald.conf
nginx -t
systemctl enable -q --now nginx
systemctl reload nginx

# --------------------------------------------------------------- 8. respaldos
log "Respaldo diario (03:15) de base de datos y archivos en /var/backups/casaronald"
install -m 755 "$AQUI/backup.sh" /usr/local/bin/casaronald-backup
install -m 644 "$AQUI/systemd/casaronald-backup.service" /etc/systemd/system/casaronald-backup.service
install -m 644 "$AQUI/systemd/casaronald-backup.timer" /etc/systemd/system/casaronald-backup.timer
install -d -m 700 /var/backups/casaronald
systemctl daemon-reload
systemctl enable -q --now casaronald-backup.timer

# ------------------------------------- 9. parches de seguridad automáticos
log "Parches de seguridad automáticos (dnf-automatic)"
sed -i -e 's/^upgrade_type = .*/upgrade_type = security/' \
       -e 's/^apply_updates = .*/apply_updates = yes/' /etc/dnf/automatic.conf
systemctl enable -q --now dnf-automatic.timer

# ----------------------------------------------------------- 10. utilerías
cat > /usr/local/bin/casaronald-artisan <<'ART'
#!/usr/bin/env bash
# Ejecuta artisan como el usuario de la aplicación (evita archivos con dueño equivocado).
exec sudo -u casaronald -H php /var/www/casaronald/artisan "$@"
ART
chmod 755 /usr/local/bin/casaronald-artisan

log "Servidor listo."
echo
echo "Llave pública de despliegue (regístrala en GitHub como Deploy key de solo lectura):"
echo
cat "/home/$APP_USER/.ssh/id_ed25519.pub"
echo
echo "Después clona el repositorio y ejecuta deploy/deploy.sh:"
echo "  sudo -u $APP_USER -H git clone --branch main git@github.com:USUARIO/REPO.git $APP_DIR"
echo "  sudo bash $APP_DIR/deploy/deploy.sh $APP_HOST"
