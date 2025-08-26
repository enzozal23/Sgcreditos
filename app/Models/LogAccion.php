<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAccion extends Model
{
    protected $table = 'log_acciones';
    
    protected $fillable = [
        'usuario_id',
        'accion',
        'modulo',
        'entidad',
        'entidad_id',
        'descripcion',
        'datos_anteriores',
        'datos_nuevos',
        'ip_address',
        'user_agent',
        'nivel'
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Scope para filtrar por nivel
     */
    public function scopeNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    /**
     * Scope para filtrar por módulo
     */
    public function scopeModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    /**
     * Scope para filtrar por acción
     */
    public function scopeAccion($query, $accion)
    {
        return $query->where('accion', $accion);
    }

    /**
     * Scope para filtrar por usuario
     */
    public function scopeUsuario($query, $usuario_id)
    {
        return $query->where('usuario_id', $usuario_id);
    }

    /**
     * Método estático para registrar una acción
     */
    public static function registrar($accion, $modulo, $descripcion = null, $datos = [])
    {
        $log = new self();
        $log->usuario_id = auth()->id();
        $log->accion = $accion;
        $log->modulo = $modulo;
        $log->descripcion = $descripcion;
        $log->ip_address = request()->ip();
        $log->user_agent = request()->userAgent();
        $log->nivel = $datos['nivel'] ?? 'info';
        $log->entidad = $datos['entidad'] ?? null;
        $log->entidad_id = $datos['entidad_id'] ?? null;
        $log->datos_anteriores = $datos['datos_anteriores'] ?? null;
        $log->datos_nuevos = $datos['datos_nuevos'] ?? null;
        
        return $log->save();
    }
}
