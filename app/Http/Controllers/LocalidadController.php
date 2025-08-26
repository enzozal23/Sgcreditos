<?php

namespace App\Http\Controllers;

use App\Models\Localidad;
use App\Models\Provincia;
use Illuminate\Http\Request;

class LocalidadController extends Controller
{
    /**
     * Obtener localidades por provincia
     */
    public function porProvincia(Request $request)
    {
        $provinciaId = $request->get('provincia_id');
        
        if (!$provinciaId) {
            return response()->json(['error' => 'ID de provincia requerido'], 400);
        }

        $localidades = Localidad::porProvincia($provinciaId)
            ->ordenadas()
            ->get(['codigo_localidad', 'nombre', 'codigo_postal', 'provincia_id']);

        return response()->json(['localidades' => $localidades]);
    }

    /**
     * Buscar localidades por nombre
     */
    public function buscar(Request $request)
    {
        $nombre = $request->get('nombre');
        
        if (!$nombre || strlen($nombre) < 2) {
            return response()->json(['error' => 'Nombre debe tener al menos 2 caracteres'], 400);
        }

        $localidades = Localidad::buscarPorNombre($nombre)
            ->ordenadas()
            ->limit(20)
            ->get(['id', 'nombre', 'provincia_id', 'codigo_postal']);

        return response()->json(['localidades' => $localidades]);
    }

    /**
     * Obtener localidad por ID
     */
    public function show($id)
    {
        $localidad = Localidad::find($id);
        
        if (!$localidad) {
            return response()->json(['error' => 'Localidad no encontrada'], 404);
        }

        return response()->json(['localidad' => $localidad]);
    }

    /**
     * Obtener localidades por código postal
     */
    public function porCodigoPostal(Request $request)
    {
        $codigoPostal = $request->get('codigo_postal');
        
        if (!$codigoPostal) {
            return response()->json(['error' => 'Código postal requerido'], 400);
        }

        $localidades = Localidad::porCodigoPostal($codigoPostal)
            ->ordenadas()
            ->get(['id', 'nombre', 'provincia_id']);

        return response()->json(['localidades' => $localidades]);
    }

    /**
     * Obtener todas las provincias disponibles
     */
    public function provincias()
    {
        $provincias = Provincia::ordenadas()
            ->get(['codigo_provincia', 'nombre']);

        return response()->json(['provincias' => $provincias]);
    }
}
