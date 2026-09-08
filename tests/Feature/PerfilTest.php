<?php

namespace Tests\Feature;

use App\Enums\Accion;
use App\Models\RegistroActividad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerfilTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_ve_su_propio_perfil_con_su_actividad(): void
    {
        $user = User::factory()->staff()->create();
        $otro = User::factory()->create();

        RegistroActividad::create(['user_id' => $user->id, 'accion' => Accion::Escaneo, 'detalle' => 'Lavandería — Niño Ejemplo']);
        RegistroActividad::create(['user_id' => $otro->id, 'accion' => Accion::Descarga, 'detalle' => 'Expediente ajeno']);

        $this->actingAs($user)->get('/perfil')
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee('Lavandería — Niño Ejemplo')
            ->assertSee(Accion::Escaneo->etiqueta())
            // Solo su propia actividad, no la de otras cuentas.
            ->assertDontSee('Expediente ajeno');
    }

    public function test_el_perfil_muestra_los_datos_de_la_cuenta(): void
    {
        $user = User::factory()->trabajadorSocial()->create();

        $this->actingAs($user)->get('/perfil')
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee($user->email)
            ->assertSee('Trabajador social')
            ->assertSee('Activa')
            ->assertSee($user->created_at->legibleFecha());
    }

    public function test_un_usuario_puede_abrir_su_perfil_por_id(): void
    {
        $user = User::factory()->staff()->create();

        $this->actingAs($user)->get("/perfil/{$user->id}")->assertOk();
    }

    public function test_un_usuario_no_master_no_ve_el_perfil_de_otro(): void
    {
        $user = User::factory()->staff()->create();
        $otro = User::factory()->create();

        $this->actingAs($user)->get("/perfil/{$otro->id}")->assertForbidden();
    }

    public function test_un_master_si_ve_el_perfil_de_otro(): void
    {
        $master = User::factory()->master()->create();
        $staff = User::factory()->staff()->create();

        RegistroActividad::create(['user_id' => $staff->id, 'accion' => Accion::InicioSesion, 'detalle' => null]);

        $this->actingAs($master)->get("/perfil/{$staff->id}")
            ->assertOk()
            ->assertSee($staff->name)
            ->assertSee(Accion::InicioSesion->etiqueta());
    }

    public function test_un_invitado_es_redirigido_a_login(): void
    {
        $this->get('/perfil')->assertRedirect(route('login'));
    }
}
