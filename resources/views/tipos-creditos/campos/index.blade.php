<x-app-layout>
    <x-slot name="title">Campos del Tipo de Crédito</x-slot>
    
    @php
        $breadcrumbs = [
            ['title' => 'Configuraciones', 'url' => '#'],
            ['title' => 'Tipos de Créditos', 'url' => route('tipos.creditos')],
            ['title' => 'Campos', 'url' => '#']
        ];
    @endphp
    
    <div class="row">
        <div class="col-md-12">
            <div class="card bg-white border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>Campos del Tipo de Crédito
                        </h5>
                        <small class="text-muted" id="tipo-credito-info">Configurando campos para: <span id="nombre-tipo-credito" class="fw-bold"></span></small>
                    </div>
                    <div>
                        <a href="{{ route('tipos.creditos') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" onclick="agregarCampo()">
                            <i class="fas fa-plus me-1"></i>Agregar Campo
                        </button>
                    </div>
                </div>
                <div class="card-body bg-white">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Información de la Tabla</h6>
                        <p class="mb-1"><strong>Tabla:</strong> <span id="nombre-tabla" class="badge bg-info"></span></p>
                        <p class="mb-0"><small>Esta tabla contiene los créditos de este tipo. Aquí puedes agregar campos personalizados adicionales.</small></p>
                    </div>
                    
                    <div class="table-responsive">
                        <table id="campos-table" class="table table-striped table-hover">
                                                                                                                   <thead>
                                  <tr>
                                      <th>Orden</th>
                                      <th>Nombre del Campo</th>
                                      <th>Alias</th>
                                      <th>Tipo</th>
                                      <th>Requerido</th>
                                      <th>Monto Trans.</th>
                                      <th>Fecha Ejec.</th>
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

    <!-- Modal para agregar/editar campo -->
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
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre_campo" class="form-label">Nombre del Campo *</label>
                                    <input type="text" class="form-control" id="nombre_campo" name="nombre_campo" placeholder="Ej: monto_extra" required>
                                    <div class="form-text">Nombre de la columna en la base de datos (solo minúsculas, números y guiones bajos)</div>
                                    <div class="invalid-feedback" id="nombre_campo-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="alias" class="form-label">Alias *</label>
                                    <input type="text" class="form-control" id="alias" name="alias" placeholder="Ej: Monto Extra" required>
                                    <div class="form-text">Nombre que se mostrará al usuario</div>
                                    <div class="invalid-feedback" id="alias-error"></div>
                                </div>
                            </div>
                        </div>
                        
                                                 <div class="row">
                             <div class="col-md-6">
                                 <div class="mb-3">
                                     <label for="tipo_campo" class="form-label">Tipo de Campo *</label>
                                     <select class="form-select" id="tipo_campo" name="tipo_campo" required>
                                         <option value="">Seleccionar tipo...</option>
                                         <option value="texto">Texto</option>
                                         <option value="numero">Número</option>
                                         <option value="fecha">Fecha</option>
                                         <option value="selector">Selector</option>
                                         <option value="cuota">Cuota</option>
                                     </select>
                                     <div class="invalid-feedback" id="tipo_campo-error"></div>
                                 </div>
                             </div>
                             <div class="col-md-6">
                                 <div class="mb-3">
                                     <label for="orden" class="form-label">Orden</label>
                                     <input type="number" class="form-control" id="orden" name="orden" min="1" value="1">
                                     <div class="form-text">Orden de aparición en formularios</div>
                                     <div class="invalid-feedback" id="orden-error"></div>
                                 </div>
                             </div>
                         </div>
                         
                                                                                                       <div class="row">
                               <div class="col-md-6">
                                   <div class="mb-3">
                                       <label for="requerido" class="form-label">Requerido</label>
                                       <select class="form-select" id="requerido" name="requerido">
                                           <option value="0">No</option>
                                           <option value="1">Sí</option>
                                       </select>
                                       <div class="invalid-feedback" id="requerido-error"></div>
                                   </div>
                               </div>
                           </div>
                           
                                                       <!-- Campo Monto Transaccional - Solo para tipo número -->
                            <div class="row" id="monto-transaccional-container" style="display: none;">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="monto_transaccional" class="form-label">Monto Transaccional</label>
                                        <select class="form-select" id="monto_transaccional" name="monto_transaccional">
                                            <option value="0">No</option>
                                            <option value="1">Sí</option>
                                        </select>
                                        <div class="form-text">Campo que se usará como monto del credito</div>
                                        <div class="invalid-feedback" id="monto_transaccional-error"></div>
                                    </div>
                                </div>
                            </div>
                           
                           <!-- Campo Fecha de Ejecución - Solo para tipo fecha -->
                           <div class="row" id="fecha-ejecucion-container" style="display: none;">
                               <div class="col-md-6">
                                   <div class="mb-3">
                                       <label for="fecha_ejecucion" class="form-label">Fecha de Ejecución</label>
                                       <select class="form-select" id="fecha_ejecucion" name="fecha_ejecucion">
                                           <option value="0">No</option>
                                           <option value="1">Sí</option>
                                       </select>
                                       <div class="form-text">Usar como fecha de cobro (solo para campos de fecha)</div>
                                       <div class="invalid-feedback" id="fecha_ejecucion-error"></div>
                                   </div>
                               </div>
                           </div>
                        
                                                 <div class="mb-3" id="opciones-container" style="display: none;">
                             <label for="opciones" class="form-label">Opciones del Selector</label>
                             <textarea class="form-control" id="opciones" name="opciones" rows="3" placeholder="Una opción por línea&#10;Ej:&#10;Opción 1&#10;Opción 2&#10;Opción 3"></textarea>
                             <div class="form-text">Una opción por línea. Solo para campos tipo "Selector"</div>
                             <div class="invalid-feedback" id="opciones-error"></div>
                         </div>
                         
                         <div class="mb-3" id="cuota-config-container" style="display: none;">
                             <h6 class="mb-3">Configuración de Cuotas</h6>
                             <div class="row">
                                 <div class="col-md-6">
                                     <div class="mb-3">
                                         <label for="numero_cuotas" class="form-label">Número de Cuotas *</label>
                                         <input type="number" class="form-control" id="numero_cuotas" name="numero_cuotas" min="1" max="100" placeholder="Ej: 12">
                                         <div class="form-text">Cantidad total de cuotas disponibles</div>
                                         <div class="invalid-feedback" id="numero_cuotas-error"></div>
                                     </div>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="mb-3">
                                         <label for="tasa_porcentaje" class="form-label">Tasa de Interés (%) *</label>
                                         <input type="number" class="form-control" id="tasa_porcentaje" name="tasa_porcentaje" min="0" max="100" step="0.01" placeholder="Ej: 15.5">
                                         <div class="form-text">Porcentaje de interés anual</div>
                                         <div class="invalid-feedback" id="tasa_porcentaje-error"></div>
                                     </div>
                                 </div>
                             </div>
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
    <div class="modal fade" id="deleteCampoModal" tabindex="-1" aria-labelledby="deleteCampoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCampoModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>¡Atención!</strong> Esta acción eliminará el campo de la tabla dinámica.
                    </div>
                    <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteCampo">Eliminar</button>
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
        let tipoCreditoId = {{ request()->route('id') }};

                 // Función para verificar si ya existe una fecha de ejecución
         function verificarFechaEjecucionExistente() {
             // Remover advertencia existente primero
             $('#advertencia-fecha').remove();
             
             // Verificar en la tabla actual si ya hay una fecha de ejecución
             let existeFechaEjecucion = false;
             $('#campos-table').DataTable().rows().data().each(function(row) {
                 if (row.fecha_ejecucion == 1) {
                     existeFechaEjecucion = true;
                 }
             });

             if (existeFechaEjecucion) {
                 // Mostrar advertencia solo si no existe ya
                 if ($('#advertencia-fecha').length === 0) {
                     $('#fecha_ejecucion').closest('.mb-3').append(
                         '<div class="alert alert-warning alert-sm mt-2" id="advertencia-fecha">' +
                         '<i class="fas fa-exclamation-triangle"></i> ' +
                         'Ya existe un campo de fecha de ejecución. Solo puede haber uno por tipo de crédito.' +
                         '</div>'
                     );
                 }
                 $('#fecha_ejecucion').val('0'); // Forzar a "No"
             }
         }

         // Función para verificar fecha de ejecución existente en modo edición
         function verificarFechaEjecucionExistenteEnEdicion(campoIdActual) {
             // Remover advertencia existente primero
             $('#advertencia-fecha-edicion').remove();
             
             // Verificar en la tabla actual si ya hay otra fecha de ejecución (excluyendo el actual)
             let existeOtraFechaEjecucion = false;
             $('#campos-table').DataTable().rows().data().each(function(row) {
                 if (row.fecha_ejecucion == 1 && row.id != campoIdActual) {
                     existeOtraFechaEjecucion = true;
                 }
             });

             if (existeOtraFechaEjecucion) {
                 // Mostrar advertencia solo si no existe ya
                 if ($('#advertencia-fecha-edicion').length === 0) {
                     $('#fecha_ejecucion').closest('.mb-3').append(
                         '<div class="alert alert-warning alert-sm mt-2" id="advertencia-fecha-edicion">' +
                         '<i class="fas fa-exclamation-triangle"></i> ' +
                         'Ya existe otro campo de fecha de ejecución. Solo puede haber uno por tipo de crédito.' +
                         '</div>'
                     );
                 }
                 // No forzar el valor, solo mostrar advertencia
             }
         }

         // Función para verificar si ya existe un monto transaccional
         function verificarMontoTransaccionalExistente() {
             // Remover advertencia existente primero
             $('#advertencia-monto').remove();
             
             // Verificar en la tabla actual si ya hay un monto transaccional
             let existeMontoTransaccional = false;
             $('#campos-table').DataTable().rows().data().each(function(row) {
                 if (row.monto_transaccional == 1) {
                     existeMontoTransaccional = true;
                 }
             });

             if (existeMontoTransaccional) {
                 // Mostrar advertencia solo si no existe ya
                 if ($('#advertencia-monto').length === 0) {
                     $('#monto_transaccional').closest('.mb-3').append(
                         '<div class="alert alert-warning alert-sm mt-2" id="advertencia-monto">' +
                         '<i class="fas fa-exclamation-triangle"></i> ' +
                         'Ya existe un campo de monto transaccional. Solo puede haber uno por tipo de crédito.' +
                         '</div>'
                     );
                 }
                 $('#monto_transaccional').val('0'); // Forzar a "No"
             }
         }

         // Función para verificar monto transaccional existente en modo edición
         function verificarMontoTransaccionalExistenteEnEdicion(campoIdActual) {
             // Remover advertencia existente primero
             $('#advertencia-monto-edicion').remove();
             
             // Verificar en la tabla actual si ya hay otro monto transaccional (excluyendo el actual)
             let existeOtroMontoTransaccional = false;
             $('#campos-table').DataTable().rows().data().each(function(row) {
                 if (row.monto_transaccional == 1 && row.id != campoIdActual) {
                     existeOtroMontoTransaccional = true;
                 }
             });

             if (existeOtroMontoTransaccional) {
                 // Mostrar advertencia solo si no existe ya
                 if ($('#advertencia-monto-edicion').length === 0) {
                     $('#monto_transaccional').closest('.mb-3').append(
                         '<div class="alert alert-warning alert-sm mt-2" id="advertencia-monto-edicion">' +
                         '<i class="fas fa-exclamation-triangle"></i> ' +
                         'Ya existe otro campo de monto transaccional. Solo puede haber uno por tipo de crédito.' +
                         '</div>'
                     );
                 }
                 // No forzar el valor, solo mostrar advertencia
             }
         }

         $(document).ready(function() {
             // Cargar información del tipo de crédito
             cargarInformacionTipoCredito();
             
             // Inicializar DataTable
             table = $('#campos-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '/tipos-creditos/' + tipoCreditoId + '/campos/data',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                                                                   columns: [
                      { data: 'orden' },
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
                          data: 'monto_transaccional',
                          render: function(data) {
                              return data == 1 ? '<span class="badge bg-info">Monto Trans.</span>' : '<span class="badge bg-secondary">No</span>';
                          }
                      },
                      { 
                          data: 'fecha_ejecucion',
                          render: function(data) {
                              return data == 1 ? '<span class="badge bg-warning">Sí</span>' : '<span class="badge bg-secondary">No</span>';
                          }
                      },
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
                order: [[0, 'asc']],
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

                                                                // Mostrar/ocultar opciones según el tipo de campo
               $('#tipo_campo').on('change', function() {
                   const tipoSeleccionado = $(this).val();
                   
                                       // Ocultar todos los contenedores específicos
                    $('#opciones-container').hide();
                    $('#cuota-config-container').hide();
                    $('#monto-transaccional-container').hide();
                    $('#fecha-ejecucion-container').hide();
                   
                   // Mostrar el contenedor correspondiente
                   if (tipoSeleccionado === 'selector') {
                       $('#opciones-container').show();
                   } else if (tipoSeleccionado === 'cuota') {
                       $('#cuota-config-container').show();
                                       } else if (tipoSeleccionado === 'numero') {
                        $('#monto-transaccional-container').show();
                        verificarMontoTransaccionalExistente();
                    } else if (tipoSeleccionado === 'fecha') {
                       $('#fecha-ejecucion-container').show();
                       verificarFechaEjecucionExistente();
                   }
               });

                               // Evento para validar fecha de ejecución en modo edición
                $('#fecha_ejecucion').on('change', function() {
                    if (isEditMode && $('#tipo_campo').val() === 'fecha') {
                        verificarFechaEjecucionExistenteEnEdicion($('#campo_id').val());
                    }
                });

                // Evento para validar monto transaccional en modo edición
                $('#monto_transaccional').on('change', function() {
                    if (isEditMode && $('#tipo_campo').val() === 'numero') {
                        verificarMontoTransaccionalExistenteEnEdicion($('#campo_id').val());
                    }
                });

               // Transformar nombre del campo automáticamente
               $('#nombre_campo').on('input', function() {
                 let valor = $(this).val();
                 valor = valor.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
                 $(this).val(valor);
             });
        });

        // Función para cargar información del tipo de crédito
        function cargarInformacionTipoCredito() {
            $.ajax({
                url: '/tipos-creditos/' + tipoCreditoId + '/edit',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        const tipo = response.data;
                        $('#nombre-tipo-credito').text(tipo.nombre);
                        $('#nombre-tabla').text('credito_' + tipo.identificador);
                    }
                }
            });
        }



        // Función para agregar nuevo campo
        function agregarCampo() {
            isEditMode = false;
            $('#campoModalLabel').text('Nuevo Campo');
            $('#campo_id').val('');
            $('#form_method').val('POST');
            
                                                                                                       // Limpiar formulario
               $('#campoForm')[0].reset();
               $('.is-invalid').removeClass('is-invalid');
               $('.invalid-feedback').text('');
               $('#opciones-container').hide();
               $('#cuota-config-container').hide();
               $('#monto-transaccional-container').hide();
               $('#fecha-ejecucion-container').hide();
               $('#advertencia-fecha').remove(); // Remover advertencia si existe
               $('#advertencia-fecha-edicion').remove(); // Remover advertencia de edición si existe
            
            $('#campoModal').modal('show');
        }

                 // Función para editar campo
         function editarCampo(id) {
             console.log('Editando campo ID:', id);
             console.log('Tipo Credito ID:', tipoCreditoId);
             isEditMode = true;
             campoToEdit = id;
             
             $.ajax({
                 url: '/tipos-creditos/' + tipoCreditoId + '/campos/' + id + '/edit',
                 type: 'GET',
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                 success: function(response) {
                     console.log('Respuesta del servidor:', response);
                     if (response.success) {
                         const campo = response.data;
                         console.log('Datos del campo:', campo);
                         
                         // Limpiar formulario primero
                         $('#campoForm')[0].reset();
                         $('.is-invalid').removeClass('is-invalid');
                         $('.invalid-feedback').text('');
                         $('#advertencia-fecha').remove();
                         $('#advertencia-fecha-edicion').remove();
                         
                         // Ocultar todos los contenedores
                         $('#opciones-container').hide();
                         $('#cuota-config-container').hide();
                         $('#monto-transaccional-container').hide();
                         $('#fecha-ejecucion-container').hide();
                         
                         // Llenar el formulario
                         $('#campoModalLabel').text('Editar Campo');
                         $('#campo_id').val(campo.id);
                         $('#form_method').val('PUT');
                         $('#nombre_campo').val(campo.nombre_campo);
                         $('#alias').val(campo.alias);
                         $('#tipo_campo').val(campo.tipo_campo);
                         $('#orden').val(campo.orden);
                                                   $('#requerido').val(campo.requerido);
                          $('#monto_transaccional').val(campo.monto_transaccional);
                          $('#fecha_ejecucion').val(campo.fecha_ejecucion);
                         $('#valor_por_defecto').val(campo.valor_por_defecto);
                         $('#opciones').val(campo.opciones);
                         
                         // Mostrar/ocultar opciones según el tipo
                         if (campo.tipo_campo === 'selector') {
                             $('#opciones-container').show();
                         } else if (campo.tipo_campo === 'cuota') {
                             $('#cuota-config-container').show();
                             // Llenar los campos de cuota si existen
                             if (campo.numero_cuotas) {
                                 $('#numero_cuotas').val(campo.numero_cuotas);
                             }
                             if (campo.tasa_porcentaje) {
                                 $('#tasa_porcentaje').val(campo.tasa_porcentaje);
                             }
                                                   } else if (campo.tipo_campo === 'numero') {
                              $('#monto-transaccional-container').show();
                          } else if (campo.tipo_campo === 'fecha') {
                             $('#fecha-ejecucion-container').show();
                             verificarFechaEjecucionExistenteEnEdicion(campo.id);
                         }
                         
                         $('#campoModal').modal('show');
                     } else {
                         showError('Error', response.message);
                     }
                 },
                 error: function(xhr) {
                     console.error('Error en AJAX:', xhr);
                     showError('Error', 'No se pudo cargar el campo');
                     // Abrir modal de todas formas para debug
                     $('#campoModal').modal('show');
                 }
             });
         }

        // Función para eliminar campo
        function eliminarCampo(id) {
            campoToDelete = id;
            $('#deleteCampoModal').modal('show');
        }

        // Guardar campo (crear o editar)
        $('#saveCampo').click(function() {
            const formData = new FormData($('#campoForm')[0]);
            const url = isEditMode ? '/tipos-creditos/' + tipoCreditoId + '/campos/' + campoToEdit : '/tipos-creditos/' + tipoCreditoId + '/campos';
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
                        $('#campoModal').modal('hide');
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
                        showError('Error', 'No se pudo guardar el campo');
                    }
                }
            });
        });

        // Confirmar eliminación de campo
        $('#confirmDeleteCampo').click(function() {
            if (campoToDelete) {
                $.ajax({
                    url: '/tipos-creditos/' + tipoCreditoId + '/campos/' + campoToDelete,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showSuccess('Éxito', response.message);
                            $('#deleteCampoModal').modal('hide');
                            table.ajax.reload();
                        } else {
                            showError('Error', response.message);
                        }
                    },
                    error: function(xhr) {
                        showError('Error', 'No se pudo eliminar el campo');
                    }
                });
            }
            $('#deleteCampoModal').modal('hide');
            campoToDelete = null;
        });
    </script>
    @endpush
</x-app-layout>
