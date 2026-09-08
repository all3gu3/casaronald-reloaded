<?php

namespace App\Enums;

enum Servicio: string
{
    case Lavanderia = 'lavanderia';
    case Comedor = 'comedor';
    case Escuela = 'escuela';
    case Transporte = 'transporte';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Lavanderia => 'Lavandería',
            self::Comedor => 'Comedor',
            self::Escuela => 'Escuelita',
            self::Transporte => 'Transporte',
        };
    }

    public function icono(): string
    {
        return match ($this) {
            self::Lavanderia => 'fa-shirt',
            self::Comedor => 'fa-utensils',
            self::Escuela => 'fa-graduation-cap',
            self::Transporte => 'fa-bus',
        };
    }

    public function ruta(): string
    {
        return match ($this) {
            self::Lavanderia => 'registros-lavanderia',
            self::Comedor => 'registros-comedor',
            self::Escuela => 'registros-escuela',
            self::Transporte => 'registros-transporte',
        };
    }
}
