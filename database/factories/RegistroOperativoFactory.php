<?php

namespace Database\Factories;

use App\Models\Hospital;
use App\Models\Nino;
use App\Models\RegistroOperativo;
use App\Models\TipoDieta;
use App\Models\TipoNinio;
use App\Models\TipoTratamiento;
use App\Models\TrabajadorSocial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegistroOperativo>
 */
class RegistroOperativoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nino_id' => Nino::factory(),
            'hospital_id' => Hospital::factory(),
            'tipo_ninio_id' => TipoNinio::factory(),
            'tipo_tratamiento_id' => TipoTratamiento::factory(),
            'tipo_dieta_id' => TipoDieta::factory(),
            'trabajador_social_id' => TrabajadorSocial::factory(),
            'fecha_ingreso' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'fecha_egreso' => null,
            'medico_atiende' => 'Dr. '.fake()->name(),
            'diagnostico' => fake()->sentence(3),
            'reingreso' => fake()->boolean(20),
            'ninos_adicionales' => fake()->numberBetween(0, 2),
            'habitacion' => fake()->numberBetween(1, 30),
            'observaciones' => fake()->optional()->sentence(),
        ];
    }

    /** Estancia cerrada. */
    public function egresado(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_egreso' => fake()->dateTimeBetween($attributes['fecha_ingreso'], 'now')->format('Y-m-d'),
        ]);
    }
}
