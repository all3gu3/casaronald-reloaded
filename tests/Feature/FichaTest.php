<?php

namespace Tests\Feature;

use App\Models\Acompanante;
use App\Models\Nino;
use App\Models\User;
use App\Services\FichaGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FichaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! (gd_info()['FreeType Support'] ?? false)) {
            $this->markTestSkipped('GD sin FreeType: el compositor de fichas no puede tipografiar.');
        }
    }

    protected function tearDown(): void
    {
        // Limpia los carnets generados por la prueba.
        foreach (glob(storage_path('app/fichas/*.jpg')) ?: [] as $archivo) {
            @unlink($archivo);
        }

        parent::tearDown();
    }

    public function test_la_ficha_se_genera_y_sirve_autenticada(): void
    {
        $nino = Nino::factory()->create();
        Acompanante::factory()->recycle($nino)->count(2)->create();

        $response = $this->actingAs(User::factory()->create())->get('/fichas/'.$nino->id);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');

        $ruta = FichaGenerator::ruta($nino);
        $this->assertFileExists($ruta);

        // Vertical, con la proporción del tamaño carta (1275×1650 a 150 dpi).
        [$ancho, $alto] = getimagesize($ruta);
        $this->assertSame(FichaGenerator::ANCHO, $ancho);
        $this->assertSame(FichaGenerator::ALTO, $alto);

        // Fuera del symlink público: el prototipo permitía enumerar los carnets.
        $this->assertStringNotContainsString('app/public', $ruta);
    }

    public function test_descargar_manda_el_encabezado_de_adjunto(): void
    {
        $nino = Nino::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get('/fichas/'.$nino->id.'?download=1')
            ->assertOk()
            ->assertDownload('ficha-'.$nino->qr.'.jpg');
    }

    public function test_el_carnet_tambien_sale_como_pdf_tamano_carta(): void
    {
        $nino = Nino::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get('/fichas/'.$nino->id.'?formato=pdf')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->actingAs(User::factory()->create())
            ->get('/fichas/'.$nino->id.'?formato=pdf&download=1')
            ->assertOk()
            ->assertDownload('ficha-'.$nino->qr.'.pdf');
    }

    public function test_el_formato_qr_pdf_eliminado_cae_al_carnet_jpg(): void
    {
        // La opción "Solo QR (PDF)" se eliminó: cualquier formato desconocido
        // (incluido el viejo qr-pdf) sirve el carnet JPG por omisión.
        $nino = Nino::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get('/fichas/'.$nino->id.'?formato=qr-pdf')
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_invitados_y_desconocidos_no_ven_fichas(): void
    {
        $nino = Nino::factory()->create();

        $this->get('/fichas/'.$nino->id)->assertRedirect('/login');

        $this->actingAs(User::factory()->create())
            ->get('/fichas/999999')
            ->assertNotFound();
    }
}
