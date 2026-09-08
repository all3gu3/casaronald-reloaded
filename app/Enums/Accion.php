<?php

namespace App\Enums;

enum Accion: string
{
    case InicioSesion = 'inicio_sesion';
    case Escaneo = 'escaneo';
    case Descarga = 'descarga';
    case Alta = 'alta';
    case Edicion = 'edicion';

    public function etiqueta(): string
    {
        return match ($this) {
            self::InicioSesion => 'Inicio de sesión',
            self::Escaneo => 'Escaneo',
            self::Descarga => 'Descarga',
            self::Alta => 'Alta',
            self::Edicion => 'Edición',
        };
    }
}
