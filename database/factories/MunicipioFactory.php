<?php

namespace Database\Factories;

use App\Models\Estado;
use App\Models\Municipio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Municipio>
 */
class MunicipioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'estado_id' => Estado::factory(),
            'municipio' => fake()->unique()->city(),
        ];
    }
}
