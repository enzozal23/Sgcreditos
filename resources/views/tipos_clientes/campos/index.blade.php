<x-app-layout>
    <x-slot name="title">Campos de Tipo de Cliente</x-slot>
    
    @php
        $breadcrumbs = [
            ['title' => 'Configuraciones', 'url' => '#'],
            ['title' => 'Tipos de Clientes', 'url' => route('tipos.clientes')],
            ['title' => 'Campos', 'url' => route('tipos.clientes.campos', $tipoCliente->id)]
        ];
    @endphp
    
    <div class="row">
        <div class="col-md-12">
            <div class="card bg-white border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>Campos de: {{ $tipoCliente->nombre }}
                        </h5>
                        <small class="text-muted">Identificador: {{ $tipoCliente->identificador }}</small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" onclick="crearCampo()">
                            <i class="fas fa-plus me-1"></i>Nuevo Campo
                        </button>
                        <a href="{{ route('tipos.clientes') }}" class="btn btn-secondary btn-sm ms-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
                <div class="card-body bg-white">
                    <div class="table-responsive">
                        <table id="campos-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Campo</th>
                                    <th>Alias</th>
                                    <th>Tipo</th>
                                    <th>Requerido</th>
                                    <th>Es Único</th>
                                    <th>Orden</th>
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

    <!-- Modal para crear/editar campo -->
    <div class="modal fade" id="campoModal" tabindex="-1" aria-labelledby="campoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="campoModalLabel">Nuevo Campo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="campoForm">
                        @csrf
                        <input type="hidden" id="campo_id" name="campo_id">
                        <input type="hidden" id="form_method" name="_method" value="POST">
                        <input type="hidden" id="tipo_cliente_id" name="tipo_cliente_id" value="{{ $tipoCliente->id }}">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre_campo" class="form-label">Nombre Campo *</label>
                                    <input type="text" class="form-control" id="nombre_campo" name="nombre_campo" 
                                           placeholder="ejemplo: telefono_celular" required 
                                           pattern="[a-z0-9_]+" 
                                           title="Solo letras minúsculas, números y guiones bajos">
                                    <div class="form-text">Solo letras minúsculas, números y guiones bajos (será el nombre de la columna en la base de datos)</div>
                                    <div class="invalid-feedback" id="nombre_campo-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="alias" class="form-label">Alias *</label>
                                    <input type="text" class="form-control" id="alias" name="alias" 
                                           placeholder="ejemplo: Teléfono Celular" required>
                                    <div class="form-text">Nombre que se mostrará al usuario</div>
                                    <div class="invalid-feedback" id="alias-error"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="tipo_campo" class="form-label">Tipo de Campo *</label>
                                    <select class="form-select" id="tipo_campo" name="tipo_campo" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="texto">Texto</option>
                                        <option value="numero">Número</option>
                                        <option value="fecha">Fecha</option>
                                        <option value="selector">Selector</option>
                                    </select>
                                    <div class="invalid-feedback" id="tipo_campo-error"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="requerido" class="form-label">Requerido</label>
                                    <select class="form-select" id="requerido" name="requerido">
                                        <option value="0">No</option>
                                        <option value="1">Sí</option>
                                    </select>
                                    <div class="invalid-feedback" id="requerido-error"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="es_unico" class="form-label">Es Único</label>
                                    <select class="form-select" id="es_unico" name="es_unico">
                                        <option value="0">No</option>
                                        <option value="1">Sí</option>
                                    </select>
                                    <div class="form-text">Marque "Sí" si el valor debe ser único en la base de datos</div>
                                    <div class="invalid-feedback" id="es_unico-error"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="orden" class="form-label">Orden</label>
                                    <input type="number" class="form-control" id="orden" name="orden" min="1" value="1">
                                    <div class="invalid-feedback" id="orden-error"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3" id="opciones_container" style="display: none;">
                            <label for="opciones" class="form-label">Opciones del Selector</label>
                            <textarea class="form-control" id="opciones" name="opciones" rows="3" 
                                      placeholder="Una opción por línea. Ejemplo:&#10;Opción 1&#10;Opción 2&#10;Opción 3"></textarea>
                            <div class="form-text">Ingresa una opción por línea para campos de tipo selector</div>
                            <div class="invalid-feedback" id="opciones-error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveCampo">Guardar</button>
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
                    ¿Está seguro de que desea eliminar este campo? Esta acción no se puede deshacer.
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
        let campoToDelete = null;
        let campoToEdit = null;
        let isEditMode = false;
        const tipoClienteId = {{ $tipoCliente->id }};

        $(document).ready(function() {
            // Inicializar DataTable
            table = $('#campos-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: `/tipos-clientes/${tipoClienteId}/campos/data`,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { data: 'id' },
                    { data: 'nombre_campo' },
                    { data: 'alias' },
                    { data: 'tipo_campo' },
                    { 
                        data: 'requerido',
                        render: function(data) {
                            return data == 1 ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>';
                        }
                    },
                    { 
                        data: 'es_unico',
                        render: function(data) {
                            return data == 1 ? '<span class="badge bg-warning">Sí</span>' : '<span class="badge bg-secondary">No</span>';
                        }
                    },
                    { data: 'orden' },
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
                order: [[6, 'asc']], // Ordenar por orden
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

            // Mostrar/ocultar campo de opciones según el tipo seleccionado
            $('#tipo_campo').change(function() {
                if ($(this).val() === 'selector') {
                    $('#opciones_container').show();
                    $('#opciones').prop('required', true);
                } else {
                    $('#opciones_container').hide();
                    $('#opciones').prop('required', false);
                }
            });

            // Convertir nombre campo a formato válido
            $('#nombre_campo').on('input', function() {
                let value = $(this).val();
                // Convertir a minúsculas y reemplazar espacios y caracteres especiales con guiones bajos
                value = value.toLowerCase()
                    .replace(/[^a-z0-9_]/g, '_')
                    .replace(/_+/g, '_')
                    .replace(/^_|_$/g, '');
                $(this).val(value);
            });
        });

        // Función para crear nuevo campo
        function crearCampo() {
            isEditMode = false;
            $('#campoModalLabel').text('Nuevo Campo');
            $('#campo_id').val('');
            $('#form_method').val('POST');
            
            // Limpiar formulario
            $('#campoForm')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#opciones_container').hide();
            $('#opciones').prop('required', false);
            
            $('#campoModal').modal('show');
        }

        // Función para editar campo
        function editarCampo(id) {
            isEditMode = true;
            campoToEdit = id;
            
            $.ajax({
                url: `/tipos-clientes/${tipoClienteId}/campos/${id}/edit`,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        let campo = response.campo;
                        

                        
                        $('#campoModalLabel').text('Editar Campo');
                        $('#campo_id').val(campo.id);
                        $('#form_method').val('PUT');
                        
                        // Llenar el formulario con los datos del campo
                        $('#nombre_campo').val(campo.nombre_campo);
                        $('#alias').val(campo.alias);
                        $('#tipo_campo').val(campo.tipo_campo);
                        $('#orden').val(campo.orden);
                        $('#requerido').val(campo.requerido);
                        $('#es_unico').val(campo.es_unico);
                        $('#opciones').val(campo.opciones);
                        

                        
                        // Mostrar/ocultar opciones según el tipo
                        if (campo.tipo_campo === 'selector') {
                            $('#opciones_container').show();
                            $('#opciones').prop('required', true);
                        } else {
                            $('#opciones_container').hide();
                            $('#opciones').prop('required', false);
                        }
                        
                        // Limpiar errores previos
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').text('');
                        
                        $('#campoModal').modal('show');
                    } else {
                        showError('Error', response.message);
                    }
                },
                error: function(xhr) {
                    let message = 'Error al obtener los datos del campo';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showError('Error', message);
                }
            });
        }

        // Función para eliminar campo
        function eliminarCampo(id) {
            campoToDelete = id;
            $('#deleteModal').modal('show');
        }

        // Guardar campo (crear o editar)
        $('#saveCampo').click(function() {
            // Limpiar errores previos
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            let url, method;
            
            if (isEditMode) {
                // Modo edición
                url = `/tipos-clientes/${tipoClienteId}/campos/${campoToEdit}`;
                method = 'PUT';
            } else {
                // Modo creación
                url = `/tipos-clientes/${tipoClienteId}/campos`;
                method = 'POST';
            }
            
            $.ajax({
                url: url,
                type: method,
                data: $('#campoForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showSuccess('Éxito', response.message);
                        $('#campoModal').modal('hide');
                        table.ajax.reload();
                    } else {
                        showError('Error', response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Errores de validación
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            $('#' + field).addClass('is-invalid');
                            $('#' + field + '-error').text(messages[0]);
                        });
                    } else {
                        let message = isEditMode ? 'Error al actualizar el campo' : 'Error al crear el campo';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showError('Error', message);
                    }
                }
            });
        });

        // Confirmar eliminación
        $('#confirmDelete').click(function() {
            if (campoToDelete) {
                $.ajax({
                    url: `/tipos-clientes/${tipoClienteId}/campos/${campoToDelete}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showSuccess('Éxito', response.message);
                            table.ajax.reload();
                        } else {
                            showError('Error', response.message);
                        }
                    },
                    error: function(xhr) {
                        let message = 'Error al eliminar el campo';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showError('Error', message);
                    }
                });
            }
            $('#deleteModal').modal('hide');
            campoToDelete = null;
        });
    </script>
    @endpush
</x-app-layout>
