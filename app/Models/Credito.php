<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credito extends Model
{
    use HasFactory;

    protected $table = 'creditos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo'
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
}
