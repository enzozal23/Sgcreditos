@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>
                        <i class="fas fa-key"></i> 
                        Gestionar Permisos del Rol: <strong>{{ $role->name }}</strong>
                    </h4>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a Roles
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

                    <!-- Información del Rol -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Información del Rol</h6>
                                    <p class="mb-1"><strong>Nombre:</strong> {{ $role->name }}</p>
                                    <p class="mb-1"><strong>Guard:</strong> {{ $role->guard_name }}</p>
                                    <p class="mb-1"><strong>Descripción:</strong> {{ $role->description ?? 'Sin descripción' }}</p>
                                    <p class="mb-0"><strong>Estado:</strong> 
                                        @if($role->is_active ?? true)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Estadísticas</h6>
                                    <p class="mb-1"><strong>Permisos Asignados:</strong> 
                                        <span class="badge bg-primary">{{ $role->permissions->count() }}</span>
                                    </p>
                                    <p class="mb-1"><strong>Usuarios con este Rol:</strong> 
                                        <span class="badge bg-info">{{ $role->users->count() ?? 0 }}</span>
                                    </p>
                                    <p class="mb-0"><strong>Creado:</strong> {{ $role->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Permisos -->
                    <form action="{{ route('admin.roles.permissions.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Permisos Disponibles</h5>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                        <i class="fas fa-check-double"></i> Seleccionar Todos
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                                        <i class="fas fa-times"></i> Deseleccionar Todos
                                    </button>
                                </div>
                            </div>

                            @if($permissions->count() > 0)
                                <div class="row">
                                    @foreach($permissions as $permission)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox" 
                                                           type="checkbox" 
                                                           id="permission_{{ $permission->id }}" 
                                                           name="permissions[]" 
                                                           value="{{ $permission->id }}"
                                                           {{ $role->hasPermissionTo($permission) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                                 style="width: 25px; height: 25px;">
                                                                <i class="fas fa-key text-white" style="font-size: 12px;"></i>
                                                            </div>
                                                            <div>
                                                                <strong>{{ $permission->name }}</strong>
                                                                @if($permission->description)
                                                                    <br><small class="text-muted">{{ $permission->description }}</small>
                                                                @endif
                                                                <br><small class="text-muted">Guard: {{ $permission->guard_name }}</small>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    No hay permisos disponibles en el sistema.
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary me-2">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Permisos
                            </button>
                        </div>
                    </form>
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

function selectAll() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAll() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Validar formulario antes de enviar
document.querySelector('form').addEventListener('submit', function(e) {
    const checkedPermissions = document.querySelectorAll('.permission-checkbox:checked');
    
    if (checkedPermissions.length === 0) {
        e.preventDefault();
        if (confirm('No has seleccionado ningún permiso. ¿Estás seguro de que quieres continuar?')) {
            return true;
        }
        return false;
    }
});
</script>
@endpush
@endsection
