<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClienteCampoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'cliente_id' => $this->cliente_id,
            'tipo' => $this->tipo,
            'nombre_campo' => $this->nombre_campo,
            'nombre_mostrar' => $this->nombre_mostrar,
            'orden_listado' => $this->orden_listado,
            'requerido' => $this->requerido,
            'valor_unico' => $this->valor_unico,
            'longitud' => $this->longitud,
            'valores' => $this->valores,
            'status' => 1
        ];
        
        // Si es un campo de tipo número (tipo 2), decodificar los valores para obtener decimales y separador
        if ($this->tipo == '2' && !empty($this->valores)) {
            $valores = json_decode($this->valores, true);
            if ($valores) {
                $data['cantidad_decimales'] = $valores['cantidad_decimales'] ?? 0;
                $data['separador_decimal'] = $valores['separador_decimal'] ?? '.';
            }
        }
        
        // Si es un campo de tipo fecha (tipo 3), decodificar los valores para obtener el formato
        if ($this->tipo == '3' && !empty($this->valores)) {
            $valores = json_decode($this->valores, true);
            if ($valores) {
                $data['formato_fecha'] = $valores['formato_fecha'] ?? '';
            }
        }
        
        return $data;
    }
}
