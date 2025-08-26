<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampoCredito extends Model
{
    protected $table = 'campos_tipo_creditos';
    
    protected $fillable = [
        'tipo_credito_id',
        'credito_id',
        'credito_cuota_id',
        'nombre_campo',
        'alias',
        'tipo_campo',
        'requerido',
        'monto_transaccional',
        'orden',
        'valor_por_defecto',
        'opciones',
        'fecha_ejecucion'
    ];
    
    protected $casts = [
        'requerido' => 'boolean',
        'monto_transaccional' => 'boolean',
        'orden' => 'integer',
        'fecha_ejecucion' => 'boolean'
    ];
    
    /**
     * Relación con TipoCredito
     */
    public function tipoCredito(): BelongsTo
    {
        return $this->belongsTo(TipoCredito::class, 'tipo_credito_id');
    }

    /**
     * Relación con Credito
     */
    public function credito(): BelongsTo
    {
        return $this->belongsTo(Credito::class, 'credito_id');
    }

    /**
     * Relación con CreditoCuota
     */
    public function creditoCuota(): BelongsTo
    {
        return $this->belongsTo(CreditoCuota::class, 'credito_cuota_id');
    }
    
    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden', 'asc');
    }
    
    /**
     * Scope para campos requeridos
     */
    public function scopeRequeridos($query)
    {
        return $query->where('requerido', true);
    }
    
    /**
     * Obtener opciones como array
     */
    public function getOpcionesArrayAttribute()
    {
        if (empty($this->opciones)) {
            return [];
        }
        
        return array_filter(array_map('trim', explode("\n", $this->opciones)));
    }
    
    /**
     * Validar que el nombre del campo sea único para el tipo de crédito
     */
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($campo) {
            // Verificar que el nombre del campo no exista ya en este tipo de crédito
            $existe = self::where('tipo_credito_id', $campo->tipo_credito_id)
                         ->where('nombre_campo', $campo->nombre_campo)
                         ->exists();
            
            if ($existe) {
                throw new \Exception('Ya existe un campo con ese nombre en este tipo de crédito.');
            }
        });
        
        static::updating(function ($campo) {
            // Verificar que el nombre del campo no exista ya en este tipo de crédito (excluyendo el actual)
            $existe = self::where('tipo_credito_id', $campo->tipo_credito_id)
                         ->where('nombre_campo', $campo->nombre_campo)
                         ->where('id', '!=', $campo->id)
                         ->exists();
            
            if ($existe) {
                throw new \Exception('Ya existe un campo con ese nombre en este tipo de crédito.');
            }
        });
    }
}
