@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Permiso: {{ $permission->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.permisos.update', $permission) }}" method="POST" id="formPermiso">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre del Permiso *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $permission->name) }}" 
                                           placeholder="ej: usuarios.crear" required>
                                    <div class="form-text">Formato: modulo.accion (ej: usuarios.crear, clientes.editar)</div>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="guard_name" class="form-label">Guard *</label>
                                    <select class="form-select @error('guard_name') is-invalid @enderror" 
                                            id="guard_name" name="guard_name" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="web" {{ old('guard_name', $permission->guard_name) == 'web' ? 'selected' : '' }}>Web</option>
                                        <option value="api" {{ old('guard_name', $permission->guard_name) == 'api' ? 'selected' : '' }}>API</option>
                                    </select>
                                    @error('guard_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Descripción del permiso">{{ old('description', $permission->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.permisos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Actualizar Permiso
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
$(document).ready(function() {
    // Validación del formulario
    $('#formPermiso').on('submit', function(e) {
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
        
        // Mostrar loading
        Swal.fire({
            title: 'Actualizando...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
    
    // Auto-formatear el nombre del permiso
    $('#name').on('input', function() {
        let value = $(this).val();
        value = value.toLowerCase().replace(/[^a-z.]/g, '');
        $(this).val(value);
    });
});
</script>
@endpush
@endsection
