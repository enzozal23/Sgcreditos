<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteCampoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('id') ?? $this->input('id');
        $clienteId = $this->input('tipo_cliente_id') ?? $this->input('cliente_id');
        
        $rules = [
            'tipo' => [
                'required',
                'integer',
                'exists:tipos_campos,id',
                function ($attribute, $value, $fail) {
                    if (($this->input('valor_unico') ?? 0) == 1 && !in_array($value, [1, 2])) {
                        $fail('Para campos con valor único solo se permiten tipos Texto o Número.');
                    }
                }
            ],
            'posicion' => 'required|integer|min:1|max:100',
            'tipo_cliente_id' => 'required|integer|exists:clientes,id',
            'orden' => 'required|integer',
            'requerido' => 'required|integer|in:0,1',
            'valores' => 'nullable|string',
            'longitud' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000',
                function ($attribute, $value, $fail) {
                    if ($this->input('tipo') == '2' && $this->has('cantidad_decimales')) {
                        // Si tiene decimales y no es un campo único, la longitud debe ser al menos 4
                        if ($this->input('cantidad_decimales') > 0 && ($this->input('valor_unico') ?? 0) != 1) {
                            if ($value < 4) {
                                $fail('Con decimales, la longitud debe ser al menos 4 (1 dígito + punto decimal + 2 decimales)');
                                return;
                            }
                        }
                    }
                }
            ],
            'valor_unico' => 'required|integer|in:0,1',
            'tipo_entrada' => 'required|in:automatica,manual',
            'nombre_campo' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9_]+$/',
                'max:255',
                Rule::unique('cliente_campo', 'nombre_campo')
                    ->where('tipo_cliente_id', $clienteId)
                    ->ignore($id)
            ],
            'nombre_mostrar' => 'nullable|string|max:500',
        ];
        
        // Reglas específicas para cada tipo de campo
        switch ($this->input('tipo')) {
            case 2: // Número
                $rules['cantidad_decimales'] = 'required|integer|min:0|max:10';
                $rules['separador_decimal'] = 'required_if:cantidad_decimales,>,0|in:.,,';
                break;
            case 3: // Fecha
                $rules['formato_fecha'] = 'required|string';
                break;
        }
        
        return $rules;
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nombre_campo.regex' => 'El nombre del campo solo puede contener letras, números y guiones bajos.',
            'nombre_campo.unique' => 'Ya existe un campo con este nombre para este cliente.',
            'tipo.exists' => 'El tipo de campo seleccionado no es válido.',
            'cantidad_decimales.required' => 'Para campos numéricos, debe especificar la cantidad de decimales.',
            'formato_fecha.required' => 'Para campos de fecha, debe especificar el formato.',
        ];
    }
    
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has('valores') && !is_string($this->valores)) {
            $this->merge([
                'valores' => json_encode($this->valores)
            ]);
        }
        // Si los datos vienen como form_data (array de objetos con name y value)
        if ($this->has('form_data')) {
            $formData = [];
            foreach ($this->input('form_data') as $input) {
                $name = $input['name'] ?? null;
                $value = $input['value'] ?? null;
                
                if (!$name) continue;
                
                if (str_ends_with($name, '[]')) {
                    $key = rtrim($name, '[]');
                    $formData[$key][] = $value;
                } else {
                    $formData[$name] = $value;
                }
            }
            
            // Si hay valores como array, convertirlos a JSON
            if (!empty($formData['valores']) && is_array($formData['valores'])) {
                $formData['valores'] = json_encode($formData['valores']);
            }
            
            $this->merge($formData);
        }
    }
}
