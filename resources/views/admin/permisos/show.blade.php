@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-eye me-2"></i>Detalles del Permiso: {{ $permission->name }}
                    </h5>
                    <div>
                        <a href="{{ route('admin.permisos.edit', $permission) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                        <a href="{{ route('admin.permisos.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Información del Permiso</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 150px;">ID:</td>
                                    <td>{{ $permission->id }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Nombre:</td>
                                    <td><span class="badge bg-primary">{{ $permission->name }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Guard:</td>
                                    <td><span class="badge bg-secondary">{{ $permission->guard_name }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Descripción:</td>
                                    <td>{{ $permission->description ?? 'Sin descripción' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Fecha Creación:</td>
                                    <td>{{ $permission->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Última Actualización:</td>
                                    <td>{{ $permission->updated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Estadísticas</h6>
                            <div class="row">
                                <div class="col-6">
                                    <div class="card bg-primary text-white text-center mb-3">
                                        <div class="card-body">
                                            <h4 class="mb-0">{{ $permission->roles->count() }}</h4>
                                            <small>Roles Asignados</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-success text-white text-center mb-3">
                                        <div class="card-body">
                                            <h4 class="mb-0">{{ $permission->roles->sum('users_count') }}</h4>
                                            <small>Usuarios Totales</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <a href="{{ route('admin.permisos.roles', $permission) }}" class="btn btn-warning">
                                    <i class="fas fa-users-cog me-1"></i>Gestionar Roles
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    @if($permission->roles->count() > 0)
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">Roles Asignados</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Rol</th>
                                            <th>Descripción</th>
                                            <th>Estado</th>
                                            <th>Usuarios</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($permission->roles as $role)
                                        <tr>
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
                                                <a href="{{ route('admin.roles.show', $role) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Ver Rol">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @else
                    <hr>
                    <div class="text-center text-muted">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h6>Este permiso no está asignado a ningún rol</h6>
                        <p>Para asignar roles a este permiso, haz clic en "Gestionar Roles"</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
