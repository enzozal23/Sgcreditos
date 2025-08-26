<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'valor',
        'descripcion'
    ];

    /**
     * Obtener el valor de una variable por nombre
     */
    public static function getValor($nombre)
    {
        $variable = self::where('nombre', $nombre)->first();
        return $variable ? $variable->valor : null;
    }

    /**
     * Establecer el valor de una variable por nombre
     */
    public static function setValor($nombre, $valor, $descripcion = null)
    {
        return self::updateOrCreate(
            ['nombre' => $nombre],
            ['valor' => $valor, 'descripcion' => $descripcion]
        );
    }
}
