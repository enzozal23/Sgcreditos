@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-key me-2"></i>Gestión de Permisos
                    </h5>
                    <button class="btn btn-primary btn-sm" onclick="nuevoPermiso()">
                        <i class="fas fa-plus me-1"></i>Nuevo Permiso
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="permisos-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Guard</th>
                                    <th>Roles Asignados</th>
                                    <th>Fecha Creación</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permissions as $permission)
                                <tr>
                                    <td>{{ $permission->id }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $permission->name }}</span>
                                    </td>
                                    <td>{{ $permission->description ?? 'Sin descripción' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $permission->guard_name }}</span>
                                    </td>
                                    <td>
                                        @if($permission->roles->count() > 0)
                                            <span class="badge bg-success">{{ $permission->roles->count() }} roles</span>
                                        @else
                                            <span class="badge bg-warning">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td>{{ $permission->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.permisos.show', $permission) }}" 
                                               class="btn btn-sm btn-outline-info" title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.permisos.edit', $permission) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    onclick="gestionarRoles({{ $permission->id }})" title="Gestionar Roles">
                                                <i class="fas fa-users-cog"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="eliminarPermiso({{ $permission->id }}, '{{ $permission->name }}')" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-info-circle me-2"></i>No hay permisos registrados
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Nuevo Permiso -->
<div class="modal fade" id="modalNuevoPermiso" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nuevo Permiso
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevoPermiso" action="{{ route('admin.permisos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre del Permiso *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               placeholder="ej: usuarios.crear" required>
                        <div class="form-text">Formato: modulo.accion (ej: usuarios.crear, clientes.editar)</div>
                    </div>
                    <div class="mb-3">
                        <label for="guard_name" class="form-label">Guard *</label>
                        <select class="form-select" id="guard_name" name="guard_name" required>
                            <option value="">Seleccionar...</option>
                            <option value="web" selected>Web</option>
                            <option value="api">API</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3" placeholder="Descripción del permiso"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#permisos-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        responsive: true,
        order: [[0, 'asc']],
        columnDefs: [
            {
                targets: -1,
                orderable: false,
                searchable: false
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copiar',
                className: 'btn btn-sm btn-outline-info'
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-sm btn-outline-success'
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-outline-primary'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-sm btn-outline-danger'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-sm btn-outline-warning'
            }
        ]
    });
});

function nuevoPermiso() {
    $('#modalNuevoPermiso').modal('show');
}

function gestionarRoles(permissionId) {
    window.location.href = `/admin/permisos/${permissionId}/roles`;
}

function eliminarPermiso(permissionId, permissionName) {
    Swal.fire({
        title: '¿Eliminar Permiso?',
        text: `¿Estás seguro de que quieres eliminar el permiso "${permissionName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear formulario temporal para eliminar
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/permisos/${permissionId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Validación del formulario
$('#formNuevoPermiso').on('submit', function(e) {
    const name = $('#name').val();
    const guard = $('#guard_name').val();
    
    if (!name || !guard) {
        e.preventDefault();
        Swal.fire('Error', 'Por favor completa todos los campos requeridos', 'error');
        return false;
    }
    
    // Validar formato del nombre
    if (!/^[a-z]+\.[a-z]+$/.test(name)) {
        e.preventDefault();
        Swal.fire('Error', 'El nombre del permiso debe tener el formato: modulo.accion', 'error');
        return false;
    }
});
</script>
@endpush
@endsection
