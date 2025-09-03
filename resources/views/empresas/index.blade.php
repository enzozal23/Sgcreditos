@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-building"></i> Gestión de Empresas</h4>
                    <a href="{{ route('empresas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Empresa
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($empresas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>CUIT</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($empresas as $empresa)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $empresa->id }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($empresa->logo)
                                                    <img src="{{ $empresa->logo }}" 
                                                         alt="Logo {{ $empresa->nombre }}" 
                                                         class="me-2" 
                                                         style="width: 30px; height: 30px; object-fit: contain;">
                                                @else
                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 30px; height: 30px;">
                                                        <i class="fas fa-building text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $empresa->nombre }}</strong>
                                                    @if($empresa->razon_social)
                                                        <br><small class="text-muted">{{ $empresa->razon_social }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <code>{{ $empresa->cuit }}</code>
                                        </td>
                                        <td>
                                            @if($empresa->email)
                                                <a href="mailto:{{ $empresa->email }}" class="text-decoration-none">
                                                    <i class="fas fa-envelope"></i> {{ $empresa->email }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($empresa->telefono)
                                                <a href="tel:{{ $empresa->telefono }}" class="text-decoration-none">
                                                    <i class="fas fa-phone"></i> {{ $empresa->telefono }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($empresa->activo)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Activa
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle"></i> Inactiva
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('empresas.show', $empresa) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('empresas.edit', $empresa) }}" 
                                                   class="btn btn-sm btn-warning" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <!-- Toggle Estado -->
                                                <form action="{{ route('empresas.toggle-estado', $empresa) }}" 
                                                      method="POST" 
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-secondary" 
                                                            title="{{ $empresa->activo ? 'Desactivar' : 'Activar' }}">
                                                        <i class="fas fa-toggle-{{ $empresa->activo ? 'off' : 'on' }}"></i>
                                                    </button>
                                                </form>
                                                
                                                <!-- Eliminar -->
                                                <form action="{{ route('empresas.destroy', $empresa) }}" 
                                                      method="POST" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta empresa? Esta acción no se puede deshacer.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $empresas->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay empresas registradas</h5>
                            <p class="text-muted">Comienza creando tu primera empresa</p>
                            <a href="{{ route('empresas.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear Primera Empresa
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endpush
@endsection
