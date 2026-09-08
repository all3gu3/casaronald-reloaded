# Despliegue en producción — AWS EC2 (Amazon Linux 2023)

Todo lo que necesita el servidor vive en esta carpeta y se instala con dos scripts:

| Script | Corre como | Qué hace |
|---|---|---|
| `provision.sh` | root, una vez por servidor | Paquetes (nginx, PHP-FPM 8.5, MariaDB 11.4 LTS, certbot), swap, usuario `casaronald`, llave de despliegue, base de datos, configuración de nginx/PHP/MariaDB, respaldos diarios y parches de seguridad automáticos. Idempotente. |
| `deploy.sh` | root, en cada despliegue | Primera vez: crea `.env`, migra, siembra y muestra la contraseña maestra. Después: mantenimiento → `git pull` → `composer install` → migraciones → cachés → recarga PHP-FPM. |

## Arquitectura en el servidor

- **Código**: `/var/www/casaronald` (clon de la rama `main`), dueño `casaronald`.
- **Usuario `casaronald`**: sin shell; corre PHP-FPM y todos los comandos artisan/composer.
  nginx (usuario `nginx`) pertenece a su grupo y solo lee `public/` y `storage/app/public`.
- **Base de datos**: MariaDB por socket local (`/var/lib/mysql/mysql.sock`), base `casaronald`,
  usuario `casaronald`; la contraseña se genera en `/home/casaronald/.db_password`.
- **HTTPS**: obligatorio (redirección 80 → 443). Sin dominio se usa un certificado autofirmado
  en `/etc/nginx/ssl/`; el teléfono mostrará un aviso una sola vez y después la cámara del
  escáner funciona con normalidad.
- **Respaldos**: `casaronald-backup.timer` a las 03:15 → `/var/backups/casaronald/`
  (`db-*.sql.gz` + `archivos-*.tar.gz` con fotos, carnets y `.env`; 14 días de retención).
- **Bitácoras**: `storage/logs/laravel-*.log` (14 días), `/var/log/nginx/casaronald.*.log`,
  `/var/log/php-fpm/www-*.log`, `journalctl -u mariadb`.

## Primera instalación

```bash
# 1. Desde tu máquina: sube esta carpeta y provisiona (5 min aprox.)
scp -i casaronald.pem -r deploy ec2-user@SERVIDOR:/tmp/casaronald-deploy
ssh -i casaronald.pem ec2-user@SERVIDOR 'sudo APP_HOST=IP_O_DOMINIO bash /tmp/casaronald-deploy/provision.sh'

# 2. Registra la llave que imprime al final como Deploy key (solo lectura) del repositorio
gh repo deploy-key add llave.pub --repo USUARIO/REPO --title "EC2 casaronald"

# 3. En el servidor: clona y despliega
sudo -u casaronald -H git clone --branch main git@github.com:USUARIO/REPO.git /var/www/casaronald
sudo bash /var/www/casaronald/deploy/deploy.sh IP_O_DOMINIO
```

El grupo de seguridad de la instancia debe permitir entrada por los puertos **22, 80 y 443**.

## Actualizar a una nueva versión

```bash
ssh -i casaronald.pem ec2-user@SERVIDOR 'sudo bash /var/www/casaronald/deploy/deploy.sh'
```

Publica primero los cambios en la rama `main`; el script hace `git pull --ff-only`, así que
nunca pisa cambios hechos a mano en el servidor (si los hay, falla y avisa).

## Directorio de trabajadoras sociales

El expediente exige una trabajadora social y no hay pantalla para administrar ese
catálogo, así que la instalación siembra solo la fila neutral **«Por asignar»**.
Carga el directorio real una vez (una línea por persona):

```bash
sudo casaronald-artisan tinker --execute="foreach (['Nombre Uno', 'Nombre Dos'] as \$n) App\Models\TrabajadorSocial::firstOrCreate(['trabajador_social' => \$n]);"
```

Para volver a sembrar catálogos o restablecer la cuenta maestra con los `MASTER_*`
del `.env`: `sudo bash /var/www/casaronald/deploy/deploy.sh --seed`.

## Cuando haya un dominio

```bash
sudo certbot --nginx -d mi-dominio.org        # certificado válido, renovación automática
sudo sed -i 's|^APP_URL=.*|APP_URL=https://mi-dominio.org|' /var/www/casaronald/.env
sudo casaronald-artisan optimize
```

Después descomenta `Strict-Transport-Security` en `/etc/nginx/conf.d/casaronald.conf`
y recarga nginx (`sudo systemctl reload nginx`).

## Operación diaria

```bash
sudo casaronald-artisan migrate:status         # cualquier comando artisan, con el usuario correcto
sudo casaronald-artisan down / up              # modo mantenimiento
sudo casaronald-backup                         # respaldo manual ahora mismo
sudo systemctl status nginx php-fpm mariadb    # estado de los servicios
sudo tail -f /var/www/casaronald/storage/logs/laravel-$(date +%F).log
```

### Restaurar un respaldo

```bash
sudo mariadb casaronald < <(zcat /var/backups/casaronald/db-FECHA.sql.gz)
sudo tar -xzf /var/backups/casaronald/archivos-FECHA.tar.gz -C /var/www/casaronald
sudo chown -R casaronald:casaronald /var/www/casaronald/storage
```

Los respaldos viven en el mismo disco de la instancia: conviene copiarlos fuera
(por ejemplo a S3 con `aws s3 sync /var/backups/casaronald s3://mi-bucket/casaronald/`).

## Ajustes de rendimiento incluidos

- OPcache con 128 MB y revalidación cada 60 s; `deploy.sh` recarga PHP-FPM tras cada despliegue.
- PHP-FPM `pm = dynamic` con máximo 8 procesos (t3.micro, 1 GB de RAM) y swap de 2 GB.
- MariaDB con `innodb_buffer_pool_size = 128M`, `performance_schema` apagado y bitácora de
  consultas lentas (> 2 s) en `/var/lib/mysql/slow.log`.
- nginx con HTTP/2, gzip y caché de estáticos (30 d librerías vendorizadas, 1 h CSS/JS propios).
- Configuración, rutas, eventos y vistas cacheadas (`artisan optimize`); autoloader optimizado.
