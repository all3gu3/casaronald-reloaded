<?php

namespace Tests\Feature;

use App\Enums\Accion;
use App\Models\Nino;
use App\Models\RegistroActividad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActividadTest extends TestCase
{
    use RefreshDatabase;

    /** Payload de edición mínimo reciclando los catálogos del propio niño. */
    private function payloadDesdeNino(Nino $nino, array $extra = []): array
    {
        return array_merge([
            'fec_sol' => $nino->fecha_solicitud->toDateString(),
            'estatus' => $nino->estatus_estancia,
            'hos' => $nino->hospital_id,
            'nombre' => $nino->nombre,
            'app' => $nino->apellido_paterno,
            'apm' => $nino->apellido_materno,
            'fec_nac' => $nino->fecha_nacimiento->toDateString(),
            'sexo' => $nino->sexo,
            'pais' => $nino->pais_id,
            'est' => $nino->estado_id,
            'mun' => $nino->municipio,
            'zona' => $nino->zona_id,
            'tel1' => $nino->primer_telefono,
            'esc' => $nino->escolaridad_id,
            'rango' => $nino->clasificacion_social_id,
            'sal_min' => $nino->salario_minimo_id,
            'trab' => $nino->trabajador_social_id,
            'diet' => $nino->tipo_dieta_id,
        ], $extra);
    }

    public function test_el_inicio_de_sesion_queda_registrado(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/');

        $this->assertDatabaseHas('registro_actividad', [
            'user_id' => $user->id,
            'accion' => Accion::InicioSesion->value,
        ]);
    }

    public function test_un_login_fallido_no_registra_nada(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'incorrecta']);

        $this->assertDatabaseCount('registro_actividad', 0);
    }

    public function test_el_escaneo_queda_registrado(): void
    {
        $staff = User::factory()->staff()->create();
        $nino = Nino::factory()->create();

        $this->actingAs($staff)
            ->postJson('/escanear/registrar', ['codigo' => $nino->qr, 'accion' => 'entrada'])
            ->assertCreated();

        $registro = RegistroActividad::sole();
        $this->assertSame($staff->id, $registro->user_id);
        $this->assertSame(Accion::Escaneo, $registro->accion);
        $this->assertStringContainsString($nino->qr, $registro->detalle);
    }

    public function test_las_descargas_quedan_registradas(): void
    {
        $trabajadorSocial = User::factory()->trabajadorSocial()->create();
        $nino = Nino::factory()->create();

        $this->actingAs($trabajadorSocial)->get('/expedientes/'.$nino->id.'/pdf?download=1')->assertOk();
        $this->actingAs($trabajadorSocial)->get('/fichas/'.$nino->id.'?download=1')->assertOk();

        // Ver el documento en pantalla (la vista previa) no es una descarga:
        // no debe anotarse.
        $this->actingAs($trabajadorSocial)->get('/fichas/'.$nino->id)->assertOk();
        $this->actingAs($trabajadorSocial)->get('/expedientes/'.$nino->id.'/pdf')->assertOk();

        $descargas = RegistroActividad::where('accion', Accion::Descarga->value)->get();
        $this->assertCount(2, $descargas);
        $this->assertStringContainsString('Reporte PDF', $descargas[0]->detalle);
        $this->assertStringContainsString('Carnet QR', $descargas[1]->detalle);
    }

    public function test_la_edicion_queda_registrada(): void
    {
        $staff = User::factory()->staff()->create();
        $nino = Nino::factory()->create();

        $this->actingAs($staff)
            ->putJson('/ninos/'.$nino->id, $this->payloadDesdeNino($nino, ['nombre' => 'Editado']))
            ->assertOk();

        $registro = RegistroActividad::sole();
        $this->assertSame(Accion::Edicion, $registro->accion);
        $this->assertStringContainsString($nino->qr, $registro->detalle);
    }

    public function test_la_bitacora_es_solo_para_administradores(): void
    {
        $this->actingAs(User::factory()->staff()->create())
            ->getJson('/administracion/actividad')->assertForbidden();
        $this->actingAs(User::factory()->trabajadorSocial()->create())
            ->getJson('/administracion/actividad')->assertForbidden();
        $this->actingAs(User::factory()->master()->create())
            ->getJson('/administracion/actividad')->assertOk();
    }

    public function test_la_bitacora_filtra_por_usuario(): void
    {
        $master = User::factory()->master()->create();
        [$una, $otra] = User::factory()->count(2)->create();
        RegistroActividad::create(['user_id' => $una->id, 'accion' => Accion::InicioSesion, 'detalle' => null]);
        RegistroActividad::create(['user_id' => $una->id, 'accion' => Accion::Escaneo, 'detalle' => 'X']);
        RegistroActividad::create(['user_id' => $otra->id, 'accion' => Accion::Descarga, 'detalle' => 'Y']);

        $todo = $this->actingAs($master)->getJson('/administracion/actividad')->assertOk()->json('data');
        $this->assertCount(3, $todo);

        $filtrado = $this->actingAs($master)
            ->getJson('/administracion/actividad?usuario='.$una->id)
            ->assertOk()
            ->json('data');
        $this->assertCount(2, $filtrado);
        $this->assertSame([$una->name, $una->name], array_column($filtrado, 'usuario'));
    }
}
