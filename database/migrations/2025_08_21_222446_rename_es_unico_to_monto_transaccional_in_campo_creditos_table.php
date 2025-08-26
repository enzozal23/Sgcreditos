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
            $table->renameColumn('es_unico', 'monto_transaccional');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campos_tipo_creditos', function (Blueprint $table) {
            $table->renameColumn('monto_transaccional', 'es_unico');
        });
    }
};
