<!-- Modal: registro de acompañante -->
<div class="modal fade" id="acomModal" tabindex="-1" role="dialog" aria-labelledby="tituloModalAcom" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModalAcom">Formulario para el registro de acompañantes</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <form id="formulario-acompanante" enctype="multipart/form-data">
                    @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <center><h4>Datos de registro</h4></center>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label for="acom_nino">Niño</label>
                                        <select name="nino" id="acom_nino" class="form-control">
                                            @foreach($ninos as $n)
                                                <option value="{{ $n->id }}">{{ $n->nombre.' '.$n->apellido_paterno.' '.$n->apellido_materno.' ['.$n->qr.']' }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">La fecha de registro se asigna automáticamente.</small>
                                    </div>
                                </div>

                                <center><h4>Datos generales</h4></center>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label class="input-group" for="acom_apellidop">Apellido Paterno:</label>
                                        <input required class="form-control" type="text" id="acom_apellidop" name="app">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="input-group" for="acom_apellidom">Apellido Materno:</label>
                                        <input required class="form-control" type="text" id="acom_apellidom" name="apm">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-5">
                                        <label class="input-group" for="acom_nombre">Nombre(s):</label>
                                        <input required class="form-control" type="text" id="acom_nombre" name="nombre">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="acom_edo_salud" class="input_group">Estado de salud:</label>
                                        <select name="edoSalud" id="acom_edo_salud" class="form-control">
                                            @foreach($edosSalud as $e)
                                                <option value="{{ $e->id }}">{{ $e->edo_salud }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="acom_fec_nac" class="input_group">Fecha de nacimiento:</label>
                                        <input type="date" name="fec_nac" required class="form-control" id="acom_fec_nac">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-3">
                                        <label for="acom_paren" class="input_group">Parentesco:</label>
                                        <select name="paren" id="acom_paren" class="form-control">
                                            @foreach($parentescos as $p)
                                                <option value="{{ $p->id }}">{{ $p->parentesco }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-9">
                                        <label class="input-group">Género:</label>
                                        <div class="col-md-4">
                                            <input type="radio" name="sexo" id="acom_sexo_m" value="Masculino" required>
                                            <label class="input_group" for="acom_sexo_m">Masculino</label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="radio" name="sexo" id="acom_sexo_f" value="Femenino">
                                            <label class="input_group" for="acom_sexo_f">Femenino</label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="radio" name="sexo" id="acom_sexo_i" value="Indefinido">
                                            <label class="input_group" for="acom_sexo_i">Indefinido</label>
                                        </div>
                                    </div>
                                </div>

                                <center><h4>Datos socioeconómicos</h4></center>

                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label class="input_group" for="acom_esc">Escolaridad:</label>
                                        <select name="esc" id="acom_esc" class="form-control">
                                            @foreach($escolaridades as $e)
                                                <option value="{{ $e->id }}">{{ $e->escolaridad }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="input-group">Trabaja:</label>
                                        <div class="col-md-6">
                                            <input type="radio" name="trab" id="acom_trab_s" value="1" required>
                                            <label class="input_group" for="acom_trab_s">Si</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="radio" name="trab" id="acom_trab_n" value="0">
                                            <label class="input_group" for="acom_trab_n">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label class="input_group" for="acom_ocu">Ocupación:</label>
                                        <select name="ocu" id="acom_ocu" class="form-control">
                                            @foreach($ocupaciones as $o)
                                                <option value="{{ $o->id }}">{{ $o->ocupacion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="input-group">Goce de sueldo:</label>
                                        <div class="col-md-6">
                                            <input type="radio" name="goce" id="acom_goce_s" value="1">
                                            <label class="input_group" for="acom_goce_s">Si</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="radio" name="goce" id="acom_goce_n" value="0">
                                            <label class="input_group" for="acom_goce_n">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label class="input-group" for="acom_dep_ec">Dependientes económicos:</label>
                                        <input required class="form-control" type="number" min="0" id="acom_dep_ec" name="dep_ec">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="input-group">Seguro médico:</label>
                                        <div class="col-md-6">
                                            <input type="radio" name="seg" id="acom_seg_s" value="1" required>
                                            <label class="input_group" for="acom_seg_s">Si</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="radio" name="seg" id="acom_seg_n" value="0">
                                            <label class="input_group" for="acom_seg_n">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label class="input-group" for="acom_rent">Renta o mensualidad:</label>
                                        <input class="form-control" type="number" min="0" id="acom_rent" name="rent">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="input-group">Casa propia:</label>
                                        <div class="col-md-6">
                                            <input type="radio" name="casa" id="acom_casa_s" value="1" required>
                                            <label class="input_group" for="acom_casa_s">Si</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="radio" name="casa" id="acom_casa_n" value="0">
                                            <label class="input_group" for="acom_casa_n">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label class="input-group" for="acom_ing">Ingreso mensual:</label>
                                        <input required class="form-control" type="number" min="0" id="acom_ing" name="ing">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="input-group">Asistencia financiera:</label>
                                        <div class="col-md-6">
                                            <input type="radio" name="asist" id="acom_asist_s" value="1" required>
                                            <label class="input_group" for="acom_asist_s">Si</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="radio" name="asist" id="acom_asist_n" value="0">
                                            <label class="input_group" for="acom_asist_n">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="input_group" for="acom_obs">Observaciones:</label><br>
                                        {{-- El prototipo no enviaba este campo (textarea sin manejo); ahora sí llega y se guarda. --}}
                                        <textarea name="obs" id="acom_obs" rows="4" cols="50" placeholder="Observaciones"></textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-7">
                                        <label for="acom_image" class="input-group">Foto del Acompañante</label>
                                        {{-- Mismo campo doble que en el expediente del niño:
                                             archivo o cámara del aparato (foto-captura.js). --}}
                                        <div class="foto-campo" id="acom_foto_campo" data-foto-campo data-foto-previa="#acom_product_image">
                                            <input type="file" class="form-control-file foto-campo-archivo" name="image" id="acom_image" accept="image/*" data-foto-archivo>
                                            <input type="file" id="acom_image_camara" accept="image/*" capture="environment" data-foto-camara hidden>
                                            <button type="button" class="bitacora-ver foto-campo-camara" data-foto-tomar>
                                                <i class="fas fa-camera" aria-hidden="true"></i> Tomar foto
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <img id="acom_product_image" src="" alt="" style="max-width: 200px;">
                                    </div>
                                </div>
                                <br>
                                <div class="col-md-12" style="text-align: center;">
                                    <button type="submit" id="acom_guarda_form" class="btn botoncito">Guardar</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <h4>© RMHC</h4>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $("#formulario-acompanante").on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('acompanantes.store') }}",
                method: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
            }).done(function (respuesta) {
                toastr.success(respuesta.msg, respuesta.title, {timeOut: 6000});
                $("#acomModal").modal("hide");
                $("#formulario-acompanante")[0].reset();
                $('#acom_product_image').attr('src', '');
                limpiarCampoFoto('#acom_foto_campo');
                if ($.fn.DataTable.isDataTable('#data-table-acompanantes')) {
                    $('#data-table-acompanantes').DataTable().ajax.reload();
                }
            });
        });

    });
</script>
