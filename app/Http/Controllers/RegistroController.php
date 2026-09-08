<?php

namespace App\Http\Controllers;

use App\Enums\Servicio;
use App\Models\EntradaSalida;
use App\Models\RegistroServicio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

/**
 * Concentrados por servicio. A diferencia del prototipo, cada fila muestra el
 * NOMBRE del niño (join a nino), no solo el código QR crudo, y las fechas se
 * presentan en hora local.
 */
class RegistroController extends Controller
{
    public function servicioDatatable(Request $request, string $servicio): JsonResponse
    {
        $servicio = Servicio::from($servicio);

        $query = RegistroServicio::query()
            ->with('nino')
            ->where('servicio', $servicio->value)
            // La página individual del niño pide solo sus registros.
            ->when($request->filled('nino'), fn ($q) => $q->where('nino_id', $request->integer('nino')))
            ->select('registros_servicios.*');

        return DataTables::eloquent($query)
            ->addColumn('nino', fn (RegistroServicio $r) => $r->nino->nombreCompleto())
            ->addColumn('QR', fn (RegistroServicio $r) => $r->qr)
            ->addColumn('servicio_nombre', fn (RegistroServicio $r) => $r->servicio->etiqueta())
            ->addColumn('fecha_hora', fn (RegistroServicio $r) => $r->created_at->legible())
            ->orderColumn('fecha_hora', 'created_at $1')
            ->toJson();
    }

    /**
     * Todos los servicios en un solo concentrado, para la página individual
     * del niño: cada fila lleva la etiqueta y el icono de su servicio, además
     * del nombre y el código del niño para homologar con las demás bitácoras.
     */
    public function serviciosDatatable(Request $request): JsonResponse
    {
        $query = RegistroServicio::query()
            ->with('nino')
            ->when($request->filled('nino'), fn ($q) => $q->where('nino_id', $request->integer('nino')))
            ->select('registros_servicios.*');

        return DataTables::eloquent($query)
            ->addColumn('nino', fn (RegistroServicio $r) => $r->nino->nombreCompleto())
            ->addColumn('QR', fn (RegistroServicio $r) => $r->qr)
            ->addColumn('servicio_nombre', fn (RegistroServicio $r) => $r->servicio->etiqueta())
            ->addColumn('servicio_icono', fn (RegistroServicio $r) => $r->servicio->icono())
            ->addColumn('fecha_hora', fn (RegistroServicio $r) => $r->created_at->legible())
            ->orderColumn('fecha_hora', 'created_at $1')
            ->toJson();
    }

    public function entradasSalidasDatatable(Request $request): JsonResponse
    {
        $query = EntradaSalida::query()
            ->with('nino')
            ->when($request->filled('nino'), fn ($q) => $q->where('nino_id', $request->integer('nino')))
            ->select('entradas_salidas.*');

        // El endpoint del prototipo nunca regresó una fila: iteraba un Builder sin
        // ->get(), llamaba format() sobre strings y tronaba con salida = NULL.
        return DataTables::eloquent($query)
            ->addColumn('nino', fn (EntradaSalida $r) => $r->nino->nombreCompleto())
            ->addColumn('QR', fn (EntradaSalida $r) => $r->qr)
            ->addColumn('entrada', fn (EntradaSalida $r) => $r->entrada?->legible() ?? '—')
            ->addColumn('salida', fn (EntradaSalida $r) => $r->salida?->legible() ?? 'En Casa')
            ->orderColumn('entrada', 'entrada $1')
            ->orderColumn('salida', 'salida $1')
            ->toJson();
    }
}
