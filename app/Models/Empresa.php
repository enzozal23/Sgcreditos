<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'razon_social',
        'cuit',
        'email',
        'telefono',
        'direccion',
        'ciudad',
        'provincia',
        'codigo_postal',
        'logo',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Obtener los usuarios de la empresa
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Obtener los clientes de la empresa
     */
    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }

    /**
     * Obtener los créditos de la empresa
     */
    public function creditos(): HasMany
    {
        return $this->hasMany(Credito::class);
    }

    /**
     * Obtener los tipos de cliente de la empresa
     */
    public function tiposCliente(): HasMany
    {
        return $this->hasMany(TipoCliente::class);
    }

    /**
     * Obtener los tipos de crédito de la empresa
     */
    public function tiposCredito(): HasMany
    {
        return $this->hasMany(TipoCredito::class);
    }

    /**
     * Scope para empresas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}
