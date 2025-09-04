<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Empresa;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear empresa por defecto (solo si no existe)
        Empresa::firstOrCreate(
            ['cuit' => '20-12345678-9'], // Buscar por CUIT único
            [
                'nombre' => 'Empresa Demo',
                'razon_social' => 'Empresa Demo S.A.',
                'cuit' => '20-12345678-9',
                'email' => 'info@empresademo.com',
                'telefono' => '011-1234-5678',
                'direccion' => 'Av. Corrientes 1234',
                'ciudad' => 'Ciudad Autónoma de Buenos Aires',
                'provincia' => 'Buenos Aires',
                'codigo_postal' => '1043',
                'activo' => true,
            ]
        );

        // Crear empresa adicional de ejemplo (solo si no existe)
        Empresa::firstOrCreate(
            ['cuit' => '30-98765432-1'], // Buscar por CUIT único
            [
                'nombre' => 'Empresa Test',
                'razon_social' => 'Empresa Test S.R.L.',
                'cuit' => '30-98765432-1',
                'email' => 'contacto@empresatest.com',
                'telefono' => '011-9876-5432',
                'direccion' => 'Av. Santa Fe 5678',
                'ciudad' => 'Ciudad Autónoma de Buenos Aires',
                'provincia' => 'Buenos Aires',
                'codigo_postal' => '1059',
                'activo' => true,
            ]
        );
    }
}
