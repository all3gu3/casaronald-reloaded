<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pantalla_de_login_carga(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_un_usuario_activo_puede_iniciar_sesion(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('inicio'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_una_contrasena_incorrecta_no_autentica(): void
    {
        $user = User::factory()->create();

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'incorrecta',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_una_cuenta_desactivada_no_puede_iniciar_sesion(): void
    {
        $user = User::factory()->inactivo()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_el_logout_cierra_la_sesion(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_un_invitado_es_redirigido_al_login(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/administracion')->assertRedirect('/login');
    }

    public function test_un_usuario_autenticado_ve_el_inicio(): void
    {
        $this->actingAs(User::factory()->create())->get('/')->assertOk();
    }

    public function test_los_documentos_de_propuesta_son_publicos(): void
    {
        $this->get('/casita-secreta')->assertOk();
    }
}
