<?php

namespace App\Http\Controllers;

use App\Enums\Accion;
use App\Http\Requests\StoreAcompananteRequest;
use App\Models\Acompanante;
use App\Models\RegistroActividad;
use App\Services\FotoPerfil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class AcompananteController extends Controller
{
    public function __construct(private readonly FotoPerfil $fotos) {}

    public function store(StoreAcompananteRequest $request): JsonResponse
    {
        $datos = $request->validated();

        $foto = null;
        if ($request->hasFile('image')) {
            $foto = $this->fotos->guardar($request->file('image'), 'fotos/acompanantes');
        }

        $acompanante = Acompanante::create([
            'nino_id' => $datos['nino'],
            'nombre' => $datos['nombre'],
            'apellido_paterno' => $datos['app'],
            'apellido_materno' => $datos['apm'],
            'edad' => (string) Carbon::parse($datos['fec_nac'])->age,
            'sexo' => $datos['sexo'],
            'fecha_registro' => now()->toDateString(),
            'observaciones' => $datos['obs'] ?? null,
            'foto' => $foto,
            'parentesco_id' => $datos['paren'],
            'escolaridad_id' => $datos['esc'],
            'edo_salud_id' => $datos['edoSalud'],
            'ocupacion_id' => $datos['ocu'],
            'trabaja' => (bool) $datos['trab'],
            'licencia_goce_sueldo' => isset($datos['goce']) ? (bool) $datos['goce'] : null,
            'seguro_medico' => (bool) $datos['seg'],
            'casa_propia' => (bool) $datos['casa'],
            'asistencia_financiera' => (bool) $datos['asist'],
            'renta_mensualidad' => $datos['rent'] ?? null,
            'dependientes_economicos' => $datos['dep_ec'],
            'ingreso_mensual' => $datos['ing'],
        ]);

        RegistroActividad::anotar(Accion::Alta, 'Acompañante '.$acompanante->nombreCompleto().' en el expediente '.$acompanante->nino->qr);

        return response()->json([
            'status' => '1',
            'title' => 'Acompañante registrado',
            'msg' => $acompanante->nombreCompleto().' quedó ligado al expediente.',
            'data' => $acompanante,
        ], 201);
    }

    public function datatable(Request $request): JsonResponse
    {
        $query = Acompanante::query()
            ->with('parentesco')
            ->where('nino_id', $request->integer('id'))
            ->select('acompanante.*');

        return DataTables::eloquent($query)
            ->addColumn('nombre', fn (Acompanante $a) => $a->nombreCompleto())
            ->addColumn('parentesco', fn (Acompanante $a) => $a->parentesco->parentesco)
            ->toJson();
    }
}
