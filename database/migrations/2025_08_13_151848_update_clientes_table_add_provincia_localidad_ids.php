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
        Schema::table('clientes', function (Blueprint $table) {
            // Agregar nuevos campos
            $table->string('provincia_id')->nullable()->after('direccion');
            $table->string('localidad_id')->nullable()->after('provincia_id');
            
            // Eliminar campos antiguos
            $table->dropColumn(['ciudad', 'provincia']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Restaurar campos antiguos
            $table->string('ciudad')->nullable()->after('direccion');
            $table->string('provincia')->nullable()->after('ciudad');
            
            // Eliminar nuevos campos
            $table->dropColumn(['provincia_id', 'localidad_id']);
        });
    }
};
