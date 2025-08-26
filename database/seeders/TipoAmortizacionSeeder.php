<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoAmortizacion;

class TipoAmortizacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposAmortizacion = [
            [
                'nombre' => 'Francesa',
                'descripcion' => 'Sistema de amortización donde la cuota es constante. Los intereses se calculan sobre el saldo pendiente y la amortización aumenta con el tiempo.',
                'formula' => 'Principal * (i * (1 + i)^n) / ((1 + i)^n - 1)',
                'estado' => true
            ],
            [
                'nombre' => 'Alemana',
                'descripcion' => 'Sistema de amortización donde la amortización del capital es constante. Los intereses disminuyen y las cuotas son decrecientes.',
                'formula' => '(Saldo * i) + (Principal / n)',
                'estado' => true
            ],
            [
                'nombre' => 'Americana',
                'descripcion' => 'Sistema de amortización donde solo se pagan intereses durante el plazo del préstamo y el capital se paga al final en una sola cuota.',
                'formula' => 'Principal * i',
                'estado' => true
            ]
        ];

        foreach ($tiposAmortizacion as $tipo) {
            TipoAmortizacion::firstOrCreate(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }
    }
}
