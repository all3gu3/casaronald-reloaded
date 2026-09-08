<?php

namespace Database\Factories;

use App\Models\Acompanante;
use App\Models\EdoSalud;
use App\Models\Escolaridad;
use App\Models\Nino;
use App\Models\Ocupacion;
use App\Models\Parentesco;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Acompanante>
 */
class AcompananteFactory extends Factory
{
    public function definition(): array
    {
        $trabaja = fake()->boolean(70);

        return [
            'nino_id' => Nino::factory(),
            'nombre' => fake()->firstName(),
            'apellido_paterno' => fake()->lastName(),
            'apellido_materno' => fake()->lastName(),
            'edad' => (string) fake()->numberBetween(18, 65),
            'sexo' => fake()->randomElement(['Masculino', 'Femenino']),
            'identificacion' => fake()->optional()->bothify('INE ####??'),
            'tratamiento' => null,
            'fecha_registro' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'observaciones' => fake()->optional()->sentence(),
            'foto' => null,
            'parentesco_id' => Parentesco::factory(),
            'escolaridad_id' => Escolaridad::factory(),
            'edo_salud_id' => EdoSalud::factory(),
            'ocupacion_id' => Ocupacion::factory(),
            'trabaja' => $trabaja,
            'licencia_goce_sueldo' => $trabaja ? fake()->boolean(30) : null,
            'seguro_medico' => fake()->boolean(40),
            'casa_propia' => fake()->boolean(50),
            'asistencia_financiera' => fake()->boolean(30),
            'renta_mensualidad' => fake()->optional(0.5)->numberBetween(500, 5000),
            'dependientes_economicos' => fake()->numberBetween(0, 6),
            'ingreso_mensual' => fake()->numberBetween(0, 15000),
        ];
    }
}
