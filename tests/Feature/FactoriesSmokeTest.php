<?php

namespace Tests\Feature;

use App\Models\Acompanante;
use App\Models\EntradaSalida;
use App\Models\Nino;
use App\Models\RegistroOperativo;
use App\Models\RegistroServicio;
use App\Models\TipoTratamiento;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Prueba de humo del esquema v2 + fábricas: cada modelo se puede construir con su
 * cadena completa de llaves foráneas sobre sqlite :memory:.
 */
class FactoriesSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_toda_la_cadena_de_fabricas_construye(): void
    {
        $nino = Nino::factory()->create();
        $acompanante = Acompanante::factory()->recycle($nino)->create();
        $registroServicio = RegistroServicio::factory()->recycle($nino)->create();
        $entrada = EntradaSalida::factory()->recycle($nino)->create();
        $operativo = RegistroOperativo::factory()->recycle($nino)->create();

        $this->assertMatchesRegularExpression('/^[0-9A-Z]{6}$/', $nino->qr);
        $this->assertSame($nino->id, $acompanante->nino->id);
        $this->assertSame($nino->qr, $registroServicio->qr, 'la bitácora hereda el QR del niño');
        $this->assertSame($nino->qr, $entrada->qr);
        $this->assertNull($operativo->fecha_egreso, 'una estancia abierta es representable');
        $this->assertIsInt($nino->edad, 'la edad se calcula desde fecha_nacimiento');
    }

    public function test_el_qr_del_nino_es_unico_a_nivel_base_de_datos(): void
    {
        $nino = Nino::factory()->create();

        $this->expectException(QueryException::class);
        Nino::factory()->create(['qr' => $nino->qr]);
    }

    public function test_los_tratamientos_se_persisten_en_el_pivote(): void
    {
        $nino = Nino::factory()->create();
        $tratamientos = TipoTratamiento::factory()->count(3)->create();

        $nino->tiposTratamiento()->sync($tratamientos->pluck('id'));

        $this->assertCount(3, $nino->fresh()->tiposTratamiento);
    }

    public function test_usuarios_master_y_staff(): void
    {
        $master = User::factory()->master()->create();
        $staff = User::factory()->create();
        $inactivo = User::factory()->inactivo()->create();

        $this->assertTrue($master->esMaster());
        $this->assertFalse($staff->esMaster());
        $this->assertFalse($inactivo->is_active);
    }
}
