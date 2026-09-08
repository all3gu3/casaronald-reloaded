<!-- Modal: formulario de solicitud (alta y edición de niño) -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="tituloModalNino" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModalNino">Formulario de solicitud</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formulario-solicitud" enctype="multipart/form-data">
                @csrf
                    <div class="modal-seccion">
                        <h5>Solicitud y estancia</h5>
                        <div class="formulario-campos">
                            <div>
                                <label for="nino_fec_sol">Fecha de solicitud *</label>
                                <input type="date" name="fec_sol" required class="form-control" id="nino_fec_sol">
                            </div>
                            <div class="campo-2">
                                <label id="nino_estatus_etiqueta">Estatus de la estancia *</label>
                                <div class="formulario-opciones" role="radiogroup" aria-labelledby="nino_estatus_etiqueta">
                                    <input type="radio" name="estatus" id="nino_estatus_primera" value="Primera vez" required>
                                    <label for="nino_estatus_primera">Primera vez</label>
                                    <input type="radio" name="estatus" id="nino_estatus_prorroga" value="Prorroga">
                                    <label for="nino_estatus_prorroga">Prórroga</label>
                                    <input type="radio" name="estatus" id="nino_estatus_subsecuente" value="Subsecuente">
                                    <label for="nino_estatus_subsecuente">Subsecuente</label>
                                </div>
                            </div>
                            <div>
                                <label for="nino_hos">Hospital *</label>
                                <select name="hos" id="nino_hos" class="form-control">
                                    @foreach($hospitales as $h)
                                        <option value="{{ $h->id }}">{{ $h->hospital }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="campo-2">
                                <label for="nino_serv">Servicio</label>
                                <input type="text" id="nino_serv" name="serv" class="form-control">
                            </div>
                            <div>
                                <label for="nino_fec_ing">Fecha de ingreso</label>
                                <input name="fec_ing" class="form-control" type="date" id="nino_fec_ing">
                            </div>
                            <div>
                                <label for="nino_fec_sal">Fecha de salida</label>
                                <input name="fec_sal" class="form-control" type="date" id="nino_fec_sal">
                            </div>
                        </div>
                    </div>

                    <div class="modal-seccion">
                        <h5>Datos generales</h5>
                        <div class="formulario-campos">
                            <div>
                                <label for="nino_nombre">Nombre(s) *</label>
                                <input required class="form-control" type="text" id="nino_nombre" name="nombre">
                            </div>
                            <div>
                                <label for="nino_apellidop">Apellido paterno *</label>
                                <input required class="form-control" type="text" id="nino_apellidop" name="app">
                            </div>
                            <div>
                                <label for="nino_apellidom">Apellido materno *</label>
                                <input required class="form-control" type="text" id="nino_apellidom" name="apm">
                            </div>
                            <div>
                                <label for="nino_fec_nac">Fecha de nacimiento *</label>
                                <input type="date" name="fec_nac" required class="form-control" id="nino_fec_nac">
                                <small class="formulario-nota">La edad se calcula automáticamente.</small>
                            </div>
                            <div class="campo-2">
                                <label id="nino_sexo_etiqueta">Género *</label>
                                <div class="formulario-opciones" role="radiogroup" aria-labelledby="nino_sexo_etiqueta">
                                    <input type="radio" name="sexo" id="nino_sexo_m" value="Masculino" required>
                                    <label for="nino_sexo_m">Masculino</label>
                                    <input type="radio" name="sexo" id="nino_sexo_f" value="Femenino">
                                    <label for="nino_sexo_f">Femenino</label>
                                    <input type="radio" name="sexo" id="nino_sexo_i" value="Indefinido">
                                    <label for="nino_sexo_i">Indefinido</label>
                                </div>
                            </div>
                            <div class="campo-2">
                                <label for="nino_image">Foto del niño</label>
                                {{-- Dos entradas: el archivo de siempre y una de respaldo
                                     con capture. public/js/foto-captura.js las coordina:
                                     «Tomar foto» enciende la cámara del aparato en la
                                     página y normaliza la imagen antes de subirla. --}}
                                <div class="foto-campo" id="nino_foto_campo" data-foto-campo data-foto-previa="#nino_product_image">
                                    <input type="file" class="form-control-file foto-campo-archivo" name="image" id="nino_image" accept="image/*" data-foto-archivo>
                                    <input type="file" id="nino_image_camara" accept="image/*" capture="environment" data-foto-camara hidden>
                                    <button type="button" class="bitacora-ver foto-campo-camara" data-foto-tomar>
                                        <i class="fas fa-camera" aria-hidden="true"></i> Tomar foto
                                    </button>
                                </div>
                                <small class="formulario-nota">Se recomienda una imagen cuadrada. «Tomar foto» enciende la cámara del aparato.</small>
                            </div>
                            <div>
                                <img id="nino_product_image" src="" alt="" class="formulario-foto-previa">
                            </div>
                        </div>
                    </div>

                    <div class="modal-seccion">
                        <h5>Dirección y contacto</h5>
                        <div class="formulario-campos">
                            <div>
                                <label for="nino_pais">País *</label>
                                <select name="pais" id="nino_pais" class="form-control">
                                    @foreach($paises as $p)
                                        <option value="{{ $p->id }}">{{ $p->pais }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="nino_est">Estado *</label>
                                <select name="est" id="nino_est" class="form-control">
                                    @foreach($estados as $e)
                                        <option value="{{ $e->id }}">{{ $e->estado }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="nino_mun">Municipio *</label>
                                <input required class="form-control" type="text" id="nino_mun" name="mun">
                            </div>
                            <div class="campo-2">
                                <label for="nino_calle">Calle</label>
                                <input class="form-control" type="text" id="nino_calle" name="calle">
                            </div>
                            <div>
                                <label for="nino_num_c">Número</label>
                                <input class="form-control" type="text" id="nino_num_c" name="num_c">
                            </div>
                            <div>
                                <label for="nino_col">Colonia</label>
                                <input class="form-control" type="text" id="nino_col" name="col">
                            </div>
                            <div>
                                <label for="nino_loca">Localidad</label>
                                <input class="form-control" type="text" id="nino_loca" name="loca">
                            </div>
                            <div>
                                <label for="nino_cp">Código postal</label>
                                <input class="form-control" type="text" id="nino_cp" name="cp" maxlength="5" pattern="[0-9]{4,5}">
                            </div>
                            <div>
                                <label for="nino_zona">Zona *</label>
                                <select name="zona" id="nino_zona" class="form-control">
                                    @foreach($zonas as $z)
                                        <option value="{{ $z->id }}">{{ $z->zona }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="nino_tel1">Teléfono 1 *</label>
                                <input required class="form-control" type="text" id="nino_tel1" name="tel1">
                            </div>
                            <div>
                                <label for="nino_tel2">Teléfono 2</label>
                                <input class="form-control" type="text" id="nino_tel2" name="tel2">
                            </div>
                            <div>
                                <label for="nino_dial">Dialecto</label>
                                <input class="form-control" type="text" id="nino_dial" name="dial">
                            </div>
                        </div>
                    </div>

                    <div class="modal-seccion">
                        <h5>Datos socioeconómicos</h5>
                        <div class="formulario-campos">
                            <div>
                                <label for="nino_esc">Escolaridad *</label>
                                <select name="esc" id="nino_esc" class="form-control">
                                    @foreach($escolaridades as $e)
                                        <option value="{{ $e->id }}">{{ $e->escolaridad }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="nino_sal_min">Salario mínimo *</label>
                                <select name="sal_min" id="nino_sal_min" class="form-control">
                                    @foreach($salario as $s)
                                        <option value="{{ $s->id }}">{{ $s->salario_minimo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="nino_trab">Trabajador social *</label>
                                <select name="trab" id="nino_trab" class="form-control">
                                    @foreach($trabajadores as $t)
                                        <option value="{{ $t->id }}">{{ $t->trabajador_social }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="campo-3">
                                <label for="nino_socieco">Número socioeconómico *</label>
                                <div class="formulario-rango">
                                    <input class="form-control" type="range" name="rango" min="1" max="6" step="1" id="nino_socieco"
                                           list="nino_socieco_marcas" value="3">
                                    <output id="nino_socieco_valor" for="nino_socieco">3</output>
                                    <datalist id="nino_socieco_marcas">
                                        <option value="1" label="1">
                                        <option value="2" label="2">
                                        <option value="3" label="3">
                                        <option value="4" label="4">
                                        <option value="5" label="5">
                                        <option value="6" label="6">
                                    </datalist>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-seccion">
                        <h5>Datos médicos</h5>
                        <div class="formulario-campos">
                            <div>
                                <label for="nino_medico">Médico</label>
                                <input class="form-control" type="text" id="nino_medico" name="medico">
                            </div>
                            <div class="campo-2">
                                <label for="nino_diag">Diagnóstico</label>
                                <input class="form-control" type="text" id="nino_diag" name="diag">
                            </div>
                            <div>
                                <label for="nino_ale_alim">Alergias en alimentos</label>
                                <input type="text" id="nino_ale_alim" name="ale_alim" class="form-control">
                            </div>
                            <div>
                                <label for="nino_ale_med">Alergias en medicamentos</label>
                                <input type="text" id="nino_ale_med" name="ale_med" class="form-control">
                            </div>
                            <div>
                                <label for="nino_diet">Tipo de dieta *</label>
                                <select name="diet" id="nino_diet" class="form-control">
                                    @foreach($dietas as $d)
                                        <option value="{{ $d->id }}">{{ $d->tipo_dieta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="campo-3">
                                <label id="nino_tt_etiqueta">Tratamientos</label>
                                {{-- Checkboxes desde el catálogo, con name="tt[]": el prototipo
                                     usaba name="tt" y solo llegaba (y se descartaba) un valor. --}}
                                <div class="formulario-opciones" role="group" aria-labelledby="nino_tt_etiqueta">
                                    @foreach($tratamientos as $t)
                                        <input type="checkbox" name="tt[]" id="tt_{{ $t->id }}" value="{{ $t->id }}">
                                        <label for="tt_{{ $t->id }}">{{ $t->tipo_tratamiento }}</label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-seccion">
                        <h5>Observaciones</h5>
                        <div class="formulario-campos">
                            <div class="campo-3">
                                <label for="nino_obs">Observaciones</label>
                                <textarea name="obs" id="nino_obs" rows="3" class="form-control" placeholder="Observaciones"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            {{-- Mismo pie que las ventanas de vista previa: la acción principal
                 centrada y "Cerrar" a la derecha. --}}
            <div class="preview-pie">
                <button type="submit" form="formulario-solicitud" id="nino_guarda_form" class="bitacora-ver perfil-boton-editar">
                    <i class="fas fa-floppy-disk" aria-hidden="true"></i>
                    <span id="nino_guarda_texto">Guardar</span>
                </button>
                <button type="button" class="bitacora-ver preview-cerrar" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Un solo formulario para alta y edición: ninoEnEdicion decide el destino
    // del submit (POST /ninos o PUT /ninos/{id} vía _method).
    var ninoEnEdicion = null;

    // Salvaguarda de cierre: se compara el formulario contra la instantánea
    // tomada al abrirlo. serialize() no ve el archivo de la foto, así que ese
    // cambio se marca aparte.
    var instantaneaAlAbrir = null;
    var fotoCambiada = false;
    var cierreConfirmado = false;

    function marcarSinCambios() {
        instantaneaAlAbrir = $('#formulario-solicitud').serialize();
        fotoCambiada = false;
    }

    function hayCambiosSinGuardar() {
        if (instantaneaAlAbrir === null) { return false; }

        return fotoCambiada || $('#formulario-solicitud').serialize() !== instantaneaAlAbrir;
    }

    function nuevoNino() {
        ninoEnEdicion = null;
        $('#formulario-solicitud')[0].reset();
        $('#nino_socieco_valor').text($('#nino_socieco').val());
        $('#nino_product_image').attr('src', '').removeAttr('style');
        limpiarCampoFoto('#nino_foto_campo');
        $('#tituloModalNino').text('Formulario de solicitud');
        $('#nino_guarda_texto').text('Guardar');
        marcarSinCambios();
    }

    function editarNino(id) {
        nuevoNino();
        ninoEnEdicion = id;
        $('#tituloModalNino').text('Cargando expediente…');

        $.getJSON('/ninos/' + id + '/editar').done(function (nino) {
            $.each(nino.campos, function (name, valor) {
                var campo = $('#formulario-solicitud [name="' + name + '"]');
                if (campo.attr('type') === 'radio') {
                    campo.filter('[value="' + valor + '"]').prop('checked', true);
                } else {
                    campo.val(valor === null ? '' : valor);
                }
            });
            $('#nino_socieco_valor').text($('#nino_socieco').val());
            nino.tt.forEach(function (tratamiento) {
                $('#tt_' + tratamiento).prop('checked', true);
            });
            if (nino.foto_url) {
                $('#nino_product_image').attr('src', nino.foto_url).css('width', 200);
            }
            $('#tituloModalNino').text('Editar expediente · ' + nino.nombre_completo);
            $('#nino_guarda_texto').text('Guardar cambios');
            marcarSinCambios();
        });
    }

    $(function () {
        // FormData: el prototipo usaba serialize(), que no puede transportar la
        // foto; los errores los captura el manejador global (ajaxError).
        $("#formulario-solicitud").on('submit', function (e) {
            e.preventDefault();
            var datos = new FormData(this);
            var url = "{{ route('ninos.store') }}";
            if (ninoEnEdicion !== null) {
                url = '/ninos/' + ninoEnEdicion;
                datos.append('_method', 'PUT');
            }
            $.ajax({
                url: url,
                method: 'POST',
                data: datos,
                processData: false,
                contentType: false,
            }).done(function (respuesta) {
                toastr.success(respuesta.msg, respuesta.title, {timeOut: 6000});
                marcarSinCambios();
                $("#myModal").modal("hide");
                nuevoNino();
                // La página del perfil define alGuardarNino() para recargar sus
                // datos (van rendereados en el servidor); en /expedientes se
                // refresca el concentrado.
                if (typeof alGuardarNino === 'function') {
                    alGuardarNino();
                } else {
                    $('#data-table-ninos').DataTable().ajax.reload();
                }
            });
        });

        $('#nino_socieco').on('input', function () {
            $('#nino_socieco_valor').text(this.value);
        });

        // foto-captura.js normaliza la imagen y avisa aquí: la foto no viaja en
        // serialize(), así que este es el único modo de saber que cambió.
        $('#nino_foto_campo').on('foto:cambiada', function () {
            fotoCambiada = true;
        });

        // Cerrar con cambios sin guardar pide confirmación. Cubre los tres
        // caminos de salida de Bootstrap: "Cerrar", la ✕ del encabezado y
        // la tecla Escape.
        $('#myModal').on('hide.bs.modal', function (e) {
            if (cierreConfirmado || !hayCambiosSinGuardar()) { return; }

            e.preventDefault();

            if (window.confirm('Hay cambios sin guardar en el expediente. ¿Cerrar de todas formas y descartarlos?')) {
                cierreConfirmado = true;
                $('#myModal').modal('hide');
            }
        });

        $('#myModal').on('hidden.bs.modal', function () {
            cierreConfirmado = false;
            instantaneaAlAbrir = null;
        });
    });
</script>
