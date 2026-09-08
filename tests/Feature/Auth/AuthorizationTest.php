<?php

namespace Tests\Feature\Auth;

use App\Models\Nino;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_personal_no_puede_administrar_usuarios(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get('/administracion')->assertForbidden();
        $this->actingAs($staff)->post('/administracion', [
            'name' => 'Intruso',
            'email' => 'intruso@casaronald.local',
            'password' => 'secreta123',
            'role' => 'master',
        ])->assertForbidden();
    }

    public function test_la_cuenta_maestra_si_puede_administrar_usuarios(): void
    {
        $master = User::factory()->master()->create();

        $this->actingAs($master)->get('/administracion')->assertOk();
    }

    public function test_el_trabajo_social_consulta_registros_pero_no_escanea_ni_administra(): void
    {
        $trabajadorSocial = User::factory()->trabajadorSocial()->create();

        $this->actingAs($trabajadorSocial)->get('/registros')->assertOk();
        $this->actingAs($trabajadorSocial)->get('/registros-comedor')->assertOk();
        $this->actingAs($trabajadorSocial)->get('/datatables/ninos')->assertOk();

        $this->actingAs($trabajadorSocial)
            ->postJson('/escanear/registrar', ['codigo' => 'qr-x', 'accion' => 'entrada'])
            ->assertForbidden();

        $this->actingAs($trabajadorSocial)->get('/administracion')->assertForbidden();
        $this->actingAs($trabajadorSocial)->post('/administracion', [
            'name' => 'Colega',
            'email' => 'colega@casaronald.local',
            'password' => 'secreta123',
            'role' => 'staff',
        ])->assertForbidden();
    }

    public function test_el_personal_operativo_si_puede_escanear(): void
    {
        $staff = User::factory()->staff()->create();
        $nino = Nino::factory()->create();

        $this->actingAs($staff)
            ->postJson('/escanear/registrar', ['codigo' => $nino->qr, 'accion' => 'comedor'])
            ->assertCreated();
    }

    public function test_una_cuenta_desactivada_a_media_sesion_pierde_acceso(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/')->assertOk();

        $user->update(['is_active' => false]);

        $this->actingAs($user)->get('/')->assertRedirect('/login');
        $this->assertGuest();
    }
}
