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
        Schema::table('campos_tipo_creditos', function (Blueprint $table) {
            // Agregar columna para fecha de ejecución (fecha de cobro)
            $table->boolean('fecha_ejecucion')->default(false)->after('opciones');
            
            // La columna es_unico ya existe, solo agregamos fecha_ejecucion
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campos_tipo_creditos', function (Blueprint $table) {
            // Eliminar columna fecha_ejecucion
            $table->dropColumn('fecha_ejecucion');
        });
    }
};
