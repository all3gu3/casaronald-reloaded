<?php

namespace Tests\Feature;

use App\Enums\Servicio;
use App\Models\EntradaSalida;
use App\Models\Nino;
use App\Models\RegistroServicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ScanTest extends TestCase
{
    use RefreshDatabase;

    private function registrar(Nino $nino, string $accion, ?User $user = null)
    {
        return $this->actingAs($user ?? User::factory()->create())
            ->postJson('/escanear/registrar', ['codigo' => $nino->qr, 'accion' => $accion]);
    }

    public function test_cada_servicio_crea_su_bitacora_ligada_por_nino_id(): void
    {
        $nino = Nino::factory()->create();

        foreach (['lavanderia', 'comedor', 'escuela', 'transporte'] as $accion) {
            $this->registrar($nino, $accion)->assertCreated();
        }

        $this->assertSame(4, RegistroServicio::count());
        RegistroServicio::all()->each(function (RegistroServicio $registro) use ($nino) {
            // FK dura + qr de despliegue (el prototipo solo guardaba el string qr).
            $this->assertSame($nino->id, $registro->nino_id);
            $this->assertSame($nino->qr, $registro->qr);
        });
        $this->assertSame(1, RegistroServicio::where('servicio', Servicio::Comedor)->count());
    }

    public function test_la_respuesta_incluye_la_dieta_para_el_comedor(): void
    {
        $nino = Nino::factory()->create(['alerg_alimentos' => 'Cacahuate']);

        $this->registrar($nino, 'comedor')
            ->assertCreated()
            ->assertJsonPath('nino.nombre_completo', $nino->nombreCompleto())
            ->assertJsonPath('nino.dieta', $nino->tipoDieta->tipo_dieta)
            ->assertJsonPath('nino.alerg_alimentos', 'Cacahuate');
    }

    public function test_la_entrada_abre_registro_con_hora_exacta(): void
    {
        $this->travelTo(Carbon::parse('2026-09-03 14:30:45'));
        $nino = Nino::factory()->create();

        $this->registrar($nino, 'entrada')->assertCreated();

        $registro = EntradaSalida::first();
        $this->assertSame($nino->id, $registro->nino_id);
        // DATETIME de verdad: la columna DATE del prototipo truncaba a medianoche.
        $this->assertSame('2026-09-03 14:30:45', $registro->entrada->format('Y-m-d H:i:s'));
        $this->assertNull($registro->salida);
    }

    public function test_el_ciclo_entrada_salida_reentrada(): void
    {
        $nino = Nino::factory()->create();
        $user = User::factory()->create();

        $this->registrar($nino, 'entrada', $user)->assertCreated();

        // Doble entrada sin salida → error claro, no fila duplicada.
        $this->registrar($nino, 'entrada', $user)->assertUnprocessable();
        $this->assertSame(1, EntradaSalida::count());

        $this->registrar($nino, 'salida', $user)->assertCreated();
        $this->assertNotNull(EntradaSalida::first()->salida);

        // Reentrada → fila NUEVA.
        $this->registrar($nino, 'entrada', $user)->assertCreated();
        $this->assertSame(2, EntradaSalida::count());
        $this->assertSame(1, EntradaSalida::abiertas()->count());
    }

    public function test_salida_sin_entrada_abierta_es_422_no_500(): void
    {
        // El prototipo hacía null-deref (500) en este caso.
        $nino = Nino::factory()->create();

        $this->registrar($nino, 'salida')
            ->assertUnprocessable()
            ->assertJsonPath('message', fn ($msg) => str_contains($msg, 'No hay una entrada abierta'));

        $this->assertSame(0, EntradaSalida::count());
    }

    public function test_codigo_desconocido_y_accion_invalida_se_rechazan(): void
    {
        Nino::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/escanear/registrar', ['codigo' => 'ZZZ999', 'accion' => 'comedor'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('codigo');

        $this->actingAs($user)->postJson('/escanear/registrar', ['codigo' => Nino::first()->qr, 'accion' => 'spa'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('accion');

        $this->assertSame(0, RegistroServicio::count());
    }

    public function test_el_codigo_se_normaliza_a_mayusculas(): void
    {
        $nino = Nino::factory()->create(['qr' => 'A1B2C3']);

        $this->actingAs(User::factory()->create())
            ->postJson('/escanear/registrar', ['codigo' => 'a1b2c3', 'accion' => 'comedor'])
            ->assertCreated();

        $this->assertSame(1, RegistroServicio::count());
    }

    public function test_solo_post_y_solo_con_sesion(): void
    {
        $nino = Nino::factory()->create();

        // Sin sesión: nada de registrar.
        $this->postJson('/escanear/registrar', ['codigo' => $nino->qr, 'accion' => 'comedor'])
            ->assertUnauthorized();

        // El prototipo mutaba estado por GET (sin CSRF): ahora es 405.
        $this->actingAs(User::factory()->create())
            ->getJson('/escanear/registrar?codigo='.$nino->qr.'&accion=comedor')
            ->assertMethodNotAllowed();

        $this->assertSame(0, RegistroServicio::count());
    }

    public function test_la_ventana_de_escaneo_aparece_en_las_paginas_de_quien_puede_escanear(): void
    {
        // La antigua página /escanear ya no existe: la interfaz vive en la
        // ventana flotante incluida en el esqueleto de toda página.
        $this->actingAs(User::factory()->create())->get('/')
            ->assertOk()
            ->assertSee('escaneo-velo')
            ->assertSee('RUTA_REGISTRAR');

        $this->actingAs(User::factory()->trabajadorSocial()->create())->get('/')
            ->assertOk()
            ->assertDontSee('escaneo-velo');
    }
}
