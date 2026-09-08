<?php

namespace Tests\Feature;

use App\Enums\Servicio;
use App\Models\EntradaSalida;
use App\Models\Nino;
use App\Models\RegistroServicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatatableTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_concentrado_de_ninos_muestra_el_nombre_en_orden_correcto(): void
    {
        Nino::factory()->create([
            'nombre' => 'Ronald',
            'apellido_paterno' => 'Mc',
            'apellido_materno' => 'Uno',
        ]);

        $this->actingAs(User::factory()->create())
            ->getJson('/datatables/ninos?draw=1&start=0&length=10')
            ->assertOk()
            ->assertJsonPath('data.0.nombre', 'Ronald Mc Uno');
    }

    public function test_los_concentrados_de_servicio_muestran_el_nombre_del_nino(): void
    {
        $nino = Nino::factory()->create(['nombre' => 'Ronald', 'apellido_paterno' => 'Mc', 'apellido_materno' => 'Dos']);
        RegistroServicio::factory()->recycle($nino)->create(['servicio' => Servicio::Comedor]);

        $respuesta = $this->actingAs(User::factory()->create())
            ->getJson('/datatables/servicios/comedor?draw=1&start=0&length=10')
            ->assertOk();

        // El prototipo solo mostraba el QR crudo: había que cruzarlo a mano.
        $respuesta->assertJsonPath('data.0.nino', 'Ronald Mc Dos');
        $respuesta->assertJsonPath('data.0.QR', $nino->qr);
    }

    public function test_un_servicio_desconocido_es_404(): void
    {
        $this->actingAs(User::factory()->create())
            ->getJson('/datatables/servicios/spa')
            ->assertNotFound();
    }

    public function test_entradas_salidas_ya_no_truena_con_estancias_abiertas(): void
    {
        $nino = Nino::factory()->create();
        EntradaSalida::factory()->recycle($nino)->create(['salida' => null]);
        EntradaSalida::factory()->recycle($nino)->cerrada()->create();

        // El endpoint del prototipo jamás regresó una fila (Builder sin get(),
        // format() sobre strings y null-deref con salida abierta).
        $respuesta = $this->actingAs(User::factory()->create())
            ->getJson('/datatables/entradas-salidas?draw=1&start=0&length=10')
            ->assertOk();

        $this->assertCount(2, $respuesta->json('data'));
        $salidas = collect($respuesta->json('data'))->pluck('salida');
        $this->assertContains('En Casa', $salidas, 'una estancia abierta se muestra como "En Casa"');
    }

    public function test_los_feeds_requieren_sesion(): void
    {
        $this->getJson('/datatables/ninos')->assertUnauthorized();
        $this->getJson('/datatables/servicios')->assertUnauthorized();
        $this->getJson('/datatables/servicios/comedor')->assertUnauthorized();
        $this->getJson('/datatables/entradas-salidas')->assertUnauthorized();
    }

    public function test_las_paginas_de_registros_cargan(): void
    {
        $user = User::factory()->create();

        foreach (['/expedientes', '/registros', '/registros-lavanderia', '/registros-comedor',
            '/registros-escuela', '/registros-transporte', '/registros-entradas-salidas'] as $pagina) {
            $this->actingAs($user)->get($pagina)->assertOk();
        }
    }
}
