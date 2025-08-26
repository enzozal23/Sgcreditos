<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CampoCredito;
use App\Models\CreditoCuota;

class MigrateExistingCuotaFieldsToCreditoCuotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los campos de tipo 'cuota' que no tienen credito_cuota_id
        $camposCuota = CampoCredito::where('tipo_campo', 'cuota')
            ->whereNull('credito_cuota_id')
            ->get();

        foreach ($camposCuota as $campo) {
            // Buscar la cuota correspondiente por credito_id
            if ($campo->credito_id) {
                $cuota = CreditoCuota::where('credito_id', $campo->credito_id)->first();
                
                if ($cuota) {
                    // Actualizar el campo con el credito_cuota_id
                    $campo->update(['credito_cuota_id' => $cuota->id]);
                    
                    $this->command->info("Campo '{$campo->nombre_campo}' vinculado con cuota ID: {$cuota->id} (Número: {$cuota->numero_cuota}, Tasa: {$cuota->tasa})");
                } else {
                    $this->command->warn("No se encontró cuota para el campo '{$campo->nombre_campo}' con credito_id: {$campo->credito_id}");
                }
            } else {
                $this->command->warn("Campo '{$campo->nombre_campo}' no tiene credito_id asignado");
            }
        }

        $this->command->info('Migración de campos de cuota a credito_cuota_id completada.');
    }
}
