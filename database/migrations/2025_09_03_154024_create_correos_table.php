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
        Schema::create('correos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('email', 255)->unique();
            $table->enum('tipo', ['personal', 'trabajo', 'otro'])->default('personal');
            $table->boolean('es_principal')->default(false);
            $table->boolean('verificado')->default(false);
            $table->timestamp('verificado_at')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['cliente_id', 'tipo']);
            $table->index('es_principal');
            $table->index('verificado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correos');
    }
};
