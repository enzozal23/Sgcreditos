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
        Schema::create('credito_cuotas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credito_id');
            $table->integer('numero_cuota');
            $table->decimal('tasa', 6, 4); // Permite hasta 99.9999%
            $table->timestamps();

            // Clave foránea
            $table->foreign('credito_id')->references('id')->on('creditos')->onDelete('cascade');
            
            // Índices
            $table->index('credito_id');
            $table->index('numero_cuota');
            
            // Restricción única para evitar duplicados de número de cuota por crédito
            $table->unique(['credito_id', 'numero_cuota']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credito_cuotas');
    }
};
