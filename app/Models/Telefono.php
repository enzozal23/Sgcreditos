<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telefono extends Model
{
    use HasFactory;

    protected $table = 'telefonos';

    protected $fillable = [
        'cliente_id',
        'numero',
        'tipo',
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

    // Scope para teléfonos principales
    public function scopePrincipales($query)
    {
        return $query->where('es_principal', true);
    }

    // Scope por tipo
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Scope por empresa (a través del cliente)
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->whereHas('cliente', function ($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        });
    }
}
