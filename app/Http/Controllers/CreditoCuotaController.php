<?php

namespace App\Http\Controllers;

use App\Models\Credito;
use App\Models\CreditoCuota;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CreditoCuotaController extends Controller
{
    /**
     * Mostrar la vista de configuración de cuotas
     */
    public function index($credito_id)
    {
        $credito = Credito::findOrFail($credito_id);
        return view('credito-cuotas.index', compact('credito'));
    }

    /**
     * Obtener datos de cuotas para DataTable
     */
    public function getData($credito_id): JsonResponse
    {
        $credito = Credito::findOrFail($credito_id);
        $cuotas = $credito->cuotas;
        
        $data = $cuotas->map(function ($cuota) {
            return [
                'id' => $cuota->id,
                'numero_cuota' => $cuota->numero_cuota,
                'tasa' => $cuota->tasa,
                'tasa_porcentaje' => $cuota->tasa_porcentaje,
                'acciones' => $this->generarBotonesAcciones($cuota->id)
            ];
        });
        
        return response()->json(['data' => $data]);
    }

    /**
     * Crear nueva cuota
     */
    public function store(Request $request, $credito_id): JsonResponse
    {
        $credito = Credito::findOrFail($credito_id);
        
        $validator = Validator::make($request->all(), [
            'numero_cuota' => 'required|integer|min:1|max:100',
            'tasa_porcentaje' => 'required|numeric|min:0|max:100'
        ], [
            'numero_cuota.required' => 'El número de cuota es obligatorio',
            'numero_cuota.integer' => 'El número de cuota debe ser un número entero',
            'numero_cuota.min' => 'El número de cuota debe ser mayor a 0',
            'numero_cuota.max' => 'El número de cuota no puede ser mayor a 100',
            'tasa_porcentaje.required' => 'La tasa de interés es obligatoria',
            'tasa_porcentaje.numeric' => 'La tasa debe ser un número',
            'tasa_porcentaje.min' => 'La tasa no puede ser menor a 0',
            'tasa_porcentaje.max' => 'La tasa no puede ser mayor a 100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cuota = CreditoCuota::create([
                'credito_id' => $credito_id,
                'numero_cuota' => $request->numero_cuota,
                'tasa' => $request->tasa_porcentaje // El mutator convertirá automáticamente
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cuota creada exitosamente',
                'data' => $cuota
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la cuota: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener cuota para editar
     */
    public function edit($credito_id, $cuota_id): JsonResponse
    {
        $cuota = CreditoCuota::where('credito_id', $credito_id)
                            ->where('id', $cuota_id)
                            ->first();
        
        if (!$cuota) {
            return response()->json([
                'success' => false,
                'message' => 'Cuota no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $cuota
        ]);
    }

    /**
     * Actualizar cuota
     */
    public function update(Request $request, $credito_id, $cuota_id): JsonResponse
    {
        $cuota = CreditoCuota::where('credito_id', $credito_id)
                            ->where('id', $cuota_id)
                            ->first();
        
        if (!$cuota) {
            return response()->json([
                'success' => false,
                'message' => 'Cuota no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'numero_cuota' => 'required|integer|min:1|max:100',
            'tasa_porcentaje' => 'required|numeric|min:0|max:100'
        ], [
            'numero_cuota.required' => 'El número de cuota es obligatorio',
            'numero_cuota.integer' => 'El número de cuota debe ser un número entero',
            'numero_cuota.min' => 'El número de cuota debe ser mayor a 0',
            'numero_cuota.max' => 'El número de cuota no puede ser mayor a 100',
            'tasa_porcentaje.required' => 'La tasa de interés es obligatoria',
            'tasa_porcentaje.numeric' => 'La tasa debe ser un número',
            'tasa_porcentaje.min' => 'La tasa no puede ser menor a 0',
            'tasa_porcentaje.max' => 'La tasa no puede ser mayor a 100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cuota->update([
                'numero_cuota' => $request->numero_cuota,
                'tasa' => $request->tasa_porcentaje
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cuota actualizada exitosamente',
                'data' => $cuota
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la cuota: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar cuota
     */
    public function destroy($credito_id, $cuota_id): JsonResponse
    {
        $cuota = CreditoCuota::where('credito_id', $credito_id)
                            ->where('id', $cuota_id)
                            ->first();
        
        if (!$cuota) {
            return response()->json([
                'success' => false,
                'message' => 'Cuota no encontrada'
            ], 404);
        }

        try {
            $cuota->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cuota eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la cuota: ' . $e->getMessage()
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
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarCuota(' . $id . ')" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarCuota(' . $id . ')" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }
}
