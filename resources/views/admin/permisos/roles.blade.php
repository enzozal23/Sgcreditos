@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users-cog me-2"></i>Gestionar Roles del Permiso: {{ $permission->name }}
                    </h5>
                    <a href="{{ route('admin.permisos.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body">
                    <!-- Información del Permiso -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Información del Permiso</h6>
                                    <p class="mb-1"><strong>Nombre:</strong> <span class="badge bg-primary">{{ $permission->name }}</span></p>
                                    <p class="mb-1"><strong>Guard:</strong> <span class="badge bg-secondary">{{ $permission->guard_name }}</span></p>
                                    <p class="mb-0"><strong>Descripción:</strong> {{ $permission->description ?? 'Sin descripción' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4 class="mb-0">{{ $permission->roles->count() }}</h4>
                                    <small>Roles Actualmente Asignados</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.permisos.roles.update', $permission) }}" method="POST" id="formRoles">
                        @csrf
                        @method('PUT')
                        
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Asignar/Revocar Roles</h6>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="seleccionarTodos()">
                                        <i class="fas fa-check-double me-1"></i>Seleccionar Todos
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deseleccionarTodos()">
                                        <i class="fas fa-times me-1"></i>Deseleccionar Todos
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($roles->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50px;">
                                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                                    </th>
                                                    <th>Rol</th>
                                                    <th>Descripción</th>
                                                    <th>Estado</th>
                                                    <th>Usuarios</th>
                                                    <th>Asignado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($roles as $role)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                                               class="form-check-input role-checkbox"
                                                               {{ $permission->roles->contains($role->id) ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $role->name }}</span>
                                                    </td>
                                                    <td>{{ $role->description ?? 'Sin descripción' }}</td>
                                                    <td>
                                                        @if($role->is_active)
                                                            <span class="badge bg-success">Activo</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $role->users_count ?? 0 }} usuarios</span>
                                                    </td>
                                                    <td>
                                                        @if($permission->roles->contains($role->id))
                                                            <span class="badge bg-success">Sí</span>
                                                        @else
                                                            <span class="badge bg-warning">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                                        <h6>No hay roles disponibles</h6>
                                        <p>Primero debes crear roles en el sistema</p>
                                        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>Crear Rol
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($roles->count() > 0)
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    <small>Selecciona los roles que quieres asignar a este permiso</small>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary" id="btnGuardar">
                                        <i class="fas fa-save me-1"></i>Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Control del checkbox "Seleccionar Todos"
    $('#selectAll').on('change', function() {
        $('.role-checkbox').prop('checked', $(this).is(':checked'));
    });

    // Actualizar el estado del checkbox "Seleccionar Todos"
    $('.role-checkbox').on('change', function() {
        const totalCheckboxes = $('.role-checkbox').length;
        const checkedCheckboxes = $('.role-checkbox:checked').length;
        
        if (checkedCheckboxes === totalCheckboxes) {
            $('#selectAll').prop('checked', true);
        } else {
            $('#selectAll').prop('checked', false);
        }
    });

    // Validación del formulario
    $('#formRoles').on('submit', function(e) {
        const checkedRoles = $('.role-checkbox:checked').length;
        
        if (checkedRoles === 0) {
            e.preventDefault();
            Swal.fire({
                title: 'Advertencia',
                text: 'Debes seleccionar al menos un rol para este permiso',
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
            return false;
        }

        // Mostrar loading
        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
});

function seleccionarTodos() {
    $('.role-checkbox').prop('checked', true);
    $('#selectAll').prop('checked', true);
}

function deseleccionarTodos() {
    $('.role-checkbox').prop('checked', false);
    $('#selectAll').prop('checked', false);
}
</script>
@endpush
@endsection
