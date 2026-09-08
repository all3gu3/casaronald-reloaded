#!/usr/bin/env bash
# Casa Ronald — respaldo de base de datos y archivos (instalado como /usr/local/bin/casaronald-backup)
# Lo dispara casaronald-backup.timer todos los días a las 03:15; también se puede correr a mano.
set -Eeuo pipefail

DEST=/var/backups/casaronald
APP_DIR=/var/www/casaronald
DB_NAME=casaronald
KEEP_DAYS=14
STAMP="$(date +%Y%m%d-%H%M)"

umask 077
mkdir -p "$DEST"

mariadb-dump --single-transaction --quick --routines --triggers --events "$DB_NAME" \
    | gzip -6 > "$DEST/db-$STAMP.sql.gz"

# Fotos, carnets y el .env (APP_KEY y credenciales) para poder restaurar en otro servidor
tar -czf "$DEST/archivos-$STAMP.tar.gz" -C "$APP_DIR" storage/app .env

find "$DEST" -type f -name '*.gz' -mtime +"$KEEP_DAYS" -delete

echo "Respaldo $STAMP guardado en $DEST ($(du -sh "$DEST" | cut -f1) en total)"
