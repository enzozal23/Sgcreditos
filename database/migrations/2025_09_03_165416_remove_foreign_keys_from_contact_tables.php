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
        // Eliminar FK de tabla telefonos (si existe)
        if (Schema::hasTable('telefonos')) {
            try {
                Schema::table('telefonos', function (Blueprint $table) {
                    $table->dropForeign(['cliente_id']);
                });
            } catch (\Exception $e) {
                // La FK no existe, continuar
            }
            
            // Eliminar índice si existe
            try {
                Schema::table('telefonos', function (Blueprint $table) {
                    $table->dropIndex(['cliente_id']);
                });
            } catch (\Exception $e) {
                // El índice no existe, continuar
            }
        }
        
        // Eliminar FK de tabla correos (si existe)
        if (Schema::hasTable('correos')) {
            try {
                Schema::table('correos', function (Blueprint $table) {
                    $table->dropForeign(['cliente_id']);
                });
            } catch (\Exception $e) {
                // La FK no existe, continuar
            }
            
            // Eliminar índice si existe
            try {
                Schema::table('correos', function (Blueprint $table) {
                    $table->dropIndex(['cliente_id']);
                });
            } catch (\Exception $e) {
                // El índice no existe, continuar
            }
        }
        
        // Eliminar FK de tabla direcciones (si existe)
        if (Schema::hasTable('direcciones')) {
            try {
                Schema::table('direcciones', function (Blueprint $table) {
                    $table->dropForeign(['cliente_id']);
                });
            } catch (\Exception $e) {
                // La FK no existe, continuar
            }
            
            // Eliminar índice si existe
            try {
                Schema::table('direcciones', function (Blueprint $table) {
                    $table->dropIndex(['cliente_id']);
                });
            } catch (\Exception $e) {
                // El índice no existe, continuar
            }
        }
        
        // Mantener cliente_id pero sin restricciones FK
        // El cliente_id ahora apunta al ID de la tabla dinámica, no a clientes.id
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar FK de tabla telefonos
        Schema::table('telefonos', function (Blueprint $table) {
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->index('cliente_id');
        });
        
        // Restaurar FK de tabla correos
        Schema::table('correos', function (Blueprint $table) {
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->index('cliente_id');
        });
        
        // Restaurar FK de tabla direcciones
        Schema::table('direcciones', function (Blueprint $table) {
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->index('cliente_id');
        });
    }
};
