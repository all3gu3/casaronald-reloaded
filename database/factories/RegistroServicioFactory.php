<?php

namespace Database\Factories;

use App\Enums\Servicio;
use App\Models\Nino;
use App\Models\RegistroServicio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegistroServicio>
 */
class RegistroServicioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nino_id' => Nino::factory(),
            'qr' => fn (array $attributes) => Nino::find($attributes['nino_id'])?->qr
                ?? strtoupper(fake()->bothify('?#?#?#')),
            'servicio' => fake()->randomElement(Servicio::cases()),
        ];
    }
}
