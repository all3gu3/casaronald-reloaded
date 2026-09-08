{{-- Barra de navegación superior: persistente, blanca y con el logo a la izquierda.
     El logo lleva al inicio; los enlaces conservan sus mismas rutas. --}}
<nav class="topbar">
    <a href="{{ route('inicio') }}" class="topbar-logo" aria-label="Ir al inicio">
        <img src="{{ asset('img/RMHC_Mexico_logo.png') }}" alt="RMHC México">
    </a>

    <button type="button" class="topbar-hamburguesa" id="topbar-hamburguesa"
            aria-label="Abrir menú" aria-expanded="false" aria-controls="topbar-enlaces">
        <i class="fa fa-bars"></i>
    </button>

    {{-- Enlaces a la izquierda, junto al logo: Escanear abre el grupo, seguido de
         Expedientes y Bitácora; Administración y el menú del usuario van a la derecha. --}}
    <div class="topbar-enlaces" id="topbar-enlaces">
        @can('escanear')
            <button type="button" class="topbar-escanear js-abrir-escanear">
                <i class="fas fa-qrcode"></i><span>Escanear</span>
            </button>
        @endcan
        @if (Route::has('expedientes'))
            <a href="{{ route('expedientes') }}" class="topbar-enlace {{ request()->routeIs('expedientes') ? 'activo' : '' }}">
                <i class="fa fa-folder-open"></i><span>Expedientes</span>
            </a>
        @endif
        @if (Route::has('registros'))
            <a href="{{ route('registros') }}" class="topbar-enlace {{ request()->routeIs('registros', 'registros-*') ? 'activo' : '' }}">
                <i class="fas fa-clipboard-list"></i><span>Bitácora</span>
            </a>
        @endif
        {{-- Administración y menú del usuario, empujados al extremo derecho. --}}
        <div class="topbar-derecha">
            @can('manage-users')
                <a href="{{ route('administracion.index') }}" class="topbar-enlace {{ request()->routeIs('administracion.*') ? 'activo' : '' }}">
                    <i class="fas fa-user-shield"></i><span>Administración</span>
                </a>
            @endcan

            {{-- Menú del usuario loggueado: perfil (cuando exista la ruta) y salir. --}}
            <div class="topbar-usuario" id="topbar-usuario">
                <button type="button" class="topbar-usuario-boton" id="topbar-usuario-boton"
                        aria-haspopup="true" aria-expanded="false" aria-controls="topbar-usuario-menu">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ auth()->user()->name }}</span>
                    <i class="fas fa-caret-down topbar-usuario-caret"></i>
                </button>
                <div class="topbar-usuario-menu" id="topbar-usuario-menu">
                    @if (Route::has('perfil'))
                        <a href="{{ route('perfil') }}" class="topbar-usuario-opcion">
                            <i class="fas fa-id-badge"></i><span>Ver mi perfil</span>
                        </a>
                    @endif
                    <a href="#" class="topbar-usuario-opcion topbar-usuario-salir"
                       onclick="event.preventDefault(); if (confirm('¿Seguro que deseas salir?')) { document.getElementById('form-logout').submit(); }">
                        <i class="fas fa-sign-out-alt"></i><span>Salir</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="form-logout" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</nav>

<script>
    // Menú plegable en pantallas angostas.
    $('#topbar-hamburguesa').on('click', function () {
        var abierto = $('#topbar-enlaces').toggleClass('abierto').hasClass('abierto');
        $(this).attr('aria-expanded', abierto ? 'true' : 'false');
    });

    // Dropdown del usuario: se abre con el botón y se cierra al hacer clic fuera.
    $('#topbar-usuario-boton').on('click', function (event) {
        event.stopPropagation();
        var abierto = $('#topbar-usuario').toggleClass('abierto').hasClass('abierto');
        $(this).attr('aria-expanded', abierto ? 'true' : 'false');
    });
    $(document).on('click', function () {
        $('#topbar-usuario').removeClass('abierto');
        $('#topbar-usuario-boton').attr('aria-expanded', 'false');
    });
</script>
