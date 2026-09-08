<?php

namespace App\Http\Controllers;

use App\Enums\Accion;
use App\Enums\Servicio;
use App\Models\RegistroActividad;
use App\Models\RegistroServicio;
use App\Services\ExcelBitacora;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

/**
 * Reportes de uso de servicios para la página de administración: desglose por
 * bloque de tiempo (día, semana, mes, año, todo o un rango específico) y
 * descarga de la bitácora del periodo en Excel. Solo administradores
 * (middleware `master`, igual que el resto de /administracion).
 */
class ReporteController extends Controller
{
    private const MESES = [1 => 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
        'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

    private const DIAS = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

    private const MESES_LARGOS = [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    /** Desglose de registros por servicio en el periodo pedido, listo para graficar. */
    public function datos(Request $request): JsonResponse
    {
        [$inicio, $fin, $granularidad, $descripcion] = $this->resolverPeriodo($request);

        $registros = RegistroServicio::whereBetween('created_at', [$inicio, $fin])
            ->get(['servicio', 'created_at']);

        // Conteo por servicio y cubeta de tiempo. El volumen de una casa es
        // pequeño, así que se agrupa en PHP y el SQL queda portable.
        $conteos = [];
        foreach ($registros as $registro) {
            $clave = $this->claveDeCubeta($registro->created_at, $granularidad);
            $conteos[$registro->servicio->value][$clave] = ($conteos[$registro->servicio->value][$clave] ?? 0) + 1;
        }

        [$claves, $etiquetas] = $this->cubetasDelPeriodo($inicio, $fin, $granularidad);

        return response()->json([
            'descripcion' => $descripcion,
            'labels' => $etiquetas,
            'series' => collect(Servicio::cases())->map(fn (Servicio $servicio) => [
                'servicio' => $servicio->value,
                'etiqueta' => $servicio->etiqueta(),
                'datos' => array_map(fn (string $clave) => $conteos[$servicio->value][$clave] ?? 0, $claves),
                'total' => array_sum($conteos[$servicio->value] ?? []),
            ]),
            'total' => $registros->count(),
        ]);
    }

    /** La bitácora del periodo en Excel: usuario, servicio, fecha y hora de cada registro. */
    public function excel(Request $request, ExcelBitacora $excel): Response
    {
        [$inicio, $fin, , $descripcion] = $this->resolverPeriodo($request);

        $registros = RegistroServicio::whereBetween('created_at', [$inicio, $fin])
            ->with('nino')
            ->orderBy('created_at')
            ->get();

        $filas = $registros->map(fn (RegistroServicio $registro) => [
            $registro->nino->nombreCompleto(),
            $registro->qr,
            $registro->servicio->etiqueta(),
            $registro->created_at->format('d/m/Y'),
            $registro->created_at->format('g:i A'),
        ])->all();

        RegistroActividad::anotar(Accion::Descarga,
            'Bitácora de servicios en Excel — '.$descripcion.' ('.count($filas).' registros)');

        $nombre = 'bitacora-servicios-'.$inicio->format('Y-m-d').'-a-'.$fin->format('Y-m-d').'.xlsx';

        return response($excel->generar(['Usuario', 'QR', 'Servicio', 'Fecha', 'Hora'], $filas), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$nombre.'"',
        ]);
    }

    /**
     * Traduce el periodo pedido a [inicio, fin, granularidad, descripción].
     * La granularidad de las cubetas se ajusta al tamaño del bloque: un día se
     * desglosa por hora, una semana o un mes por día y un año por mes.
     *
     * @return array{Carbon, Carbon, string, string}
     */
    private function resolverPeriodo(Request $request): array
    {
        $validado = $request->validate([
            'periodo' => ['nullable', 'in:dia,semana,mes,ano,todo,rango'],
            'desde' => ['required_if:periodo,rango', 'nullable', 'date'],
            'hasta' => ['required_if:periodo,rango', 'nullable', 'date', 'after_or_equal:desde'],
        ]);

        $hoy = Carbon::now();

        switch ($validado['periodo'] ?? 'dia') {
            case 'semana':
                $inicio = $hoy->copy()->startOfWeek();
                $fin = $hoy->copy()->endOfWeek();

                return [$inicio, $fin, 'dia',
                    'Semana del '.$inicio->legibleFecha().' al '.$fin->legibleFecha()];

            case 'mes':
                return [$hoy->copy()->startOfMonth(), $hoy->copy()->endOfMonth(), 'dia',
                    self::MESES_LARGOS[$hoy->month].' del '.$hoy->year];

            case 'ano':
                return [$hoy->copy()->startOfYear(), $hoy->copy()->endOfYear(), 'mes',
                    'Año '.$hoy->year];

            case 'todo':
                $primero = RegistroServicio::oldest()->first();
                $inicio = ($primero?->created_at ?? $hoy)->copy()->startOfMonth();

                return [$inicio, $hoy->copy()->endOfDay(), 'mes', 'Todo el historial'];

            case 'rango':
                $inicio = Carbon::parse($validado['desde'])->startOfDay();
                $fin = Carbon::parse($validado['hasta'])->endOfDay();
                $granularidad = match (true) {
                    $inicio->isSameDay($fin) => 'hora',
                    $inicio->diffInDays($fin) <= 92 => 'dia',
                    default => 'mes',
                };

                return [$inicio, $fin, $granularidad,
                    'Del '.$inicio->legibleFecha().' al '.$fin->legibleFecha()];

            default: // dia
                return [$hoy->copy()->startOfDay(), $hoy->copy()->endOfDay(), 'hora',
                    'Hoy, '.$hoy->legibleFecha()];
        }
    }

    private function claveDeCubeta(Carbon $fecha, string $granularidad): string
    {
        return match ($granularidad) {
            'hora' => $fecha->format('Y-m-d H'),
            'dia' => $fecha->format('Y-m-d'),
            'mes' => $fecha->format('Y-m'),
        };
    }

    /**
     * Las cubetas del periodo completo, con ceros incluidos: el gráfico
     * muestra también las horas o días sin actividad.
     *
     * @return array{list<string>, list<string>}
     */
    private function cubetasDelPeriodo(Carbon $inicio, Carbon $fin, string $granularidad): array
    {
        $cursor = match ($granularidad) {
            'hora' => $inicio->copy()->startOfHour(),
            'dia' => $inicio->copy()->startOfDay(),
            'mes' => $inicio->copy()->startOfMonth(),
        };

        $claves = [];
        $etiquetas = [];
        while ($cursor <= $fin) {
            $claves[] = $this->claveDeCubeta($cursor, $granularidad);
            $etiquetas[] = match ($granularidad) {
                'hora' => $cursor->format('g A'),
                'dia' => self::DIAS[$cursor->dayOfWeek].' '.$cursor->day.' '.self::MESES[$cursor->month],
                'mes' => self::MESES[$cursor->month].' '.$cursor->year,
            };
            $cursor = match ($granularidad) {
                'hora' => $cursor->addHour(),
                'dia' => $cursor->addDay(),
                'mes' => $cursor->addMonth(),
            };
        }

        return [$claves, $etiquetas];
    }
}
