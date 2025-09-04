@extends('layouts.app')

@section('content')
    
    @php
        $breadcrumbs = [
            ['title' => 'Clientes', 'url' => route('clientes.index')],
            ['title' => 'Tipos de Clientes', 'url' => route('clientes.index')],
            ['title' => $tipoCliente->nombre, 'url' => '#']
        ];
    @endphp
    
    <div class="row">
        <div class="col-md-12">
            <div class="card bg-white border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Clientes - {{ $tipoCliente->nombre }}
                    </h5>
                    <button type="button" class="btn btn-primary" onclick="crearCliente()">
                        <i class="fas fa-plus me-2"></i>Nuevo Cliente
                    </button>
                </div>
                <div class="card-body bg-white">
                    <div class="table-responsive">
                        <table id="clientes-tipo-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <!-- Las columnas se generarán dinámicamente -->
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

    <!-- Modal para crear/editar cliente -->
    <div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clienteModalLabel">Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="clienteForm">
                    <div class="modal-body">
                        <div id="campos-dinamicos">
                            <!-- Los campos se generarán dinámicamente -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
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
                    ¿Está seguro de que desea eliminar este cliente?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let table;
        let clienteToDelete;
        let clienteToEdit;
        let isEditMode = false;
        let camposConfiguracion = [];

        $(document).ready(function() {
            // Cargar configuración de campos primero, luego inicializar DataTable
            cargarConfiguracionCampos().then(() => {
                inicializarDataTable();
            });
            
            // Función para formatear nombres de columnas
            function formatearNombreColumna(columna) {
                // Buscar el alias del campo en la configuración
                const campo = camposConfiguracion.find(c => c.nombre_campo === columna);
                if (campo) {
                    return campo.alias;
                }
                // Si no se encuentra, convertir snake_case a palabras con espacios y capitalizar
                return columna.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            }
            
            // Función para inicializar DataTable con columnas dinámicas
            function inicializarDataTable() {
                console.log('Campos configuración:', camposConfiguracion);
                
                // Verificar si hay campos configurados
                if (!camposConfiguracion || camposConfiguracion.length === 0) {
                    $('#clientes-tipo-table').html('<tr><td colspan="1" class="text-center">No hay campos configurados para este tipo de cliente</td></tr>');
                    return;
                }
                
                                 // Generar configuración de columnas basada en la configuración de campos
                 const columnasConfig = camposConfiguracion.map(campo => {
                     const columna = {
                         data: campo.nombre_campo,
                         title: campo.alias,
                         orderable: true,
                         searchable: true
                     };
                     
                     // Para campos numéricos, agregar renderizado personalizado
                     if (campo.tipo_campo === 'numero') {
                         columna.render = function(data) {
                             if (data === null || data === '' || data === undefined) {
                                 return '';
                             }
                             const numValor = parseFloat(data);
                             if (Number.isInteger(numValor)) {
                                 return numValor;
                             } else {
                                 return data;
                             }
                         };
                     }
                     
                     return columna;
                 });
                
                // Agregar columna de acciones al final
                columnasConfig.push({
                    data: 'id',
                    title: 'Acciones',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return '<div class="btn-group" role="group">' +
                               '<button type="button" class="btn btn-sm btn-outline-primary" title="Editar" onclick="editarCliente(' + data + ')">' +
                               '<i class="fas fa-edit"></i> Editar</button>' +
                               '<button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="eliminarCliente(' + data + ')">' +
                               '<i class="fas fa-trash"></i> Eliminar</button>' +
                               '</div>';
                    }
                });
                
                // Inicializar DataTable con configuración completa
                table = $('#clientes-tipo-table').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: '/clientes/tipo/{{ $tipoCliente->id }}/data',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataSrc: 'data',
                        error: function(xhr, error, thrown) {
                            console.error('Error en AJAX del DataTable:', error);
                            console.error('Respuesta del servidor:', xhr.responseText);
                            $('#clientes-tipo-table').html('<tr><td colspan="1" class="text-center">Error al cargar los datos</td></tr>');
                        }
                    },
                    columns: columnasConfig,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                    },
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                    responsive: true,
                    order: [],
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
            }

            // Manejar envío del formulario
            $('#clienteForm').on('submit', function(e) {
                e.preventDefault();
                saveCliente();
            });
        });

        // Función para cargar configuración de campos
        function cargarConfiguracionCampos() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '/clientes/tipo/{{ $tipoCliente->id }}/campos',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            camposConfiguracion = response.campos;
                            resolve();
                        } else {
                            console.error('Error al cargar configuración de campos');
                            reject('Error al cargar configuración de campos');
                        }
                    },
                    error: function() {
                        console.error('Error al cargar configuración de campos');
                        reject('Error al cargar configuración de campos');
                    }
                });
            });
        }



                 // Función para crear nuevo cliente
         function crearCliente() {
             isEditMode = false;
             $('#clienteModalLabel').text('Nuevo Cliente');
             generarCamposFormulario();
             $('#clienteModal').modal('show');
         }

        // Función para editar cliente
        function editarCliente(id) {
            isEditMode = true;
            clienteToEdit = id;
            $('#clienteModalLabel').text('Editar Cliente');
            
            console.log('Editando cliente con ID:', id);
            
            // Cargar datos del cliente
            $.ajax({
                url: `/clientes/tipo/{{ $tipoCliente->id }}/${id}/edit`,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Respuesta del servidor:', response);
                    if (response.success) {
                        generarCamposFormulario(response.cliente);
                        $('#clienteModal').modal('show');
                    } else {
                        showError('Error', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en AJAX:', xhr.responseText);
                    showError('Error', 'Error al cargar los datos del cliente');
                }
            });
        }

        // Función para eliminar cliente
        function eliminarCliente(id) {
            clienteToDelete = id;
            $('#deleteModal').modal('show');
        }

        // Función para confirmar eliminación
        function confirmDelete() {
            if (!clienteToDelete) return;
            
            $.ajax({
                url: `/clientes/tipo/{{ $tipoCliente->id }}/${clienteToDelete}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();
                        showSuccess('Éxito', response.message);
                    } else {
                        showError('Error', response.message);
                    }
                },
                error: function() {
                    showError('Error', 'Error al eliminar el cliente');
                }
            });
        }

                 // Función para generar campos del formulario
         function generarCamposFormulario(datos = null) {
             const container = $('#campos-dinamicos');
             container.empty();
             
             console.log('Generando campos con datos:', datos);
             
             // Agregar campo cliente_id oculto
             const clienteIdValue = datos && datos.cliente_id ? datos.cliente_id : 1;
             container.append(`
                 <input type="hidden" id="cliente_id" name="cliente_id" value="${clienteIdValue}">
             `);
             
             // Generar campos dinámicos basados en la configuración
             camposConfiguracion.forEach(campo => {
                const required = campo.requerido ? 'required' : '';
                const requiredLabel = campo.requerido ? ' *' : '';
                
                let inputHtml = '';
                
                                 switch(campo.tipo_campo) {
                     case 'texto':
                         inputHtml = `<input type="text" class="form-control" id="${campo.nombre_campo}" name="${campo.nombre_campo}" placeholder="Ej: Juan Pérez" ${required}>`;
                         break;
                     case 'numero':
                         inputHtml = `<input type="number" class="form-control" id="${campo.nombre_campo}" name="${campo.nombre_campo}" placeholder="Ej: 12345.67" step="0.01" ${required}>`;
                         break;
                     case 'fecha':
                         inputHtml = `<input type="date" class="form-control" id="${campo.nombre_campo}" name="${campo.nombre_campo}" ${required}>`;
                         break;
                     case 'selector':
                         const opciones = campo.opciones ? campo.opciones.split(',').map(op => op.trim()) : [];
                         inputHtml = `<select class="form-control" id="${campo.nombre_campo}" name="${campo.nombre_campo}" ${required}>`;
                         inputHtml += '<option value="">Seleccione una opción...</option>';
                         opciones.forEach(opcion => {
                             inputHtml += `<option value="${opcion}">${opcion}</option>`;
                         });
                         inputHtml += '</select>';
                         break;
                 }
                
                container.append(`
                    <div class="mb-3">
                        <label for="${campo.nombre_campo}" class="form-label">${campo.alias}${requiredLabel}</label>
                        ${inputHtml}
                    </div>
                `);
            });
            
                         // Si hay datos, llenar el formulario
             if (datos) {
                 console.log('Llenando formulario con datos:', datos);
                 Object.keys(datos).forEach(key => {
                     const valor = datos[key];
                     console.log(`Estableciendo ${key} = ${valor}`);
                     const elemento = $(`#${key}`);
                     console.log(`Elemento encontrado para ${key}:`, elemento.length > 0);
                     
                     // Para campos numéricos, formatear sin .00 si no tiene decimales
                     if (elemento.attr('type') === 'number' && valor !== null && valor !== '') {
                         const numValor = parseFloat(valor);
                         if (Number.isInteger(numValor)) {
                             elemento.val(numValor);
                         } else {
                             elemento.val(valor);
                         }
                     } else {
                         elemento.val(valor);
                     }
                     
                     console.log(`Valor establecido para ${key}:`, elemento.val());
                 });
             }
        }

                 // Función para guardar cliente
         function saveCliente() {
             console.log('saveCliente llamado, isEditMode:', isEditMode);
             console.log('clienteToEdit:', clienteToEdit);
             
             // Serializar el formulario como objeto
             const formData = {};
             $('#clienteForm').serializeArray().forEach(function(item) {
                 formData[item.name] = item.value;
             });
             
             // Log de los datos del formulario
             console.log('Datos del formulario:', formData);
             
             const url = isEditMode 
                 ? `/clientes/tipo/{{ $tipoCliente->id }}/${clienteToEdit}`
                 : `/clientes/tipo/{{ $tipoCliente->id }}`;
             const method = isEditMode ? 'PUT' : 'POST';
             
             console.log('URL:', url);
             console.log('Method:', method);
             
             $.ajax({
                 url: url,
                 type: method,
                 data: formData,
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                 success: function(response) {
                     console.log('Respuesta exitosa:', response);
                     if (response.success) {
                         $('#clienteModal').modal('hide');
                         table.ajax.reload();
                         showSuccess('Éxito', response.message);
                     } else {
                         showError('Error', response.message);
                     }
                 },
                 error: function(xhr, status, error) {
                     console.error('Error en saveCliente:', error);
                     console.error('Status:', status);
                     console.error('Response:', xhr.responseText);
                     
                     // Intentar obtener el mensaje de error del backend
                     let errorMessage = 'Error al guardar el cliente';
                     
                     if (xhr.responseJSON && xhr.responseJSON.message) {
                         errorMessage = xhr.responseJSON.message;
                     } else if (xhr.responseText) {
                         try {
                             const response = JSON.parse(xhr.responseText);
                             if (response.message) {
                                 errorMessage = response.message;
                             }
                         } catch (e) {
                             console.error('Error parsing response:', e);
                         }
                     }
                     
                     showError('Error', errorMessage);
                 }
             });
         }
    </script>
    @endpush
@endsection
