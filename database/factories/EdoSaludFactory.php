<?php

namespace Database\Factories;

use App\Models\EdoSalud;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EdoSalud>
 */
class EdoSaludFactory extends Factory
{
    public function definition(): array
    {
        return [
            'edo_salud' => fake()->unique()->randomElement(['Sano', 'Enfermo', 'En tratamiento', 'Recuperación', 'Delicado', 'Estable']),
        ];
    }
}
