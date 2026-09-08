<?php

namespace App\Http\Controllers;

use App\Enums\Accion;
use App\Http\Requests\StoreNinoRequest;
use App\Models\Escolaridad;
use App\Models\Estado;
use App\Models\Hospital;
use App\Models\Nino;
use App\Models\Pais;
use App\Models\RegistroActividad;
use App\Models\SalarioMinimo;
use App\Models\TipoDieta;
use App\Models\TipoTratamiento;
use App\Models\TrabajadorSocial;
use App\Models\Zona;
use App\Services\FotoPerfil;
use App\Services\QrCodeService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class NinoController extends Controller
{
    public function __construct(
        private readonly QrCodeService $qr,
        private readonly FotoPerfil $fotos,
    ) {}

    /**
     * Alta de expediente. Corrige los defectos del prototipo: apellidos ya no se
     * cruzan, fec_ing/fec_sal por fin se guardan (leía fec_in/fec_eg), las
     * alergias y los tratamientos se persisten, y la foto se sube de verdad.
     */
    public function store(StoreNinoRequest $request): JsonResponse
    {
        $datos = $request->validated();

        $foto = null;
        if ($request->hasFile('image')) {
            $foto = $this->fotos->guardar($request->file('image'), 'fotos/ninos');
        }

        $crear = fn () => Nino::create([
            ...$this->atributosDesdeFormulario($datos),
            'qr' => $this->qr->generar(),
            'foto' => $foto,
        ]);

        try {
            $nino = $crear();
        } catch (QueryException $e) {
            // Red de seguridad ante una colisión de QR concurrente: un reintento.
            if (! str_contains($e->getMessage(), 'qr')) {
                throw $e;
            }
            $nino = $crear();
        }

        $nino->tiposTratamiento()->sync($datos['tt'] ?? []);

        RegistroActividad::anotar(Accion::Alta, 'Expediente de '.$nino->nombreCompleto().' ('.$nino->qr.')');

        return response()->json([
            'status' => '1',
            'title' => 'Niño registrado',
            'msg' => 'El expediente de '.$nino->nombreCompleto().' fue creado con el código '.$nino->qr.'.',
            'data' => $nino,
        ], 201);
    }

    /**
     * Edición del expediente con el mismo formulario (y las mismas reglas) del
     * alta. El QR nunca cambia; la foto solo si llega una nueva, y en ese caso
     * la anterior se borra del disco para no acumular huérfanas.
     */
    public function update(StoreNinoRequest $request, Nino $nino): JsonResponse
    {
        $datos = $request->validated();

        $atributos = $this->atributosDesdeFormulario($datos);
        if ($request->hasFile('image')) {
            $atributos['foto'] = $this->fotos->guardar($request->file('image'), 'fotos/ninos');
        }

        $fotoAnterior = $nino->foto;
        $nino->update($atributos);
        $nino->tiposTratamiento()->sync($datos['tt'] ?? []);

        if (isset($atributos['foto']) && $fotoAnterior !== null) {
            Storage::disk('public')->delete($fotoAnterior);
        }

        RegistroActividad::anotar(Accion::Edicion, 'Expediente de '.$nino->nombreCompleto().' ('.$nino->qr.')');

        return response()->json([
            'status' => '1',
            'title' => 'Expediente actualizado',
            'msg' => 'Los datos de '.$nino->nombreCompleto().' fueron guardados.',
            'data' => $nino,
        ]);
    }

    /**
     * Valores crudos para precargar el formulario de edición: las llaves de
     * `campos` son los name de los inputs, así el front rellena por nombre
     * sin conocer el esquema (los catálogos van como id, no como etiqueta).
     */
    public function edit(Nino $nino): JsonResponse
    {
        $nino->load('tiposTratamiento');

        return response()->json([
            'id' => $nino->id,
            'nombre_completo' => $nino->nombreCompleto(),
            'foto_url' => $nino->foto !== null ? Storage::disk('public')->url($nino->foto) : null,
            'campos' => [
                'fec_sol' => $nino->fecha_solicitud?->toDateString(),
                'estatus' => $nino->estatus_estancia,
                'hos' => $nino->hospital_id,
                'serv' => $nino->servicio,
                'nombre' => $nino->nombre,
                'app' => $nino->apellido_paterno,
                'apm' => $nino->apellido_materno,
                'fec_nac' => $nino->fecha_nacimiento?->toDateString(),
                'sexo' => $nino->sexo,
                'pais' => $nino->pais_id,
                'est' => $nino->estado_id,
                'mun' => $nino->municipio,
                'calle' => $nino->calle,
                'num_c' => $nino->numero,
                'col' => $nino->colonia,
                'loca' => $nino->localidad,
                'cp' => $nino->cp,
                'zona' => $nino->zona_id,
                'tel1' => $nino->primer_telefono,
                'tel2' => $nino->segundo_telefono,
                'dial' => $nino->dialecto,
                'esc' => $nino->escolaridad_id,
                'rango' => $nino->clasificacion_social_id,
                'sal_min' => $nino->salario_minimo_id,
                'trab' => $nino->trabajador_social_id,
                'medico' => $nino->medico,
                'diag' => $nino->diagnostico,
                'ale_alim' => $nino->alerg_alimentos,
                'ale_med' => $nino->alerg_medicamentos,
                'diet' => $nino->tipo_dieta_id,
                'obs' => $nino->observaciones,
                'fec_ing' => $nino->fecha_ingreso?->toDateString(),
                'fec_sal' => $nino->fecha_salida?->toDateString(),
            ],
            'tt' => $nino->tiposTratamiento->pluck('id')->values(),
        ]);
    }

    /** Mapeo formulario → columnas, compartido por el alta y la edición. */
    private function atributosDesdeFormulario(array $datos): array
    {
        return [
            'nombre' => $datos['nombre'],
            'apellido_paterno' => $datos['app'],
            'apellido_materno' => $datos['apm'],
            'fecha_nacimiento' => $datos['fec_nac'],
            'sexo' => $datos['sexo'],
            'calle' => $datos['calle'] ?? null,
            'numero' => $datos['num_c'] ?? null,
            'colonia' => $datos['col'] ?? null,
            'localidad' => $datos['loca'] ?? null,
            'municipio' => $datos['mun'],
            'cp' => $datos['cp'] ?? null,
            'primer_telefono' => $datos['tel1'],
            'segundo_telefono' => $datos['tel2'] ?? null,
            'dialecto' => $datos['dial'] ?? null,
            'diagnostico' => $datos['diag'] ?? null,
            'medico' => $datos['medico'] ?? null,
            'alerg_alimentos' => $datos['ale_alim'] ?? null,
            'alerg_medicamentos' => $datos['ale_med'] ?? null,
            'servicio' => $datos['serv'] ?? null,
            'estatus_estancia' => $datos['estatus'],
            'fecha_solicitud' => $datos['fec_sol'],
            'fecha_ingreso' => $datos['fec_ing'] ?? null,
            'fecha_salida' => $datos['fec_sal'] ?? null,
            'observaciones' => $datos['obs'] ?? null,
            'trabajador_social_id' => $datos['trab'],
            'tipo_dieta_id' => $datos['diet'],
            'hospital_id' => $datos['hos'],
            'escolaridad_id' => $datos['esc'],
            'clasificacion_social_id' => $datos['rango'],
            'zona_id' => $datos['zona'],
            'salario_minimo_id' => $datos['sal_min'],
            'pais_id' => $datos['pais'],
            'estado_id' => $datos['est'],
        ];
    }

    /**
     * Expediente completo para el modal de consulta. El payload va plano y con
     * los catálogos ya resueltos: cada llave corresponde a un campo del modal
     * (#exp_<llave>), así el front solo vacía valores sin conocer el esquema.
     */
    public function show(Nino $nino): JsonResponse
    {
        $nino->load([
            'pais', 'estado', 'hospital', 'tipoDieta', 'trabajadorSocial',
            'escolaridad', 'clasificacionSocial', 'zona', 'salarioMinimo',
            'tiposTratamiento', 'acompanantes.parentesco',
        ]);

        $fecha = fn ($valor) => $valor?->legibleFecha();

        return response()->json([
            'id' => $nino->id,
            'qr' => $nino->qr,
            'foto_url' => $nino->foto !== null ? Storage::disk('public')->url($nino->foto) : null,

            'nombre_completo' => $nino->nombreCompleto(),
            'fecha_nacimiento' => $fecha($nino->fecha_nacimiento),
            'edad' => $nino->edad !== null ? $nino->edad.' años' : null,
            'sexo' => $nino->sexo,

            'fecha_solicitud' => $fecha($nino->fecha_solicitud),
            'estatus_estancia' => $nino->estatus_estancia,
            'hospital' => $nino->hospital->hospital,
            'servicio' => $nino->servicio,
            'fecha_ingreso' => $fecha($nino->fecha_ingreso),
            'fecha_salida' => $fecha($nino->fecha_salida),

            'direccion' => trim($nino->calle.' '.$nino->numero),
            'colonia' => $nino->colonia,
            'localidad' => $nino->localidad,
            'municipio' => $nino->municipio,
            'cp' => $nino->cp,
            'zona' => $nino->zona->zona,
            'estado' => $nino->estado->estado,
            'pais' => $nino->pais->pais,
            'primer_telefono' => $nino->primer_telefono,
            'segundo_telefono' => $nino->segundo_telefono,
            'dialecto' => $nino->dialecto,

            'escolaridad' => $nino->escolaridad->escolaridad,
            'clasificacion_social' => $nino->clasificacionSocial->clasificacion_social,
            'salario_minimo' => $nino->salarioMinimo->salario_minimo,
            'trabajador_social' => $nino->trabajadorSocial->trabajador_social,

            'medico' => $nino->medico,
            'diagnostico' => $nino->diagnostico,
            'alerg_alimentos' => $nino->alerg_alimentos,
            'alerg_medicamentos' => $nino->alerg_medicamentos,
            'tipo_dieta' => $nino->tipoDieta->tipo_dieta,
            'tratamientos' => $nino->tiposTratamiento->pluck('tipo_tratamiento')->implode(', '),
            'observaciones' => $nino->observaciones,

            'acompanantes' => $nino->acompanantes->map(fn ($a) => [
                'nombre_completo' => $a->nombreCompleto(),
                'edad' => $a->edad,
                'sexo' => $a->sexo,
                'parentesco' => $a->parentesco->parentesco,
            ])->values(),
        ]);
    }

    /**
     * Página individual del niño: resumen del expediente con sus descargas y
     * la bitácora (entradas/salidas y servicios) filtrada a su expediente.
     */
    public function perfil(Nino $nino): View
    {
        $nino->load([
            'pais', 'estado', 'hospital', 'tipoDieta', 'trabajadorSocial',
            'escolaridad', 'clasificacionSocial', 'zona', 'salarioMinimo',
            'tiposTratamiento', 'acompanantes.parentesco',
        ]);

        return view('ninos.perfil', [
            'nino' => $nino,
            // Catálogos para el modal de edición (los mismos que en /expedientes).
            'hospitales' => Hospital::orderBy('hospital')->get(),
            'paises' => Pais::all(),
            'estados' => Estado::orderBy('estado')->get(),
            'zonas' => Zona::all(),
            'escolaridades' => Escolaridad::all(),
            'salario' => SalarioMinimo::all(),
            'trabajadores' => TrabajadorSocial::orderBy('trabajador_social')->get(),
            'dietas' => TipoDieta::all(),
            'tratamientos' => TipoTratamiento::all(),
        ]);
    }

    /**
     * Resolución de credencial para los puntos de servicio.
     * Desconocido → 404 (el prototipo regresaba {} con 200); el sexo se regresa
     * tal cual se almacena (el mapeo M/F siempre daba "Indefinido").
     */
    public function porQr(string $qr): JsonResponse
    {
        $nino = Nino::where('qr', strtoupper($qr))
            ->with(['estado', 'hospital', 'tipoDieta', 'acompanantes.parentesco'])
            ->first();

        if ($nino === null) {
            return response()->json([
                'status' => '0',
                'title' => 'Código no encontrado',
                'msg' => 'Ningún expediente corresponde al código '.strtoupper($qr).'.',
            ], 404);
        }

        return response()->json([
            'status' => '1',
            'nino_id' => $nino->id,
            'qr' => $nino->qr,
            'nombre_completo' => $nino->nombreCompleto(),
            'edad' => $nino->edad,
            'sexo' => $nino->sexo,
            'estado' => $nino->estado->estado,
            'municipio' => $nino->municipio,
            'hospital' => $nino->hospital->hospital,
            'dieta' => $nino->tipoDieta->tipo_dieta,
            'alerg_alimentos' => $nino->alerg_alimentos,
            'alerg_medicamentos' => $nino->alerg_medicamentos,
            'parientes' => $nino->acompanantes->map(fn ($a) => [
                'nombre_completo' => $a->nombreCompleto(),
                'edad' => $a->edad,
                'sexo' => $a->sexo,
                'parentesco' => $a->parentesco->parentesco,
            ])->values(),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        $query = Nino::query()->with(['estado'])->select('nino.*');

        return DataTables::eloquent($query)
            ->addColumn('nombre', fn (Nino $nino) => $nino->nombreCompleto())
            ->addColumn('edad', fn (Nino $nino) => $nino->edad)
            ->addColumn('procedencia', fn (Nino $nino) => trim($nino->municipio.', '.$nino->estado->estado, ', '))
            ->orderColumn('nombre', 'nombre $1')
            ->filterColumn('nombre', function ($query, $keyword) {
                // Concatenar es distinto en MySQL y sqlite; buscar por columna es portable.
                $query->where(function ($q) use ($keyword) {
                    $q->where('nombre', 'like', "%{$keyword}%")
                        ->orWhere('apellido_paterno', 'like', "%{$keyword}%")
                        ->orWhere('apellido_materno', 'like', "%{$keyword}%");
                });
            })
            ->toJson();
    }
}
