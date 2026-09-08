<?php

namespace Database\Factories;

use App\Models\ClasificacionSocial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClasificacionSocial>
 */
class ClasificacionSocialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clasificacion_social' => 'Clase '.fake()->unique()->numerify('##'),
        ];
    }
}
