<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Casita Digital | El proyecto</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Raleway:wght@600;700;800&family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400&display=swap">
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
  }
  * { box-sizing: border-box; }
  body { margin: 0; background: var(--azul); font-family: "Open Sans", "Segoe UI", sans-serif; color: var(--texto); line-height: 1.65; }
  .lienzo { max-width: 920px; margin: 0 auto; padding: 2.5rem 1.2rem 4rem; }

  header.portada {
    background: rgba(255,255,255,0.97);
    border-radius: 14px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.25);
    padding: 3rem 3rem 2.5rem;
    margin-bottom: 1.6rem;
  }
  img.logo { max-width: 200px; height: auto; margin-bottom: 1.4rem; }
  .eyebrow {
    font-family: "Raleway", sans-serif; font-weight: 700; font-size: .78rem;
    letter-spacing: .14em; text-transform: uppercase; color: var(--azul);
    margin: 0 0 .6rem;
  }
  h1 { font-family: "Raleway", sans-serif; font-weight: 800; font-size: 2.6rem; line-height: 1.1; margin: 0 0 .8rem; color: var(--texto); }
  h1 .amarillo { color: var(--amarillo); }
  .bajada { font-size: 1.15rem; color: var(--gris); max-width: 60ch; margin: 0; }
  .sello {
    display: inline-block; margin-top: 1.5rem;
    background: var(--amarillo); color: #5c4500;
    font-family: "Raleway", sans-serif; font-weight: 700; font-size: .85rem;
    padding: .4rem 1rem; border-radius: 999px;
  }

  article {
    background: rgba(255,255,255,0.97);
    border-radius: 14px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.18);
    padding: 2.4rem 3rem;
    margin-bottom: 1.6rem;
  }
  h2 { font-family: "Raleway", sans-serif; font-weight: 800; font-size: 1.6rem; margin: 0 0 1rem; color: var(--azul-oscuro); }
  h2::after { content: ""; display: block; width: 56px; height: 4px; background: var(--rojo); border-radius: 2px; margin-top: .5rem; }
  h3 { font-family: "Raleway", sans-serif; font-weight: 700; font-size: 1.15rem; margin: 1.6rem 0 .5rem; color: var(--texto); }
  p { margin: 0 0 1rem; max-width: 70ch; }
  ul { margin: 0 0 1rem; padding-left: 1.3rem; max-width: 68ch; }
  li { margin-bottom: .45rem; }
  li::marker { color: var(--azul); }
  strong { font-weight: 700; }
  .qr {
    font-family: "Courier New", monospace; font-weight: 700; letter-spacing: .08em;
    background: #FFF3D1; color: var(--amarillo-tinta); padding: .05em .45em; border-radius: 4px;
  }

  .cifras { display: flex; flex-wrap: wrap; gap: 1rem; margin: 1.4rem 0; }
  .cifra {
    flex: 1 1 160px; background: var(--crema); border: 1px solid var(--borde);
    border-radius: 10px; padding: 1rem 1.2rem; text-align: center;
  }
  .cifra b { display: block; font-family: "Raleway", sans-serif; font-weight: 800; font-size: 1.7rem; color: var(--azul); }
  .cifra span { font-size: .85rem; color: var(--gris); }

  .momento {
    display: flex; gap: 1.2rem; margin-bottom: 1.6rem; align-items: flex-start;
  }
  .momento .num {
    flex-shrink: 0; width: 2.6rem; height: 2.6rem; border-radius: 50%;
    background: var(--amarillo); color: #5c4500;
    font-family: "Raleway", sans-serif; font-weight: 800; font-size: 1.2rem;
    display: flex; align-items: center; justify-content: center;
  }
  .momento h3 { margin: .2rem 0 .4rem; }
  .momento p { margin-bottom: .6rem; }
  .escena {
    background: var(--crema); border-left: 4px solid var(--amarillo);
    border-radius: 0 8px 8px 0; padding: .8rem 1.1rem; font-style: italic;
    color: var(--gris); max-width: 62ch; margin: .6rem 0 0;
  }

  table { border-collapse: collapse; width: 100%; font-size: .95rem; margin: 0 0 1rem; }
  th, td { text-align: left; padding: .55rem .8rem; border-bottom: 1px solid var(--borde); vertical-align: top; }
  th { font-family: "Raleway", sans-serif; font-size: .78rem; text-transform: uppercase; letter-spacing: .07em; color: var(--gris); background: var(--crema); }
  tr:last-child td { border-bottom: none; }
  .tabla-scroll { overflow-x: auto; border: 1px solid var(--borde); border-radius: 8px; margin-bottom: 1rem; }
  .tabla-scroll table { margin: 0; }

  .compromiso { border-left: 4px solid var(--azul); background: #EFF3FA; border-radius: 0 8px 8px 0; padding: 1rem 1.3rem; margin: 1rem 0; }
  .compromiso p { margin: 0; max-width: none; }

  footer {
    text-align: center; color: rgba(255,255,255,0.85); font-size: .85rem; padding: 1rem 0 0;
  }
  footer a { color: var(--amarillo); }
  .volver { text-align: center; margin-top: 1rem; }
  .volver a {
    color: #fff; text-decoration: none; font-family: "Raleway", sans-serif; font-weight: 700;
    border: 2px solid rgba(255,255,255,0.6); border-radius: 999px; padding: .5rem 1.4rem; display: inline-block;
  }
  .volver a:hover { background: rgba(255,255,255,0.12); }
  @media (max-width: 640px) { article, header.portada { padding: 1.8rem 1.4rem; } h1 { font-size: 2rem; } }
</style>
</head>
<body>
<div class="lienzo">

<header class="portada">
  <img class="logo" src="{{ asset('img/RMHC_Mexico_logo.png') }}" alt="Fundación Infantil Ronald McDonald México">
  <p class="eyebrow">Propuesta de donación tecnológica · Documento para la Dirección de la Casa</p>
  <h1>Casita <span class="amarillo">Digital</span></h1>
  <p class="bajada">Un sistema hecho a la medida de la operación diaria de la Casa Ronald McDonald: el expediente de cada familia, una credencial con código QR y el registro de cada servicio con un solo escaneo.</p>
  <span class="sello">Donación sin costo · sin licencias · sin condiciones</span>
</header>

<article>
  <h2>Por qué existe este proyecto</h2>
  <p>Quien presenta este documento fue <strong>voluntario de la Casa Ronald McDonald</strong> hace algunos años. De esa experiencia nació este proyecto: un sistema construido alrededor de cómo trabaja realmente la Casa — no un software genérico adaptado a la fuerza.</p>
  <p>La labor de la Fundación habla por sí misma. Desde 1997 en México, con tres Casas (Ciudad de México, Puebla y Tlalnepantla) y seis Salas Familiares en hospitales públicos, la Fundación acompaña cada mes a <strong>más de 540 familias</strong> con hospedaje, alimentación, transporte diario al hospital y acompañamiento emocional. Tan solo la Casa de Puebla, abierta en octubre de 2014, ha recibido a las familias de <strong>más de 12,400 niñas y niños</strong> del interior del estado y de todo el país.</p>
  <div class="cifras">
    <div class="cifra"><b>540+</b><span>familias acompañadas al mes en las tres Casas</span></div>
    <div class="cifra"><b>17,000+</b><span>familias apoyadas durante 2025</span></div>
    <div class="cifra"><b>12,400+</b><span>niñas y niños atendidos por la Casa Puebla desde 2014</span></div>
  </div>
  <p>Detrás de cada una de esas familias hay registros: la solicitud que llega desde Trabajo Social del hospital, el estudio socioeconómico, la lista de acompañantes, las comidas, la lavandería, la escuelita, los traslados. Hoy buena parte de esa información vive en papel y en hojas sueltas. Este proyecto propone darle una casa digital — segura, ordenada y siempre disponible.</p>
</article>

<article>
  <h2>Cómo funciona, contado en cuatro momentos</h2>

  <div class="momento">
    <div class="num">1</div>
    <div>
      <h3>Llega una familia: el expediente digital</h3>
      <p>Cuando Trabajo Social canaliza a una familia, la trabajadora social captura la solicitud una sola vez, en un formulario que sigue el orden del expediente que ya conocen: datos del niño o niña (nombre, fecha de nacimiento, hospital que lo atiende, diagnóstico, médico tratante, alergias, tipo de dieta), su lugar de origen (estado, municipio y localidad — la Casa recibe familias de los 32 estados), y el perfil socioeconómico que la Fundación ya levanta: escolaridad, ocupación, ingreso mensual, zona rural o urbana, e incluso el <strong>dialecto o lengua</strong>, para acompañar mejor a las familias de comunidades indígenas.</p>
      <p>Cada acompañante queda ligado al expediente del niño con su parentesco, edad, estado de salud y situación laboral — la información que hoy sustenta prórrogas y reportes.</p>
      <p>El sistema ya viene cargado con los catálogos de la operación real: <strong>17 hospitales</strong> con los que trabaja la Casa Puebla (Hospital para el Niño Poblano, CRIT, IMSS, Cruz Roja, entre otros), <strong>14 tipos de parentesco</strong>, <strong>7 tipos de dieta</strong> (normal, blanda, renal, para lactantes…), <strong>11 niveles de escolaridad</strong> y el directorio de trabajadoras sociales.</p>
      <p class="escena">María llega desde la Sierra Norte con su hijo de 6 años, canalizada por Trabajo Social del Hospital para el Niño Poblano. En quince minutos su expediente está completo y no volverá a llenar el mismo papel dos veces.</p>
    </div>
  </div>

  <div class="momento">
    <div class="num">2</div>
    <div>
      <h3>La credencial con código QR</h3>
      <p>Al terminar el registro, el sistema imprime una <strong>credencial personalizada</strong> con los colores y el logotipo de la Fundación: el nombre del niño, su lugar de origen, su hospital, la lista de sus acompañantes y un código QR único de seis caracteres — por ejemplo <span class="qr">A1B2C3</span>. Esa tarjeta es la llave de la familia dentro de la Casa durante toda su estancia.</p>
      <p class="escena">La familia guarda su credencial junto con el carnet del hospital. Ya no hay que preguntar el nombre completo ni buscar en la libreta: la tarjeta habla por ellos.</p>
    </div>
  </div>

  <div class="momento">
    <div class="num">3</div>
    <div>
      <h3>El día a día: un escaneo por servicio</h3>
      <p>Cada servicio de la Casa registra su uso con un escaneo de la credencial. Sin filas, sin listas a mano:</p>
      <ul>
        <li><strong>Comedor</strong> — quién tomó cada una de las tres comidas del día, respetando el tipo de dieta indicado en su expediente.</li>
        <li><strong>Lavandería</strong> — control de uso del servicio por familia.</li>
        <li><strong>Escuelita</strong> — asistencia de las niñas y niños a las actividades educativas.</li>
        <li><strong>Transporte</strong> — los traslados diarios al hospital y de regreso.</li>
        <li><strong>Entradas y salidas</strong> — quién está dentro de la Casa en cada momento, un dato de seguridad esencial en un albergue que cuida menores.</li>
      </ul>
      <p class="escena">Son las 7:40 de la mañana. En la puerta del comedor, la encargada escanea las credenciales conforme entran las familias. Al servir el desayuno ya sabe que el niño de la habitación 3 tiene dieta renal.</p>
    </div>
  </div>

  <div class="momento">
    <div class="num">4</div>
    <div>
      <h3>Los números de la Casa, siempre a la mano</h3>
      <p>Todo lo que se escanea se convierte en información ordenada: cuántas familias se hospedaron este mes, cuántas comidas sirvió el comedor, cuántos traslados hizo la camioneta, cuántos niños asistieron a la escuelita. Los concentrados que hoy se arman a mano para el patronato, los donantes y los reportes de impacto salen del sistema en minutos, con cifras consistentes.</p>
      <p class="escena">Fin de mes. En lugar de sumar renglones de tres libretas distintas, la Dirección abre el concentrado del comedor y copia la cifra al informe del patronato.</p>
    </div>
  </div>
</article>

<article>
  <h2>Qué incluye la donación</h2>
  <ul>
    <li><strong>El sistema completo</strong>, entregado como software libre a nombre de la Fundación: sin costo de compra, sin licencias anuales, sin dependencia de un proveedor.</li>
    <li><strong>La puesta en marcha</strong>: instalación, carga de catálogos reales de la Casa y diseño de la credencial con la identidad de la Fundación.</li>
    <li><strong>Capacitación</strong> para trabajadoras sociales y personal operativo — el sistema se maneja con un navegador y un lector de códigos, sin conocimientos técnicos.</li>
    <li><strong>Acompañamiento durante el piloto</strong> y ajustes conforme a la retroalimentación del equipo.</li>
  </ul>
  <h3>Qué necesitaría la Casa</h3>
  <ul>
    <li>Una computadora con internet para el registro y una impresora para las credenciales.</li>
    <li>Un lector de códigos QR (o un teléfono) en cada punto de servicio.</li>
    <li>La decisión, junto con el área de tecnología, de dónde se alojará la información.</li>
  </ul>
  <div class="compromiso">
    <p><strong>Compromiso con la privacidad.</strong> El sistema guarda únicamente la información que la Fundación ya recaba en papel, y se propone operar bajo la Ley Federal de Protección de Datos Personales, con aviso de privacidad, accesos restringidos por rol y respaldos cifrados. Los datos de las niñas, niños y sus familias son de la Fundación y solo de la Fundación. El documento técnico que acompaña a esta propuesta detalla cómo.</p>
  </div>
</article>

<article>
  <h2>Siguientes pasos que proponemos</h2>
  <ul>
    <li><strong>Una demostración en vivo</strong> con el equipo de la Casa, usando datos de ejemplo (en esta demo, la familia de prueba es — cómo no — la del pequeño <em>Ronald Mc Uno</em>).</li>
    <li><strong>Revisión técnica</strong> por parte de la Dirección de Tecnología, con el documento técnico complementario.</li>
    <li><strong>Un piloto de cuatro semanas en un solo servicio</strong> — sugerimos el comedor — para medir el ahorro de tiempo real antes de decidir nada más.</li>
  </ul>
  <p>La Casa dedica su energía a las familias; este proyecto solo quiere devolverle unas horas a la semana para eso mismo.</p>
</article>

<div class="volver"><a href="{{ route('casita-secreta') }}">← Volver al índice de documentos</a></div>
<footer>
  <p>Presentado por: [Nombre del presentador] · ex-voluntario de la Casa Ronald McDonald · Propuesta independiente, no es un documento oficial de la Fundación.<br>
  Documento complementario: <a href="{{ route('casita-tecnico') }}">versión técnica para la Dirección de Tecnología</a>.</p>
</footer>

</div>
</body>
</html>
