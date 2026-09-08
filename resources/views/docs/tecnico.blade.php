<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Casita Digital | Documento técnico</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Raleway:wght@600;700;800&family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400&family=IBM+Plex+Mono:wght@400;500&display=swap">
<style>
  :root {
    --azul: #4872AE;
    --azul-oscuro: #38598A;
    --amarillo: #FFC72C;
    --amarillo-tinta: #7a5c00;
    --rojo: #F64740;
    --texto: #33383F;
    --gris: #666666;
    --crema: #FBFAF6;
    --borde: #E8E6DF;
    --codigo: #F1EFE8;
  }
  * { box-sizing: border-box; }
  body { margin: 0; background: var(--azul); font-family: "Open Sans", "Segoe UI", sans-serif; color: var(--texto); line-height: 1.62; }
  .lienzo { max-width: 960px; margin: 0 auto; padding: 2.5rem 1.2rem 4rem; }

  header.portada {
    background: rgba(255,255,255,0.97); border-radius: 14px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.25); padding: 3rem 3rem 2.5rem; margin-bottom: 1.6rem;
  }
  img.logo { max-width: 200px; height: auto; margin-bottom: 1.4rem; }
  .eyebrow { font-family: "Raleway", sans-serif; font-weight: 700; font-size: .78rem; letter-spacing: .14em; text-transform: uppercase; color: var(--azul); margin: 0 0 .6rem; }
  h1 { font-family: "Raleway", sans-serif; font-weight: 800; font-size: 2.4rem; line-height: 1.1; margin: 0 0 .8rem; }
  h1 .amarillo { color: var(--amarillo); }
  .bajada { font-size: 1.1rem; color: var(--gris); max-width: 64ch; margin: 0 0 1.2rem; }
  .chips { display: flex; flex-wrap: wrap; gap: .5rem; }
  .chip { font-family: "IBM Plex Mono", monospace; font-size: .76rem; padding: .25rem .7rem; border: 1px solid var(--borde); border-radius: 999px; background: var(--crema); color: var(--gris); white-space: nowrap; }

  article {
    background: rgba(255,255,255,0.97); border-radius: 14px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.18); padding: 2.4rem 3rem; margin-bottom: 1.6rem;
  }
  h2 { font-family: "Raleway", sans-serif; font-weight: 800; font-size: 1.55rem; margin: 0 0 1rem; color: var(--azul-oscuro); }
  h2::after { content: ""; display: block; width: 56px; height: 4px; background: var(--rojo); border-radius: 2px; margin-top: .5rem; }
  h3 { font-family: "Raleway", sans-serif; font-weight: 700; font-size: 1.12rem; margin: 1.6rem 0 .5rem; }
  p { margin: 0 0 1rem; max-width: 74ch; }
  ul, ol { margin: 0 0 1rem; padding-left: 1.3rem; max-width: 72ch; }
  li { margin-bottom: .45rem; }
  li::marker { color: var(--azul); }
  code { font-family: "IBM Plex Mono", monospace; font-size: .86em; background: var(--codigo); padding: .1em .4em; border-radius: 4px; }
  .qr { font-family: "IBM Plex Mono", monospace; font-weight: 500; letter-spacing: .06em; background: #FFF3D1; color: var(--amarillo-tinta); padding: .05em .45em; border-radius: 4px; }

  .tabla-scroll { overflow-x: auto; border: 1px solid var(--borde); border-radius: 8px; margin-bottom: 1.2rem; background: #fff; }
  table { border-collapse: collapse; width: 100%; font-size: .9rem; }
  th, td { text-align: left; padding: .55rem .85rem; border-bottom: 1px solid var(--borde); vertical-align: top; }
  th { font-family: "Raleway", sans-serif; font-size: .76rem; text-transform: uppercase; letter-spacing: .07em; color: var(--gris); background: var(--crema); white-space: nowrap; }
  tr:last-child td { border-bottom: none; }
  td code { white-space: nowrap; }

  .diagrama {
    background: var(--codigo); border: 1px solid var(--borde); border-radius: 8px;
    padding: 1.1rem 1.3rem; overflow-x: auto; font-family: "IBM Plex Mono", monospace;
    font-size: .8rem; line-height: 1.5; margin-bottom: 1.2rem; white-space: pre;
  }

  .honesto { border-left: 4px solid var(--rojo); background: #FDEEED; border-radius: 0 8px 8px 0; padding: 1rem 1.3rem; margin: 1rem 0 1.4rem; }
  .honesto p { margin: 0 0 .5rem; max-width: none; }
  .honesto p:last-child { margin-bottom: 0; }
  .nota-azul { border-left: 4px solid var(--azul); background: #EFF3FA; border-radius: 0 8px 8px 0; padding: 1rem 1.3rem; margin: 1rem 0; }
  .nota-azul p { margin: 0; max-width: none; }

  .fase { display: flex; gap: 1.1rem; margin-bottom: 1.3rem; align-items: flex-start; }
  .fase .num {
    flex-shrink: 0; width: 2.4rem; height: 2.4rem; border-radius: 50%;
    background: var(--azul); color: #fff; font-family: "Raleway", sans-serif;
    font-weight: 800; font-size: 1.05rem; display: flex; align-items: center; justify-content: center;
  }
  .fase h3 { margin: .15rem 0 .35rem; }
  .fase p, .fase ul { margin-bottom: .4rem; }

  footer { text-align: center; color: rgba(255,255,255,0.85); font-size: .85rem; padding: 1rem 0 0; }
  footer a { color: var(--amarillo); }
  .volver { text-align: center; margin-top: 1rem; }
  .volver a { color: #fff; text-decoration: none; font-family: "Raleway", sans-serif; font-weight: 700; border: 2px solid rgba(255,255,255,0.6); border-radius: 999px; padding: .5rem 1.4rem; display: inline-block; }
  .volver a:hover { background: rgba(255,255,255,0.12); }
  @media (max-width: 640px) { article, header.portada { padding: 1.8rem 1.4rem; } h1 { font-size: 1.9rem; } }
</style>
</head>
<body>
<div class="lienzo">

<header class="portada">
  <img class="logo" src="{{ asset('img/RMHC_Mexico_logo.png') }}" alt="Fundación Infantil Ronald McDonald México">
  <p class="eyebrow">Propuesta de donación tecnológica · Documento para la Dirección de Tecnología</p>
  <h1>Casita <span class="amarillo">Digital</span> — diseño del sistema</h1>
  <p class="bajada">Estado actual, arquitectura, modelo de datos y plan de endurecimiento de un sistema de gestión de huéspedes y servicios construido a la medida de la operación de la Casa Ronald McDonald. Este documento asume la lectura del documento de proyecto y entra en la profundidad que la revisión técnica requiere.</p>
  <div class="chips">
    <span class="chip">Laravel · PHP</span>
    <span class="chip">MySQL</span>
    <span class="chip">Blade + jQuery · server-rendered</span>
    <span class="chip">22 tablas · 293 filas de catálogo</span>
    <span class="chip">Licencia MIT</span>
    <span class="chip">Estado: prototipo funcional</span>
  </div>
</header>

<article>
  <h2>1 · Resumen ejecutivo</h2>
  <p><strong>Qué es.</strong> Un monolito web que digitaliza el ciclo operativo de una Casa: expediente del menor y sus acompañantes (con el estudio socioeconómico que Trabajo Social ya levanta), credencial impresa con código QR de 6 caracteres (p. ej. <span class="qr">A1B2C3</span>), registro de servicios por escaneo (comedor, lavandería, escuelita, transporte, entradas/salidas) y concentrados por servicio para reportes.</p>
  <p><strong>Qué se ofrece.</strong> Donación del sistema completo bajo licencia MIT a nombre de la Fundación, con puesta en marcha, capacitación y acompañamiento. Sin costo, sin lock-in: el repositorio, la documentación y los datos quedan en manos de la organización.</p>
  <p><strong>Qué se pide de Tecnología.</strong> Revisión de esta propuesta, definición de requisitos corporativos (hosting, seguridad, cumplimiento) y un entorno de prueba para el piloto. El plan de la sección 6 condiciona explícitamente cualquier operación con datos reales a completar la fase de endurecimiento.</p>
</article>

<article>
  <h2>2 · Alcance funcional (mapeo proceso ↔ módulo)</h2>
  <p>La cobertura funcional fue definida desde la operación observada como voluntario, y coincide con el proceso público de admisión de la Fundación: la familia llega canalizada por Trabajo Social del hospital con solicitud, valoración médica y estudio socioeconómico.</p>
  <div class="tabla-scroll"><table>
    <tr><th>Proceso real de la Casa</th><th>Módulo del sistema</th><th>Datos específicos</th></tr>
    <tr><td>Solicitud de hospedaje vía Trabajo Social</td><td>Expediente del niño (<code>/familias</code>)</td><td>Identidad, fecha de nacimiento, sexo, hospital, diagnóstico, médico, alergias, dieta, estatus de estancia (primera vez / prórroga / subsecuente), fechas de solicitud/ingreso/salida</td></tr>
    <tr><td>Estudio socioeconómico</td><td>Mismo expediente + acompañantes</td><td>Escolaridad, ocupación, ingreso mensual, dependientes, vivienda propia, seguro médico, apoyo financiero, zona (rural/suburbana/urbana), clasificación social, salario mínimo, dialecto/lengua</td></tr>
    <tr><td>Familias de todo el país (32 estados)</td><td>Catálogos de geografía</td><td><code>pais → estado → municipio</code>; precargados 3 países, 32 estados, 81 municipios</td></tr>
    <tr><td>Red de hospitales de Puebla</td><td>Catálogo <code>hospital</code></td><td>17 hospitales precargados: Hospital para el Niño Poblano, CRIT, IMSS, Cruz Roja Puebla, etc.</td></tr>
    <tr><td>Credencial de la familia</td><td>Generador de ficha (<code>/get-ficha/{id}</code>)</td><td>Carnet JPG 1072×830 con identidad de la Fundación, datos del menor, acompañantes y QR</td></tr>
    <tr><td>Comedor (3 comidas, dietas)</td><td rowspan="2">Registro de servicios por QR</td><td rowspan="2">Bitácora <code>registros_servicios</code> (servicio + timestamp); catálogo de 7 dietas visibles en el expediente</td></tr>
    <tr><td>Lavandería · Escuelita · Transporte</td></tr>
    <tr><td>Control de acceso al albergue</td><td>Entradas y salidas por QR</td><td>Bitácora <code>entradas_salidas</code> con apertura/cierre de estancia diaria</td></tr>
    <tr><td>Reportes a patronato y donantes</td><td>Concentrados por servicio</td><td>Grids con historial por servicio; base para exportables (fase 2)</td></tr>
  </table></div>
  <p><strong>Fuera de alcance actual</strong> (previsto como evolución): módulo de habitaciones/ocupación, registro operativo de estancia completo, reportes exportables, multi-Casa y Salas Familiares.</p>
</article>

<article>
  <h2>3 · Arquitectura</h2>
  <div class="diagrama">┌─────────────────────────┐        ┌──────────────────────────────┐
│  Estación de registro   │        │  Puntos de servicio          │
│  (navegador)            │        │  (lector QR / teléfono)      │
│  · expedientes          │        │  · comedor · lavandería      │
│  · credenciales         │        │  · escuelita · transporte    │
│  · concentrados         │        │  · entradas/salidas          │
└───────────┬─────────────┘        └───────────────┬──────────────┘
            │ Blade + AJAX (DataTables)            │ JSON (contrato de escaneo)
            ▼                                      ▼
┌──────────────────────────────────────────────────────────────────┐
│                    Monolito Laravel (PHP)                        │
│  Controladores API ── Modelos Eloquent ── Generador de fichas    │
│                                           (Intervention Image)  │
└───────────────────────────────┬──────────────────────────────────┘
                                ▼
                        ┌──────────────┐
                        │    MySQL     │  22 tablas · FKs RESTRICT
                        └──────────────┘</div>
  <ul>
    <li><strong>Monolito server-rendered</strong>: Laravel + Blade + jQuery/DataTables. Deliberadamente simple de operar y de auditar; sin microservicios ni dependencias de infraestructura exóticas.</li>
    <li><strong>Contrato de escaneo</strong>: los puntos de servicio consumen endpoints JSON; cualquier cliente con cámara/lector (PWA, app, pistola USB) puede integrarse sin tocar el núcleo.</li>
    <li><strong>Generación de credenciales</strong>: pipeline de composición de imagen (plantillas + tipografía Raleway + QR) del lado del servidor; salida JPG lista para imprimir.</li>
  </ul>
  <h3>Contrato de API (superficie actual)</h3>
  <div class="tabla-scroll"><table>
    <tr><th>Endpoint</th><th>Función</th></tr>
    <tr><td><code>GET /get-nino-by-qr?qr=</code></td><td>Resolución de credencial: menor + acompañantes (payload mínimo para el punto de servicio)</td></tr>
    <tr><td><code>POST /add-nino</code> · <code>POST /add-acompanante</code></td><td>Alta de expediente y acompañantes desde el formulario de Trabajo Social</td></tr>
    <tr><td><code>/add-registro-servicio?qr=&amp;servicio=</code></td><td>Bitácora de comedor / lavandería / escuelita / transporte</td></tr>
    <tr><td><code>/add-entrada-salida?qr=&amp;tipo_registro=</code></td><td>Apertura/cierre de entrada al albergue</td></tr>
    <tr><td><code>GET /get-ficha/{id}</code></td><td>Genera la credencial JPG y devuelve su URL</td></tr>
    <tr><td><code>GET /get-*-datatable</code></td><td>Feeds de los concentrados por servicio</td></tr>
  </table></div>
  <p class="nota-azul" style="margin-top:0"><strong>Nota de diseño:</strong> en el plan de endurecimiento, todos los endpoints que mutan estado migran a <code>POST</code> autenticado (hoy varios operan por <code>GET</code>, herencia del prototipo), y la resolución por QR pasa a estar limitada por sesión de punto de servicio.</p>
</article>

<article>
  <h2>4 · Modelo de datos</h2>
  <p>22 tablas en MySQL: 3 entidades núcleo, 2 bitácoras, 15 catálogos y la cadena geográfica. Convenciones uniformes (PK <code>BIGINT id</code>, timestamps en todas las tablas, FKs con <code>RESTRICT</code>).</p>
  <div class="tabla-scroll"><table>
    <tr><th>Grupo</th><th>Tablas</th><th>Detalle</th></tr>
    <tr><td><strong>Núcleo</strong></td><td><code>nino</code> · <code>acompanante</code> · <code>registro_operativo</code></td><td><code>nino</code> (39 columnas, 10 FKs a catálogos) es el agregado central; <code>acompanante</code> (25 columnas) cuelga de él con el bloque socioeconómico; <code>registro_operativo</code> modela la estancia (habitación, ingreso/egreso, reingreso) y es el módulo a completar en fase 2</td></tr>
    <tr><td><strong>Bitácoras QR</strong></td><td><code>registros_servicios</code> · <code>entradas_salidas</code></td><td>Eventos por escaneo, hoy ligados por el código QR; el plan las re-liga por <code>nino_id</code> con FK dura</td></tr>
    <tr><td><strong>Catálogos</strong></td><td><code>hospital</code> · <code>trabajador_social</code> · <code>parentesco</code> · <code>tipo_dieta</code> · <code>escolaridad</code> · <code>ocupacion</code> · <code>edo_salud</code> · <code>clasificacion_social</code> · <code>zona</code> · <code>salario_minimo</code> · <code>tipo_ninio</code> · <code>tipo_tratamiento</code> · …</td><td>Precargados con los valores de la operación Puebla (293 filas en total); administrables sin tocar código</td></tr>
    <tr><td><strong>Geografía</strong></td><td><code>pais → estado → municipio</code></td><td>Jerarquía referencial para la procedencia de las familias</td></tr>
  </table></div>
  <p>La documentación de ingeniería completa (diccionario de datos por columna, diagrama entidad-relación y catálogo de hallazgos) se entrega junto con el repositorio (<code>DOCUMENTATION.md</code>).</p>
</article>

<article>
  <h2>5 · Estado actual, sin maquillaje</h2>
  <div class="honesto">
    <p><strong>Esto es un prototipo funcional, no un sistema listo para producción.</strong> Funciona de punta a punta — expediente, credencial, escaneos, concentrados — y eso es exactamente lo que afirma. Someterlo a esta revisión técnica con sus defectos a la vista es deliberado.</p>
  </div>
  <p>Deuda conocida y reconocida, ya inventariada archivo por archivo:</p>
  <ul>
    <li><strong>Sin autenticación ni roles.</strong> El prototipo no tiene login; es el punto número uno del plan y la razón por la que nunca ha operado con datos reales de familias.</li>
    <li><strong>Validación de entrada desactivada</strong> en la mayoría de los formularios (existe el código, está comentado).</li>
    <li><strong>Unicidad del QR no garantizada</strong> a nivel de base de datos ni de aplicación; el diseño correcto (índice único + verificación de colisiones + llave foránea por <code>nino_id</code> en las bitácoras) está especificado.</li>
    <li><strong>Framework desactualizado</strong>: el prototipo nació en Laravel 8 / PHP 8.0 (2021), hoy fin de vida; requiere migración a versiones soportadas.</li>
    <li>Defectos funcionales puntuales documentados (mapeos de campos, un módulo inconcluso, dependencias externas ya sustituidas), listados con ruta y línea en la documentación de ingeniería.</li>
  </ul>
  <p>Nada de lo anterior compromete la validez del diseño funcional — que es lo que este prototipo demuestra — pero sí define el trabajo previo a un piloto con datos reales.</p>
</article>

<article>
  <h2>6 · Plan de endurecimiento y despliegue</h2>

  <div class="fase">
    <div class="num">0</div>
    <div>
      <h3>Fase 0 — Endurecimiento (previo a cualquier dato real)</h3>
      <ul>
        <li>Migración a Laravel LTS vigente y PHP soportado; suite de pruebas sobre los flujos críticos.</li>
        <li>Autenticación y roles: <em>Trabajo Social</em> (expedientes), <em>Operación</em> (escaneo por servicio), <em>Dirección</em> (concentrados), <em>Administración</em> (catálogos).</li>
        <li>Reactivación de validación server-side; endpoints mutadores a <code>POST</code> con CSRF; rate-limiting en resolución de QR.</li>
        <li>Índice único + generación local del QR (sin servicios externos); bitácoras re-ligadas por <code>nino_id</code>.</li>
        <li>HTTPS obligatorio, cifrado en reposo de campos sensibles, respaldos automatizados cifrados y probados.</li>
      </ul>
    </div>
  </div>

  <div class="fase">
    <div class="num">1</div>
    <div>
      <h3>Fase 1 — Piloto acotado (4 semanas)</h3>
      <p>Un solo servicio (comedor) en la Casa Puebla, con credenciales reales y medición: minutos ahorrados por comida servida, exactitud del concentrado mensual contra el conteo manual, retroalimentación de trabajadoras sociales.</p>
    </div>
  </div>

  <div class="fase">
    <div class="num">2</div>
    <div>
      <h3>Fase 2 — Consolidación</h3>
      <p>Resto de servicios, módulo de estancia/habitaciones completo, reportes exportables (PDF/Excel) para patronato y donantes, y — si la Fundación lo decide — extensión multi-Casa con datos segregados por sede.</p>
    </div>
  </div>

  <h3>Requisitos de operación</h3>
  <ul>
    <li><strong>Hosting</strong>: un VPS modesto (2 vCPU / 4 GB) o el estándar corporativo que Tecnología defina; el sistema es un monolito PHP+MySQL convencional, empaquetable en contenedores si la política lo requiere. Costo de infraestructura estimado: el de un VPS básico.</li>
    <li><strong>En sitio</strong>: navegador + impresora para credenciales + lectores QR (o teléfonos) por punto de servicio.</li>
    <li><strong>Datos</strong>: propiedad exclusiva de la Fundación; exportables en cualquier momento (MySQL estándar, sin formatos propietarios).</li>
  </ul>
</article>

<article>
  <h2>7 · Seguridad, privacidad y cumplimiento</h2>
  <ul>
    <li><strong>Marco legal</strong>: datos personales de menores y datos sensibles (salud, situación socioeconómica) bajo la LFPDPPP — aviso de privacidad, consentimiento en el registro, derechos ARCO, y minimización: el sistema captura únicamente lo que el expediente en papel ya recaba.</li>
    <li><strong>Controles propuestos</strong>: acceso por rol con bitácora de auditoría, sesiones con expiración, cifrado en tránsito y en reposo, respaldos cifrados con retención definida por la Fundación, y política de borrado al egreso conforme a su normativa.</li>
    <li><strong>Revisión corporativa</strong>: el código es abierto y auditable; estamos en plena disposición de someterlo al proceso de revisión de seguridad del corporativo (análisis estático, pentest, cuestionarios de proveedor) antes del piloto, y de ajustar lo que resulte.</li>
  </ul>
</article>

<article>
  <h2>8 · Licencia, entrega y sostenibilidad</h2>
  <ul>
    <li><strong>Licencia MIT</strong>, repositorio transferido a la Fundación. Sin costo presente ni futuro; sin dependencia del donante.</li>
    <li><strong>Entregables</strong>: código, documentación de ingeniería (diccionario de datos, ER, catálogo de hallazgos), manuales de usuario por rol y guía de despliegue.</li>
    <li><strong>Sostenibilidad</strong>: stack (PHP/Laravel/MySQL) con oferta amplia de talento en México; cualquier proveedor o voluntario técnico puede mantenerlo. Compromiso de acompañamiento durante piloto y transición.</li>
  </ul>
  <p><strong>Lo que pedimos de la Dirección de Tecnología:</strong> una sesión de revisión de esta propuesta, los requisitos corporativos que el despliegue deba cumplir, y su criterio sobre el entorno del piloto. La demo funciona hoy con datos ficticios (el paciente de prueba es <em>Ronald Mc Uno</em>, QR <span class="qr">A1B2C3</span>) y puede mostrarse en vivo cuando lo indiquen.</p>
</article>

<div class="volver"><a href="{{ route('casita-secreta') }}">← Volver al índice de documentos</a></div>
<footer>
  <p>Presentado por: [Nombre del presentador] · ex-voluntario de la Casa Ronald McDonald · Propuesta independiente, no es un documento oficial de la Fundación.<br>
  Documento complementario: <a href="{{ route('casita-proyecto') }}">versión de proyecto para la Dirección de la Casa</a>.</p>
</footer>

</div>
</body>
</html>
