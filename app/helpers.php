<?php

if (!function_exists('getClientIp')) {
    /**
     * Obtener la IP del cliente
     */
    function getClientIp()
    {
        $ipKeys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        // Si no se encuentra una IP válida, devolver la primera disponible
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = trim($_SERVER[$key]);
                if (!empty($ip)) {
                    return $ip;
                }
            }
        }

        return '127.0.0.1'; // IP local por defecto
    }
}

if (!function_exists('LogRegistrar')) {
    /**
     * Registrar acciones CRUD en el log
     */
    function LogRegistrar($mensaje)
    {
        try {
            \App\Models\LogAccion::create([
                'usuario_id' => auth()->id(),
                'accion' => 'CRUD',
                'modulo' => 'sistema',
                'descripcion' => $mensaje,
                'ip_address' => getClientIp(),
                'user_agent' => request()->userAgent(),
                'nivel' => 'info'
            ]);
        } catch (\Exception $e) {
            // Si falla el log, no interrumpir la operación principal
            \Log::error('Error al registrar log: ' . $e->getMessage());
        }
    }
}

if (!function_exists('LogAccess')) {
    /**
     * Registrar accesos (login, logout, register) en el log
     */
    function LogAccess($mensaje, $tipo = 'login')
    {
        try {
            \App\Models\LogAcceso::create([
                'usuario_id' => auth()->id(),
                'email' => auth()->user() ? auth()->user()->email : 'no_autenticado',
                'tipo' => $tipo,
                'ip_address' => getClientIp(),
                'user_agent' => request()->userAgent(),
                'detalles' => $mensaje,
                'estado' => 'exitoso',
                'fecha_acceso' => now()
            ]);
        } catch (\Exception $e) {
            // Si falla el log, no interrumpir la operación principal
            \Log::error('Error al registrar log de acceso: ' . $e->getMessage());
        }
    }
}
