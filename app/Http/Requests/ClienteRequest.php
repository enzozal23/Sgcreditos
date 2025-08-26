<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteRequest extends FormRequest
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
        $id = $this->route('id');
        
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.()\[\]]+$/',
                Rule::unique('clientes')->ignore($id),
            ],
            'identificador' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.()\[\]_\-+#@!$%&*]+$/',
                Rule::unique('clientes')->ignore($id),
            ],
        ];
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nombre.regex' => 'El nombre solo puede contener letras, números, espacios, paréntesis y algunos caracteres especiales.',
            'nombre.unique' => 'Este nombre de cliente ya está en uso.',
            'identificador.regex' => 'El identificador puede contener letras, números, espacios y caracteres especiales.',
            'identificador.unique' => 'Este identificador ya está en uso.',
        ];
    }
}
