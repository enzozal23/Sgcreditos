<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correo extends Model
{
    use HasFactory;

    protected $table = 'correos';

    protected $fillable = [
        'cliente_id',
        'email',
        'tipo',
        'es_principal',
        'verificado',
        'verificado_at',
        'observaciones'
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'verificado' => 'boolean',
        'verificado_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Scope para correos principales
    public function scopePrincipales($query)
    {
        return $query->where('es_principal', true);
    }

    // Scope por tipo
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Scope para correos verificados
    public function scopeVerificados($query)
    {
        return $query->where('verificado', true);
    }

    // Scope por empresa (a través del cliente)
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->whereHas('cliente', function ($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        });
    }
}
