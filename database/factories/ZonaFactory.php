<?php

namespace Database\Factories;

use App\Models\Zona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Zona>
 */
class ZonaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'zona' => fake()->unique()->randomElement(['Rural', 'Suburbana', 'Urbana', 'Semiurbana', 'Indígena', 'Metropolitana']),
        ];
    }
}
