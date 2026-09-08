<?php

namespace Database\Factories;

use App\Models\Pais;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pais>
 */
class PaisFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pais' => fake()->unique()->country(),
        ];
    }
}
