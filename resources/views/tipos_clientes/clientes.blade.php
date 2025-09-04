@extends('layouts.app')

@section('content')
    
    @php
        $breadcrumbs = [
            ['title' => 'Configuraciones', 'url' => '#'],
            ['title' => 'Tipos de Clientes', 'url' => route('tipos.clientes')]
        ];
    @endphp
    
    <div class="row">
        <div class="col-md-12">
            <div class="card bg-white border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-tags me-2"></i>Gestión de Tipos de Clientes
                    </h5>
                    <button type="button" class="btn btn-primary btn-sm" onclick="crearTipoCliente()">
                        <i class="fas fa-plus me-1"></i>Nuevo Tipo
                    </button>
                </div>
                <div class="card-body bg-white">
                    <div class="table-responsive">
                        <table id="tipos-clientes-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                                                         <th>ID</th>
                                     <th>Nombre</th>
                                     <th>Identificador</th>
                                     <th>Estado</th>
                                     <th>Fecha Creación</th>
                                     <th width="20%" class="no-sort">Acciones</th>
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

    <!-- Modal para crear/editar tipo de cliente -->
    <div class="modal fade" id="tipoClienteModal" tabindex="-1" aria-labelledby="tipoClienteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tipoClienteModalLabel">Nuevo Tipo de Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="tipoClienteForm">
                        @csrf
                        <input type="hidden" id="tipo_id" name="tipo_id">
                        <input type="hidden" id="form_method" name="_method" value="POST">
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                            <div class="invalid-feedback" id="nombre-error"></div>
                        </div>
                        
                                                 <div class="mb-3">
                             <label for="identificador" class="form-label">Identificador *</label>
                             <input type="text" class="form-control" id="identificador" name="identificador" required maxlength="50">
                             <div class="invalid-feedback" id="identificador-error"></div>
                         </div>
                        
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado *</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="">Seleccionar estado</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                            <div class="invalid-feedback" id="estado-error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveTipoCliente">Guardar</button>
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
                    ¿Está seguro de que desea eliminar este tipo de cliente? Esta acción no se puede deshacer.
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
        let tipoToDelete = null;
        let tipoToEdit = null;
        let isEditMode = false;

        $(document).ready(function() {
            // Inicializar DataTable
            table = $('#tipos-clientes-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '{{ route("tipos.clientes.data") }}',
                    type: 'GET',
                    xhrFields: {
                        withCredentials: true
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    error: function(xhr, error, thrown) {
                        console.error('Error en DataTable:', error);
                        console.error('XHR:', xhr);
                        console.error('Thrown:', thrown);
                        if (xhr.status === 401 || xhr.status === 403) {
                            alert('Sesión expirada. Por favor, vuelva a iniciar sesión.');
                            window.location.href = '/login';
                        }
                    }
                },
                                 columns: [
                     { data: 'id' },
                     { data: 'nombre' },
                     { data: 'identificador' },
                     { 
                         data: 'estado',
                        render: function(data) {
                            let badgeClass = 'bg-success';
                            let text = 'Activo';
                            
                            if (data === 'inactivo') {
                                badgeClass = 'bg-danger';
                                text = 'Inactivo';
                            }
                            
                            return `<span class="badge ${badgeClass}">${text}</span>`;
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
        });

        // Función para crear nuevo tipo de cliente
        function crearTipoCliente() {
            isEditMode = false;
            $('#tipoClienteModalLabel').text('Nuevo Tipo de Cliente');
            $('#tipo_id').val('');
            $('#form_method').val('POST');
            
            // Limpiar formulario
            $('#tipoClienteForm')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            $('#tipoClienteModal').modal('show');
        }

                 // Función para editar tipo de cliente
         function editarTipoCliente(id) {
             isEditMode = true;
             tipoToEdit = id;
             
             $.ajax({
                 url: `/tipos-clientes/${id}/edit`,
                 type: 'GET',
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                 success: function(response) {
                     if (response.success) {
                         let tipo = response.tipo;
                         
                         $('#tipoClienteModalLabel').text('Editar Tipo de Cliente');
                         $('#tipo_id').val(tipo.id);
                         $('#form_method').val('PUT');
                         
                                                   // Llenar el formulario con los datos del tipo
                          $('#nombre').val(tipo.nombre);
                          $('#identificador').val(tipo.identificador);
                          $('#estado').val(tipo.estado);
                         
                         // Limpiar errores previos
                         $('.is-invalid').removeClass('is-invalid');
                         $('.invalid-feedback').text('');
                         
                         $('#tipoClienteModal').modal('show');
                     } else {
                         showError('Error', response.message);
                     }
                 },
                 error: function(xhr) {
                     let message = 'Error al obtener los datos del tipo de cliente';
                     if (xhr.responseJSON && xhr.responseJSON.message) {
                         message = xhr.responseJSON.message;
                     }
                     showError('Error', message);
                 }
             });
         }

                 // Función para eliminar tipo de cliente
         function eliminarTipoCliente(id) {
             tipoToDelete = id;
             $('#deleteModal').modal('show');
         }
         
         // Función para definir campos del tipo de cliente
         function definirCampos(id) {
             // Redirigir a la vista de campos
             window.location.href = `/tipos-clientes/${id}/campos`;
         }

        // Guardar tipo de cliente (crear o editar)
        $('#saveTipoCliente').click(function() {
            // Limpiar errores previos
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            let url, method;
            
            if (isEditMode) {
                // Modo edición
                url = `/tipos-clientes/${tipoToEdit}`;
                method = 'PUT';
            } else {
                // Modo creación
                url = '/tipos-clientes';
                method = 'POST';
            }
            
            $.ajax({
                url: url,
                type: method,
                data: $('#tipoClienteForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showSuccess('Éxito', response.message);
                        $('#tipoClienteModal').modal('hide');
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
                        let message = isEditMode ? 'Error al actualizar el tipo de cliente' : 'Error al crear el tipo de cliente';
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
            if (tipoToDelete) {
                $.ajax({
                    url: `/tipos-clientes/${tipoToDelete}`,
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
                        let message = 'Error al eliminar el tipo de cliente';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showError('Error', message);
                    }
                });
            }
            $('#deleteModal').modal('hide');
            tipoToDelete = null;
        });
    </script>
    @endpush
@endsection
