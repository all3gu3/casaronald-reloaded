<?php

namespace Database\Factories;

use App\Models\Escolaridad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Escolaridad>
 */
class EscolaridadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'escolaridad' => fake()->unique()->words(2, true),
        ];
    }
}
