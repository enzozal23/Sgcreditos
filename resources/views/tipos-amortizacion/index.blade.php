@extends('layouts.app')

@section('content')
    
    @php
        $breadcrumbs = [
            ['title' => 'Configuraciones', 'url' => '#'],
            ['title' => 'Tipos de Amortización', 'url' => route('tipos.amortizacion')]
        ];
    @endphp

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-calculator me-2"></i>Tipos de Amortización
                        </h5>
                        <button type="button" class="btn btn-primary btn-sm" onclick="crearTipoAmortizacion()">
                            <i class="fas fa-plus me-1"></i>Nuevo Tipo de Amortización
                        </button>
                    </div>
                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table id="tipos-amortizacion-table" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Fórmula</th>
                                        <th>Estado</th>
                                        <th>Fecha Creación</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los datos se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Crear/Editar Tipo de Amortización -->
    <div class="modal fade" id="modalTipoAmortizacion" tabindex="-1" aria-labelledby="modalTipoAmortizacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTipoAmortizacionLabel">Nuevo Tipo de Amortización</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formTipoAmortizacion">
                    <div class="modal-body">
                        <input type="hidden" id="tipo_amortizacion_id" name="id">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="255">
                                    <div class="invalid-feedback" id="nombre-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado *</label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                    <div class="invalid-feedback" id="estado-error"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="1000" placeholder="Descripción del tipo de amortización..."></textarea>
                            <div class="invalid-feedback" id="descripcion-error"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="formula" class="form-label">Fórmula</label>
                            <textarea class="form-control" id="formula" name="formula" rows="4" maxlength="1000" placeholder="Fórmula matemática para el cálculo de amortización..."></textarea>
                            <div class="invalid-feedback" id="formula-error"></div>
                            <div class="form-text">Ejemplo: Cuota = Principal * (i * (1 + i)^n) / ((1 + i)^n - 1)</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardar">
                            <i class="fas fa-save me-1"></i>Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let table;
        let isEditing = false;

        $(document).ready(function() {
            // Inicializar DataTable
            table = $('#tipos-amortizacion-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '/tipos-amortizacion/data',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { data: 'id', title: 'ID' },
                    { data: 'nombre', title: 'Nombre' },
                    { data: 'descripcion', title: 'Descripción' },
                    { data: 'formula', title: 'Fórmula' },
                    { 
                        data: 'estado', 
                        title: 'Estado',
                        render: function(data) {
                            return data ? 
                                '<span class="badge bg-success">Activo</span>' : 
                                '<span class="badge bg-secondary">Inactivo</span>';
                        }
                    },
                    { data: 'fecha_creacion', title: 'Fecha Creación' },
                    { data: 'acciones', title: 'Acciones', orderable: false, searchable: false }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                order: [[0, 'desc']],
                responsive: true
            });

            // Configurar formulario
            configurarFormulario();
        });

        function configurarFormulario() {
            $('#formTipoAmortizacion').on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const url = isEditing ? 
                    '/tipos-amortizacion/' + $('#tipo_amortizacion_id').val() : 
                    '/tipos-amortizacion';
                const method = isEditing ? 'PUT' : 'POST';

                // Limpiar errores previos
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                // Deshabilitar botón
                $('#btnGuardar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Guardando...');

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Mostrar mensaje de éxito
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Cerrar modal y recargar tabla
                            $('#modalTipoAmortizacion').modal('hide');
                            table.ajax.reload();
                            
                            // Limpiar formulario
                            limpiarFormulario();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Errores de validación
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(field) {
                                $('#' + field).addClass('is-invalid');
                                $('#' + field + '-error').text(errors[field][0]);
                            });
                        } else {
                            // Error general
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Error al procesar la solicitud'
                            });
                        }
                    },
                    complete: function() {
                        // Habilitar botón
                        $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Guardar');
                    }
                });
            });
        }

        function crearTipoAmortizacion() {
            isEditing = false;
            $('#modalTipoAmortizacionLabel').text('Nuevo Tipo de Amortización');
            limpiarFormulario();
            $('#modalTipoAmortizacion').modal('show');
        }

        function editarTipoAmortizacion(id) {
            isEditing = true;
            $('#modalTipoAmortizacionLabel').text('Editar Tipo de Amortización');
            
            // Cargar datos del tipo de amortización
            $.ajax({
                url: '/tipos-amortizacion/' + id + '/edit',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#tipo_amortizacion_id').val(data.id);
                        $('#nombre').val(data.nombre);
                        $('#descripcion').val(data.descripcion);
                        $('#formula').val(data.formula);
                        $('#estado').val(data.estado ? '1' : '0');
                        $('#modalTipoAmortizacion').modal('show');
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Error al cargar los datos'
                    });
                }
            });
        }

        function eliminarTipoAmortizacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/tipos-amortizacion/' + id,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Eliminado!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                table.ajax.reload();
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Error al eliminar el tipo de amortización'
                            });
                        }
                    });
                }
            });
        }

        function limpiarFormulario() {
            $('#formTipoAmortizacion')[0].reset();
            $('#tipo_amortizacion_id').val('');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            isEditing = false;
        }

        // Limpiar formulario cuando se cierre el modal
        $('#modalTipoAmortizacion').on('hidden.bs.modal', function() {
            limpiarFormulario();
        });
    </script>
    @endpush
@endsection
