<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Empresa;

class AsignarEmpresaUsuarios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usuarios:asignar-empresa {empresa_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna una empresa a todos los usuarios que no tengan empresa asignada';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $empresaId = $this->argument('empresa_id');
        
        if (!$empresaId) {
            // Si no se especifica empresa, usar la primera disponible
            $empresa = Empresa::first();
            if (!$empresa) {
                $this->error('No hay empresas disponibles. Ejecute primero el EmpresaSeeder.');
                return 1;
            }
            $empresaId = $empresa->id;
            $this->info("Usando empresa: {$empresa->nombre} (ID: {$empresaId})");
        } else {
            // Verificar que la empresa existe
            $empresa = Empresa::find($empresaId);
            if (!$empresa) {
                $this->error("La empresa con ID {$empresaId} no existe.");
                return 1;
            }
            $this->info("Usando empresa: {$empresa->nombre}");
        }

        // Contar usuarios sin empresa
        $usuariosSinEmpresa = User::whereNull('empresa_id')->count();
        
        if ($usuariosSinEmpresa === 0) {
            $this->info('Todos los usuarios ya tienen empresa asignada.');
            return 0;
        }

        $this->info("Asignando empresa a {$usuariosSinEmpresa} usuarios...");

        // Asignar empresa a usuarios sin empresa
        $actualizados = User::whereNull('empresa_id')->update(['empresa_id' => $empresaId]);

        $this->info("Se actualizaron {$actualizados} usuarios exitosamente.");
        
        return 0;
    }
}
