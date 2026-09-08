<?php

namespace Database\Seeders;

use App\Models\ClasificacionSocial;
use App\Models\EdoSalud;
use App\Models\Escolaridad;
use App\Models\Hospital;
use App\Models\Ocupacion;
use App\Models\Parentesco;
use App\Models\SalarioMinimo;
use App\Models\TipoDieta;
use App\Models\TipoNinio;
use App\Models\TipoTratamiento;
use App\Models\TrabajadorSocial;
use App\Models\Zona;
use Illuminate\Database\Seeder;

/**
 * Catálogos de la operación (valores reales del prototipo, depurados):
 * - Se eliminaron las filas basura 'NO SPEC EN EXCEL' y 'x1'.
 * - Los 66 nombres reales de trabajadoras sociales (PII) no se siembran. Fuera de
 *   producción se generan 12 nombres ficticios con la fábrica (Faker); en
 *   producción solo la fila neutral «Por asignar», porque el expediente exige una
 *   trabajadora social y la Casa carga su directorio real al instalar
 *   (ver deploy/README.md).
 */
class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        $catalogos = [
            [ClasificacionSocial::class, 'clasificacion_social', ['1', '2', '3', '4', '5', '6']],
            [EdoSalud::class, 'edo_salud', ['Sano', 'En tratamiento', 'Enfermo']],
            [Escolaridad::class, 'escolaridad', [
                'Sin Escuela', 'Educación inicial', 'Maternal', 'Kinder', 'Primaria',
                'Secundaria', 'Preparatoria/Bachiller', 'Carrera Técnica', 'Licenciatura',
                'Maestría', 'Doctorado',
            ]],
            [Hospital::class, 'hospital', [
                'CRIT', 'HNP', 'HGCH', 'HUP', 'HGN', 'HGS', 'HdlM', 'UQxP', 'TyO',
                'HA CCR', 'HB', 'SEDIF', 'IMEX', 'IMSS Margarita', 'Fundación AMS',
                'Cruz Roja Puebla', 'Torres Med. Ang.',
            ]],
            [Ocupacion::class, 'ocupacion', [
                'Desempleado', 'Labores del Hogar', 'Empleado', 'Profesionista', 'Plomero',
                'Albañil', 'Chofer', 'Sirvienta', 'Campesino', 'Taxista',
            ]],
            [Parentesco::class, 'parentesco', [
                'Padre', 'Madre', 'Abuelo', 'Abuela', 'Tia', 'Tio', 'Hermano', 'Hermana',
                'Primo', 'Prima', 'Cuñada', 'Cuñado', 'Sin Parentesco', 'Tutor',
            ]],
            [SalarioMinimo::class, 'salario_minimo', ['< 1', '1 a 2', '> 2']],
            [TipoDieta::class, 'tipo_dieta', [
                'Normal', 'Blanda', 'Lactante', 'Papilla', 'Renal', 'Especial', 'Restringida',
            ]],
            [TipoNinio::class, 'tipo_ninio', ['En casa', 'Hospitalizado']],
            [TipoTratamiento::class, 'tipo_tratamiento', [
                'Terapia', 'Tratamiento', 'Estudios de Laboratorio', 'Valoración Médica',
                'Estudios de Gabinete', 'Rehabilitación', 'Consulta Externa', 'Cirugía',
                'Observación - Urgencias', 'Pre-Hospitalización', 'Terapia Intensiva',
                'Post-Hospitalización', 'Hospitalización', 'Otro',
            ]],
            [Zona::class, 'zona', ['Rural', 'Sub-urbana', 'Urbana']],
        ];

        foreach ($catalogos as [$modelo, $columna, $valores]) {
            foreach ($valores as $valor) {
                $modelo::firstOrCreate([$columna => $valor]);
            }
        }

        if (TrabajadorSocial::count() === 0) {
            if (app()->environment('production')) {
                TrabajadorSocial::firstOrCreate(['trabajador_social' => 'Por asignar']);
            } else {
                TrabajadorSocial::factory()->count(12)->create();
            }
        }
    }
}
