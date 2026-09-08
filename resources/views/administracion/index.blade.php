@extends('layouts.skeleton')
@section('title', 'Administración')
@section('description-page', 'Reportes de servicios, cuentas del personal y bitácora de actividad')
@section('content')
<div class="pagina">
    {{-- Los avisos viven al principio de la página: las tres acciones (crear
         cuenta, restablecer contraseña y activar/desactivar) regresan aquí y
         su resultado debe verse sin desplazarse. --}}
    @if (session('exito'))
        <div class="alert alert-success">{{ session('exito') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <small class="d-block">{{ $error }}</small>
            @endforeach
        </div>
    @endif

    <section class="pagina-tarjeta">
        {{-- Sin encabezado de página: la primera tarjeta arranca directo en su
             sección. --}}
        <div class="pagina-cuerpo pagina-cuerpo-primero">
            <div class="bitacora-encabezado">
                <h2><i class="fas fa-chart-column" aria-hidden="true"></i> Reportes de servicios</h2>
            </div>
            <p class="bitacora-sub">Desglose de uso de lavandería, comedor, escuelita y transporte por bloque de tiempo.</p>

            <div class="reportes-controles">
                <div class="reportes-bloques" role="group" aria-label="Bloque de tiempo">
                    <button type="button" class="reportes-bloque activo" data-periodo="dia">Hoy</button>
                    <button type="button" class="reportes-bloque" data-periodo="semana">Semana</button>
                    <button type="button" class="reportes-bloque" data-periodo="mes">Mes</button>
                    <button type="button" class="reportes-bloque" data-periodo="ano">Año</button>
                    <button type="button" class="reportes-bloque" data-periodo="todo">Todo</button>
                </div>
                <form id="reporte_rango" class="reportes-rango">
                    <label for="reporte_desde">Del</label>
                    <input type="date" id="reporte_desde" class="form-control" required>
                    <label for="reporte_hasta">al</label>
                    <input type="date" id="reporte_hasta" class="form-control" required>
                    <button type="submit" class="reportes-aplicar">Aplicar</button>
                </form>
            </div>

            <div class="reportes-resumen">
                <p id="reporte_descripcion" class="reportes-descripcion" aria-live="polite"></p>
                <a id="reporte_excel" class="pagina-boton-primario reportes-excel"
                   href="{{ route('administracion.reportes.excel', ['periodo' => 'dia']) }}">
                    <i class="fas fa-file-excel" aria-hidden="true"></i>
                    <span>Descargar reporte</span>
                </a>
            </div>

            <div class="reportes-kpis">
                <div class="reportes-kpi reportes-kpi-total">
                    <span class="reportes-kpi-valor" id="kpi_total">—</span>
                    <span class="reportes-kpi-nombre">Registros</span>
                </div>
                @foreach ($servicios as $servicio)
                    <div class="reportes-kpi">
                        <span class="reportes-kpi-valor" id="kpi_{{ $servicio->value }}">—</span>
                        <span class="reportes-kpi-nombre">
                            <span class="reportes-kpi-dot" data-servicio="{{ $servicio->value }}" aria-hidden="true"></span>
                            {{ $servicio->etiqueta() }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="reportes-grafico">
                <canvas id="grafico_servicios" role="img" aria-label="Gráfico de uso de servicios por periodo"></canvas>
            </div>
            <p id="reporte_vacio" class="reportes-vacio" hidden>Sin registros de servicios en este periodo.</p>
        </div>
    </section>

    <section class="pagina-tarjeta">
        <div class="pagina-cuerpo">
            <div class="bitacora-encabezado">
                <h2><i class="fas fa-users-cog" aria-hidden="true"></i> Cuentas existentes</h2>
            </div>
            <p class="bitacora-sub">Desactiva, reactiva o restablece la contraseña de cualquier cuenta del personal.</p>

            <div class="table-responsive">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th style="width: 300px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $usuario)
                            <tr>
                                <td>
                                    <a class="enlace-nino" href="{{ route('perfil', $usuario) }}" title="Ver perfil de la cuenta">{{ $usuario->name }}</a>
                                </td>
                                <td>{{ $usuario->email }}</td>
                                <td>{{ $usuario->role->etiqueta() }}</td>
                                <td>
                                    @if ($usuario->is_active)
                                        <span class="bitacora-badge">Activa</span>
                                    @else
                                        <span class="bitacora-badge badge-inactiva">Desactivada</span>
                                    @endif
                                </td>
                                {{-- Acciones apiladas: primero la contraseña, y debajo
                                     el alta/baja de la cuenta. --}}
                                <td>
                                    <div class="cuenta-acciones">
                                        <form method="POST" action="{{ route('administracion.password', $usuario) }}" class="cuenta-password">
                                            @csrf
                                            @method('PATCH')
                                            <input type="password" name="password" class="cuenta-password-campo"
                                                   placeholder="Nueva contraseña" autocomplete="new-password">
                                            <button type="submit" class="cuenta-boton cuenta-boton-neutro">Restablecer</button>
                                        </form>

                                        @unless ($usuario->is(auth()->user()))
                                            <form method="POST" action="{{ route('administracion.toggle', $usuario) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="cuenta-boton {{ $usuario->is_active ? 'cuenta-boton-baja' : 'cuenta-boton-alta' }}">
                                                    {{ $usuario->is_active ? 'Desactivar' : 'Reactivar' }}
                                                </button>
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="pagina-tarjeta">
        <div class="pagina-cuerpo">
            <div class="bitacora-encabezado">
                <h2><i class="fas fa-user-plus" aria-hidden="true"></i> Nueva cuenta</h2>
            </div>
            <p class="bitacora-sub">Alta de una cuenta para el personal de la Casa. Solo los administradores pueden crearlas.</p>

            <form method="POST" action="{{ route('administracion.store') }}" class="formulario-campos">
                @csrf
                <div>
                    <label for="name">Nombre</label>
                    <input id="name" name="name" value="{{ old('name') }}" class="form-control" required>
                </div>
                <div>
                    <label for="email">Correo</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                </div>
                <div>
                    <label for="password">Contraseña</label>
                    <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password">
                </div>
                <div class="campo-2">
                    <label for="role">Rol</label>
                    <select id="role" name="role" class="form-control">
                        <option value="staff" @selected(old('role') === 'staff')>Personal operativo</option>
                        <option value="trabajador_social" @selected(old('role') === 'trabajador_social')>Trabajador social</option>
                        <option value="master" @selected(old('role') === 'master')>Administrador</option>
                    </select>
                </div>
                <div class="formulario-accion">
                    <button type="submit" class="pagina-boton-primario">
                        <i class="fas fa-user-plus" aria-hidden="true"></i>
                        <span>Crear cuenta</span>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <section class="pagina-tarjeta">
        <div class="pagina-cuerpo">
            <div class="bitacora-encabezado">
                <h2><i class="fas fa-clipboard-list" aria-hidden="true"></i> Registro de actividad</h2>
                <select id="filtro_usuario" class="form-control filtro-usuario" aria-label="Filtrar por usuario">
                    <option value="">Todos los usuarios</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                    @endforeach
                </select>
            </div>
            <p class="bitacora-sub">Inicios de sesión, escaneos, descargas, altas y ediciones de todas las cuentas.</p>

            <div class="table-responsive">
                <table class="table data-table" id="data-table-actividad">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
    $(function () {
        // ------------------------------------------------------------------
        // Reportes de servicios
        // ------------------------------------------------------------------
        // Paleta categórica fija (validada para daltonismo): el color sigue al
        // servicio, nunca a su posición, y el orden no se recicla.
        var COLORES = {
            lavanderia: '#2a78d6',
            comedor: '#eb6834',
            escuela: '#1baf7a',
            transporte: '#eda100',
        };

        var filtro = { periodo: 'dia' };
        var grafico = null;

        Object.keys(COLORES).forEach(function (servicio) {
            $('.reportes-kpi-dot[data-servicio="' + servicio + '"]').css('background', COLORES[servicio]);
        });

        function cargarReporte() {
            $.getJSON("{{ route('administracion.reportes.datos') }}", filtro, function (reporte) {
                $('#reporte_descripcion').text(reporte.descripcion);
                $('#reporte_excel').attr('href', "{{ route('administracion.reportes.excel') }}" + '?' + $.param(filtro));
                $('#kpi_total').text(reporte.total);
                reporte.series.forEach(function (serie) {
                    $('#kpi_' + serie.servicio).text(serie.total);
                });
                $('#reporte_vacio').prop('hidden', reporte.total > 0);
                dibujar(reporte);
            });
        }

        function dibujar(reporte) {
            var datasets = reporte.series.map(function (serie) {
                return {
                    label: serie.etiqueta,
                    data: serie.datos,
                    backgroundColor: COLORES[serie.servicio],
                    stack: 'servicios',
                    borderColor: '#ffffff',
                    borderWidth: 1,
                    borderRadius: 3,
                    maxBarThickness: 34,
                };
            });

            if (grafico) {
                grafico.data.labels = reporte.labels;
                grafico.data.datasets = datasets;
                grafico.update();
                return;
            }

            Chart.defaults.font.family = "'Open Sans', system-ui, sans-serif";
            grafico = new Chart(document.getElementById('grafico_servicios'), {
                type: 'bar',
                data: { labels: reporte.labels, datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index' },
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false },
                            border: { color: '#c3c2b7' },
                            ticks: { color: '#898781', autoSkip: true, maxRotation: 45 },
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: { color: '#e1e0d9' },
                            border: { display: false },
                            ticks: { color: '#898781', precision: 0 },
                        },
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, boxWidth: 8, boxHeight: 8, color: '#52514e' },
                        },
                        tooltip: {
                            backgroundColor: '#22345C',
                            titleFont: { weight: '600' },
                            padding: 10,
                        },
                    },
                },
            });
        }

        $('.reportes-bloque').on('click', function () {
            $('.reportes-bloque').removeClass('activo');
            $(this).addClass('activo');
            $('#reporte_rango')[0].reset();
            filtro = { periodo: $(this).data('periodo') };
            cargarReporte();
        });

        $('#reporte_rango').on('submit', function (evento) {
            evento.preventDefault();
            var desde = $('#reporte_desde').val();
            var hasta = $('#reporte_hasta').val();
            if (!desde || !hasta) {
                toastr.warning('Elige ambas fechas para filtrar por rango.');
                return;
            }
            if (desde > hasta) {
                toastr.warning('La fecha inicial debe ser anterior o igual a la final.');
                return;
            }
            $('.reportes-bloque').removeClass('activo');
            filtro = { periodo: 'rango', desde: desde, hasta: hasta };
            cargarReporte();
        });

        cargarReporte();

        // ------------------------------------------------------------------
        // Bitácora de actividad
        // ------------------------------------------------------------------
        var tablaActividad = $('#data-table-actividad').DataTable({
            processing: true,
            serverSide: true,
            order: [[0, 'desc']],
            ajax: {
                url: "{{ route('administracion.actividad') }}",
                data: function (d) { d.usuario = $('#filtro_usuario').val(); }
            },
            columns: [
                {data: 'fecha', name: 'fecha'},
                {data: 'usuario', name: 'usuario', orderable: false, searchable: false},
                {data: 'accion_etiqueta', name: 'accion', orderable: false, searchable: false},
                {data: 'detalle', name: 'detalle', orderable: false},
            ]
        });

        $('#filtro_usuario').on('change', function () {
            tablaActividad.ajax.reload();
        });
    });
</script>
@endsection
