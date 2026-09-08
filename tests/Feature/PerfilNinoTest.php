<?php

namespace Tests\Feature;

use App\Enums\Servicio;
use App\Models\Acompanante;
use App\Models\EntradaSalida;
use App\Models\Nino;
use App\Models\RegistroServicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerfilNinoTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pagina_del_nino_muestra_el_resumen_del_expediente(): void
    {
        $nino = Nino::factory()->create([
            'nombre' => 'Ronald',
            'apellido_paterno' => 'Mc',
            'apellido_materno' => 'Perfil',
            'observaciones' => 'Llega con su abuela.',
        ]);
        Acompanante::factory()->for($nino, 'nino')->create();

        $this->actingAs(User::factory()->staff()->create())
            ->get('/ninos/'.$nino->id.'/perfil')
            ->assertOk()
            ->assertSee('Ronald Mc Perfil')
            ->assertSee($nino->qr)
            ->assertSee($nino->hospital->hospital)
            ->assertSee('Llega con su abuela.')
            ->assertSee('abrirPreviewCarnet('.$nino->id.')', false)
            ->assertSee('Editar expediente')
            ->assertSee('img/avatar-nino.svg', false); // sin foto, el avatar de reserva
    }

    public function test_las_fechas_de_la_bitacora_se_muestran_legibles(): void
    {
        $nino = Nino::factory()->create();
        EntradaSalida::factory()->recycle($nino)->create(['entrada' => '2001-08-03 11:21:00', 'salida' => null]);

        $this->actingAs(User::factory()->create())
            ->getJson('/datatables/entradas-salidas?draw=1&start=0&length=10&nino='.$nino->id)
            ->assertOk()
            ->assertJsonPath('data.0.entrada', '3 de Agosto del 2001, 11:21 AM')
            ->assertJsonPath('data.0.salida', 'En Casa');
    }

    public function test_el_boton_del_pdf_respeta_el_permiso_de_descarga(): void
    {
        $nino = Nino::factory()->create();

        // Trabajo social y cuenta maestra ven la descarga; el personal operativo no.
        $this->actingAs(User::factory()->trabajadorSocial()->create())
            ->get('/ninos/'.$nino->id.'/perfil')
            ->assertOk()
            ->assertSee('/expedientes/'.$nino->id.'/pdf', false);

        $this->actingAs(User::factory()->staff()->create())
            ->get('/ninos/'.$nino->id.'/perfil')
            ->assertOk()
            ->assertDontSee('/expedientes/'.$nino->id.'/pdf', false);
    }

    public function test_los_feeds_de_la_bitacora_se_filtran_por_nino(): void
    {
        $nino = Nino::factory()->create(['nombre' => 'Ronald', 'apellido_paterno' => 'Mc', 'apellido_materno' => 'Uno']);
        $otro = Nino::factory()->create(['nombre' => 'Ronald', 'apellido_paterno' => 'Mc', 'apellido_materno' => 'Dos']);
        RegistroServicio::factory()->recycle($nino)->create(['servicio' => Servicio::Comedor]);
        RegistroServicio::factory()->recycle($otro)->create(['servicio' => Servicio::Comedor]);
        EntradaSalida::factory()->recycle($nino)->create();
        EntradaSalida::factory()->recycle($otro)->create();

        $usuario = User::factory()->create();

        // El concentrado combinado (el que usa la página del perfil)…
        $servicios = $this->actingAs($usuario)
            ->getJson('/datatables/servicios?draw=1&start=0&length=15&nino='.$nino->id)
            ->assertOk();
        $this->assertCount(1, $servicios->json('data'));
        $servicios->assertJsonPath('data.0.servicio_nombre', 'Comedor');

        // …y el feed por servicio también respeta el filtro.
        $comedor = $this->actingAs($usuario)
            ->getJson('/datatables/servicios/comedor?draw=1&start=0&length=10&nino='.$nino->id)
            ->assertOk();
        $this->assertCount(1, $comedor->json('data'));
        $comedor->assertJsonPath('data.0.nino', 'Ronald Mc Uno');

        $entradas = $this->actingAs($usuario)
            ->getJson('/datatables/entradas-salidas?draw=1&start=0&length=10&nino='.$nino->id)
            ->assertOk();
        $this->assertCount(1, $entradas->json('data'));
        $entradas->assertJsonPath('data.0.nino', 'Ronald Mc Uno');
    }

    public function test_sin_sesion_no_hay_perfil(): void
    {
        $nino = Nino::factory()->create();

        $this->get('/ninos/'.$nino->id.'/perfil')->assertRedirect('/login');
    }
}
