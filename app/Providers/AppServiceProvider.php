<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fechas legibles en toda la interfaz: "3 de Agosto del 2001, 11:21 AM"
        // en lugar de números pelones. Como macros, cualquier Carbon (casts de
        // los modelos, now()) las tiene; el orden de las tablas no depende del
        // texto porque los feeds ordenan por la columna real de la base.
        Carbon::macro('legibleFecha', function (): string {
            $meses = [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

            /** @var Carbon $this */
            return $this->day.' de '.$meses[$this->month].' del '.$this->year;
        });

        Carbon::macro('legible', function (): string {
            /** @var Carbon $this */
            return $this->legibleFecha().', '.$this->format('g:i A');
        });

        Gate::define('manage-users', fn (User $user) => $user->esMaster());

        // El trabajo social consulta los registros pero no opera la estación
        // de escaneo (esa es tarea del personal operativo).
        Gate::define('escanear', fn (User $user) => ! $user->esTrabajadorSocial());

        // El reporte PDF del expediente sale de la casa: solo los administradores
        // y el trabajo social pueden descargarlo (el personal operativo consulta
        // en pantalla, pero no genera documentos).
        Gate::define('descargar-expediente', fn (User $user) => $user->esMaster() || $user->esTrabajadorSocial());
    }
}
