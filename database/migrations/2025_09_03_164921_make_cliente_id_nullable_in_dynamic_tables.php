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
        // Obtener todas las tablas dinámicas existentes
        $tablas = \DB::select("SHOW TABLES LIKE 'base_cliente_%'");
        
        foreach ($tablas as $tabla) {
            $nombreTabla = array_values((array) $tabla)[0];
            
            if (Schema::hasColumn($nombreTabla, 'cliente_id')) {
                Schema::table($nombreTabla, function (Blueprint $table) {
                    $table->unsignedBigInteger('cliente_id')->nullable()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Obtener todas las tablas dinámicas existentes
        $tablas = \DB::select("SHOW TABLES LIKE 'base_cliente_%'");
        
        foreach ($tablas as $tabla) {
            $nombreTabla = array_values((array) $tabla)[0];
            
            if (Schema::hasColumn($nombreTabla, 'cliente_id')) {
                Schema::table($nombreTabla, function (Blueprint $table) {
                    $table->unsignedBigInteger('cliente_id')->nullable(false)->change();
                });
            }
        }
    }
};
