@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-user-tag"></i> Gestión de Roles</h4>
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Rol
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

                    @if($roles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Usuarios</th>
                                        <th>Permisos</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $role->id }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 30px; height: 30px;">
                                                    <i class="fas fa-user-tag text-white"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $role->name }}</strong>
                                                    @if($role->guard_name)
                                                        <br><small class="text-muted">{{ $role->guard_name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $role->description ?? 'Sin descripción' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $role->users_count ?? 0 }} usuarios
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">
                                                {{ $role->permissions_count ?? 0 }} permisos
                                            </span>
                                        </td>
                                        <td>
                                            @if($role->is_active ?? true)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Activo
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle"></i> Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.roles.show', $role) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.roles.edit', $role) }}" 
                                                   class="btn btn-sm btn-warning" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.roles.permissions', $role) }}" 
                                                   class="btn btn-sm btn-secondary" 
                                                   title="Gestionar Permisos">
                                                    <i class="fas fa-key"></i>
                                                </a>
                                                
                                                <!-- Toggle Estado -->
                                                <form action="{{ route('admin.roles.toggle-estado', $role) }}" 
                                                      method="POST" 
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-secondary" 
                                                            title="{{ $role->is_active ?? true ? 'Desactivar' : 'Activar' }}">
                                                        <i class="fas fa-toggle-{{ $role->is_active ?? true ? 'off' : 'on' }}"></i>
                                                    </button>
                                                </form>
                                                
                                                <!-- Eliminar -->
                                                <form action="{{ route('admin.roles.destroy', $role) }}" 
                                                      method="POST" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('¿Estás seguro de que quieres eliminar este rol? Esta acción no se puede deshacer.')">
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
                            {{ $roles->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay roles registrados</h5>
                            <p class="text-muted">Comienza creando tu primer rol</p>
                            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear Primer Rol
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
