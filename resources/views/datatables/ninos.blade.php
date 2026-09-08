<div class="pagina-encabezado">
    <h1>Expediente de niños</h1>
    <hr class="pagina-acento">
    <p class="pagina-sub">Concentrado general de niños albergados en la Casa Ronald McDonald Puebla</p>
</div>

<div class="pagina-cuerpo">
    {{-- Botón centrado (pagina-acciones lo alinea a la derecha por defecto). --}}
    <div class="pagina-acciones" style="justify-content: center;">
        <button type="button" class="pagina-boton-primario" onclick="nuevoNino();" data-toggle="modal" data-target="#myModal">
            <i class="fas fa-user-plus" aria-hidden="true"></i>
            <span>Nuevo registro</span>
        </button>
    </div>

    <div class="table-responsive">
        <table class="table data-table" id="data-table-ninos">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Nombre</th>
                    <th>Edad</th>
                    <th>Procedencia</th>
                    <th>QR</th>
                    <th>Ver QR</th>
                    <th>Acompañantes</th>
                    <th>Expediente</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<script>
    var tablaAcompanantes = null;
    var ninoActual = null;

    function verAcompanantes(id) {
        ninoActual = id;
        if (tablaAcompanantes === null) {
            tablaAcompanantes = $('#data-table-acompanantes').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                searching: false,
                ajax: {
                    url: "{{ route('acompanantes.datatable') }}",
                    data: function (d) { d.id = ninoActual; }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'nombre', name: 'nombre', orderable: false, searchable: false},
                    {data: 'edad', name: 'edad'},
                    {data: 'sexo', name: 'sexo'},
                    {data: 'parentesco', name: 'parentesco', orderable: false, searchable: false},
                ]
            });
        } else {
            tablaAcompanantes.ajax.reload();
        }
    }

    // Al agregar un acompañante desde una fila, el niño queda preseleccionado
    // (el prototipo abría el modal sin pasar el id: asociación silenciosa al primero).
    function agregarAcompanante(id) {
        $('#acom_nino').val(id);
    }

    $(function () {
        $('#data-table-ninos').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('ninos.datatable') }}",
            columns: [
                {data: 'id', name: 'id'},
                // El nombre enlaza a la página individual del niño (el feed ya
                // entrega el texto escapado, así que puede ir dentro del anchor).
                {data: 'nombre', name: 'nombre', render: function (data, type, row) {
                    if (type !== 'display') { return data; }
                    return '<a class="enlace-nino" href="/ninos/' + row.id + '/perfil" title="Ver página del niño">' + data + '</a>';
                }},
                {data: 'edad', name: 'edad', orderable: false, searchable: false},
                {data: 'procedencia', name: 'procedencia', orderable: false, searchable: false},
                {data: 'qr', name: 'qr'},
                // El carnet abre la vista previa compartida (parts/preview),
                // la misma que usa la página individual del niño.
                {data: 'id', orderable: false, searchable: false, render: function (data) {
                    return '<button class="btn fichita" onClick="abrirPreviewCarnet(' + data + ');" title="Ver carnet QR"><i class="fa fa-qrcode"></i></button>';
                }},
                {data: 'id', orderable: false, searchable: false, render: function (data) {
                    return '<button class="btn verr" onClick="verAcompanantes(' + data + ');" data-target="#acomModalTable" data-toggle="modal" data-backdrop="false"><i class="fa fa-eye"></i></button>'
                        + '<button class="btn agregarr" onClick="agregarAcompanante(' + data + ');" data-target="#acomModal" data-toggle="modal" data-backdrop="false"><i class="fa fa-user-plus"></i></button>';
                }},
                {data: 'id', orderable: false, searchable: false, render: function (data) {
                    return '<button class="btn verr" onClick="verExpediente(' + data + ');" data-target="#expedienteModal" data-toggle="modal" title="Ver expediente"><i class="fa fa-folder-open"></i></button>'
                        + '<button class="btn agregarr" onClick="editarNino(' + data + ');" data-target="#myModal" data-toggle="modal" data-backdrop="false" title="Editar expediente"><i class="fas fa-pen"></i></button>';
                }},
            ]
        });
    });
</script>
