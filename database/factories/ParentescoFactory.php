<?php

namespace Database\Factories;

use App\Models\Parentesco;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Parentesco>
 */
class ParentescoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'parentesco' => fake()->unique()->words(2, true),
        ];
    }
}
