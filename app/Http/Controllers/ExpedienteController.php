<?php

namespace App\Http\Controllers;

use App\Enums\Accion;
use App\Models\Nino;
use App\Models\RegistroActividad;
use App\Services\QrCodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Reporte PDF del expediente completo. Sigue el mismo patrón de seguridad que
 * el carnet (FichaController): se genera al pedirlo, nunca toca el disco
 * público y la ruta exige el permiso descargar-expediente (cuenta maestra y
 * trabajo social).
 */
class ExpedienteController extends Controller
{
    public function pdf(Request $request, Nino $nino, QrCodeService $qr): Response
    {
        $nino->load([
            'pais', 'estado', 'hospital', 'tipoDieta', 'trabajadorSocial',
            'escolaridad', 'clasificacionSocial', 'zona', 'salarioMinimo',
            'tiposTratamiento',
            'acompanantes.parentesco', 'acompanantes.escolaridad',
            'acompanantes.edoSalud', 'acompanantes.ocupacion',
        ]);

        // dompdf no puede pedir recursos por HTTP (y no debe: las fotos viven
        // detrás de autenticación), así que todo va incrustado en base64.
        $foto = null;
        if ($nino->foto !== null && Storage::disk('public')->exists($nino->foto)) {
            $foto = 'data:'.Storage::disk('public')->mimeType($nino->foto).';base64,'
                .base64_encode(Storage::disk('public')->get($nino->foto));
        }

        $pdf = Pdf::loadView('pdf.expediente', [
            'nino' => $nino,
            'qrPng' => 'data:image/png;base64,'.base64_encode($qr->render($nino->qr)),
            'logo' => 'data:image/png;base64,'.base64_encode(file_get_contents(resource_path('ficha/logo_n.png'))),
            'foto' => $foto,
        ])->setPaper('letter');

        // Sin ?download=1 el PDF se sirve en línea (la vista previa del
        // perfil); como con el carnet, solo la descarga real queda anotada.
        if (! $request->boolean('download')) {
            return $pdf->stream('expediente-'.$nino->qr.'.pdf');
        }

        RegistroActividad::anotar(Accion::Descarga, 'Reporte PDF de '.$nino->nombreCompleto().' ('.$nino->qr.')');

        return $pdf->download('expediente-'.$nino->qr.'.pdf');
    }
}
