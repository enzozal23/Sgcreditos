<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Variable;

class VariablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $variables = [
            [
                'nombre' => 'copa_background_login_custom',
                'valor' => '0',
                'descripcion' => 'Indica si usar fondo personalizado en login'
            ],
            [
                'nombre' => 'background_login_custom_path',
                'valor' => null,
                'descripcion' => 'Ruta del fondo personalizado para login'
            ],
            [
                'nombre' => 'copa_background_home_custom',
                'valor' => '0',
                'descripcion' => 'Indica si usar fondo personalizado en home'
            ],
            [
                'nombre' => 'background_home_custom_path',
                'valor' => null,
                'descripcion' => 'Ruta del fondo personalizado para home'
            ],
            [
                'nombre' => 'reset_password_30_dias',
                'valor' => '0',
                'descripcion' => 'Indica si forzar cambio de contraseña cada 30 días'
            ]
        ];

        foreach ($variables as $variable) {
            Variable::updateOrCreate(
                ['nombre' => $variable['nombre']],
                $variable
            );
        }
    }
}
