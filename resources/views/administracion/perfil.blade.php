@extends('layouts.skeleton')
@section('title', 'Perfil de '.$usuario->name)
@section('description-page', 'Datos de la cuenta y su bitácora de actividad')
@section('content')
<div class="pagina">
    <section class="pagina-tarjeta">
        <div class="pagina-encabezado">
            <h1>{{ $usuario->name }}</h1>
            <hr class="pagina-acento">
            <p class="pagina-sub">{{ $usuario->role->etiqueta() }} de la Casa Ronald McDonald Puebla</p>
        </div>

        <div class="pagina-cuerpo">
            <div class="modal-seccion">
                <h5>Datos de la cuenta</h5>
                <div class="expediente-campos">
                    <div><small>Nombre</small><span>{{ $usuario->name }}</span></div>
                    <div><small>Correo</small><span>{{ $usuario->email }}</span></div>
                    <div><small>Rol</small><span>{{ $usuario->role->etiqueta() }}</span></div>
                    <div>
                        <small>Estado</small>
                        <span>
                            @if ($usuario->is_active)
                                <span class="bitacora-badge">Activa</span>
                            @else
                                <span class="bitacora-badge badge-inactiva">Desactivada</span>
                            @endif
                        </span>
                    </div>
                    <div><small>Fecha de alta</small><span>{{ $usuario->created_at?->legibleFecha() ?? '—' }}</span></div>
                </div>
            </div>

            <div class="bitacora-seccion">
                <div class="bitacora-encabezado">
                    <h2><i class="fas fa-clipboard-list" aria-hidden="true"></i> Actividad de la cuenta</h2>
                </div>
                <p class="bitacora-sub">Inicios de sesión, escaneos, descargas, altas y ediciones de esta cuenta, de lo más reciente a lo más antiguo.</p>

                @if ($actividad->isEmpty())
                    <p class="bitacora-vacio">Todavía no hay actividad registrada en esta cuenta.</p>
                @else
                    <div class="table-responsive">
                        <table class="table data-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Acción</th>
                                    <th>Detalle</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($actividad as $registro)
                                    <tr>
                                        <td>{{ $registro->created_at->legible() }}</td>
                                        <td>{{ $registro->accion->etiqueta() }}</td>
                                        <td>{{ $registro->detalle ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $actividad->links('pagination::bootstrap-4') }}
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
