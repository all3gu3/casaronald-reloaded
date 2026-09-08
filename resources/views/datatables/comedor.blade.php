@extends('layouts.skeleton')
@section('title', 'Bitácora | Comedor')
@section('content')
    <div class="container" id="comedor" style="background-color: white">
        <h1>Bitácora | Comedor</h1>
        <table class="table table-bordered data-table" id="data-table-comedor">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Niño</th>
                    <th>Código</th>
                    <th>Servicio</th>
                    <th>Fecha y hora</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <script>
        $(function () {
            $('#data-table-comedor').DataTable({
                processing: true,
                serverSide: true,
                order: [[4, 'desc']],
                ajax: "{{ route('registros.datatable', 'comedor') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'nino', name: 'nino', orderable: false, searchable: false, render: function (data, type, row) {
                        if (type !== 'display') { return data; }
                        return '<a class="enlace-nino" href="/ninos/' + row.nino_id + '/perfil" title="Ver página del niño">' + data + '</a>';
                    }},
                    {data: 'QR', name: 'qr'},
                    {data: 'servicio_nombre', orderable: false, searchable: false, render: function (data) {
                        return '<i class="fas fa-drumstick-bite"></i> ' + data;
                    }},
                    {data: 'fecha_hora', name: 'fecha_hora', searchable: false},
                ]
            });
        });
    </script>
@endsection
