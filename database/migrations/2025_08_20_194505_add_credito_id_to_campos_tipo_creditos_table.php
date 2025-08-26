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
            $table->unsignedBigInteger('credito_id')->nullable()->after('tipo_credito_id');
            $table->foreign('credito_id')->references('id')->on('creditos')->onDelete('set null');
            $table->index('credito_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campos_tipo_creditos', function (Blueprint $table) {
            $table->dropForeign(['credito_id']);
            $table->dropIndex(['credito_id']);
            $table->dropColumn('credito_id');
        });
    }
};
