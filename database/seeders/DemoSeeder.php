<?php

namespace Database\Seeders;

use App\Enums\Accion;
use App\Enums\Servicio;
use App\Models\Acompanante;
use App\Models\ClasificacionSocial;
use App\Models\EdoSalud;
use App\Models\EntradaSalida;
use App\Models\Escolaridad;
use App\Models\Estado;
use App\Models\Hospital;
use App\Models\Nino;
use App\Models\Ocupacion;
use App\Models\Parentesco;
use App\Models\RegistroActividad;
use App\Models\RegistroServicio;
use App\Models\SalarioMinimo;
use App\Models\TipoDieta;
use App\Models\TipoTratamiento;
use App\Models\TrabajadorSocial;
use App\Models\User;
use App\Models\Zona;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Datos ficticios de demostración pensados para enseñar el sistema completo:
 * expedientes variados (edades, procedencias, hospitales y dietas coherentes
 * con los catálogos), acompañantes con parentescos distintos, semanas de
 * historial en los cuatro servicios, entradas/salidas con niños actualmente
 * dentro de la Casa, cuentas de ejemplo para cada rol y bitácora de actividad.
 *
 * Los nombres son inventados con sabor mexicano/latino (algunos guiñan a las
 * mascotas de McDonald's tropicalizadas: Ronaldo, Grimaldo "Grimas", Hamburto,
 * Birdita...). No se ejecuta en pruebas y asume base recién migrada
 * (los QR fijos son únicos).
 */
class DemoSeeder extends Seeder
{
    /** Contraseña compartida de las cuentas de demostración (solo entorno local). */
    private const PASSWORD_DEMO = 'casita-demo';

