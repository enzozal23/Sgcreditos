<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoCliente;
use App\Models\CampoTipoCliente;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TipoClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('tipos_clientes.clientes');
    }

    /**
     * Obtener datos para DataTable
     */
    public function data()
    {
        try {
            $tipos = TipoCliente::select([
                'id',
                'nombre',
                'identificador',
                'estado',
                'created_at'
            ])->get();

            $data = $tipos->map(function ($tipo) {
                return [
                    'id' => $tipo->id,
                    'nombre' => $tipo->nombre,
                    'identificador' => $tipo->identificador,
                    'estado' => $tipo->estado,
                    'fecha_creacion' => $tipo->created_at->format('d/m/Y H:i'),
                    'acciones' => $this->generarBotonesAcciones($tipo->id)
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            Log::error('Error al obtener tipos de clientes: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los datos'], 500);
        }
    }

    /**
     * Generar botones de acciones para DataTable
     */
    private function generarBotonesAcciones($id)
    {
        return ' 
            <div>
                <button type="button" class="btn btn-sm btn-info" onclick="definirCampos(' . $id . ')" title="Definir Campos">
                    <i class="fas fa-cogs"></i> Definir Campos
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="editarTipoCliente(' . $id . ')" title="Editar">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="eliminarTipoCliente(' . $id . ')" title="Eliminar">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        ';
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:100|unique:tipo_clientes',
                'identificador' => 'required|string|max:50|unique:tipo_clientes',
                'estado' => 'required|in:activo,inactivo'
            ]);

            // Crear el tipo de cliente
            $tipoCliente = TipoCliente::create([
                'nombre' => $request->nombre,
                'identificador' => $request->identificador,
                'estado' => $request->estado
            ]);

            // Crear la tabla base para este tipo de cliente
            $tipoCliente->crearTablaBase();

            // Registrar en el log
            LogRegistrar("Creó tipo de cliente: {$tipoCliente->nombre} ({$tipoCliente->identificador})");

            return response()->json([
                'success' => true,
                'message' => 'Tipo de cliente creado exitosamente',
                'tipo' => $tipoCliente
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear tipo de cliente: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tipo de cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $tipoCliente = TipoCliente::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'tipo' => [
                    'id' => $tipoCliente->id,
                    'nombre' => $tipoCliente->nombre,
                    'identificador' => $tipoCliente->identificador,
                    'estado' => $tipoCliente->estado,
                    'tabla_base' => $tipoCliente->tabla_base
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener tipo de cliente para editar: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el tipo de cliente'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $tipoCliente = TipoCliente::findOrFail($id);
            
            $request->validate([
                'nombre' => 'required|string|max:100|unique:tipo_clientes,nombre,' . $id,
                'identificador' => 'required|string|max:50|unique:tipo_clientes,identificador,' . $id,
                'estado' => 'required|in:activo,inactivo'
            ]);

            // Guardar datos anteriores para el log
            $datosAnteriores = [
                'nombre' => $tipoCliente->nombre,
                'identificador' => $tipoCliente->identificador,
                'estado' => $tipoCliente->estado
            ];

            $tipoCliente->update([
                'nombre' => $request->nombre,
                'identificador' => $request->identificador,
                'estado' => $request->estado
            ]);

            // Registrar en el log
            LogRegistrar("Actualizó tipo de cliente: {$tipoCliente->nombre} ({$tipoCliente->identificador})");

            return response()->json([
                'success' => true,
                'message' => 'Tipo de cliente actualizado exitosamente',
                'tipo' => $tipoCliente
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar tipo de cliente: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo de cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $tipoCliente = TipoCliente::findOrFail($id);
            
            // Guardar datos para el log antes de eliminar
            $datosEliminados = [
                'id' => $tipoCliente->id,
                'nombre' => $tipoCliente->nombre,
                'identificador' => $tipoCliente->identificador,
                'estado' => $tipoCliente->estado,
                'tabla_base' => $tipoCliente->tabla_base
            ];
            
            // Eliminar la tabla base asociada primero
            $tipoCliente->eliminarTablaBase();
            
            // Eliminar el tipo de cliente
            $tipoCliente->delete();

            // Registrar en el log
            LogRegistrar("Eliminó tipo de cliente: {$datosEliminados['nombre']} ({$datosEliminados['identificador']})");

            return response()->json([
                'success' => true,
                'message' => 'Tipo de cliente eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar tipo de cliente: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tipo de cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar la vista de campos del tipo de cliente
     */
    public function campos(string $id)
    {
        try {
            $tipoCliente = TipoCliente::findOrFail($id);
            return view('tipos_clientes.campos.index', compact('tipoCliente'));
        } catch (\Exception $e) {
            Log::error('Error al mostrar campos del tipo de cliente: ' . $e->getMessage());
            return redirect()->route('tipos.clientes')->with('error', 'Error al cargar los campos del tipo de cliente');
        }
    }

    /**
     * Obtener datos de campos para DataTable
     */
    public function camposData(string $id)
    {
        try {
            $tipoCliente = TipoCliente::findOrFail($id);
            
            // Obtener campos de la base de datos
            $campos = CampoTipoCliente::where('tipo_cliente_id', $id)
                                     ->orderBy('orden', 'asc')
                                     ->get();
            
            $data = $campos->map(function ($campo) {
                return [
                    'id' => $campo->id,
                    'nombre_campo' => $campo->nombre_campo,
                    'alias' => $campo->alias,
                    'tipo_campo' => $campo->tipo_campo,
                    'requerido' => $campo->requerido ? 1 : 0,
                    'es_unico' => $campo->es_unico ? 1 : 0,
                    'orden' => $campo->orden,
                    'acciones' => $this->generarBotonesAccionesCampos($campo->id)
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            Log::error('Error al obtener campos del tipo de cliente: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los datos'], 500);
        }
    }

    /**
     * Generar botones de acciones para campos
     */
    private function generarBotonesAccionesCampos($id)
    {
        return ' 
            <div>
                <button type="button" class="btn btn-sm btn-primary" onclick="editarCampo(' . $id . ')" title="Editar">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="eliminarCampo(' . $id . ')" title="Eliminar">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        ';
    }

    /**
     * Crear un nuevo campo
     */
    public function camposStore(Request $request, string $id)
    {
        try {
            $tipoCliente = TipoCliente::findOrFail($id);
            
            $request->validate([
                'nombre_campo' => 'required|string|max:100|regex:/^[a-z0-9_]+$/',
                'alias' => 'required|string|max:100',
                'tipo_campo' => 'required|in:texto,numero,fecha,selector',
                'orden' => 'nullable|integer|min:1',
                'requerido' => 'nullable|boolean',
                'es_unico' => 'nullable|boolean',
                'opciones' => 'nullable|string'
            ]);

            // Validar que las opciones sean requeridas para campos tipo selector
            if ($request->tipo_campo === 'selector' && empty($request->opciones)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las opciones son requeridas para campos tipo selector',
                    'errors' => ['opciones' => ['Las opciones son requeridas para campos tipo selector']]
                ], 422);
            }

            // Crear el campo en la base de datos
            $campo = CampoTipoCliente::create([
                'tipo_cliente_id' => $tipoCliente->id,
                'nombre_campo' => $request->nombre_campo,
                'alias' => $request->alias,
                'tipo_campo' => $request->tipo_campo,
                'requerido' => $request->requerido ?? false,
                'es_unico' => $request->es_unico ?? false,
                'orden' => $request->orden ?? 1,
                'opciones' => $request->opciones
            ]);

            // Crear la columna en la tabla específica del tipo de cliente
            $this->crearColumnaEnTabla($tipoCliente, $request->nombre_campo, $request->tipo_campo, $request->requerido ?? false, $request->es_unico ?? false);

            // Registrar en el log
            LogRegistrar("Creó campo '{$campo->nombre_campo}' ({$campo->alias}) para tipo de cliente: {$tipoCliente->nombre}");

            return response()->json([
                'success' => true,
                'message' => 'Campo creado exitosamente',
                'campo' => $campo
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear campo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el campo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener campo para editar
     */
    public function camposEdit(string $tipo_id, string $campo_id)
    {
        try {
            $tipoCliente = TipoCliente::findOrFail($tipo_id);
            
            // Obtener el campo de la base de datos
            $campo = CampoTipoCliente::where('tipo_cliente_id', $tipo_id)
                                    ->where('id', $campo_id)
                                    ->firstOrFail();

            // Obtener valores directamente de la base de datos sin casts
            $campoRaw = DB::table('campos_tipo_clientes')->where('id', $campo_id)->first();
            
            // Convertir valores numéricos a strings para el frontend
            $campo->requerido = (string)$campoRaw->requerido;
            $campo->es_unico = (string)$campoRaw->es_unico;



            return response()->json([
                'success' => true,
                'campo' => [
                    'id' => $campo->id,
                    'nombre_campo' => $campo->nombre_campo,
                    'alias' => $campo->alias,
                    'tipo_campo' => $campo->tipo_campo,
                    'requerido' => (string)$campoRaw->requerido,
                    'es_unico' => (string)$campoRaw->es_unico,
                    'orden' => $campo->orden,
                    'opciones' => $campo->opciones
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener campo para editar: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el campo'
            ], 500);
        }
    }

    /**
     * Actualizar campo
     */
    public function camposUpdate(Request $request, string $tipo_id, string $campo_id)
    {
        try {
            $tipoCliente = TipoCliente::findOrFail($tipo_id);
            
            $request->validate([
                'nombre_campo' => 'required|string|max:100|regex:/^[a-z0-9_]+$/',
                'alias' => 'required|string|max:100',
                'tipo_campo' => 'required|in:texto,numero,fecha,selector',
                'orden' => 'nullable|integer|min:1',
                'requerido' => 'nullable|boolean',
                'es_unico' => 'nullable|boolean',
                'opciones' => 'nullable|string'
            ]);

            // Validar que las opciones sean requeridas para campos tipo selector
            if ($request->tipo_campo === 'selector' && empty($request->opciones)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las opciones son requeridas para campos tipo selector',
                    'errors' => ['opciones' => ['Las opciones son requeridas para campos tipo selector']]
                ], 422);
            }

            // Obtener y actualizar el campo
            $campo = CampoTipoCliente::where('tipo_cliente_id', $tipo_id)
                                    ->where('id', $campo_id)
                                    ->firstOrFail();

            // Guardar el nombre anterior para actualizar la columna
            $nombreAnterior = $campo->nombre_campo;
            
            $campo->update([
                'nombre_campo' => $request->nombre_campo,
                'alias' => $request->alias,
                'tipo_campo' => $request->tipo_campo,
                'requerido' => $request->requerido ?? false,
                'es_unico' => $request->es_unico ?? false,
                'orden' => $request->orden ?? 1,
                'opciones' => $request->opciones
            ]);

            // Actualizar la columna en la tabla específica del tipo de cliente
            $this->actualizarColumnaEnTabla($tipoCliente, $nombreAnterior, $request->nombre_campo, $request->tipo_campo, $request->requerido ?? false, $request->es_unico ?? false);

            // Registrar en el log
            LogRegistrar("Actualizó campo '{$campo->nombre_campo}' ({$campo->alias}) para tipo de cliente: {$tipoCliente->nombre}");

            return response()->json([
                'success' => true,
                'message' => 'Campo actualizado exitosamente',
                'campo' => $campo
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar campo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el campo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar campo
     */
    public function camposDestroy(string $tipo_id, string $campo_id)
    {
        try {
            $tipoCliente = TipoCliente::findOrFail($tipo_id);
            
            // Obtener y eliminar el campo
            $campo = CampoTipoCliente::where('tipo_cliente_id', $tipo_id)
                                    ->where('id', $campo_id)
                                    ->firstOrFail();

            $nombreCampo = $campo->nombre_campo;
            $alias = $campo->alias;
            
            // Eliminar la columna de la tabla específica del tipo de cliente
            $this->eliminarColumnaDeTabla($tipoCliente, $nombreCampo);
            
            $campo->delete();

            // Registrar en el log
            LogRegistrar("Eliminó campo '{$nombreCampo}' ({$alias}) para tipo de cliente: {$tipoCliente->nombre}");

            return response()->json([
                'success' => true,
                'message' => 'Campo eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar campo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el campo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear columna en la tabla específica del tipo de cliente
     */
    private function crearColumnaEnTabla($tipoCliente, $nombreCampo, $tipoCampo, $requerido, $esUnico)
    {
        try {
            // Verificar si la tabla específica existe, si no, crearla
            $this->crearTablaBaseSiNoExiste($tipoCliente);
            
            $tablaNombre = $tipoCliente->tabla_base;
            $tipoColumna = $this->mapearTipoCampoAColumna($tipoCampo);
            $nullable = $requerido ? 'NOT NULL' : 'NULL';
            $unique = $esUnico ? 'UNIQUE' : '';
            
            $sql = "ALTER TABLE {$tablaNombre} ADD COLUMN {$nombreCampo} {$tipoColumna} {$nullable}";
            if ($unique) {
                $sql .= " {$unique}";
            }
            
            DB::statement($sql);
            
            Log::info("Columna creada en {$tablaNombre}: {$nombreCampo} ({$tipoColumna})");
        } catch (\Exception $e) {
            Log::error("Error al crear columna {$nombreCampo} en {$tablaNombre}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar columna en la tabla específica del tipo de cliente
     */
    private function actualizarColumnaEnTabla($tipoCliente, $nombreAnterior, $nombreNuevo, $tipoCampo, $requerido, $esUnico)
    {
        try {
            $tablaNombre = $tipoCliente->tabla_base;
            
            // Si el nombre cambió, renombrar la columna
            if ($nombreAnterior !== $nombreNuevo) {
                DB::statement("ALTER TABLE {$tablaNombre} RENAME COLUMN {$nombreAnterior} TO {$nombreNuevo}");
            }
            
            // Actualizar el tipo y restricciones
            $tipoColumna = $this->mapearTipoCampoAColumna($tipoCampo);
            $nullable = $requerido ? 'NOT NULL' : 'NULL';
            
            // Primero cambiar el tipo
            DB::statement("ALTER TABLE {$tablaNombre} MODIFY COLUMN {$nombreNuevo} {$tipoColumna}");
            
            // Luego cambiar si es nullable o no
            if ($requerido) {
                DB::statement("ALTER TABLE {$tablaNombre} MODIFY COLUMN {$nombreNuevo} {$tipoColumna} NOT NULL");
            } else {
                DB::statement("ALTER TABLE {$tablaNombre} MODIFY COLUMN {$nombreNuevo} {$tipoColumna} NULL");
            }
            
            // Manejar restricción UNIQUE
            $this->manejarRestriccionUnica($tablaNombre, $nombreNuevo, $esUnico);
            
            Log::info("Columna actualizada en {$tablaNombre}: {$nombreNuevo} ({$tipoColumna})");
        } catch (\Exception $e) {
            Log::error("Error al actualizar columna {$nombreNuevo} en {$tablaNombre}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Eliminar columna de la tabla específica del tipo de cliente
     */
    private function eliminarColumnaDeTabla($tipoCliente, $nombreCampo)
    {
        try {
            $tablaNombre = $tipoCliente->tabla_base;
            
            // Eliminar restricción UNIQUE si existe
            $this->manejarRestriccionUnica($tablaNombre, $nombreCampo, false);
            
            // Eliminar la columna
            DB::statement("ALTER TABLE {$tablaNombre} DROP COLUMN {$nombreCampo}");
            
            Log::info("Columna eliminada de {$tablaNombre}: {$nombreCampo}");
        } catch (\Exception $e) {
            Log::error("Error al eliminar columna {$nombreCampo} de {$tablaNombre}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mapear tipo de campo a tipo de columna SQL
     */
    private function mapearTipoCampoAColumna($tipoCampo)
    {
        switch ($tipoCampo) {
            case 'texto':
                return 'VARCHAR(255)';
            case 'numero':
                return 'DECIMAL(15,2)';
            case 'fecha':
                return 'DATE';
            case 'selector':
                return 'VARCHAR(100)';
            default:
                return 'VARCHAR(255)';
        }
    }

    /**
     * Crear tabla específica del tipo de cliente si no existe
     */
    private function crearTablaBaseSiNoExiste($tipoCliente)
    {
        try {
            $tablaNombre = $tipoCliente->tabla_base;
            $tableExists = DB::select("SHOW TABLES LIKE '{$tablaNombre}'");
            
            if (empty($tableExists)) {
                DB::statement("
                    CREATE TABLE {$tablaNombre} (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        cliente_id BIGINT UNSIGNED NOT NULL,
                        created_at TIMESTAMP NULL DEFAULT NULL,
                        updated_at TIMESTAMP NULL DEFAULT NULL,
                        INDEX idx_cliente_id (cliente_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
                
                Log::info("Tabla {$tablaNombre} creada exitosamente - Solo columnas básicas del sistema");
            }
        } catch (\Exception $e) {
            Log::error("Error al crear tabla {$tablaNombre}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Manejar restricción UNIQUE en columna
     */
    private function manejarRestriccionUnica($tablaNombre, $nombreCampo, $esUnico)
    {
        try {
            // Verificar si ya existe una restricción UNIQUE
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = '{$tablaNombre}' 
                AND COLUMN_NAME = '{$nombreCampo}' 
                AND CONSTRAINT_NAME LIKE '%unique%'
            ");
            
            $constraintName = null;
            if (!empty($constraints)) {
                $constraintName = $constraints[0]->CONSTRAINT_NAME;
            }
            
            if ($esUnico && !$constraintName) {
                // Crear restricción UNIQUE
                DB::statement("ALTER TABLE {$tablaNombre} ADD CONSTRAINT uk_{$nombreCampo} UNIQUE ({$nombreCampo})");
            } elseif (!$esUnico && $constraintName) {
                // Eliminar restricción UNIQUE
                DB::statement("ALTER TABLE {$tablaNombre} DROP CONSTRAINT {$constraintName}");
            }
        } catch (\Exception $e) {
            Log::error("Error al manejar restricción UNIQUE para {$nombreCampo} en {$tablaNombre}: " . $e->getMessage());
        }
    }
}
