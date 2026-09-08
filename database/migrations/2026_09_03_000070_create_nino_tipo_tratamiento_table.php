<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivote niño ↔ tipos de tratamiento: los 16 checkboxes del formulario de
     * solicitud por fin se persisten (el prototipo los descartaba en silencio).
     */
    public function up(): void
    {
        Schema::create('nino_tipo_tratamiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nino_id')->constrained('nino')->cascadeOnDelete();
            $table->foreignId('tipo_tratamiento_id')->constrained('tipo_tratamiento')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['nino_id', 'tipo_tratamiento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nino_tipo_tratamiento');
    }
};
