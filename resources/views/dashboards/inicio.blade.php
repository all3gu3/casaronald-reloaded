<div class="container-2">
    <section class="inicio">
        <div class="inicio-hero">
            <div class="slides">
                <img src="{{ asset('img/a.png') }}" alt="Comedor de la Casa Ronald McDonald">
                <img src="{{ asset('img/b.jpg') }}" alt="Casa Ronald McDonald México">
                <img src="{{ asset('img/c.jpg') }}" alt="Fachada de la Casa Ronald McDonald México">
                <img src="{{ asset('img/d.jpg') }}" alt="Casa Ronald McDonald México">
            </div>
            <div class="inicio-hero-velo"></div>
            <div class="inicio-hero-texto">
                <h1>¡Bienvenidos!</h1>
                <p>Fundación Infantil Ronald McDonald México</p>
            </div>
        </div>

        <div class="inicio-modulos">
            <h2>Módulos</h2>
            <hr class="inicio-acento">
            <p class="inicio-modulos-sub">Elige un módulo para comenzar</p>

            {{-- Escanear va arriba como pastilla a lo ancho y abre la ventana
                 flotante de escaneo; debajo, los dos módulos cuadrados. Si el
                 usuario no puede escanear, la fila queda sola sin hueco. --}}
            <div class="inicio-botones">
                @can('escanear')
                    <button type="button" class="inicio-boton inicio-boton-escanear js-abrir-escanear">
                        <i class="fas fa-qrcode" aria-hidden="true"></i>
                        <span>Escanear</span>
                    </button>
                @endcan

                <div class="inicio-botones-fila">
                    <a href="{{ route('expedientes') }}" class="inicio-boton">
                        <i class="fas fa-folder-open" aria-hidden="true"></i>
                        <span>Expedientes</span>
                    </a>

                    <a href="{{ route('registros') }}" class="inicio-boton">
                        <i class="fas fa-clipboard-list" aria-hidden="true"></i>
                        <span>Bitácora</span>
                    </a>
                </div>
            </div>
        </div>

        <footer class="inicio-pie">
            <p>
                © RMHC

                Las siguientes marcas comerciales utilizadas aquí son propiedad de la Corporación McDonald’s y sus filiales; McDonald’s, Ronald McDonald House Charities, el logo de Ronald McDonald House Charities, el término RMHC, Casa Ronald McDonald, Sala Familiar Ronald McDonald, Unidad o Clínica Móvil Ronald McDonald y el eslogan “manteniendo a las familias cerca.”

                Fundación Infantil Ronald McDonald es una organización sin fines de lucro, con autorización de donataria, según el DOF de 19 de enero de 2018.
            </p>
        </footer>
    </section>
</div>
<script>
    $(function () {
        $(".slides").slidesjs({
            width: 1100,
            height: 380,
            navigation: {active: false},
            pagination: {active: true, effect: "slide"},
            play: {
                active: false,
                effect: "slide",
                interval: 5000,
                auto: true,
                swap: false,
                pauseOnHover: true,
                restartDelay: 2500
            }
        });
    });
</script>
