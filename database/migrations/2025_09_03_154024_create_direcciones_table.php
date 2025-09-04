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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->enum('tipo', ['casa', 'trabajo', 'otro'])->default('casa');
            $table->string('calle', 255);
            $table->string('numero', 20)->nullable();
            $table->string('piso', 10)->nullable();
            $table->string('departamento', 20)->nullable();
            $table->string('codigo_postal', 10)->nullable();
            $table->string('ciudad', 100);
            $table->string('provincia', 100)->nullable();
            $table->string('pais', 100)->default('Argentina');
            $table->boolean('es_principal')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['cliente_id', 'tipo']);
            $table->index('es_principal');
            $table->index(['ciudad', 'provincia']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};
