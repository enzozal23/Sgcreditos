<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'dni' => '12345678',
                'email' => 'juan.perez@email.com',
                'telefono' => '011-1234-5678',
                'direccion' => 'Av. Corrientes 1234',
                'ciudad' => 'Buenos Aires',
                'provincia' => 'Buenos Aires',
                'codigo_postal' => '1043',
                'fecha_nacimiento' => '1985-03-15',
                'estado' => 'activo',
                'observaciones' => 'Cliente frecuente'
            ],
            [
                'nombre' => 'María',
                'apellido' => 'González',
                'dni' => '23456789',
                'email' => 'maria.gonzalez@email.com',
                'telefono' => '011-2345-6789',
                'direccion' => 'Calle Florida 567',
                'ciudad' => 'Buenos Aires',
                'provincia' => 'Buenos Aires',
                'codigo_postal' => '1005',
                'fecha_nacimiento' => '1990-07-22',
                'estado' => 'activo',
                'observaciones' => 'Nuevo cliente'
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'López',
                'dni' => '34567890',
                'email' => 'carlos.lopez@email.com',
                'telefono' => '011-3456-7890',
                'direccion' => 'Av. Santa Fe 890',
                'ciudad' => 'Buenos Aires',
                'provincia' => 'Buenos Aires',
                'codigo_postal' => '1059',
                'fecha_nacimiento' => '1982-11-08',
                'estado' => 'pendiente',
                'observaciones' => 'Pendiente de documentación'
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Martínez',
                'dni' => '45678901',
                'email' => 'ana.martinez@email.com',
                'telefono' => '011-4567-8901',
                'direccion' => 'Calle Lavalle 234',
                'ciudad' => 'Buenos Aires',
                'provincia' => 'Buenos Aires',
                'codigo_postal' => '1047',
                'fecha_nacimiento' => '1988-05-12',
                'estado' => 'inactivo',
                'observaciones' => 'Cliente inactivo por falta de pago'
            ],
            [
                'nombre' => 'Roberto',
                'apellido' => 'Fernández',
                'dni' => '56789012',
                'email' => 'roberto.fernandez@email.com',
                'telefono' => '011-5678-9012',
                'direccion' => 'Av. Córdoba 456',
                'ciudad' => 'Buenos Aires',
                'provincia' => 'Buenos Aires',
                'codigo_postal' => '1054',
                'fecha_nacimiento' => '1975-09-30',
                'estado' => 'activo',
                'observaciones' => 'Cliente VIP'
            ]
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
