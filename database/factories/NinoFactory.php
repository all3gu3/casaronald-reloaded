<?php

namespace Database\Factories;

use App\Models\ClasificacionSocial;
use App\Models\Escolaridad;
use App\Models\Estado;
use App\Models\Hospital;
use App\Models\Nino;
use App\Models\Pais;
use App\Models\SalarioMinimo;
use App\Models\TipoDieta;
use App\Models\TrabajadorSocial;
use App\Models\Zona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Nino>
 *
 * En pruebas conviene sembrar los catálogos una vez y usar `->recycle()` para no
 * crear una cadena de 9 catálogos por cada niño.
 */
class NinoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'qr' => strtoupper(fake()->unique()->bothify('?#?#?#')),
            'nombre' => fake()->firstName(),
            'apellido_paterno' => fake()->lastName(),
            'apellido_materno' => fake()->lastName(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-15 years', '-1 year')->format('Y-m-d'),
            'sexo' => fake()->randomElement(['Masculino', 'Femenino', 'Indefinido']),
            'foto' => null,
            'calle' => fake()->streetName(),
            'numero' => fake()->boolean(20) ? fake()->buildingNumber().'-'.fake()->randomLetter() : fake()->buildingNumber(),
            'colonia' => fake()->word(),
            'localidad' => fake()->city(),
            'municipio' => fake()->city(),
            'cp' => fake()->numerify('#####'),
            'primer_telefono' => fake()->numerify('##########'),
            'segundo_telefono' => fake()->optional()->numerify('##########'),
            'dialecto' => fake()->optional(0.2)->randomElement(['Náhuatl', 'Totonaco', 'Mazateco', 'Mixteco']),
            'diagnostico' => fake()->sentence(3),
            'medico' => 'Dr. '.fake()->name(),
            'alerg_alimentos' => fake()->optional(0.3)->word(),
            'alerg_medicamentos' => fake()->optional(0.3)->word(),
            'servicio' => null,
            'estatus_estancia' => fake()->randomElement(['Primera vez', 'Prórroga', 'Subsecuente']),
            'fecha_solicitud' => fake()->dateTimeBetween('-2 months', '-1 month')->format('Y-m-d'),
            'fecha_ingreso' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'fecha_salida' => null,
            'observaciones' => fake()->optional()->sentence(),
            'trabajador_social_id' => TrabajadorSocial::factory(),
            'tipo_dieta_id' => TipoDieta::factory(),
            'hospital_id' => Hospital::factory(),
            'escolaridad_id' => Escolaridad::factory(),
            'clasificacion_social_id' => ClasificacionSocial::factory(),
            'zona_id' => Zona::factory(),
            'salario_minimo_id' => SalarioMinimo::factory(),
            'pais_id' => Pais::factory(),
            'estado_id' => Estado::factory(),
        ];
    }

    /** Estancia ya cerrada (con fecha de salida). */
    public function egresado(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_salida' => fake()->dateTimeBetween($attributes['fecha_ingreso'] ?? '-1 month', 'now')->format('Y-m-d'),
        ]);
    }
}
