<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAmortizacion extends Model
{
    use HasFactory;

    protected $table = 'tipos_amortizacion';

    protected $fillable = [
        'nombre',
        'descripcion',
        'formula',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scope para filtrar por estado activo
     */
    public function scopeActivo($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para filtrar por estado inactivo
     */
    public function scopeInactivo($query)
    {
        return $query->where('estado', false);
    }
}
