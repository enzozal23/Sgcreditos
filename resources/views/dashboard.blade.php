@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <!-- Aquí puedes agregar tu imagen de fondo -->
    <!-- <img src="/build/sgc_theme/images/dashboard-bg2.png" alt="sgc" title="sgc"> -->
</div>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Clientes</h6>
                                            <h3 class="mb-0">{{ \App\Models\Cliente::count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Créditos</h6>
                                            <h3 class="mb-0">{{ \App\Models\Credito::count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-credit-card fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Tipos de Cliente</h6>
                                            <h3 class="mb-0">{{ \App\Models\TipoCliente::count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-tags fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Tipos de Crédito</h6>
                                            <h3 class="mb-0">{{ \App\Models\TipoCredito::count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-credit-card fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-line me-2"></i>Resumen del Sistema
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">
                                        Bienvenido al Sistema de Gestión de Créditos (SGC). 
                                        Aquí puedes administrar clientes, créditos y configuraciones del sistema.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Imagen de fondo solo para el dashboard */
    .dashboard-container {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('/build/sgc_theme/images/dashboard-bg2.png'); 
        background-size: cover;
        background-position: center; 
        background-repeat: no-repeat;
        z-index: -1;
        opacity: 0.3; /* Hacer la imagen más sutil */
    }
    
    /* Solo afectar al contenedor principal cuando estemos en dashboard */
    .min-h-screen {
        position: relative;
        z-index: 1;
    }
    
    /* Asegurar que el contenido del dashboard esté visible */
    .container {
        position: relative;
        z-index: 1;
    }
    
    /* Asegurar que el footer se mantenga visible */
    footer {
        position: relative;
        z-index: 1;
    }
    
    /* Asegurar que el header y navegación se mantengan visibles */
    header, nav, .navbar {
        position: relative;
        z-index: 2 !important;
    }
    
    /* Estilos para las tarjetas de estadísticas */
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: none;
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
@endsection
