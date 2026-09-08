@extends('layouts.skeleton')
@section('title', 'Bitácora | Transporte')
@section('content')
    <div class="container" id="transporte" style="background-color: white">
        <h1>Bitácora | Transporte</h1>
        <table class="table table-bordered data-table" id="data-table-transporte">
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
            $('#data-table-transporte').DataTable({
                processing: true,
                serverSide: true,
                order: [[4, 'desc']],
                ajax: "{{ route('registros.datatable', 'transporte') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'nino', name: 'nino', orderable: false, searchable: false, render: function (data, type, row) {
                        if (type !== 'display') { return data; }
                        return '<a class="enlace-nino" href="/ninos/' + row.nino_id + '/perfil" title="Ver página del niño">' + data + '</a>';
                    }},
                    {data: 'QR', name: 'qr'},
                    {data: 'servicio_nombre', orderable: false, searchable: false, render: function (data) {
                        return '<i class="fas fa-bus"></i> ' + data;
                    }},
                    {data: 'fecha_hora', name: 'fecha_hora', searchable: false},
                ]
            });
        });
    </script>
@endsection
