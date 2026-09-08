<?php

namespace Tests\Feature;

use App\Models\ClasificacionSocial;
use App\Models\EdoSalud;
use App\Models\Escolaridad;
use App\Models\Estado;
use App\Models\Hospital;
use App\Models\Nino;
use App\Models\Ocupacion;
use App\Models\Pais;
use App\Models\Parentesco;
use App\Models\SalarioMinimo;
use App\Models\TipoDieta;
use App\Models\TipoTratamiento;
use App\Models\TrabajadorSocial;
use App\Models\User;
use App\Models\Zona;
use App\Services\FotoPerfil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IntakeTest extends TestCase
{
    use RefreshDatabase;

    private function catalogos(): array
    {
        return [
            'hospital' => Hospital::factory()->create(),
            'pais' => Pais::factory()->create(),
            'estado' => Estado::factory()->create(),
            'zona' => Zona::factory()->create(),
            'escolaridad' => Escolaridad::factory()->create(),
            'salario' => SalarioMinimo::factory()->create(),
            'trabajador' => TrabajadorSocial::factory()->create(),
            'dieta' => TipoDieta::factory()->create(),
            'clasificacion' => ClasificacionSocial::factory()->create(),
        ];
    }

    private function payloadValido(array $catalogos, array $extra = []): array
    {
        return array_merge([
            'fec_sol' => '2026-09-01',
            'estatus' => 'Primera vez',
            'hos' => $catalogos['hospital']->id,
            'serv' => 'Oncología',
            'nombre' => 'María José',
            'app' => 'Paterno',
            'apm' => 'Materno',
            'fec_nac' => '2019-04-15',
            'sexo' => 'Femenino',
            'pais' => $catalogos['pais']->id,
            'est' => $catalogos['estado']->id,
            'mun' => 'Acatzingo',
            'calle' => 'Reforma',
            'num_c' => '15-B',
            'col' => 'Centro',
            'loca' => 'Acatzingo',
            'cp' => '75110',
            'zona' => $catalogos['zona']->id,
            'tel1' => '2221234567',
            'tel2' => '2227654321',
            'dial' => 'Náhuatl',
            'esc' => $catalogos['escolaridad']->id,
            'rango' => $catalogos['clasificacion']->id,
            'sal_min' => $catalogos['salario']->id,
            'trab' => $catalogos['trabajador']->id,
            'medico' => 'Dra. Pérez',
            'diag' => 'Leucemia',
            'ale_alim' => 'Cacahuate',
            'ale_med' => 'Penicilina',
            'obs' => 'Sin observaciones',
            'diet' => $catalogos['dieta']->id,
            'fec_ing' => '2026-09-02',
            'fec_sal' => '2026-09-20',
        ], $extra);
    }

    public function test_el_alta_guarda_todos_los_campos_correctamente(): void
    {
        $catalogos = $this->catalogos();
        $tratamientos = TipoTratamiento::factory()->count(3)->create();

        $response = $this->actingAs(User::factory()->create())->postJson('/ninos', $this->payloadValido($catalogos, [
            'tt' => $tratamientos->pluck('id')->all(),
        ]));

        $response->assertCreated();

        $nino = Nino::first();

        // El defecto insignia del prototipo: app→materno / apm→paterno (cruzados).
        $this->assertSame('Paterno', $nino->apellido_paterno);
        $this->assertSame('Materno', $nino->apellido_materno);

        // Fechas de ingreso/salida: el prototipo leía fec_in/fec_eg y siempre quedaban NULL.
        $this->assertSame('2026-09-02', $nino->fecha_ingreso->toDateString());
        $this->assertSame('2026-09-20', $nino->fecha_salida->toDateString());

        // Sexo tal cual (el mapeo M/F daba siempre "Indefinido").
        $this->assertSame('Femenino', $nino->sexo);

        // Alergias y tratamientos: antes se descartaban en silencio.
        $this->assertSame('Cacahuate', $nino->alerg_alimentos);
        $this->assertSame('Penicilina', $nino->alerg_medicamentos);
        $this->assertCount(3, $nino->tiposTratamiento);

        // "15-B" rompía la columna INT del prototipo.
        $this->assertSame('15-B', $nino->numero);

        // QR asignado: 6 caracteres, 3 dígitos + 3 letras.
        $this->assertMatchesRegularExpression('/^[0-9A-Z]{6}$/', $nino->qr);
        $this->assertSame(3, preg_match_all('/[0-9]/', $nino->qr));
        $this->assertSame(3, preg_match_all('/[A-Z]/', $nino->qr));
    }

    public function test_la_foto_se_sube_y_persiste(): void
    {
        Storage::fake('public');
        $catalogos = $this->catalogos();

        $this->actingAs(User::factory()->create())->post('/ninos', $this->payloadValido($catalogos, [
            'image' => UploadedFile::fake()->image('nino.jpg', 300, 300),
        ]), ['Accept' => 'application/json'])->assertCreated();

        $nino = Nino::first();
        $this->assertNotNull($nino->foto, 'la foto ya no se descarta (serialize() nunca la enviaba)');
        Storage::disk('public')->assertExists($nino->foto);
    }

    public function test_la_foto_se_normaliza_a_jpeg_y_se_reduce(): void
    {
        Storage::fake('public');
        $catalogos = $this->catalogos();

        // Un PNG grande: el navegador normaliza antes de subir, pero el
        // servidor no puede confiar en eso (lector USB, cliente viejo, curl).
        $this->actingAs(User::factory()->create())->post('/ninos', $this->payloadValido($catalogos, [
            'image' => UploadedFile::fake()->image('nino.png', 2400, 1800),
        ]), ['Accept' => 'application/json'])->assertCreated();

        $nino = Nino::first();
        $this->assertStringEndsWith('.jpg', $nino->foto, 'todo formato termina como JPEG mostrable');

        $bytes = Storage::disk('public')->get($nino->foto);
        $this->assertSame("\xFF\xD8\xFF", substr($bytes, 0, 3), 'los bytes son de un JPEG real');

        [$ancho, $alto] = getimagesizefromstring($bytes);
        $this->assertSame(FotoPerfil::LADO_MAXIMO, $ancho, 'el lado largo se reduce al máximo permitido');
        $this->assertSame(900, $alto, 'la proporción original se conserva');
    }

    public function test_la_validacion_esta_activa(): void
    {
        // El prototipo tenía la validación comentada: aceptaba cualquier cosa.
        $this->actingAs(User::factory()->create())
            ->postJson('/ninos', ['nombre' => 'Solo un nombre'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['fec_sol', 'app', 'apm', 'fec_nac', 'sexo', 'hos']);
    }

    public function test_un_invitado_no_puede_dar_de_alta(): void
    {
        $this->postJson('/ninos', [])->assertUnauthorized();
    }

    public function test_la_edicion_actualiza_campos_y_tratamientos_sin_cambiar_el_qr(): void
    {
        $catalogos = $this->catalogos();
        $tratamientos = TipoTratamiento::factory()->count(3)->create();
        $nino = Nino::factory()->create();
        $nino->tiposTratamiento()->sync([$tratamientos[0]->id]);
        $qrOriginal = $nino->qr;

        $this->actingAs(User::factory()->create())
            ->putJson('/ninos/'.$nino->id, $this->payloadValido($catalogos, [
                'nombre' => 'Nombre Corregido',
                'diag' => 'Diagnóstico actualizado',
                'tt' => [$tratamientos[1]->id, $tratamientos[2]->id],
            ]))
            ->assertOk();

        $nino->refresh();
        $this->assertSame('Nombre Corregido', $nino->nombre);
        $this->assertSame('Diagnóstico actualizado', $nino->diagnostico);
        $this->assertSame($qrOriginal, $nino->qr);
        $this->assertEqualsCanonicalizing(
            [$tratamientos[1]->id, $tratamientos[2]->id],
            $nino->tiposTratamiento->pluck('id')->all(),
        );
    }

    public function test_la_edicion_valida_igual_que_el_alta(): void
    {
        $nino = Nino::factory()->create();

        $this->actingAs(User::factory()->create())
            ->putJson('/ninos/'.$nino->id, ['nombre' => 'Solo un nombre'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['fec_sol', 'app', 'apm', 'fec_nac', 'sexo', 'hos']);
    }

    public function test_una_foto_nueva_reemplaza_y_borra_la_anterior(): void
    {
        Storage::fake('public');
        $catalogos = $this->catalogos();
        $anterior = UploadedFile::fake()->image('vieja.jpg')->store('fotos/ninos', 'public');
        $nino = Nino::factory()->create(['foto' => $anterior]);

        // _method=PUT vía POST: PHP no interpreta multipart en un PUT real,
        // igual que lo envía el formulario.
        $this->actingAs(User::factory()->create())
            ->post('/ninos/'.$nino->id, $this->payloadValido($catalogos, [
                '_method' => 'PUT',
                'image' => UploadedFile::fake()->image('nueva.jpg', 300, 300),
            ]), ['Accept' => 'application/json'])
            ->assertOk();

        $nino->refresh();
        $this->assertNotSame($anterior, $nino->foto);
        Storage::disk('public')->assertExists($nino->foto);
        Storage::disk('public')->assertMissing($anterior);
    }

    public function test_sin_foto_nueva_la_edicion_conserva_la_actual(): void
    {
        $catalogos = $this->catalogos();
        $nino = Nino::factory()->create(['foto' => 'fotos/ninos/actual.jpg']);

        $this->actingAs(User::factory()->create())
            ->putJson('/ninos/'.$nino->id, $this->payloadValido($catalogos))
            ->assertOk();

        $this->assertSame('fotos/ninos/actual.jpg', $nino->refresh()->foto);
    }

    public function test_el_formulario_de_edicion_se_precarga_con_valores_crudos(): void
    {
        $tratamiento = TipoTratamiento::factory()->create();
        $nino = Nino::factory()->create(['fecha_nacimiento' => '2019-04-15']);
        $nino->tiposTratamiento()->sync([$tratamiento->id]);

        $respuesta = $this->actingAs(User::factory()->create())
            ->getJson('/ninos/'.$nino->id.'/editar')
            ->assertOk()
            ->json();

        // Ids de catálogo y fechas ISO, no etiquetas: son para llenar inputs.
        $this->assertSame($nino->hospital_id, $respuesta['campos']['hos']);
        $this->assertSame('2019-04-15', $respuesta['campos']['fec_nac']);
        $this->assertSame($nino->apellido_paterno, $respuesta['campos']['app']);
        $this->assertSame([$tratamiento->id], $respuesta['tt']);
    }

    public function test_un_invitado_no_puede_editar(): void
    {
        $nino = Nino::factory()->create();

        $this->putJson('/ninos/'.$nino->id, [])->assertUnauthorized();
        $this->getJson('/ninos/'.$nino->id.'/editar')->assertUnauthorized();
    }

    public function test_alta_de_acompanante_con_edad_calculada(): void
    {
        $nino = Nino::factory()->create();
        $payload = [
            'nino' => $nino->id,
            'nombre' => 'Rosa',
            'app' => 'García',
            'apm' => 'López',
            'fec_nac' => now()->subYears(34)->toDateString(),
            'sexo' => 'Femenino',
            'paren' => Parentesco::factory()->create()->id,
            'edoSalud' => EdoSalud::factory()->create()->id,
            'esc' => Escolaridad::factory()->create()->id,
            'ocu' => Ocupacion::factory()->create()->id,
            'trab' => 1,
            'goce' => 0,
            'seg' => 1,
            'casa' => 0,
            'asist' => 1,
            'rent' => 1500,
            'dep_ec' => 2,
            'ing' => 6000,
            'obs' => 'Observación que antes se perdía',
        ];

        $this->actingAs(User::factory()->create())->postJson('/acompanantes', $payload)->assertCreated();

        $acompanante = $nino->acompanantes()->first();
        $this->assertSame('García', $acompanante->apellido_paterno);
        $this->assertSame('34', $acompanante->edad);
        $this->assertTrue($acompanante->trabaja);
        $this->assertFalse($acompanante->casa_propia);
        $this->assertSame('Observación que antes se perdía', $acompanante->observaciones);
    }

    public function test_resolucion_por_qr(): void
    {
        $nino = Nino::factory()->create([
            'nombre' => 'Ronald',
            'apellido_paterno' => 'Mc',
            'apellido_materno' => 'Uno',
            'sexo' => 'Masculino',
        ]);

        $respuesta = $this->actingAs(User::factory()->create())
            ->getJson('/ninos/por-qr/'.$nino->qr)
            ->assertOk()
            ->json();

        // Orden correcto de apellidos (el prototipo imprimía materno-paterno).
        $this->assertSame('Ronald Mc Uno', $respuesta['nombre_completo']);
        // Sexo verbatim (antes: siempre "Indefinido" para datos reales).
        $this->assertSame('Masculino', $respuesta['sexo']);
        $this->assertArrayHasKey('dieta', $respuesta);

        // Desconocido → 404 (el prototipo devolvía {} con 200).
        $this->actingAs(User::factory()->create())
            ->getJson('/ninos/por-qr/ZZZ999')
            ->assertNotFound();
    }
}
