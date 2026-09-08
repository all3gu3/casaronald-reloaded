<!-- Modal: consulta del expediente completo -->
<div class="modal fade" id="expedienteModal" tabindex="-1" role="dialog" aria-labelledby="tituloModalExpediente" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModalExpediente">Expediente del niño</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="expediente_cargando" class="text-muted" style="text-align: center;">Cargando expediente…</div>
                <div id="expediente_datos" style="display: none;">
                    <div class="expediente-encabezado">
                        <img id="expediente_foto" src="" alt="Foto del niño" class="expediente-foto">
                        <div>
                            <h4 data-exp="nombre_completo"></h4>
                            <p class="expediente-sub">
                                <span data-exp="sexo"></span> · <span data-exp="edad"></span> ·
                                Código <strong data-exp="qr"></strong>
                            </p>
                        </div>
                    </div>

                    <div class="modal-seccion">
                        <h5>Solicitud y estancia</h5>
                        <div class="expediente-campos">
                            <div><small>Fecha de solicitud</small><span data-exp="fecha_solicitud"></span></div>
                            <div><small>Estatus de la estancia</small><span data-exp="estatus_estancia"></span></div>
                            <div><small>Hospital</small><span data-exp="hospital"></span></div>
                            <div><small>Servicio</small><span data-exp="servicio"></span></div>
                            <div><small>Fecha de ingreso</small><span data-exp="fecha_ingreso"></span></div>
                            <div><small>Fecha de salida</small><span data-exp="fecha_salida"></span></div>
                        </div>
                    </div>

                    <div class="modal-seccion">
                        <h5>Datos generales</h5>
                        <div class="expediente-campos">
                            <div><small>Fecha de nacimiento</small><span data-exp="fecha_nacimiento"></span></div>
                            <div><small>Dialecto</small><span data-exp="dialecto"></span></div>
                            <div><small>Teléfono 1</small><span data-exp="primer_telefono"></span></div>
                            <div><small>Teléfono 2</small><span data-exp="segundo_telefono"></span></div>
                        </div>
                    </div>

                    <div class="modal-seccion">
                        <h5>Dirección</h5>
                        <div class="expediente-campos">
                            <div><small>Calle y número</small><span data-exp="direccion"></span></div>
                            <div><small>Colonia</small><span data-exp="colonia"></span></div>
                            <div><small>Localidad</small><span data-exp="localidad"></span></div>
                            <div><small>Municipio</small><span data-exp="municipio"></span></div>
                            <div><small>Estado</small><span data-exp="estado"></span></div>
                            <div><small>País</small><span data-exp="pais"></span></div>
                            <div><small>CP</small><span data-exp="cp"></span></div>
                            <div><small>Zona</small><span data-exp="zona"></span></div>
                        </div>
                    </div>

                    <div class="modal-seccion">
                        <h5>Datos socioeconómicos</h5>
                        <div class="expediente-campos">
                            <div><small>Escolaridad</small><span data-exp="escolaridad"></span></div>
                            <div><small>Clasificación social</small><span data-exp="clasificacion_social"></span></div>
                            <div><small>Salario mínimo</small><span data-exp="salario_minimo"></span></div>
                            <div><small>Trabajador social</small><span data-exp="trabajador_social"></span></div>
                        </div>
                    </div>

                    <div class="modal-seccion">
                        <h5>Datos médicos</h5>
                        <div class="expediente-campos">
                            <div><small>Médico</small><span data-exp="medico"></span></div>
                            <div><small>Diagnóstico</small><span data-exp="diagnostico"></span></div>
                            <div><small>Alergias en alimentos</small><span data-exp="alerg_alimentos"></span></div>
                            <div><small>Alergias en medicamentos</small><span data-exp="alerg_medicamentos"></span></div>
                            <div><small>Tipo de dieta</small><span data-exp="tipo_dieta"></span></div>
                            <div><small>Tratamientos</small><span data-exp="tratamientos"></span></div>
                        </div>
                    </div>

                    <div class="modal-seccion">
                        <h5>Observaciones</h5>
                        <p class="expediente-observaciones" data-exp="observaciones"></p>
                    </div>

                    <div class="modal-seccion">
                        <h5>Acompañantes</h5>
                        <table class="table table-bordered" id="expediente_acompanantes">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Parentesco</th>
                                    <th>Edad</th>
                                    <th>Sexo</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <p id="expediente_sin_acompanantes" class="text-muted" style="display: none;">Sin acompañantes registrados.</p>
                    </div>
                </div>
            </div>
            {{-- Mismo pie que la vista previa del carnet: la descarga azul
                 centrada y "Cerrar" a la derecha. --}}
            <div class="preview-pie">
                {{-- El reporte sale de la casa: solo trabajo social y administradores
                     lo descargan (la ruta también lo exige, esto es solo la vista). --}}
                @can('descargar-expediente')
                    <a id="expediente_descargar" href="#" class="bitacora-ver perfil-boton-descarga">
                        <i class="fas fa-file-pdf" aria-hidden="true"></i> Descargar PDF
                    </a>
                @endcan
                <button type="button" class="bitacora-ver preview-cerrar" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Consulta de solo lectura: el payload de /ninos/{id} viene plano y con los
    // catálogos resueltos; cada llave se vacía en su [data-exp] homónimo.
    function verExpediente(id) {
        $('#expediente_datos').hide();
        $('#expediente_cargando').show();

        $.getJSON('/ninos/' + id).done(function (nino) {
            $('#expedienteModal [data-exp]').each(function () {
                var valor = nino[$(this).data('exp')];
                $(this).text(valor !== null && valor !== '' ? valor : '—');
            });

            // Sin foto, el avatar de reserva: el encabezado no queda cojo.
            $('#expediente_foto').attr('src', nino.foto_url || '{{ asset('img/avatar-nino.svg') }}').show();

            var cuerpo = $('#expediente_acompanantes tbody').empty();
            nino.acompanantes.forEach(function (a) {
                $('<tr>')
                    .append($('<td>').text(a.nombre_completo))
                    .append($('<td>').text(a.parentesco))
                    .append($('<td>').text(a.edad !== null ? a.edad : '—'))
                    .append($('<td>').text(a.sexo !== null ? a.sexo : '—'))
                    .appendTo(cuerpo);
            });
            $('#expediente_acompanantes').toggle(nino.acompanantes.length > 0);
            $('#expediente_sin_acompanantes').toggle(nino.acompanantes.length === 0);

            $('#expediente_descargar').attr('href', '/expedientes/' + id + '/pdf');

            $('#expediente_cargando').hide();
            $('#expediente_datos').show();
        });
    }
</script>
