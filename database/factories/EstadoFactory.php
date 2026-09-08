<?php

namespace Database\Factories;

use App\Models\Estado;
use App\Models\Pais;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Estado>
 */
class EstadoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pais_id' => Pais::factory(),
            'estado' => fake()->unique()->city(),
        ];
    }
}
