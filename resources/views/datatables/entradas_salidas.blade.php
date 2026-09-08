@extends('layouts.skeleton')
@section('title', 'Bitácora | Entradas y salidas')
@section('content')
    <div class="container" id="entradas_salidas" style="background-color: white">
        <h1>Bitácora | Entradas y salidas</h1>
        <table class="table table-bordered data-table" id="data-table-entradas-salidas">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Niño</th>
                    <th>Código</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <script>
        $(function () {
            $('#data-table-entradas-salidas').DataTable({
                processing: true,
                serverSide: true,
                order: [[3, 'desc']],
                ajax: "{{ route('entradas-salidas.datatable') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'nino', name: 'nino', orderable: false, searchable: false, render: function (data, type, row) {
                        if (type !== 'display') { return data; }
                        return '<a class="enlace-nino" href="/ninos/' + row.nino_id + '/perfil" title="Ver página del niño">' + data + '</a>';
                    }},
                    {data: 'QR', name: 'qr'},
                    {data: 'entrada', name: 'entrada', searchable: false},
                    {data: 'salida', name: 'salida', searchable: false},
                ]
            });
        });
    </script>
@endsection
