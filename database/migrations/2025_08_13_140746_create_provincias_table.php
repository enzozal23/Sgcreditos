<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('provincias', function (Blueprint $table) {
            $table->id();
            $table->string('centroide_lat')->nullable();
            $table->string('centroide_lon')->nullable();
            $table->string('codigo_provincia')->unique(); // id del archivo SQL
            $table->string('nombre');
            $table->string('codigo_provincia_erp')->default('');
            $table->timestamps();
            
            // Índices para mejorar el rendimiento
            $table->index('codigo_provincia');
            $table->index('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provincias');
    }
};
