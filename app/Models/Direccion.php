<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direcciones';

    protected $fillable = [
        'cliente_id',
        'tipo',
        'calle',
        'numero',
        'piso',
        'departamento',
        'codigo_postal',
        'ciudad',
        'provincia',
        'pais',
        'es_principal',
        'observaciones'
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Scope para direcciones principales
    public function scopePrincipales($query)
    {
        return $query->where('es_principal', true);
    }

    // Scope por tipo
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Scope por ciudad
    public function scopePorCiudad($query, $ciudad)
    {
        return $query->where('ciudad', 'like', "%{$ciudad}%");
    }

    // Scope por empresa (a través del cliente)
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->whereHas('cliente', function ($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        });
    }

    // Método para obtener dirección completa formateada
    public function getDireccionCompletaAttribute()
    {
        $direccion = $this->calle;
        
        if ($this->numero) {
            $direccion .= ' ' . $this->numero;
        }
        
        if ($this->piso) {
            $direccion .= ', Piso ' . $this->piso;
        }
        
        if ($this->departamento) {
            $direccion .= ', Depto ' . $this->departamento;
        }
        
        if ($this->codigo_postal) {
            $direccion .= ' (' . $this->codigo_postal . ')';
        }
        
        $direccion .= ', ' . $this->ciudad;
        
        if ($this->provincia) {
            $direccion .= ', ' . $this->provincia;
        }
        
        $direccion .= ', ' . $this->pais;
        
        return $direccion;
    }
}
