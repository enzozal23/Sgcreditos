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
        Schema::create('log_acciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->string('accion', 100); // CREATE, UPDATE, DELETE, LOGIN, LOGOUT, etc.
            $table->string('modulo', 100); // usuarios, clientes, creditos, etc.
            $table->string('entidad', 100)->nullable(); // nombre de la tabla o entidad
            $table->unsignedBigInteger('entidad_id')->nullable(); // ID del registro afectado
            $table->text('descripcion')->nullable(); // descripción detallada de la acción
            $table->json('datos_anteriores')->nullable(); // datos antes del cambio
            $table->json('datos_nuevos')->nullable(); // datos después del cambio
            $table->string('ip_address', 45)->nullable(); // dirección IP del usuario
            $table->string('user_agent')->nullable(); // navegador y sistema operativo
            $table->enum('nivel', ['info', 'warning', 'error', 'critical'])->default('info');
            $table->timestamps();
            
            // Índices para mejorar el rendimiento de consultas
            $table->index(['usuario_id', 'created_at']);
            $table->index(['accion', 'modulo']);
            $table->index(['entidad', 'entidad_id']);
            $table->index('nivel');
            $table->index('created_at');
            
            // Clave foránea opcional al usuario
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_acciones');
    }
};
