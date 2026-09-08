<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\MasterUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function master(): User
    {
        return User::factory()->master()->create();
    }

    public function test_la_url_vieja_de_usuarios_redirige_a_administracion(): void
    {
        $this->actingAs($this->master())->get('/usuarios')->assertRedirect('/administracion');
    }

    public function test_la_cuenta_maestra_crea_cuentas_del_personal(): void
    {
        $response = $this->actingAs($this->master())->post('/administracion', [
            'name' => 'Trabajadora Social',
            'email' => 'ts@casaronald.local',
            'password' => 'secreta123',
            'role' => 'staff',
        ]);

        $response->assertRedirect(route('administracion.index'));

        $nueva = User::where('email', 'ts@casaronald.local')->first();
        $this->assertNotNull($nueva);
        $this->assertSame(Role::Staff, $nueva->role);
        $this->assertTrue($nueva->is_active);
        $this->assertTrue(Hash::check('secreta123', $nueva->password), 'la contraseña se guarda hasheada');

        // La cuenta recién creada puede iniciar sesión.
        $this->post('/logout');
        $this->post('/login', ['email' => 'ts@casaronald.local', 'password' => 'secreta123'])
            ->assertRedirect(route('inicio'));
    }

    public function test_valida_correo_duplicado_y_contrasena_corta(): void
    {
        $existente = User::factory()->create();

        $this->actingAs($this->master())->post('/administracion', [
            'name' => 'Alguien',
            'email' => $existente->email,
            'password' => 'corta',
            'role' => 'staff',
        ])->assertSessionHasErrors(['email', 'password']);
    }

    public function test_desactivar_una_cuenta_bloquea_su_siguiente_login(): void
    {
        $master = $this->master();
        $staff = User::factory()->create();

        $this->actingAs($master)->patch("/administracion/{$staff->id}/estado")
            ->assertRedirect(route('administracion.index'));

        $this->assertFalse($staff->fresh()->is_active);

        $this->post('/logout');
        $this->post('/login', ['email' => $staff->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        // Reactivación
        $this->actingAs($master)->patch("/administracion/{$staff->id}/estado");
        $this->assertTrue($staff->fresh()->is_active);
    }

    public function test_la_cuenta_maestra_no_puede_desactivarse_a_si_misma(): void
    {
        $master = $this->master();

        $this->actingAs($master)->patch("/administracion/{$master->id}/estado")
            ->assertSessionHasErrors('usuario');

        $this->assertTrue($master->fresh()->is_active);
    }

    public function test_restablecer_contrasena(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($this->master())->patch("/administracion/{$staff->id}/password", [
            'password' => 'renovada123',
        ])->assertRedirect(route('administracion.index'));

        $this->post('/logout');
        $this->post('/login', ['email' => $staff->email, 'password' => 'renovada123'])
            ->assertRedirect(route('inicio'));
    }

    public function test_el_seeder_crea_la_cuenta_maestra_desde_env(): void
    {
        $this->seed(MasterUserSeeder::class);

        $master = User::where('email', config('casaronald.master.email'))->first();
        $this->assertNotNull($master);
        $this->assertTrue($master->esMaster());
    }
}
