<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Verificando estructura de la tabla credito_creditos_pyme:\n";
echo "=====================================================\n";

if (Schema::hasTable('credito_creditos_pyme')) {
    $columns = DB::select("DESCRIBE credito_creditos_pyme");
    
    foreach ($columns as $column) {
        echo sprintf("%-20s %-15s %-10s %-10s %-10s %-10s\n", 
            $column->Field, 
            $column->Type, 
            $column->Null, 
            $column->Key, 
            $column->Default, 
            $column->Extra
        );
    }
} else {
    echo "La tabla credito_creditos_pyme no existe.\n";
}
