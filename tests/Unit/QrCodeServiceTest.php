<?php

namespace Tests\Unit;

use App\Models\Nino;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_formato_es_3_digitos_y_3_letras(): void
    {
        $servicio = new QrCodeService;

        for ($i = 0; $i < 25; $i++) {
            $codigo = $servicio->generar();
            $this->assertMatchesRegularExpression('/^[0-9A-Z]{6}$/', $codigo);
            $this->assertSame(3, preg_match_all('/[0-9]/', $codigo), "dígitos en {$codigo}");
            $this->assertSame(3, preg_match_all('/[A-Z]/', $codigo), "letras en {$codigo}");
        }
    }

    public function test_una_colision_provoca_reintento(): void
    {
        $existente = Nino::factory()->create(['qr' => 'A1B2C3']);

        // El prototipo tenía validaQR() como stub que siempre regresaba 1:
        // el bucle de colisiones jamás corría. Aquí forzamos la colisión.
        $servicio = new class(['A1B2C3', 'X9Y8Z7']) extends QrCodeService
        {
            public int $intentos = 0;

            public function __construct(private array $secuencia) {}

            protected function codigoAleatorio(): string
            {
                return $this->secuencia[$this->intentos++];
            }
        };

        $codigo = $servicio->generar();

        $this->assertSame('X9Y8Z7', $codigo, 'el código colisionado se descarta y se genera otro');
        $this->assertSame(2, $servicio->intentos);
        $this->assertNotSame($existente->qr, $codigo);
    }

    public function test_render_produce_un_png_en_memoria(): void
    {
        $png = (new QrCodeService)->render('A1B2C3');

        $this->assertStringStartsWith("\x89PNG", $png, 'firma PNG');
        $this->assertGreaterThan(100, strlen($png));

        // Endroid redondea al múltiplo del módulo QR (p. ej. 320 para size 300).
        [$ancho, $alto] = getimagesizefromstring($png);
        $this->assertGreaterThanOrEqual(300, $ancho);
        $this->assertSame($ancho, $alto);
    }
}
