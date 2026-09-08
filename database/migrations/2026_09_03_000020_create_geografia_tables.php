<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pais', function (Blueprint $table) {
            $table->id();
            $table->string('pais');
            $table->timestamps();
        });

        Schema::create('estado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pais_id')->constrained('pais')->restrictOnDelete();
            $table->string('estado');
            $table->timestamps();
        });

        Schema::create('municipio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estado_id')->constrained('estado')->restrictOnDelete();
            $table->string('municipio');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipio');
        Schema::dropIfExists('estado');
        Schema::dropIfExists('pais');
    }
};
