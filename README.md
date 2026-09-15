# Casa Ronald McDonald: Expediente Digital

Sistema de gestión de huéspedes y servicios de la operación de la **Casa Ronald McDonald (Puebla)**: El sistema cuenta con un expediente digital del menor y sus acompañantes (con estudio socioeconómico) para facilitar la información a trabajadores sociales, credencial imprimible con código QR de 6 caracteres, registro de servicios por escaneo (comedor, lavandería, escuelita, transporte, entradas/salidas) y concentrados de datos de entrada/salida, y uso de servicios en gráficos y formatos exportables.

## Gestión de uso de servicios a cargo del personal operativo
El objetivo del Expediente Digital es que cada familia se registre una vez y que a partir de ahí baste con escanear su credencial para dejar constancia de cada servicio que recibe.

<img width=100% alt="image" src="https://github.com/user-attachments/assets/5e99fd25-7fe3-499e-b490-66800619c9fe" />

## Expediente digital
Se capturan los datos relevantes de las familias y se conecta la información completa del niño
| Concentrado de Expedientes |
|----------------|
| <img width="1470" height="454" alt="image" src="https://github.com/user-attachments/assets/55f00596-31d9-4aca-b284-e0f91d9a0e6b" /> |

| Captura de Expediente | Credencial QR imprimible |
|----------|----------|
| <img width="1470" height="711" alt="image" src="https://github.com/user-attachments/assets/20685e5a-25f5-49a7-9383-5e3e4efedff6" /> | <img width="1470" height="712" alt="image" src="https://github.com/user-attachments/assets/01f13564-008a-4dbf-9b46-262844af6b3a" />

### Proceso de captura de uso de servicios para personal operativo
| 1: Selección de servicio | 2: Captura de código (QR o manual) | Registro de servicio exitoso|
|----------|----------|----------|
| <img width="478" height="457" alt="image" src="https://github.com/user-attachments/assets/13ce4270-be41-48b4-bdab-3c7bbc3b8736" /> | <img width="479" height="450" alt="image" src="https://github.com/user-attachments/assets/2c4d4878-1f79-4165-a37d-93642de0b67e" /> | <img width="473" height="456" alt="image" src="https://github.com/user-attachments/assets/26553b6f-f6b5-4eef-a1a0-521539e4aac3" /> |

## Visualización de datos

### Bitácora
Acceso a la bitácora para el personal operativo
| Acceso a Bitácora |
|---|
| <img width="1470" height="719" alt="image" src="https://github.com/user-attachments/assets/4b40eeac-b2ee-4df5-8969-31375418c0fe" />| 

### Reportes y administración
Acceso a reportes descargables y visualización de actividad para cuentas administrativas
| Reportes de Servicios | Administración de cuentas | Registro de actividad en la plataforma |
|----------|----------|----------|
| <img width="1470" height="720" alt="image" src="https://github.com/user-attachments/assets/cf642358-df4b-45c8-97c8-c9795163cb48" /> | <img width="1463" height="423" alt="image" src="https://github.com/user-attachments/assets/2ba6a0ee-4ceb-4847-b764-1a0410f346c2" /> <img width="1470" height="301" alt="image" src="https://github.com/user-attachments/assets/fb9ccbcf-c163-4783-8b13-55fe1b946122" /> | <img width="1470" height="718" alt="image" src="https://github.com/user-attachments/assets/0b7dd727-ab0a-48a3-8b62-07a87075d618" /> |

## Versión móvil
El diseño de la interfaz de usuario es adaptable dispositivos móviles

| Presentación y funcionamiento para dispositivos móviles |
|---|
| <img height="300" alt="image" src="https://github.com/user-attachments/assets/edb54f96-6e5b-4955-98cb-8d272a5dc30a" /> <img height="300" alt="image" src="https://github.com/user-attachments/assets/51969473-bca0-4428-a912-05e1172b4f99" /> <img height="300" alt="image" src="https://github.com/user-attachments/assets/345a745b-df61-436e-8b75-f437223c2862" /> <img height="300" alt="image" src="https://github.com/user-attachments/assets/7adc9947-ec62-408a-8d87-6f39c3cc8b63" /> <img height="300" alt="image" src="https://github.com/user-attachments/assets/d0f73247-f56a-4f44-87bb-2d6b3dead98a" /> |

---
# Arquitectura y diseño del sistema

## Stack
- Laravel 12 · PHP ≥ 8.2 (desarrollado con 8.5) · MySQL 8 o MariaDB 10.6+ (pruebas: sqlite `:memory:`)
- Blade + jQuery 3.7 + Bootstrap 4.6 + DataTables (Yajra) — todo vendorizado en `public/vendor/`
- QR local con `endroid/qr-code` (sin servicios externos) · carnet con `intervention/image` v3
- Escáner web con `html5-qrcode` (cámara del teléfono o lector USB)

