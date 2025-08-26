<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAcceso extends Model
{
    protected $table = 'log_accesos';
    
    protected $fillable = [
        'usuario_id',
        'email',
        'tipo',
        'ip_address',
        'user_agent',
        'pais',
        'ciudad',
        'detalles',
        'estado',
        'motivo_fallo',
        'fecha_acceso'
    ];

    protected $casts = [
        'fecha_acceso' => 'datetime',
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
     * Scope para filtrar por tipo
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para filtrar por usuario
     */
    public function scopeUsuario($query, $usuario_id)
    {
        return $query->where('usuario_id', $usuario_id);
    }

    /**
     * Scope para filtrar por email
     */
    public function scopeEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope para filtrar por IP
     */
    public function scopeIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Método estático para registrar un acceso
     */
    public static function registrar($tipo, $email, $estado = 'exitoso', $detalles = [])
    {
        $log = new self();
        $log->usuario_id = auth()->id();
        $log->email = $email;
        $log->tipo = $tipo;
        $log->estado = $estado;
        $log->ip_address = request()->ip();
        $log->user_agent = request()->userAgent();
        $log->detalles = $detalles['detalles'] ?? null;
        $log->motivo_fallo = $detalles['motivo_fallo'] ?? null;
        $log->pais = $detalles['pais'] ?? null;
        $log->ciudad = $detalles['ciudad'] ?? null;
        $log->fecha_acceso = now();
        
        return $log->save();
    }

    /**
     * Método para registrar un login exitoso
     */
    public static function loginExitoso($usuario)
    {
        return self::registrar('login', $usuario->email, 'exitoso', [
            'detalles' => 'Login exitoso al sistema'
        ]);
    }

    /**
     * Método para registrar un login fallido
     */
    public static function loginFallido($email, $motivo = 'Credenciales incorrectas')
    {
        return self::registrar('login_failed', $email, 'fallido', [
            'detalles' => 'Intento de login fallido',
            'motivo_fallo' => $motivo
        ]);
    }

    /**
     * Método para registrar un logout
     */
    public static function logout($usuario)
    {
        return self::registrar('logout', $usuario->email, 'exitoso', [
            'detalles' => 'Logout del sistema'
        ]);
    }
}
