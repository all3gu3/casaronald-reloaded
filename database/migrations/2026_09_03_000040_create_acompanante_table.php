<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Acompañante del menor + bloque socioeconómico.
     * Los booleanos del estudio ahora son boolean (eran INT), los montos unsignedInteger.
     */
    public function up(): void
    {
        Schema::create('acompanante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nino_id')->constrained('nino')->restrictOnDelete();
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->string('edad', 10);
            $table->string('sexo', 20);
            $table->string('identificacion')->nullable();
            $table->string('tratamiento')->nullable();
            $table->date('fecha_registro');
            $table->string('observaciones')->nullable();
            $table->string('foto')->nullable();

            $table->foreignId('parentesco_id')->constrained('parentesco')->restrictOnDelete();
            $table->foreignId('escolaridad_id')->constrained('escolaridad')->restrictOnDelete();
            $table->foreignId('edo_salud_id')->constrained('edo_salud')->restrictOnDelete();
            $table->foreignId('ocupacion_id')->constrained('ocupacion')->restrictOnDelete();

            // Estudio socioeconómico
            $table->boolean('trabaja')->default(false);
            $table->boolean('licencia_goce_sueldo')->nullable();
            $table->boolean('seguro_medico')->default(false);
            $table->boolean('casa_propia')->default(false);
            $table->boolean('asistencia_financiera')->default(false);
            $table->unsignedInteger('renta_mensualidad')->nullable();
            $table->unsignedSmallInteger('dependientes_economicos')->default(0);
            $table->unsignedInteger('ingreso_mensual')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acompanante');
    }
};
