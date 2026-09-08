<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Casita Digital | Guía del equipo</title>
<link rel="icon" href="{{ asset('favicon.ico') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Raleway:wght@600;700;800&family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400&display=swap">
<style>
  :root {
    color-scheme: light;
    --azul: #4872AE;
    --azul-oscuro: #38598A;
    --azul-tenue: #EFF3FA;
    --amarillo: #FFC72C;
    --amarillo-tinta: #5c4500;
    --amarillo-suave: #FFF3D1;
    --rojo: #F64740;
    --rojo-tenue: #FDEEED;
    --rojo-tinta: #8C2622;
    --verde: #2E7D5B;
    --verde-tenue: #E8F4EE;
    --texto: #33383F;
    --gris: #666666;
    --gris-claro: #8A9099;
    --crema: #FBFAF6;
    --borde: #E8E6DF;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    background: var(--azul);
    font-family: "Open Sans", "Segoe UI", system-ui, sans-serif;
    color: var(--texto);
    line-height: 1.68;
    font-size: 16px;
  }
  .lienzo { max-width: 940px; margin: 0 auto; padding: 2.5rem 1.2rem 4rem; display: flex; flex-direction: column; gap: 1.6rem; }

  img.logo { max-width: 200px; height: auto; margin-bottom: 1.4rem; }
  .volver { text-align: center; }
  .volver a { color: #fff; text-decoration: none; font-family: "Raleway", sans-serif; font-weight: 700; border: 2px solid rgba(255,255,255,0.6); border-radius: 999px; padding: .5rem 1.4rem; display: inline-block; }
  .volver a:hover { background: rgba(255,255,255,0.12); }
  header.portada { background: #fff; border-radius: 16px; box-shadow: 0 12px 40px rgba(0,0,0,0.25); padding: 3rem 3rem 2.6rem; }
  .eyebrow { font-family: "Raleway", sans-serif; font-weight: 700; font-size: .78rem; letter-spacing: .14em; text-transform: uppercase; color: var(--azul); margin: 0 0 .6rem; }
  h1 { font-family: "Raleway", sans-serif; font-weight: 800; font-size: 2.7rem; line-height: 1.08; margin: 0 0 .8rem; text-wrap: balance; }
  h1 .amarillo { color: var(--amarillo); }
  .bajada { font-size: 1.14rem; color: var(--gris); max-width: 58ch; margin: 0 0 1.4rem; }
  .sello { display: inline-block; background: var(--amarillo); color: var(--amarillo-tinta); font-family: "Raleway", sans-serif; font-weight: 700; font-size: .88rem; padding: .45rem 1.1rem; border-radius: 999px; }

  article { background: #fff; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.18); padding: 2.5rem 3rem; }
  h2 { font-family: "Raleway", sans-serif; font-weight: 800; font-size: 1.65rem; margin: 0 0 1.1rem; color: var(--azul-oscuro); text-wrap: balance; }
  h2::after { content: ""; display: block; width: 56px; height: 4px; background: var(--rojo); border-radius: 2px; margin-top: .55rem; }
  h3 { font-family: "Raleway", sans-serif; font-weight: 700; font-size: 1.18rem; margin: 1.9rem 0 .5rem; }
  p { margin: 0 0 1rem; max-width: 68ch; }
  ul, ol { margin: 0 0 1rem; padding-left: 1.35rem; max-width: 66ch; }
  li { margin-bottom: .5rem; }
  li::marker { color: var(--azul); font-weight: 700; }
  strong { font-weight: 700; }
  .qr { font-family: ui-monospace, "Courier New", monospace; font-weight: 700; letter-spacing: .08em; background: var(--amarillo-suave); color: #7a5c00; padding: .05em .45em; border-radius: 4px; }

  /* pasos numerados */
  .paso { display: flex; gap: 1.2rem; align-items: flex-start; margin-bottom: 1.7rem; }
  .paso .num { flex: 0 0 auto; width: 2.7rem; height: 2.7rem; border-radius: 50%; background: var(--amarillo); color: var(--amarillo-tinta); font-family: "Raleway", sans-serif; font-weight: 800; font-size: 1.25rem; display: flex; align-items: center; justify-content: center; }
  .paso .cuerpo { min-width: 0; flex: 1; }
  .paso h3 { margin: .2rem 0 .4rem; }
  .paso p:last-child { margin-bottom: 0; }

  /* capturas */
  figure { margin: 1.3rem 0 1.6rem; }
  figure img { display: block; width: 100%; height: auto; border-radius: 10px; border: 1px solid var(--borde); box-shadow: 0 4px 16px rgba(0,0,0,0.10); }
  figure .marco { overflow-x: auto; border: 1px solid var(--borde); border-radius: 12px; background: var(--crema); padding: 1.2rem; }
  figure .marco svg { display: block; width: 100%; min-width: 560px; height: auto; }
  figcaption { font-size: .87rem; color: var(--gris); margin-top: .6rem; max-width: 68ch; }
  figcaption b { color: var(--texto); }

  /* avisos */
  .consejo, .ojo, .nota {
    border-radius: 0 10px 10px 0; padding: .95rem 1.25rem; margin: 1.1rem 0;
    display: flex; gap: .85rem; align-items: flex-start;
  }
  .consejo { border-left: 4px solid var(--amarillo); background: var(--amarillo-suave); }
  .ojo { border-left: 4px solid var(--rojo); background: var(--rojo-tenue); }
  .nota { border-left: 4px solid var(--azul); background: var(--azul-tenue); }
  .consejo p, .ojo p, .nota p { margin: 0; max-width: none; font-size: .96rem; }
  .consejo svg, .ojo svg, .nota svg { flex: 0 0 auto; margin-top: .18rem; }

  /* tarjetas de rol */
  .roles { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1rem; margin: 1.3rem 0 1.5rem; }
  .rol { background: var(--crema); border: 1px solid var(--borde); border-radius: 12px; padding: 1.2rem 1.3rem; }
  .rol .icono { display: block; margin-bottom: .6rem; }
  .rol h4 { font-family: "Raleway", sans-serif; font-weight: 800; font-size: 1.02rem; margin: 0 0 .35rem; color: var(--azul-oscuro); }
  .rol p { font-size: .9rem; color: var(--gris); margin: 0; max-width: none; }

  /* tabla */
  .tabla-scroll { overflow-x: auto; border: 1px solid var(--borde); border-radius: 10px; margin-bottom: 1.2rem; }
  table { border-collapse: collapse; width: 100%; font-size: .93rem; }
  th, td { text-align: left; padding: .6rem .9rem; border-bottom: 1px solid var(--borde); vertical-align: top; }
  th { font-family: "Raleway", sans-serif; font-size: .76rem; text-transform: uppercase; letter-spacing: .07em; color: var(--gris); background: var(--crema); white-space: nowrap; }
  tr:last-child td { border-bottom: none; }
  td.c { text-align: center; }
  .si { color: var(--verde); font-weight: 700; }
  .no { color: var(--gris-claro); }

  /* preguntas */
  .pregunta { border-top: 1px solid var(--borde); padding-top: 1rem; margin-top: 1rem; }
  .pregunta:first-of-type { border-top: none; padding-top: 0; margin-top: 0; }
  .pregunta h4 { font-family: "Raleway", sans-serif; font-weight: 700; font-size: 1.02rem; margin: 0 0 .3rem; color: var(--azul-oscuro); }
  .pregunta p { margin: 0; font-size: .96rem; }

  footer { text-align: center; color: rgba(255,255,255,0.9); font-size: .86rem; padding: .5rem 0 0; }
  footer p { max-width: none; margin: 0 auto; }
  footer a { color: var(--amarillo); }

  @media (max-width: 700px) {
    article, header.portada { padding: 1.9rem 1.3rem; }
    h1 { font-size: 2rem; }
    .lienzo { padding-top: 1.4rem; }
    .paso { gap: .8rem; }
  }
</style>
</head>
<body>

<div class="lienzo">

<header class="portada">
  <img class="logo" src="{{ asset('img/RMHC_Mexico_logo.png') }}" alt="Fundación Infantil Ronald McDonald México">
  <p class="eyebrow">Para el equipo de la Casa Ronald McDonald</p>
  <h1>Guía de la <span class="amarillo">Casita Digital</span></h1>
  <p class="bajada">Todo lo que hace el sistema, contado como se usa: entrar, registrar a una familia, imprimir su credencial, escanear en cada servicio y sacar los números del mes. Sin tecnicismos y con las pantallas reales.</p>
</header>

<article>
  <h2>La idea, en una imagen</h2>
  <p>La Casita Digital hace una sola cosa, bien: <strong>que cada familia se registre una vez</strong> y que a partir de ahí baste con escanear su credencial para dejar constancia de cada servicio que recibe. Lo que hoy son libretas, hojas sueltas y sumas a mano al final del mes, queda anotado solo.</p>

  <figure>
    <div class="marco">
      <svg viewBox="0 0 900 210" role="img" aria-label="Recorrido: llega la familia, se abre su expediente, se imprime su credencial con código QR, se escanea en cada servicio del día y al final del mes salen los números.">
        <defs>
          <marker id="f1" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="8" markerHeight="8" orient="auto-start-reverse">
            <path d="M 0 0 L 10 5 L 0 10 z" fill="#4872AE"></path>
          </marker>
        </defs>

        <!-- 1 llega la familia -->
        <circle cx="80" cy="70" r="34" fill="#FFF3D1" stroke="#FFC72C" stroke-width="2"></circle>
        <circle cx="70" cy="62" r="9" fill="none" stroke="#5c4500" stroke-width="2.2"></circle>
        <path d="M 56 86 a 14 13 0 0 1 28 0" fill="none" stroke="#5c4500" stroke-width="2.2"></path>
        <circle cx="90" cy="68" r="6.5" fill="none" stroke="#5c4500" stroke-width="2.2"></circle>
        <path d="M 80 86 a 10 9 0 0 1 20 0" fill="none" stroke="#5c4500" stroke-width="2.2"></path>
        <text x="80" y="132" font-size="13.5" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Llega la familia</text>
        <text x="80" y="152" font-size="11.5" fill="#666666" text-anchor="middle">canalizada por</text>
        <text x="80" y="168" font-size="11.5" fill="#666666" text-anchor="middle">trabajo social</text>

        <!-- 2 expediente -->
        <circle cx="270" cy="70" r="34" fill="#EFF3FA" stroke="#4872AE" stroke-width="2"></circle>
        <path d="M 252 54 h 14 l 4 6 h 18 v 26 h -36 z" fill="none" stroke="#38598A" stroke-width="2.2" stroke-linejoin="round"></path>
        <line x1="258" y1="72" x2="282" y2="72" stroke="#38598A" stroke-width="2"></line>
        <line x1="258" y1="80" x2="276" y2="80" stroke="#38598A" stroke-width="2"></line>
        <text x="270" y="132" font-size="13.5" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Se abre su expediente</text>
        <text x="270" y="152" font-size="11.5" fill="#666666" text-anchor="middle">una sola vez,</text>
        <text x="270" y="168" font-size="11.5" fill="#666666" text-anchor="middle">con sus acompañantes</text>

        <!-- 3 carnet -->
        <circle cx="460" cy="70" r="34" fill="#FFF3D1" stroke="#FFC72C" stroke-width="2"></circle>
        <rect x="444" y="54" width="14" height="14" fill="none" stroke="#5c4500" stroke-width="2.2"></rect>
        <rect x="462" y="54" width="14" height="14" fill="none" stroke="#5c4500" stroke-width="2.2"></rect>
        <rect x="444" y="72" width="14" height="14" fill="none" stroke="#5c4500" stroke-width="2.2"></rect>
        <rect x="464" y="74" width="4" height="4" fill="#5c4500"></rect>
        <rect x="472" y="82" width="4" height="4" fill="#5c4500"></rect>
        <rect x="464" y="82" width="4" height="4" fill="#5c4500"></rect>
        <text x="460" y="132" font-size="13.5" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Se imprime su credencial</text>
        <text x="460" y="152" font-size="11.5" fill="#666666" text-anchor="middle">con un código QR</text>
        <text x="460" y="168" font-size="11.5" fill="#666666" text-anchor="middle">de seis caracteres</text>

        <!-- 4 escaneo -->
        <circle cx="650" cy="70" r="34" fill="#EFF3FA" stroke="#4872AE" stroke-width="2"></circle>
        <rect x="638" y="50" width="24" height="40" rx="4" fill="none" stroke="#38598A" stroke-width="2.2"></rect>
        <line x1="644" y1="58" x2="656" y2="58" stroke="#38598A" stroke-width="2"></line>
        <line x1="644" y1="66" x2="656" y2="66" stroke="#38598A" stroke-width="2"></line>
        <line x1="644" y1="74" x2="652" y2="74" stroke="#38598A" stroke-width="2"></line>
        <path d="M 666 62 l 8 8 l -8 8" fill="none" stroke="#4872AE" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"></path>
        <text x="650" y="132" font-size="13.5" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Se escanea en cada servicio</text>
        <text x="650" y="152" font-size="11.5" fill="#666666" text-anchor="middle">comedor, lavandería, escuelita,</text>
        <text x="650" y="168" font-size="11.5" fill="#666666" text-anchor="middle">transporte, entradas y salidas</text>

        <!-- 5 números -->
        <circle cx="840" cy="70" r="34" fill="#FFF3D1" stroke="#FFC72C" stroke-width="2"></circle>
        <rect x="824" y="70" width="8" height="18" fill="#5c4500"></rect>
        <rect x="836" y="58" width="8" height="30" fill="#5c4500"></rect>
        <rect x="848" y="64" width="8" height="24" fill="#5c4500"></rect>
        <text x="840" y="132" font-size="13.5" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Salen los números</text>
        <text x="840" y="152" font-size="11.5" fill="#666666" text-anchor="middle">del día, la semana,</text>
        <text x="840" y="168" font-size="11.5" fill="#666666" text-anchor="middle">el mes o el año</text>

        <line x1="122" y1="70" x2="228" y2="70" stroke="#4872AE" stroke-width="2" marker-end="url(#f1)"></line>
        <line x1="312" y1="70" x2="418" y2="70" stroke="#4872AE" stroke-width="2" marker-end="url(#f1)"></line>
        <line x1="502" y1="70" x2="608" y2="70" stroke="#4872AE" stroke-width="2" marker-end="url(#f1)"></line>
        <line x1="692" y1="70" x2="798" y2="70" stroke="#4872AE" stroke-width="2" marker-end="url(#f1)"></line>
      </svg>
    </div>
    <figcaption><b>De la llegada al informe.</b> Solo el segundo paso cuesta trabajo, y se hace una vez por familia. Todo lo demás es escanear.</figcaption>
  </figure>

  <div class="nota">
    <svg width="18" height="18" viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="9" fill="none" stroke="#38598A" stroke-width="2"></circle><line x1="10" y1="9" x2="10" y2="15" stroke="#38598A" stroke-width="2" stroke-linecap="round"></line><circle cx="10" cy="5.6" r="1.3" fill="#38598A"></circle></svg>
    <p>Todas las pantallas de esta guía son del sistema real, pero con <strong>familias de prueba inventadas</strong>. Ningún dato de una familia real ha pasado nunca por él.</p>
  </div>
</article>

<article>
  <h2>Tu cuenta</h2>
  <p>Cada persona del equipo tiene su <strong>propia cuenta</strong>: un correo y una contraseña. No hay una cuenta compartida, y eso es a propósito: el sistema anota quién hizo cada cosa, así que un error se puede rastrear y corregir sin tener que adivinar.</p>

  <figure>
    <img src="{{ asset('img/docs/guia-login.jpg') }}" alt="Pantalla de inicio de sesión con el logotipo de la Fundación, los campos de correo electrónico y contraseña, y el botón Entrar.">
    <figcaption><b>La puerta de entrada.</b> Sin esta pantalla no se ve absolutamente nada del sistema.</figcaption>
  </figure>

  <h3>Quién puede hacer qué</h3>
  <div class="roles">
    <div class="rol">
      <svg class="icono" width="34" height="34" viewBox="0 0 34 34" aria-hidden="true">
        <circle cx="17" cy="17" r="16" fill="#FFF3D1"></circle>
        <circle cx="17" cy="13" r="4.6" fill="none" stroke="#5c4500" stroke-width="2"></circle>
        <path d="M 9 26 a 8 7.5 0 0 1 16 0" fill="none" stroke="#5c4500" stroke-width="2"></path>
        <path d="M 22 8 l 1.6 3.4 l 3.6 .4 l -2.7 2.5 l .8 3.6 l -3.3 -1.9 l -3.3 1.9" fill="none" stroke="#5c4500" stroke-width="1.6" stroke-linejoin="round" opacity="0"></path>
      </svg>
      <h4>Dirección</h4>
      <p>Hace todo lo que hacen los demás, y además crea las cuentas del personal, las activa o desactiva, ve los reportes de uso y la bitácora de quién hizo qué.</p>
    </div>
    <div class="rol">
      <svg class="icono" width="34" height="34" viewBox="0 0 34 34" aria-hidden="true">
        <circle cx="17" cy="17" r="16" fill="#EFF3FA"></circle>
        <circle cx="17" cy="13" r="4.6" fill="none" stroke="#38598A" stroke-width="2"></circle>
        <path d="M 9 26 a 8 7.5 0 0 1 16 0" fill="none" stroke="#38598A" stroke-width="2"></path>
      </svg>
      <h4>Personal operativo</h4>
      <p>Recepción, comedor, voluntariado. Abre expedientes, imprime credenciales, consulta la bitácora y —lo más importante— <strong>opera el escáner</strong> en cada servicio.</p>
    </div>
    <div class="rol">
      <svg class="icono" width="34" height="34" viewBox="0 0 34 34" aria-hidden="true">
        <circle cx="17" cy="17" r="16" fill="#EFF3FA"></circle>
        <path d="M 11 9 h 12 v 16 h -12 z" fill="none" stroke="#38598A" stroke-width="2" stroke-linejoin="round"></path>
        <line x1="14" y1="14" x2="20" y2="14" stroke="#38598A" stroke-width="1.8"></line>
        <line x1="14" y1="18" x2="20" y2="18" stroke="#38598A" stroke-width="1.8"></line>
        <line x1="14" y1="22" x2="18" y2="22" stroke="#38598A" stroke-width="1.8"></line>
      </svg>
      <h4>Trabajo social</h4>
      <p>Abre y actualiza expedientes, consulta la información y descarga el expediente completo en PDF. No usa el escáner: esa es tarea del personal de cada servicio.</p>
    </div>
  </div>

  <div class="tabla-scroll"><table>
    <tr><th>Puedo…</th><th class="c">Dirección</th><th class="c">Personal</th><th class="c">Trabajo social</th></tr>
    <tr><td>Abrir y editar expedientes</td><td class="c si">Sí</td><td class="c si">Sí</td><td class="c si">Sí</td></tr>
    <tr><td>Imprimir la credencial con QR</td><td class="c si">Sí</td><td class="c si">Sí</td><td class="c si">Sí</td></tr>
    <tr><td>Consultar la bitácora de servicios</td><td class="c si">Sí</td><td class="c si">Sí</td><td class="c si">Sí</td></tr>
    <tr><td>Escanear credenciales</td><td class="c si">Sí</td><td class="c si">Sí</td><td class="c no">No</td></tr>
    <tr><td>Descargar el expediente en PDF</td><td class="c si">Sí</td><td class="c no">No</td><td class="c si">Sí</td></tr>
    <tr><td>Crear cuentas y ver los reportes</td><td class="c si">Sí</td><td class="c no">No</td><td class="c no">No</td></tr>
  </table></div>

  <div class="ojo">
    <svg width="18" height="18" viewBox="0 0 20 20" aria-hidden="true"><path d="M 10 2.5 L 18.5 17.5 H 1.5 Z" fill="none" stroke="#8C2622" stroke-width="2" stroke-linejoin="round"></path><line x1="10" y1="8" x2="10" y2="12.5" stroke="#8C2622" stroke-width="2" stroke-linecap="round"></line><circle cx="10" cy="15.2" r="1.1" fill="#8C2622"></circle></svg>
    <p><strong>Tu contraseña es tuya.</strong> No se presta ni se pega en un papel junto a la computadora. Si crees que alguien más la conoce, pídele a Dirección que te la restablezca: toma diez segundos. Y si dejas de trabajar en la Casa, tu cuenta se desactiva —nunca se borra—, para que el historial siga completo.</p>
  </div>
</article>

<article>
  <h2>Lo que ves al entrar</h2>
  <p>La barra de arriba está siempre a la vista, en todas las páginas. Tiene cuatro cosas y no hay dónde perderse.</p>

  <figure>
    <img src="{{ asset('img/docs/guia-inicio.jpg') }}" alt="Pantalla de inicio con la barra superior (Escanear, Expedientes, Bitácora, Administración), una fotografía de la Casa y los botones de módulos.">
    <figcaption><b>La pantalla de inicio.</b> El botón amarillo grande es el que más se usa en el día.</figcaption>
  </figure>

  <ul>
    <li><strong>Escanear</strong> — abre la ventanita para registrar un servicio. Está disponible desde cualquier página, no hay que regresar al inicio.</li>
    <li><strong>Expedientes</strong> — la lista de todos los niños y niñas albergados, con su código y sus acompañantes.</li>
    <li><strong>Bitácora</strong> — lo que se ha registrado en cada servicio, con fecha y hora.</li>
    <li><strong>Administración</strong> — solo la ve Dirección: cuentas, reportes y el registro de actividad.</li>
  </ul>
</article>

<article>
  <h2>Registrar a una familia nueva</h2>
  <p>Es el único momento que toma tiempo, y se hace <strong>una sola vez por familia</strong>. El formulario sigue el mismo orden del expediente en papel que ya conoces.</p>

  <div class="paso">
    <div class="num">1</div>
    <div class="cuerpo">
      <h3>Entra a Expedientes y toca «Nuevo registro»</h3>
      <p>Se abre el formulario. Ten a la mano la solicitud de trabajo social: los datos del menor, su procedencia, el hospital que lo atiende, el diagnóstico y el médico tratante.</p>
    </div>
  </div>

  <div class="paso">
    <div class="num">2</div>
    <div class="cuerpo">
      <h3>Llena los datos y toma la foto</h3>
      <p>Nombre y apellidos, fecha de nacimiento, procedencia (estado, municipio y localidad), teléfonos, dialecto o lengua si aplica, escolaridad, tipo de dieta, alergias y el tipo de tratamiento. La foto se puede tomar ahí mismo con la cámara.</p>
      <p>Si un dato no lo tienes todavía, muchos campos son opcionales: guarda lo que tengas y complétalo después con el botón de editar.</p>
    </div>
  </div>

  <div class="paso">
    <div class="num">3</div>
    <div class="cuerpo">
      <h3>Agrega a los acompañantes</h3>
      <p>Cada acompañante se liga al expediente del menor con su parentesco, edad, estado de salud y situación laboral. Es la información que después sustenta prórrogas y reportes.</p>
    </div>
  </div>

  <figure>
    <img src="{{ asset('img/docs/guia-expedientes.jpg') }}" alt="Lista de expedientes con columnas de número, nombre, edad, procedencia, código QR y botones para ver la credencial, agregar acompañantes y abrir o editar el expediente.">
    <figcaption><b>La lista de expedientes.</b> Cada renglón trae su código de seis caracteres y los botones para ver la credencial, agregar acompañantes o abrir el expediente completo.</figcaption>
  </figure>

  <div class="consejo">
    <svg width="18" height="18" viewBox="0 0 20 20" aria-hidden="true"><path d="M 10 1.8 a 6.4 6.4 0 0 1 3.6 11.7 v 2.2 h -7.2 v -2.2 A 6.4 6.4 0 0 1 10 1.8 z" fill="none" stroke="#7a5c00" stroke-width="1.9" stroke-linejoin="round"></path><line x1="7.6" y1="18" x2="12.4" y2="18" stroke="#7a5c00" stroke-width="1.9" stroke-linecap="round"></line></svg>
    <p><strong>El código no lo escribes tú.</strong> Al guardar, el sistema le asigna a la familia un código único de seis caracteres, como <span class="qr">R2M4Z6</span>, y ya nunca cambia — ni aunque después corrijas el nombre o la fecha.</p>
  </div>
</article>

<article>
  <h2>La credencial con código QR</h2>
  <p>Al terminar el registro, el sistema arma la credencial de la familia: tamaño carta, lista para imprimir. Trae el logotipo de la Fundación, el nombre y la edad del menor, su procedencia, el hospital y la lista de sus acompañantes con espacio para firmar.</p>

  <figure>
    <img src="{{ asset('img/docs/guia-carnet.jpg') }}" alt="Credencial impresa: logotipo de la Fundación, nombre y edad del menor, código QR grande dentro de un aro amarillo, el código de seis caracteres, procedencia, hospital y la lista de acompañantes con línea de firma.">
    <figcaption><b>La credencial.</b> El código está impreso en grande debajo del QR, a propósito: si la cámara no lee, siempre se puede teclear.</figcaption>
  </figure>

  <ul>
    <li>Se genera desde el botón <strong>Carnet QR</strong> del expediente, en la lista o en la página del niño.</li>
    <li>Sale en dos formatos: imagen para ver o guardar, y <strong>PDF tamaño carta</strong> para imprimir directo.</li>
    <li>Si se pierde o se maltrata, se vuelve a imprimir sin más: <strong>el código es el mismo</strong>, no hay que rehacer nada.</li>
    <li>La credencial se actualiza sola si el expediente cambió — por ejemplo, si se agregó un acompañante.</li>
  </ul>
</article>

<article>
  <h2>Registrar un servicio: el escáner</h2>
  <p>Esto es lo que se hace decenas de veces al día, y por eso está pensado para tomar <strong>menos de cinco segundos</strong>. El botón amarillo <strong>Escanear</strong> abre una ventanita flotante que funciona desde cualquier página, y desde el teléfono igual que desde la computadora.</p>

  <h3>Los seis registros</h3>
  <figure>
    <div class="marco">
      <svg viewBox="0 0 900 150" role="img" aria-label="Los seis registros disponibles: comedor, lavandería, escuelita, transporte, entrada y salida.">
        <!-- Comedor -->
        <rect x="10" y="20" width="136" height="110" rx="12" fill="#FDEEED" stroke="#F0A79E"></rect>
        <line x1="69" y1="46" x2="69" y2="76" stroke="#8C2622" stroke-width="2.6" stroke-linecap="round"></line>
        <line x1="63" y1="46" x2="63" y2="58" stroke="#8C2622" stroke-width="2.2" stroke-linecap="round"></line>
        <line x1="75" y1="46" x2="75" y2="58" stroke="#8C2622" stroke-width="2.2" stroke-linecap="round"></line>
        <path d="M 91 46 a 7 12 0 0 1 0 22 h -0.5 v 8" fill="none" stroke="#8C2622" stroke-width="2.4" stroke-linecap="round"></path>
        <path d="M 55 88 h 46" stroke="#8C2622" stroke-width="2.4" stroke-linecap="round"></path>
        <text x="78" y="116" font-size="14" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Comedor</text>

        <!-- Lavandería -->
        <rect x="156" y="20" width="136" height="110" rx="12" fill="#EFF3FA" stroke="#A9BEDB"></rect>
        <path d="M 206 46 h 12 a 6 6 0 0 0 12 0 h 12 l 8 12 l -8 7 v 25 h -36 v -25 l -8 -7 z" fill="none" stroke="#38598A" stroke-width="2.2" stroke-linejoin="round"></path>
        <text x="224" y="116" font-size="14" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Lavandería</text>

        <!-- Escuelita -->
        <rect x="302" y="20" width="136" height="110" rx="12" fill="#E8F4EE" stroke="#9BC9B4"></rect>
        <path d="M 340 58 l 30 -14 l 30 14 l -30 13 z" fill="none" stroke="#2E7D5B" stroke-width="2.2" stroke-linejoin="round"></path>
        <path d="M 352 65 v 14 a 20 10 0 0 0 36 0 v -14" fill="none" stroke="#2E7D5B" stroke-width="2.2"></path>
        <line x1="400" y1="58" x2="400" y2="76" stroke="#2E7D5B" stroke-width="2.2" stroke-linecap="round"></line>
        <text x="370" y="116" font-size="14" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Escuelita</text>

        <!-- Transporte -->
        <rect x="448" y="20" width="136" height="110" rx="12" fill="#FFF3D1" stroke="#E5C264"></rect>
        <rect x="486" y="44" width="60" height="34" rx="5" fill="none" stroke="#7a5c00" stroke-width="2.2"></rect>
        <line x1="486" y1="58" x2="546" y2="58" stroke="#7a5c00" stroke-width="2"></line>
        <line x1="516" y1="44" x2="516" y2="58" stroke="#7a5c00" stroke-width="2"></line>
        <circle cx="497" cy="84" r="6" fill="none" stroke="#7a5c00" stroke-width="2.2"></circle>
        <circle cx="535" cy="84" r="6" fill="none" stroke="#7a5c00" stroke-width="2.2"></circle>
        <text x="516" y="116" font-size="14" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Transporte</text>

        <!-- Entrada -->
        <rect x="594" y="20" width="136" height="110" rx="12" fill="#E8F4EE" stroke="#9BC9B4"></rect>
        <path d="M 663 44 h 26 v 46 h -26" fill="none" stroke="#2E7D5B" stroke-width="2.2" stroke-linejoin="round"></path>
        <line x1="635" y1="67" x2="665" y2="67" stroke="#2E7D5B" stroke-width="2.4" stroke-linecap="round"></line>
        <path d="M 657 59 l 8 8 l -8 8" fill="none" stroke="#2E7D5B" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"></path>
        <text x="662" y="116" font-size="14" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Entrada</text>

        <!-- Salida -->
        <rect x="740" y="20" width="136" height="110" rx="12" fill="#FDEEED" stroke="#F0A79E"></rect>
        <path d="M 807 44 h -26 v 46 h 26" fill="none" stroke="#8C2622" stroke-width="2.2" stroke-linejoin="round"></path>
        <line x1="805" y1="67" x2="835" y2="67" stroke="#8C2622" stroke-width="2.4" stroke-linecap="round"></line>
        <path d="M 827 59 l 8 8 l -8 8" fill="none" stroke="#8C2622" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"></path>
        <text x="808" y="116" font-size="14" font-weight="700" fill="#33383F" text-anchor="middle" font-family="Raleway, sans-serif">Salida</text>
      </svg>
    </div>
    <figcaption><b>Elige uno y escanea.</b> Los cuatro primeros anotan un servicio recibido. Los dos últimos llevan el control de quién está dentro de la Casa en cada momento.</figcaption>
  </figure>

  <h3>Los tres toques</h3>
  <div class="paso">
    <div class="num">1</div>
    <div class="cuerpo">
      <h3>Elige el servicio</h3>
      <p>Comedor, lavandería, escuelita, transporte, entrada o salida. Si te equivocaste, «Cambiar servicio» te regresa a esta pantalla.</p>
      <figure>
        <img src="{{ asset('img/docs/guia-escanear-servicios.jpg') }}" alt="Ventana flotante «Registrar servicio» con seis botones de colores: Comedor, Lavandería, Escuelita, Transporte, Entrada y Salida.">
      </figure>
    </div>
  </div>

  <div class="paso">
    <div class="num">2</div>
    <div class="cuerpo">
      <h3>Escanea la credencial o teclea el código</h3>
      <p>Con «Activar cámara», apuntas al QR y el código se llena solo; la cámara se apaga sola en cuanto lee. Si prefieres, escribes los seis caracteres a mano, o usas un lector de código conectado por USB, que funciona como si tecleara.</p>
    </div>
  </div>

  <div class="paso">
    <div class="num">3</div>
    <div class="cuerpo">
      <h3>Toca «Registrar» y lee la confirmación</h3>
      <p>Aparece la palomita verde con el nombre, la edad, <strong>el tipo de dieta y las alergias</strong> del menor. Ese es el punto: quien sirve la comida ve la dieta en la misma pantalla donde acaba de escanear, sin ir a buscarla a ningún lado.</p>
      <figure>
        <img src="{{ asset('img/docs/guia-escanear-confirmacion.jpg') }}" alt="Confirmación verde: «Comedor registrado», con el nombre del niño, su edad, su dieta, la hora del registro y la alergia alimentaria señalada en rojo.">
      </figure>
      <p>Con «Registrar otro» sigues con la siguiente familia sin cerrar nada. Es lo que se usa a la hora del desayuno.</p>
    </div>
  </div>

  <h3>La regla de entradas y salidas</h3>
  <p>Es la única regla que el sistema te va a hacer respetar, y protege un dato de seguridad: saber quién está dentro de la Casa.</p>

  <figure>
    <div class="marco">
      <svg viewBox="0 0 880 190" role="img" aria-label="Regla de entradas y salidas: una entrada abre el registro, la salida lo cierra; una segunda entrada seguida se rechaza y una salida sin entrada previa también.">
        <defs>
          <marker id="f2" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="8" markerHeight="8" orient="auto-start-reverse">
            <path d="M 0 0 L 10 5 L 0 10 z" fill="#2E7D5B"></path>
          </marker>
          <marker id="f2r" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="8" markerHeight="8" orient="auto-start-reverse">
            <path d="M 0 0 L 10 5 L 0 10 z" fill="#8C2622"></path>
          </marker>
        </defs>

        <rect x="20" y="58" width="180" height="62" rx="10" fill="#E8F4EE" stroke="#2E7D5B"></rect>
        <text x="110" y="84" font-size="14" font-weight="700" fill="#2E7D5B" text-anchor="middle" font-family="Raleway, sans-serif">Escaneas «Entrada»</text>
        <text x="110" y="105" font-size="12" fill="#33383F" text-anchor="middle">queda dentro de la Casa</text>

        <rect x="350" y="58" width="180" height="62" rx="10" fill="#FDEEED" stroke="#8C2622"></rect>
        <text x="440" y="84" font-size="14" font-weight="700" fill="#8C2622" text-anchor="middle" font-family="Raleway, sans-serif">Escaneas «Salida»</text>
        <text x="440" y="105" font-size="12" fill="#33383F" text-anchor="middle">se cierra el registro</text>

        <rect x="680" y="58" width="180" height="62" rx="10" fill="#E8F4EE" stroke="#2E7D5B"></rect>
        <text x="770" y="84" font-size="14" font-weight="700" fill="#2E7D5B" text-anchor="middle" font-family="Raleway, sans-serif">Entrada otra vez</text>
        <text x="770" y="105" font-size="12" fill="#33383F" text-anchor="middle">y vuelve a empezar</text>

        <line x1="206" y1="89" x2="344" y2="89" stroke="#2E7D5B" stroke-width="2" marker-end="url(#f2)"></line>
        <line x1="536" y1="89" x2="674" y2="89" stroke="#2E7D5B" stroke-width="2" marker-end="url(#f2)"></line>

        <path d="M 110 54 q 0 -34 40 -34 q 40 0 40 26" fill="none" stroke="#8C2622" stroke-width="2" stroke-dasharray="5 4" marker-end="url(#f2r)"></path>
        <text x="150" y="14" font-size="11.5" fill="#8C2622" text-anchor="middle">dos entradas seguidas: el sistema avisa y no registra</text>

        <path d="M 440 124 q 0 30 -40 30 q -40 0 -40 -24" fill="none" stroke="#8C2622" stroke-width="2" stroke-dasharray="5 4" marker-end="url(#f2r)"></path>
        <text x="440" y="180" font-size="11.5" fill="#8C2622" text-anchor="middle">salida sin entrada previa: el sistema avisa y no registra</text>
      </svg>
    </div>
    <figcaption><b>Una entrada abre, una salida cierra.</b> Si te sale un aviso naranja, no es un error tuyo: es el sistema evitando que el conteo de quién está en la Casa quede mal.</figcaption>
  </figure>

  <h3>Los avisos que te puede dar</h3>
  <div class="tabla-scroll"><table>
    <tr><th>Si ves…</th><th>Quiere decir</th><th>Qué haces</th></tr>
    <tr><td>«Ningún expediente corresponde a este código»</td><td>El código está mal tecleado, o esa familia no está registrada</td><td>Revisa los seis caracteres de la credencial. Si no aparece, hay que abrir su expediente primero</td></tr>
    <tr><td>«Ya hay una entrada abierta desde…»</td><td>Esa persona nunca registró su salida anterior</td><td>Registra primero la salida y luego la entrada nueva</td></tr>
    <tr><td>«No hay una entrada abierta para…»</td><td>Se está registrando una salida de alguien que no marcó entrada</td><td>Registra la entrada primero</td></tr>
    <tr><td>La cámara no enciende</td><td>El navegador solo deja usar la cámara en conexiones seguras</td><td>Teclea el código a mano: se registra exactamente igual</td></tr>
  </table></div>
</article>

<article>
  <h2>Consultar la bitácora</h2>
  <p>En <strong>Bitácora</strong> está todo lo registrado, con una pestaña por servicio: comedor, lavandería, escuelita, transporte y entradas y salidas. Cada renglón dice quién, con qué código, en qué servicio y a qué hora.</p>

  <figure>
    <img src="{{ asset('img/docs/guia-comedor.jpg') }}" alt="Tabla de la bitácora del comedor con columnas de número, niño, código, servicio y fecha y hora, con buscador y paginación.">
    <figcaption><b>La bitácora del comedor.</b> Se puede buscar por nombre o por código, ordenar por cualquier columna y avanzar de página.</figcaption>
  </figure>

  <ul>
    <li>El buscador de arriba a la derecha filtra por <strong>nombre o código</strong> mientras escribes.</li>
    <li>Tocando el nombre del niño llegas a su expediente completo.</li>
    <li>Nada de esto se borra ni se edita: la bitácora es el respaldo de lo que realmente pasó.</li>
  </ul>
</article>

<article>
  <h2>Los números del mes</h2>
  <p>Esta parte la ve Dirección. Es la que convierte los escaneos del día en las cifras que se reportan al patronato y a los donantes, sin sumar renglones a mano.</p>

  <figure>
    <img src="{{ asset('img/docs/guia-reportes.jpg') }}" alt="Reporte de servicios: botones de Hoy, Semana, Mes, Año y Todo, un rango de fechas, los totales por servicio y una gráfica de barras apiladas por día.">
    <figcaption><b>Reportes de servicios.</b> Se elige el bloque de tiempo y la gráfica se ajusta sola: un día se ve por hora, un mes por día, un año por mes.</figcaption>
  </figure>

  <ul>
    <li><strong>Hoy, Semana, Mes, Año o Todo</strong>, o un rango de fechas exacto si el informe lo pide así.</li>
    <li>Arriba, el total del periodo y el desglose por servicio; abajo, cómo se repartió en el tiempo.</li>
    <li><strong>Descargar reporte</strong> baja la bitácora del periodo en Excel: un renglón por registro, con usuario, código, servicio, fecha y hora. Listo para pegar en el informe.</li>
  </ul>

  <h3>Y quién hizo qué</h3>
  <p>En la misma página está el <strong>registro de actividad</strong>: cada inicio de sesión, cada escaneo, cada alta o edición de expediente y cada descarga, con la cuenta que lo hizo y el momento exacto. No es para vigilar a nadie; es para poder reconstruir qué pasó cuando algo no cuadra.</p>

  <figure>
    <img src="{{ asset('img/docs/guia-actividad.jpg') }}" alt="Tabla del registro de actividad con fecha y hora, usuario, tipo de acción y detalle de cada movimiento.">
    <figcaption><b>Registro de actividad.</b> Se puede filtrar por cuenta y buscar por texto.</figcaption>
  </figure>
</article>

<article>
  <h2>La página de cada niño</h2>
  <p>Desde la lista de expedientes, tocando el nombre, se abre su página: el expediente completo en una sola vista, con su foto, su código, sus acompañantes y su historial de servicios y de entradas y salidas.</p>

  <figure>
    <img src="{{ asset('img/docs/guia-perfil.jpg') }}" alt="Página del expediente de un niño: foto, nombre, código, botones de editar expediente, carnet QR y expediente, y las secciones de solicitud y estancia, datos generales y dirección.">
    <figcaption><b>Todo en un lugar.</b> Desde aquí se edita el expediente, se saca la credencial y se descarga el expediente en PDF.</figcaption>
  </figure>
</article>

<article>
  <h2>Cuidar la información de las familias</h2>
  <p>Lo que guarda el sistema es exactamente lo que la Casa ya recaba en papel: datos del menor, su salud y la situación de su familia. Es información delicada y merece el mismo cuidado que el expediente físico.</p>
  <ul>
    <li><strong>Cierra tu sesión</strong> al terminar, sobre todo en computadoras compartidas. Está en tu nombre, arriba a la derecha.</li>
    <li><strong>No tomes fotos de las pantallas</strong> con datos de familias, ni las compartas por mensajería.</li>
    <li><strong>Las credenciales impresas</strong> se entregan a la familia; las que se reimprimen y no se usan, se destruyen.</li>
    <li><strong>Si te equivocaste al registrar algo</strong>, avísale a Dirección en lugar de intentar arreglarlo por tu cuenta. Queda anotado y se corrige bien.</li>
    <li>Los datos son <strong>de la Fundación</strong>. Se respaldan solos todas las noches y no se comparten con nadie más.</li>
  </ul>
</article>

<article>
  <h2>Preguntas que siempre salen</h2>

  <div class="pregunta">
    <h4>¿Y si la familia pierde su credencial?</h4>
    <p>Se vuelve a imprimir desde su expediente. El código es el mismo de siempre y el historial no se pierde.</p>
  </div>
  <div class="pregunta">
    <h4>¿Puedo usar mi teléfono para escanear?</h4>
    <p>Sí. Entras al sistema desde el navegador del teléfono, tocas Escanear y activas la cámara. Es la forma más cómoda en el comedor o en la puerta.</p>
  </div>
  <div class="pregunta">
    <h4>¿Y si no hay cámara, o no lee bien?</h4>
    <p>Tecleas los seis caracteres que están impresos en grande debajo del QR. Queda registrado igual, sin ninguna diferencia.</p>
  </div>
  <div class="pregunta">
    <h4>Escaneé dos veces por error, ¿pasa algo?</h4>
    <p>En comedor, lavandería, escuelita o transporte quedan dos registros; avísale a Dirección para que lo tenga en cuenta al cerrar el mes. En entradas y salidas no pasa nada: el sistema no te deja repetir.</p>
  </div>
  <div class="pregunta">
    <h4>¿Se puede borrar un registro?</h4>
    <p>No desde las pantallas, y es a propósito. La bitácora es la memoria de lo que ocurrió; corregirla a mano la volvería inservible como respaldo.</p>
  </div>
  <div class="pregunta">
    <h4>Me olvidé la contraseña.</h4>
    <p>Dirección la restablece desde Administración y te da una nueva. No hay forma de que alguien la vea: ni siquiera el sistema la guarda tal cual.</p>
  </div>
  <div class="pregunta">
    <h4>¿Y si se va la luz o el internet?</h4>
    <p>Se sigue trabajando como siempre, en papel, y al volver la conexión se capturan los registros tecleando los códigos. Nada se pierde.</p>
  </div>
  <div class="pregunta">
    <h4>¿Quién puede ver los datos de las familias?</h4>
    <p>Solo las cuentas del personal de la Casa, y cada quien únicamente lo que su rol le permite. Sin iniciar sesión no se ve absolutamente nada.</p>
  </div>
</article>

<div class="volver"><a href="{{ route('casita-secreta') }}">← Volver al índice de documentos</a></div>

<footer>
  <p>Guía preparada por: [Nombre del presentador] · ex-voluntario de la Casa Ronald McDonald · Documento independiente, no es un documento oficial de la Fundación.<br>
  Documentos complementarios: <a href="{{ route('casita-proyecto') }}">el proyecto</a> (Dirección de la Casa) · <a href="{{ route('casita-tecnico') }}">diseño del sistema</a> y <a href="{{ route('casita-arquitectura') }}">modelo y arquitectura</a> (Dirección de Tecnología).</p>
</footer>

</div>

</body>
</html>
