<?php

namespace App\Http\Controllers;

use App\Enums\Accion;
use App\Models\Nino;
use App\Models\RegistroActividad;
use App\Services\FichaGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sirve el carnet SOLO a usuarios autenticados. El prototipo guardaba los
 * carnets bajo el symlink público: cualquiera podía enumerar
 * /storage/carnes/{1..n}.jpg y descargar los datos de cada niño.
 *
 * El mismo carnet sale en dos formatos: JPG (por omisión) y ?formato=pdf
 * (el carnet completo en tamaño carta).
 */
class FichaController extends Controller
{
    public function show(Request $request, Nino $nino, FichaGenerator $generator): Response
    {
        $ruta = FichaGenerator::ruta($nino);

        // Se (re)genera si no existe, si el expediente cambió después de
        // generarla, o si quedó cacheada con el diseño anterior (otro tamaño).
        if (! is_file($ruta)
            || filemtime($ruta) < $nino->updated_at?->getTimestamp()
            || (getimagesize($ruta)[0] ?? null) !== FichaGenerator::ANCHO) {
            $ruta = $generator->generar($nino);
        }

        if ($request->input('formato') === 'pdf') {
            return $this->pdf($request, $nino, $ruta);
        }

        if ($request->boolean('download')) {
            RegistroActividad::anotar(Accion::Descarga, 'Carnet QR de '.$nino->nombreCompleto().' ('.$nino->qr.')');

            return response()->download($ruta, 'ficha-'.$nino->qr.'.jpg');
        }

        return response()->file($ruta, ['Cache-Control' => 'no-store']);
    }

    /**
     * El carnet ocupa la página completa: la imagen ya trae la proporción
     * carta y va incrustada en base64 (dompdf no debe pedir nada por HTTP).
     */
    private function pdf(Request $request, Nino $nino, string $ruta): Response
    {
        $pdf = Pdf::loadView('pdf.ficha', [
            'imagen' => 'data:image/jpeg;base64,'.base64_encode(file_get_contents($ruta)),
        ])->setPaper('letter');

        if ($request->boolean('download')) {
            RegistroActividad::anotar(Accion::Descarga, 'Carnet QR (PDF) de '.$nino->nombreCompleto().' ('.$nino->qr.')');

            return $pdf->download('ficha-'.$nino->qr.'.pdf');
        }

        return $pdf->stream('ficha-'.$nino->qr.'.pdf');
    }
}
