<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogos simples: id + columna de valor (mismo nombre que la tabla) + timestamps.
     * Las tablas muertas del prototipo (`tratamiento`, `habitaciones`) ya no se crean.
     */
    private const CATALOGOS = [
        'clasificacion_social',
        'edo_salud',
        'escolaridad',
        'hospital',
        'ocupacion',
        'parentesco',
        'salario_minimo',
        'tipo_dieta',
        'tipo_ninio',
        'tipo_tratamiento',
        'trabajador_social',
        'zona',
    ];

    public function up(): void
    {
        foreach (self::CATALOGOS as $catalogo) {
            Schema::create($catalogo, function (Blueprint $table) use ($catalogo) {
                $table->id();
                $table->string($catalogo);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse(self::CATALOGOS) as $catalogo) {
            Schema::dropIfExists($catalogo);
        }
    }
};
