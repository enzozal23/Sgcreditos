<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LocalidadController;
use App\Http\Controllers\ProvinciaController;

Route::get('/', function () {
    return view('welcome');
});

// Nueva ruta personalizada
Route::get('/bienvenido', function () {
    return view('bienvenido');
});

// Ruta del dashboard (página principal después del login)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Rutas de Administración
Route::middleware('auth')->group(function () {
    Route::get('/admin/monitoreo', function () {
        return view('admin.monitoreo');
    })->name('admin.monitoreo');

    Route::get('/admin/permisos', function () {
        return view('admin.permisos');
    })->name('admin.permisos');

    Route::get('/admin/roles', function () {
        return view('admin.roles');
    })->name('admin.roles');

    Route::get('/admin/usuarios', function () {
        return view('admin.usuarios');
    })->name('admin.usuarios');
});

    // Rutas de Clientes
    Route::middleware('auth')->group(function () {
        // Ruta para obtener datos de DataTable (DEBE IR ANTES del resource)
        Route::get('/clientes/data', [ClienteController::class, 'getData'])->name('clientes.data');
    Route::get('/clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
        
        // Rutas para CRUD de clientes por tipo
        Route::get('/clientes/tipo/{tipoClienteId}', [ClienteController::class, 'clientesPorTipo'])->name('clientes.por.tipo');
        Route::get('/clientes/tipo/{tipoClienteId}/data', [ClienteController::class, 'clientesPorTipoData'])->name('clientes.por.tipo.data');
        Route::get('/clientes/tipo/{tipoClienteId}/campos', [ClienteController::class, 'clientesPorTipoCampos'])->name('clientes.por.tipo.campos');
        Route::post('/clientes/tipo/{tipoClienteId}', [ClienteController::class, 'clientesPorTipoStore'])->name('clientes.por.tipo.store');
        Route::get('/clientes/tipo/{tipoClienteId}/{id}/edit', [ClienteController::class, 'clientesPorTipoEdit'])->name('clientes.por.tipo.edit');
        Route::put('/clientes/tipo/{tipoClienteId}/{id}', [ClienteController::class, 'clientesPorTipoUpdate'])->name('clientes.por.tipo.update');
        Route::delete('/clientes/tipo/{tipoClienteId}/{id}', [ClienteController::class, 'clientesPorTipoDestroy'])->name('clientes.por.tipo.destroy');
        
        // Rutas de Clientes
        Route::resource('clientes', ClienteController::class)->names([
            'index' => 'clientes.index',
            'create' => 'clientes.create',
            'store' => 'clientes.store',
            'show' => 'clientes.show',
            'edit' => 'clientes.edit',
            'update' => 'clientes.update',
            'destroy' => 'clientes.destroy',
        ]);
    });

// Rutas de Créditos
Route::middleware('auth')->group(function () {
    // Ruta para el listado de créditos
    Route::get('/creditos', function () {
        return view('creditos.index');
    })->name('creditos.index');
    
    // Rutas para tipos de créditos
    Route::get('/tipos-creditos', [App\Http\Controllers\TipoCreditoController::class, 'index'])->name('tipos.creditos');
    Route::get('/tipos-creditos/data', [App\Http\Controllers\TipoCreditoController::class, 'getData'])->name('tipos.creditos.data');
    Route::get('/tipos-creditos/data-creditos', [App\Http\Controllers\TipoCreditoController::class, 'getDataCreditos'])->name('tipos.creditos.data.creditos');
    Route::post('/tipos-creditos', [App\Http\Controllers\TipoCreditoController::class, 'store'])->name('tipos.creditos.store');
    Route::get('/tipos-creditos/{id}/edit', [App\Http\Controllers\TipoCreditoController::class, 'edit'])->name('tipos.creditos.edit');
    Route::put('/tipos-creditos/{id}', [App\Http\Controllers\TipoCreditoController::class, 'update'])->name('tipos.creditos.update');
    Route::delete('/tipos-creditos/{id}', [App\Http\Controllers\TipoCreditoController::class, 'destroy'])->name('tipos.creditos.destroy');
    
    // Rutas para tipos de amortización
    Route::get('/tipos-amortizacion', [App\Http\Controllers\TipoAmortizacionController::class, 'index'])->name('tipos.amortizacion');
    Route::get('/tipos-amortizacion/data', [App\Http\Controllers\TipoAmortizacionController::class, 'getData'])->name('tipos.amortizacion.data');
    Route::post('/tipos-amortizacion', [App\Http\Controllers\TipoAmortizacionController::class, 'store'])->name('tipos.amortizacion.store');
    Route::get('/tipos-amortizacion/{id}/edit', [App\Http\Controllers\TipoAmortizacionController::class, 'edit'])->name('tipos.amortizacion.edit');
    Route::put('/tipos-amortizacion/{id}', [App\Http\Controllers\TipoAmortizacionController::class, 'update'])->name('tipos.amortizacion.update');
    Route::delete('/tipos-amortizacion/{id}', [App\Http\Controllers\TipoAmortizacionController::class, 'destroy'])->name('tipos.amortizacion.destroy');
    
    // Rutas para campos de tipos de créditos
    Route::get('/tipos-creditos/{id}/campos', [App\Http\Controllers\TipoCreditoController::class, 'campos'])->name('tipos.creditos.campos');
    Route::get('/tipos-creditos/{id}/campos/data', [App\Http\Controllers\TipoCreditoController::class, 'camposData'])->name('tipos.creditos.campos.data');
    Route::post('/tipos-creditos/{id}/campos', [App\Http\Controllers\TipoCreditoController::class, 'camposStore'])->name('tipos.creditos.campos.store');
    Route::get('/tipos-creditos/{tipo_id}/campos/{campo_id}/edit', [App\Http\Controllers\TipoCreditoController::class, 'camposEdit'])->name('tipos.creditos.campos.edit');
    Route::put('/tipos-creditos/{tipo_id}/campos/{campo_id}', [App\Http\Controllers\TipoCreditoController::class, 'camposUpdate'])->name('tipos.creditos.campos.update');
    Route::delete('/tipos-creditos/{tipo_id}/campos/{campo_id}', [App\Http\Controllers\TipoCreditoController::class, 'camposDestroy'])->name('tipos.creditos.campos.destroy');
    
    // Rutas para créditos de tipos específicos
    Route::get('/tipos-creditos/{id}/creditos', [App\Http\Controllers\TipoCreditoController::class, 'creditosListado'])->name('tipos.creditos.creditos.listado');
    Route::get('/tipos-creditos/{id}/creditos/data', [App\Http\Controllers\TipoCreditoController::class, 'creditosData'])->name('tipos.creditos.creditos.data');
    Route::get('/tipos-creditos/{id}/creditos/crear', [App\Http\Controllers\TipoCreditoController::class, 'creditosCrear'])->name('tipos.creditos.creditos.crear');
    Route::get('/tipos-creditos/{id}/cuotas', [App\Http\Controllers\TipoCreditoController::class, 'obtenerCuotas'])->name('tipos.creditos.cuotas');
    Route::post('/tipos-creditos/{id}/creditos', [App\Http\Controllers\TipoCreditoController::class, 'creditosStore'])->name('tipos.creditos.creditos.store');
    Route::get('/tipos-creditos/{tipoCreditoId}/creditos/{creditoId}/plan-pago', [App\Http\Controllers\TipoCreditoController::class, 'obtenerPlanPago'])->name('tipos.creditos.creditos.plan.pago');
    
    // Rutas para cuotas de créditos
    Route::get('/creditos/{credito_id}/cuotas', [App\Http\Controllers\CreditoCuotaController::class, 'index'])->name('creditos.cuotas');
    Route::get('/creditos/{credito_id}/cuotas/data', [App\Http\Controllers\CreditoCuotaController::class, 'getData']);
    Route::post('/creditos/{credito_id}/cuotas', [App\Http\Controllers\CreditoCuotaController::class, 'store']);
    Route::get('/creditos/{credito_id}/cuotas/{cuota_id}/edit', [App\Http\Controllers\CreditoCuotaController::class, 'edit']);
    Route::put('/creditos/{credito_id}/cuotas/{cuota_id}', [App\Http\Controllers\CreditoCuotaController::class, 'update']);
    Route::delete('/creditos/{credito_id}/cuotas/{cuota_id}', [App\Http\Controllers\CreditoCuotaController::class, 'destroy']);
    
    // Rutas para tipos de clientes
    Route::get('/tipos-clientes', [App\Http\Controllers\TipoClienteController::class, 'index'])->name('tipos.clientes');
    Route::get('/tipos-clientes/data', [App\Http\Controllers\TipoClienteController::class, 'data'])->name('tipos.clientes.data');
    Route::post('/tipos-clientes', [App\Http\Controllers\TipoClienteController::class, 'store'])->name('tipos.clientes.store');
    Route::get('/tipos-clientes/{id}/edit', [App\Http\Controllers\TipoClienteController::class, 'edit'])->name('tipos.clientes.edit');
    Route::put('/tipos-clientes/{id}', [App\Http\Controllers\TipoClienteController::class, 'update'])->name('tipos.clientes.update');
    Route::delete('/tipos-clientes/{id}', [App\Http\Controllers\TipoClienteController::class, 'destroy'])->name('tipos.clientes.destroy');
    
    // Rutas para campos de tipos de clientes
    Route::get('/tipos-clientes/{id}/campos', [App\Http\Controllers\TipoClienteController::class, 'campos'])->name('tipos.clientes.campos');
    Route::get('/tipos-clientes/{id}/campos/data', [App\Http\Controllers\TipoClienteController::class, 'camposData'])->name('tipos.clientes.campos.data');
    Route::post('/tipos-clientes/{id}/campos', [App\Http\Controllers\TipoClienteController::class, 'camposStore'])->name('tipos.clientes.campos.store');
    Route::get('/tipos-clientes/{tipo_id}/campos/{campo_id}/edit', [App\Http\Controllers\TipoClienteController::class, 'camposEdit'])->name('tipos.clientes.campos.edit');
    Route::put('/tipos-clientes/{tipo_id}/campos/{campo_id}', [App\Http\Controllers\TipoClienteController::class, 'camposUpdate'])->name('tipos.clientes.campos.update');
    Route::delete('/tipos-clientes/{tipo_id}/campos/{campo_id}', [App\Http\Controllers\TipoClienteController::class, 'camposDestroy'])->name('tipos.clientes.campos.destroy');
});

// Ruta de prueba para DataTable
Route::get('/test-datatable', function () {
    return view('test-datatable');
})->name('test.datatable');

           // Ruta temporal para probar DataTable sin autenticación
           Route::get('/test-clientes-data', [ClienteController::class, 'getData'])->name('test.clientes.data');
           
           // Rutas para provincias
           Route::get('/provincias', [ProvinciaController::class, 'index'])->name('provincias.index');
           Route::get('/provincias/buscar', [ProvinciaController::class, 'buscar'])->name('provincias.buscar');
           Route::get('/provincias/{id}', [ProvinciaController::class, 'show'])->name('provincias.show');
           Route::get('/provincias/por-codigo', [ProvinciaController::class, 'porCodigo'])->name('provincias.porCodigo');
           Route::get('/provincias/{id}/localidades', [ProvinciaController::class, 'conLocalidades'])->name('provincias.conLocalidades');
           
           // Rutas para localidades
           Route::get('/localidades/provincias', [LocalidadController::class, 'provincias'])->name('localidades.provincias');
           Route::get('/localidades/por-provincia', [LocalidadController::class, 'porProvincia'])->name('localidades.porProvincia');
           Route::get('/localidades/buscar', [LocalidadController::class, 'buscar'])->name('localidades.buscar');
           Route::get('/localidades/{id}', [LocalidadController::class, 'show'])->name('localidades.show');
           Route::get('/localidades/por-codigo-postal', [LocalidadController::class, 'porCodigoPostal'])->name('localidades.porCodigoPostal');

// Incluir rutas de autenticación
require __DIR__.'/auth - copia.php';
