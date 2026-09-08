<?php

namespace Database\Factories;

use App\Models\TipoNinio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TipoNinio>
 */
class TipoNinioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tipo_ninio' => fake()->unique()->words(2, true),
        ];
    }
}
