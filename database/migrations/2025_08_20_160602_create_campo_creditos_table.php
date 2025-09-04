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
        Schema::create('campos_tipo_creditos', function (Blueprint $table) {
            $table->id();
            
            // Relación con el tipo de crédito
            $table->unsignedBigInteger('tipo_credito_id');
            $table->foreign('tipo_credito_id')->references('id')->on('tipo_creditos')->onDelete('cascade');
            
            // Campos de configuración
            $table->string('nombre_campo', 100); // Nombre de la columna en la base de datos
            $table->string('alias', 100); // Nombre que se muestra al usuario
            $table->enum('tipo_campo', ['texto', 'numero', 'fecha', 'selector', 'cuota', 'archivo']); // Tipo de campo
            $table->boolean('requerido')->default(false); // Si el campo es requerido
            $table->boolean('es_unico')->default(false); // Si el campo debe ser único
            $table->integer('orden')->default(1); // Orden de aparición
            $table->string('valor_por_defecto')->nullable(); // Valor por defecto
            $table->text('opciones')->nullable(); // Opciones para campos tipo selector
            
            // Campos de auditoría
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index('tipo_credito_id');
            $table->index('orden');
            $table->unique(['tipo_credito_id', 'nombre_campo']); // Un campo no puede repetirse en el mismo tipo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campos_tipo_creditos');
    }
};
