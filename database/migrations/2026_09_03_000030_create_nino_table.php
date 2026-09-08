<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expediente del menor. Cambios vs. el prototipo:
     * - `qr` con índice ÚNICO (la unicidad nunca estuvo garantizada).
     * - `numero` es string ("15-B" existía y rompía el INT).
     * - Se elimina `edad` (se calcula desde `fecha_nacimiento`, no se almacena).
     * - Se elimina `municipio_id` (nunca se escribió; el dato real es el texto libre).
     * - `foto` para la fotografía del expediente (el formulario la pedía y se descartaba).
     * - Campos de contacto secundario/dirección opcionales, como en el expediente en papel.
     */
    public function up(): void
    {
        Schema::create('nino', function (Blueprint $table) {
            $table->id();
            $table->string('qr', 6)->unique();
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->date('fecha_nacimiento');
            $table->string('sexo', 20);
            $table->string('foto')->nullable();

            // Domicilio y contacto
            $table->string('calle')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('colonia')->nullable();
            $table->string('localidad')->nullable();
            $table->string('municipio')->nullable();
            $table->string('cp', 10)->nullable();
            $table->string('primer_telefono', 20);
            $table->string('segundo_telefono', 20)->nullable();
            $table->string('dialecto')->nullable();

            // Datos médicos
            $table->string('diagnostico')->nullable();
            $table->string('medico')->nullable();
            $table->string('alerg_alimentos')->nullable();
            $table->string('alerg_medicamentos')->nullable();

            // Estancia
            $table->string('servicio')->nullable();
            $table->string('estatus_estancia')->nullable();
            $table->date('fecha_solicitud')->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_salida')->nullable();
            $table->string('observaciones')->nullable();

            // Catálogos
            $table->foreignId('trabajador_social_id')->constrained('trabajador_social')->restrictOnDelete();
            $table->foreignId('tipo_dieta_id')->constrained('tipo_dieta')->restrictOnDelete();
            $table->foreignId('hospital_id')->constrained('hospital')->restrictOnDelete();
            $table->foreignId('escolaridad_id')->constrained('escolaridad')->restrictOnDelete();
            $table->foreignId('clasificacion_social_id')->constrained('clasificacion_social')->restrictOnDelete();
            $table->foreignId('zona_id')->constrained('zona')->restrictOnDelete();
            $table->foreignId('salario_minimo_id')->constrained('salario_minimo')->restrictOnDelete();
            $table->foreignId('pais_id')->constrained('pais')->restrictOnDelete();
            $table->foreignId('estado_id')->constrained('estado')->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nino');
    }
};
