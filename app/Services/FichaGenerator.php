<?php

namespace App\Services;

use App\Models\Nino;
use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Geometry\Factories\CircleFactory;
use Intervention\Image\Geometry\Factories\LineFactory;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Typography\FontFactory;

/**
 * Compositor del carnet imprimible, ahora en formato vertical con la
 * proporción exacta del tamaño carta (1275×1650 = carta a 150 dpi): la misma
 * imagen sirve de vista previa, de descarga JPG y de página completa del PDF.
 *
 * El diseño se dibuja por código (franjas, aro amarillo, líneas de firma) en
 * lugar de componer las mitades apaisadas 1.png/2.png del prototipo:
 * - Arriba: logotipo de la fundación, nombre y edad del niño.
 * - Al centro: el QR dentro del aro amarillo de la identidad original y el
 *   código de 6 caracteres en grande, legible para captura manual.
 * - Abajo: procedencia y hospital, y los acompañantes con línea de firma.
 *
 * Igual que antes, el QR se genera localmente y en memoria, los assets viven
 * versionados en resources/ficha/ y la salida va a storage/app/fichas/
 * (disco local NO público, servido solo autenticado vía FichaController).
 */
class FichaGenerator
{
    /** Carta a 150 dpi; FichaController regenera los carnets con otro tamaño. */
    public const ANCHO = 1275;

    public const ALTO = 1650;

    private const AMARILLO = '#FFC72C';

    private const AZUL = '#4872AE';

    private const TINTA = '#333333';

    private const GRIS = '#666666';

    public function __construct(private readonly QrCodeService $qr) {}

    /** Genera el carnet y regresa la ruta absoluta del JPG. */
    public function generar(Nino $nino): string
    {
        $nino->loadMissing(['acompanantes.parentesco', 'estado', 'pais', 'hospital']);

        $manager = new ImageManager(new Driver);
        $img = $manager->create(self::ANCHO, self::ALTO)->fill('ffffff');
        $centro = intdiv(self::ANCHO, 2);

        // Franjas institucionales arriba y abajo
        $this->franja($img, 0, self::AMARILLO, 20);
        $this->franja($img, 20, self::AZUL, 6);
        $this->franja($img, self::ALTO - 50, self::AMARILLO, 8);
        $this->franja($img, self::ALTO - 42, self::AZUL, 42);

        // Logotipo centrado (proporción original ~2.6:1)
        $img->place($manager->read($this->asset('logo_n.png'))->resize(580, 223), 'top-left', $centro - 290, 60);

        // Nombre y edad, centrados bajo el logotipo
        $this->texto($img, $nino->nombreCompleto().', '.$nino->edad.' años', $centro, 330, 40, self::TINTA, 'Bold', 'center', 1080);

        // QR dentro del aro amarillo (la ventana circular del carnet original)
        $img->drawCircle($centro, 745, function (CircleFactory $circulo) {
            $circulo->radius(282);
            $circulo->background(self::AMARILLO);
        });
        $img->drawCircle($centro, 745, function (CircleFactory $circulo) {
            $circulo->radius(264);
            $circulo->background('#ffffff');
        });
        $qr = $manager->read($this->qr->render($nino->qr))->resize(360, 360);
        $img->place($qr, 'top-left', $centro - 180, 565);

        // Código en grande, para captura manual cuando el lector falla
        $this->texto($img, $nino->qr, $centro, 1050, 54, '#000000', 'Bold', 'center');

        // Panel de procedencia y hospital
        $img->drawRectangle(90, 1140, function (RectangleFactory $panel) {
            $panel->size(self::ANCHO - 180, 168);
            $panel->background('#F2F6FB');
        });
        $img->drawRectangle(90, 1140, function (RectangleFactory $acento) {
            $acento->size(8, 168);
            $acento->background(self::AZUL);
        });
        $img->place($manager->read($this->asset('loc.png'))->resize(40, 40), 'top-left', 130, 1165);
        $this->texto($img, (string) $nino->localidad, 186, 1166, 28, '#444444', 'Bold');
        $this->texto($img, trim($nino->municipio.', '.$nino->estado->estado.', '.$nino->pais->pais, ', '), 186, 1206, 24, self::GRIS);
        $img->place($manager->read($this->asset('hos.png'))->resize(40, 40), 'top-left', 130, 1246);
        $this->texto($img, $nino->hospital->hospital, 186, 1252, 24, self::GRIS, 'Medium', 'left', 950);

        // Acompañantes, cada uno con su línea de firma
        $this->texto($img, 'Acompañantes', 100, 1330, 30, self::TINTA, 'Bold');
        $img->drawRectangle(100, 1372, function (RectangleFactory $acento) {
            $acento->size(230, 5);
            $acento->background(self::AMARILLO);
        });

        if ($nino->acompanantes->isEmpty()) {
            $this->texto($img, 'Sin acompañantes registrados.', 100, 1400, 24, self::GRIS);
        }

        // Con más de tres acompañantes el paso se comprime para no invadir el pie.
        $y = 1396;
        $paso = min(62, intdiv(max(1590 - $y, 0), max($nino->acompanantes->count(), 1)));
        foreach ($nino->acompanantes as $acompanante) {
            $detalle = implode(' · ', array_filter([
                $acompanante->parentesco->parentesco,
                $acompanante->sexo,
                $acompanante->edad !== null ? $acompanante->edad.' años' : null,
            ]));
            $this->texto($img, $acompanante->nombreCompleto().' — '.$detalle, 100, $y, 24, '#444444');
            $img->drawLine(function (LineFactory $linea) use ($y, $paso) {
                $linea->from(100, $y + $paso - 14);
                $linea->to(self::ANCHO - 100, $y + $paso - 14);
                $linea->color('#AAB6C6');
                $linea->width(2);
            });
            $y += $paso;
        }

        // Nombre de la casa en la franja azul del pie
        $this->texto($img, 'Casa Ronald McDonald Puebla', $centro, self::ALTO - 34, 22, '#ffffff', 'Bold', 'center');

        $ruta = self::ruta($nino);
        File::ensureDirectoryExists(dirname($ruta));
        $img->toJpeg(95)->save($ruta);

        return $ruta;
    }

    public static function ruta(Nino $nino): string
    {
        return storage_path('app/fichas/'.$nino->id.'.jpg');
    }

    private function franja(ImageInterface $img, int $y, string $color, int $alto): void
    {
        $img->drawRectangle(0, $y, function (RectangleFactory $franja) use ($color, $alto) {
            $franja->size(self::ANCHO, $alto);
            $franja->background($color);
        });
    }

    private function texto(
        ImageInterface $img,
        string $contenido,
        int $x,
        int $y,
        int $tamano,
        string $color,
        string $fuente = 'Medium',
        string $alineacion = 'left',
        ?int $ancho = null,
    ): void {
        $img->text($contenido, $x, $y, function (FontFactory $font) use ($tamano, $color, $fuente, $alineacion, $ancho) {
            $font->filename($this->asset('Raleway-'.$fuente.'.ttf'));
            $font->color($color);
            $font->size($tamano);
            $font->align($alineacion);
            $font->valign('top');
            if ($ancho !== null) {
                $font->wrap($ancho);
            }
        });
    }

    private function asset(string $archivo): string
    {
        return resource_path('ficha/'.$archivo);
    }
}
