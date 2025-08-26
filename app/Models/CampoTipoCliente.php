<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampoTipoCliente extends Model
{
    protected $table = 'campos_tipo_clientes';
    
    protected $fillable = [
        'tipo_cliente_id',
        'nombre_campo',
        'alias',
        'tipo_campo',
        'requerido',
        'es_unico',
        'orden',
        'opciones'
    ];
    
    protected $casts = [
        'requerido' => 'boolean',
        'es_unico' => 'boolean',
        'orden' => 'integer'
    ];
    
    /**
     * Relación con TipoCliente
     */
    public function tipoCliente(): BelongsTo
    {
        return $this->belongsTo(TipoCliente::class, 'tipo_cliente_id');
    }
    
    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden', 'asc');
    }
    
    /**
     * Scope para campos activos
     */
    public function scopeActivos($query)
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
     * Validar que el nombre del campo sea único para el tipo de cliente
     */
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($campo) {
            $existe = static::where('tipo_cliente_id', $campo->tipo_cliente_id)
                           ->where('nombre_campo', $campo->nombre_campo)
                           ->exists();
            
            if ($existe) {
                throw new \Exception("Ya existe un campo con el nombre '{$campo->nombre_campo}' en este tipo de cliente.");
            }
        });
        
        static::updating(function ($campo) {
            $existe = static::where('tipo_cliente_id', $campo->tipo_cliente_id)
                           ->where('nombre_campo', $campo->nombre_campo)
                           ->where('id', '!=', $campo->id)
                           ->exists();
            
            if ($existe) {
                throw new \Exception("Ya existe un campo con el nombre '{$campo->nombre_campo}' en este tipo de cliente.");
            }
        });
    }
}
