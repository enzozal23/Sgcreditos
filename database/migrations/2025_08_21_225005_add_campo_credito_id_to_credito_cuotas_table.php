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
        Schema::table('credito_cuotas', function (Blueprint $table) {
            $table->unsignedBigInteger('campo_credito_id')->nullable()->after('credito_id');
            $table->foreign('campo_credito_id')->references('id')->on('campos_tipo_creditos')->onDelete('cascade');
            $table->index('campo_credito_id');
            
            // Remover la restricción única existente para permitir múltiples cuotas con el mismo número
            $table->dropUnique(['credito_id', 'numero_cuota']);
            
            // Agregar nueva restricción única que incluye campo_credito_id
            $table->unique(['credito_id', 'numero_cuota', 'campo_credito_id'], 'credito_cuotas_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credito_cuotas', function (Blueprint $table) {
            $table->dropForeign(['campo_credito_id']);
            $table->dropIndex(['campo_credito_id']);
            $table->dropColumn('campo_credito_id');
            
            // Restaurar la restricción única original
            $table->dropUnique('credito_cuotas_unique');
            $table->unique(['credito_id', 'numero_cuota']);
        });
    }
};
