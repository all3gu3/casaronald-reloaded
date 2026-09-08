<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Expediente Digital | Modelo y arquitectura</title>
<link rel="icon" href="{{ asset('favicon.ico') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Raleway:wght@600;700;800&family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400&family=IBM+Plex+Mono:wght@400;500&display=swap">
<style>
  :root {
    color-scheme: light;
    --azul: #4872AE;
    --azul-oscuro: #38598A;
    --azul-tenue: #EFF3FA;
    --linea-azul: #C9D4E6;
    --amarillo: #FFC72C;
    --amarillo-tinta: #7a5c00;
    --amarillo-suave: #FFF3D1;
    --rojo: #F64740;
    --verde: #2E7D5B;
    --texto: #33383F;
    --gris: #666666;
    --gris-claro: #8A9099;
    --crema: #FBFAF6;
    --borde: #E8E6DF;
    --codigo: #F1EFE8;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    background: var(--azul);
    font-family: "Open Sans", "Segoe UI", system-ui, sans-serif;
    color: var(--texto);
    line-height: 1.62;
  }
  .lienzo { max-width: 1000px; margin: 0 auto; padding: 2.5rem 1.2rem 4rem; display: flex; flex-direction: column; gap: 1.6rem; }

  img.logo { max-width: 200px; height: auto; margin-bottom: 1.4rem; }
  .volver { text-align: center; }
  .volver a { color: #fff; text-decoration: none; font-family: "Raleway", sans-serif; font-weight: 700; border: 2px solid rgba(255,255,255,0.6); border-radius: 999px; padding: .5rem 1.4rem; display: inline-block; }
  .volver a:hover { background: rgba(255,255,255,0.12); }
  header.portada {
    background: #fff; border-radius: 14px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.25); padding: 3rem 3rem 2.5rem;
  }
  .eyebrow { font-family: "Raleway", sans-serif; font-weight: 700; font-size: .78rem; letter-spacing: .14em; text-transform: uppercase; color: var(--azul); margin: 0 0 .6rem; }
  h1 { font-family: "Raleway", sans-serif; font-weight: 800; font-size: 2.4rem; line-height: 1.1; margin: 0 0 .8rem; text-wrap: balance; }
  h1 .amarillo { color: var(--amarillo); }
  .bajada { font-size: 1.08rem; color: var(--gris); max-width: 66ch; margin: 0 0 1.3rem; }
  .chips { display: flex; flex-wrap: wrap; gap: .5rem; }
  .chip { font-family: "IBM Plex Mono", ui-monospace, monospace; font-size: .74rem; padding: .25rem .7rem; border: 1px solid var(--borde); border-radius: 999px; background: var(--crema); color: var(--gris); white-space: nowrap; }

  article { background: #fff; border-radius: 14px; box-shadow: 0 8px 30px rgba(0,0,0,0.18); padding: 2.4rem 3rem; }
  h2 { font-family: "Raleway", sans-serif; font-weight: 800; font-size: 1.5rem; margin: 0 0 1.1rem; color: var(--azul-oscuro); text-wrap: balance; }
  h2::after { content: ""; display: block; width: 56px; height: 4px; background: var(--rojo); border-radius: 2px; margin-top: .5rem; }
  h3 { font-family: "Raleway", sans-serif; font-weight: 700; font-size: 1.1rem; margin: 1.8rem 0 .5rem; }
  h3:first-of-type { margin-top: 1.2rem; }
  p { margin: 0 0 1rem; max-width: 74ch; }
  ul, ol { margin: 0 0 1rem; padding-left: 1.3rem; max-width: 74ch; }
  li { margin-bottom: .4rem; }
  li::marker { color: var(--azul); }
  code { font-family: "IBM Plex Mono", ui-monospace, monospace; font-size: .86em; background: var(--codigo); padding: .1em .4em; border-radius: 4px; }
  .qr { font-family: "IBM Plex Mono", ui-monospace, monospace; font-weight: 500; letter-spacing: .06em; background: var(--amarillo-suave); color: var(--amarillo-tinta); padding: .05em .45em; border-radius: 4px; }

  .tabla-scroll { overflow-x: auto; border: 1px solid var(--borde); border-radius: 8px; margin-bottom: 1.2rem; background: #fff; }
  table { border-collapse: collapse; width: 100%; font-size: .88rem; }
  th, td { text-align: left; padding: .55rem .85rem; border-bottom: 1px solid var(--borde); vertical-align: top; }
  th { font-family: "Raleway", sans-serif; font-size: .74rem; text-transform: uppercase; letter-spacing: .07em; color: var(--gris); background: var(--crema); white-space: nowrap; }
  tr:last-child td { border-bottom: none; }
  td code { white-space: nowrap; }
  td.n { font-variant-numeric: tabular-nums; text-align: right; white-space: nowrap; }
  .si { color: var(--verde); font-weight: 700; }
  .no { color: var(--gris-claro); }

  figure { margin: 1.4rem 0 1.6rem; }
  figure .marco { overflow-x: auto; border: 1px solid var(--borde); border-radius: 10px; background: var(--crema); padding: 1.2rem; }
  figure svg { display: block; width: 100%; min-width: 620px; height: auto; color: var(--texto); }
  figcaption { font-size: .84rem; color: var(--gris); margin-top: .6rem; max-width: 74ch; }
  figcaption b { color: var(--texto); }

  .nota-azul { border-left: 4px solid var(--azul); background: var(--azul-tenue); border-radius: 0 8px 8px 0; padding: .9rem 1.2rem; margin: 1.1rem 0; }
  .nota-azul p { margin: 0; max-width: none; font-size: .93rem; }
  .honesto { border-left: 4px solid var(--rojo); background: #FDEEED; border-radius: 0 8px 8px 0; padding: .9rem 1.2rem; margin: 1.1rem 0; }
  .honesto p { margin: 0 0 .5rem; max-width: none; font-size: .93rem; }
  .honesto p:last-child { margin-bottom: 0; }

  .rejilla { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: .9rem; margin: 1.2rem 0 1.4rem; }
  .dato { background: var(--crema); border: 1px solid var(--borde); border-radius: 10px; padding: .9rem 1.1rem; }
  .dato b { display: block; font-family: "Raleway", sans-serif; font-weight: 800; font-size: 1.5rem; color: var(--azul-oscuro); font-variant-numeric: tabular-nums; line-height: 1.2; }
  .dato span { font-size: .82rem; color: var(--gris); }

  pre.consola {
    font-family: "IBM Plex Mono", ui-monospace, monospace; font-size: .8rem; line-height: 1.6;
    background: #2C333D; color: #E7EAEE; border-radius: 8px; padding: 1rem 1.2rem;
    overflow-x: auto; margin: .8rem 0 1.2rem;
  }
  pre.consola .cmt { color: #94A3B0; }

  footer { text-align: center; color: rgba(255,255,255,0.88); font-size: .85rem; padding: .5rem 0 0; }
  footer p { max-width: none; margin: 0 auto; }
  footer a { color: var(--amarillo); }

  @media (max-width: 700px) {
    article, header.portada { padding: 1.8rem 1.3rem; }
    h1 { font-size: 1.85rem; }
    .lienzo { padding-top: 1.4rem; }
  }
</style>
</head>
<body>

<div class="lienzo">

<header class="portada">
  <img class="logo" src="{{ asset('img/RMHC_Mexico_logo.png') }}" alt="Fundación Infantil Ronald McDonald México">
  <p class="eyebrow">Documentación técnica · Dirección de Tecnología</p>
  <h1>Expediente <span class="amarillo">Digital</span> — modelo y arquitectura</h1>
  <p class="bajada">Documentación del sistema tal como está hoy: el modelo de datos tabla por tabla, el diagrama de clases del dominio, la infraestructura sobre la que corre y el catálogo completo de casos de uso con sus flujos principales.</p>
  <div class="chips">
    <span class="chip">Laravel 12 · PHP 8.2+</span>
    <span class="chip">MariaDB 11.4 / MySQL 8</span>
    <span class="chip">23 tablas propias</span>
    <span class="chip">17 casos de uso</span>
    <span class="chip">88 pruebas · 434 aserciones</span>
    <span class="chip">EC2 t3.micro · Amazon Linux 2023</span>
    <span class="chip">Licencia MIT</span>
  </div>
</header>

<article>
  <h2>1 · Panorama</h2>
  <p><strong>Qué hace.</strong> Digitaliza el ciclo operativo de una Casa Ronald McDonald: el expediente del menor albergado y de sus acompañantes —incluido el estudio socioeconómico que trabajo social ya levanta—, una credencial imprimible con código QR de seis caracteres, el registro por escaneo de cada servicio (comedor, lavandería, escuelita, transporte, entradas y salidas) y los concentrados y reportes que salen de esos registros.</p>
  <p><strong>Cómo está construido.</strong> Un monolito Laravel renderizado en servidor, con Blade y jQuery en el navegador y MariaDB detrás. Sin colas, sin caché distribuida, sin microservicios y sin dependencias de infraestructura exóticas: la decisión de fondo es que el sistema pueda ser operado y mantenido por cualquier proveedor o voluntario técnico, no solo por quien lo escribió.</p>

  <div class="rejilla">
    <div class="dato"><b>23</b><span>tablas propias en la base</span></div>
    <div class="dato"><b>17</b><span>casos de uso implementados</span></div>
    <div class="dato"><b>3</b><span>roles con permisos distintos</span></div>
    <div class="dato"><b>88</b><span>pruebas automatizadas</span></div>
  </div>

  <h3>Qué está en operación</h3>
  <ul>
    <li>Expediente del menor y de sus acompañantes, con foto, catálogos de la operación real y edición posterior.</li>
    <li>Credencial con QR generada en el servidor, en JPG y en PDF tamaño carta.</li>
    <li>Estación de escaneo desde el navegador —cámara del teléfono o lector USB— para los seis tipos de registro.</li>
    <li>Concentrados por servicio, reporte de uso por periodo con descarga en Excel y expediente completo en PDF.</li>
    <li>Autenticación con tres roles, bitácora de auditoría y administración de cuentas.</li>
    <li>Despliegue reproducible por script, con HTTPS obligatorio, respaldos diarios y parches automáticos.</li>
  </ul>
</article>

<article>
  <h2>2 · Modelo de datos</h2>
  <p>La base tiene 31 tablas: <strong>23 propias</strong> (el dominio más <code>users</code>) y 8 de infraestructura de Laravel (<code>sessions</code>, <code>cache</code>, <code>jobs</code>, <code>migrations</code>…). Las 23 propias se agrupan en cuatro familias: el núcleo del expediente, las bitácoras de eventos, los catálogos y la cadena geográfica.</p>

  <figure>
    <div class="marco">
      <svg viewBox="0 0 940 640" role="img" aria-label="Diagrama entidad-relación: el expediente del menor al centro, con acompañantes, bitácoras de escaneo, catálogos, geografía y la bitácora de cuentas.">
        <defs>
          <marker id="flechaEr" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
            <path d="M 0 0 L 10 5 L 0 10 z" fill="#8FA3C4"></path>
          </marker>
        </defs>

        <!-- catálogos -->
        <rect x="20" y="86" width="210" height="152" rx="7" fill="#ffffff" stroke="#C9D4E6"></rect>
        <rect x="20" y="86" width="210" height="26" rx="7" fill="#FFC72C"></rect>
        <rect x="20" y="103" width="210" height="9" fill="#FFC72C"></rect>
        <text x="32" y="104" font-size="12.5" font-weight="700" fill="#5c4500" font-family="Raleway, sans-serif">12 catálogos</text>
        <text x="32" y="132" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">hospital · tipo_dieta</text>
        <text x="32" y="149" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">escolaridad · parentesco</text>
        <text x="32" y="166" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">ocupacion · edo_salud</text>
        <text x="32" y="183" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">zona · salario_minimo</text>
        <text x="32" y="200" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">tipo_tratamiento · …</text>
        <text x="32" y="223" font-size="10.5" fill="#666666">90 filas precargadas</text>

        <!-- geografía -->
        <rect x="20" y="280" width="210" height="108" rx="7" fill="#ffffff" stroke="#C9D4E6"></rect>
        <rect x="20" y="280" width="210" height="26" rx="7" fill="#FFC72C"></rect>
        <rect x="20" y="297" width="210" height="9" fill="#FFC72C"></rect>
        <text x="32" y="298" font-size="12.5" font-weight="700" fill="#5c4500" font-family="Raleway, sans-serif">Geografía</text>
        <text x="32" y="326" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">pais → estado → municipio</text>
        <text x="32" y="348" font-size="10.5" fill="#666666">3 países · 32 estados</text>
        <text x="32" y="365" font-size="10.5" fill="#666666">97 municipios</text>

        <!-- cuentas y auditoría -->
        <rect x="20" y="452" width="210" height="62" rx="7" fill="#ffffff" stroke="#C9D4E6"></rect>
        <rect x="20" y="452" width="210" height="26" rx="7" fill="#E8E6DF"></rect>
        <rect x="20" y="469" width="210" height="9" fill="#E8E6DF"></rect>
        <text x="32" y="470" font-size="12.5" font-weight="700" fill="#33383F" font-family="IBM Plex Mono, monospace">users</text>
        <text x="32" y="497" font-size="10.5" fill="#666666">role · is_active · password</text>

        <rect x="20" y="556" width="210" height="62" rx="7" fill="#ffffff" stroke="#C9D4E6"></rect>
        <rect x="20" y="556" width="210" height="26" rx="7" fill="#4872AE"></rect>
        <rect x="20" y="573" width="210" height="9" fill="#4872AE"></rect>
        <text x="32" y="574" font-size="12.5" font-weight="700" fill="#ffffff" font-family="IBM Plex Mono, monospace">registro_actividad</text>
        <text x="32" y="601" font-size="10.5" fill="#666666">solo inserción · 5 acciones</text>
        <line x1="125" y1="514" x2="125" y2="552" stroke="#8FA3C4" stroke-width="1.5" marker-end="url(#flechaEr)"></line>
        <text x="133" y="538" font-size="10" fill="#666666">1 → N</text>

        <!-- nino -->
        <rect x="330" y="66" width="252" height="272" rx="7" fill="#ffffff" stroke="#38598A" stroke-width="1.5"></rect>
        <rect x="330" y="66" width="252" height="28" rx="7" fill="#38598A"></rect>
        <rect x="330" y="85" width="252" height="9" fill="#38598A"></rect>
        <text x="344" y="85" font-size="13" font-weight="700" fill="#ffffff" font-family="IBM Plex Mono, monospace">nino</text>
        <text x="504" y="85" font-size="10.5" fill="#C9D4E6" font-family="Raleway, sans-serif">38 col.</text>
        <text x="344" y="115" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">id · qr UNIQUE(6)</text>
        <text x="344" y="134" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">nombre · apellidos · sexo</text>
        <text x="344" y="153" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">fecha_nacimiento · foto</text>
        <text x="344" y="172" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">domicilio · teléfonos</text>
        <text x="344" y="191" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">dialecto</text>
        <text x="344" y="210" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">diagnostico · medico</text>
        <text x="344" y="229" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">alerg_alimentos/med</text>
        <text x="344" y="248" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">estatus_estancia</text>
        <text x="344" y="267" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">fecha_solicitud/ing/sal</text>
        <line x1="344" y1="280" x2="568" y2="280" stroke="#E8E6DF"></line>
        <text x="344" y="299" font-size="10.5" fill="#666666">9 FK a catálogos y geografía</text>
        <text x="344" y="317" font-size="10.5" fill="#666666">edad: calculada, no almacenada</text>

        <line x1="230" y1="162" x2="326" y2="180" stroke="#8FA3C4" stroke-width="1.5" marker-end="url(#flechaEr)"></line>
        <text x="238" y="157" font-size="10" fill="#666666">7 FK</text>
        <line x1="230" y1="322" x2="326" y2="270" stroke="#8FA3C4" stroke-width="1.5" marker-end="url(#flechaEr)"></line>
        <text x="240" y="310" font-size="10" fill="#666666">pais, estado</text>

        <!-- pivote tratamientos -->
        <rect x="330" y="382" width="252" height="66" rx="7" fill="#ffffff" stroke="#C9D4E6"></rect>
        <rect x="330" y="382" width="252" height="26" rx="7" fill="#E8E6DF"></rect>
        <rect x="330" y="399" width="252" height="9" fill="#E8E6DF"></rect>
        <text x="344" y="400" font-size="12" font-weight="700" fill="#33383F" font-family="IBM Plex Mono, monospace">nino_tipo_tratamiento</text>
        <text x="344" y="428" font-size="10.5" fill="#666666">pivote N↔N · par único</text>
        <line x1="456" y1="338" x2="456" y2="378" stroke="#8FA3C4" stroke-width="1.5" marker-end="url(#flechaEr)"></line>
        <text x="464" y="364" font-size="10" fill="#666666">N ↔ N</text>

        <!-- registro operativo -->
        <rect x="330" y="492" width="252" height="80" rx="7" fill="#ffffff" stroke="#C9D4E6" stroke-dasharray="4 3"></rect>
        <rect x="330" y="492" width="252" height="26" rx="7" fill="#E8E6DF"></rect>
        <rect x="330" y="509" width="252" height="9" fill="#E8E6DF"></rect>
        <text x="344" y="510" font-size="12" font-weight="700" fill="#33383F" font-family="IBM Plex Mono, monospace">registro_operativo</text>
        <text x="344" y="538" font-size="10.5" fill="#666666">habitación · ingreso/egreso</text>
        <text x="344" y="556" font-size="10.5" fill="#8A9099">modelado, sin pantallas aún</text>
        <polyline points="326,300 296,300 296,532 326,532" fill="none" stroke="#8FA3C4" stroke-width="1.5" stroke-dasharray="4 3" marker-end="url(#flechaEr)"></polyline>
        <text x="288" y="432" font-size="10" fill="#666666" text-anchor="end">1 → N</text>

        <!-- acompanante -->
        <rect x="682" y="66" width="238" height="140" rx="7" fill="#ffffff" stroke="#38598A" stroke-width="1.5"></rect>
        <rect x="682" y="66" width="238" height="28" rx="7" fill="#38598A"></rect>
        <rect x="682" y="85" width="238" height="9" fill="#38598A"></rect>
        <text x="696" y="85" font-size="13" font-weight="700" fill="#ffffff" font-family="IBM Plex Mono, monospace">acompanante</text>
        <text x="856" y="85" font-size="10.5" fill="#C9D4E6" font-family="Raleway, sans-serif">26 col.</text>
        <text x="696" y="115" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">nombre · edad · sexo</text>
        <text x="696" y="134" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">identificacion · foto</text>
        <text x="696" y="153" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">estudio socioeconómico</text>
        <line x1="696" y1="166" x2="906" y2="166" stroke="#E8E6DF"></line>
        <text x="696" y="185" font-size="10.5" fill="#666666">4 FK a catálogos</text>
        <line x1="586" y1="140" x2="678" y2="130" stroke="#8FA3C4" stroke-width="1.5" marker-end="url(#flechaEr)"></line>
        <text x="596" y="126" font-size="10" fill="#666666">1 → N</text>

        <!-- registros_servicios -->
        <rect x="682" y="250" width="238" height="96" rx="7" fill="#ffffff" stroke="#C9D4E6"></rect>
        <rect x="682" y="250" width="238" height="26" rx="7" fill="#4872AE"></rect>
        <rect x="682" y="267" width="238" height="9" fill="#4872AE"></rect>
        <text x="696" y="268" font-size="12" font-weight="700" fill="#ffffff" font-family="IBM Plex Mono, monospace">registros_servicios</text>
        <text x="696" y="295" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">nino_id · qr · servicio</text>
        <text x="696" y="314" font-size="10.5" fill="#666666">un renglón por escaneo</text>
        <text x="696" y="332" font-size="10.5" fill="#666666">índices en qr y servicio</text>
        <line x1="586" y1="250" x2="678" y2="288" stroke="#8FA3C4" stroke-width="1.5" marker-end="url(#flechaEr)"></line>
        <text x="592" y="252" font-size="10" fill="#666666">1 → N</text>

        <!-- entradas_salidas -->
        <rect x="682" y="390" width="238" height="96" rx="7" fill="#ffffff" stroke="#C9D4E6"></rect>
        <rect x="682" y="390" width="238" height="26" rx="7" fill="#4872AE"></rect>
        <rect x="682" y="407" width="238" height="9" fill="#4872AE"></rect>
        <text x="696" y="408" font-size="12" font-weight="700" fill="#ffffff" font-family="IBM Plex Mono, monospace">entradas_salidas</text>
        <text x="696" y="435" font-size="11" fill="#33383F" font-family="IBM Plex Mono, monospace">entrada · salida DATETIME</text>
        <text x="696" y="454" font-size="10.5" fill="#666666">salida NULL = dentro de la Casa</text>
        <text x="696" y="472" font-size="10.5" fill="#666666">una abierta por menor</text>
        <line x1="586" y1="300" x2="678" y2="428" stroke="#8FA3C4" stroke-width="1.5" marker-end="url(#flechaEr)"></line>
        <text x="600" y="376" font-size="10" fill="#666666">1 → N</text>

        <!-- catálogos → acompanante, ruteado por arriba -->
        <polyline points="125,82 125,32 801,32 801,62" fill="none" stroke="#8FA3C4" stroke-width="1.5" stroke-dasharray="5 4" marker-end="url(#flechaEr)"></polyline>
        <text x="404" y="26" font-size="10" fill="#666666">parentesco · escolaridad · edo_salud · ocupacion</text>
      </svg>
    </div>
    <figcaption><b>Entidades y relaciones.</b> Todas las llaves foráneas son <code>RESTRICT</code>: un catálogo en uso no se puede borrar. Las bitácoras cuelgan del expediente por <code>nino_id</code>, y conservan además el <code>qr</code> escaneado como evidencia de qué credencial se presentó.</figcaption>
  </figure>

  <h3>Tablas núcleo</h3>
  <div class="tabla-scroll"><table>
    <tr><th>Tabla</th><th>Columnas</th><th>Qué guarda</th><th>Notas de diseño</th></tr>
    <tr><td><code>nino</code></td><td class="n">38</td><td>El expediente del menor: identidad, domicilio y procedencia, datos médicos (diagnóstico, médico, alergias, dieta) y el bloque de estancia (estatus, fechas de solicitud, ingreso y salida)</td><td><code>qr</code> con índice <code>UNIQUE</code>; la edad se calcula desde <code>fecha_nacimiento</code> y no se almacena; <code>numero</code> es cadena porque «15-B» existe en el expediente en papel</td></tr>
    <tr><td><code>acompanante</code></td><td class="n">26</td><td>Cada acompañante ligado al menor, con parentesco, edad, estado de salud, ocupación y el estudio socioeconómico completo</td><td>Los ocho campos del estudio se guardan con su tipo real: <code>boolean</code> para las respuestas de sí o no, <code>unsignedInteger</code> para los montos</td></tr>
    <tr><td><code>nino_tipo_tratamiento</code></td><td class="n">5</td><td>Los tratamientos marcados en la solicitud (terapia, cirugía, consulta externa, hospitalización…)</td><td>Pivote N↔N con par único: las 14 casillas del formulario de solicitud se persisten</td></tr>
    <tr><td><code>registro_operativo</code></td><td class="n">17</td><td>La estancia: habitación, ingreso y egreso, reingreso, niños adicionales</td><td><code>fecha_egreso</code> es <code>NULL</code> mientras la estancia sigue abierta. Modelado y con fábrica de pruebas; sin interfaz todavía</td></tr>
  </table></div>

  <h3>Bitácoras</h3>
  <div class="tabla-scroll"><table>
    <tr><th>Tabla</th><th>Se escribe cuando</th><th>Columnas clave</th><th>Regla</th></tr>
    <tr><td><code>registros_servicios</code></td><td>Se escanea una credencial en comedor, lavandería, escuelita o transporte</td><td><code>nino_id</code>, <code>qr</code>, <code>servicio</code>, <code>created_at</code></td><td>Inserción pura: un renglón por evento, nunca se edita. <code>servicio</code> se castea al enum <code>Servicio</code></td></tr>
    <tr><td><code>entradas_salidas</code></td><td>Se registra una entrada o una salida de la Casa</td><td><code>nino_id</code>, <code>qr</code>, <code>entrada</code>, <code>salida</code></td><td>La entrada abre el renglón, la salida lo cierra. <code>DATETIME</code>, no <code>DATE</code>: en un control de acceso la hora es el dato</td></tr>
    <tr><td><code>registro_actividad</code></td><td>Inicio de sesión, escaneo, descarga, alta o edición</td><td><code>user_id</code>, <code>accion</code>, <code>detalle</code>, <code>created_at</code></td><td>Sin <code>updated_at</code>: una bitácora de auditoría no se modifica. Índices en <code>accion</code> y <code>created_at</code></td></tr>
  </table></div>

  <h3>Catálogos precargados</h3>
  <p>Los valores son los de la operación real de la Casa Puebla. Son administrables por base de datos sin tocar código.</p>
  <div class="tabla-scroll"><table>
    <tr><th>Catálogo</th><th class="n">Filas</th><th>Ejemplos</th></tr>
    <tr><td><code>hospital</code></td><td class="n">17</td><td>CRIT, HNP (Hospital para el Niño Poblano), HGCH, IMSS Margarita, Cruz Roja Puebla…</td></tr>
    <tr><td><code>parentesco</code></td><td class="n">14</td><td>Padre, Madre, Abuela, Tía, Tutor, Sin parentesco…</td></tr>
    <tr><td><code>tipo_tratamiento</code></td><td class="n">14</td><td>Terapia, Cirugía, Consulta externa, Rehabilitación, Terapia intensiva…</td></tr>
    <tr><td><code>escolaridad</code></td><td class="n">11</td><td>Sin escuela, Maternal, Primaria, Carrera técnica, Doctorado…</td></tr>
    <tr><td><code>ocupacion</code></td><td class="n">10</td><td>Labores del hogar, Campesino, Albañil, Profesionista…</td></tr>
    <tr><td><code>tipo_dieta</code></td><td class="n">7</td><td>Normal, Blanda, Lactante, Papilla, Renal, Restringida…</td></tr>
    <tr><td><code>clasificacion_social</code></td><td class="n">6</td><td>Rangos 1 a 6</td></tr>
    <tr><td><code>zona</code>, <code>edo_salud</code>, <code>salario_minimo</code></td><td class="n">3 c/u</td><td>Rural/Sub-urbana/Urbana · Sano/En tratamiento/Enfermo · &lt;1, 1a2, &gt;2 salarios</td></tr>
    <tr><td><code>tipo_ninio</code></td><td class="n">2</td><td>En casa · Hospitalizado</td></tr>
    <tr><td><code>trabajador_social</code></td><td class="n">1</td><td>Solo la fila neutral «Por asignar»: los nombres del directorio son datos personales y no se siembran. La Casa carga el suyo al instalar</td></tr>
    <tr><td>Geografía</td><td class="n">132</td><td>3 países, 32 estados, 97 municipios</td></tr>
  </table></div>

  <h3>Convenciones</h3>
  <ul>
    <li>Llave primaria <code>BIGINT id</code> y <code>timestamps</code> en todas las tablas salvo la de auditoría, que solo tiene <code>created_at</code>.</li>
    <li>Todas las llaves foráneas son <code>RESTRICT</code>, excepto el pivote de tratamientos, que es <code>CASCADE</code> respecto del expediente.</li>
    <li>Cotejamiento <code>utf8mb4_unicode_ci</code>; nombres de tabla en singular, consistentes en todo el esquema.</li>
    <li>Fotos de expediente normalizadas antes de guardarse (rotación EXIF corregida, transparencia aplanada, lado máximo 1200 px, JPEG calidad 82).</li>
  </ul>
</article>

<article>
  <h2>3 · Diagrama de clases</h2>
  <p>El dominio es deliberadamente pequeño: seis clases con estado, tres enumeraciones y cuatro servicios sin estado. Todo lo demás son catálogos que solo aportan una etiqueta.</p>

  <figure>
    <div class="marco">
      <svg viewBox="0 0 940 560" role="img" aria-label="Diagrama de clases: modelos Nino, Acompanante, RegistroServicio, EntradaSalida, User y RegistroActividad, con las enumeraciones Servicio, Role y Accion y los servicios QrCodeService, FichaGenerator, FotoPerfil y ExcelBitacora.">
        <defs>
          <marker id="flechaCl" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
            <path d="M 0 0 L 10 5 L 0 10 z" fill="#8FA3C4"></path>
          </marker>
        </defs>

        <text x="20" y="20" font-size="11" font-weight="700" fill="#666666" font-family="Raleway, sans-serif" letter-spacing="1.4">MODELOS DEL DOMINIO</text>

        <!-- Nino -->
        <rect x="20" y="34" width="250" height="182" rx="6" fill="#ffffff" stroke="#38598A" stroke-width="1.5"></rect>
        <rect x="20" y="34" width="250" height="26" rx="6" fill="#38598A"></rect>
        <rect x="20" y="52" width="250" height="8" fill="#38598A"></rect>
        <text x="33" y="52" font-size="12.5" font-weight="700" fill="#ffffff" font-family="IBM Plex Mono, monospace">Nino</text>
        <text x="33" y="79" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">– qr: string(6) UNIQUE</text>
        <text x="33" y="96" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">– fecha_nacimiento: date</text>
        <text x="33" y="113" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">– estatus_estancia: string</text>
        <line x1="33" y1="124" x2="257" y2="124" stroke="#E8E6DF"></line>
        <text x="33" y="142" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">+ edad(): int  ⟵ calculada</text>
        <text x="33" y="159" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">+ nombreCompleto(): string</text>
        <text x="33" y="176" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">+ acompanantes()</text>
        <text x="33" y="193" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">+ registrosServicios()</text>
        <text x="33" y="210" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">+ entradasSalidas()</text>

        <!-- Acompanante -->
        <rect x="330" y="34" width="240" height="106" rx="6" fill="#ffffff" stroke="#38598A" stroke-width="1.5"></rect>
        <rect x="330" y="34" width="240" height="26" rx="6" fill="#38598A"></rect>
        <rect x="330" y="52" width="240" height="8" fill="#38598A"></rect>
        <text x="343" y="52" font-size="12.5" font-weight="700" fill="#ffffff" font-family="IBM Plex Mono, monospace">Acompanante</text>
        <text x="343" y="79" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">– parentesco_id: FK</text>
        <text x="343" y="96" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">– ingreso_mensual: int</text>
        <text x="343" y="113" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">– trabaja, casa_propia: bool</text>
        <text x="343" y="131" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">+ nombreCompleto(): string</text>
        <line x1="274" y1="80" x2="326" y2="80" stroke="#8FA3C4" stroke-width="1.5" marker-end="url(#flechaCl)"></line>
        <text x="280" y="74" font-size="10" fill="#666666">1 → *</text>

        <!-- RegistroServicio -->
        <rect x="330" y="168" width="240" height="92" rx="6" fill="#ffffff" stroke="#C9D4E6"></rect>
        <rect x="330" y="168" width="240" height="26" rx="6" fill="#4872AE"></rect>
        <rect x="330" y="186" width="240" height="8" fill="#4872AE"></rect>
        <text x="343" y="186" font-size="12.5" font-weight="700" fill="#ffffff" font-family="IBM Plex Mono, monospace">RegistroServicio</text>
        <text x="343" y="213" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">– servicio: Servicio</text>
        <text x="343" y="230" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">– qr: string(6)</text>
        <text x="343" y="250" font-size="10.5" fill="#666666" font-family="IBM Plex Mono, monospace">created_at = el evento</text>
        <line x1="274" y1="140" x2="326" y2="190" stroke="#8FA3C4" stroke-width="1.5" marker-end="url(#flechaCl)"></line>
        <text x="272" y="168" font-size="10" fill="#666666">1 → *</text>

        <!-- EntradaSalida -->
        <rect x="330" y="288" width="240" height="110" rx="6" fill="#ffffff" stroke="#C9D4E6"></rect>
        <rect x="330" y="288" width="240" height="26" rx="6" fill="#4872AE"></rect>
        <rect x="330" y="306" width="240" height="8" fill="#4872AE"></rect>
        <text x="343" y="306" font-size="12.5" font-weight="700" fill="#ffffff" font-family="IBM Plex Mono, monospace">EntradaSalida</text>
        <text x="343" y="333" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">– entrada, salida: datetime</text>
        <line x1="343" y1="344" x2="557" y2="344" stroke="#E8E6DF"></line>
        <text x="343" y="362" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">+ scopeAbiertas()</text>
        <text x="343" y="379" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">+ abiertaDe(Nino): ?self</text>
        <polyline points="274,205 300,205 300,344 326,344" fill="none" stroke="#8FA3C4" stroke-width="1.5" marker-end="url(#flechaCl)"></polyline>
        <text x="296" y="290" font-size="10" fill="#666666" text-anchor="end">1 → *</text>

        <!-- User -->
        <rect x="20" y="288" width="250" height="110" rx="6" fill="#ffffff" stroke="#C9D4E6"></rect>
        <rect x="20" y="288" width="250" height="26" rx="6" fill="#E8E6DF"></rect>
        <rect x="20" y="306" width="250" height="8" fill="#E8E6DF"></rect>
        <text x="33" y="306" font-size="12.5" font-weight="700" fill="#33383F" font-family="IBM Plex Mono, monospace">User</text>
        <text x="33" y="333" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">– role: Role</text>
        <text x="33" y="350" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">– is_active: bool</text>
        <line x1="33" y1="361" x2="257" y2="361" stroke="#E8E6DF"></line>
        <text x="33" y="379" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">+ esMaster(): bool</text>

        <!-- RegistroActividad -->
        <rect x="20" y="440" width="250" height="98" rx="6" fill="#ffffff" stroke="#C9D4E6"></rect>
        <rect x="20" y="440" width="250" height="26" rx="6" fill="#4872AE"></rect>
        <rect x="20" y="458" width="250" height="8" fill="#4872AE"></rect>
        <text x="33" y="458" font-size="12.5" font-weight="700" fill="#ffffff" font-family="IBM Plex Mono, monospace">RegistroActividad</text>
        <text x="33" y="485" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">– accion: Accion</text>
        <text x="33" y="502" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">– detalle: ?string</text>
        <line x1="33" y1="513" x2="257" y2="513" stroke="#E8E6DF"></line>
        <text x="33" y="531" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">+ anotar(Accion, ?string)</text>
        <line x1="145" y1="402" x2="145" y2="436" stroke="#8FA3C4" stroke-width="1.5" marker-end="url(#flechaCl)"></line>
        <text x="153" y="424" font-size="10" fill="#666666">1 → *</text>

        <!-- Enumeraciones -->
        <text x="630" y="20" font-size="11" font-weight="700" fill="#666666" font-family="Raleway, sans-serif" letter-spacing="1.4">ENUMERACIONES</text>
        <rect x="630" y="34" width="290" height="88" rx="6" fill="#FFF9E8" stroke="#FFC72C"></rect>
        <text x="643" y="55" font-size="11.5" font-weight="700" fill="#5c4500" font-family="IBM Plex Mono, monospace">«enum» Servicio</text>
        <text x="643" y="74" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">Comedor · Lavanderia</text>
        <text x="643" y="91" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">Escuela · Transporte</text>
        <text x="643" y="112" font-size="10.5" fill="#666666" font-family="IBM Plex Mono, monospace">etiqueta() · icono() · ruta()</text>

        <rect x="630" y="138" width="290" height="72" rx="6" fill="#FFF9E8" stroke="#FFC72C"></rect>
        <text x="643" y="159" font-size="11.5" font-weight="700" fill="#5c4500" font-family="IBM Plex Mono, monospace">«enum» Role</text>
        <text x="643" y="178" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">Master · Staff</text>
        <text x="643" y="195" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">TrabajadorSocial</text>

        <rect x="630" y="226" width="290" height="88" rx="6" fill="#FFF9E8" stroke="#FFC72C"></rect>
        <text x="643" y="247" font-size="11.5" font-weight="700" fill="#5c4500" font-family="IBM Plex Mono, monospace">«enum» Accion</text>
        <text x="643" y="266" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">InicioSesion · Escaneo</text>
        <text x="643" y="283" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">Descarga · Alta · Edicion</text>
        <text x="643" y="304" font-size="10.5" fill="#666666" font-family="IBM Plex Mono, monospace">lo que registra la auditoría</text>

        <!-- Servicios -->
        <text x="630" y="352" font-size="11" font-weight="700" fill="#666666" font-family="Raleway, sans-serif" letter-spacing="1.4">SERVICIOS (SIN ESTADO)</text>
        <rect x="630" y="366" width="290" height="172" rx="6" fill="#FDEEED" stroke="#F64740"></rect>
        <text x="643" y="387" font-size="11.5" font-weight="700" fill="#8C2622" font-family="IBM Plex Mono, monospace">QrCodeService</text>
        <text x="643" y="404" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">generar(): string · render(): png</text>
        <line x1="643" y1="415" x2="907" y2="415" stroke="#F3C7C5"></line>
        <text x="643" y="434" font-size="11.5" font-weight="700" fill="#8C2622" font-family="IBM Plex Mono, monospace">FichaGenerator</text>
        <text x="643" y="451" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">generar(Nino): ruta JPG 1275×1650</text>
        <line x1="643" y1="462" x2="907" y2="462" stroke="#F3C7C5"></line>
        <text x="643" y="481" font-size="11.5" font-weight="700" fill="#8C2622" font-family="IBM Plex Mono, monospace">FotoPerfil</text>
        <text x="643" y="498" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">guardar(UploadedFile): ruta</text>
        <line x1="643" y1="509" x2="907" y2="509" stroke="#F3C7C5"></line>
        <text x="643" y="528" font-size="11.5" font-weight="700" fill="#8C2622" font-family="IBM Plex Mono, monospace">ExcelBitacora</text>
        <text x="643" y="545" font-size="10.5" fill="#33383F" font-family="IBM Plex Mono, monospace">generar(encabezados, filas): .xlsx</text>
      </svg>
    </div>
    <figcaption><b>Clases del dominio.</b> Las flechas son relaciones Eloquent con su multiplicidad. Las enumeraciones no llevan flecha porque ya aparecen como tipo del atributo que las usa (<code>servicio: Servicio</code>, <code>role: Role</code>, <code>accion: Accion</code>), y los servicios se inyectan donde hacen falta: <code>QrCodeService</code> en el alta del expediente, <code>FichaGenerator</code> en el carnet, <code>FotoPerfil</code> en cada foto y <code>ExcelBitacora</code> en la descarga del reporte. Los 12 catálogos se omiten: son idénticos entre sí — id, una columna de etiqueta y una relación inversa.</figcaption>
  </figure>

  <h3>Cómo atraviesa una petición la aplicación</h3>
  <figure>
    <div class="marco">
      <svg viewBox="0 0 940 210" role="img" aria-label="Capas que atraviesa una petición: navegador, middleware de sesión y rol, FormRequest de validación, controlador, servicios y modelos, base de datos.">
        <defs>
          <marker id="flechaCapa" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="8" markerHeight="8" orient="auto-start-reverse">
            <path d="M 0 0 L 10 5 L 0 10 z" fill="#38598A"></path>
          </marker>
        </defs>
        <rect x="10" y="60" width="130" height="72" rx="8" fill="#ffffff" stroke="#C9D4E6"></rect>
        <text x="75" y="88" font-size="12" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Navegador</text>
        <text x="75" y="107" font-size="10.5" fill="#666666" text-anchor="middle">Blade + jQuery</text>
        <text x="75" y="122" font-size="10.5" fill="#666666" text-anchor="middle">cámara o lector USB</text>

        <rect x="178" y="60" width="150" height="72" rx="8" fill="#EFF3FA" stroke="#4872AE"></rect>
        <text x="253" y="84" font-size="12" font-weight="700" fill="#38598A" text-anchor="middle" font-family="Raleway, sans-serif">Middleware</text>
        <text x="253" y="102" font-size="10.5" fill="#33383F" text-anchor="middle" font-family="IBM Plex Mono, monospace">auth · active</text>
        <text x="253" y="118" font-size="10.5" fill="#33383F" text-anchor="middle" font-family="IBM Plex Mono, monospace">master · can:…</text>

        <rect x="366" y="60" width="150" height="72" rx="8" fill="#EFF3FA" stroke="#4872AE"></rect>
        <text x="441" y="84" font-size="12" font-weight="700" fill="#38598A" text-anchor="middle" font-family="Raleway, sans-serif">FormRequest</text>
        <text x="441" y="102" font-size="10.5" fill="#33383F" text-anchor="middle">validación en servidor</text>
        <text x="441" y="118" font-size="10.5" fill="#666666" text-anchor="middle">422 con mensajes</text>

        <rect x="554" y="60" width="150" height="72" rx="8" fill="#ffffff" stroke="#C9D4E6"></rect>
        <text x="629" y="84" font-size="12" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Controlador</text>
        <text x="629" y="102" font-size="10.5" fill="#666666" text-anchor="middle">10 controladores</text>
        <text x="629" y="118" font-size="10.5" fill="#666666" text-anchor="middle">+ 4 servicios</text>

        <rect x="742" y="60" width="188" height="72" rx="8" fill="#ffffff" stroke="#C9D4E6"></rect>
        <text x="836" y="84" font-size="12" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Eloquent → MariaDB</text>
        <text x="836" y="102" font-size="10.5" fill="#666666" text-anchor="middle">socket local, sin puerto</text>
        <text x="836" y="118" font-size="10.5" fill="#666666" text-anchor="middle">expuesto a la red</text>

        <line x1="144" y1="96" x2="174" y2="96" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaCapa)"></line>
        <line x1="332" y1="96" x2="362" y2="96" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaCapa)"></line>
        <line x1="520" y1="96" x2="550" y2="96" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaCapa)"></line>
        <line x1="708" y1="96" x2="738" y2="96" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaCapa)"></line>

        <text x="253" y="45" font-size="10" fill="#666666" text-anchor="middle">sin sesión → /login</text>
        <text x="441" y="45" font-size="10" fill="#666666" text-anchor="middle">CSRF en toda escritura</text>
        <text x="629" y="163" font-size="10" fill="#8C2622" text-anchor="middle">RegistroActividad::anotar()</text>
        <line x1="629" y1="136" x2="629" y2="152" stroke="#F64740" stroke-width="1.5" stroke-dasharray="3 3"></line>
      </svg>
    </div>
    <figcaption><b>Un monolito de cinco capas.</b> Sin colas, sin caché distribuida, sin microservicios: la petición entra, se autoriza, se valida, se ejecuta y se anota. La simplicidad es intencional — el sistema debe ser mantenible por cualquier proveedor o voluntario técnico.</figcaption>
  </figure>
</article>

<article>
  <h2>4 · Infraestructura</h2>
  <p>El sistema corre sobre una única instancia EC2 de Amazon Web Services, provisionada con dos scripts versionados en el repositorio (<code>deploy/provision.sh</code> y <code>deploy/deploy.sh</code>). No hay nada configurado a mano: reconstruir el servidor desde cero es reproducible.</p>

  <div class="rejilla">
    <div class="dato"><b>t3.micro</b><span>2 vCPU · 1 GB RAM + 2 GB de swap</span></div>
    <div class="dato"><b>1</b><span>instancia: web y base de datos juntas</span></div>
    <div class="dato"><b>03:15</b><span>respaldo diario, 14 días de retención</span></div>
    <div class="dato"><b>443</b><span>único puerto de aplicación; 80 redirige</span></div>
  </div>

  <figure>
    <div class="marco">
      <svg viewBox="0 0 940 480" role="img" aria-label="Infraestructura: navegadores y teléfonos entran por HTTPS a nginx en una instancia EC2 con Amazon Linux 2023; nginx sirve estáticos y pasa PHP por socket a PHP-FPM, que ejecuta Laravel y consulta MariaDB por socket local; un temporizador diario respalda base y archivos, y el despliegue llega desde GitHub.">
        <defs>
          <marker id="flechaInf" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
            <path d="M 0 0 L 10 5 L 0 10 z" fill="#38598A"></path>
          </marker>
          <marker id="flechaInfGris" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
            <path d="M 0 0 L 10 5 L 0 10 z" fill="#8A9099"></path>
          </marker>
        </defs>

        <!-- clientes -->
        <rect x="14" y="96" width="164" height="70" rx="8" fill="#ffffff" stroke="#C9D4E6"></rect>
        <text x="96" y="122" font-size="12" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Estación de registro</text>
        <text x="96" y="140" font-size="10.5" fill="#666666" text-anchor="middle">navegador · impresora</text>
        <text x="96" y="155" font-size="10.5" fill="#666666" text-anchor="middle">de credenciales</text>

        <rect x="14" y="204" width="164" height="70" rx="8" fill="#ffffff" stroke="#C9D4E6"></rect>
        <text x="96" y="230" font-size="12" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Puntos de servicio</text>
        <text x="96" y="248" font-size="10.5" fill="#666666" text-anchor="middle">teléfono con cámara</text>
        <text x="96" y="263" font-size="10.5" fill="#666666" text-anchor="middle">o lector USB</text>

        <line x1="182" y1="131" x2="252" y2="150" stroke="#38598A" stroke-width="2" marker-end="url(#flechaInf)"></line>
        <line x1="182" y1="239" x2="252" y2="200" stroke="#38598A" stroke-width="2" marker-end="url(#flechaInf)"></line>
        <text x="216" y="112" font-size="10.5" fill="#38598A" text-anchor="middle" font-weight="700">HTTPS</text>
        <text x="216" y="274" font-size="10.5" fill="#666666" text-anchor="middle">443 · TLS</text>

        <!-- servidor -->
        <rect x="262" y="34" width="486" height="412" rx="10" fill="#ffffff" stroke="#38598A" stroke-width="1.5"></rect>
        <text x="280" y="58" font-size="12.5" font-weight="800" fill="#38598A" font-family="Raleway, sans-serif">EC2 t3.micro · Amazon Linux 2023</text>
        <text x="280" y="75" font-size="10.5" fill="#666666" font-family="IBM Plex Mono, monospace">us-east-1 · usuario de sistema «casaronald», sin shell</text>

        <rect x="282" y="92" width="200" height="82" rx="7" fill="#EFF3FA" stroke="#4872AE"></rect>
        <text x="382" y="116" font-size="12" font-weight="700" fill="#38598A" text-anchor="middle" font-family="Raleway, sans-serif">nginx</text>
        <text x="382" y="134" font-size="10.5" fill="#33383F" text-anchor="middle">HTTP/2 · gzip · 80 → 443</text>
        <text x="382" y="150" font-size="10.5" fill="#33383F" text-anchor="middle">sirve public/ y estáticos</text>
        <text x="382" y="165" font-size="10.5" fill="#666666" text-anchor="middle">cert. autofirmado (sin dominio)</text>

        <rect x="528" y="92" width="200" height="82" rx="7" fill="#EFF3FA" stroke="#4872AE"></rect>
        <text x="628" y="116" font-size="12" font-weight="700" fill="#38598A" text-anchor="middle" font-family="Raleway, sans-serif">PHP-FPM 8.5</text>
        <text x="628" y="134" font-size="10.5" fill="#33383F" text-anchor="middle">pm dynamic · máx. 8 procesos</text>
        <text x="628" y="150" font-size="10.5" fill="#33383F" text-anchor="middle">OPcache 128 MB</text>
        <text x="628" y="165" font-size="10.5" fill="#666666" text-anchor="middle">corre como «casaronald»</text>
        <line x1="486" y1="133" x2="524" y2="133" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaInf)"></line>
        <text x="505" y="126" font-size="9.5" fill="#666666" text-anchor="middle">socket</text>

        <rect x="282" y="200" width="446" height="74" rx="7" fill="#ffffff" stroke="#C9D4E6"></rect>
        <text x="298" y="224" font-size="12" font-weight="700" fill="#33383F" font-family="Raleway, sans-serif">Laravel 12 — /var/www/casaronald</text>
        <text x="298" y="243" font-size="10.5" fill="#666666" font-family="IBM Plex Mono, monospace">storage/app/public/fotos → expuesto por symlink</text>
        <text x="298" y="260" font-size="10.5" fill="#666666" font-family="IBM Plex Mono, monospace">storage/app/fichas → privado, servido solo con sesión</text>
        <line x1="628" y1="178" x2="628" y2="196" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaInf)"></line>

        <rect x="282" y="302" width="200" height="76" rx="7" fill="#EFF3FA" stroke="#4872AE"></rect>
        <text x="382" y="326" font-size="12" font-weight="700" fill="#38598A" text-anchor="middle" font-family="Raleway, sans-serif">MariaDB 11.4 LTS</text>
        <text x="382" y="344" font-size="10.5" fill="#33383F" text-anchor="middle">socket unix, sin puerto abierto</text>
        <text x="382" y="360" font-size="10.5" fill="#666666" text-anchor="middle">innodb_buffer_pool 128 MB</text>
        <line x1="382" y1="278" x2="382" y2="298" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaInf)"></line>

        <rect x="528" y="302" width="200" height="76" rx="7" fill="#ffffff" stroke="#C9D4E6"></rect>
        <text x="628" y="326" font-size="12" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">systemd timers</text>
        <text x="628" y="344" font-size="10.5" fill="#666666" text-anchor="middle">respaldo 03:15 diario</text>
        <text x="628" y="360" font-size="10.5" fill="#666666" text-anchor="middle">dnf-automatic (parches)</text>
        <line x1="524" y1="340" x2="490" y2="340" stroke="#8A9099" stroke-width="1.5" stroke-dasharray="4 3" marker-end="url(#flechaInfGris)"></line>

        <text x="298" y="410" font-size="10.5" fill="#666666" font-family="IBM Plex Mono, monospace">Bitácoras: laravel-*.log (14 d) · nginx · php-fpm · journalctl</text>
        <text x="298" y="429" font-size="10.5" fill="#666666" font-family="IBM Plex Mono, monospace">Cabeceras: X-Frame-Options · nosniff · Referrer-Policy</text>

        <!-- respaldos -->
        <rect x="768" y="288" width="158" height="104" rx="8" fill="#ffffff" stroke="#C9D4E6"></rect>
        <text x="847" y="312" font-size="11.5" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">/var/backups</text>
        <text x="847" y="331" font-size="10.5" fill="#666666" text-anchor="middle">db-*.sql.gz</text>
        <text x="847" y="347" font-size="10.5" fill="#666666" text-anchor="middle">archivos-*.tar.gz</text>
        <text x="847" y="366" font-size="10.5" fill="#666666" text-anchor="middle">14 días</text>
        <text x="847" y="384" font-size="10" fill="#8C2622" text-anchor="middle">mismo disco — sacar a S3</text>
        <line x1="732" y1="340" x2="764" y2="340" stroke="#8A9099" stroke-width="1.5" marker-end="url(#flechaInfGris)"></line>

        <!-- despliegue -->
        <rect x="768" y="96" width="158" height="90" rx="8" fill="#ffffff" stroke="#C9D4E6"></rect>
        <text x="847" y="120" font-size="11.5" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">GitHub (privado)</text>
        <text x="847" y="139" font-size="10.5" fill="#666666" text-anchor="middle">rama main</text>
        <text x="847" y="155" font-size="10.5" fill="#666666" text-anchor="middle">deploy key de solo lectura</text>
        <text x="847" y="174" font-size="10.5" fill="#666666" text-anchor="middle">sin credenciales en el repo</text>
        <polyline points="768,150 750,150 750,232 732,232" fill="none" stroke="#8A9099" stroke-width="1.5" stroke-dasharray="4 3" marker-end="url(#flechaInfGris)"></polyline>
        <text x="762" y="212" font-size="10" fill="#666666">deploy.sh · git pull</text>
      </svg>
    </div>
    <figcaption><b>Todo en una instancia.</b> Dimensionada para la escala real de una Casa: decenas de familias y algunos cientos de escaneos al día. Si Tecnología prefiere su estándar corporativo (contenedores, base gestionada, balanceador), el sistema es un monolito PHP+MySQL convencional y migra sin cambios de código.</figcaption>
  </figure>

  <h3>Componentes y versiones</h3>
  <div class="tabla-scroll"><table>
    <tr><th>Capa</th><th>Componente</th><th>Configuración relevante</th></tr>
    <tr><td>Sistema</td><td>Amazon Linux 2023, t3.micro</td><td>Swap de 2 GB; <code>dnf-automatic.timer</code> aplica parches de seguridad; grupo de seguridad abierto solo en 22, 80 y 443</td></tr>
    <tr><td>Web</td><td>nginx</td><td>HTTP/2, gzip, redirección 80→443, caché de estáticos (30 d librerías vendorizadas, 1 h CSS/JS propios, 7 d fotos), cabeceras de seguridad</td></tr>
    <tr><td>Aplicación</td><td>PHP-FPM 8.5</td><td><code>pm = dynamic</code>, máximo 8 procesos, OPcache 128 MB con revalidación cada 60 s, <code>X-Powered-By</code> oculto</td></tr>
    <tr><td>Datos</td><td>MariaDB 11.4 LTS</td><td>Conexión por socket local — no escucha en ningún puerto de red; <code>innodb_buffer_pool_size = 128M</code>, bitácora de consultas lentas (&gt; 2 s)</td></tr>
    <tr><td>TLS</td><td>Certificado autofirmado</td><td>HTTPS obligatorio (la cámara del escáner lo exige). Con dominio: <code>certbot --nginx</code> y HSTS, ya previsto en la configuración</td></tr>
    <tr><td>Respaldos</td><td><code>casaronald-backup.timer</code></td><td>03:15 diario: volcado de base + <code>storage/app</code> + <code>.env</code>, comprimidos, 14 días de retención</td></tr>
    <tr><td>Despliegue</td><td><code>deploy.sh</code></td><td>Modo mantenimiento → <code>git pull --ff-only</code> → <code>composer install --no-dev</code> → migraciones → <code>artisan optimize</code> → recarga de PHP-FPM</td></tr>
  </table></div>

  <h3>Operación diaria</h3>
  <pre class="consola"><span class="cmt"># publicar una versión nueva (previamente empujada a main)</span>
