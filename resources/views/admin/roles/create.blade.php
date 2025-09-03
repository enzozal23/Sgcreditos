@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Crear Nuevo Rol</h4>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Nombre del Rol -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nombre del Rol *</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="ej: admin, usuario, supervisor"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Usa solo letras minúsculas, números y guiones bajos
                                </small>
                            </div>

                            <!-- Guard Name -->
                            <div class="col-md-6 mb-3">
                                <label for="guard_name" class="form-label">Guard *</label>
                                <select class="form-select @error('guard_name') is-invalid @enderror" 
                                        id="guard_name" 
                                        name="guard_name" 
                                        required>
                                    <option value="">Seleccionar Guard</option>
                                    <option value="web" {{ old('guard_name') == 'web' ? 'selected' : '' }}>Web</option>
                                    <option value="api" {{ old('guard_name') == 'api' ? 'selected' : '' }}>API</option>
                                    <option value="admin" {{ old('guard_name') == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                @error('guard_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Define el contexto de autenticación
                                </small>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Describe el propósito y responsabilidades de este rol">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Permisos -->
                        <div class="mb-3">
                            <label class="form-label">Permisos Disponibles</label>
                            <div class="row">
                                @foreach($permissions ?? [] as $permission)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="permission_{{ $permission->id }}" 
                                               name="permissions[]" 
                                               value="{{ $permission->id }}"
                                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            <strong>{{ $permission->name }}</strong>
                                            @if($permission->description)
                                                <br><small class="text-muted">{{ $permission->description }}</small>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @if(empty($permissions))
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    No hay permisos disponibles. Primero debes crear algunos permisos.
                                </div>
                            @endif
                        </div>

                        <!-- Estado -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Rol Activo
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Los roles inactivos no pueden ser asignados a usuarios
                            </small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary me-2">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Crear Rol
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
    // Validar formulario antes de enviar
    const form = document.querySelector('form');
    const nameInput = document.getElementById('name');
    
    // Formatear nombre del rol (solo minúsculas, números y guiones bajos)
    nameInput.addEventListener('input', function(e) {
        let value = e.target.value.toLowerCase();
        value = value.replace(/[^a-z0-9_]/g, '');
        e.target.value = value;
    });

    form.addEventListener('submit', function(e) {
        const name = nameInput.value.trim();
        const guardName = document.getElementById('guard_name').value;
        
        if (!name) {
            e.preventDefault();
            alert('El nombre del rol es obligatorio');
            nameInput.focus();
            return false;
        }
        
        if (!guardName) {
            e.preventDefault();
            alert('Debes seleccionar un guard');
            document.getElementById('guard_name').focus();
            return false;
        }
        
        // Validar formato del nombre
        const nameRegex = /^[a-z0-9_]+$/;
        if (!nameRegex.test(name)) {
            e.preventDefault();
            alert('El nombre del rol solo puede contener letras minúsculas, números y guiones bajos');
            nameInput.focus();
            return false;
        }
    });
});
</script>
@endpush
@endsection
