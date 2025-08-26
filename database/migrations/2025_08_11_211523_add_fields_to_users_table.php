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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->string('nombre')->after('username');
            $table->string('apellido')->after('nombre');
            $table->integer('intentos_login')->default(0)->after('password');
            $table->boolean('bloqueado')->default(false)->after('intentos_login');
            $table->boolean('habilitado')->default(true)->after('bloqueado');
            $table->timestamp('ultimo_login')->nullable()->after('habilitado');
            $table->timestamp('ultima_fecha_restablecimiento')->nullable()->after('ultimo_login');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'nombre',
                'apellido',
                'intentos_login',
                'bloqueado',
                'habilitado',
                'ultimo_login',
                'ultima_fecha_restablecimiento'
            ]);
        });
    }
};
