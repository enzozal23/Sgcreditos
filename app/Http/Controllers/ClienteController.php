<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Telefono;
use App\Models\Correo;
use App\Models\Direccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
        \Log::info('Método store ejecutándose...');
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
            DB::beginTransaction();
            
            $data = $request->all();
            
            // Asignar empresa_id del usuario autenticado
            if (auth()->check() && auth()->user()->empresa_id) {
                $data['empresa_id'] = auth()->user()->empresa_id;
            }
            
            \Log::info('Data to create: ' . json_encode($data));
            
            $cliente = Cliente::create($data);
            
            // Guardar teléfono en tabla telefonos (siempre se crea porque es requerido)
            \Log::info('Creando teléfono para cliente ID: ' . $cliente->id . ' - Número: ' . $request->telefono);
            $telefono = Telefono::create([
                'cliente_id' => $cliente->id,
                'numero' => $request->telefono,
                'tipo' => 'celular',
                'es_principal' => true,
                'observaciones' => 'Teléfono principal del cliente'
            ]);
            \Log::info('Teléfono creado con ID: ' . $telefono->id);
            
            // Guardar correo en tabla correos (siempre se crea porque es requerido)
            \Log::info('Creando correo para cliente ID: ' . $cliente->id . ' - Email: ' . $request->email);
            $correo = Correo::create([
                'cliente_id' => $cliente->id,
                'email' => $request->email,
                'tipo' => 'personal',
                'es_principal' => true,
                'verificado' => false,
                'observaciones' => 'Correo principal del cliente'
            ]);
            \Log::info('Correo creado con ID: ' . $correo->id);
            
            // Guardar dirección en tabla direcciones
            if ($request->filled('direccion')) {
                Direccion::create([
                    'cliente_id' => $cliente->id,
                    'tipo' => 'casa',
                    'calle' => $request->direccion,
                    'ciudad' => 'Ciudad', // Por defecto, se puede mejorar
                    'pais' => 'Argentina',
                    'es_principal' => true,
                    'observaciones' => 'Dirección principal del cliente'
                ]);
            }
            
            DB::commit();
            
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
            DB::rollBack();
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
            \Log::info('=== CLIENTES POR TIPO STORE DEBUG ===');
            \Log::info('Tipo Cliente ID: ' . $tipoClienteId);
            \Log::info('All request data: ' . json_encode($request->all()));
            
            $tipoCliente = \App\Models\TipoCliente::findOrFail($tipoClienteId);
            $tablaNombre = $tipoCliente->tabla_base;
            
            \Log::info('Tabla nombre: ' . $tablaNombre);
            
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
            
            \Log::info('Campos encontrados: ' . $campos->count());
            
            // Validar solo los campos dinámicos que son requeridos
            $reglas = [];
            foreach ($campos as $campo) {
                if ($campo->requerido) {
                    $reglas[$campo->nombre_campo] = 'required';
                }
            }
            
            // Los campos telefono, correo y direccion son requeridos por defecto
            $reglas['telefono'] = 'required|string|max:20';
            $reglas['correo'] = 'required|email|max:255';
            $reglas['direccion'] = 'nullable|string|max:255';
            
            $request->validate($reglas);
            
            \Log::info('Validación exitosa, procediendo a crear cliente...');
            
            DB::beginTransaction();
            
            // Preparar datos para inserción en tabla dinámica
            $datos = [];
            foreach ($campos as $campo) {
                $datos[$campo->nombre_campo] = $request->input($campo->nombre_campo);
            }
            
            \Log::info('Datos para tabla dinámica: ' . json_encode($datos));
            
            // Insertar en la tabla dinámica PRIMERO para obtener el ID
            $id = \DB::table($tablaNombre)->insertGetId($datos);
            
            \Log::info('Registro insertado en tabla dinámica con ID: ' . $id);
            
            // Usar el ID de la tabla dinámica para las tablas relacionadas
            $clienteId = $id;
            
            // Guardar teléfono en tabla telefonos
            \Log::info('Creando teléfono para cliente ID: ' . $clienteId . ' - Número: ' . $request->telefono);
            $telefono = Telefono::create([
                'cliente_id' => $clienteId,
                'numero' => $request->telefono,
                'tipo' => 'celular',
                'es_principal' => true,
                'observaciones' => 'Teléfono principal del cliente'
            ]);
            \Log::info('Teléfono creado con ID: ' . $telefono->id);
            
            // Guardar correo en tabla correos
            \Log::info('Creando correo para cliente ID: ' . $clienteId . ' - Email: ' . $request->correo);
            $correo = Correo::create([
                'cliente_id' => $clienteId,
                'email' => $request->correo,
                'tipo' => 'personal',
                'es_principal' => true,
                'verificado' => false,
                'observaciones' => 'Correo principal del cliente'
            ]);
            \Log::info('Correo creado con ID: ' . $correo->id);
            
            // Guardar dirección en tabla direcciones (si está presente)
            if ($request->filled('direccion')) {
                \Log::info('Creando dirección para cliente ID: ' . $clienteId);
                $direccion = Direccion::create([
                    'cliente_id' => $clienteId,
                    'tipo' => 'casa',
                    'calle' => $request->direccion,
                    'ciudad' => 'Ciudad', // Por defecto
                    'pais' => 'Argentina',
                    'es_principal' => true,
                    'observaciones' => 'Dirección principal del cliente'
                ]);
                \Log::info('Dirección creada con ID: ' . $direccion->id);
            }
            

            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'cliente_id' => $clienteId,
                'tabla_dinamica_id' => $id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error en clientesPorTipoStore: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            
            // Manejar errores específicos de base de datos
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if (strpos($e->getMessage(), 'clientes_dni_unique') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ya existe un cliente con esos datos. No se pueden crear clientes duplicados.',
                        'error_type' => 'duplicate_dni'
                    ], 422);
                } elseif (strpos($e->getMessage(), 'clientes_email_unique') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ya existe un cliente con ese correo electrónico. No se pueden crear clientes duplicados.',
                        'error_type' => 'duplicate_email'
                    ], 422);
                }
            }
            
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
            
            \Log::info("Reglas de validación:", $reglas);
            
            $request->validate($reglas);
            
            // Preparar datos para actualización (sin cliente_id ya que no existe en la tabla dinámica)
            $datos = [];
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
    
    /**
     * Guardar información de contacto del cliente
     */
    public function guardarContacto(Request $request)
    {
        try {
            \Log::info('=== GUARDAR CONTACTO DEBUG ===');
            \Log::info('Cliente ID: ' . $request->input('cliente_id'));
            \Log::info('Teléfonos: ' . json_encode($request->input('telefonos')));
            \Log::info('Correos: ' . json_encode($request->input('correos')));
            \Log::info('Direcciones: ' . json_encode($request->input('direcciones')));
            
            $clienteId = $request->input('cliente_id');
            $telefonos = $request->input('telefonos', []);
            $correos = $request->input('correos', []);
            $direcciones = $request->input('direcciones', []);
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de cliente es requerido'
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Guardar teléfonos
            foreach ($telefonos as $telefonoData) {
                if (!empty($telefonoData['numero'])) {
                    Telefono::create([
                        'cliente_id' => $clienteId,
                        'numero' => $telefonoData['numero'],
                        'tipo' => $telefonoData['tipo'] ?? 'celular',
                        'es_principal' => $telefonoData['es_principal'] ?? false,
                        'observaciones' => 'Teléfono agregado desde módulo de contacto'
                    ]);
                }
            }
            
            // Guardar correos
            foreach ($correos as $correoData) {
                if (!empty($correoData['email'])) {
                    Correo::create([
                        'cliente_id' => $clienteId,
                        'email' => $correoData['email'],
                        'tipo' => $correoData['tipo'] ?? 'personal',
                        'es_principal' => $correoData['es_principal'] ?? false,
                        'verificado' => false,
                        'observaciones' => 'Correo agregado desde módulo de contacto'
                    ]);
                }
            }
            
            // Guardar direcciones
            foreach ($direcciones as $direccionData) {
                if (!empty($direccionData['calle']) || !empty($direccionData['numero'])) {
                    Direccion::create([
                        'cliente_id' => $clienteId,
                        'tipo' => $direccionData['tipo'] ?? 'domicilio',
                        'calle' => $direccionData['calle'] ?? '',
                        'numero' => $direccionData['numero'] ?? '',
                        'piso' => $direccionData['piso'] ?? '',
                        'departamento' => '',
                        'codigo_postal' => $direccionData['codigo_postal'] ?? '',
                        'ciudad' => $direccionData['ciudad'] ?? '',
                        'provincia' => $direccionData['provincia'] ?? '',
                        'pais' => 'Argentina',
                        'es_principal' => $direccionData['es_principal'] ?? false,
                        'observaciones' => 'Dirección agregada desde módulo de contacto'
                    ]);
                }
            }
            
            DB::commit();
            
            \Log::info('Información de contacto guardada exitosamente');
            
            return response()->json([
                'success' => true,
                'message' => 'Información de contacto guardada correctamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al guardar información de contacto: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la información de contacto: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener información de contacto del cliente
     */
    public function obtenerContactos(Request $request)
    {
        try {
            $clienteId = $request->input('cliente_id');
            
            if (!$clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de cliente es requerido'
                ], 400);
            }
            
            \Log::info('Obteniendo contactos para cliente ID: ' . $clienteId);
            
            // Obtener teléfonos
            $telefonos = Telefono::where('cliente_id', $clienteId)->get();
            
            // Obtener correos
            $correos = Correo::where('cliente_id', $clienteId)->get();
            
            // Obtener direcciones
            $direcciones = Direccion::where('cliente_id', $clienteId)->get();
            
            \Log::info('Contactos encontrados:', [
                'telefonos' => $telefonos->count(),
                'correos' => $correos->count(),
                'direcciones' => $direcciones->count()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'telefonos' => $telefonos,
                    'correos' => $correos,
                    'direcciones' => $direcciones
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al obtener contactos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la información de contacto: ' . $e->getMessage()
            ], 500);
        }
    }
}
