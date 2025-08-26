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
        Schema::table('tipo_clientes', function (Blueprint $table) {
            // Eliminar la columna descripcion si existe
            if (Schema::hasColumn('tipo_clientes', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
            
            // Agregar la columna identificador si no existe
            if (!Schema::hasColumn('tipo_clientes', 'identificador')) {
                $table->string('identificador', 50)->unique()->after('nombre');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipo_clientes', function (Blueprint $table) {
            // Revertir los cambios
            if (Schema::hasColumn('tipo_clientes', 'identificador')) {
                $table->dropColumn('identificador');
            }
            
            if (!Schema::hasColumn('tipo_clientes', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('nombre');
            }
        });
    }
};
