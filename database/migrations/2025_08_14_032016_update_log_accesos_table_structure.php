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
        Schema::table('log_accesos', function (Blueprint $table) {
            // Agregar nuevos campos
            $table->unsignedBigInteger('usuario_id')->nullable()->after('id');
            $table->enum('tipo', ['login', 'logout', 'login_failed', 'password_reset', 'account_locked'])->default('login')->after('email');
            $table->string('pais', 100)->nullable()->after('user_agent');
            $table->string('ciudad', 100)->nullable()->after('pais');
            $table->text('detalles')->nullable()->after('ciudad');
            $table->enum('estado', ['exitoso', 'fallido', 'bloqueado'])->default('exitoso')->after('detalles');
            $table->string('motivo_fallo', 255)->nullable()->after('estado');
            $table->timestamp('fecha_acceso')->after('motivo_fallo');
            
            // Agregar índices
            $table->index(['usuario_id', 'fecha_acceso']);
            $table->index(['email', 'tipo']);
            $table->index(['ip_address', 'fecha_acceso']);
            $table->index('estado');
            $table->index('fecha_acceso');
            
            // Agregar clave foránea
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_accesos', function (Blueprint $table) {
            // Eliminar clave foránea
            $table->dropForeign(['usuario_id']);
            
            // Eliminar índices
            $table->dropIndex(['usuario_id', 'fecha_acceso']);
            $table->dropIndex(['email', 'tipo']);
            $table->dropIndex(['ip_address', 'fecha_acceso']);
            $table->dropIndex(['estado']);
            $table->dropIndex(['fecha_acceso']);
            
            // Eliminar columnas
            $table->dropColumn([
                'usuario_id',
                'tipo',
                'pais',
                'ciudad',
                'detalles',
                'estado',
                'motivo_fallo',
                'fecha_acceso'
            ]);
        });
    }
};
