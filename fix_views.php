<?php

// Script para corregir todas las vistas que usan x-app-layout
$viewsDir = __DIR__ . '/resources/views';
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsDir)
);

$fixedCount = 0;
$totalFiles = 0;

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'blade.php') {
        $totalFiles++;
        $content = file_get_contents($file->getPathname());
        
        // Verificar si usa x-app-layout
        if (strpos($content, '<x-app-layout>') !== false) {
            echo "Corrigiendo: " . $file->getPathname() . "\n";
            
            // Reemplazar x-app-layout con @extends
            $content = str_replace('<x-app-layout>', '@extends(\'layouts.app\')' . "\n\n@section('content')", $content);
            $content = str_replace('</x-app-layout>', '@endsection', $content);
            
            // Remover x-slot si existe
            $content = preg_replace('/<x-slot name="title">.*?<\/x-slot>\s*/s', '', $content);
            
            // Guardar el archivo corregido
            file_put_contents($file->getPathname(), $content);
            $fixedCount++;
        }
    }
}

echo "\n=== RESUMEN ===\n";
echo "Total de archivos revisados: $totalFiles\n";
echo "Archivos corregidos: $fixedCount\n";
echo "Script completado.\n";
