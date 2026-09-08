<?php

namespace Tests\Feature;

use App\Models\Acompanante;
use App\Models\Nino;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpedienteTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_consulta_del_expediente_resuelve_catalogos_y_acompanantes(): void
    {
        $nino = Nino::factory()->create(['observaciones' => 'Llega con su abuela.']);
        Acompanante::factory()->for($nino, 'nino')->create();

        $respuesta = $this->actingAs(User::factory()->staff()->create())
            ->getJson('/ninos/'.$nino->id);

        $respuesta->assertOk()
            ->assertJsonPath('qr', $nino->qr)
            ->assertJsonPath('nombre_completo', $nino->nombreCompleto())
            ->assertJsonPath('hospital', $nino->hospital->hospital)
            ->assertJsonPath('trabajador_social', $nino->trabajadorSocial->trabajador_social)
            ->assertJsonPath('observaciones', 'Llega con su abuela.')
            ->assertJsonCount(1, 'acompanantes');
    }

    public function test_trabajo_social_y_cuenta_maestra_descargan_el_reporte_pdf(): void
    {
        $nino = Nino::factory()->create();

        foreach ([User::factory()->trabajadorSocial(), User::factory()->master()] as $factory) {
            $usuario = $factory->create();

            // Sin parámetros el PDF llega en línea (la vista previa del perfil)…
            $this->actingAs($usuario)->get('/expedientes/'.$nino->id.'/pdf')
                ->assertOk()
                ->assertHeader('content-type', 'application/pdf');

            // …y con ?download=1 llega como adjunto.
            $this->actingAs($usuario)->get('/expedientes/'.$nino->id.'/pdf?download=1')
                ->assertOk()
                ->assertDownload('expediente-'.$nino->qr.'.pdf');
        }
    }

    public function test_el_personal_operativo_no_descarga_el_reporte_pdf(): void
    {
        $nino = Nino::factory()->create();

        $this->actingAs(User::factory()->staff()->create())
            ->get('/expedientes/'.$nino->id.'/pdf')
            ->assertForbidden();
    }

    public function test_sin_sesion_no_hay_expediente_ni_reporte(): void
    {
        $nino = Nino::factory()->create();

        $this->getJson('/ninos/'.$nino->id)->assertUnauthorized();
        $this->get('/expedientes/'.$nino->id.'/pdf')->assertRedirect('/login');
    }
}
