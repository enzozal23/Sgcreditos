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
        Schema::table('campos_tipo_creditos', function (Blueprint $table) {
            $table->unsignedBigInteger('credito_cuota_id')->nullable()->after('credito_id');
            $table->foreign('credito_cuota_id')->references('id')->on('credito_cuotas')->onDelete('set null');
            $table->index('credito_cuota_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campos_tipo_creditos', function (Blueprint $table) {
            $table->dropForeign(['credito_cuota_id']);
            $table->dropIndex(['credito_cuota_id']);
            $table->dropColumn('credito_cuota_id');
        });
    }
};
