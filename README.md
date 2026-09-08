# Casa Ronald — Casita Digital

Sistema de gestión de huéspedes y servicios hecho a la medida de la operación de la
**Casa Ronald McDonald (Puebla)**: expediente del menor y sus acompañantes (con estudio
socioeconómico), credencial imprimible con código QR de 6 caracteres, registro de
servicios por escaneo (comedor, lavandería, escuelita, transporte, entradas/salidas)
y concentrados por servicio.

> **Estado**: reescritura del prototipo 2021 sobre Laravel 12 / PHP 8.2+, con
> autenticación, validación, esquema corregido, suite de pruebas y despliegue
> reproducible (`deploy/`). El prototipo original sigue en el repositorio
> [all3gu3/casaronald](https://github.com/all3gu3/casaronald); su auditoría completa
> vive en `DOCUMENTATION.md`.

## Stack

- Laravel 12 · PHP ≥ 8.2 (desarrollado con 8.5) · MySQL 8 o MariaDB 10.6+ (pruebas: sqlite `:memory:`)
- Blade + jQuery 3.7 + Bootstrap 4.6 + DataTables (Yajra) — todo vendorizado en `public/vendor/`
- QR local con `endroid/qr-code` (sin servicios externos) · carnet con `intervention/image` v3
- Escáner web con `html5-qrcode` (cámara del teléfono o lector USB)

## Puesta en marcha

```bash
composer install
cp .env.example .env && php artisan key:generate

# MySQL: crear la base y ajustar credenciales en .env
mysql -u root -e "CREATE DATABASE IF NOT EXISTS casaronald CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Cuenta maestra: define en .env ANTES de sembrar
#   MASTER_NAME, MASTER_EMAIL, MASTER_PASSWORD

php artisan migrate:fresh --seed   # catálogos reales + cuenta maestra (+ demo en local)
php artisan storage:link           # sin esto las fotos de expediente salen rotas (404)
php artisan serve                  # → http://127.0.0.1:8000
```

En entorno `local` se siembra la familia demo **Ronald Mc Uno..Cuatro** (QRs `A1B2C3`,
`J1B2X7`, `B1S2C3`, `A9B215`).

## Cuentas y roles

- **master** (cuenta maestra): todo + `/usuarios` — crear cuentas, desactivar/reactivar
  (nunca se borran) y restablecer contraseñas. Se crea/actualiza con `MASTER_*` del
  `.env` al sembrar (`Database\Seeders\MasterUserSeeder`).
- **staff** (personal): expedientes, concentrados y estación de escaneo.

Todo exige sesión salvo `/login` y las páginas de propuesta `/casita-secreta`.

## Registro por escaneo (ventana flotante)

El botón **Escanear** (barra superior o pantalla de inicio) abre una ventana flotante
disponible en cualquier página — la antigua página `/escanear` ya no existe:

1. Elige el servicio (comedor, lavandería, escuelita, transporte, entrada o salida);
   luego activa la cámara o teclea el código. Al escanear con la cámara, el código se
   rellena solo, aparece la confirmación verde y la cámara se apaga. Al **Registrar**,
   la confirmación muestra nombre, edad, **dieta** y alergias del menor.
2. La **cámara requiere HTTPS o localhost** (restricción de los navegadores para
   `getUserMedia`). Para probar desde un teléfono en la red local usa un túnel
   (p. ej. `ngrok http 8000`) o despliega detrás de TLS.
3. La **captura manual** (teclear el código o usar un lector USB tipo teclado) siempre
   está disponible y pasa por el mismo endpoint validado.

Reglas de entradas/salidas: una entrada abre un registro; una segunda entrada sin
salida se rechaza con mensaje; la salida cierra el registro abierto; una salida sin
entrada abierta se rechaza (el prototipo tronaba con error 500).

## Credencial (carnet)

`GET /fichas/{nino}` genera (o regenera, si el expediente cambió) el JPG 1072×830 y lo
sirve **solo autenticado**; `?download=1` lo descarga. Assets del diseño en
`resources/ficha/`; salida en `storage/app/fichas/` (disco no público).

## Pruebas

```bash
php artisan test        # 88 pruebas sobre sqlite :memory: — nunca toca tu MySQL
vendor/bin/pint         # estilo de código
```

Fábricas para los 21 modelos del dominio (faker `es_MX`) con estados útiles
(`master()`, `inactivo()`, `cerrada()`, `egresado()`); en pruebas usa `->recycle()`
para no crear la cadena de 9 catálogos por cada niño.

## Despliegue en producción

La carpeta [`deploy/`](deploy/README.md) contiene todo lo necesario para un servidor
**Amazon Linux 2023** (nginx + PHP-FPM 8.5 + MariaDB 11.4 LTS): `provision.sh` prepara
la máquina una sola vez y `deploy.sh` instala o actualiza la aplicación (mantenimiento,
`git pull`, `composer install --no-dev`, migraciones, cachés, recarga de PHP-FPM).
Incluye HTTPS obligatorio, respaldos diarios y parches de seguridad automáticos.

```bash
ssh ec2-user@SERVIDOR 'sudo bash /var/www/casaronald/deploy/deploy.sh'   # publicar main
```

## Historia

El prototipo (dic 2020 – ene 2021, Laravel 8) vive en el repositorio original
[all3gu3/casaronald](https://github.com/all3gu3/casaronald) (tag `legacy-final`).
`DOCUMENTATION.md` conserva la auditoría archivo por archivo de ese código: los
defectos ahí listados (autenticación ausente, validación comentada, apellidos
cruzados, fechas que no se guardaban, QR sin unicidad, bitácoras sin FK, grid de
entradas/salidas roto, etc.) son los que esta rama corrige.
