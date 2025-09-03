<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificarEmpresa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Verificar si el usuario tiene una empresa asignada
        if (!$user->empresa_id) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Su cuenta no tiene una empresa asignada. Contacte al administrador.'
            ]);
        }

        // Agregar la empresa_id a la request para uso posterior
        $request->merge(['empresa_id' => $user->empresa_id]);
        
        return $next($request);
    }
}
