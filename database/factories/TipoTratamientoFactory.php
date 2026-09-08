<?php

namespace Database\Factories;

use App\Models\TipoTratamiento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TipoTratamiento>
 */
class TipoTratamientoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tipo_tratamiento' => fake()->unique()->words(2, true),
        ];
    }
}
