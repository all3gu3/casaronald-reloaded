<?php

namespace Database\Factories;

use App\Models\SalarioMinimo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalarioMinimo>
 */
class SalarioMinimoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'salario_minimo' => fake()->unique()->numerify('De # a ## salarios mínimos'),
        ];
    }
}
