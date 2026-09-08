<?php

namespace Tests\Feature;

use App\Enums\Accion;
use App\Enums\Servicio;
use App\Models\Nino;
use App\Models\RegistroServicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class ReporteTest extends TestCase
{
    use RefreshDatabase;

    public function test_los_reportes_son_solo_de_administradores(): void
    {
        $this->getJson('/administracion/reportes/datos')->assertUnauthorized();

        $this->actingAs(User::factory()->staff()->create())
            ->getJson('/administracion/reportes/datos?periodo=dia')
            ->assertForbidden();

        $this->actingAs(User::factory()->trabajadorSocial()->create())
            ->getJson('/administracion/reportes/excel?periodo=dia')
            ->assertForbidden();
    }

    public function test_el_desglose_del_dia_solo_cuenta_registros_de_hoy(): void
    {
        RegistroServicio::factory()->count(2)->create(['servicio' => Servicio::Comedor]);
        RegistroServicio::factory()->create([
            'servicio' => Servicio::Lavanderia,
            'created_at' => now()->subDay(),
        ]);

        $respuesta = $this->actingAs(User::factory()->master()->create())
            ->getJson('/administracion/reportes/datos?periodo=dia')
            ->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonCount(24, 'labels'); // un día se desglosa por hora

        $series = collect($respuesta->json('series'))->keyBy('servicio');
        $this->assertSame(2, $series['comedor']['total']);
        $this->assertSame(0, $series['lavanderia']['total']);
        $this->assertSame(2, array_sum($series['comedor']['datos']), 'la serie suma su total');
    }

    public function test_el_rango_especifico_filtra_por_fechas(): void
    {
        RegistroServicio::factory()->create([
            'servicio' => Servicio::Escuela,
            'created_at' => '2026-08-10 10:00:00',
        ]);
        RegistroServicio::factory()->create([
            'servicio' => Servicio::Transporte,
            'created_at' => '2026-08-25 16:30:00',
        ]);

        $this->actingAs(User::factory()->master()->create())
            ->getJson('/administracion/reportes/datos?periodo=rango&desde=2026-08-01&hasta=2026-08-15')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('descripcion', 'Del 1 de Agosto del 2026 al 15 de Agosto del 2026');
    }

    public function test_un_rango_al_reves_o_incompleto_se_rechaza(): void
    {
        $master = User::factory()->master()->create();

        $this->actingAs($master)
            ->getJson('/administracion/reportes/datos?periodo=rango&desde=2026-08-15&hasta=2026-08-01')
            ->assertUnprocessable();

        $this->actingAs($master)
            ->getJson('/administracion/reportes/datos?periodo=rango')
            ->assertUnprocessable();
    }

    public function test_el_excel_trae_usuario_servicio_fecha_y_hora_y_queda_anotado(): void
    {
        $nino = Nino::factory()->create(['nombre' => 'Ronald', 'apellido_paterno' => 'Mc', 'apellido_materno' => 'Donald']);
        RegistroServicio::factory()->recycle($nino)->create([
            'servicio' => Servicio::Comedor,
            'created_at' => '2026-09-05 13:45:00',
        ]);

        $master = User::factory()->master()->create();
        $respuesta = $this->actingAs($master)
            ->get('/administracion/reportes/excel?periodo=rango&desde=2026-09-01&hasta=2026-09-07')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->assertStringContainsString(
            'bitacora-servicios-2026-09-01-a-2026-09-07.xlsx',
            $respuesta->headers->get('Content-Disposition'),
        );

        // El .xlsx es un ZIP: la hoja debe traer el nombre, el servicio y la fecha/hora.
        $ruta = tempnam(sys_get_temp_dir(), 'xlsx');
        file_put_contents($ruta, $respuesta->getContent());
        $zip = new ZipArchive;
        $this->assertTrue($zip->open($ruta));
        $hoja = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        unlink($ruta);

        $this->assertStringContainsString('Ronald Mc Donald', $hoja);
        $this->assertStringContainsString('Comedor', $hoja);
        $this->assertStringContainsString('05/09/2026', $hoja);
        $this->assertStringContainsString('1:45 PM', $hoja);

        // La descarga queda en la bitácora de actividad.
        $this->assertDatabaseHas('registro_actividad', [
            'user_id' => $master->id,
            'accion' => Accion::Descarga->value,
        ]);
    }

    public function test_el_periodo_todo_funciona_sin_registros(): void
    {
        $this->actingAs(User::factory()->master()->create())
            ->getJson('/administracion/reportes/datos?periodo=todo')
            ->assertOk()
            ->assertJsonPath('total', 0);
    }
}
