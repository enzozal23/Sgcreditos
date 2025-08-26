<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use Illuminate\Http\Request;

class ProvinciaController extends Controller
{
    /**
     * Obtener todas las provincias
     */
    public function index()
    {
        $provincias = Provincia::ordenadas()
            ->get(['id', 'codigo_provincia', 'nombre']);

        return response()->json(['provincias' => $provincias]);
    }

    /**
     * Buscar provincias por nombre
     */
    public function buscar(Request $request)
    {
        $nombre = $request->get('nombre');
        
        if (!$nombre || strlen($nombre) < 2) {
            return response()->json(['error' => 'Nombre debe tener al menos 2 caracteres'], 400);
        }

        $provincias = Provincia::buscarPorNombre($nombre)
            ->ordenadas()
            ->limit(10)
            ->get(['id', 'codigo_provincia', 'nombre']);

        return response()->json(['provincias' => $provincias]);
    }

    /**
     * Obtener provincia por ID
     */
    public function show($id)
    {
        $provincia = Provincia::find($id);
        
        if (!$provincia) {
            return response()->json(['error' => 'Provincia no encontrada'], 404);
        }

        return response()->json(['provincia' => $provincia]);
    }

    /**
     * Obtener provincia por código
     */
    public function porCodigo(Request $request)
    {
        $codigo = $request->get('codigo');
        
        if (!$codigo) {
            return response()->json(['error' => 'Código de provincia requerido'], 400);
        }

        $provincia = Provincia::porCodigo($codigo)->first();

        if (!$provincia) {
            return response()->json(['error' => 'Provincia no encontrada'], 404);
        }

        return response()->json(['provincia' => $provincia]);
    }

    /**
     * Obtener provincia con sus localidades
     */
    public function conLocalidades($id)
    {
        $provincia = Provincia::with('localidades')->find($id);
        
        if (!$provincia) {
            return response()->json(['error' => 'Provincia no encontrada'], 404);
        }

        return response()->json(['provincia' => $provincia]);
    }
}
