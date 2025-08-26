<?php

namespace App;

use App\Models\LogAccion;

trait Loggable
{
    /**
     * Registrar una acción en el log
     */
    protected function logAccion($accion, $modulo, $descripcion = null, $datos = [])
    {
        try {
            LogAccion::registrar($accion, $modulo, $descripcion, $datos);
        } catch (\Exception $e) {
            // Si falla el log, no interrumpir la operación principal
            \Log::error('Error al registrar log de acción: ' . $e->getMessage());
        }
    }

    /**
     * Registrar creación de un registro
     */
    protected function logCrear($modulo, $entidad, $entidad_id, $datos_nuevos = null)
    {
        $this->logAccion('CREATE', $modulo, "Creación de {$entidad}", [
            'entidad' => $entidad,
            'entidad_id' => $entidad_id,
            'datos_nuevos' => $datos_nuevos
        ]);
    }

    /**
     * Registrar actualización de un registro
     */
    protected function logActualizar($modulo, $entidad, $entidad_id, $datos_anteriores = null, $datos_nuevos = null)
    {
        $this->logAccion('UPDATE', $modulo, "Actualización de {$entidad}", [
            'entidad' => $entidad,
            'entidad_id' => $entidad_id,
            'datos_anteriores' => $datos_anteriores,
            'datos_nuevos' => $datos_nuevos
        ]);
    }

    /**
     * Registrar eliminación de un registro
     */
    protected function logEliminar($modulo, $entidad, $entidad_id, $datos_anteriores = null)
    {
        $this->logAccion('DELETE', $modulo, "Eliminación de {$entidad}", [
            'entidad' => $entidad,
            'entidad_id' => $entidad_id,
            'datos_anteriores' => $datos_anteriores
        ]);
    }

    /**
     * Registrar acceso a un módulo
     */
    protected function logAcceso($modulo, $accion = 'ACCESS', $descripcion = null)
    {
        $this->logAccion($accion, $modulo, $descripcion);
    }

    /**
     * Registrar error
     */
    protected function logError($modulo, $descripcion, $datos = [])
    {
        $this->logAccion('ERROR', $modulo, $descripcion, array_merge($datos, ['nivel' => 'error']));
    }

    /**
     * Registrar advertencia
     */
    protected function logWarning($modulo, $descripcion, $datos = [])
    {
        $this->logAccion('WARNING', $modulo, $descripcion, array_merge($datos, ['nivel' => 'warning']));
    }
}
