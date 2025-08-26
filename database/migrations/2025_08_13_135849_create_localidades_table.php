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
        Schema::create('localidades', function (Blueprint $table) {
            $table->id();
            $table->string('centroide_lat')->nullable();
            $table->string('centroide_lon')->nullable();
            $table->string('codigo_localidad')->unique(); // id del archivo SQL
            $table->string('nombre');
            $table->string('provincia_id');
            $table->integer('codigo_postal');
            $table->string('codigo_localidad_erp', 10)->default('');
            $table->string('codigo_provincia_erp', 10)->nullable();
            $table->timestamps();
            
            // Índices para mejorar el rendimiento
            $table->index('provincia_id');
            $table->index('codigo_postal');
            $table->index('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('localidades');
    }
};
