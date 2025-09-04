<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TipoCliente extends Model
{
    protected $fillable = [
        'nombre',
        'identificador',
        'estado',
        'empresa_id'
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
                
                // Campos predefinidos para contacto
                $table->string('telefono', 20)->nullable();
                $table->string('correo', 255)->nullable();
                $table->text('direccion')->nullable();
                
                $table->timestamps();
                
                // Índices para mejorar rendimiento
                $table->index('telefono');
                $table->index('correo');
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

    /**
     * Relación con Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Scope para filtrar por empresa del usuario autenticado
     */
    public function scopeDeMiEmpresa($query)
    {
        if (auth()->check() && auth()->user()->empresa_id) {
            return $query->where('empresa_id', auth()->user()->empresa_id);
        }
        return $query;
    }

    /**
     * Scope para filtrar por empresa específica
     */
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Crear campos predefinidos para este tipo de cliente
     */
    public function crearCamposPredefinidos()
    {
        $camposPredefinidos = [
            [
                'nombre_campo' => 'telefono',
                'alias' => 'Teléfono',
                'tipo_campo' => 'numero',
                'requerido' => true,
                'es_unico' => true,
                'orden' => 1
            ],
            [
                'nombre_campo' => 'correo',
                'alias' => 'Correo Electrónico',
                'tipo_campo' => 'texto',
                'requerido' => true,
                'es_unico' => true,
                'orden' => 2
            ],
            [
                'nombre_campo' => 'direccion',
                'alias' => 'Dirección',
                'tipo_campo' => 'texto',
                'requerido' => true,
                'es_unico' => true,
                'orden' => 3
            ]
        ];

        foreach ($camposPredefinidos as $campo) {
            $this->campos()->create($campo);
        }
    }

    /**
     * Boot del modelo - crear tabla y campos automáticamente
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($tipoCliente) {
            // Asignar empresa_id del usuario autenticado
            if (auth()->check() && auth()->user()->empresa_id) {
                $tipoCliente->empresa_id = auth()->user()->empresa_id;
            }
        });

        static::created(function ($tipoCliente) {
            // Crear tabla base automáticamente
            $tipoCliente->crearTablaBase();
            
            // Crear campos predefinidos automáticamente
            $tipoCliente->crearCamposPredefinidos();
        });
    }
}
