<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TipoCliente extends Model
{
    protected $fillable = [
        'nombre',
        'identificador',
        'estado'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Obtener el nombre de la tabla base para este tipo de cliente
     */
    public function getTablaBaseAttribute()
    {
        return 'base_cliente_' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Crear la tabla base para este tipo de cliente
     */
    public function crearTablaBase()
    {
        $tablaNombre = $this->tabla_base;
        
        if (!Schema::hasTable($tablaNombre)) {
            Schema::create($tablaNombre, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cliente_id');
                $table->timestamps();
                
                // Índice para cliente_id
                $table->index('cliente_id', 'idx_cliente_id');
            });
        }
    }

    /**
     * Eliminar la tabla base para este tipo de cliente
     */
    public function eliminarTablaBase()
    {
        $tablaNombre = $this->tabla_base;
        
        if (Schema::hasTable($tablaNombre)) {
            Schema::dropIfExists($tablaNombre);
        }
    }
    
    /**
     * Relación con los campos personalizados
     */
    public function campos(): HasMany
    {
        return $this->hasMany(CampoTipoCliente::class, 'tipo_cliente_id')->ordenado();
    }
    
    /**
     * Obtener campos requeridos
     */
    public function camposRequeridos(): HasMany
    {
        return $this->hasMany(CampoTipoCliente::class, 'tipo_cliente_id')->activos()->ordenado();
    }
}
