<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Verificando Provincias ===\n\n";

$provincias = App\Models\Provincia::all();

echo "Total de provincias: " . $provincias->count() . "\n\n";

echo "Todas las provincias:\n";
foreach ($provincias as $provincia) {
    echo $provincia->codigo_provincia . " - " . $provincia->nombre . "\n";
}

echo "\n=== Verificando Localidades ===\n\n";

$provinciasConLocalidades = App\Models\Localidad::select('provincia_id')
    ->distinct()
    ->pluck('provincia_id')
    ->toArray();

echo "Provincias con localidades: " . count($provinciasConLocalidades) . "\n";
foreach ($provinciasConLocalidades as $provinciaId) {
    echo "Provincia ID: " . $provinciaId . "\n";
    
    $localidadesCount = App\Models\Localidad::where('provincia_id', $provinciaId)->count();
    echo "  - Localidades: " . $localidadesCount . "\n";
}

echo "\n=== Fin ===\n";
