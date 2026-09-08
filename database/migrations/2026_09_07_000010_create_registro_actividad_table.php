<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bitácora de actividad de las cuentas (inicios de sesión, escaneos,
     * descargas, altas y ediciones). Sin updated_at: es un registro de
     * auditoría de solo inserción.
     */
    public function up(): void
    {
        Schema::create('registro_actividad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('accion', 20);
            $table->string('detalle')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('accion');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registro_actividad');
    }
};
