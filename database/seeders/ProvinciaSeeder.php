<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Provincia;

class ProvinciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen provincias
        if (Provincia::count() > 0) {
            $this->command->info('Las provincias ya han sido importadas. Omitiendo...');
            return;
        }

        $this->command->info('Importando provincias...');

        // Cargar los datos desde el archivo generado
        $provinciasData = require database_path('seeders/provincias_data.php');

        $bar = $this->command->getOutput()->createProgressBar(count($provinciasData));
        $bar->start();

        // Insertar en lotes para mejor rendimiento
        $chunks = array_chunk($provinciasData, 10);

        foreach ($chunks as $chunk) {
            Provincia::insert($chunk);
            $bar->advance(count($chunk));
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Provincias importadas exitosamente: ' . count($provinciasData) . ' registros');
    }
}
