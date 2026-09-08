<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registro de estancia (módulo Operación, a completar en Fase 2).
     * `fecha_egreso` ahora es NULLABLE: una estancia abierta no tiene fecha de salida.
     */
    public function up(): void
    {
        Schema::create('registro_operativo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nino_id')->constrained('nino')->restrictOnDelete();
            $table->foreignId('hospital_id')->constrained('hospital')->restrictOnDelete();
            $table->foreignId('tipo_ninio_id')->constrained('tipo_ninio')->restrictOnDelete();
            $table->foreignId('tipo_tratamiento_id')->constrained('tipo_tratamiento')->restrictOnDelete();
            $table->foreignId('tipo_dieta_id')->constrained('tipo_dieta')->restrictOnDelete();
            $table->foreignId('trabajador_social_id')->constrained('trabajador_social')->restrictOnDelete();
            $table->date('fecha_ingreso');
            $table->date('fecha_egreso')->nullable();
            $table->string('medico_atiende');
            $table->string('diagnostico');
            $table->boolean('reingreso')->default(false);
            $table->unsignedSmallInteger('ninos_adicionales')->default(0);
            $table->unsignedSmallInteger('habitacion')->nullable();
            $table->string('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registro_operativo');
    }
};
