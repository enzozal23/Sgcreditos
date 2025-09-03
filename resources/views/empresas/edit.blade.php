@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Editar Empresa: {{ $empresa->nombre }}</h4>
                    <a href="{{ route('empresas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('empresas.update', $empresa) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Nombre -->
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="{{ old('nombre', $empresa->nombre) }}" 
                                       required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Razón Social -->
                            <div class="col-md-6 mb-3">
                                <label for="razon_social" class="form-label">Razón Social</label>
                                <input type="text" 
                                       class="form-control @error('razon_social') is-invalid @enderror" 
                                       id="razon_social" 
                                       name="razon_social" 
                                       value="{{ old('razon_social', $empresa->razon_social) }}">
                                @error('razon_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- CUIT -->
                            <div class="col-md-6 mb-3">
                                <label for="cuit" class="form-label">CUIT *</label>
                                <input type="text" 
                                       class="form-control @error('cuit') is-invalid @enderror" 
                                       id="cuit" 
                                       name="cuit" 
                                       value="{{ old('cuit', $empresa->cuit) }}" 
                                       placeholder="XX-XXXXXXXX-X"
                                       required>
                                @error('cuit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $empresa->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Teléfono -->
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" 
                                       class="form-control @error('telefono') is-invalid @enderror" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="{{ old('telefono', $empresa->telefono) }}">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Logo -->
                            <div class="col-md-6 mb-3">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="text" 
                                       class="form-control @error('logo') is-invalid @enderror" 
                                       id="logo" 
                                       name="logo" 
                                       value="{{ old('logo', $empresa->logo) }}"
                                       placeholder="URL del logo">
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" 
                                   class="form-control @error('direccion') is-invalid @enderror" 
                                   id="direccion" 
                                   name="direccion" 
                                   value="{{ old('direccion', $empresa->direccion) }}">
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Ciudad -->
                            <div class="col-md-4 mb-3">
                                <label for="ciudad" class="form-label">Ciudad</label>
                                <input type="text" 
                                       class="form-control @error('ciudad') is-invalid @enderror" 
                                       id="ciudad" 
                                       name="ciudad" 
                                       value="{{ old('ciudad', $empresa->ciudad) }}">
                                @error('ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Provincia -->
                            <div class="col-md-4 mb-3">
                                <label for="provincia" class="form-label">Provincia</label>
                                <input type="text" 
                                       class="form-control @error('provincia') is-invalid @enderror" 
                                       id="provincia" 
                                       name="provincia" 
                                       value="{{ old('provincia', $empresa->provincia) }}">
                                @error('provincia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Código Postal -->
                            <div class="col-md-4 mb-3">
                                <label for="codigo_postal" class="form-label">Código Postal</label>
                                <input type="text" 
                                       class="form-control @error('codigo_postal') is-invalid @enderror" 
                                       id="codigo_postal" 
                                       name="codigo_postal" 
                                       value="{{ old('codigo_postal', $empresa->codigo_postal) }}">
                                @error('codigo_postal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="activo" 
                                       name="activo" 
                                       value="1" 
                                       {{ old('activo', $empresa->activo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">
                                    Empresa Activa
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('empresas.index') }}" class="btn btn-secondary me-2">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Empresa
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
    // Formatear CUIT automáticamente
    const cuitInput = document.getElementById('cuit');
    cuitInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            if (value.length <= 2) {
                value = value;
            } else if (value.length <= 10) {
                value = value.substring(0, 2) + '-' + value.substring(2);
            } else {
                value = value.substring(0, 2) + '-' + value.substring(2, 10) + '-' + value.substring(10, 11);
            }
        }
        e.target.value = value;
    });

    // Validar formulario antes de enviar
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const nombre = document.getElementById('nombre').value.trim();
        const cuit = document.getElementById('cuit').value.trim();
        
        if (!nombre) {
            e.preventDefault();
            alert('El nombre es obligatorio');
            return false;
        }
        
        if (!cuit) {
            e.preventDefault();
            alert('El CUIT es obligatorio');
            return false;
        }
        
        // Validar formato de CUIT (XX-XXXXXXXX-X)
        const cuitRegex = /^\d{2}-\d{8}-\d{1}$/;
        if (!cuitRegex.test(cuit)) {
            e.preventDefault();
            alert('El CUIT debe tener el formato XX-XXXXXXXX-X');
            return false;
        }
    });
});
</script>
@endpush
@endsection
