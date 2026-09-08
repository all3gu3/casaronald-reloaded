@extends('layouts.skeleton')
@section('title', $nino->nombreCompleto())
@section('description-page', 'Expediente y bitácora individual del niño')
@section('content')
    <div class="pagina">
        @include('forms.nino')

        <section class="pagina-tarjeta">
            <div class="pagina-encabezado">
                <img src="{{ $nino->foto !== null ? Storage::disk('public')->url($nino->foto) : asset('img/avatar-nino.svg') }}"
                     alt="Foto de {{ $nino->nombreCompleto() }}" class="perfil-foto">
                <h1>{{ $nino->nombreCompleto() }}</h1>
                <hr class="pagina-acento">
                <p class="pagina-sub">
                    {{ $nino->sexo }} · {{ $nino->edad !== null ? $nino->edad.' años' : 'Edad sin registrar' }} ·
                    Código <strong>{{ $nino->qr }}</strong> · {{ $nino->estatus_estancia }}
                </p>
            </div>

            <div class="pagina-cuerpo">
                {{-- Descargas: el carnet lo baja cualquier usuario; el reporte PDF
                     sale de la casa y la ruta exige el permiso descargar-expediente.
                     Los botones abren la vista previa compartida (parts/preview);
                     la descarga real sale de los botones azules del modal. --}}
                <div class="pagina-acciones perfil-acciones">
                    <button type="button" class="bitacora-ver perfil-boton-editar" onclick="editarNino({{ $nino->id }});" data-toggle="modal" data-target="#myModal" data-backdrop="false">
                        <i class="fas fa-pen" aria-hidden="true"></i> Editar expediente
                    </button>
                    <button type="button" class="bitacora-ver perfil-boton-descarga" onclick="abrirPreviewCarnet({{ $nino->id }});">
                        <i class="fa fa-qrcode" aria-hidden="true"></i> Carnet QR
                    </button>
                    @can('descargar-expediente')
                        <button type="button" class="bitacora-ver perfil-boton-descarga" onclick="abrirPreviewExpediente();">
                            <i class="fas fa-folder-open" aria-hidden="true"></i> Expediente
                        </button>
                    @endcan
                </div>

                {{-- Resumen del expediente: mismas secciones que el modal de consulta,
                     pero rendereadas en el servidor. --}}
                <div class="modal-seccion">
                    <h5>Solicitud y estancia</h5>
                    <div class="expediente-campos">
                        <div><small>Fecha de solicitud</small><span>{{ $nino->fecha_solicitud?->legibleFecha() ?? '—' }}</span></div>
                        <div><small>Estatus de la estancia</small><span>{{ $nino->estatus_estancia ?: '—' }}</span></div>
                        <div><small>Hospital</small><span>{{ $nino->hospital->hospital }}</span></div>
                        <div><small>Servicio</small><span>{{ $nino->servicio ?: '—' }}</span></div>
                        <div><small>Fecha de ingreso</small><span>{{ $nino->fecha_ingreso?->legibleFecha() ?? '—' }}</span></div>
                        <div><small>Fecha de salida</small><span>{{ $nino->fecha_salida?->legibleFecha() ?? '—' }}</span></div>
                    </div>
                </div>

                <div class="modal-seccion">
                    <h5>Datos generales</h5>
                    <div class="expediente-campos">
                        <div><small>Fecha de nacimiento</small><span>{{ $nino->fecha_nacimiento?->legibleFecha() ?? '—' }}</span></div>
                        <div><small>Dialecto</small><span>{{ $nino->dialecto ?: '—' }}</span></div>
                        <div><small>Teléfono 1</small><span>{{ $nino->primer_telefono ?: '—' }}</span></div>
                        <div><small>Teléfono 2</small><span>{{ $nino->segundo_telefono ?: '—' }}</span></div>
                    </div>
                </div>

                <div class="modal-seccion">
                    <h5>Dirección</h5>
                    <div class="expediente-campos">
                        <div><small>Calle y número</small><span>{{ trim($nino->calle.' '.$nino->numero) ?: '—' }}</span></div>
                        <div><small>Colonia</small><span>{{ $nino->colonia ?: '—' }}</span></div>
                        <div><small>Localidad</small><span>{{ $nino->localidad ?: '—' }}</span></div>
                        <div><small>Municipio</small><span>{{ $nino->municipio ?: '—' }}</span></div>
                        <div><small>Estado</small><span>{{ $nino->estado->estado }}</span></div>
                        <div><small>País</small><span>{{ $nino->pais->pais }}</span></div>
                        <div><small>CP</small><span>{{ $nino->cp ?: '—' }}</span></div>
                        <div><small>Zona</small><span>{{ $nino->zona->zona }}</span></div>
                    </div>
                </div>

                <div class="modal-seccion">
                    <h5>Datos socioeconómicos</h5>
                    <div class="expediente-campos">
                        <div><small>Escolaridad</small><span>{{ $nino->escolaridad->escolaridad }}</span></div>
                        <div><small>Clasificación social</small><span>{{ $nino->clasificacionSocial->clasificacion_social }}</span></div>
                        <div><small>Salario mínimo</small><span>{{ $nino->salarioMinimo->salario_minimo }}</span></div>
                        <div><small>Trabajador social</small><span>{{ $nino->trabajadorSocial->trabajador_social }}</span></div>
                    </div>
                </div>

                <div class="modal-seccion">
                    <h5>Datos médicos</h5>
                    <div class="expediente-campos">
                        <div><small>Médico</small><span>{{ $nino->medico ?: '—' }}</span></div>
                        <div><small>Diagnóstico</small><span>{{ $nino->diagnostico ?: '—' }}</span></div>
                        <div><small>Alergias en alimentos</small><span>{{ $nino->alerg_alimentos ?: '—' }}</span></div>
                        <div><small>Alergias en medicamentos</small><span>{{ $nino->alerg_medicamentos ?: '—' }}</span></div>
                        <div><small>Tipo de dieta</small><span>{{ $nino->tipoDieta->tipo_dieta }}</span></div>
                        <div><small>Tratamientos</small><span>{{ $nino->tiposTratamiento->pluck('tipo_tratamiento')->implode(', ') ?: '—' }}</span></div>
                    </div>
                </div>

                <div class="modal-seccion">
                    <h5>Observaciones</h5>
                    <p class="expediente-observaciones">{{ $nino->observaciones ?: '—' }}</p>
                </div>

                <div class="modal-seccion">
                    <h5>Acompañantes</h5>
                    @if ($nino->acompanantes->isEmpty())
                        <p class="bitacora-vacio">Sin acompañantes registrados.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table data-table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Parentesco</th>
                                        <th>Edad</th>
                                        <th>Sexo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nino->acompanantes as $acompanante)
                                        <tr>
                                            <td>{{ $acompanante->nombreCompleto() }}</td>
                                            <td>{{ $acompanante->parentesco->parentesco }}</td>
                                            <td>{{ $acompanante->edad ?? '—' }}</td>
                                            <td>{{ $acompanante->sexo ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Bitácora del niño: la misma vista rápida que /registros, pero
                     con tablas paginadas (5 por página) en lugar del enlace a la
                     bitácora completa. --}}
                <div class="bitacora-seccion">
                    <div class="bitacora-encabezado">
                        <h2><i class="fas fa-arrow-right-arrow-left" aria-hidden="true"></i> Entradas y salidas</h2>
                    </div>
                    <p class="bitacora-sub">Solo los movimientos de {{ $nino->nombre }}.</p>

                    <div class="table-responsive">
                        <table class="table data-table" id="data-table-perfil-entradas">
                            <thead>
                                <tr>
                                    <th>Entrada</th>
                                    <th>Salida</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bitacora-seccion">
                    <div class="bitacora-encabezado">
                        <h2><i class="fas fa-clipboard-list" aria-hidden="true"></i> Servicios</h2>
                    </div>
                    <p class="bitacora-sub">Todos los registros de servicios de {{ $nino->nombre }}, del más reciente al más antiguo.</p>

                    <div class="table-responsive">
                        <table class="table data-table" id="data-table-perfil-servicios">
                            <thead>
                                <tr>
                                    <th>Fecha y hora</th>
                                    <th>Servicio</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        @include('parts.preview')
    </div>
    <script>
        // Tras guardar una edición, el resumen (rendereado en el servidor)
        // quedaría desactualizado: se recarga la página completa.
        function alGuardarNino() {
            window.location.reload();
        }

        @can('descargar-expediente')
        // El expediente sí es un PDF embebido en iframe; solo ofrece la
        // descarga en PDF (no hay versión en imagen).
        function abrirPreviewExpediente() {
            abrirPreview({
                vista: "{{ route('expedientes.pdf', $nino) }}",
                imagen: false,
                descargaPdf: "{{ route('expedientes.pdf', $nino) }}?download=1",
            });
        }
        @endcan

        // Tablas sin buscador: la bitácora ya llega filtrada al niño por el
        // parámetro `nino` del feed. El pageLength lo fija cada tabla.
        var opcionesCompactas = {
            processing: true,
            serverSide: true,
            pageLength: 5,
            lengthChange: false,
            searching: false,
            pagingType: 'simple_numbers',
        };

        $(function () {
            $('#data-table-perfil-entradas').DataTable($.extend({}, opcionesCompactas, {
                order: [[0, 'desc']],
                ajax: "{{ route('entradas-salidas.datatable') }}?nino={{ $nino->id }}",
                columns: [
                    {data: 'entrada', name: 'entrada', searchable: false},
                    {data: 'salida', name: 'salida', searchable: false, render: function (data, type) {
                        if (type === 'display' && data === 'En Casa') {
                            return '<span class="bitacora-badge">En casa</span>';
                        }
                        return data;
                    }},
                ]
            }));

            $('#data-table-perfil-servicios').DataTable($.extend({}, opcionesCompactas, {
                pageLength: 15,
                order: [[0, 'desc']],
                ajax: "{{ route('servicios.datatable') }}?nino={{ $nino->id }}",
                columns: [
                    {data: 'fecha_hora', name: 'fecha_hora', searchable: false},
                    {data: 'servicio_nombre', orderable: false, searchable: false, render: function (data, type, row) {
                        if (type !== 'display') { return data; }
                        return '<i class="fas ' + row.servicio_icono + '" aria-hidden="true"></i> ' + data;
                    }},
                ]
            }));
        });
    </script>
@endsection
