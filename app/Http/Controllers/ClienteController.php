<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('clientes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('creditos.clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // DEBUG: Log de todos los datos recibidos
        \Log::info('=== STORE CLIENTE DEBUG ===');
        \Log::info('All request data: ' . json_encode($request->all()));
        \Log::info('codigo_localidad from request: ' . $request->input('codigo_localidad'));
        \Log::info('provincia_id from request: ' . $request->input('provincia_id'));
        \Log::info('codigo_postal from request: ' . $request->input('codigo_postal'));
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:clientes,dni',
            'email' => 'required|email|max:255|unique:clientes,email',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'provincia_id' => 'nullable|string|max:10',
            'codigo_localidad' => 'nullable|string|max:20',
            'codigo_postal' => 'nullable|string|max:10',
            'fecha_nacimiento' => 'nullable|date',
            'estado' => 'required|in:activo,inactivo,pendiente',
            'observaciones' => 'nullable|string'
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'dni.required' => 'El DNI es obligatorio.',
            'dni.unique' => 'El DNI ya está registrado.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.unique' => 'El email ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo, inactivo o pendiente.'
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed: ' . json_encode($validator->errors()->toArray()));
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            
            // Asignar empresa_id del usuario autenticado
            if (auth()->check() && auth()->user()->empresa_id) {
                $data['empresa_id'] = auth()->user()->empresa_id;
            }
            
            \Log::info('Data to create: ' . json_encode($data));
            
            $cliente = Cliente::create($data);
            
            // Registrar en el log
            LogRegistrar("Creó cliente: {$cliente->nombre} {$cliente->apellido} (DNI: {$cliente->dni})");
            
            \Log::info('Cliente created successfully: ' . json_encode([
                'id' => $cliente->id,
                'provincia_id' => $cliente->provincia_id,
                'codigo_localidad' => $cliente->codigo_localidad,
                'codigo_postal' => $cliente->codigo_postal
            ]));
            
            $cliente->fecha_nacimiento_formateada = $cliente->fecha_nacimiento_formateada;
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente.',
                'cliente' => $cliente
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating cliente: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        // Verificar que el cliente pertenece a la empresa del usuario
        if (auth()->check() && auth()->user()->empresa_id && $cliente->empresa_id !== auth()->user()->empresa_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este cliente.'
            ], 403);
        }
        
        $cliente->load(['provincia', 'localidad']);
        $cliente->fecha_nacimiento_formateada = $cliente->fecha_nacimiento_formateada;
        
        return response()->json([
            'success' => true,
            'cliente' => $cliente
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        // Verificar que el cliente pertenece a la empresa del usuario
        if (auth()->check() && auth()->user()->empresa_id && $cliente->empresa_id !== auth()->user()->empresa_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para editar este cliente.'
            ], 403);
        }
        
        $cliente->load(['provincia', 'localidad']);
        $cliente->fecha_nacimiento_formateada = $cliente->fecha_nacimiento_formateada;
        
        return response()->json([
            'success' => true,
            'cliente' => $cliente
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        // DEBUG: Log de todos los datos recibidos
        \Log::info('=== UPDATE CLIENTE DEBUG ===');
        \Log::info('Cliente ID: ' . $cliente->id);
        \Log::info('All request data: ' . json_encode($request->all()));
        \Log::info('codigo_localidad from request: ' . $request->input('codigo_localidad'));
        \Log::info('provincia_id from request: ' . $request->input('provincia_id'));
        \Log::info('codigo_postal from request: ' . $request->input('codigo_postal'));
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:clientes,dni,' . $cliente->id,
            'email' => 'required|email|max:255|unique:clientes,email,' . $cliente->id,
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'provincia_id' => 'nullable|string|max:10',
            'codigo_localidad' => 'nullable|string|max:20',
            'codigo_postal' => 'nullable|string|max:10',
            'fecha_nacimiento' => 'nullable|date',
            'estado' => 'required|in:activo,inactivo,pendiente',
            'observaciones' => 'nullable|string'
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'dni.required' => 'El DNI es obligatorio.',
            'dni.unique' => 'El DNI ya está registrado.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.unique' => 'El email ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo, inactivo o pendiente.'
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed: ' . json_encode($validator->errors()->toArray()));
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar que el cliente pertenece a la empresa del usuario
            if (auth()->check() && auth()->user()->empresa_id && $cliente->empresa_id !== auth()->user()->empresa_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para actualizar este cliente.'
                ], 403);
            }
            
            $data = $request->all();
            \Log::info('Data to update: ' . json_encode($data));
            
            $cliente->update($data);
            
            \Log::info('Cliente updated successfully: ' . json_encode([
                'id' => $cliente->id,
                'provincia_id' => $cliente->provincia_id,
                'codigo_localidad' => $cliente->codigo_localidad,
                'codigo_postal' => $cliente->codigo_postal
            ]));
            
            $cliente->load(['provincia', 'localidad']);
            $cliente->fecha_nacimiento_formateada = $cliente->fecha_nacimiento_formateada;
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado exitosamente.',
                'cliente' => $cliente
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating cliente: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        try {
            // Verificar que el cliente pertenece a la empresa del usuario
            if (auth()->check() && auth()->user()->empresa_id && $cliente->empresa_id !== auth()->user()->empresa_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para eliminar este cliente.'
                ], 403);
            }
            
            $cliente->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos para DataTable
     */
    public function getData()
    {
        try {
            \Log::info('getData method called');
            \Log::info('Auth check: ' . (auth()->check() ? 'true' : 'false'));
            
            if (!auth()->check()) {
                \Log::warning('User not authenticated in getData');
                return response()->json(['error' => 'No autenticado'], 401);
            }
            
            $clientes = Cliente::deMiEmpresa()->with(['provincia', 'localidad'])->orderBy('created_at', 'desc')->get();
            \Log::info('Clientes found: ' . $clientes->count());
            
            $data = [];
            foreach ($clientes as $cliente) {
                $data[] = [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'apellido' => $cliente->apellido,
                    'dni' => $cliente->dni,
                    'email' => $cliente->email,
                    'telefono' => $cliente->telefono,
                    'estado' => $cliente->estado,
                    'fecha_creacion' => $cliente->created_at->format('d/m/Y'),
                    'acciones' => '<div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" title="Editar" onclick="editarCliente(' . $cliente->id . ')">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="eliminarCliente(' . $cliente->id . ')">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>'
                ];
            }
            
            \Log::info('Data prepared: ' . count($data) . ' records');
            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            \Log::error('Error in getData: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Mostrar vista de clientes por tipo
     */
    public function clientesPorTipo($tipoClienteId)
    {
        try {
            $tipoCliente = \App\Models\TipoCliente::findOrFail($tipoClienteId);
            return view('clientes.por-tipo', compact('tipoCliente'));
        } catch (\Exception $e) {
            \Log::error('Error al obtener tipo de cliente: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Tipo de cliente no encontrado');
        }
    }

    /**
     * Obtener configuración de campos del tipo de cliente
     */
    public function clientesPorTipoCampos($tipoClienteId)
    {
        try {
            $campos = \App\Models\CampoTipoCliente::where('tipo_cliente_id', $tipoClienteId)
                ->orderBy('orden')
                ->get();
            
            return response()->json([
                'success' => true,
                'campos' => $campos
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en clientesPorTipoCampos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la configuración de campos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos de clientes por tipo para DataTable
     */
    public function clientesPorTipoData($tipoClienteId)
    {
        try {
            $tipoCliente = \App\Models\TipoCliente::findOrFail($tipoClienteId);
            $tablaNombre = $tipoCliente->tabla_base;
            
            // Verificar si la tabla existe
            if (!\Schema::hasTable($tablaNombre)) {
                return response()->json(['data' => []]);
            }
            
            // Obtener configuración de campos del tipo de cliente
            $campos = \App\Models\CampoTipoCliente::where('tipo_cliente_id', $tipoClienteId)
                ->orderBy('orden')
                ->get();
            
            // Obtener solo los nombres de campos configurados
            $columnasMostrar = $campos->pluck('nombre_campo')->toArray();
            
            // Obtener datos de la tabla dinámica
            $clientes = \DB::table($tablaNombre)->get();
            
            $data = [];
            foreach ($clientes as $cliente) {
                $fila = [];
                // Agregar el ID para la columna de acciones
                $fila['id'] = $cliente->id;
                foreach ($columnasMostrar as $columna) {
                    $valor = $cliente->$columna;
                    $fila[$columna] = $valor;
                }
                $data[] = $fila;
            }
            
            return response()->json([
                'data' => $data,
                'columnas' => $columnasMostrar
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en clientesPorTipoData: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function clientesPorTipoStore(Request $request, $tipoClienteId)
    {
        try {
            $tipoCliente = \App\Models\TipoCliente::findOrFail($tipoClienteId);
            $tablaNombre = $tipoCliente->tabla_base;
            
            // Verificar si la tabla existe
            if (!\Schema::hasTable($tablaNombre)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La tabla asociada no existe'
                ], 400);
            }
            
            // Obtener configuración de campos
            $campos = \App\Models\CampoTipoCliente::where('tipo_cliente_id', $tipoClienteId)
                ->orderBy('orden')
                ->get();
            
            // Validar campos requeridos
            $reglas = [];
            foreach ($campos as $campo) {
                if ($campo->requerido) {
                    $reglas[$campo->nombre_campo] = 'required';
                }
            }
            $reglas['cliente_id'] = 'required|integer';
            
            $request->validate($reglas);
            
            // Preparar datos para inserción
            $datos = ['cliente_id' => $request->cliente_id];
            foreach ($campos as $campo) {
                $datos[$campo->nombre_campo] = $request->input($campo->nombre_campo);
            }
            
            // Insertar en la tabla dinámica
            $id = \DB::table($tablaNombre)->insertGetId($datos);
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'id' => $id
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en clientesPorTipoStore: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function clientesPorTipoEdit($tipoClienteId, $id)
    {
        try {
            \Log::info("clientesPorTipoEdit llamado con tipoClienteId: {$tipoClienteId}, id: {$id}");
            
            $tipoCliente = \App\Models\TipoCliente::findOrFail($tipoClienteId);
            $tablaNombre = $tipoCliente->tabla_base;
            
            \Log::info("Tabla nombre: {$tablaNombre}");
            
            // Verificar si la tabla existe
            if (!\Schema::hasTable($tablaNombre)) {
                \Log::error("Tabla {$tablaNombre} no existe");
                return response()->json([
                    'success' => false,
                    'message' => 'La tabla asociada no existe'
                ], 400);
            }
            
            // Obtener datos del cliente
            $cliente = \DB::table($tablaNombre)->where('id', $id)->first();
            
            \Log::info("Cliente encontrado:", $cliente ? (array)$cliente : ['null']);
            if ($cliente) {
                // Log de todos los campos del cliente
                foreach ((array)$cliente as $campo => $valor) {
                    \Log::info("Campo del cliente {$campo}: {$valor}");
                }
            }
            
            if (!$cliente) {
                \Log::error("Cliente con ID {$id} no encontrado en tabla {$tablaNombre}");
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }
            
            \Log::info("Retornando cliente exitosamente");
            return response()->json([
                'success' => true,
                'cliente' => $cliente
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en clientesPorTipoEdit: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los datos del cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function clientesPorTipoUpdate(Request $request, $tipoClienteId, $id)
    {
        try {
            \Log::info("clientesPorTipoUpdate llamado con tipoClienteId: {$tipoClienteId}, id: {$id}");
            \Log::info("Datos recibidos:", $request->all());
            \Log::info("Request method:", ['method' => $request->method()]);
            \Log::info("Request headers:", $request->headers->all());
            
            // Log específico para ver qué campos se están enviando
            \Log::info("Request all():", $request->all());
            \Log::info("Request input():", $request->input());
            \Log::info("Request post():", $request->post());
            
            foreach ($request->all() as $key => $value) {
                \Log::info("Campo {$key}: {$value}");
            }
            
            $tipoCliente = \App\Models\TipoCliente::findOrFail($tipoClienteId);
            $tablaNombre = $tipoCliente->tabla_base;
            
            \Log::info("Tabla nombre: {$tablaNombre}");
            
            // Verificar si la tabla existe
            if (!\Schema::hasTable($tablaNombre)) {
                \Log::error("Tabla {$tablaNombre} no existe");
                return response()->json([
                    'success' => false,
                    'message' => 'La tabla asociada no existe'
                ], 400);
            }
            
            // Verificar si el cliente existe
            $clienteExistente = \DB::table($tablaNombre)->where('id', $id)->first();
            if (!$clienteExistente) {
                \Log::error("Cliente con ID {$id} no encontrado en tabla {$tablaNombre}");
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }
            
            // Obtener configuración de campos
            $campos = \App\Models\CampoTipoCliente::where('tipo_cliente_id', $tipoClienteId)
                ->orderBy('orden')
                ->get();
            
            \Log::info("Campos configurados:", $campos->toArray());
            
            // Validar campos requeridos
            $reglas = [];
            foreach ($campos as $campo) {
                if ($campo->requerido) {
                    $reglas[$campo->nombre_campo] = 'required';
                }
            }
            $reglas['cliente_id'] = 'required|integer';
            
            \Log::info("Reglas de validación:", $reglas);
            
            $request->validate($reglas);
            
            // Preparar datos para actualización
            $datos = ['cliente_id' => $request->cliente_id];
            foreach ($campos as $campo) {
                $valor = $request->input($campo->nombre_campo);
                $datos[$campo->nombre_campo] = $valor;
                \Log::info("Campo configurado {$campo->nombre_campo}: {$valor}");
            }
            
            \Log::info("Datos para actualizar:", $datos);
            
            // Actualizar en la tabla dinámica
            \DB::table($tablaNombre)->where('id', $id)->update($datos);
            
            \Log::info("Cliente actualizado exitosamente");
            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en clientesPorTipoUpdate: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function clientesPorTipoDestroy($tipoClienteId, $id)
    {
        try {
            $tipoCliente = \App\Models\TipoCliente::findOrFail($tipoClienteId);
            $tablaNombre = $tipoCliente->tabla_base;
            
            // Verificar si la tabla existe
            if (!\Schema::hasTable($tablaNombre)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La tabla asociada no existe'
                ], 400);
            }
            
            // Verificar si el cliente existe
            $clienteExistente = \DB::table($tablaNombre)->where('id', $id)->first();
            if (!$clienteExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }
            
            // Eliminar de la tabla dinámica
            \DB::table($tablaNombre)->where('id', $id)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en clientesPorTipoDestroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar clientes por tipo y término de búsqueda
     */
    public function buscar(Request $request)
    {
        try {
            $tipoClienteId = $request->input('tipo_cliente_id');
            $searchTerm = $request->input('search');
            
            if (!$tipoClienteId || !$searchTerm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de cliente y término de búsqueda son requeridos'
                ], 400);
            }
            
            $tipoCliente = \App\Models\TipoCliente::findOrFail($tipoClienteId);
            $tablaNombre = $tipoCliente->tabla_base;
            
            // Verificar si la tabla existe
            if (!\Schema::hasTable($tablaNombre)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La tabla asociada no existe'
                ], 400);
            }
            
            // Obtener la configuración de campos del tipo de cliente
            $camposConfigurados = \App\Models\CampoTipoCliente::where('tipo_cliente_id', $tipoClienteId)
                ->orderBy('orden')
                ->get();
            
            // Buscar el campo marcado como es_unico = 1 (ID único del cliente)
            $campoUnico = $camposConfigurados->where('es_unico', 1)->first();
            
            // Construir la consulta de búsqueda
            $query = \DB::table($tablaNombre);
            
            // Si se encontró un campo único, darle prioridad en la búsqueda
            if ($campoUnico) {
                $query->where(function($q) use ($searchTerm, $campoUnico, $camposConfigurados) {
                    // Primero buscar en el campo único (ID del cliente)
                    $q->where($campoUnico->nombre_campo, 'LIKE', "%{$searchTerm}%");
                    
                    // Luego buscar en otros campos configurados
                    foreach ($camposConfigurados as $campo) {
                        if ($campo->id !== $campoUnico->id) {
                            $q->orWhere($campo->nombre_campo, 'LIKE', "%{$searchTerm}%");
                        }
                    }
                });
            } else {
                // Si no hay campo único configurado, buscar en campos estándar
                $query->where(function($q) use ($searchTerm, $camposConfigurados) {
                    $q->where('id', 'LIKE', "%{$searchTerm}%");
                    
                    // Buscar en campos configurados
                    foreach ($camposConfigurados as $campo) {
                        $q->orWhere($campo->nombre_campo, 'LIKE', "%{$searchTerm}%");
                    }
                });
            }
            
            $clientes = $query->limit(10)->get();
            
            // Formatear campos numéricos para que no muestren decimales
            $clientes = $clientes->map(function($cliente) use ($camposConfigurados) {
                foreach ($camposConfigurados as $campo) {
                    $nombreCampo = $campo->nombre_campo;
                    if (isset($cliente->$nombreCampo) && is_numeric($cliente->$nombreCampo)) {
                        // Si es un número entero, convertirlo a entero
                        if (floor($cliente->$nombreCampo) == $cliente->$nombreCampo) {
                            $cliente->$nombreCampo = (int)$cliente->$nombreCampo;
                        }
                    }
                }
                return $cliente;
            });
            
            // Agregar información sobre el campo único encontrado para debugging
            \Log::info('Búsqueda de clientes', [
                'tipo_cliente_id' => $tipoClienteId,
                'tabla' => $tablaNombre,
                'search_term' => $searchTerm,
                'campo_unico' => $campoUnico ? $campoUnico->nombre_campo : 'No encontrado',
                'resultados' => $clientes->count()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $clientes,
                'campo_unico' => $campoUnico ? $campoUnico->nombre_campo : null,
                'campos_configurados' => $camposConfigurados
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en buscar clientes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar clientes: ' . $e->getMessage()
            ], 500);
        }
    }
}
