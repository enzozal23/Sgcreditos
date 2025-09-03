<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $empresas = Empresa::orderBy('nombre')->paginate(10);
        return view('empresas.index', compact('empresas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('empresas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'cuit' => 'required|string|unique:empresas,cuit|max:20',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Empresa::create($request->all());

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Empresa $empresa)
    {
        return view('empresas.show', compact('empresa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empresa $empresa)
    {
        return view('empresas.edit', compact('empresa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empresa $empresa)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'cuit' => 'required|string|unique:empresas,cuit,' . $empresa->id . '|max:20',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $empresa->update($request->all());

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empresa $empresa)
    {
        // Verificar que no haya usuarios, clientes o créditos asociados
        if ($empresa->users()->count() > 0 || 
            $empresa->clientes()->count() > 0 || 
            $empresa->creditos()->count() > 0) {
            return redirect()->route('empresas.index')
                ->with('error', 'No se puede eliminar la empresa porque tiene datos asociados.');
        }

        $empresa->delete();

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa eliminada exitosamente.');
    }

    /**
     * Cambiar el estado activo/inactivo de la empresa
     */
    public function toggleEstado(Empresa $empresa)
    {
        $empresa->update(['activo' => !$empresa->activo]);
        
        $estado = $empresa->activo ? 'activada' : 'desactivada';
        
        return redirect()->route('empresas.index')
            ->with('success', "Empresa {$estado} exitosamente.");
    }
}
