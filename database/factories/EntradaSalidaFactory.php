<?php

namespace Database\Factories;

use App\Models\EntradaSalida;
use App\Models\Nino;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EntradaSalida>
 */
class EntradaSalidaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nino_id' => Nino::factory(),
            'qr' => fn (array $attributes) => Nino::find($attributes['nino_id'])?->qr
                ?? strtoupper(fake()->bothify('?#?#?#')),
            'entrada' => fake()->dateTimeBetween('-3 days', '-1 hour'),
            'salida' => null,
        ];
    }

    /** Registro ya cerrado (con salida posterior a la entrada). */
    public function cerrada(): static
    {
        return $this->state(fn (array $attributes) => [
            'salida' => fake()->dateTimeBetween($attributes['entrada'], 'now'),
        ]);
    }
}
