<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\TipoAmortizacion;
use Illuminate\Support\Facades\Validator;

class TipoAmortizacionController extends Controller
{
    /**
     * Mostrar la vista principal de tipos de amortización
     */
    public function index()
    {
        return view('tipos-amortizacion.index');
    }

    /**
     * Obtener datos para DataTable
     */
    public function getData(): JsonResponse
    {
        try {
            $tiposAmortizacion = TipoAmortizacion::orderBy('created_at', 'desc')->get();

            $data = $tiposAmortizacion->map(function ($tipo) {
                return [
                    'id' => $tipo->id,
                    'nombre' => $tipo->nombre,
                    'descripcion' => $tipo->descripcion ?? '-',
                    'formula' => $tipo->formula ?? '-',
                    'estado' => $tipo->estado,
                    'fecha_creacion' => $tipo->created_at ? $tipo->created_at->format('d/m/Y H:i') : '-',
                    'acciones' => $this->generarBotonesAcciones($tipo->id)
                ];
            });

            return response()->json(['data' => $data]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener tipos de amortización: ' . $e->getMessage());
            return response()->json([
                'data' => [],
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nuevo tipo de amortización
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255|unique:tipos_amortizacion,nombre',
                'descripcion' => 'nullable|string|max:1000',
                'formula' => 'nullable|string|max:1000',
                'estado' => 'required|boolean'
            ], [
                'nombre.required' => 'El nombre es obligatorio',
                'nombre.unique' => 'Ya existe un tipo de amortización con ese nombre',
                'nombre.max' => 'El nombre no puede tener más de 255 caracteres',
                'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres',
                'formula.max' => 'La fórmula no puede tener más de 1000 caracteres',
                'estado.required' => 'El estado es obligatorio',
                'estado.boolean' => 'El estado debe ser activo o inactivo'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $tipoAmortizacion = TipoAmortizacion::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'formula' => $request->formula,
                'estado' => $request->estado
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de amortización creado exitosamente',
                'data' => $tipoAmortizacion
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al crear tipo de amortización: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tipo de amortización: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener tipo de amortización para editar
     */
    public function edit($id): JsonResponse
    {
        try {
            $tipoAmortizacion = TipoAmortizacion::find($id);

            if (!$tipoAmortizacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de amortización no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $tipoAmortizacion
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener tipo de amortización: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el tipo de amortización: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar tipo de amortización
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $tipoAmortizacion = TipoAmortizacion::find($id);

            if (!$tipoAmortizacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de amortización no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255|unique:tipos_amortizacion,nombre,' . $id,
                'descripcion' => 'nullable|string|max:1000',
                'formula' => 'nullable|string|max:1000',
                'estado' => 'required|boolean'
            ], [
                'nombre.required' => 'El nombre es obligatorio',
                'nombre.unique' => 'Ya existe un tipo de amortización con ese nombre',
                'nombre.max' => 'El nombre no puede tener más de 255 caracteres',
                'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres',
                'formula.max' => 'La fórmula no puede tener más de 1000 caracteres',
                'estado.required' => 'El estado es obligatorio',
                'estado.boolean' => 'El estado debe ser activo o inactivo'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $tipoAmortizacion->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'formula' => $request->formula,
                'estado' => $request->estado
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de amortización actualizado exitosamente',
                'data' => $tipoAmortizacion
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al actualizar tipo de amortización: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo de amortización: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar tipo de amortización
     */
    public function destroy($id): JsonResponse
    {
        try {
            $tipoAmortizacion = TipoAmortizacion::find($id);

            if (!$tipoAmortizacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de amortización no encontrado'
                ], 404);
            }

            // Verificar si está siendo usado en algún crédito
            // Aquí puedes agregar la lógica para verificar dependencias

            $tipoAmortizacion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de amortización eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al eliminar tipo de amortización: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tipo de amortización: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar botones de acciones para DataTable
     */
    private function generarBotonesAcciones($id): string
    {
        return '
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarTipoAmortizacion(' . $id . ')" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarTipoAmortizacion(' . $id . ')" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }
}
