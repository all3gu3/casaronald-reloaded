<?php

namespace App\Http\Controllers;

use App\Enums\Accion;
use App\Enums\Servicio;
use App\Http\Requests\RegistrarEscaneoRequest;
use App\Models\EntradaSalida;
use App\Models\Nino;
use App\Models\RegistroActividad;
use App\Models\RegistroServicio;
use Illuminate\Http\JsonResponse;

/**
 * Registro por escaneo desde el navegador (teléfono o lector USB). Sustituye a
 * la app móvil externa del prototipo: mismo contrato, pero autenticado, por
 * POST con CSRF y con manejo real de errores. La interfaz vive en la ventana
 * flotante (parts/escanear-modal) disponible en cualquier página.
 */
class EscanearController extends Controller
{
    public function registrar(RegistrarEscaneoRequest $request): JsonResponse
    {
        $datos = $request->validated();
        $nino = Nino::where('qr', $datos['codigo'])->with('tipoDieta')->firstOrFail();

        $resultado = match ($datos['accion']) {
            'entrada' => $this->registrarEntrada($nino),
            'salida' => $this->registrarSalida($nino),
            default => $this->registrarServicio($nino, Servicio::from($datos['accion'])),
        };

        RegistroActividad::anotar(Accion::Escaneo, $resultado['title'].': '.$nino->nombreCompleto().' ('.$nino->qr.')');

        return response()->json([
            'status' => '1',
            'title' => $resultado['title'],
            'msg' => $resultado['msg'],
            'nino' => [
                'nombre_completo' => $nino->nombreCompleto(),
                'edad' => $nino->edad,
                'dieta' => $nino->tipoDieta->tipo_dieta,
                'alerg_alimentos' => $nino->alerg_alimentos,
                'alerg_medicamentos' => $nino->alerg_medicamentos,
            ],
        ], 201);
    }

    /** @return array{title: string, msg: string} */
    private function registrarServicio(Nino $nino, Servicio $servicio): array
    {
        RegistroServicio::create([
            'nino_id' => $nino->id,
            'qr' => $nino->qr,
            'servicio' => $servicio,
        ]);

        return [
            'title' => $servicio->etiqueta().' registrado',
            'msg' => $nino->nombreCompleto().' quedó registrado en '.$servicio->etiqueta().' a las '.now()->format('g:i A').'.',
        ];
    }

    /** @return array{title: string, msg: string} */
    private function registrarEntrada(Nino $nino): array
    {
        if ($abierta = EntradaSalida::abiertaDe($nino)) {
            abort(422, 'Ya hay una entrada abierta desde el '.$abierta->entrada->legible().'. Registra primero la salida.');
        }

        EntradaSalida::create([
            'nino_id' => $nino->id,
            'qr' => $nino->qr,
            'entrada' => now(),
        ]);

        return [
            'title' => 'Entrada registrada',
            'msg' => $nino->nombreCompleto().' entró a la Casa a las '.now()->format('g:i A').'.',
        ];
    }

    /** @return array{title: string, msg: string} */
    private function registrarSalida(Nino $nino): array
    {
        $abierta = EntradaSalida::abiertaDe($nino);

        // El prototipo hacía null-deref aquí (500) con un QR sin entrada previa.
        if ($abierta === null) {
            abort(422, 'No hay una entrada abierta para '.$nino->nombreCompleto().'. Registra primero la entrada.');
        }

        $abierta->update(['salida' => now()]);

        return [
            'title' => 'Salida registrada',
            'msg' => $nino->nombreCompleto().' salió de la Casa a las '.now()->format('g:i A').'.',
        ];
    }
}
