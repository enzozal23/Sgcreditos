<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class TipoCredito extends Model
{
    protected $table = 'tipo_creditos';
    
    protected $fillable = [
        'nombre',
        'identificador',
        'empresa_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Obtener el nombre de la tabla de créditos para este tipo
     */
    public function getTablaCreditoAttribute()
    {
        return 'credito_' . $this->identificador;
    }

    /**
     * Crear la tabla de créditos para este tipo
     */
    public function crearTablaCredito()
    {
        $tablaNombre = $this->tabla_credito;
        
        if (!Schema::hasTable($tablaNombre)) {
            Schema::create($tablaNombre, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cliente_id');
                $table->unsignedBigInteger('tipo_cliente_id');
                $table->unsignedBigInteger('amortizacion_id')->nullable();
                $table->timestamps();
                
                // Índices básicos
                $table->index('cliente_id');
                $table->index('tipo_cliente_id');
                $table->index('amortizacion_id');
                
                // Foreign keys
                $table->foreign('tipo_cliente_id')->references('id')->on('tipo_clientes')->onDelete('cascade');
                $table->foreign('amortizacion_id')->references('id')->on('tipos_amortizacion')->onDelete('set null');
            });
        }
    }
    
    /**
     * Actualizar la tabla de créditos con los campos personalizados
     */
    public function actualizarTablaCredito()
    {
        $tablaNombre = $this->tabla_credito;
        
        if (Schema::hasTable($tablaNombre)) {
            // Obtener los campos definidos para este tipo de crédito
            $campos = $this->campos()->get();
            
            Schema::table($tablaNombre, function (Blueprint $table) use ($campos) {
                // Agregar columna amortizacion_id si no existe
                if (!Schema::hasColumn($this->tabla_credito, 'amortizacion_id')) {
                    $table->unsignedBigInteger('amortizacion_id')->nullable()->after('tipo_cliente_id');
                    $table->index('amortizacion_id');
                    $table->foreign('amortizacion_id')->references('id')->on('tipos_amortizacion')->onDelete('set null');
                }
                
                foreach ($campos as $campo) {
                    $nombreColumna = $campo->nombre_campo;
                    
                    // Verificar si la columna ya existe
                    if (!Schema::hasColumn($this->tabla_credito, $nombreColumna)) {
                        switch ($campo->tipo_campo) {
                            case 'texto':
                                $table->string($nombreColumna, 255)->nullable();
                                break;
                            case 'numero':
                                $table->decimal($nombreColumna, 15, 2)->nullable();
                                break;
                            case 'fecha':
                                $table->date($nombreColumna)->nullable();
                                break;
                            case 'selector':
                                $table->string($nombreColumna, 100)->nullable();
                                break;
                            case 'cuota':
                                $table->boolean($nombreColumna)->default(0);
                                break;
                            default:
                                $table->string($nombreColumna, 255)->nullable();
                                break;
                        }
                    }
                }
            });
        }
    }

    /**
     * Eliminar la tabla de créditos para este tipo
     */
    public function eliminarTablaCredito()
    {
        $tablaNombre = $this->tabla_credito;
        
        if (Schema::hasTable($tablaNombre)) {
            Schema::dropIfExists($tablaNombre);
        }
    }

    /**
     * Generar identificador único basado en el nombre
     */
    public static function generarIdentificador($nombre)
    {
        // Convertir a minúsculas y reemplazar espacios por guiones bajos
        $base = strtolower(trim($nombre));
        $base = preg_replace('/\s+/', '_', $base);
        // Remover caracteres especiales excepto guiones bajos
        $base = preg_replace('/[^a-z0-9_]/', '', $base);
        
        $identificador = $base;
        $contador = 1;
        
        while (self::where('identificador', $identificador)->exists()) {
            $identificador = $base . '_' . $contador;
            $contador++;
        }
        
        return $identificador;
    }

    /**
     * Relación con los campos personalizados
     */
    public function campos(): HasMany
    {
        return $this->hasMany(CampoCredito::class, 'tipo_credito_id')->ordenado();
    }
    
    /**
     * Obtener campos requeridos
     */
    public function camposRequeridos(): HasMany
    {
        return $this->hasMany(CampoCredito::class, 'tipo_credito_id')->requeridos()->ordenado();
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
     * Obtener información del cliente desde la tabla dinámica
     */
    public static function obtenerCliente($tipoClienteId, $clienteId)
    {
        $tipoCliente = \App\Models\TipoCliente::find($tipoClienteId);
        if (!$tipoCliente) {
            return null;
        }
        
        return \DB::table($tipoCliente->tabla_base)->find($clienteId);
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($tipoCredito) {
            if (empty($tipoCredito->identificador)) {
                $tipoCredito->identificador = self::generarIdentificador($tipoCredito->nombre);
            }
            
            // Asignar empresa_id del usuario autenticado
            if (auth()->check() && auth()->user()->empresa_id) {
                $tipoCredito->empresa_id = auth()->user()->empresa_id;
            }
        });
        
        static::created(function ($tipoCredito) {
            $tipoCredito->crearTablaCredito();
        });
        
        static::deleting(function ($tipoCredito) {
            // Guardar el nombre de la tabla antes de eliminar el modelo
            $tipoCredito->tabla_credito_temp = $tipoCredito->tabla_credito;
        });
        
        static::deleted(function ($tipoCredito) {
            // Eliminar la tabla usando el nombre guardado
            if (isset($tipoCredito->tabla_credito_temp)) {
                $tablaNombre = $tipoCredito->tabla_credito_temp;
                
                if (Schema::hasTable($tablaNombre)) {
                    Schema::dropIfExists($tablaNombre);
                    \Log::info('Tabla de créditos eliminada automáticamente', ['tabla' => $tablaNombre]);
                }
            }
        });
    }
}
