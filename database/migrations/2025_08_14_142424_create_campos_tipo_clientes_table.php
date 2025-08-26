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
        Schema::create('campos_tipo_clientes', function (Blueprint $table) {
            $table->id();
            
            // Relación con el tipo de cliente
            $table->unsignedBigInteger('tipo_cliente_id');
            $table->foreign('tipo_cliente_id')->references('id')->on('tipo_clientes')->onDelete('cascade');
            
            // Campos de configuración
            $table->string('nombre_campo', 100); // Nombre de la columna en la base de datos
            $table->string('alias', 100); // Nombre que se muestra al usuario
            $table->enum('tipo_campo', ['texto', 'numero', 'fecha', 'selector']); // Tipo de campo
            $table->boolean('requerido')->default(false); // Si el campo es requerido
            $table->integer('orden')->default(1); // Orden de aparición
            $table->text('opciones')->nullable(); // Opciones para campos tipo selector
            
            // Campos de auditoría
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index('tipo_cliente_id');
            $table->index('orden');
            $table->unique(['tipo_cliente_id', 'nombre_campo']); // Un campo no puede repetirse en el mismo tipo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campos_tipo_clientes');
    }
};
