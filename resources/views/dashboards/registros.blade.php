<div class="pagina">
    <section class="pagina-tarjeta">
        <div class="pagina-encabezado">
            <h1>Bitácora</h1>
            <hr class="pagina-acento">
            <p class="pagina-sub">Los movimientos más recientes de la casa, de un vistazo</p>
        </div>

        <div class="pagina-cuerpo">
            {{-- Entradas y salidas: los últimos 3 movimientos --}}
            <div class="bitacora-seccion">
                <div class="bitacora-encabezado">
                    <h2><i class="fas fa-arrow-right-arrow-left" aria-hidden="true"></i> Entradas y salidas</h2>
                    <a href="{{ route('registros-entradas-salidas') }}" class="bitacora-ver">
                        Ver la bitácora completa <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                @if ($ultimasEntradas->isEmpty())
                    <p class="bitacora-vacio">Todavía no hay entradas ni salidas registradas.</p>
                @else
                    <div class="table-responsive">
                        <table class="table data-table">
                            <thead>
                                <tr>
                                    <th>Niño</th>
                                    <th>Código</th>
                                    {{-- Verde y rojo, como entrada y salida en la ventana de escaneo. --}}
                                    <th class="bitacora-th-entrada"><i class="fas fa-sign-in-alt" aria-hidden="true"></i> Entrada</th>
                                    <th class="bitacora-th-salida"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Salida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ultimasEntradas as $registro)
                                    <tr>
                                        <td>
                                            @if ($registro->nino)
                                                <a class="enlace-nino" href="{{ route('ninos.perfil', $registro->nino) }}">{{ $registro->nino->nombreCompleto() }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td><span class="bitacora-codigo">{{ $registro->qr }}</span></td>
                                        <td>{{ $registro->entrada?->legible() ?? '—' }}</td>
                                        <td>
                                            @if ($registro->salida)
                                                {{ $registro->salida->legible() }}
                                            @else
                                                <span class="bitacora-badge">En casa</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Servicios: una tabla de vista previa por cada servicio --}}
            <div class="bitacora-seccion">
                <div class="bitacora-encabezado">
                    <h2><i class="fas fa-clipboard-list" aria-hidden="true"></i> Servicios</h2>
                </div>
                <p class="bitacora-sub">Últimos registros de cada servicio de la casa.</p>

                <div class="bitacora-servicios-grid">
                    @foreach ($serviciosPrevia as $panel)
                        {{-- data-servicio pinta el panel con el color del servicio
                             (mismo mapa que la ventana de escaneo). --}}
                        <div class="bitacora-panel" data-servicio="{{ $panel['servicio']->value }}">
                            <h3 class="bitacora-panel-titulo">
                                <i class="fas {{ $panel['servicio']->icono() }}" aria-hidden="true"></i>
                                {{ $panel['servicio']->etiqueta() }}
                            </h3>

                            @if ($panel['registros']->isEmpty())
                                <p class="bitacora-vacio">Todavía no hay registros.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table data-table">
                                        <thead>
                                            <tr>
                                                <th>Niño</th>
                                                <th>Código</th>
                                                <th>Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($panel['registros'] as $registro)
                                                <tr>
                                                    <td>
                                                        @if ($registro->nino)
                                                            <a class="enlace-nino" href="{{ route('ninos.perfil', $registro->nino) }}">{{ $registro->nino->nombreCompleto() }}</a>
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td><span class="bitacora-codigo">{{ $registro->qr }}</span></td>
                                                    <td>{{ $registro->created_at->legible() }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            <div class="bitacora-panel-pie">
                                <a href="{{ route($panel['servicio']->ruta()) }}" class="bitacora-ver">
                                    Ver la bitácora completa <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
