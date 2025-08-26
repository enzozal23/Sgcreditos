<?php

namespace App\Http\Controllers;

use App\Models\TipoCredito;
use App\Models\CampoCredito;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TipoCreditoController extends Controller
{
    /**
     * Mostrar la vista de tipos de créditos
     */
    public function index()
    {
        return view('tipos-creditos.index');
    }

    /**
     * Obtener datos para DataTable
     */
    public function getData(): JsonResponse
    {
        $tiposCredito = TipoCredito::orderBy('created_at', 'desc')->get();
        
        $data = $tiposCredito->map(function ($tipo) {
            return [
                'id' => $tipo->id,
                'nombre' => $tipo->nombre,
                'identificador' => $tipo->identificador,
                'tabla_credito' => $tipo->tabla_credito,
                'fecha_creacion' => $tipo->created_at->format('d/m/Y H:i'),
                'acciones' => $this->generarBotonesAcciones($tipo->id)
            ];
        });
        
        return response()->json(['data' => $data]);
    }

    /**
     * Obtener datos para DataTable (vista /creditos)
     */
    public function getDataCreditos(): JsonResponse
    {
        $tiposCredito = TipoCredito::orderBy('created_at', 'desc')->get();
        
        $data = $tiposCredito->map(function ($tipo) {
            return [
                'id' => $tipo->id,
                'nombre' => $tipo->nombre,
                'identificador' => $tipo->identificador,
                'tabla_credito' => $tipo->tabla_credito,
                'fecha_creacion' => $tipo->created_at->format('d/m/Y H:i'),
                'acciones' => $this->generarBotonesAccionesCreditos($tipo->id)
            ];
        });
        
        return response()->json(['data' => $data]);
    }

    /**
     * Crear nuevo tipo de crédito
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:tipo_creditos,nombre',
            'identificador' => 'required|string|max:50|unique:tipo_creditos,identificador|regex:/^[a-z0-9_]+$/'
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Ya existe un tipo de crédito con ese nombre',
            'identificador.required' => 'El identificador es obligatorio',
            'identificador.unique' => 'Ya existe un tipo de crédito con ese identificador',
            'identificador.regex' => 'El identificador solo puede contener letras minúsculas, números y guiones bajos (_)'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tipoCredito = TipoCredito::create([
                'nombre' => $request->nombre,
                'identificador' => $request->identificador
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de crédito creado exitosamente',
                'data' => $tipoCredito
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tipo de crédito: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener tipo de crédito para editar
     */
    public function edit($id): JsonResponse
    {
        $tipoCredito = TipoCredito::find($id);
        
        if (!$tipoCredito) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de crédito no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tipoCredito
        ]);
    }

    /**
     * Actualizar tipo de crédito
     */
    public function update(Request $request, $id): JsonResponse
    {
        $tipoCredito = TipoCredito::find($id);
        
        if (!$tipoCredito) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de crédito no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:tipo_creditos,nombre,' . $id,
            'identificador' => 'required|string|max:50|unique:tipo_creditos,identificador,' . $id . '|regex:/^[a-z0-9_]+$/'
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Ya existe un tipo de crédito con ese nombre',
            'identificador.required' => 'El identificador es obligatorio',
            'identificador.unique' => 'Ya existe un tipo de crédito con ese identificador',
            'identificador.regex' => 'El identificador solo puede contener letras minúsculas, números y guiones bajos (_)'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tipoCredito->update([
                'nombre' => $request->nombre,
                'identificador' => $request->identificador
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de crédito actualizado exitosamente',
                'data' => $tipoCredito
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo de crédito: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar tipo de crédito
     */
    public function destroy($id): JsonResponse
    {
        $tipoCredito = TipoCredito::find($id);
        
        if (!$tipoCredito) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de crédito no encontrado'
            ], 404);
        }

        try {
            // Guardar información para logging
            $nombreTipoCredito = $tipoCredito->nombre;
            $tablaCredito = $tipoCredito->tabla_credito;
            
            // Eliminar el tipo de crédito (esto eliminará los campos asociados por cascade)
            $tipoCredito->delete();
            
            \Log::info('Tipo de crédito eliminado', [
                'tipo_credito_id' => $id,
                'nombre' => $nombreTipoCredito,
                'tabla_preservada' => $tablaCredito
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de crédito eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al eliminar tipo de crédito', [
                'tipo_credito_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tipo de crédito: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar botones de acciones para DataTable (vista /tipos-creditos)
     */
    private function generarBotonesAcciones($id): string
    {
        return '
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarTipoCredito(' . $id . ')" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-info" onclick="definirCampos(' . $id . ')" title="Definir Campos">
                    <i class="fas fa-cogs"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarTipoCredito(' . $id . ')" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }

    /**
     * Generar botones de acciones para DataTable (vista /creditos)
     */
    private function generarBotonesAccionesCreditos($id): string
    {
        return '
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-success" onclick="verListadoCreditos(' . $id . ')" title="Ver Listado de Créditos">
                    <i class="fas fa-list"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning" onclick="crearNuevoCredito(' . $id . ')" title="Crear Nuevo Crédito">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        ';
    }

    /**
     * Mostrar vista de campos del tipo de crédito
     */
    public function campos($id)
    {
        $tipoCredito = TipoCredito::findOrFail($id);
        return view('tipos-creditos.campos.index', compact('tipoCredito'));
    }

    /**
     * Obtener datos de campos para DataTable
     */
    public function camposData($id): JsonResponse
    {
        $tipoCredito = TipoCredito::findOrFail($id);
        $campos = $tipoCredito->campos;
        
        $data = $campos->map(function ($campo) {
            return [
                'id' => $campo->id,
                'orden' => $campo->orden,
                'nombre_campo' => $campo->nombre_campo,
                'alias' => $campo->alias,
                'tipo_campo' => $campo->tipo_campo,
                'requerido' => $campo->requerido,
                'monto_transaccional' => $campo->monto_transaccional,
                'fecha_ejecucion' => $campo->fecha_ejecucion,
                'acciones' => $this->generarBotonesAccionesCampo($campo->id)
            ];
        });
        
        return response()->json(['data' => $data]);
    }

    /**
     * Crear nuevo campo
     */
    public function camposStore(Request $request, $id): JsonResponse
    {
        $tipoCredito = TipoCredito::findOrFail($id);
        
        // Validaciones básicas para todos los campos
        $validator = Validator::make($request->all(), [
            'nombre_campo' => 'required|string|max:100|regex:/^[a-z0-9_]+$/',
            'alias' => 'required|string|max:100',
            'tipo_campo' => 'required|in:texto,numero,fecha,selector,cuota',
            'requerido' => 'boolean',
            'orden' => 'integer|min:1',
            'valor_por_defecto' => 'nullable|string|max:255',
            'opciones' => 'nullable|string'
        ], [
            'nombre_campo.required' => 'El nombre del campo es obligatorio',
            'nombre_campo.regex' => 'El nombre del campo solo puede contener letras minúsculas, números y guiones bajos',
            'alias.required' => 'El alias es obligatorio',
            'tipo_campo.required' => 'El tipo de campo es obligatorio',
            'tipo_campo.in' => 'El tipo de campo no es válido'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validaciones adicionales para campos de tipo cuota
        if ($request->tipo_campo === 'cuota') {
            $cuotaValidator = Validator::make($request->all(), [
                'numero_cuotas' => 'required|integer|min:1|max:100',
                'tasa_porcentaje' => 'required|numeric|min:0|max:100'
            ], [
                'numero_cuotas.required' => 'El número de cuotas es obligatorio cuando el tipo es cuota',
                'numero_cuotas.integer' => 'El número de cuotas debe ser un número entero',
                'numero_cuotas.min' => 'El número de cuotas debe ser mayor a 0',
                'numero_cuotas.max' => 'El número de cuotas no puede ser mayor a 100',
                'tasa_porcentaje.required' => 'La tasa de interés es obligatoria cuando el tipo es cuota',
                'tasa_porcentaje.numeric' => 'La tasa debe ser un número',
                'tasa_porcentaje.min' => 'La tasa no puede ser menor a 0',
                'tasa_porcentaje.max' => 'La tasa no puede ser mayor a 100'
            ]);

            if ($cuotaValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $cuotaValidator->errors()
                ], 422);
            }
        }

        // Validaciones adicionales para campos de tipo número (monto_transaccional)
        if ($request->tipo_campo === 'numero') {
            $numeroValidator = Validator::make($request->all(), [
                'monto_transaccional' => 'boolean'
            ]);

            if ($numeroValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $numeroValidator->errors()
                ], 422);
            }

            // Validar que no exista ya otro monto transaccional para este tipo de crédito
            if ($request->monto_transaccional == 1) {
                $existeMontoTransaccional = CampoCredito::where('tipo_credito_id', $id)
                    ->where('monto_transaccional', true)
                    ->exists();

                if ($existeMontoTransaccional) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'monto_transaccional' => ['Ya existe un campo de monto transaccional para este tipo de crédito. Solo puede haber uno.']
                        ]
                    ], 422);
                }
            }
        }

        // Validaciones adicionales para campos de tipo fecha (fecha_ejecucion)
        if ($request->tipo_campo === 'fecha') {
            $fechaValidator = Validator::make($request->all(), [
                'fecha_ejecucion' => 'boolean'
            ]);

            if ($fechaValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $fechaValidator->errors()
                ], 422);
            }

            // Validar que no exista ya otra fecha de ejecución para este tipo de crédito
            if ($request->fecha_ejecucion == 1) {
                $existeFechaEjecucion = CampoCredito::where('tipo_credito_id', $id)
                    ->where('fecha_ejecucion', true)
                    ->exists();

                if ($existeFechaEjecucion) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'fecha_ejecucion' => ['Ya existe un campo de fecha de ejecución para este tipo de crédito. Solo puede haber uno.']
                        ]
                    ], 422);
                }
            }
        }

        try {
            // Preparar datos básicos del campo
            $campoData = [
                'tipo_credito_id' => $id,
                'nombre_campo' => $request->nombre_campo,
                'alias' => $request->alias,
                'tipo_campo' => $request->tipo_campo,
                'requerido' => $request->requerido ?? false,
                'orden' => $request->orden ?? 1,
                'valor_por_defecto' => $request->valor_por_defecto,
                'opciones' => $request->opciones,
                'monto_transaccional' => false,
                'fecha_ejecucion' => false
            ];

            // Asignar campos específicos según el tipo
            if ($request->tipo_campo === 'numero') {
                $campoData['monto_transaccional'] = $request->monto_transaccional ?? false;
            } elseif ($request->tipo_campo === 'fecha') {
                $campoData['fecha_ejecucion'] = $request->fecha_ejecucion ?? false;
            }

            // Si el tipo de campo es 'cuota', crear también el registro en credito_cuotas
            if ($request->tipo_campo === 'cuota' && $request->has('numero_cuotas') && $request->has('tasa_porcentaje')) {
                try {
                    // Primero necesitamos crear o obtener el crédito correspondiente
                    $credito = \App\Models\Credito::firstOrCreate([
                        'nombre' => $tipoCredito->nombre,
                        'descripcion' => 'Crédito tipo: ' . $tipoCredito->nombre
                    ]);

                    \Log::info('Crédito creado/obtenido:', ['credito_id' => $credito->id, 'nombre' => $credito->nombre]);

                    // Asignar el credito_id al campo
                    $campoData['credito_id'] = $credito->id;

                    // Crear la cuota directamente asociada al campo
                    $cuota = \App\Models\CreditoCuota::create([
                        'credito_id' => $credito->id,
                        'campo_credito_id' => null, // Se asignará después de crear el campo
                        'numero_cuota' => $request->numero_cuotas,
                        'tasa' => $request->tasa_porcentaje
                    ]);

                    \Log::info('Cuota creada:', ['cuota_id' => $cuota->id, 'numero_cuota' => $cuota->numero_cuota, 'tasa' => $cuota->tasa]);

                    // Asignar el credito_cuota_id al campo
                    $campoData['credito_cuota_id'] = $cuota->id;
                } catch (\Exception $e) {
                    \Log::error('Error al crear cuota:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                    throw $e;
                }
            }

            $campo = CampoCredito::create($campoData);
            
            // Si es un campo de tipo cuota, actualizar la cuota con el campo_credito_id
            if ($request->tipo_campo === 'cuota' && isset($cuota)) {
                $cuota->update(['campo_credito_id' => $campo->id]);
                \Log::info('Cuota actualizada con campo_credito_id:', ['cuota_id' => $cuota->id, 'campo_credito_id' => $campo->id]);
            }
            
            // Actualizar la tabla dinámica con el nuevo campo
            $tipoCredito->actualizarTablaCredito();
            
            \Log::info('Campo creado exitosamente:', ['campo_id' => $campo->id, 'tipo' => $request->tipo_campo]);

            return response()->json([
                'success' => true,
                'message' => 'Campo creado exitosamente',
                'data' => $campo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el campo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener campo para editar
     */
    public function camposEdit($tipo_id, $campo_id): JsonResponse
    {
        \Log::info('Editando campo - Tipo ID: ' . $tipo_id . ', Campo ID: ' . $campo_id);
        $campo = CampoCredito::where('tipo_credito_id', $tipo_id)
                            ->where('id', $campo_id)
                            ->first();
        
        if (!$campo) {
            return response()->json([
                'success' => false,
                'message' => 'Campo no encontrado'
            ], 404);
        }

        // Si el campo es de tipo 'cuota', buscar los datos de cuota
        if ($campo->tipo_campo === 'cuota') {
            // Buscar la cuota asociada directamente al campo
            if ($campo->credito_cuota_id) {
                $cuota = \App\Models\CreditoCuota::find($campo->credito_cuota_id);
                if ($cuota) {
                    $campo->numero_cuotas = $cuota->numero_cuota;
                    $campo->tasa_porcentaje = $cuota->tasa_porcentaje;
                }
            } else {
                // Fallback: buscar por credito_id (para campos existentes)
                if ($campo->credito_id) {
                    $credito = \App\Models\Credito::find($campo->credito_id);
                    if ($credito) {
                        $cuota = \App\Models\CreditoCuota::where('credito_id', $credito->id)->first();
                        if ($cuota) {
                            $campo->numero_cuotas = $cuota->numero_cuota;
                            $campo->tasa_porcentaje = $cuota->tasa_porcentaje;
                        }
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $campo
        ]);
    }

    /**
     * Actualizar campo
     */
    public function camposUpdate(Request $request, $tipo_id, $campo_id): JsonResponse
    {
        $campo = CampoCredito::where('tipo_credito_id', $tipo_id)
                            ->where('id', $campo_id)
                            ->first();
        
        if (!$campo) {
            return response()->json([
                'success' => false,
                'message' => 'Campo no encontrado'
            ], 404);
        }

        $tipoCredito = TipoCredito::find($tipo_id);

        $validator = Validator::make($request->all(), [
            'nombre_campo' => 'required|string|max:100|regex:/^[a-z0-9_]+$/',
            'alias' => 'required|string|max:100',
            'tipo_campo' => 'required|in:texto,numero,fecha,selector,cuota',
            'requerido' => 'boolean',
            'monto_transaccional' => 'boolean',
            'orden' => 'integer|min:1',
            'valor_por_defecto' => 'nullable|string|max:255',
            'opciones' => 'nullable|string'
        ], [
            'nombre_campo.required' => 'El nombre del campo es obligatorio',
            'nombre_campo.regex' => 'El nombre del campo solo puede contener letras minúsculas, números y guiones bajos',
            'alias.required' => 'El alias es obligatorio',
            'tipo_campo.required' => 'El tipo de campo es obligatorio',
            'tipo_campo.in' => 'El tipo de campo no es válido'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validaciones adicionales para campos de tipo cuota
        if ($request->tipo_campo === 'cuota') {
            $cuotaValidator = Validator::make($request->all(), [
                'numero_cuotas' => 'required|integer|min:1|max:100',
                'tasa_porcentaje' => 'required|numeric|min:0|max:100'
            ], [
                'numero_cuotas.required' => 'El número de cuotas es obligatorio cuando el tipo es cuota',
                'numero_cuotas.integer' => 'El número de cuotas debe ser un número entero',
                'numero_cuotas.min' => 'El número de cuotas debe ser mayor a 0',
                'numero_cuotas.max' => 'El número de cuotas no puede ser mayor a 100',
                'tasa_porcentaje.required' => 'La tasa de interés es obligatoria cuando el tipo es cuota',
                'tasa_porcentaje.numeric' => 'La tasa debe ser un número',
                'tasa_porcentaje.min' => 'La tasa no puede ser menor a 0',
                'tasa_porcentaje.max' => 'La tasa no puede ser mayor a 100'
            ]);

            if ($cuotaValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $cuotaValidator->errors()
                ], 422);
            }
        }

        // Validaciones adicionales para campos de tipo número (monto_transaccional)
        if ($request->tipo_campo === 'numero') {
            $numeroValidator = Validator::make($request->all(), [
                'monto_transaccional' => 'boolean'
            ]);

            if ($numeroValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $numeroValidator->errors()
                ], 422);
            }

            // Validar que no exista ya otro monto transaccional para este tipo de crédito (excluyendo el actual)
            if ($request->monto_transaccional == 1) {
                $existeMontoTransaccional = CampoCredito::where('tipo_credito_id', $tipo_id)
                    ->where('monto_transaccional', true)
                    ->where('id', '!=', $campo_id)
                    ->exists();

                if ($existeMontoTransaccional) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'monto_transaccional' => ['Ya existe un campo de monto transaccional para este tipo de crédito. Solo puede haber uno.']
                        ]
                    ], 422);
                }
            }
        }

        // Validaciones adicionales para campos de tipo fecha (fecha_ejecucion)
        if ($request->tipo_campo === 'fecha') {
            $fechaValidator = Validator::make($request->all(), [
                'fecha_ejecucion' => 'boolean'
            ]);

            if ($fechaValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $fechaValidator->errors()
                ], 422);
            }

            // Validar que no exista ya otra fecha de ejecución para este tipo de crédito (excluyendo el actual)
            if ($request->fecha_ejecucion == 1) {
                $existeFechaEjecucion = CampoCredito::where('tipo_credito_id', $tipo_id)
                    ->where('fecha_ejecucion', true)
                    ->where('id', '!=', $campo_id)
                    ->exists();

                if ($existeFechaEjecucion) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'fecha_ejecucion' => ['Ya existe un campo de fecha de ejecución para este tipo de crédito. Solo puede haber uno.']
                        ]
                    ], 422);
                }
            }
        }

        try {
            // Preparar datos básicos del campo
            $campoData = [
                'nombre_campo' => $request->nombre_campo,
                'alias' => $request->alias,
                'tipo_campo' => $request->tipo_campo,
                'requerido' => $request->requerido ?? false,
                'orden' => $request->orden ?? 1,
                'valor_por_defecto' => $request->valor_por_defecto,
                'opciones' => $request->opciones,
                'monto_transaccional' => false,
                'fecha_ejecucion' => false
            ];

            // Asignar campos específicos según el tipo
            if ($request->tipo_campo === 'numero') {
                $campoData['monto_transaccional'] = $request->monto_transaccional ?? false;
            } elseif ($request->tipo_campo === 'fecha') {
                $campoData['fecha_ejecucion'] = $request->fecha_ejecucion ?? false;
            }

            // Si el tipo de campo es 'cuota', actualizar también el registro en credito_cuotas
            if ($request->tipo_campo === 'cuota' && $request->has('numero_cuotas') && $request->has('tasa_porcentaje')) {
                try {
                    // Buscar el crédito correspondiente
                    $credito = \App\Models\Credito::where('nombre', $tipoCredito->nombre)->first();
                    
                    if ($credito) {
                        // Asignar el credito_id al campo
                        $campoData['credito_id'] = $credito->id;
                        
                        // Buscar la cuota asociada a este campo específico
                        $cuota = \App\Models\CreditoCuota::where('campo_credito_id', $campo_id)->first();
                        
                        if ($cuota) {
                            // Actualizar la cuota existente
                            $cuota->update([
                                'numero_cuota' => $request->numero_cuotas,
                                'tasa' => $request->tasa_porcentaje
                            ]);
                            \Log::info('Cuota actualizada en edición:', ['cuota_id' => $cuota->id, 'numero_cuota' => $cuota->numero_cuota, 'tasa' => $cuota->tasa]);
                        } else {
                            // Crear una nueva cuota si no existe
                            $cuota = \App\Models\CreditoCuota::create([
                                'credito_id' => $credito->id,
                                'campo_credito_id' => $campo_id,
                                'numero_cuota' => $request->numero_cuotas,
                                'tasa' => $request->tasa_porcentaje
                            ]);
                            \Log::info('Cuota creada en actualización:', ['cuota_id' => $cuota->id, 'numero_cuota' => $cuota->numero_cuota, 'tasa' => $cuota->tasa]);
                        }
                        
                        // Asignar el credito_cuota_id al campo
                        $campoData['credito_cuota_id'] = $cuota->id;
                    }
                } catch (\Exception $e) {
                    \Log::error('Error al actualizar cuota:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                    throw $e;
                }
            }

            $campo->update($campoData);
            
            // Actualizar la tabla dinámica con los cambios del campo
            $tipoCredito->actualizarTablaCredito();

            return response()->json([
                'success' => true,
                'message' => 'Campo actualizado exitosamente',
                'data' => $campo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el campo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar campo
     */
    public function camposDestroy($tipo_id, $campo_id): JsonResponse
    {
        $campo = CampoCredito::where('tipo_credito_id', $tipo_id)
                            ->where('id', $campo_id)
                            ->first();
        
        if (!$campo) {
            return response()->json([
                'success' => false,
                'message' => 'Campo no encontrado'
            ], 404);
        }

        try {
            // Si el campo es de tipo 'cuota', eliminar también las cuotas asociadas
            if ($campo->tipo_campo === 'cuota') {
                \Log::info('Eliminando campo de tipo cuota:', ['campo_id' => $campo->id, 'tipo_credito_id' => $tipo_id]);
                
                // Eliminar la cuota específica asociada al campo
                $cuota = \App\Models\CreditoCuota::where('campo_credito_id', $campo->id)->first();
                if ($cuota) {
                    $cuota->delete();
                    \Log::info('Cuota eliminada:', ['cuota_id' => $cuota->id, 'numero_cuota' => $cuota->numero_cuota]);
                }
                
                // Verificar si hay otros campos de tipo cuota que usen el mismo crédito
                $otrosCamposCuota = CampoCredito::where('tipo_credito_id', $tipo_id)
                    ->where('id', '!=', $campo_id)
                    ->where('tipo_campo', 'cuota')
                    ->where('credito_id', $campo->credito_id)
                    ->count();
                
                // Si no hay otros campos de tipo cuota para este crédito, eliminar el crédito también
                if ($otrosCamposCuota == 0 && $campo->credito_id) {
                    $credito = \App\Models\Credito::find($campo->credito_id);
                    if ($credito) {
                        // Eliminar todas las cuotas restantes del crédito
                        $cuotasEliminadas = \App\Models\CreditoCuota::where('credito_id', $credito->id)->delete();
                        \Log::info('Cuotas restantes eliminadas del crédito:', ['credito_id' => $credito->id, 'cuotas_eliminadas' => $cuotasEliminadas]);
                        
                        // Eliminar el crédito
                        $credito->delete();
                        \Log::info('Crédito eliminado:', ['credito_id' => $credito->id, 'nombre' => $credito->nombre]);
                    }
                }
            }

            // Eliminar el campo
            $campo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Campo eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al eliminar campo:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el campo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar botones de acciones para campos en DataTable
     */
    private function generarBotonesAccionesCampo($id): string
    {
        \Log::info('Generando botones para campo ID: ' . $id);
        return '
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarCampo(' . $id . ')" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarCampo(' . $id . ')" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }

    /**
     * Mostrar vista de listado de créditos de un tipo específico
     */
    public function creditosListado($id)
    {
        $tipoCredito = TipoCredito::findOrFail($id);
        return view('creditos.listado', compact('tipoCredito'));
    }

    /**
     * Obtener datos de créditos para DataTable
     */
    public function creditosData($id): JsonResponse
    {
        $tipoCredito = TipoCredito::findOrFail($id);
        $tablaCredito = $tipoCredito->tabla_credito;
        
        try {
            // Verificar si la tabla existe
            if (!\Schema::hasTable($tablaCredito)) {
                return response()->json([
                    'data' => [],
                    'message' => 'La tabla de créditos no existe'
                ]);
            }

            // Obtener todos los créditos de la tabla dinámica
            $creditos = \DB::table($tablaCredito)
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $creditos->map(function ($credito) {
                // Convertir el objeto a array
                $datos = (array) $credito;
                
                // Obtener información del cliente
                if (isset($datos['cliente_id']) && $datos['cliente_id']) {
                    // Buscar en todas las tablas de clientes
                    $cliente = $this->buscarClienteEnTablas($datos['cliente_id']);
                    $datos['cliente_nombre'] = $cliente ? $cliente->nombre : 'Cliente no encontrado';
                } else {
                    $datos['cliente_nombre'] = 'Sin cliente';
                }
                
                // Obtener nombre del tipo de cliente
                if (isset($datos['tipo_cliente_id']) && $datos['tipo_cliente_id']) {
                    $tipoCliente = \App\Models\TipoCliente::find($datos['tipo_cliente_id']);
                    $datos['tipo_cliente_nombre'] = $tipoCliente ? $tipoCliente->nombre : 'Tipo no encontrado';
                } else {
                    $datos['tipo_cliente_nombre'] = 'Sin tipo';
                }
                
                // Obtener nombre del tipo de amortización
                if (isset($datos['amortizacion_id']) && $datos['amortizacion_id']) {
                    $tipoAmortizacion = \App\Models\TipoAmortizacion::find($datos['amortizacion_id']);
                    $datos['amortizacion_nombre'] = $tipoAmortizacion ? $tipoAmortizacion->nombre : 'Amortización no encontrada';
                } else {
                    $datos['amortizacion_nombre'] = 'Sin amortización';
                }
                
                // Agregar botones de acciones
                $datos['acciones'] = $this->generarBotonesAccionesCredito($credito->id);
                
                return $datos;
            });

            return response()->json(['data' => $data]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener datos de créditos: ' . $e->getMessage());
            return response()->json([
                'data' => [],
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar botones de acciones para créditos en DataTable
     */
    private function generarBotonesAccionesCredito($id): string
    {
        return '
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarCredito(' . $id . ')" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-info" onclick="verCredito(' . $id . ')" title="Ver Detalles">
                    <i class="fas fa-eye"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="verPlanPago(' . $id . ')" title="Ver Plan de Pago">
                    <i class="fas fa-calendar-alt"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarCredito(' . $id . ')" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }

    /**
     * Mostrar vista para crear nuevo crédito
     */
    public function creditosCrear($id)
    {
        $tipoCredito = TipoCredito::findOrFail($id);
        return view('creditos.crear', compact('tipoCredito'));
    }

    /**
     * Obtener cuotas disponibles para un tipo de crédito
     */
    public function obtenerCuotas($id): JsonResponse
    {
        $tipoCredito = TipoCredito::findOrFail($id);
        $camposCuota = $tipoCredito->campos()->where('tipo_campo', 'cuota')->get();
        
        $cuotas = [];
        
        foreach ($camposCuota as $campo) {
            // Buscar las cuotas asociadas a este campo
            $cuotasCampo = \App\Models\CreditoCuota::where('campo_credito_id', $campo->id)->get();
            
            foreach ($cuotasCampo as $cuota) {
                $cuotas[] = [
                    'campo_nombre' => $campo->nombre_campo,
                    'campo_alias' => $campo->alias,
                    'numero_cuota' => $cuota->numero_cuota,
                    'tasa' => $cuota->tasa,
                    'valor' => $campo->nombre_campo . '_' . $cuota->numero_cuota,
                    'texto' => $campo->alias . ' - Cuota ' . $cuota->numero_cuota . ' (' . number_format($cuota->tasa * 100, 2) . '%)'
                ];
            }
        }
        
        return response()->json(['data' => $cuotas]);
    }

    /**
     * Guardar nuevo crédito
     */
    public function creditosStore(Request $request, $id): JsonResponse
    {
        $tipoCredito = TipoCredito::findOrFail($id);
        
        try {
            // Validar que se haya proporcionado un cliente_id
            if (!$request->input('cliente_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe seleccionar un cliente'
                ], 422);
            }
            
            // Validar que el cliente_id sea un número válido
            $clienteId = $request->input('cliente_id');
            $tipoClienteId = $request->input('tipo_cliente_id');
            
            if (!is_numeric($clienteId) || $clienteId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'El ID del cliente no es válido'
                ], 422);
            }
            
            if (!$tipoClienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe seleccionar un tipo de cliente'
                ], 422);
            }
            
            // Validar tipo de amortización
            $tipoAmortizacionId = $request->input('tipo_amortizacion_id');
            if (!$tipoAmortizacionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe seleccionar un tipo de amortización'
                ], 422);
            }
            
            // Verificar que el tipo de amortización existe
            $tipoAmortizacion = \App\Models\TipoAmortizacion::find($tipoAmortizacionId);
            if (!$tipoAmortizacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de amortización no encontrado'
                ], 422);
            }
            
            // Obtener el tipo de cliente para saber en qué tabla buscar
            $tipoCliente = \App\Models\TipoCliente::find($tipoClienteId);
            if (!$tipoCliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de cliente no encontrado'
                ], 422);
            }
            
            // Verificar que el cliente existe en la tabla dinámica correspondiente
            $tablaCliente = $tipoCliente->tabla_base;
            $clienteExiste = \DB::table($tablaCliente)->where('id', $clienteId)->exists();
            
            if (!$clienteExiste) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente seleccionado no existe en la tabla correspondiente'
                ], 422);
            }
            
            // Obtener todos los campos definidos para este tipo de crédito
            $campos = $tipoCredito->campos()->orderBy('orden')->get();
            
            // Validar que existan campos definidos
            if ($campos->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay campos definidos para este tipo de crédito'
                ], 422);
            }
            
            // Preparar datos para insertar en la tabla dinámica
            $datosCredito = [
                'cliente_id' => $request->input('cliente_id'),
                'tipo_cliente_id' => $tipoClienteId, // Agregar referencia al tipo de cliente
                'amortizacion_id' => $request->input('tipo_amortizacion_id'), // Agregar referencia al tipo de amortización
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Procesar cada campo del formulario
            foreach ($campos as $campo) {
                if ($campo->tipo_campo === 'cuota') {
                    // Para campos de tipo cuota, procesar el selector de cuotas
                    $selectorCuotas = $request->input('selector_cuotas');
                    
                    if ($campo->requerido && empty($selectorCuotas)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Debe seleccionar una cuota'
                        ], 422);
                    }
                    
                    // Si se seleccionó una cuota, verificar si corresponde a este campo
                    if (!empty($selectorCuotas) && strpos($selectorCuotas, $campo->nombre_campo) === 0) {
                        // Guardar 1 (true) si se seleccionó esta cuota
                        $datosCredito[$campo->nombre_campo] = 1;
                    } else {
                        // Guardar 0 (false) si no se seleccionó
                        $datosCredito[$campo->nombre_campo] = 0;
                    }
                } else {
                    // Para campos que no son cuota
                    $valorCampo = $request->input('campo_' . $campo->nombre_campo);
                    
                    // Validar campos requeridos
                    if ($campo->requerido && empty($valorCampo)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'El campo "' . $campo->alias . '" es obligatorio'
                        ], 422);
                    }
                    
                    // Asignar valor al campo
                    $datosCredito[$campo->nombre_campo] = $valorCampo;
                }
            }
            
            // Insertar en la tabla dinámica
            $creditoId = \DB::table($tipoCredito->tabla_credito)->insertGetId($datosCredito);
            
            // Log para debugging
            \Log::info('Crédito creado exitosamente', [
                'tipo_credito_id' => $tipoCredito->id,
                'tabla_credito' => $tipoCredito->tabla_credito,
                'credito_id' => $creditoId,
                'cliente_id' => $clienteId,
                'datos_credito' => $datosCredito
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Crédito creado exitosamente',
                'data' => ['id' => $creditoId]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al crear crédito: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el crédito: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener plan de pago para un crédito específico
     */
    public function obtenerPlanPago($tipoCreditoId, $creditoId): JsonResponse
    {
        try {
            $debug = [];
            $debug[] = "Iniciando cálculo de plan de pago - TipoCreditoId: $tipoCreditoId, CreditoId: $creditoId";
            
            $tipoCredito = TipoCredito::findOrFail($tipoCreditoId);
            $tablaCredito = $tipoCredito->tabla_credito;
            
            $debug[] = "Tipo de crédito encontrado - Tabla: $tablaCredito";
            
            // Obtener datos del crédito
            $credito = \DB::table($tablaCredito)->where('id', $creditoId)->first();
            if (!$credito) {
                return response()->json([
                    'success' => false,
                    'message' => 'Crédito no encontrado',
                    'debug' => $debug
                ], 404);
            }
            
            $debug[] = "Crédito encontrado - Datos: " . json_encode((array) $credito);
            
            // Obtener el tipo de amortización
            $tipoAmortizacion = \App\Models\TipoAmortizacion::find($credito->amortizacion_id);
            if (!$tipoAmortizacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de amortización no encontrado',
                    'debug' => $debug
                ], 404);
            }
            
            $debug[] = "Tipo de amortización encontrado: " . $tipoAmortizacion->nombre;
            
            // Obtener campos del tipo de crédito
            $campos = $tipoCredito->campos()->get();
            
            $debug[] = "Campos del tipo de crédito - Total: " . $campos->count();
            
            // Buscar el campo marcado como monto transaccional
            $campoMontoTransaccional = $campos->where('monto_transaccional', true)->first();
            if (!$campoMontoTransaccional) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un campo marcado como monto transaccional',
                    'debug' => $debug
                ], 404);
            }
            
            $debug[] = "Campo monto transaccional encontrado: " . $campoMontoTransaccional->nombre_campo;
            
            // Buscar el campo marcado como fecha de ejecución
            $campoFechaEjecucion = $campos->where('fecha_ejecucion', true)->first();
            if (!$campoFechaEjecucion) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un campo marcado como fecha de ejecución',
                    'debug' => $debug
                ], 404);
            }
            
            $debug[] = "Campo fecha de ejecución encontrado: " . $campoFechaEjecucion->nombre_campo;
            
            // Obtener el monto del crédito
            $monto = $credito->{$campoMontoTransaccional->nombre_campo};
            if (!$monto || $monto <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto del crédito no es válido',
                    'debug' => $debug
                ], 404);
            }
            
            $debug[] = "Monto del crédito: $monto";
            
            // Obtener la fecha de ejecución del crédito
            $fechaEjecucion = $credito->{$campoFechaEjecucion->nombre_campo};
            if (!$fechaEjecucion) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha de ejecución del crédito no es válida',
                    'debug' => $debug
                ], 404);
            }
            
            $fechaEjecucion = \Carbon\Carbon::parse($fechaEjecucion);
            $debug[] = "Fecha de ejecución del crédito: " . $fechaEjecucion->format('d/m/Y');
            
            // Buscar campos de tipo cuota que estén seleccionados (valor = 1)
            $camposCuotaSeleccionados = [];
            foreach ($campos as $campo) {
                if ($campo->tipo_campo === 'cuota' && $credito->{$campo->nombre_campo} == 1) {
                    $camposCuotaSeleccionados[] = $campo;
                    $debug[] = "Campo cuota seleccionado: " . $campo->nombre_campo . " (valor: " . $credito->{$campo->nombre_campo} . ")";
                }
            }
            
            if (empty($camposCuotaSeleccionados)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ninguna cuota seleccionada',
                    'debug' => $debug
                ], 404);
            }
            
            $debug[] = "Campos de cuota seleccionados - Total: " . count($camposCuotaSeleccionados);
            
            // Obtener las cuotas y tasas asociadas
            $planPago = [];
            
            foreach ($camposCuotaSeleccionados as $campoCuota) {
                // Obtener la cuota asociada a este campo usando credito_cuota_id
                $cuota = \App\Models\CreditoCuota::where('id', $campoCuota->credito_cuota_id)->first();
                
                if (!$cuota) {
                    $debug[] = "No se encontró cuota para el campo " . $campoCuota->nombre_campo;
                    continue;
                }
                
                $debug[] = "Cuota encontrada para campo " . $campoCuota->nombre_campo . " - Número: " . $cuota->numero_cuota . ", Tasa: " . $cuota->tasa;
                
                // Generar las cuotas basándose en el numero_cuota
                $totalCuotas = $cuota->numero_cuota;
                
                for ($i = 1; $i <= $totalCuotas; $i++) {
                    // Calcular la fecha de vencimiento sumando meses a la fecha de ejecución
                    $fechaVencimiento = $fechaEjecucion->copy()->addMonths($i);
                    
                    $cuotaCalculada = $this->calcularCuota(
                        $monto,
                        $i, // Número de cuota actual
                        $cuota->tasa,
                        $tipoAmortizacion->nombre,
                        $fechaVencimiento,
                        $totalCuotas
                    );
                    
                    $planPago[] = $cuotaCalculada;
                    
                    $debug[] = "Cuota " . $i . " calculada - Tasa: " . $cuota->tasa . ", Fecha: " . $fechaVencimiento->format('d/m/Y');
                }
            }
            
            // Ordenar por número de cuota
            usort($planPago, function($a, $b) {
                return $a['numero_cuota'] <=> $b['numero_cuota'];
            });
            
            $debug[] = "Plan de pago final - Total cuotas: " . count($planPago);
            
            return response()->json([
                'success' => true,
                'data' => $planPago,
                'info' => [
                    'monto' => $monto,
                    'tipo_amortizacion' => $tipoAmortizacion->nombre,
                    'total_cuotas' => count($planPago)
                ],
                'debug' => $debug
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular el plan de pago: ' . $e->getMessage(),
                'debug' => isset($debug) ? $debug : ['Error sin debug disponible']
            ], 500);
        }
    }
    
    /**
     * Calcular cuota según el tipo de amortización usando la fórmula de la base de datos
     */
    private function calcularCuota($monto, $numeroCuota, $tasa, $tipoAmortizacion, $fechaVencimiento, $totalCuotas)
    {
        $tasaMensual = $tasa / 12; // Convertir tasa anual a mensual
        
        // Obtener la fórmula desde la base de datos
        $tipoAmortizacionDB = \App\Models\TipoAmortizacion::where('nombre', $tipoAmortizacion)->first();
        
        if (!$tipoAmortizacionDB) {
            throw new \Exception('Tipo de amortización no encontrado: ' . $tipoAmortizacion);
        }
        
        return $this->calcularCuotaConFormula(
            $monto, 
            $numeroCuota, 
            $tasaMensual, 
            $fechaVencimiento, 
            $totalCuotas,
            $tipoAmortizacionDB->formula,
            $tipoAmortizacionDB->nombre
        );
    }

    /**
     * Calcular cuota usando la fórmula específica del tipo de amortización
     */
    private function calcularCuotaConFormula($monto, $numeroCuota, $tasaMensual, $fechaVencimiento, $totalCuotas, $formula, $tipoAmortizacion)
    {
        // Usar la fórmula de la base de datos para calcular la cuota
        return $this->calcularCuotaConFormulaDB($monto, $numeroCuota, $tasaMensual, $fechaVencimiento, $totalCuotas, $formula, $tipoAmortizacion);
    }

    /**
     * Calcular cuota usando la fórmula de la base de datos
     */
    private function calcularCuotaConFormulaDB($monto, $numeroCuota, $tasaMensual, $fechaVencimiento, $totalCuotas, $formula, $tipoAmortizacion)
    {
        // Preparar variables para la fórmula
        $variables = [
            'Principal' => $monto,
            'i' => $tasaMensual,
            'n' => $totalCuotas,
            'Saldo' => $this->calcularSaldoInicial($monto, $numeroCuota, $tasaMensual, $totalCuotas, $tipoAmortizacion)
        ];
        
        // Calcular la cuota usando la fórmula
        $cuotaMensual = $this->evaluarFormula($formula, $variables, $tipoAmortizacion);
        
        // Calcular los componentes de la cuota
        $resultado = $this->calcularComponentesCuota($monto, $numeroCuota, $tasaMensual, $cuotaMensual, $totalCuotas, $tipoAmortizacion);
        
        return [
            'numero_cuota' => $numeroCuota,
            'fecha_vencimiento' => $fechaVencimiento->format('d/m/Y'),
            'capital' => round($resultado['amortizacion'], 2),
            'interes' => round($resultado['interes'], 2),
            'cuota' => round($cuotaMensual, 2),
            'saldo' => round($resultado['saldo_final'], 2)
        ];
    }

    /**
     * Evaluar la fórmula de amortización
     */
    private function evaluarFormula($formula, $variables, $tipoAmortizacion)
    {
        // Intentar evaluar la fórmula de forma dinámica
        try {
            return $this->evaluarFormulaDinamica($formula, $variables, $tipoAmortizacion);
        } catch (\Exception $e) {
            // Si falla, usar el switch como fallback para tipos conocidos
            return $this->evaluarFormulaConocida($variables, $tipoAmortizacion);
        }
    }

    /**
     * Evaluar fórmula de forma dinámica usando las variables
     */
    private function evaluarFormulaDinamica($formula, $variables, $tipoAmortizacion)
    {
        // Reemplazar variables en la fórmula
        $formulaEvaluada = $formula;
        
        // Reemplazar variables específicas
        foreach ($variables as $variable => $valor) {
            $formulaEvaluada = str_replace($variable, $valor, $formulaEvaluada);
        }
        
        // Reemplazar operadores matemáticos comunes
        $formulaEvaluada = str_replace('×', '*', $formulaEvaluada);
        $formulaEvaluada = str_replace('÷', '/', $formulaEvaluada);
        $formulaEvaluada = str_replace('^', '**', $formulaEvaluada);
        
        // Limpiar la fórmula para seguridad
        $formulaEvaluada = preg_replace('/[^0-9+\-*\/()\s.]/', '', $formulaEvaluada);
        
        // Evaluar la fórmula
        return eval("return $formulaEvaluada;");
    }

    /**
     * Evaluar fórmula para tipos conocidos (fallback)
     */
    private function evaluarFormulaConocida($variables, $tipoAmortizacion)
    {
        switch (strtolower($tipoAmortizacion)) {
            case 'francesa':
                return $variables['Principal'] * ($variables['i'] * pow(1 + $variables['i'], $variables['n'])) / (pow(1 + $variables['i'], $variables['n']) - 1);
                
            case 'alemana':
                return ($variables['Saldo'] * $variables['i']) + ($variables['Principal'] / $variables['n']);
                
            case 'americana':
                $numeroCuota = $variables['n'];
                if ($numeroCuota == $variables['n']) {
                    return ($variables['Principal'] * $variables['i']) + $variables['Principal'];
                } else {
                    return $variables['Principal'] * $variables['i'];
                }
                
            default:
                throw new \Exception('Tipo de amortización no soportado: ' . $tipoAmortizacion);
        }
    }

    /**
     * Calcular el saldo inicial para una cuota específica
     */
    private function calcularSaldoInicial($monto, $numeroCuota, $tasaMensual, $totalCuotas, $tipoAmortizacion)
    {
        // Intentar calcular de forma dinámica
        try {
            return $this->calcularSaldoDinamico($monto, $numeroCuota, $tasaMensual, $totalCuotas, $tipoAmortizacion);
        } catch (\Exception $e) {
            // Si falla, usar el switch como fallback
            return $this->calcularSaldoConocido($monto, $numeroCuota, $tasaMensual, $totalCuotas, $tipoAmortizacion);
        }
    }

    /**
     * Calcular saldo de forma dinámica
     */
    private function calcularSaldoDinamico($monto, $numeroCuota, $tasaMensual, $totalCuotas, $tipoAmortizacion)
    {
        // Para tipos de amortización genéricos, calcular el saldo paso a paso
        // Primero necesitamos calcular la cuota mensual
        $cuotaMensual = $this->calcularCuotaMensual($monto, $tasaMensual, $totalCuotas, $tipoAmortizacion);
        
        // Calcular el saldo al inicio de cada cuota
        $saldoInicial = $monto;
        for ($i = 1; $i < $numeroCuota; $i++) {
            $interes = $saldoInicial * $tasaMensual;
            $amortizacion = $cuotaMensual - $interes;
            $saldoInicial -= $amortizacion;
        }
        
        return $saldoInicial;
    }

    /**
     * Calcular cuota mensual para tipos de amortización genéricos
     */
    private function calcularCuotaMensual($monto, $tasaMensual, $totalCuotas, $tipoAmortizacion)
    {
        // Para tipos genéricos, usar una fórmula básica de cuota constante
        // Esto se puede mejorar en el futuro con fórmulas más específicas
        return $monto * ($tasaMensual * pow(1 + $tasaMensual, $totalCuotas)) / (pow(1 + $tasaMensual, $totalCuotas) - 1);
    }

    /**
     * Calcular saldo para tipos conocidos (fallback)
     */
    private function calcularSaldoConocido($monto, $numeroCuota, $tasaMensual, $totalCuotas, $tipoAmortizacion)
    {
        switch (strtolower($tipoAmortizacion)) {
            case 'francesa':
                // Para francesa, necesitamos calcular el saldo paso a paso
                $cuotaMensual = $monto * ($tasaMensual * pow(1 + $tasaMensual, $totalCuotas)) / (pow(1 + $tasaMensual, $totalCuotas) - 1);
                $saldoInicial = $monto;
                for ($i = 1; $i < $numeroCuota; $i++) {
                    $interes = $saldoInicial * $tasaMensual;
                    $amortizacion = $cuotaMensual - $interes;
                    $saldoInicial -= $amortizacion;
                }
                return $saldoInicial;
                
            case 'alemana':
                // Para alemana, el saldo se calcula directamente
                $amortizacionMensual = $monto / $totalCuotas;
                return $monto - ($amortizacionMensual * ($numeroCuota - 1));
                
            case 'americana':
                // Para americana, el saldo siempre es el principal hasta la última cuota
                return $monto;
                
            default:
                return $monto;
        }
    }

    /**
     * Calcular los componentes de la cuota (interés, amortización, saldo final)
     */
    private function calcularComponentesCuota($monto, $numeroCuota, $tasaMensual, $cuotaMensual, $totalCuotas, $tipoAmortizacion)
    {
        // Intentar calcular de forma dinámica
        try {
            return $this->calcularComponentesDinamico($monto, $numeroCuota, $tasaMensual, $cuotaMensual, $totalCuotas, $tipoAmortizacion);
        } catch (\Exception $e) {
            // Si falla, usar el switch como fallback
            return $this->calcularComponentesConocido($monto, $numeroCuota, $tasaMensual, $cuotaMensual, $totalCuotas, $tipoAmortizacion);
        }
    }

    /**
     * Calcular componentes de forma dinámica
     */
    private function calcularComponentesDinamico($monto, $numeroCuota, $tasaMensual, $cuotaMensual, $totalCuotas, $tipoAmortizacion)
    {
        // Calcular el saldo inicial para esta cuota específica
        $saldoInicial = $this->calcularSaldoInicial($monto, $numeroCuota, $tasaMensual, $totalCuotas, $tipoAmortizacion);
        
        // Calcular los componentes
        $interes = $saldoInicial * $tasaMensual;
        $amortizacion = $cuotaMensual - $interes;
        $saldoFinal = $saldoInicial - $amortizacion;
        
        return [
            'interes' => $interes,
            'amortizacion' => $amortizacion,
            'saldo_final' => $saldoFinal
        ];
    }

    /**
     * Calcular componentes para tipos conocidos (fallback)
     */
    private function calcularComponentesConocido($monto, $numeroCuota, $tasaMensual, $cuotaMensual, $totalCuotas, $tipoAmortizacion)
    {
        switch (strtolower($tipoAmortizacion)) {
            case 'francesa':
                $saldoInicial = $this->calcularSaldoInicial($monto, $numeroCuota, $tasaMensual, $totalCuotas, $tipoAmortizacion);
                $interes = $saldoInicial * $tasaMensual;
                $amortizacion = $cuotaMensual - $interes;
                $saldoFinal = $saldoInicial - $amortizacion;
                break;
                
            case 'alemana':
                $amortizacionMensual = $monto / $totalCuotas;
                $saldoInicial = $this->calcularSaldoInicial($monto, $numeroCuota, $tasaMensual, $totalCuotas, $tipoAmortizacion);
                $interes = $saldoInicial * $tasaMensual;
                $amortizacion = $amortizacionMensual;
                $saldoFinal = $saldoInicial - $amortizacion;
                break;
                
            case 'americana':
                $interes = $monto * $tasaMensual;
                $amortizacion = ($numeroCuota == $totalCuotas) ? $monto : 0;
                $saldoFinal = ($numeroCuota == $totalCuotas) ? 0 : $monto;
                break;
                
            default:
                throw new \Exception('Tipo de amortización no soportado: ' . $tipoAmortizacion);
        }
        
        return [
            'interes' => $interes,
            'amortizacion' => $amortizacion,
            'saldo_final' => $saldoFinal
        ];
    }

    /**
     * Buscar un cliente en todas las tablas de clientes.
     *
     * @param int $clienteId
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private function buscarClienteEnTablas($clienteId)
    {
        $tipoClientes = \App\Models\TipoCliente::all();
        foreach ($tipoClientes as $tipoCliente) {
            $tablaBase = $tipoCliente->tabla_base;
            if (\Schema::hasTable($tablaBase)) {
                $cliente = \DB::table($tablaBase)->where('id', $clienteId)->first();
                if ($cliente) {
                    return $cliente;
                }
            }
        }
        return null;
    }
}
