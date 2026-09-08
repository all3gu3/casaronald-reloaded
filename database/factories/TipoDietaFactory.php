<?php

namespace Database\Factories;

use App\Models\TipoDieta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TipoDieta>
 */
class TipoDietaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tipo_dieta' => 'Dieta '.fake()->unique()->word(),
        ];
    }
}
