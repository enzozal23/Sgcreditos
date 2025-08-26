<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TipoCredito;
use Illuminate\Support\Facades\Schema;

class ActualizarTablasCreditos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'creditos:actualizar-tablas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza las tablas de créditos existentes con las columnas de los campos personalizados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando actualización de tablas de créditos...');

        $tiposCredito = TipoCredito::all();
        
        if ($tiposCredito->isEmpty()) {
            $this->warn('No se encontraron tipos de crédito para actualizar.');
            return;
        }

        $bar = $this->output->createProgressBar($tiposCredito->count());
        $bar->start();

        foreach ($tiposCredito as $tipoCredito) {
            try {
                $this->line("\nActualizando tabla: {$tipoCredito->tabla_credito}");
                
                // Verificar si la tabla existe
                if (!Schema::hasTable($tipoCredito->tabla_credito)) {
                    $this->warn("La tabla {$tipoCredito->tabla_credito} no existe. Creando...");
                    $tipoCredito->crearTablaCredito();
                }
                
                // Actualizar la tabla con los campos personalizados
                $tipoCredito->actualizarTablaCredito();
                
                $this->info("✓ Tabla {$tipoCredito->tabla_credito} actualizada correctamente");
                
            } catch (\Exception $e) {
                $this->error("✗ Error al actualizar tabla {$tipoCredito->tabla_credito}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Actualización de tablas completada.');
    }
}
