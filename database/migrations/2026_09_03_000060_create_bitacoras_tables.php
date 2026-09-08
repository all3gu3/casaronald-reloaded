<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bitácoras de escaneo. Cambios vs. el prototipo:
     * - Ligadas por `nino_id` con FK dura (antes: string `qr` suelto, sin índice).
     * - `qr` se conserva como dato de despliegue/auditoría del escaneo.
     * - `entrada`/`salida` son DATETIME (eran DATE: se perdía la hora en un control de acceso).
     */
    public function up(): void
    {
        Schema::create('entradas_salidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nino_id')->constrained('nino')->restrictOnDelete();
            $table->string('qr', 6);
            $table->dateTime('entrada')->nullable();
            $table->dateTime('salida')->nullable();
            $table->timestamps();

            $table->index('qr');
        });

        Schema::create('registros_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nino_id')->constrained('nino')->restrictOnDelete();
            $table->string('qr', 6);
            $table->string('servicio', 20);
            $table->timestamps();

            $table->index('qr');
            $table->index('servicio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros_servicios');
        Schema::dropIfExists('entradas_salidas');
    }
};
