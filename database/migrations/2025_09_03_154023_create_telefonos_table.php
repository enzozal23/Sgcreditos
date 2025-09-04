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
        Schema::create('telefonos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('numero', 20);
            $table->enum('tipo', ['celular', 'casa', 'trabajo', 'otro'])->default('celular');
            $table->boolean('es_principal')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['cliente_id', 'tipo']);
            $table->index('es_principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telefonos');
    }
};