## Qué está en operación
- Expediente del menor y de sus acompañantes, con foto, catálogos de la operación real y edición posterior.
- Credencial con QR generada en el servidor, en JPG y en PDF tamaño carta.
- Estación de escaneo desde el navegador —cámara del teléfono o lector USB— para los seis tipos de registro.
- Concentrados por servicio, reporte de uso por periodo con descarga en Excel y expediente completo en PDF.
- Autenticación con tres roles, bitácora de auditoría y administración de cuentas.
- Despliegue reproducible por script, con HTTPS obligatorio, respaldos diarios y parches automáticos.

## Alcance funcional

La cobertura funcional sigue el proceso público de admisión de la Fundación: la familia llega canalizada por trabajo social del hospital con solicitud, valoración médica y estudio socioeconómico. Cada paso de ese proceso tiene su contraparte en el sistema.

| Proceso real de la Casa | Módulo del sistema | Datos que captura |
|---|---|---|
| Solicitud de hospedaje vía trabajo social | Expediente del menor<br>`/expedientes` | Identidad, fecha de nacimiento, sexo, hospital, diagnóstico, médico tratante, alergias, dieta, estatus de estancia (primera vez, prórroga, subsecuente) y fechas de solicitud, ingreso y salida |
| Estudio socioeconómico | Mismo expediente + acompañantes | Escolaridad, ocupación, ingreso mensual, dependientes económicos, vivienda propia, seguro médico, apoyo financiero, zona (rural, suburbana, urbana), clasificación social, salario mínimo y dialecto o lengua |
| Familias de todo el país | Catálogos de geografía | `pais` → `estado` → `municipio`: 3 países, 32 estados y 97 municipios precargados |
| Red de hospitales de Puebla | Catálogo `hospital` | 17 hospitales de la operación real: Hospital para el Niño Poblano, CRIT, IMSS Margarita, Cruz Roja Puebla, entre otros |
| Credencial de la familia | Generador de carnet<br>`/fichas/{nino}` | Carnet tamaño carta (1275×1650 px) con identidad de la Fundación, datos del menor, procedencia, hospital, acompañantes con línea de firma y el QR |
| Comedor (tres comidas, dietas)<br>Lavandería · Escuelita · Transporte | Registro de servicios por escaneo | Bitácora `registros_servicios`: un renglón por evento con servicio y marca de tiempo. La confirmación del escaneo devuelve la dieta y las alergias del menor |
| Control de acceso al albergue | Entradas y salidas por escaneo | Bitácora `entradas_salidas`: la entrada abre el renglón y la salida lo cierra; una salida sin entrada abierta se rechaza |
| Reportes a patronato y donantes | Concentrados y reportes | Tablas por servicio, gráfico de uso por periodo y descarga de la bitácora en Excel |
| Resguardo del expediente | Reporte PDF y auditoría | Expediente completo en PDF con foto y QR incrustados; bitácora de qué cuenta hizo qué y cuándo |

**Fuera de alcance por ahora, previsto como evolución:** el módulo de habitaciones y ocupación (la tabla `registro_operativo` ya está modelada, pero sin pantallas), la administración del catálogo de trabajadoras sociales desde la interfaz, y la extensión a varias Casas y Salas Familiares con datos segregados por sede.

## Modelo Entidad-Relación
| Modelo Entidad-Relación |
|---|
| <img width=100% alt="image" src="https://github.com/user-attachments/assets/0b6f19db-dd2e-49f9-be12-2de0c5cc39e2" /> |

## Diagrama de Clases
| Diagrama de Clases |
|---|
| <img width=100% alt="image" src="https://github.com/user-attachments/assets/d23cab08-31c4-4f21-a8e1-42d87c377d1b" /> |

| Cómo atraviesa una petición la aplicación |
|---|
| <img width=100% alt="image" src="https://github.com/user-attachments/assets/feb202e7-b528-4e8d-80fa-6bc9a10575cb" /> |

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

## Historia

> **Estado**: reescritura del prototipo 2021 sobre Laravel 12 / PHP 8.2+, con
> autenticación, validación, esquema corregido, suite de pruebas y despliegue
> reproducible (`deploy/`). El prototipo original sigue en el repositorio
> [all3gu3/casaronald](https://github.com/all3gu3/casaronald); su auditoría completa
> vive en `DOCUMENTATION.md`.

El prototipo (dic 2020 – ene 2021, Laravel 8) vive en el repositorio original
[all3gu3/casaronald](https://github.com/all3gu3/casaronald) (tag `legacy-final`).
`DOCUMENTATION.md` conserva la auditoría archivo por archivo de ese código: los
defectos ahí listados (autenticación ausente, validación comentada, apellidos
cruzados, fechas que no se guardaban, QR sin unicidad, bitácoras sin FK, grid de
entradas/salidas roto, etc.) son los que esta rama corrige.
