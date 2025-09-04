@extends('layouts.app')

@section('content')
    
    @php
        $breadcrumbs = [
            ['title' => 'Configuraciones', 'url' => '#'],
            ['title' => 'Tipos de Créditos', 'url' => route('tipos.creditos')]
        ];
    @endphp
    
    <div class="row">
        <div class="col-md-12">
            <div class="card bg-white border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>Tipos de Créditos
                    </h5>
                    <div>
                        <button type="button" class="btn btn-info btn-sm me-2" onclick="irATiposAmortizacion()">
                            <i class="fas fa-calculator me-1"></i>Tipos de Amortización
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="crearTipoCredito()">
                            <i class="fas fa-plus me-1"></i>Nuevo Tipo de Crédito
                        </button>
                    </div>
                </div>
                <div class="card-body bg-white">
                    <div class="table-responsive">
                        <table id="tipos-creditos-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Identificador</th>
                                    <th>Tabla de Créditos</th>
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

    <!-- Modal para crear/editar tipo de crédito -->
    <div class="modal fade" id="tipoCreditoModal" tabindex="-1" aria-labelledby="tipoCreditoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tipoCreditoModalLabel">Nuevo Tipo de Crédito</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="tipoCreditoForm">
                        @csrf
                        <input type="hidden" id="tipo_credito_id" name="tipo_credito_id">
                        <input type="hidden" id="form_method" name="_method" value="POST">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Tipo de Crédito *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Créditos Tasa 0" required>                                    
                                    <div class="invalid-feedback" id="nombre-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="identificador" class="form-label">Identificador *</label>
                                    <input type="text" class="form-control" id="identificador" name="identificador" placeholder="Ej: tasa_0" required>
                                    <div class="form-text">Solo letras minúsculas, números y guiones bajos (_)</div>
                                    <div class="invalid-feedback" id="identificador-error"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveTipoCredito">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para confirmar eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>¡Atención!</strong> Esta acción eliminará:
                    </div>
                    <ul>
                        <li>El tipo de crédito de la base de datos</li>
                        <li>La tabla dinámica asociada: <span id="tabla-eliminar" class="badge bg-danger"></span></li>
                        <li>Todos los datos de créditos almacenados en esa tabla</li>
                    </ul>
                    <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let table;
        let tipoCreditoToDelete = null;
        let tipoCreditoToEdit = null;
        let isEditMode = false;

        $(document).ready(function() {
            // Inicializar DataTable
            table = $('#tipos-creditos-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '/tipos-creditos/data',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    { data: 'identificador' },
                    { 
                        data: 'tabla_credito',
                        render: function(data) {
                            return '<span class="badge bg-info">' + data + '</span>';
                        }
                    },
                    { data: 'fecha_creacion' },
                    { 
                        data: 'acciones',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                responsive: true,
                order: [[0, 'desc']],
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

            // Actualizar preview de tabla cuando cambie el identificador
            $('#identificador').on('input', function() {
                let valor = $(this).val();
                
                // Reemplazar espacios por guiones bajos automáticamente
                valor = valor.replace(/\s+/g, '_');
                
                // Convertir a minúsculas automáticamente
                valor = valor.toLowerCase();
                
                // Remover caracteres no permitidos (excepto a-z, 0-9, _)
                valor = valor.replace(/[^a-z0-9_]/g, '');
                
                // Actualizar el valor del campo
                $(this).val(valor);
                
                // Validar formato en tiempo real
                if (valor && !/^[a-z0-9_]*$/.test(valor)) {
                    $(this).addClass('is-invalid');
                    $('#identificador-error').text('Solo se permiten letras minúsculas, números y guiones bajos (_)');
                } else {
                    $(this).removeClass('is-invalid');
                    $('#identificador-error').text('');
                }
                actualizarPreviewTabla();
            });

            // Actualizar preview de tabla cuando cambie el nombre
            $('#nombre').on('input', function() {
                actualizarPreviewTabla();
            });

            // Aplicar transformación cuando se pegue texto en el identificador
            $('#identificador').on('paste', function() {
                setTimeout(function() {
                    let valor = $('#identificador').val();
                    
                    // Reemplazar espacios por guiones bajos automáticamente
                    valor = valor.replace(/\s+/g, '_');
                    
                    // Convertir a minúsculas automáticamente
                    valor = valor.toLowerCase();
                    
                    // Remover caracteres no permitidos (excepto a-z, 0-9, _)
                    valor = valor.replace(/[^a-z0-9_]/g, '');
                    
                    // Actualizar el valor del campo
                    $('#identificador').val(valor);
                    actualizarPreviewTabla();
                }, 10);
            });
        });

        // Función para actualizar el preview de la tabla
        function actualizarPreviewTabla() {
            let identificador = $('#identificador').val();
            if (identificador) {
                $('#tabla-preview').text('credito_' + identificador);
            } else {
                $('#tabla-preview').text('credito_[identificador]');
            }
        }

        // Función para generar identificador basado en el nombre
        function generarIdentificador(nombre) {
            if (!nombre) return '';
            return nombre.toLowerCase()
                .trim()
                .replace(/\s+/g, '_')  // Reemplazar espacios por guiones bajos
                .replace(/[^a-z0-9_]/g, '')  // Solo permitir letras minúsculas, números y guiones bajos
                .substring(0, 50);
        }



        // Función para crear nuevo tipo de crédito
        function crearTipoCredito() {
            isEditMode = false;
            $('#tipoCreditoModalLabel').text('Nuevo Tipo de Crédito');
            $('#tipo_credito_id').val('');
            $('#form_method').val('POST');
            
            // Limpiar formulario
            $('#tipoCreditoForm')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            // Actualizar preview
            actualizarPreviewTabla();
            
            $('#tipoCreditoModal').modal('show');
        }

        // Función para editar tipo de crédito
        function editarTipoCredito(id) {
            isEditMode = true;
            tipoCreditoToEdit = id;
            
            $.ajax({
                url: '/tipos-creditos/' + id + '/edit',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        const tipo = response.data;
                        
                        $('#tipoCreditoModalLabel').text('Editar Tipo de Crédito');
                        $('#tipo_credito_id').val(tipo.id);
                        $('#form_method').val('PUT');
                        $('#nombre').val(tipo.nombre);
                        $('#identificador').val(tipo.identificador);
                        
                        // Actualizar preview
                        actualizarPreviewTabla();
                        
                        $('#tipoCreditoModal').modal('show');
                    } else {
                        showError('Error', response.message);
                    }
                },
                error: function(xhr) {
                    showError('Error', 'No se pudo cargar el tipo de crédito');
                }
            });
        }



        // Función para eliminar tipo de crédito
        function eliminarTipoCredito(id) {
            tipoCreditoToDelete = id;
            
            // Obtener información del tipo de crédito para mostrar en el modal
            $.ajax({
                url: '/tipos-creditos/' + id + '/edit',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        const tipo = response.data;
                        $('#tabla-eliminar').text('credito_' + tipo.identificador);
                        $('#deleteModal').modal('show');
                    } else {
                        showError('Error', response.message);
                    }
                },
                error: function(xhr) {
                    showError('Error', 'No se pudo cargar la información del tipo de crédito');
                }
            });
        }

        // Guardar tipo de crédito (crear o editar)
        $('#saveTipoCredito').click(function() {
            const formData = new FormData($('#tipoCreditoForm')[0]);
            const url = isEditMode ? '/tipos-creditos/' + tipoCreditoToEdit : '/tipos-creditos';
            const method = isEditMode ? 'PUT' : 'POST';
            
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
                        showSuccess('Éxito', response.message);
                        $('#tipoCreditoModal').modal('hide');
                        table.ajax.reload();
                    } else {
                        showError('Error', response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').text('');
                        
                        $.each(errors, function(field, messages) {
                            $('#' + field).addClass('is-invalid');
                            $('#' + field + '-error').text(messages[0]);
                        });
                    } else {
                        showError('Error', 'No se pudo guardar el tipo de crédito');
                    }
                }
            });
        });

        // Confirmar eliminación
        $('#confirmDelete').click(function() {
            if (tipoCreditoToDelete) {
                $.ajax({
                    url: '/tipos-creditos/' + tipoCreditoToDelete,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showSuccess('Éxito', response.message);
                            $('#deleteModal').modal('hide');
                            table.ajax.reload();
                        } else {
                            showError('Error', response.message);
                        }
                    },
                    error: function(xhr) {
                        showError('Error', 'No se pudo eliminar el tipo de crédito');
                    }
                });
            }
            $('#deleteModal').modal('hide');
            tipoCreditoToDelete = null;
        });

        // Función para definir campos
        function definirCampos(id) {
            window.location.href = '/tipos-creditos/' + id + '/campos';
        }

        // Función para ir a tipos de amortización
        function irATiposAmortizacion() {
            window.location.href = '/tipos-amortizacion';
        }
    </script>
    @endpush
@endsection
