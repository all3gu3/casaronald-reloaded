<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Casa Ronald | @yield('title')</title>
        <meta content="@yield('description-page')" name="description">
        <meta content="Casa Ronald McDonald México" name="keywords">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800" rel="stylesheet" media="print" onload="this.media='all'">

        {{-- Una sola copia de cada librería, servida localmente --}}
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">

        <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
        <script src="{{ asset('js/jquery.slides.js') }}" defer></script>
        <script src="{{ asset('js/foto-captura.js') }}" defer></script>

        {{-- Estilos propios --}}
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/home.css') }}">
        <link rel="stylesheet" href="{{ asset('css/slider.css') }}">
        <link rel="stylesheet" href="{{ asset('css/fondo.css') }}">
        <link rel="stylesheet" href="{{ asset('css/menu_registros.css') }}">
        <link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
        <link rel="stylesheet" href="{{ asset('css/pagina.css') }}">
        <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
        <link rel="stylesheet" href="{{ asset('css/escanear.css') }}">

        <script>
            // CSRF en todo AJAX y manejo global de errores: un fallo YA NUNCA
            // se disfraza de éxito (defecto del prototipo).
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                },
            });
            $(document).ajaxError(function (event, xhr) {
                if (xhr.status === 401 || xhr.status === 419) {
                    window.location.href = '{{ route('login') }}';
                    return;
                }
                var mensaje = 'Ocurrió un error inesperado.';
                if (xhr.responseJSON) {
                    mensaje = xhr.responseJSON.message || mensaje;
                    if (xhr.responseJSON.errors) {
                        mensaje = Object.values(xhr.responseJSON.errors).flat().join(' ');
                    }
                }
                toastr.error(mensaje, 'Error', {timeOut: 6000});
            });

            // DataTables en español (México) para todas las tablas del sistema:
            // un solo default global en lugar de repetirlo en cada vista.
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    lengthMenu: 'Mostrar _MENU_ registros',
                    search: 'Buscar:',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    infoEmpty: 'Sin registros para mostrar',
                    infoFiltered: '(filtrados de _MAX_)',
                    zeroRecords: 'No se encontraron resultados',
                    emptyTable: 'Sin datos disponibles',
                    processing: 'Procesando…',
                    paginate: {
                        first: 'Primera',
                        last: 'Última',
                        next: 'Siguiente',
                        previous: 'Anterior',
                    },
                },
            });
        </script>
    </head>
    <body>
        @auth
            @include('parts.navbar')
            @can('escanear')
                @include('parts.escanear-modal')
            @endcan
            @include('parts.foto-camara')
        @endauth
        @yield('content')
    </body>
</html>
