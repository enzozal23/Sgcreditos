@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <!-- Aquí puedes agregar tu imagen de fondo -->
    <!-- <img src="/build/sgc_theme/images/dashboard-bg2.png" alt="sgc" title="sgc"> -->
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
</style>
@endsection
