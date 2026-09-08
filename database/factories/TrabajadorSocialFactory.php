<?php

namespace Database\Factories;

use App\Models\TrabajadorSocial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrabajadorSocial>
 */
class TrabajadorSocialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'trabajador_social' => fake()->unique()->name(),
        ];
    }
}
