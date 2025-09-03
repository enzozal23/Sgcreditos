<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-cube me-2"></i>SGC
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <!-- <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a> -->
                </li>
                
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cogs me-1"></i>Administración
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.monitoreo') }}"><i class="fas fa-chart-line me-2"></i>Monitoreo</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.permisos.index') }}"><i class="fas fa-key me-2"></i>Permisos</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.roles.index') }}"><i class="fas fa-users-cog me-2"></i>Roles</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.usuarios') }}"><i class="fas fa-users me-2"></i>Usuarios</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="configuracionesDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cog me-1"></i>Configuraciones
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('tipos.clientes') }}"><i class="fas fa-tags me-2"></i>Tipos de Clientes</a></li>
                        <li><a class="dropdown-item" href="{{ route('tipos.creditos') }}"><i class="fas fa-credit-card me-2"></i>Tipos de Créditos</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="creditosDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-credit-card me-1"></i>Créditos
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('creditos.index') }}"><i class="fas fa-list me-2"></i>Listado de Créditos</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="clientesDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-users me-1"></i>Clientes
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('clientes.index') }}"><i class="fas fa-users me-2"></i>Listado de Clientes</a></li>
                    </ul>
                </li>
                @endauth
            </ul>
            
            <ul class="navbar-nav">
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i>Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                    </a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
