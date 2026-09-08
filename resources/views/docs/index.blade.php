<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Expediente Digital | Documentación</title>
<link rel="icon" href="{{ asset('favicon.ico') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Raleway:wght@600;700;800&family=Open+Sans:wght@400;600&display=swap">
<style>
  :root {
    --azul: #4872AE;
    --azul-oscuro: #38598A;
    --amarillo: #FFC72C;
    --rojo: #F64740;
    --texto: #33383F;
    --gris: #666666;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    background: var(--azul);
    color: var(--texto);
    font-family: "Open Sans", "Segoe UI", sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
  }
  .panel {
    background: rgba(255,255,255,0.97);
    border-radius: 14px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.25);
    max-width: 860px;
    width: 100%;
    padding: 3rem 2.5rem;
    text-align: center;
  }
  img.logo { max-width: 220px; height: auto; margin-bottom: 1rem; }
  h1 {
    font-family: "Raleway", sans-serif;
    font-weight: 800;
    font-size: 2.2rem;
    margin: 0 0 .3rem;
    color: var(--azul-oscuro);
  }
  h1 span { color: var(--amarillo); }
  .sub { color: var(--gris); margin: 0 auto 2.2rem; max-width: 54ch; }
  .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.4rem; }
  a.tarjeta {
    display: block;
    height: 100%;
    text-decoration: none;
    color: inherit;
    background: #fff;
    border: 2px solid #E8E6DF;
    border-radius: 12px;
    padding: 1.8rem 1.5rem;
    text-align: left;
    transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
  }
  a.tarjeta:hover { transform: translateY(-4px); border-color: var(--amarillo); box-shadow: 0 10px 24px rgba(0,0,0,0.12); }
  .tarjeta .tag {
    display: inline-block;
    font-family: "Raleway", sans-serif;
    font-weight: 700;
    font-size: .72rem;
    letter-spacing: .1em;
    text-transform: uppercase;
    padding: .25rem .7rem;
    border-radius: 999px;
    margin-bottom: .9rem;
  }
  .tarjeta.dir .tag { background: var(--amarillo); color: #5c4500; }
  .tarjeta.tec .tag { background: var(--azul); color: #fff; }
  .tarjeta.eq .tag { background: var(--rojo); color: #fff; }
  .tarjeta h2 { font-family: "Raleway", sans-serif; font-weight: 800; font-size: 1.25rem; margin: 0 0 .5rem; color: var(--texto); }
  .tarjeta p { margin: 0; color: var(--gris); font-size: .95rem; line-height: 1.55; }
  hr.brand { border: none; height: 3px; background: var(--rojo); width: 70px; margin: 2.2rem auto 1.2rem; border-radius: 2px; }
  .nota { font-size: .82rem; color: var(--gris); margin: 0; }
</style>
</head>
<body>
  <main class="panel">
    <img class="logo" src="{{ asset('img/RMHC_Mexico_logo.png') }}" alt="Fundación Infantil Ronald McDonald México">
    <h1>Expediente <span>Digital</span></h1>
    <p class="sub">Documentación del sistema de expediente y servicios de la Casa Ronald McDonald. Tres documentos complementarios, cada uno escrito para su lector.</p>
    <div class="cards">
      <a class="tarjeta dir" href="{{ route('documentacion-proyecto') }}">
        <span class="tag">Para la Dirección de la Casa</span>
        <h2>El proyecto</h2>
        <p>Qué hace el sistema por las familias y por el equipo de la Casa, contado con los casos de uso del día a día: llegada, credencial, comedor, escuelita, lavandería, transporte y reportes. Sin tecnicismos.</p>
      </a>
      <a class="tarjeta tec" href="{{ route('documentacion-tecnico') }}">
        <span class="tag">Para la Dirección de Tecnología</span>
        <h2>El documento técnico</h2>
        <p>El sistema por dentro, tal como está hoy: alcance funcional, modelo de datos tabla por tabla, diagrama de clases, la infraestructura sobre la que corre, los casos de uso con sus flujos y los términos de la donación.</p>
      </a>
      <a class="tarjeta eq" href="{{ route('documentacion-guia') }}">
        <span class="tag">Para el equipo de la Casa</span>
        <h2>Guía del equipo</h2>
        <p>Cómo se usa el sistema, paso a paso y con las pantallas reales: entrar, registrar a una familia, imprimir su credencial, escanear en cada servicio y sacar los números del mes.</p>
      </a>
    </div>
    <hr class="brand">
    <p class="nota">Documento de propuesta independiente, preparado por un ex-voluntario. No es un documento oficial de la Fundación Infantil Ronald McDonald México.</p>
  </main>
</body>
</html>
