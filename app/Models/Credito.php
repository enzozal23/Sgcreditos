<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Credito extends Model
{
    use HasFactory;

    protected $table = 'creditos';

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($credito) {
            // Asignar empresa_id del usuario autenticado
            if (auth()->check() && auth()->user()->empresa_id) {
                $credito->empresa_id = auth()->user()->empresa_id;
            }
        });
    }

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
        'empresa_id'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    /**
     * Relación con las cuotas del crédito
     */
    public function cuotas()
    {
        return $this->hasMany(CreditoCuota::class)->ordenado();
    }

    public function camposCredito()
    {
        return $this->hasMany(CampoCredito::class);
    }

    /**
     * Scope para créditos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Relación con Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Scope para filtrar por empresa del usuario autenticado
     */
    public function scopeDeMiEmpresa($query)
    {
        if (auth()->check() && auth()->user()->empresa_id) {
            return $query->where('empresa_id', auth()->user()->empresa_id);
        }
        return $query;
    }

    /**
     * Scope para filtrar por empresa específica
     */
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