    private const MESES_LARGOS = [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    public function run(): void
    {
        $expedientes = $this->sembrarExpedientes();
        $usuarios = $this->sembrarUsuarios();
        $this->sembrarHistorialServicios($expedientes);
        $this->sembrarEntradasSalidas($expedientes);
        $this->sembrarActividad($usuarios, $expedientes);

        $this->command?->info(
            'Cuentas demo (contraseña "'.self::PASSWORD_DEMO.'"): direccion@, recepcion@, '
            .'voluntariado@ y tsocial@casaronald.local'
        );
    }

    /**
     * Crea los expedientes con sus acompañantes y tratamientos.
     *
     * @return list<array{nino: Nino, dentro: bool}>
     */
    private function sembrarExpedientes(): array
    {
        $expedientes = [];
        foreach ($this->expedientesDemo() as $config) {
            // Catálogos con firstOrFail: si un valor no existe, la demo debe
            // tronar en la siembra y no a media presentación.
            $estado = Estado::where('estado', $config['estado'])->firstOrFail();
            $hospital = Hospital::where('hospital', $config['hospital'])->firstOrFail();
            $dieta = TipoDieta::where('tipo_dieta', $config['dieta'])->firstOrFail();

            $ingreso = now()->subDays($config['ingreso_hace']);
            $salida = $config['salida_hace'] !== null ? now()->subDays($config['salida_hace']) : null;
            $edad = Carbon::parse($config['nacimiento'])->age;

            // Todas las claves foráneas se fijan aquí para que la factory no
            // fabrique catálogos nuevos; el resto del expediente (calle, CP,
            // identificación...) sí sale de la factory con locale es_MX.
            $nino = Nino::factory()->create([
                'qr' => $config['qr'],
                'nombre' => $config['nombre'],
                'apellido_paterno' => $config['apellido_paterno'],
                'apellido_materno' => $config['apellido_materno'],
                'sexo' => $config['sexo'],
                'fecha_nacimiento' => $config['nacimiento'],
                'municipio' => $config['municipio'],
                'localidad' => $config['municipio'],
                'colonia' => fake()->randomElement(['Centro', 'La Loma', 'El Carmen', 'San Miguel', 'Guadalupe', 'Los Sauces']),
                'estado_id' => $estado->id,
                'pais_id' => $estado->pais_id,
                'hospital_id' => $hospital->id,
                'primer_telefono' => $config['telefono'],
                'segundo_telefono' => $config['telefono2'],
                'dialecto' => $config['dialecto'],
                'diagnostico' => $config['diagnostico'],
                'medico' => $config['medico'],
                'alerg_alimentos' => $config['alergias'][0],
                'alerg_medicamentos' => $config['alergias'][1],
                'estatus_estancia' => $config['estatus'],
                'observaciones' => $config['observaciones'],
                'fecha_solicitud' => $ingreso->copy()->subDays(fake()->numberBetween(4, 10))->toDateString(),
                'fecha_ingreso' => $ingreso->toDateString(),
                'fecha_salida' => $salida?->toDateString(),
                'trabajador_social_id' => TrabajadorSocial::query()->inRandomOrder()->value('id'),
                'tipo_dieta_id' => $dieta->id,
                'escolaridad_id' => $this->escolaridadPara($edad),
                'clasificacion_social_id' => ClasificacionSocial::query()->inRandomOrder()->value('id'),
                'zona_id' => Zona::query()->inRandomOrder()->value('id'),
                'salario_minimo_id' => SalarioMinimo::query()->inRandomOrder()->value('id'),
                // El alta del expediente coincide con el ingreso para que la
                // bitácora de actividad cuente una historia coherente.
                'created_at' => $ingreso->copy()->setTime(fake()->numberBetween(9, 12), fake()->numberBetween(0, 59)),
                'updated_at' => $ingreso->copy()->setTime(13, 0),
            ]);

            foreach ($config['acompanantes'] as [$nombre, $paterno, $materno, $parentesco, $sexo, $edadAcomp]) {
                Acompanante::factory()->create([
                    'nino_id' => $nino->id,
                    'nombre' => $nombre,
                    'apellido_paterno' => $paterno,
                    'apellido_materno' => $materno,
                    'sexo' => $sexo,
                    'edad' => (string) $edadAcomp,
                    'fecha_registro' => $ingreso->toDateString(),
                    'observaciones' => null,
                    'parentesco_id' => Parentesco::where('parentesco', $parentesco)->firstOrFail()->id,
                    'escolaridad_id' => Escolaridad::query()->inRandomOrder()->value('id'),
                    'edo_salud_id' => EdoSalud::query()->inRandomOrder()->value('id'),
                    'ocupacion_id' => Ocupacion::query()->inRandomOrder()->value('id'),
                ]);
            }

            // Tratamientos curados por diagnóstico (no al azar) para que el
            // expediente impreso se lea creíble.
            $nino->tiposTratamiento()->sync(
                TipoTratamiento::whereIn('tipo_tratamiento', $config['tratamientos'])->pluck('id'),
            );

            $expedientes[] = ['nino' => $nino, 'dentro' => $config['dentro']];
        }

        return $expedientes;
    }

    /**
     * Cuentas de ejemplo para cada rol del enum Role. La cuenta maestra real de
     * MasterUserSeeder no se toca: aquí solo se agregan perfiles de demostración,
     * incluida una cuenta desactivada para enseñar ese flujo.
     *
     * @return array<string, User>
     */
    private function sembrarUsuarios(): array
    {
        $password = Hash::make(self::PASSWORD_DEMO);

        $usuarios = [
            'direccion' => User::factory()->master()->create([
                'name' => 'Grimalda Orozco Macías',
                'email' => 'direccion@casaronald.local',
                'password' => $password,
            ]),
            'recepcion' => User::factory()->staff()->create([
                'name' => 'Refugio Cid Zepeda',
                'email' => 'recepcion@casaronald.local',
                'password' => $password,
            ]),
            'voluntariado' => User::factory()->staff()->create([
                'name' => 'Marisol Cuautle Barrios',
                'email' => 'voluntariado@casaronald.local',
                'password' => $password,
            ]),
            'tsocial' => User::factory()->trabajadorSocial()->create([
                'name' => 'Filomena Xicale Tepox',
                'email' => 'tsocial@casaronald.local',
                'password' => $password,
            ]),
        ];

        User::factory()->staff()->inactivo()->create([
            'name' => 'Anselmo Buendía Coyotl',
            'email' => 'exvoluntario@casaronald.local',
            'password' => $password,
        ]);

        return $usuarios;
    }

    /**
     * Historial de los cuatro servicios repartido en toda la estancia de cada
     * niño (últimas ~6 semanas), con horarios verosímiles: comedor en los tres
     * tiempos, lavandería de día, escuelita entre semana y transporte al
     * hospital. Así los concentrados y las gráficas de reportes tienen relieve.
     *
     * @param  list<array{nino: Nino, dentro: bool}>  $expedientes
     */
    private function sembrarHistorialServicios(array $expedientes): void
    {
        $filas = [];
        foreach ($expedientes as $expediente) {
            $nino = $expediente['nino'];
            $fin = $nino->fecha_salida?->copy()->endOfDay() ?? now();

            for ($dia = $nino->fecha_ingreso->copy()->startOfDay(); $dia->lte($fin); $dia->addDay()) {
                // Algunos días la familia pasa la jornada completa en el hospital.
                if (fake()->boolean(15)) {
                    continue;
                }

                $eventos = [];
                if (fake()->boolean(90)) {
                    $eventos[] = [Servicio::Comedor, 7, 8]; // desayuno
                }
                if (fake()->boolean(80)) {
                    $eventos[] = [Servicio::Comedor, 13, 14]; // comida
                }
                if (fake()->boolean(70)) {
                    $eventos[] = [Servicio::Comedor, 19, 20]; // cena
                }
                if (fake()->boolean(30)) {
                    $eventos[] = [Servicio::Lavanderia, 9, 17];
                }
                if ($dia->isWeekday() && $nino->edad >= 4 && fake()->boolean(55)) {
                    $eventos[] = [Servicio::Escuela, 9, 12];
                }
                if (fake()->boolean(40)) {
                    $eventos[] = fake()->boolean()
                        ? [Servicio::Transporte, 7, 8]   // ida al hospital
                        : [Servicio::Transporte, 16, 17]; // regreso
                }

                foreach ($eventos as [$servicio, $horaMin, $horaMax]) {
                    $cuando = $dia->copy()->setTime(fake()->numberBetween($horaMin, $horaMax), fake()->numberBetween(0, 59));
                    if ($cuando->greaterThan(now())) {
                        continue; // el día de hoy solo hasta la hora actual
                    }
                    $filas[] = [
                        'nino_id' => $nino->id,
                        'qr' => $nino->qr,
                        'servicio' => $servicio->value,
                        'created_at' => $cuando,
                        'updated_at' => $cuando,
                    ];
                }
            }
        }

        // Insert por lotes: son cientos de registros y crear modelos uno a uno
        // vuelve lenta la siembra sin aportar nada a la demo.
        foreach (array_chunk($filas, 500) as $lote) {
            RegistroServicio::insert($lote);
        }
    }

    /**
     * Entradas y salidas de la Casa: por las noches duermen aquí (entrada por
     * la tarde, salida a la mañana siguiente rumbo al hospital). Los niños con
     * `dentro = true` quedan con una entrada abierta — se ven "en Casa" ahora
     * mismo — y los egresados cierran su última salida el día de su egreso.
     *
     * @param  list<array{nino: Nino, dentro: bool}>  $expedientes
     */
    private function sembrarEntradasSalidas(array $expedientes): void
    {
        $filas = [];
        foreach ($expedientes as $expediente) {
            $nino = $expediente['nino'];
            $egreso = $nino->fecha_salida?->copy();
            $finPares = ($egreso ?? now())->copy()->subDay()->startOfDay();

            $cursor = $nino->fecha_ingreso->copy()->startOfDay();
            while ($cursor->lt($finPares)) {
                $entrada = $cursor->copy()->setTime(fake()->numberBetween(16, 19), fake()->numberBetween(0, 59));
                $salida = $cursor->copy()->addDay()->setTime(fake()->numberBetween(7, 9), fake()->numberBetween(0, 59));
                $filas[] = $this->filaEntradaSalida($nino, $entrada, $salida);
                // No duermen aquí todas las noches: a veces se quedan en el hospital.
                $cursor->addDays(fake()->numberBetween(1, 3));
            }

            if ($egreso !== null) {
                // Última noche y salida definitiva la mañana del egreso.
                $filas[] = $this->filaEntradaSalida(
                    $nino,
                    $egreso->copy()->subDay()->setTime(18, fake()->numberBetween(0, 59)),
                    $egreso->copy()->setTime(fake()->numberBetween(10, 11), fake()->numberBetween(0, 59)),
                );
            } elseif ($expediente['dentro']) {
                // Entrada abierta: el niño está dentro de la Casa en este momento.
                $filas[] = $this->filaEntradaSalida(
                    $nino,
                    now()->subMinutes(fake()->numberBetween(60, 300)),
                    null,
                );
            } else {
                // Está fuera ahora: durmió aquí y salió hoy por la mañana.
                $salidaHoy = now()->subMinutes(fake()->numberBetween(30, 180));
                $filas[] = $this->filaEntradaSalida(
                    $nino,
                    $salidaHoy->copy()->subHours(14),
                    $salidaHoy,
                );
            }
        }

        EntradaSalida::insert($filas);
    }

    /** @return array<string, mixed> */
    private function filaEntradaSalida(Nino $nino, Carbon $entrada, ?Carbon $salida): array
    {
        return [
            'nino_id' => $nino->id,
            'qr' => $nino->qr,
            'entrada' => $entrada,
            'salida' => $salida,
            'created_at' => $entrada,
            'updated_at' => $salida ?? $entrada,
        ];
    }

    /**
     * Bitácora de actividad de las cuentas demo, con los mismos textos de
     * `detalle` que escribe la aplicación (ver EscanearController, NinoController,
     * FichaController y ReporteController) para que la tabla se vea real.
     *
     * @param  array<string, User>  $usuarios
     * @param  list<array{nino: Nino, dentro: bool}>  $expedientes
     */
    private function sembrarActividad(array $usuarios, array $expedientes): void
    {
        $filas = [];
        $anota = function (User $usuario, Accion $accion, ?string $detalle, Carbon $cuando) use (&$filas): void {
            if ($cuando->greaterThan(now())) {
                return; // nada de actividad en el futuro
            }
            $filas[] = [
                'user_id' => $usuario->id,
                'accion' => $accion->value,
                'detalle' => $detalle,
                'created_at' => $cuando,
            ];
        };

        // Altas: la trabajadora social captura expediente y acompañantes el día
        // del ingreso; en varios casos imprime el carnet QR ahí mismo.
        foreach ($expedientes as $expediente) {
            $nino = $expediente['nino'];
            $alta = $nino->created_at->copy();
            $anota($usuarios['tsocial'], Accion::Alta,
                'Expediente de '.$nino->nombreCompleto().' ('.$nino->qr.')', $alta);
            foreach ($nino->acompanantes as $i => $acompanante) {
                $anota($usuarios['tsocial'], Accion::Alta,
                    'Acompañante '.$acompanante->nombreCompleto().' en el expediente '.$nino->qr,
                    $alta->copy()->addMinutes(5 + $i * 4));
            }
            if (fake()->boolean(60)) {
                $anota($usuarios['tsocial'], Accion::Descarga,
                    'Carnet QR de '.$nino->nombreCompleto().' ('.$nino->qr.')',
                    $alta->copy()->addMinutes(20));
            }
        }

        // Inicios de sesión de los últimos 14 días, cada quien a su hora.
        foreach (range(0, 13) as $hace) {
            $dia = now()->subDays($hace);
            $anota($usuarios['recepcion'], Accion::InicioSesion, null,
                $dia->copy()->setTime(7, fake()->numberBetween(40, 59)));
            if (fake()->boolean(60)) {
                $anota($usuarios['voluntariado'], Accion::InicioSesion, null,
                    $dia->copy()->setTime(12, fake()->numberBetween(0, 45)));
            }
            if ($dia->isWeekday()) {
                $anota($usuarios['tsocial'], Accion::InicioSesion, null,
                    $dia->copy()->setTime(9, fake()->numberBetween(0, 30)));
                $anota($usuarios['direccion'], Accion::InicioSesion, null,
                    $dia->copy()->setTime(10, fake()->numberBetween(0, 59)));
            }
        }

        // Escaneos: una muestra de los registros de servicio recientes, con el
        // mismo formato que anota la estación de escaneo. Por la mañana escanea
        // recepción y por la tarde el voluntariado.
        $recientes = RegistroServicio::with('nino')
            ->where('created_at', '>=', now()->subDays(7))
            ->inRandomOrder()
            ->limit(45)
            ->get();
        foreach ($recientes as $registro) {
            $quien = $registro->created_at->hour < 13 ? $usuarios['recepcion'] : $usuarios['voluntariado'];
            $anota($quien, Accion::Escaneo,
                $registro->servicio->etiqueta().' registrado: '.$registro->nino->nombreCompleto().' ('.$registro->qr.')',
                $registro->created_at->copy());
        }
        $pases = EntradaSalida::with('nino')
            ->where('entrada', '>=', now()->subDays(5))
            ->get();
        foreach ($pases as $pase) {
            $anota($usuarios['recepcion'], Accion::Escaneo,
                'Entrada registrada: '.$pase->nino->nombreCompleto().' ('.$pase->qr.')',
                $pase->entrada->copy());
            if ($pase->salida !== null) {
                $anota($usuarios['recepcion'], Accion::Escaneo,
                    'Salida registrada: '.$pase->nino->nombreCompleto().' ('.$pase->qr.')',
                    $pase->salida->copy());
            }
        }

        // La dirección baja reportes: la bitácora del mes pasado en Excel (con
        // el conteo real sembrado) y un par de reportes PDF de expedientes.
        $mesPasado = now()->subMonthNoOverflow();
        $enElMes = RegistroServicio::whereBetween('created_at', [
            $mesPasado->copy()->startOfMonth(), $mesPasado->copy()->endOfMonth(),
        ])->count();
        $anota($usuarios['direccion'], Accion::Descarga,
            'Bitácora de servicios en Excel — '.self::MESES_LARGOS[$mesPasado->month].' del '.$mesPasado->year
            .' ('.$enElMes.' registros)',
            now()->subDays(3)->setTime(10, 25));
        foreach (fake()->randomElements($expedientes, 2) as $expediente) {
            $nino = $expediente['nino'];
            $anota($usuarios['direccion'], Accion::Descarga,
                'Reporte PDF de '.$nino->nombreCompleto().' ('.$nino->qr.')',
                now()->subDays(fake()->numberBetween(1, 6))->setTime(11, fake()->numberBetween(0, 59)));
        }

        // Ediciones: la trabajadora social corrige algunos expedientes días después.
        foreach (fake()->randomElements($expedientes, 3) as $expediente) {
            $nino = $expediente['nino'];
            $anota($usuarios['tsocial'], Accion::Edicion,
                'Expediente de '.$nino->nombreCompleto().' ('.$nino->qr.')',
                $nino->created_at->copy()->addDays(fake()->numberBetween(2, 6))->setTime(fake()->numberBetween(9, 17), fake()->numberBetween(0, 59)));
        }

        RegistroActividad::insert($filas);
    }

    /** Escolaridad acorde a la edad, para que el expediente no diga que un bebé cursa secundaria. */
    private function escolaridadPara(int $edad): int
    {
        $nombre = match (true) {
            $edad < 2 => 'Educación inicial',
            $edad < 4 => 'Maternal',
            $edad < 6 => 'Kinder',
            $edad < 12 => 'Primaria',
            $edad < 15 => 'Secundaria',
            default => 'Preparatoria/Bachiller',
        };

        return Escolaridad::where('escolaridad', $nombre)->firstOrFail()->id;
    }

    /**
     * Los expedientes de demostración. Estados y municipios salen del catálogo
     * de GeografiaSeeder (Puebla y Guerrero); para Tlaxcala y Veracruz — sin
     * catálogo de municipios sembrado — se usan municipios reales como texto
     * libre, igual que captura la Casa. Ladas telefónicas acordes a la región.
     *
     * `ingreso_hace`/`salida_hace` son días atrás (salida null = sigue en casa);
     * `dentro` indica si el niño está físicamente en la Casa en este momento.
     * Acompañantes: [nombre, paterno, materno, parentesco, sexo, edad].
     *
     * @return list<array<string, mixed>>
     */
    private function expedientesDemo(): array
    {
        return [
            [
                'qr' => 'R2M4Z6', 'nombre' => 'Ronaldo', 'apellido_paterno' => 'Macías', 'apellido_materno' => 'Zepeda',
                'sexo' => 'Masculino', 'nacimiento' => '2017-04-12',
                'estado' => 'Puebla', 'municipio' => 'Acatzingo', 'hospital' => 'HNP',
                'telefono' => '2495521834', 'telefono2' => '2495578102', 'dialecto' => null,
                'diagnostico' => 'Leucemia linfoblástica aguda', 'medico' => 'Dra. Leticia Cañedo',
                'dieta' => 'Normal', 'alergias' => ['Cacahuate', null],
                'tratamientos' => ['Tratamiento', 'Estudios de Laboratorio'],
                'estatus' => 'Subsecuente', 'observaciones' => null,
                'ingreso_hace' => 38, 'salida_hace' => null, 'dentro' => true,
                'acompanantes' => [
                    ['María Guadalupe', 'Zepeda', 'Alcaraz', 'Madre', 'Femenino', 34],
                    ['Petra', 'Alcaraz', 'Cuautle', 'Abuela', 'Femenino', 58],
                ],
            ],
            [
                'qr' => 'G5H1T9', 'nombre' => 'Grimaldo', 'apellido_paterno' => 'Huerta', 'apellido_materno' => 'Tlatelpa',
                'sexo' => 'Masculino', 'nacimiento' => '2019-11-03',
                'estado' => 'Puebla', 'municipio' => 'Tehuacán', 'hospital' => 'CRIT',
                'telefono' => '2381479205', 'telefono2' => null, 'dialecto' => null,
                'diagnostico' => 'Parálisis cerebral en rehabilitación', 'medico' => 'Dr. Aurelio Sandoval',
                'dieta' => 'Blanda', 'alergias' => [null, null],
                'tratamientos' => ['Rehabilitación', 'Terapia'],
                'estatus' => 'Prórroga', 'observaciones' => 'En la Casa todos le dicen "Grimas".',
                'ingreso_hace' => 31, 'salida_hace' => null, 'dentro' => true,
                'acompanantes' => [
                    ['Rosalba', 'Tlatelpa', 'Xicale', 'Madre', 'Femenino', 38],
                ],
            ],
            [
                'qr' => 'H7P3C1', 'nombre' => 'Hamburto', 'apellido_paterno' => 'Peralta', 'apellido_materno' => 'Cortés',
                'sexo' => 'Masculino', 'nacimiento' => '2014-06-27',
                'estado' => 'Puebla', 'municipio' => 'Izúcar de Matamoros', 'hospital' => 'TyO',
                'telefono' => '2436108874', 'telefono2' => null, 'dialecto' => null,
                'diagnostico' => 'Fractura de fémur en rehabilitación', 'medico' => 'Dr. Heriberto Osuna',
                'dieta' => 'Normal', 'alergias' => [null, null],
                'tratamientos' => ['Rehabilitación', 'Consulta Externa'],
                'estatus' => 'Primera vez', 'observaciones' => null,
                'ingreso_hace' => 25, 'salida_hace' => null, 'dentro' => false,
                'acompanantes' => [
                    ['Melquiades', 'Peralta', 'Quiroz', 'Padre', 'Masculino', 41],
                    ['Baldomero', 'Cortés', 'Naranjo', 'Tio', 'Masculino', 35],
                ],
            ],
            [
                'qr' => 'B3C9E5', 'nombre' => 'Birdita', 'apellido_paterno' => 'Cuautle', 'apellido_materno' => 'Espinosa',
                'sexo' => 'Femenino', 'nacimiento' => '2021-02-14',
                'estado' => 'Puebla', 'municipio' => 'Cuetzalan del Progreso', 'hospital' => 'HNP',
                'telefono' => '2337752961', 'telefono2' => null, 'dialecto' => 'Náhuatl',
                'diagnostico' => 'Cardiopatía congénita', 'medico' => 'Dra. Refugio Palafox',
                'dieta' => 'Especial', 'alergias' => [null, null],
                'tratamientos' => ['Cirugía', 'Post-Hospitalización'],
                'estatus' => 'Subsecuente', 'observaciones' => null,
                'ingreso_hace' => 40, 'salida_hace' => null, 'dentro' => true,
                'acompanantes' => [
                    ['Xóchitl', 'Espinosa', 'Coyotl', 'Madre', 'Femenino', 27],
                ],
            ],
            [
                'qr' => 'A1M8R4', 'nombre' => 'Alondra', 'apellido_paterno' => 'Macuil', 'apellido_materno' => 'Rojas',
                'sexo' => 'Femenino', 'nacimiento' => '2012-09-18',
                'estado' => 'Puebla', 'municipio' => 'Teziutlán', 'hospital' => 'HUP',
                'telefono' => '2314903327', 'telefono2' => '2314900218', 'dialecto' => null,
                'diagnostico' => 'Insuficiencia renal crónica', 'medico' => 'Dr. Genaro Mastache',
                'dieta' => 'Renal', 'alergias' => ['Fresa', null],
                'tratamientos' => ['Tratamiento', 'Estudios de Laboratorio', 'Consulta Externa'],
                'estatus' => 'Prórroga', 'observaciones' => null,
                'ingreso_hace' => 42, 'salida_hace' => null, 'dentro' => true,
                'acompanantes' => [
                    ['Casimira', 'Rojas', 'Landeta', 'Madre', 'Femenino', 45],
                    ['Yesenia', 'Macuil', 'Rojas', 'Hermana', 'Femenino', 21],
                ],
            ],
            [
                'qr' => 'E6X2C8', 'nombre' => 'Emiliano', 'apellido_paterno' => 'Xicale', 'apellido_materno' => 'Coyotl',
                'sexo' => 'Masculino', 'nacimiento' => '2018-01-30',
                'estado' => 'Puebla', 'municipio' => 'Cholula de Rivadavia', 'hospital' => 'HGN',
                'telefono' => '2225873049', 'telefono2' => null, 'dialecto' => null,
                'diagnostico' => 'Epilepsia refractaria', 'medico' => 'Dra. Imelda Bonilla',
                'dieta' => 'Normal', 'alergias' => [null, 'Penicilina'],
                'tratamientos' => ['Valoración Médica', 'Estudios de Gabinete'],
                'estatus' => 'Primera vez', 'observaciones' => null,
                'ingreso_hace' => 18, 'salida_hace' => null, 'dentro' => false,
                'acompanantes' => [
                    ['Margarito', 'Xicale', 'Tepox', 'Padre', 'Masculino', 45],
                ],
            ],
            [
                'qr' => 'Y4Z0G2', 'nombre' => 'Yaretzi', 'apellido_paterno' => 'Zepeda', 'apellido_materno' => 'Galindo',
                'sexo' => 'Femenino', 'nacimiento' => '2023-05-21',
                'estado' => 'Puebla', 'municipio' => 'Zacatlán', 'hospital' => 'IMSS Margarita',
                'telefono' => '7971056482', 'telefono2' => null, 'dialecto' => null,
                'diagnostico' => 'Labio y paladar hendido (posquirúrgico)', 'medico' => 'Dr. Cutberto Ríos',
                'dieta' => 'Papilla', 'alergias' => [null, null],
                'tratamientos' => ['Post-Hospitalización', 'Consulta Externa'],
                'estatus' => 'Primera vez', 'observaciones' => null,
                'ingreso_hace' => 12, 'salida_hace' => null, 'dentro' => true,
                'acompanantes' => [
                    ['Imelda', 'Galindo', 'Osorio', 'Madre', 'Femenino', 24],
                    ['Fidencio', 'Zepeda', 'Barragán', 'Padre', 'Masculino', 28],
                ],
            ],
            [
                'qr' => 'T8G6O0', 'nombre' => 'Tadeo', 'apellido_paterno' => 'Grimaldi', 'apellido_materno' => 'Osorio',
                'sexo' => 'Masculino', 'nacimiento' => '2016-08-09',
                'estado' => 'Guerrero', 'municipio' => 'Tlapa de Comonfort', 'hospital' => 'HNP',
                'telefono' => '7573860714', 'telefono2' => null, 'dialecto' => 'Mixteco',
                'diagnostico' => 'Tumor de Wilms', 'medico' => 'Dra. Eloísa Camacho',
                'dieta' => 'Normal', 'alergias' => [null, null],
                'tratamientos' => ['Tratamiento', 'Cirugía'],
                'estatus' => 'Subsecuente', 'observaciones' => null,
                'ingreso_hace' => 22, 'salida_hace' => null, 'dentro' => false,
                'acompanantes' => [
                    ['Hortensia', 'Osorio', 'Chontal', 'Tia', 'Femenino', 39],
                ],
            ],
            [
                'qr' => 'R9B5Q1', 'nombre' => 'Renata', 'apellido_paterno' => 'Barragán', 'apellido_materno' => 'Quiroz',
                'sexo' => 'Femenino', 'nacimiento' => '2011-12-02',
                'estado' => 'Guerrero', 'municipio' => 'Chilpancingo de los Bravo', 'hospital' => 'HGCH',
                'telefono' => '7472319608', 'telefono2' => null, 'dialecto' => null,
                'diagnostico' => 'Quemaduras de segundo grado (injertos)', 'medico' => 'Dr. Froylán Ledesma',
                'dieta' => 'Especial', 'alergias' => [null, 'Sulfas'],
                'tratamientos' => ['Cirugía', 'Terapia'],
                'estatus' => 'Primera vez', 'observaciones' => null,
                'ingreso_hace' => 9, 'salida_hace' => null, 'dentro' => false,
                'acompanantes' => [
                    ['Casilda', 'Quiroz', 'Mendiola', 'Madre', 'Femenino', 44],
                ],
            ],
            [
                'qr' => 'R0C7M3', 'nombre' => 'Ronaldina', 'apellido_paterno' => 'Cortés', 'apellido_materno' => 'Macías',
                'sexo' => 'Femenino', 'nacimiento' => '2020-07-25',
                'estado' => 'Tlaxcala', 'municipio' => 'Apizaco', 'hospital' => 'HNP',
                'telefono' => '2417804553', 'telefono2' => null, 'dialecto' => null,
                'diagnostico' => 'Leucemia mieloide aguda', 'medico' => 'Dra. Nayeli Cordero',
                'dieta' => 'Normal', 'alergias' => [null, null],
                'tratamientos' => ['Tratamiento'],
                'estatus' => 'Primera vez', 'observaciones' => null,
                'ingreso_hace' => 6, 'salida_hace' => null, 'dentro' => false,
                'acompanantes' => [
                    ['Ronaldo', 'Cortés', 'Peralta', 'Padre', 'Masculino', 33],
                    ['Juana', 'Macías', 'Solís', 'Madre', 'Femenino', 31],
                ],
            ],
            [
                'qr' => 'B2T4M6', 'nombre' => 'Bruno', 'apellido_paterno' => 'Tepox', 'apellido_materno' => 'Meza',
                'sexo' => 'Masculino', 'nacimiento' => '2015-03-16',
                'estado' => 'Veracruz de Ignacio de la Llave', 'municipio' => 'Orizaba', 'hospital' => 'UQxP',
                'telefono' => '2725147790', 'telefono2' => null, 'dialecto' => null,
                'diagnostico' => 'Osteosarcoma', 'medico' => 'Dr. Silvano Ocampo',
                'dieta' => 'Normal', 'alergias' => [null, null],
                'tratamientos' => ['Tratamiento', 'Cirugía'],
                'estatus' => 'Subsecuente', 'observaciones' => null,
                'ingreso_hace' => 35, 'salida_hace' => 5, 'dentro' => false,
                'acompanantes' => [
                    ['Herlinda', 'Meza', 'Landero', 'Madre', 'Femenino', 37],
                ],
            ],
            [
                'qr' => 'C5H9P7', 'nombre' => 'Camila', 'apellido_paterno' => 'Huerta', 'apellido_materno' => 'Peralta',
                'sexo' => 'Femenino', 'nacimiento' => '2022-10-08',
                'estado' => 'Puebla', 'municipio' => 'Atlixco', 'hospital' => 'SEDIF',
                'telefono' => '2449062318', 'telefono2' => null, 'dialecto' => null,
                'diagnostico' => 'Desnutrición severa en recuperación', 'medico' => 'Dra. Herminia Palacios',
                'dieta' => 'Blanda', 'alergias' => [null, null],
                'tratamientos' => ['Tratamiento', 'Consulta Externa'],
                'estatus' => 'Primera vez', 'observaciones' => 'Bajo tutela de la abuela.',
                'ingreso_hace' => 33, 'salida_hace' => 12, 'dentro' => false,
                'acompanantes' => [
                    ['Eufrosina', 'Peralta', 'Vega', 'Tutor', 'Femenino', 61],
                ],
            ],
            [
                'qr' => 'A7C3Z9', 'nombre' => 'Ángel', 'apellido_paterno' => 'Coyotl', 'apellido_materno' => 'Zepeda',
                'sexo' => 'Masculino', 'nacimiento' => '2013-02-11',
                'estado' => 'Puebla', 'municipio' => 'Huauchinango', 'hospital' => 'HB',
                'telefono' => '7765231884', 'telefono2' => null, 'dialecto' => 'Totonaco',
                'diagnostico' => 'Cirugía de escoliosis', 'medico' => 'Dr. Baldomero Cifuentes',
                'dieta' => 'Normal', 'alergias' => [null, null],
                'tratamientos' => ['Cirugía', 'Rehabilitación'],
                'estatus' => 'Primera vez', 'observaciones' => null,
                'ingreso_hace' => 41, 'salida_hace' => 20, 'dentro' => false,
                'acompanantes' => [
                    ['Cirilo', 'Coyotl', 'Cuamatzi', 'Padre', 'Masculino', 48],
                    ['Griselda', 'Zepeda', 'Ahuactzin', 'Madre', 'Femenino', 46],
                ],
            ],
            [
                'qr' => 'I1O5C3', 'nombre' => 'Itzel', 'apellido_paterno' => 'Osorio', 'apellido_materno' => 'Cuautle',
                'sexo' => 'Femenino', 'nacimiento' => '2025-01-19',
                'estado' => 'Puebla', 'municipio' => 'San Martín Texmelucan', 'hospital' => 'IMSS Margarita',
                'telefono' => '2483706125', 'telefono2' => null, 'dialecto' => null,
                'diagnostico' => 'Cardiopatía congénita (posquirúrgica)', 'medico' => 'Dra. Otilia Manzano',
                'dieta' => 'Lactante', 'alergias' => [null, null],
                'tratamientos' => ['Post-Hospitalización', 'Terapia Intensiva'],
                'estatus' => 'Primera vez', 'observaciones' => 'Recién operada; seguimiento de enfermería.',
                'ingreso_hace' => 3, 'salida_hace' => null, 'dentro' => true,
                'acompanantes' => [
                    ['Guillermina', 'Cuautle', 'Ramos', 'Madre', 'Femenino', 22],
                ],
            ],
        ];
    }
}
