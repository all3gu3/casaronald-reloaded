<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Expediente {{ $nino->qr }}</title>
    <style>
        /* dompdf: sin flexbox ni grid; todo el acomodo va con tablas.
           DejaVu Sans viene incluida y cubre acentos y ñ. */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #22345C; }

        .encabezado { width: 100%; border-bottom: 3px solid #F64740; padding-bottom: 10px; }
        .encabezado td { vertical-align: middle; }
        .encabezado .logo { width: 170px; }
        .encabezado .titulo h1 { font-size: 17px; }
        .encabezado .titulo p { color: #8A94A6; font-size: 10px; margin-top: 2px; }
        .encabezado .codigo { width: 110px; text-align: right; }
        .encabezado .codigo .qr { width: 90px; height: 90px; }
        .encabezado .codigo .clave { font-size: 13px; font-weight: bold; letter-spacing: 2px; }

        .identidad { width: 100%; margin-top: 12px; }
        .identidad .foto { width: 95px; vertical-align: top; }
        .identidad .foto img { width: 85px; height: 85px; object-fit: cover; border: 1px solid #d9e4f2; }
        .identidad h2 { font-size: 14px; margin-bottom: 2px; }
        .identidad .sub { color: #8A94A6; }

        .seccion { margin-top: 14px; }
        .seccion h3 {
            font-size: 11px; text-transform: uppercase; letter-spacing: 1px;
            background: #EEF3FA; padding: 4px 8px; border-left: 3px solid #F64740;
            margin-bottom: 6px;
        }
        table.campos { width: 100%; border-collapse: collapse; }
        table.campos td { width: 33.33%; padding: 3px 8px; vertical-align: top; }
        table.campos .etiqueta { color: #8A94A6; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        table.campos .valor { font-size: 10px; }

        table.listado { width: 100%; border-collapse: collapse; margin-top: 2px; }
        table.listado th {
            background: #EEF3FA; text-align: left; padding: 4px 8px;
            font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px;
        }
        table.listado td { padding: 4px 8px; border-bottom: 1px solid #E7ECF3; }

        .observaciones { padding: 4px 8px; }

        .pie {
            position: fixed; bottom: 0; left: 0; right: 0;
            border-top: 1px solid #E7ECF3; padding-top: 5px;
            color: #8A94A6; font-size: 8px; text-align: center;
        }
    </style>
</head>
<body>
    <table class="encabezado">
        <tr>
            <td class="logo"><img src="{{ $logo }}" alt="Casa Ronald McDonald" style="width: 160px;"></td>
            <td class="titulo">
                <h1>Expediente de niño</h1>
                <p>Casa Ronald McDonald Puebla &mdash; Reporte generado el {{ now()->legible() }}</p>
            </td>
            <td class="codigo">
                <img class="qr" src="{{ $qrPng }}" alt="Código QR"><br>
                <span class="clave">{{ $nino->qr }}</span>
            </td>
        </tr>
    </table>

    <table class="identidad">
        <tr>
            @if ($foto !== null)
                <td class="foto"><img src="{{ $foto }}" alt="Foto"></td>
            @endif
            <td>
                <h2>{{ $nino->nombreCompleto() }}</h2>
                <p class="sub">
                    {{ $nino->sexo }}@if ($nino->edad !== null), {{ $nino->edad }} años @endif
                    @if ($nino->fecha_nacimiento) &mdash; nació el {{ $nino->fecha_nacimiento->legibleFecha() }} @endif
                </p>
            </td>
        </tr>
    </table>

    <div class="seccion">
        <h3>Solicitud y estancia</h3>
        <table class="campos">
            <tr>
                <td><div class="etiqueta">Fecha de solicitud</div><div class="valor">{{ $nino->fecha_solicitud?->legibleFecha() ?? '—' }}</div></td>
                <td><div class="etiqueta">Estatus de la estancia</div><div class="valor">{{ $nino->estatus_estancia ?? '—' }}</div></td>
                <td><div class="etiqueta">Hospital</div><div class="valor">{{ $nino->hospital->hospital }}</div></td>
            </tr>
            <tr>
                <td><div class="etiqueta">Servicio</div><div class="valor">{{ $nino->servicio ?? '—' }}</div></td>
                <td><div class="etiqueta">Fecha de ingreso</div><div class="valor">{{ $nino->fecha_ingreso?->legibleFecha() ?? '—' }}</div></td>
                <td><div class="etiqueta">Fecha de salida</div><div class="valor">{{ $nino->fecha_salida?->legibleFecha() ?? '—' }}</div></td>
            </tr>
        </table>
    </div>

    <div class="seccion">
        <h3>Dirección y contacto</h3>
        <table class="campos">
            <tr>
                <td><div class="etiqueta">Calle y número</div><div class="valor">{{ trim($nino->calle.' '.$nino->numero) ?: '—' }}</div></td>
                <td><div class="etiqueta">Colonia</div><div class="valor">{{ $nino->colonia ?? '—' }}</div></td>
                <td><div class="etiqueta">Localidad</div><div class="valor">{{ $nino->localidad ?? '—' }}</div></td>
            </tr>
            <tr>
                <td><div class="etiqueta">Municipio</div><div class="valor">{{ $nino->municipio ?? '—' }}</div></td>
                <td><div class="etiqueta">Estado / País</div><div class="valor">{{ $nino->estado->estado }}, {{ $nino->pais->pais }}</div></td>
                <td><div class="etiqueta">CP / Zona</div><div class="valor">{{ $nino->cp ?? '—' }} / {{ $nino->zona->zona }}</div></td>
            </tr>
            <tr>
                <td><div class="etiqueta">Teléfono 1</div><div class="valor">{{ $nino->primer_telefono ?? '—' }}</div></td>
                <td><div class="etiqueta">Teléfono 2</div><div class="valor">{{ $nino->segundo_telefono ?? '—' }}</div></td>
                <td><div class="etiqueta">Dialecto</div><div class="valor">{{ $nino->dialecto ?? '—' }}</div></td>
            </tr>
        </table>
    </div>

    <div class="seccion">
        <h3>Datos socioeconómicos</h3>
        <table class="campos">
            <tr>
                <td><div class="etiqueta">Escolaridad</div><div class="valor">{{ $nino->escolaridad->escolaridad }}</div></td>
                <td><div class="etiqueta">Clasificación social</div><div class="valor">{{ $nino->clasificacionSocial->clasificacion_social }}</div></td>
                <td><div class="etiqueta">Salario mínimo</div><div class="valor">{{ $nino->salarioMinimo->salario_minimo }}</div></td>
            </tr>
            <tr>
                <td colspan="3"><div class="etiqueta">Trabajador social</div><div class="valor">{{ $nino->trabajadorSocial->trabajador_social }}</div></td>
            </tr>
        </table>
    </div>

    <div class="seccion">
        <h3>Datos médicos</h3>
        <table class="campos">
            <tr>
                <td><div class="etiqueta">Médico</div><div class="valor">{{ $nino->medico ?? '—' }}</div></td>
                <td colspan="2"><div class="etiqueta">Diagnóstico</div><div class="valor">{{ $nino->diagnostico ?? '—' }}</div></td>
            </tr>
            <tr>
                <td><div class="etiqueta">Alergias en alimentos</div><div class="valor">{{ $nino->alerg_alimentos ?? '—' }}</div></td>
                <td><div class="etiqueta">Alergias en medicamentos</div><div class="valor">{{ $nino->alerg_medicamentos ?? '—' }}</div></td>
                <td><div class="etiqueta">Tipo de dieta</div><div class="valor">{{ $nino->tipoDieta->tipo_dieta }}</div></td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="etiqueta">Tratamientos</div>
                    <div class="valor">{{ $nino->tiposTratamiento->pluck('tipo_tratamiento')->implode(', ') ?: '—' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="seccion">
        <h3>Acompañantes</h3>
        @if ($nino->acompanantes->isEmpty())
            <p class="observaciones">Sin acompañantes registrados.</p>
        @else
            <table class="listado">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Parentesco</th>
                        <th>Edad</th>
                        <th>Sexo</th>
                        <th>Ocupación</th>
                        <th>Estado de salud</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($nino->acompanantes as $acompanante)
                        <tr>
                            <td>{{ $acompanante->nombreCompleto() }}</td>
                            <td>{{ $acompanante->parentesco->parentesco }}</td>
                            <td>{{ $acompanante->edad ?? '—' }}</td>
                            <td>{{ $acompanante->sexo ?? '—' }}</td>
                            <td>{{ $acompanante->ocupacion?->ocupacion ?? '—' }}</td>
                            <td>{{ $acompanante->edoSalud?->edo_salud ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if ($nino->observaciones)
        <div class="seccion">
            <h3>Observaciones</h3>
            <p class="observaciones">{{ $nino->observaciones }}</p>
        </div>
    @endif

    <div class="pie">
        Documento confidencial de uso interno &mdash; Casa Ronald McDonald Puebla. Expediente {{ $nino->qr }}.
    </div>
</body>
</html>
