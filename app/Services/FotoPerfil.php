<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Normaliza las fotos de expediente antes de guardarlas.
 *
 * El navegador acepta lo que el teléfono o la computadora le den (WebP, BMP,
 * PNG con transparencia, un JPEG de 12 megapíxeles girado por EXIF) y aquí
 * todo termina como el mismo JPEG chico y derecho. Así la foto se ve igual en
 * el expediente, en el carnet y en el PDF, sin depender del formato de origen.
 */
class FotoPerfil
{
    /** Lado máximo en píxeles: de sobra para el carnet impreso. */
    public const LADO_MAXIMO = 1200;

    /** Calidad del JPEG resultante. */
    public const CALIDAD = 82;

    /**
     * Guarda la foto normalizada en el disco público y devuelve su ruta.
     *
     * Si la imagen no se puede decodificar (un HEIC de iPhone en un servidor
     * sin libheif, por ejemplo) se guarda el archivo tal cual: es mejor
     * conservar el original que perder la foto.
     */
    public function guardar(UploadedFile $archivo, string $carpeta): string
    {
        try {
            $imagen = (new ImageManager(new Driver))->read($archivo->getRealPath());
        } catch (\Throwable) {
            return $archivo->store($carpeta, 'public');
        }

        // orient() endereza según EXIF (las fotos de teléfono llegan giradas);
        // blendTransparency evita que un PNG transparente quede con fondo negro.
        $imagen->orient()
            ->blendTransparency('ffffff')
            ->scaleDown(self::LADO_MAXIMO, self::LADO_MAXIMO);

        $ruta = $carpeta.'/'.Str::random(40).'.jpg';
        Storage::disk('public')->put($ruta, (string) $imagen->toJpeg(self::CALIDAD));

        return $ruta;
    }
}
