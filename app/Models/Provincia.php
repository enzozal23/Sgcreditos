<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    use HasFactory;

    protected $table = 'provincias';

    protected $fillable = [
        'centroide_lat',
        'centroide_lon',
        'codigo_provincia',
        'nombre',
        'codigo_provincia_erp',
    ];

    /**
     * Relación con localidades
     */
    public function localidades()
    {
        return $this->hasMany(Localidad::class, 'provincia_id', 'codigo_provincia');
    }

    /**
     * Obtener provincias ordenadas por nombre
     */
    public function scopeOrdenadas($query)
    {
        return $query->orderBy('nombre', 'asc');
    }

    /**
     * Buscar provincias por nombre
     */
    public function scopeBuscarPorNombre($query, $nombre)
    {
        return $query->where('nombre', 'LIKE', "%{$nombre}%");
    }

    /**
     * Obtener provincia por código
     */
    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo_provincia', $codigo);
    }
}
