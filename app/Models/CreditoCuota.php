<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditoCuota extends Model
{
    use HasFactory;

    protected $table = 'credito_cuotas';

    protected $fillable = [
        'credito_id',
        'campo_credito_id',
        'numero_cuota',
        'tasa'
    ];

    protected $casts = [
        'numero_cuota' => 'integer',
        'tasa' => 'decimal:4'
    ];

    /**
     * Relación con el crédito
     */
    public function credito()
    {
        return $this->belongsTo(Credito::class);
    }

    /**
     * Relación con el campo de crédito específico
     */
    public function campoCredito()
    {
        return $this->belongsTo(CampoCredito::class, 'campo_credito_id');
    }

    /**
     * Relación con campos de crédito (mantener para compatibilidad)
     */
    public function camposCredito()
    {
        return $this->hasMany(CampoCredito::class);
    }

    /**
     * Scope para ordenar por número de cuota
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('numero_cuota', 'asc');
    }

    /**
     * Accessor para obtener la tasa como porcentaje
     */
    public function getTasaPorcentajeAttribute()
    {
        return $this->tasa * 100;
    }

    /**
     * Mutator para convertir porcentaje a decimal
     */
    public function setTasaAttribute($value)
    {
        // Si el valor viene como porcentaje (ej: 15.5), convertirlo a decimal (0.1550)
        if (is_numeric($value)) {
            if ($value > 1) {
                $this->attributes['tasa'] = $value / 100;
            } else {
                $this->attributes['tasa'] = $value;
            }
        } else {
            $this->attributes['tasa'] = 0;
        }
    }
}
