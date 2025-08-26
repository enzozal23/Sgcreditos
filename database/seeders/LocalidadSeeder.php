<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Localidad;

class LocalidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Limpiando tabla de localidades...');
        
        // Limpiar la tabla antes de insertar
        Localidad::truncate();
        
        $this->command->info('Importando localidades...');

        // Cargar los datos desde el archivo generado
        $localidadesData = require database_path('seeders/localidades_data.php');

        $bar = $this->command->getOutput()->createProgressBar(count($localidadesData));
        $bar->start();

        // Insertar en lotes para mejor rendimiento
        $chunks = array_chunk($localidadesData, 100);

        foreach ($chunks as $chunk) {
            Localidad::insert($chunk);
            $bar->advance(count($chunk));
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Localidades importadas exitosamente: ' . count($localidadesData) . ' registros');
    }
}
