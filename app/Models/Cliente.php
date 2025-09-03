<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    use HasFactory;

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($cliente) {
            // Asignar empresa_id del usuario autenticado
            if (auth()->check() && auth()->user()->empresa_id) {
                $cliente->empresa_id = auth()->user()->empresa_id;
            }
        });
    }

    protected $fillable = [
        'nombre',
        'apellido',
        'dni',
        'email',
        'telefono',
        'direccion',
        'provincia_id',
        'codigo_localidad',
        'codigo_postal',
        'fecha_nacimiento',
        'estado',
        'observaciones',
        'empresa_id'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    /**
     * Obtener la fecha de nacimiento formateada
     */
    public function getFechaNacimientoFormateadaAttribute()
    {
        return $this->fecha_nacimiento ? $this->fecha_nacimiento->format('d/m/Y') : null;
    }

    /**
     * Obtener el nombre completo del cliente
     */
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    /**
     * Relación con Provincia
     */
    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id', 'codigo_provincia');
    }

    /**
     * Relación con Localidad
     */
    public function localidad()
    {
        return $this->belongsTo(Localidad::class, 'codigo_localidad', 'codigo_localidad');
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para buscar por nombre, apellido o DNI
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('apellido', 'like', "%{$termino}%")
              ->orWhere('dni', 'like', "%{$termino}%")
              ->orWhere('email', 'like', "%{$termino}%");
        });
    }

    /**
     * Relación con Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope global para filtrar por empresa del usuario autenticado
     */
    public function scopeDeMiEmpresa($query)
    {
        if (auth()->check() && auth()->user()->empresa_id) {
            return $query->where('empresa_id', auth()->user()->empresa_id);
        }
        return $query;
    }
}
