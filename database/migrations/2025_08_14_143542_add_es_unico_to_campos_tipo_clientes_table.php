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
        Schema::table('campos_tipo_clientes', function (Blueprint $table) {
            $table->boolean('es_unico')->default(false)->after('requerido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campos_tipo_clientes', function (Blueprint $table) {
            $table->dropColumn('es_unico');
        });
    }
};
