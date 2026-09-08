<?php

namespace Database\Factories;

use App\Models\Ocupacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ocupacion>
 */
class OcupacionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ocupacion' => fake()->unique()->jobTitle(),
        ];
    }
}
