<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    use HasFactory;

    protected $table = 'localidades';

    protected $fillable = [
        'centroide_lat',
        'centroide_lon',
        'codigo_localidad',
        'nombre',
        'provincia_id',
        'codigo_postal',
        'codigo_localidad_erp',
        'codigo_provincia_erp',
    ];

    /**
     * Obtener localidades por provincia
     */
    public function scopePorProvincia($query, $provinciaId)
    {
        return $query->where('provincia_id', $provinciaId);
    }

    /**
     * Obtener localidades por código postal
     */
    public function scopePorCodigoPostal($query, $codigoPostal)
    {
        return $query->where('codigo_postal', $codigoPostal);
    }

    /**
     * Buscar localidades por nombre
     */
    public function scopeBuscarPorNombre($query, $nombre)
    {
        return $query->where('nombre', 'LIKE', "%{$nombre}%");
    }

    /**
     * Obtener localidades ordenadas por nombre
     */
    public function scopeOrdenadas($query)
    {
        return $query->orderBy('nombre', 'asc');
    }

    /**
     * Relación con provincia
     */
    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id', 'codigo_provincia');
    }
}
