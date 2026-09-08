<?php

namespace App\Enums;

enum Role: string
{
    case Master = 'master';
    case Staff = 'staff';
    case TrabajadorSocial = 'trabajador_social';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Master => 'Administrador',
            self::Staff => 'Personal operativo',
            self::TrabajadorSocial => 'Trabajador social',
        };
    }
}
