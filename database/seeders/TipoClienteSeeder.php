<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoCliente;

class TipoClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            [
                'nombre' => 'Cliente Regular',
                'identificador' => 'REGULAR',
                'estado' => 'activo'
            ],
            [
                'nombre' => 'Cliente Premium',
                'identificador' => 'PREMIUM',
                'estado' => 'activo'
            ],
            [
                'nombre' => 'Cliente Corporativo',
                'identificador' => 'CORPORATIVO',
                'estado' => 'activo'
            ]
        ];

        foreach ($tipos as $tipo) {
            $tipoCliente = TipoCliente::create($tipo);
            $tipoCliente->crearTablaBase();
        }
    }
}
