<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CampoCredito;
use App\Models\TipoCredito;
use App\Models\Credito;

class MigrateExistingCuotaFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los campos de tipo 'cuota' que no tienen credito_id
        $camposCuota = CampoCredito::where('tipo_campo', 'cuota')
            ->whereNull('credito_id')
            ->get();

        foreach ($camposCuota as $campo) {
            // Obtener el tipo de crédito
            $tipoCredito = TipoCredito::find($campo->tipo_credito_id);
            
            if ($tipoCredito) {
                // Buscar o crear el crédito correspondiente
                $credito = Credito::firstOrCreate([
                    'nombre' => $tipoCredito->nombre
                ], [
                    'descripcion' => 'Crédito tipo: ' . $tipoCredito->nombre,
                    'activo' => true
                ]);

                // Actualizar el campo con el credito_id
                $campo->update(['credito_id' => $credito->id]);
                
                $this->command->info("Campo '{$campo->nombre_campo}' vinculado con crédito '{$credito->nombre}'");
            }
        }

        $this->command->info('Migración de campos de cuota completada.');
    }
}