ssh ec2-user@SERVIDOR 'sudo bash /var/www/casaronald/deploy/deploy.sh'

<span class="cmt"># cualquier comando artisan, con el usuario correcto</span>
sudo casaronald-artisan migrate:status
sudo casaronald-artisan down / up          <span class="cmt"># modo mantenimiento</span>

<span class="cmt"># respaldo manual y estado de los servicios</span>
sudo casaronald-backup
sudo systemctl status nginx php-fpm mariadb</pre>

  <div class="nota-azul">
    <p><strong>Sobre el certificado autofirmado.</strong> Sin dominio, el navegador muestra un aviso la primera vez. Es aceptable para la demostración y para el piloto interno, pero no para operación con datos reales: en cuanto la Fundación asigne un subdominio, el certificado válido y HSTS se activan con dos comandos ya documentados.</p>
  </div>
</article>

<article>
  <h2>5 · Casos de uso</h2>

  <h3>Actores</h3>
  <div class="tabla-scroll"><table>
    <tr><th>Rol</th><th>Quién es</th><th>Alcance</th></tr>
    <tr><td><code>master</code> · Administrador</td><td>Dirección de la Casa</td><td>Todo, más la administración: crear cuentas, activarlas o desactivarlas, restablecer contraseñas, ver los reportes de uso y la bitácora de actividad</td></tr>
    <tr><td><code>staff</code> · Personal operativo</td><td>Recepción, comedor, voluntariado</td><td>Expedientes, credenciales, concentrados y la estación de escaneo</td></tr>
    <tr><td><code>trabajador_social</code> · Trabajo social</td><td>Trabajo social de la Casa y del hospital</td><td>Expedientes y descarga del reporte PDF; <em>no</em> opera la estación de escaneo, que es tarea del personal operativo</td></tr>
  </table></div>

  <h3>Matriz de permisos</h3>
  <div class="tabla-scroll"><table>
    <tr><th>Capacidad</th><th>Mecanismo</th><th>Administrador</th><th>Personal</th><th>Trabajo social</th></tr>
    <tr><td>Alta y edición de expedientes y acompañantes</td><td><code>auth</code> + <code>active</code></td><td class="si">Sí</td><td class="si">Sí</td><td class="si">Sí</td></tr>
    <tr><td>Consulta de expedientes y concentrados</td><td><code>auth</code> + <code>active</code></td><td class="si">Sí</td><td class="si">Sí</td><td class="si">Sí</td></tr>
    <tr><td>Emisión del carnet con QR</td><td><code>auth</code> + <code>active</code></td><td class="si">Sí</td><td class="si">Sí</td><td class="si">Sí</td></tr>
    <tr><td>Registro por escaneo</td><td><code>can:escanear</code></td><td class="si">Sí</td><td class="si">Sí</td><td class="no">No</td></tr>
    <tr><td>Descarga del expediente en PDF</td><td><code>can:descargar-expediente</code></td><td class="si">Sí</td><td class="no">No</td><td class="si">Sí</td></tr>
    <tr><td>Administración de cuentas, reportes y auditoría</td><td><code>master</code></td><td class="si">Sí</td><td class="no">No</td><td class="no">No</td></tr>
  </table></div>
  <p>Una cuenta desactivada pierde el acceso en su siguiente petición aunque tenga la sesión abierta: el middleware <code>active</code> la cierra y la devuelve al login con el motivo. Las cuentas nunca se borran, para no romper la trazabilidad de la bitácora.</p>

  <h3>Catálogo de casos de uso</h3>
  <div class="tabla-scroll"><table>
    <tr><th>ID</th><th>Caso de uso</th><th>Actor</th><th>Punto de entrada</th><th>Resultado</th></tr>
    <tr><td>CU-01</td><td>Iniciar sesión</td><td>Todos</td><td><code>POST /login</code> · <code>throttle:10,1</code></td><td>Sesión regenerada; anota <code>inicio_sesion</code></td></tr>
    <tr><td>CU-02</td><td>Alta de expediente del menor</td><td>Todos</td><td><code>POST /ninos</code></td><td>Expediente + QR único + foto + tratamientos; anota <code>alta</code></td></tr>
    <tr><td>CU-03</td><td>Edición de expediente</td><td>Todos</td><td><code>PUT /ninos/{nino}</code></td><td>Mismas reglas que el alta; el QR nunca cambia; anota <code>edicion</code></td></tr>
    <tr><td>CU-04</td><td>Alta de acompañante</td><td>Todos</td><td><code>POST /acompanantes</code></td><td>Acompañante ligado al menor, con estudio socioeconómico</td></tr>
    <tr><td>CU-05</td><td>Consulta del expediente</td><td>Todos</td><td><code>GET /ninos/{nino}</code> · <code>/ninos/{nino}/perfil</code></td><td>Ficha completa en ventana o página, con su bitácora</td></tr>
    <tr><td>CU-06</td><td>Emisión del carnet con QR</td><td>Todos</td><td><code>GET /fichas/{nino}</code></td><td>JPG 1275×1650 o PDF carta; se regenera si el expediente cambió</td></tr>
    <tr><td>CU-07</td><td>Reporte PDF del expediente</td><td>Admin · Trabajo social</td><td><code>GET /expedientes/{nino}/pdf</code></td><td>PDF con foto y QR incrustados; la descarga anota <code>descarga</code></td></tr>
    <tr><td>CU-08</td><td>Registro de servicio por escaneo</td><td>Admin · Personal</td><td><code>POST /escanear/registrar</code></td><td>Renglón en <code>registros_servicios</code>; responde con dieta y alergias</td></tr>
    <tr><td>CU-09</td><td>Registro de entrada o salida</td><td>Admin · Personal</td><td><code>POST /escanear/registrar</code></td><td>Abre o cierra el renglón de <code>entradas_salidas</code></td></tr>
    <tr><td>CU-10</td><td>Resolución de credencial por QR</td><td>Todos</td><td><code>GET /ninos/por-qr/{qr}</code></td><td>Menor y acompañantes; 404 si el código no existe</td></tr>
    <tr><td>CU-11</td><td>Concentrados por servicio</td><td>Todos</td><td><code>/registros-*</code> + <code>/datatables/*</code></td><td>Tablas paginadas, ordenables y filtrables por servicio</td></tr>
    <tr><td>CU-12</td><td>Reporte de uso por periodo</td><td>Administrador</td><td><code>GET /administracion/reportes/datos</code></td><td>Serie por servicio y cubeta (hora, día o mes) según el bloque elegido</td></tr>
    <tr><td>CU-13</td><td>Descarga de la bitácora en Excel</td><td>Administrador</td><td><code>GET /administracion/reportes/excel</code></td><td><code>.xlsx</code> del periodo; anota <code>descarga</code></td></tr>
    <tr><td>CU-14</td><td>Alta de cuenta del personal</td><td>Administrador</td><td><code>POST /administracion</code></td><td>Cuenta con rol; contraseña mínima validada</td></tr>
    <tr><td>CU-15</td><td>Activar, desactivar o restablecer contraseña</td><td>Administrador</td><td><code>PATCH /administracion/{usuario}/…</code></td><td>La cuenta maestra no puede desactivarse a sí misma</td></tr>
    <tr><td>CU-16</td><td>Consulta de la bitácora de actividad</td><td>Administrador</td><td><code>GET /administracion/actividad</code></td><td>Quién hizo qué y cuándo, filtrable por cuenta</td></tr>
    <tr><td>CU-17</td><td>Perfil de cuenta</td><td>Todos</td><td><code>GET /perfil/{usuario?}</code></td><td>Cada quien el suyo; el administrador, el de cualquiera</td></tr>
  </table></div>
</article>

<article>
  <h2>6 · Flujos principales</h2>

  <h3>De la solicitud al carnet</h3>
  <figure>
    <div class="marco">
      <svg viewBox="0 0 940 240" role="img" aria-label="Flujo de alta: captura del expediente, validación en servidor, generación del código QR único, persistencia, anotación en la bitácora y composición del carnet imprimible.">
        <defs>
          <marker id="flechaAlta" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="8" markerHeight="8" orient="auto-start-reverse">
            <path d="M 0 0 L 10 5 L 0 10 z" fill="#38598A"></path>
          </marker>
        </defs>
        <rect x="12" y="70" width="150" height="76" rx="8" fill="#ffffff" stroke="#C9D4E6"></rect>
        <text x="87" y="96" font-size="11.5" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Captura</text>
        <text x="87" y="114" font-size="10.5" fill="#666666" text-anchor="middle">formulario del</text>
        <text x="87" y="129" font-size="10.5" fill="#666666" text-anchor="middle">expediente + foto</text>

        <rect x="198" y="70" width="150" height="76" rx="8" fill="#EFF3FA" stroke="#4872AE"></rect>
        <text x="273" y="96" font-size="11.5" font-weight="700" fill="#38598A" text-anchor="middle" font-family="Raleway, sans-serif">Validación</text>
        <text x="273" y="114" font-size="10.5" fill="#33383F" text-anchor="middle" font-family="IBM Plex Mono, monospace">StoreNinoRequest</text>
        <text x="273" y="129" font-size="10.5" fill="#666666" text-anchor="middle">en servidor</text>

        <rect x="384" y="70" width="164" height="76" rx="8" fill="#FFF9E8" stroke="#FFC72C"></rect>
        <text x="466" y="96" font-size="11.5" font-weight="700" fill="#5c4500" text-anchor="middle" font-family="Raleway, sans-serif">Código QR</text>
        <text x="466" y="114" font-size="10.5" fill="#33383F" text-anchor="middle">3 letras + 3 dígitos</text>
        <text x="466" y="129" font-size="10.5" fill="#666666" text-anchor="middle">CSPRNG · unicidad verificada</text>

        <rect x="584" y="70" width="150" height="76" rx="8" fill="#ffffff" stroke="#C9D4E6"></rect>
        <text x="659" y="96" font-size="11.5" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Persistencia</text>
        <text x="659" y="114" font-size="10.5" fill="#666666" text-anchor="middle">expediente, tratamientos</text>
        <text x="659" y="129" font-size="10.5" fill="#666666" text-anchor="middle">y foto normalizada</text>

        <rect x="770" y="70" width="156" height="76" rx="8" fill="#EFF3FA" stroke="#4872AE"></rect>
        <text x="848" y="96" font-size="11.5" font-weight="700" fill="#38598A" text-anchor="middle" font-family="Raleway, sans-serif">Carnet</text>
        <text x="848" y="114" font-size="10.5" fill="#33383F" text-anchor="middle" font-family="IBM Plex Mono, monospace">FichaGenerator</text>
        <text x="848" y="129" font-size="10.5" fill="#666666" text-anchor="middle">JPG o PDF carta</text>

        <line x1="166" y1="108" x2="194" y2="108" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaAlta)"></line>
        <line x1="352" y1="108" x2="380" y2="108" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaAlta)"></line>
        <line x1="552" y1="108" x2="580" y2="108" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaAlta)"></line>
        <line x1="738" y1="108" x2="766" y2="108" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaAlta)"></line>

        <text x="273" y="56" font-size="10" fill="#8C2622" text-anchor="middle">falla → 422 con mensajes</text>
        <text x="466" y="56" font-size="10" fill="#8C2622" text-anchor="middle">colisión → un reintento</text>
        <text x="659" y="176" font-size="10" fill="#8C2622" text-anchor="middle">anota «alta» en la auditoría</text>
        <line x1="659" y1="150" x2="659" y2="164" stroke="#F64740" stroke-width="1.5" stroke-dasharray="3 3"></line>
        <text x="848" y="176" font-size="10" fill="#666666" text-anchor="middle">disco privado, servido con sesión</text>
        <line x1="848" y1="150" x2="848" y2="164" stroke="#8A9099" stroke-width="1.5" stroke-dasharray="3 3"></line>

        <text x="470" y="216" font-size="10.5" fill="#666666" text-anchor="middle">El carnet se regenera solo cuando el expediente cambia después de la última generación, o cuando el diseño cambió de tamaño.</text>
      </svg>
    </div>
    <figcaption><b>CU-02 y CU-06 encadenados.</b> El código QR se genera una sola vez y acompaña al expediente toda su vida; el carnet es un artefacto derivado y desechable, siempre reconstruible desde los datos.</figcaption>
  </figure>

  <h3>Un escaneo, paso a paso</h3>
  <figure>
    <div class="marco">
      <svg viewBox="0 0 940 330" role="img" aria-label="Flujo del escaneo: captura del código por cámara o teclado, envío autenticado, validación, búsqueda del expediente y bifurcación en servicio, entrada o salida, con las reglas de rechazo.">
        <defs>
          <marker id="flechaEsc" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="8" markerHeight="8" orient="auto-start-reverse">
            <path d="M 0 0 L 10 5 L 0 10 z" fill="#38598A"></path>
          </marker>
          <marker id="flechaEscRojo" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="8" markerHeight="8" orient="auto-start-reverse">
            <path d="M 0 0 L 10 5 L 0 10 z" fill="#F64740"></path>
          </marker>
        </defs>

        <rect x="12" y="112" width="150" height="80" rx="8" fill="#ffffff" stroke="#C9D4E6"></rect>
        <text x="87" y="138" font-size="11.5" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Captura</text>
        <text x="87" y="156" font-size="10.5" fill="#666666" text-anchor="middle">cámara del teléfono,</text>
        <text x="87" y="171" font-size="10.5" fill="#666666" text-anchor="middle">lector USB o tecleado</text>

        <rect x="196" y="112" width="164" height="80" rx="8" fill="#EFF3FA" stroke="#4872AE"></rect>
        <text x="278" y="138" font-size="11.5" font-weight="700" fill="#38598A" text-anchor="middle" font-family="Raleway, sans-serif">POST autenticado</text>
        <text x="278" y="156" font-size="10.5" fill="#33383F" text-anchor="middle" font-family="IBM Plex Mono, monospace">/escanear/registrar</text>
        <text x="278" y="171" font-size="10.5" fill="#666666" text-anchor="middle">CSRF + gate «escanear»</text>

        <rect x="394" y="112" width="164" height="80" rx="8" fill="#ffffff" stroke="#C9D4E6"></rect>
        <text x="476" y="138" font-size="11.5" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Resolución</text>
        <text x="476" y="156" font-size="10.5" fill="#666666" text-anchor="middle">busca el expediente</text>
        <text x="476" y="171" font-size="10.5" fill="#666666" text-anchor="middle">por su código</text>

        <line x1="166" y1="152" x2="192" y2="152" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaEsc)"></line>
        <line x1="364" y1="152" x2="390" y2="152" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaEsc)"></line>

        <text x="278" y="98" font-size="10" fill="#8C2622" text-anchor="middle">trabajo social → 403</text>
        <text x="476" y="98" font-size="10" fill="#8C2622" text-anchor="middle">código inexistente → 404</text>

        <!-- ramas -->
        <rect x="600" y="26" width="200" height="66" rx="8" fill="#EFF3FA" stroke="#4872AE"></rect>
        <text x="700" y="50" font-size="11.5" font-weight="700" fill="#38598A" text-anchor="middle" font-family="Raleway, sans-serif">Servicio</text>
        <text x="700" y="68" font-size="10.5" fill="#666666" text-anchor="middle">comedor · lavandería</text>
        <text x="700" y="83" font-size="10.5" fill="#666666" text-anchor="middle">escuelita · transporte</text>

        <rect x="600" y="118" width="200" height="66" rx="8" fill="#EFF3FA" stroke="#4872AE"></rect>
        <text x="700" y="142" font-size="11.5" font-weight="700" fill="#38598A" text-anchor="middle" font-family="Raleway, sans-serif">Entrada</text>
        <text x="700" y="160" font-size="10.5" fill="#666666" text-anchor="middle">abre el renglón</text>
        <text x="700" y="175" font-size="10.5" fill="#8C2622" text-anchor="middle">ya abierta → 422</text>

        <rect x="600" y="210" width="200" height="66" rx="8" fill="#EFF3FA" stroke="#4872AE"></rect>
        <text x="700" y="234" font-size="11.5" font-weight="700" fill="#38598A" text-anchor="middle" font-family="Raleway, sans-serif">Salida</text>
        <text x="700" y="252" font-size="10.5" fill="#666666" text-anchor="middle">cierra el renglón abierto</text>
        <text x="700" y="267" font-size="10.5" fill="#8C2622" text-anchor="middle">sin entrada previa → 422</text>

        <polyline points="562,152 580,152 580,59 596,59" fill="none" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaEsc)"></polyline>
        <line x1="562" y1="152" x2="596" y2="152" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaEsc)"></line>
        <polyline points="562,152 580,152 580,243 596,243" fill="none" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaEsc)"></polyline>

        <!-- respuesta -->
        <rect x="820" y="118" width="108" height="66" rx="8" fill="#ffffff" stroke="#C9D4E6"></rect>
        <text x="874" y="142" font-size="11.5" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">201</text>
        <text x="874" y="160" font-size="10.5" fill="#666666" text-anchor="middle">nombre, edad,</text>
        <text x="874" y="175" font-size="10.5" fill="#666666" text-anchor="middle">dieta y alergias</text>
        <line x1="804" y1="151" x2="816" y2="151" stroke="#38598A" stroke-width="1.5" marker-end="url(#flechaEsc)"></line>

        <text x="874" y="206" font-size="10" fill="#8C2622" text-anchor="middle">anota «escaneo»</text>
        <line x1="874" y1="188" x2="874" y2="196" stroke="#F64740" stroke-width="1.5" stroke-dasharray="3 3"></line>

        <text x="470" y="310" font-size="10.5" fill="#666666" text-anchor="middle">La respuesta lleva la dieta y las alergias a propósito: quien sirve la comida las ve en la misma pantalla donde acaba de escanear.</text>
      </svg>
    </div>
    <figcaption><b>CU-08 y CU-09.</b> Un solo endpoint atiende las seis acciones. Las reglas de entrada y salida son de negocio, no de interfaz: viven en el controlador y están cubiertas por pruebas, así que el mismo comportamiento aplica venga el escaneo de donde venga.</figcaption>
  </figure>

  <h3>Del escaneo al reporte</h3>
  <p>Todo escaneo cae en <code>registros_servicios</code> con su marca de tiempo. El reporte de la página de administración agrupa esos renglones por servicio y por cubeta de tiempo, y la cubeta se ajusta sola al bloque elegido: un día se desglosa por hora, una semana o un mes por día, un año por mes. Los periodos sin actividad aparecen en cero — un hueco en la gráfica es información, no ausencia de datos. La misma consulta alimenta la descarga en Excel, generada sin dependencias externas (un <code>.xlsx</code> es un ZIP con XML dentro, y la extensión <code>zip</code> ya viene con PHP).</p>
</article>

<article>
  <h2>7 · Seguridad, auditoría y calidad</h2>

  <h3>Controles implementados</h3>
  <ul>
    <li><strong>Sesión obligatoria</strong> en toda la aplicación salvo <code>/login</code> y las páginas públicas de esta propuesta, que no contienen datos de familias.</li>
    <li><strong>Autorización por capas</strong>: middleware para lo estructural (<code>auth</code>, <code>active</code>, <code>master</code>) y <em>gates</em> para lo fino (<code>escanear</code>, <code>descargar-expediente</code>, <code>manage-users</code>).</li>
    <li><strong>Contraseñas</strong> con bcrypt de 12 rondas; la contraseña maestra se define en el <code>.env</code> del servidor, nunca en el repositorio.</li>
    <li><strong>Limitación de intentos</strong> en el login: 10 por minuto y por IP.</li>
    <li><strong>Bitácora de auditoría</strong>: inicios de sesión, escaneos, descargas, altas y ediciones quedan anotados con cuenta, momento y detalle. Solo se inserta; no existe camino para editarla desde la aplicación.</li>
    <li><strong>Archivos sensibles fuera del disco público</strong>: carnets y PDF se generan al pedirlos y se sirven con sesión; las fotos de expediente viven bajo el <em>symlink</em> público con nombre aleatorio de 40 caracteres.</li>
    <li><strong>Sin salidas a internet</strong> en tiempo de ejecución: el QR se genera en memoria y las librerías del navegador (jQuery, Bootstrap, DataTables, Chart.js) están vendorizadas en el propio servidor.</li>
  </ul>

  <h3>Lo que falta para operar con datos reales</h3>
  <div class="honesto">
    <p>El endurecimiento está hecho, pero estas piezas dependen de decisiones de la Fundación y no del código: <strong>certificado TLS válido</strong> sobre un dominio propio; <strong>respaldos fuera de la instancia</strong> (hoy conviven con la aplicación en el mismo disco); <strong>aviso de privacidad y consentimiento</strong> en el registro, con el procedimiento de derechos ARCO conforme a la LFPDPPP; y la <strong>política de retención y borrado</strong> al egreso de cada familia. El sistema captura únicamente lo que el expediente en papel ya recaba, pero el marco legal es de la organización, no de la herramienta.</p>
  </div>

  <h3>Calidad del código</h3>
  <div class="rejilla">
    <div class="dato"><b>88</b><span>pruebas automatizadas</span></div>
    <div class="dato"><b>434</b><span>aserciones</span></div>
    <div class="dato"><b>21</b><span>fábricas de datos de prueba</span></div>
    <div class="dato"><b>~20 s</b><span>corrida completa de la suite</span></div>
  </div>
  <p>Las pruebas corren sobre sqlite en memoria y cubren los flujos que importan: autenticación y autorización por rol, alta y edición de expedientes, las reglas de entrada y salida, la unicidad del QR, la generación del carnet y del PDF, los feeds de las tablas, los reportes y la administración de cuentas. El estilo se verifica con <code>pint</code>. Todo el código y la documentación se entregan bajo <strong>licencia MIT</strong> a nombre de la Fundación.</p>

  <h3>Cómo verlo funcionando</h3>
  <p>La instancia de demostración está en línea con datos ficticios — la familia de prueba es, cómo no, la de <em>Ronaldo Macías Zepeda</em>, con el código <span class="qr">R2M4Z6</span>. Ningún dato de familias reales ha pasado nunca por el sistema. Con gusto damos acceso, hacemos un recorrido en vivo o abrimos el repositorio para revisión de seguridad (análisis estático, pentest, cuestionarios de proveedor) antes de cualquier piloto.</p>
</article>

<div class="volver"><a href="{{ route('casita-secreta') }}">← Volver al índice de documentos</a></div>

<footer>
  <p>Documentación preparada por: [Nombre del presentador] · ex-voluntario de la Casa Ronald McDonald · Documento independiente, no es un documento oficial de la Fundación.</p>
</footer>

</div>

</body>
</html>
