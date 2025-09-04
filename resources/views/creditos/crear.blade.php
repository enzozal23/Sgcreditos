@extends('layouts.app')

@section('content')
    
    @php
        $breadcrumbs = [
            ['title' => 'Créditos', 'url' => route('creditos.index')],
            ['title' => 'Tipos de Créditos', 'url' => route('creditos.index')],
            ['title' => $tipoCredito->nombre, 'url' => route('tipos.creditos.creditos.listado', $tipoCredito->id)],
            ['title' => 'Crear Nuevo Crédito', 'url' => '#']
        ];
    @endphp
    
    <div class="row">
        <div class="col-md-12">
            <div class="card bg-white border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-plus me-2"></i>Crear Nuevo Crédito
                        </h5>
                        <small class="text-muted">Tipo: <span class="fw-bold">{{ $tipoCredito->nombre }}</span> | Tabla: <span class="badge bg-info">{{ $tipoCredito->tabla_credito }}</span></small>
                    </div>
                    <div>
                        <a href="{{ route('tipos.creditos.creditos.listado', $tipoCredito->id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
                <div class="card-body bg-white">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Información del Crédito</h6>
                        <p class="mb-0"><small>Completa todos los campos requeridos para crear un nuevo crédito del tipo "{{ $tipoCredito->nombre }}". Los campos marcados con <span class="text-danger">*</span> son obligatorios.</small></p>
                    </div>
                    
                                         <form id="creditoForm" method="POST" action="{{ route('tipos.creditos.creditos.store', $tipoCredito->id) }}">
                         @csrf
                         <input type="hidden" name="tipo_credito_id" value="{{ $tipoCredito->id }}">
                         <input type="hidden" name="cliente_id" id="cliente_id" value="">
                         
                         <!-- Sección de Selección de Cliente -->
                         <div class="row mb-4">
                             <div class="col-12">
                                 <div class="card border-primary">
                                     <div class="card-header bg-primary text-white">
                                         <h6 class="mb-0">
                                             <i class="fas fa-user me-2"></i>Selección de Cliente
                                         </h6>
                                     </div>
                                     <div class="card-body">
                                         <div class="row">
                                             <div class="col-md-6 mb-3">
                                                 <label for="tipo_cliente_id" class="form-label">
                                                     Tipo de Cliente <span class="text-danger">*</span>
                                                 </label>
                                                 <select class="form-control" id="tipo_cliente_id" name="tipo_cliente_id" required>
                                                     <option value="">Seleccione un tipo de cliente</option>
                                                 </select>
                                                 <div class="form-text">Seleccione el tipo de cliente para filtrar la búsqueda</div>
                                             </div>
                                             <div class="col-md-6 mb-3">
                                                 <label for="cliente_search" class="form-label">
                                                     Buscar Cliente <span class="text-danger">*</span>
                                                 </label>
                                                 <div class="input-group">
                                                                                                           <input type="text" 
                                                             class="form-control" 
                                                             id="cliente_search" 
                                                             placeholder="Buscar por DNI, nombre, apellido, etc."
                                                             autocomplete="off">
                                                     <button class="btn btn-outline-secondary" type="button" id="btnBuscarCliente">
                                                         <i class="fas fa-search"></i>
                                                     </button>
                                                 </div>
                                                                                                   <div class="form-text">Ingrese el DNI (campo único) o busque por nombre/apellido</div>
                                             </div>
                                         </div>
                                         
                                         <!-- Resultados de búsqueda -->
                                         <div id="resultados_busqueda" class="mt-3" style="display: none;">
                                             <div class="table-responsive">
                                                 <table class="table table-sm table-hover">
                                                                                                                                                                 <thead class="table-light">
                                                           <tr>
                                                               <th>Cargando...</th>
                                                           </tr>
                                                       </thead>
                                                     <tbody id="tabla_clientes">
                                                     </tbody>
                                                 </table>
                                             </div>
                                         </div>
                                         
                                         <!-- Cliente seleccionado -->
                                         <div id="cliente_seleccionado" class="mt-3" style="display: none;">
                                             <!-- El contenido se genera dinámicamente -->
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         
                         <!-- Sección de Campos del Crédito -->
                         <div class="row mb-4">
                             <div class="col-12">
                                 <div class="card border-info">
                                     <div class="card-header bg-info text-white">
                                         <h6 class="mb-0">
                                             <i class="fas fa-credit-card me-2"></i>Campos del Crédito
                                         </h6>
                                     </div>
                                     <div class="card-body">
                                         <!-- Campos dinámicos -->
                                         <div class="row" id="campos-container">
                                             <!-- Los campos se cargarán dinámicamente -->
                                             <div class="col-12 text-center">
                                                 <div class="spinner-border text-primary" role="status">
                                                     <span class="visually-hidden">Cargando campos...</span>
                                                 </div>
                                                 <p class="mt-2">Cargando campos del tipo de crédito...</p>
                                             </div>
                                         </div>
                                         
                                         <hr class="my-4">
                                         
                                         <!-- Tipo de Amortización -->
                                         <div class="row mb-4">
                                             <div class="col-md-6 mb-3">
                                                 <label for="tipo_amortizacion_id" class="form-label">
                                                     Tipo de Amortización <span class="text-danger">*</span>
                                                 </label>
                                                 <select class="form-control" id="tipo_amortizacion_id" name="tipo_amortizacion_id" required>
                                                     <option value="">Seleccione un tipo de amortización</option>
                                                 </select>
                                                 <div class="form-text">Seleccione el sistema de amortización para calcular las cuotas</div>
                                             </div>
                                             <div class="col-md-6 mb-3">
                                                 <div id="info_amortizacion" class="mt-4 d-block">
                                                     <div class="alert alert-info no-fade-alert">
                                                         <h6 class="mb-2">
                                                             <i class="fas fa-info-circle me-2"></i>Información del Tipo de Amortización
                                                         </h6>
                                                         <div id="descripcion_amortizacion">
                                                             <p class="mb-0"><em>Seleccione un tipo de amortización para ver su información.</em></p>
                                                         </div>
                                                         <div id="formula_amortizacion" class="mt-2"></div>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">
                                        <i class="fas fa-times me-1"></i>Cancelar
                                    </button>
                                    <button type="button" class="btn btn-info me-2" id="btnSimularCredito" onclick="simularCredito()">
                                        <i class="fas fa-calculator me-1"></i>Simular Crédito
                                    </button>
                                    <button type="button" class="btn btn-warning me-2" id="btnGuardarPendiente" onclick="guardarCreditoPendiente()">
                                        <i class="fas fa-clock me-1"></i>Guardar como Pendiente
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="btnGuardar">
                                        <i class="fas fa-save me-1"></i>Guardar Crédito
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let tipoCreditoId = {{ $tipoCredito->id }};
        let campos = [];
        
        $(document).ready(function() {
            cargarTiposCliente();
            cargarTiposAmortizacion();
            cargarCampos();
            
            // Asegurar que el panel de amortización esté visible con Bootstrap
            $('#info_amortizacion').removeClass('d-none').addClass('d-block');
            mostrarInfoAmortizacion();
            
            // Manejar envío del formulario
            $('#creditoForm').on('submit', function(e) {
                e.preventDefault();
                guardarCredito();
            });
            
            // Eventos para búsqueda de cliente
            $('#btnBuscarCliente').click(function() {
                buscarCliente();
            });
            
            $('#cliente_search').keypress(function(e) {
                if (e.which == 13) { // Enter key
                    buscarCliente();
                }
            });
            
            // Cambio de tipo de cliente
            $('#tipo_cliente_id').change(function() {
                console.log('🔄 NUEVO: Cambio de tipo de cliente detectado');
                
                // Solo limpiar búsqueda si no hay cliente seleccionado
                if (!$('#cliente_id').val()) {
                    console.log('🧹 NUEVO: No hay cliente seleccionado, limpiando búsqueda');
                    $('#cliente_search').val('');
                    $('#resultados_busqueda').hide();
                } else {
                    console.log('✅ NUEVO: Hay cliente seleccionado, manteniendo selección');
                    // Asegurar que el cliente seleccionado permanezca visible
                    $('#cliente_seleccionado').show();
                }
            });
            
            // Cambio de tipo de amortización
            $('#tipo_amortizacion_id').change(function() {
                mostrarInfoAmortizacion();
            });
        });
        
        function cargarTiposCliente() {
            $.ajax({
                url: '/tipos-clientes/data',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        let html = '<option value="">Seleccione un tipo de cliente</option>';
                        response.data.forEach(function(tipo) {
                            html += `<option value="${tipo.id}">${tipo.nombre}</option>`;
                        });
                        $('#tipo_cliente_id').html(html);
                    }
                },
                error: function(xhr) {
                    console.log('Error al cargar tipos de cliente:', xhr);
                }
            });
        }
        
        function buscarCliente() {
            const tipoClienteId = $('#tipo_cliente_id').val();
            const searchTerm = $('#cliente_search').val().trim();
            
            if (!tipoClienteId) {
                showError('Error', 'Debe seleccionar un tipo de cliente primero');
                return;
            }
            
            if (!searchTerm) {
                showError('Error', 'Debe ingresar un término de búsqueda');
                return;
            }
            
            // Mostrar loading
            $('#btnBuscarCliente').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            
            $.ajax({
                url: '/clientes/buscar',
                type: 'GET',
                data: {
                    tipo_cliente_id: tipoClienteId,
                    search: searchTerm
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#btnBuscarCliente').prop('disabled', false).html('<i class="fas fa-search"></i>');
                    
                    if (response.data && response.data.length > 0) {
                        mostrarResultadosBusqueda(response.data, response.campo_unico, response.campos_configurados);
                    } else {
                        showInfo('Búsqueda', 'No se encontraron clientes con los criterios especificados');
                        $('#resultados_busqueda').hide();
                    }
                },
                error: function(xhr) {
                    $('#btnBuscarCliente').prop('disabled', false).html('<i class="fas fa-search"></i>');
                    showError('Error', 'Error al buscar clientes');
                }
            });
        }
        
        function mostrarResultadosBusqueda(clientes, campoUnico, camposConfigurados) {
            // Generar encabezados dinámicamente
            let headersHtml = '';
            let colspan = 1; // Incluir columna de acción
            
            // Agregar encabezado para el campo único o ID
            if (campoUnico) {
                headersHtml += `<th>${campoUnico.toUpperCase()}</th>`;
                colspan++;
            } else {
                headersHtml += `<th>ID</th>`;
                colspan++;
            }
            
            // Agregar encabezados para cada campo configurado
            camposConfigurados.forEach(function(campo) {
                if (!campoUnico || campo.nombre_campo !== campoUnico) {
                    headersHtml += `<th>${campo.alias}</th>`;
                    colspan++;
                }
            });
            
            // Agregar columna de acción
            headersHtml += `<th>Acción</th>`;
            
            // Actualizar los encabezados de la tabla
            $('#tabla_clientes').closest('table').find('thead tr').html(headersHtml);
            
            let html = '';
            
            // Mostrar información sobre el campo único si está disponible
            if (campoUnico) {
                html += `
                    <tr class="table-info">
                        <td colspan="${colspan}" class="text-center">
                            <small><i class="fas fa-info-circle me-1"></i>Búsqueda priorizada en campo único: <strong>${campoUnico}</strong></small>
                        </td>
                    </tr>
                `;
            }
            
            clientes.forEach(function(cliente) {
                let rowHtml = '';
                
                // Agregar valor del campo único o ID
                if (campoUnico) {
                    let campoUnicoValor = cliente[campoUnico] || '-';
                    
                                         // Formatear campo único numérico para que no muestre decimales
                     if (campoUnicoValor !== '-' && !isNaN(campoUnicoValor) && Number.isInteger(parseFloat(campoUnicoValor))) {
                         campoUnicoValor = parseInt(campoUnicoValor);
                     }
                    
                    rowHtml += `<td>${campoUnicoValor}</td>`;
                } else {
                    rowHtml += `<td>${cliente.id}</td>`;
                }
                
                // Agregar valores para cada campo configurado
                camposConfigurados.forEach(function(campo) {
                    if (!campoUnico || campo.nombre_campo !== campoUnico) {
                        let valor = cliente[campo.nombre_campo] || '-';
                        
                                                 // Formatear campos numéricos para que no muestren decimales
                         if (valor !== '-' && !isNaN(valor) && Number.isInteger(parseFloat(valor))) {
                             valor = parseInt(valor);
                         }
                        
                        rowHtml += `<td>${valor}</td>`;
                    }
                });
                
                // Agregar botón de acción
                rowHtml += `
                    <td>
                        <button type="button" class="btn btn-sm btn-success" onclick="seleccionarCliente(${cliente.id}, '${cliente.nombre || ''}', '${cliente.dni || ''}', '${cliente.email || ''}', '${cliente.telefono || ''}')">
                            <i class="fas fa-check"></i> Seleccionar
                        </button>
                    </td>
                `;
                
                html += `<tr>${rowHtml}</tr>`;
            });
            
            $('#tabla_clientes').html(html);
            $('#resultados_busqueda').show();
        }
        
        function seleccionarCliente(id, nombre, dniCuit, email, telefono) {
            console.log('🔄 NUEVO: seleccionarCliente llamado con:', {id, nombre, dniCuit, email, telefono});
            
            // Establecer ID del cliente
            $('#cliente_id').val(id);
            
            // Crear HTML del cliente seleccionado con módulo de contacto
            let clienteHtml = `
                <div class="alert alert-success mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><i class="fas fa-user-check me-2"></i>Cliente Seleccionado</h6>
                            <div class="small">
                                <strong>ID:</strong> ${id} | 
                                <strong>Nombre:</strong> ${nombre || 'N/A'} | 
                                <strong>DNI:</strong> ${dniCuit || 'N/A'} | 
                                <strong>Teléfono:</strong> ${telefono || 'N/A'}
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cambiarCliente()">
                            <i class="fas fa-edit me-1"></i>Cambiar
                        </button>
                    </div>
                </div>
                
                <!-- Módulo de Confirmar Contacto -->
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-address-book me-2"></i>Confirmar Información de Contacto
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Importante:</strong> Confirme y complete la información de contacto del cliente antes de continuar.
                        </div>
                        
                        <!-- Teléfonos -->
                        <div class="mb-4">
                            <h6 class="text-primary">
                                <i class="fas fa-phone me-2"></i>Teléfonos
                                <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="agregarTelefono()">
                                    <i class="fas fa-plus me-1"></i>Agregar
                                </button>
                            </h6>
                            <div id="telefonos-container">
                                <!-- Los teléfonos se cargarán aquí -->
                            </div>
                        </div>
                        
                        <!-- Correos -->
                        <div class="mb-4">
                            <h6 class="text-success">
                                <i class="fas fa-envelope me-2"></i>Correos Electrónicos
                                <button type="button" class="btn btn-sm btn-outline-success ms-2" onclick="agregarCorreo()">
                                    <i class="fas fa-plus me-1"></i>Agregar
                                </button>
                            </h6>
                            <div id="correos-container">
                                <!-- Los correos se cargarán aquí -->
                            </div>
                        </div>
                        
                        <!-- Direcciones -->
                        <div class="mb-4">
                            <h6 class="text-info">
                                <i class="fas fa-map-marker-alt me-2"></i>Direcciones
                                <button type="button" class="btn btn-sm btn-outline-info ms-2" onclick="agregarDireccion()">
                                    <i class="fas fa-plus me-1"></i>Agregar
                                </button>
                            </h6>
                            <div id="direcciones-container">
                                <!-- Las direcciones se cargarán aquí -->
                            </div>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" onclick="cambiarCliente()">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="confirmarContacto()">
                                <i class="fas fa-check me-1"></i>Confirmar y Continuar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Reemplazar completamente el contenido del contenedor
            $('#cliente_seleccionado').html(clienteHtml);
            
            // Mostrar el contenedor
            $('#cliente_seleccionado').show();
            
            // Ocultar resultados de búsqueda
            $('#resultados_busqueda').hide();
            
            // Cargar información de contacto existente
            cargarInformacionContacto(id);
            
            console.log('✅ NUEVO: Cliente seleccionado y módulo de contacto abierto');
        }
        
        function cambiarCliente() {
            console.log('🔄 NUEVO: cambiarCliente llamado');
            
            // Limpiar selección actual
            $('#cliente_id').val('');
            $('#cliente_seleccionado').hide();
            $('#cliente_search').val('');
            $('#resultados_busqueda').hide();
            
            // Enfocar el campo de búsqueda
            $('#cliente_search').focus();
            
            console.log('✅ NUEVO: Cliente cambiado, listo para nueva selección');
        }
        
        // Variables para controlar los contadores
        let telefonoCounter = 0;
        let correoCounter = 0;
        let direccionCounter = 0;
        
        function cargarInformacionContacto(clienteId) {
            console.log('📞 Cargando información de contacto para cliente:', clienteId);
            
            // Cargar contactos existentes del cliente
            $.ajax({
                url: '/clientes/obtener-contactos',
                type: 'GET',
                data: {
                    cliente_id: clienteId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        console.log('✅ Contactos cargados:', response.data);
                        
                        // Cargar teléfonos existentes
                        if (response.data.telefonos && response.data.telefonos.length > 0) {
                            response.data.telefonos.forEach(function(telefono) {
                                agregarTelefonoConDatos(telefono);
                            });
                        } else {
                            // Si no hay teléfonos, agregar uno vacío
                            agregarTelefono();
                        }
                        
                        // Cargar correos existentes
                        if (response.data.correos && response.data.correos.length > 0) {
                            response.data.correos.forEach(function(correo) {
                                agregarCorreoConDatos(correo);
                            });
                        }
                        
                        // Cargar direcciones existentes
                        if (response.data.direcciones && response.data.direcciones.length > 0) {
                            response.data.direcciones.forEach(function(direccion) {
                                agregarDireccionConDatos(direccion);
                            });
                        }
                    } else {
                        console.log('⚠️ No se pudieron cargar los contactos, agregando campos vacíos');
                        agregarTelefono();
                    }
                },
                error: function(xhr) {
                    console.log('❌ Error al cargar contactos:', xhr);
                    // En caso de error, agregar campos vacíos
                    agregarTelefono();
                }
            });
        }
        
        function agregarTelefono() {
            telefonoCounter++;
            const telefonoHtml = `
                <div class="row mb-2" id="telefono-${telefonoCounter}">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Número de teléfono" name="telefonos[${telefonoCounter}][numero]">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="telefonos[${telefonoCounter}][tipo]">
                            <option value="celular">Celular</option>
                            <option value="fijo">Fijo</option>
                            <option value="trabajo">Trabajo</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="telefonos[${telefonoCounter}][es_principal]" value="1">
                            <label class="form-check-label">Principal</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarTelefono(${telefonoCounter})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#telefonos-container').append(telefonoHtml);
        }
        
        function agregarTelefonoConDatos(telefono) {
            telefonoCounter++;
            const telefonoHtml = `
                <div class="row mb-2" id="telefono-${telefonoCounter}">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Número de teléfono" name="telefonos[${telefonoCounter}][numero]" value="${telefono.numero || ''}">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="telefonos[${telefonoCounter}][tipo]">
                            <option value="celular" ${telefono.tipo === 'celular' ? 'selected' : ''}>Celular</option>
                            <option value="fijo" ${telefono.tipo === 'fijo' ? 'selected' : ''}>Fijo</option>
                            <option value="trabajo" ${telefono.tipo === 'trabajo' ? 'selected' : ''}>Trabajo</option>
                            <option value="otro" ${telefono.tipo === 'otro' ? 'selected' : ''}>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="telefonos[${telefonoCounter}][es_principal]" value="1" ${telefono.es_principal ? 'checked' : ''}>
                            <label class="form-check-label">Principal</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarTelefono(${telefonoCounter})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#telefonos-container').append(telefonoHtml);
        }
        
        function eliminarTelefono(id) {
            $(`#telefono-${id}`).remove();
        }
        
        function agregarCorreo() {
            correoCounter++;
            const correoHtml = `
                <div class="row mb-2" id="correo-${correoCounter}">
                    <div class="col-md-4">
                        <input type="email" class="form-control" placeholder="Correo electrónico" name="correos[${correoCounter}][email]">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="correos[${correoCounter}][tipo]">
                            <option value="personal">Personal</option>
                            <option value="trabajo">Trabajo</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="correos[${correoCounter}][es_principal]" value="1">
                            <label class="form-check-label">Principal</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarCorreo(${correoCounter})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#correos-container').append(correoHtml);
        }
        
        function agregarCorreoConDatos(correo) {
            correoCounter++;
            const correoHtml = `
                <div class="row mb-2" id="correo-${correoCounter}">
                    <div class="col-md-4">
                        <input type="email" class="form-control" placeholder="Correo electrónico" name="correos[${correoCounter}][email]" value="${correo.email || ''}">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="correos[${correoCounter}][tipo]">
                            <option value="personal" ${correo.tipo === 'personal' ? 'selected' : ''}>Personal</option>
                            <option value="trabajo" ${correo.tipo === 'trabajo' ? 'selected' : ''}>Trabajo</option>
                            <option value="otro" ${correo.tipo === 'otro' ? 'selected' : ''}>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="correos[${correoCounter}][es_principal]" value="1" ${correo.es_principal ? 'checked' : ''}>
                            <label class="form-check-label">Principal</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarCorreo(${correoCounter})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#correos-container').append(correoHtml);
        }
        
        function eliminarCorreo(id) {
            $(`#correo-${id}`).remove();
        }
        
        function agregarDireccion() {
            direccionCounter++;
            const direccionHtml = `
                <div class="row mb-3" id="direccion-${direccionCounter}">
                    <div class="col-md-3">
                        <select class="form-control" name="direcciones[${direccionCounter}][tipo]">
                            <option value="domicilio">Domicilio</option>
                            <option value="trabajo">Trabajo</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Calle" name="direcciones[${direccionCounter}][calle]">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Número" name="direcciones[${direccionCounter}][numero]">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Piso/Depto" name="direcciones[${direccionCounter}][piso]">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDireccion(${direccionCounter})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="col-md-3 mt-2">
                        <input type="text" class="form-control" placeholder="Código Postal" name="direcciones[${direccionCounter}][codigo_postal]">
                    </div>
                    <div class="col-md-4 mt-2">
                        <input type="text" class="form-control" placeholder="Ciudad" name="direcciones[${direccionCounter}][ciudad]">
                    </div>
                    <div class="col-md-4 mt-2">
                        <input type="text" class="form-control" placeholder="Provincia" name="direcciones[${direccionCounter}][provincia]">
                    </div>
                    <div class="col-md-1 mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="direcciones[${direccionCounter}][es_principal]" value="1">
                            <label class="form-check-label">Principal</label>
                        </div>
                    </div>
                </div>
            `;
            $('#direcciones-container').append(direccionHtml);
        }
        
        function agregarDireccionConDatos(direccion) {
            direccionCounter++;
            const direccionHtml = `
                <div class="row mb-3" id="direccion-${direccionCounter}">
                    <div class="col-md-3">
                        <select class="form-control" name="direcciones[${direccionCounter}][tipo]">
                            <option value="domicilio" ${direccion.tipo === 'domicilio' ? 'selected' : ''}>Domicilio</option>
                            <option value="trabajo" ${direccion.tipo === 'trabajo' ? 'selected' : ''}>Trabajo</option>
                            <option value="otro" ${direccion.tipo === 'otro' ? 'selected' : ''}>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Calle" name="direcciones[${direccionCounter}][calle]" value="${direccion.calle || ''}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Número" name="direcciones[${direccionCounter}][numero]" value="${direccion.numero || ''}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Piso/Depto" name="direcciones[${direccionCounter}][piso]" value="${direccion.piso || ''}">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDireccion(${direccionCounter})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="col-md-3 mt-2">
                        <input type="text" class="form-control" placeholder="Código Postal" name="direcciones[${direccionCounter}][codigo_postal]" value="${direccion.codigo_postal || ''}">
                    </div>
                    <div class="col-md-4 mt-2">
                        <input type="text" class="form-control" placeholder="Ciudad" name="direcciones[${direccionCounter}][ciudad]" value="${direccion.ciudad || ''}">
                    </div>
                    <div class="col-md-4 mt-2">
                        <input type="text" class="form-control" placeholder="Provincia" name="direcciones[${direccionCounter}][provincia]" value="${direccion.provincia || ''}">
                    </div>
                    <div class="col-md-1 mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="direcciones[${direccionCounter}][es_principal]" value="1" ${direccion.es_principal ? 'checked' : ''}>
                            <label class="form-check-label">Principal</label>
                        </div>
                    </div>
                </div>
            `;
            $('#direcciones-container').append(direccionHtml);
        }
        
        function eliminarDireccion(id) {
            $(`#direccion-${id}`).remove();
        }
        
        function confirmarContacto() {
            console.log('✅ Confirmando información de contacto');
            
            // Recopilar datos de teléfonos
            let telefonos = [];
            $('#telefonos-container .row').each(function() {
                let numero = $(this).find('input[name*="[numero]"]').val();
                let tipo = $(this).find('select[name*="[tipo]"]').val();
                let esPrincipal = $(this).find('input[name*="[es_principal]"]').is(':checked');
                
                if (numero) {
                    telefonos.push({
                        numero: numero,
                        tipo: tipo,
                        es_principal: esPrincipal ? 1 : 0
                    });
                }
            });
            
            // Recopilar datos de correos
            let correos = [];
            $('#correos-container .row').each(function() {
                let email = $(this).find('input[name*="[email]"]').val();
                let tipo = $(this).find('select[name*="[tipo]"]').val();
                let esPrincipal = $(this).find('input[name*="[es_principal]"]').is(':checked');
                
                if (email) {
                    correos.push({
                        email: email,
                        tipo: tipo,
                        es_principal: esPrincipal ? 1 : 0
                    });
                }
            });
            
            // Recopilar datos de direcciones
            let direcciones = [];
            $('#direcciones-container .row').each(function() {
                let tipo = $(this).find('select[name*="[tipo]"]').val();
                let calle = $(this).find('input[name*="[calle]"]').val();
                let numero = $(this).find('input[name*="[numero]"]').val();
                let piso = $(this).find('input[name*="[piso]"]').val();
                let codigoPostal = $(this).find('input[name*="[codigo_postal]"]').val();
                let ciudad = $(this).find('input[name*="[ciudad]"]').val();
                let provincia = $(this).find('input[name*="[provincia]"]').val();
                let esPrincipal = $(this).find('input[name*="[es_principal]"]').is(':checked');
                
                if (calle || numero) {
                    direcciones.push({
                        tipo: tipo,
                        calle: calle,
                        numero: numero,
                        piso: piso,
                        codigo_postal: codigoPostal,
                        ciudad: ciudad,
                        provincia: provincia,
                        es_principal: esPrincipal ? 1 : 0
                    });
                }
            });
            
            console.log('📞 Teléfonos:', telefonos);
            console.log('📧 Correos:', correos);
            console.log('🏠 Direcciones:', direcciones);
            
            // Guardar información de contacto
            guardarInformacionContacto(telefonos, correos, direcciones);
        }
        
        function guardarInformacionContacto(telefonos, correos, direcciones) {
            const clienteId = $('#cliente_id').val();
            
            $.ajax({
                url: '/clientes/guardar-contacto',
                type: 'POST',
                data: {
                    cliente_id: clienteId,
                    telefonos: telefonos,
                    correos: correos,
                    direcciones: direcciones,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showSuccess('Éxito', 'Información de contacto guardada correctamente');
                        
                        // Simplificar la vista del cliente seleccionado
                        let clienteSimplificado = `
                            <div class="alert alert-success mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><i class="fas fa-user-check me-2"></i>Cliente Seleccionado</h6>
                                        <div class="small">
                                            <strong>ID:</strong> ${clienteId} | 
                                            <strong>Contacto confirmado</strong>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cambiarCliente()">
                                        <i class="fas fa-edit me-1"></i>Cambiar
                                    </button>
                                </div>
                            </div>
                        `;
                        
                        $('#cliente_seleccionado').html(clienteSimplificado);
                    } else {
                        showError('Error', response.message || 'Error al guardar la información de contacto');
                    }
                },
                error: function(xhr) {
                    showError('Error', 'Error al guardar la información de contacto');
                }
            });
        }
        
        function simularCredito() {
            console.log('🧮 Iniciando simulación de crédito');
            
            // Validar que se haya seleccionado un cliente
            const clienteId = $('#cliente_id').val();
            if (!clienteId) {
                showError('Error', 'Debe seleccionar un cliente antes de simular el crédito');
                return;
            }
            
            // Validar que se haya seleccionado un tipo de amortización
            const tipoAmortizacionId = $('#tipo_amortizacion_id').val();
            if (!tipoAmortizacionId) {
                showError('Error', 'Debe seleccionar un tipo de amortización antes de simular el crédito');
                return;
            }
            
            // Validar formulario básico
            if (!$('#creditoForm')[0].checkValidity()) {
                $('#creditoForm')[0].reportValidity();
                return;
            }
            
            // Deshabilitar botón
            $('#btnSimularCredito').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Simulando...');
            
            // Recopilar datos del formulario
            let formData = new FormData($('#creditoForm')[0]);
            
            // Agregar flag para simulación
            formData.append('simulacion', 'true');
            
            $.ajax({
                url: $('#creditoForm').attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#btnSimularCredito').prop('disabled', false).html('<i class="fas fa-calculator me-1"></i>Simular Crédito');
                    
                    if (response.success) {
                        // Mostrar el plan de pago en un modal
                        mostrarPlanPagoModal(response.plan_pago, response.datos_credito);
                    } else {
                        showError('Error', response.message || 'Error al simular el crédito');
                    }
                },
                error: function(xhr) {
                    $('#btnSimularCredito').prop('disabled', false).html('<i class="fas fa-calculator me-1"></i>Simular Crédito');
                    
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = 'Errores de validación:<br>';
                        $.each(errors, function(field, messages) {
                            errorMessage += `• ${messages[0]}<br>`;
                        });
                        showError('Error de Validación', errorMessage);
                    } else {
                        showError('Error', 'No se pudo simular el crédito');
                    }
                }
            });
        }
        
        function mostrarPlanPagoModal(planPago, datosCredito) {
            console.log('📊 Mostrando plan de pago:', planPago);
            
            let modalHtml = `
                <div class="modal fade" id="modalPlanPago" tabindex="-1" aria-labelledby="modalPlanPagoLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title" id="modalPlanPagoLabel">
                                    <i class="fas fa-calculator me-2"></i>Simulación de Crédito - Plan de Pago
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-primary">Datos del Crédito</h6>
                                        <table class="table table-sm">
                                            <tr><td><strong>Cliente ID:</strong></td><td>${datosCredito.cliente_id}</td></tr>
                                            <tr><td><strong>Tipo de Amortización:</strong></td><td>${datosCredito.tipo_amortizacion}</td></tr>
                                            <tr><td><strong>Monto:</strong></td><td>$${datosCredito.monto || 'N/A'}</td></tr>
                                            <tr><td><strong>Plazo:</strong></td><td>${datosCredito.plazo || 'N/A'} cuotas</td></tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-success">Resumen</h6>
                                        <table class="table table-sm">
                                            <tr><td><strong>Total a Pagar:</strong></td><td>$${planPago.total_pagar || 'N/A'}</td></tr>
                                            <tr><td><strong>Interés Total:</strong></td><td>$${planPago.interes_total || 'N/A'}</td></tr>
                                            <tr><td><strong>Cuota Promedio:</strong></td><td>$${planPago.cuota_promedio || 'N/A'}</td></tr>
                                        </table>
                                    </div>
                                </div>
                                
                                <h6 class="text-info">Plan de Pago Detallado</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Cuota</th>
                                                <th>Fecha</th>
                                                <th>Capital</th>
                                                <th>Interés</th>
                                                <th>Total</th>
                                                <th>Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablaPlanPago">
                                            <!-- Las cuotas se cargarán aquí -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Cerrar
                                </button>
                                <button type="button" class="btn btn-info" onclick="imprimirPlanPago()">
                                    <i class="fas fa-print me-1"></i>Imprimir
                                </button>
                                <button type="button" class="btn btn-primary" onclick="guardarCreditoDespuesSimulacion()">
                                    <i class="fas fa-save me-1"></i>Guardar Crédito
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remover modal existente si existe
            $('#modalPlanPago').remove();
            
            // Agregar modal al body
            $('body').append(modalHtml);
            
            // Llenar tabla de cuotas
            if (planPago.cuotas && planPago.cuotas.length > 0) {
                let cuotasHtml = '';
                planPago.cuotas.forEach(function(cuota, index) {
                    cuotasHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${cuota.fecha || 'N/A'}</td>
                            <td>$${cuota.capital || '0.00'}</td>
                            <td>$${cuota.interes || '0.00'}</td>
                            <td>$${cuota.total || '0.00'}</td>
                            <td>$${cuota.saldo || '0.00'}</td>
                        </tr>
                    `;
                });
                $('#tablaPlanPago').html(cuotasHtml);
            } else {
                $('#tablaPlanPago').html('<tr><td colspan="6" class="text-center">No hay datos de cuotas disponibles</td></tr>');
            }
            
            // Mostrar modal
            $('#modalPlanPago').modal('show');
        }
        
        function guardarCreditoDespuesSimulacion() {
            // Cerrar modal
            $('#modalPlanPago').modal('hide');
            
            // Llamar a la función de guardar crédito
            guardarCredito();
        }
        
        function imprimirPlanPago() {
            console.log('🖨️ Iniciando impresión del plan de pago');
            
            // Crear una ventana nueva para la impresión
            const ventanaImpresion = window.open('', '_blank', 'width=800,height=600');
            
            // Obtener los datos del modal
            const modal = document.getElementById('modalPlanPago');
            const modalBody = modal.querySelector('.modal-body');
            
            // Crear el contenido HTML para imprimir
            const contenidoImpresion = `
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Plan de Pago - Simulación de Crédito</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                            color: #333;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 30px;
                            border-bottom: 2px solid #007bff;
                            padding-bottom: 20px;
                        }
                        .header h1 {
                            color: #007bff;
                            margin: 0;
                            font-size: 24px;
                        }
                        .header h2 {
                            color: #6c757d;
                            margin: 5px 0 0 0;
                            font-size: 18px;
                            font-weight: normal;
                        }
                        .info-section {
                            display: flex;
                            justify-content: space-between;
                            margin-bottom: 30px;
                        }
                        .info-box {
                            flex: 1;
                            margin: 0 10px;
                            padding: 15px;
                            border: 1px solid #dee2e6;
                            border-radius: 5px;
                            background-color: #f8f9fa;
                        }
                        .info-box h3 {
                            margin: 0 0 10px 0;
                            color: #007bff;
                            font-size: 16px;
                            border-bottom: 1px solid #dee2e6;
                            padding-bottom: 5px;
                        }
                        .info-table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        .info-table td {
                            padding: 5px 0;
                            border: none;
                        }
                        .info-table td:first-child {
                            font-weight: bold;
                            width: 50%;
                        }
                        .resumen-section {
                            margin-bottom: 30px;
                        }
                        .resumen-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                        }
                        .resumen-table td {
                            padding: 8px;
                            border: 1px solid #dee2e6;
                        }
                        .resumen-table td:first-child {
                            background-color: #e9ecef;
                            font-weight: bold;
                        }
                        .plan-section h3 {
                            color: #007bff;
                            margin-bottom: 15px;
                            font-size: 18px;
                        }
                        .plan-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                        }
                        .plan-table th,
                        .plan-table td {
                            padding: 10px;
                            text-align: center;
                            border: 1px solid #dee2e6;
                        }
                        .plan-table th {
                            background-color: #007bff;
                            color: white;
                            font-weight: bold;
                        }
                        .plan-table tbody tr:nth-child(even) {
                            background-color: #f8f9fa;
                        }
                        .plan-table tbody tr:hover {
                            background-color: #e9ecef;
                        }
                        .footer {
                            margin-top: 30px;
                            text-align: center;
                            font-size: 12px;
                            color: #6c757d;
                            border-top: 1px solid #dee2e6;
                            padding-top: 15px;
                        }
                        @media print {
                            body {
                                margin: 0;
                            }
                            .no-print {
                                display: none;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>📊 Plan de Pago - Simulación de Crédito</h1>
                        <h2>Generado el ${new Date().toLocaleDateString('es-ES', { 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</h2>
                    </div>
                    
                    <div class="info-section">
                        <div class="info-box">
                            <h3>📋 Datos del Crédito</h3>
                            <table class="info-table">
                                <tr><td>Cliente ID:</td><td>${$('#modalPlanPago .modal-body').find('tr:contains("Cliente ID") td:last').text()}</td></tr>
                                <tr><td>Tipo de Amortización:</td><td>${$('#modalPlanPago .modal-body').find('tr:contains("Tipo de Amortización") td:last').text()}</td></tr>
                                <tr><td>Monto:</td><td>${$('#modalPlanPago .modal-body').find('tr:contains("Monto") td:last').text()}</td></tr>
                                <tr><td>Plazo:</td><td>${$('#modalPlanPago .modal-body').find('tr:contains("Plazo") td:last').text()}</td></tr>
                            </table>
                        </div>
                        <div class="info-box">
                            <h3>💰 Resumen Financiero</h3>
                            <table class="info-table">
                                <tr><td>Total a Pagar:</td><td>${$('#modalPlanPago .modal-body').find('tr:contains("Total a Pagar") td:last').text()}</td></tr>
                                <tr><td>Interés Total:</td><td>${$('#modalPlanPago .modal-body').find('tr:contains("Interés Total") td:last').text()}</td></tr>
                                <tr><td>Cuota Promedio:</td><td>${$('#modalPlanPago .modal-body').find('tr:contains("Cuota Promedio") td:last').text()}</td></tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="plan-section">
                        <h3>📅 Plan de Pago Detallado</h3>
                        <table class="plan-table">
                            <thead>
                                <tr>
                                    <th>Cuota</th>
                                    <th>Fecha</th>
                                    <th>Capital</th>
                                    <th>Interés</th>
                                    <th>Total</th>
                                    <th>Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${generarFilasTablaPlanPago()}
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="footer">
                        <p>Este documento fue generado automáticamente por el sistema de gestión de créditos.</p>
                        <p>Para consultas o aclaraciones, contacte con el departamento correspondiente.</p>
                    </div>
                </body>
                </html>
            `;
            
            // Escribir el contenido en la ventana nueva
            ventanaImpresion.document.write(contenidoImpresion);
            ventanaImpresion.document.close();
            
            // Esperar a que se cargue el contenido y luego imprimir
            ventanaImpresion.onload = function() {
                setTimeout(function() {
                    ventanaImpresion.print();
                    ventanaImpresion.close();
                }, 500);
            };
        }
        
        function generarFilasTablaPlanPago() {
            let filas = '';
            const tablaPlanPago = document.querySelector('#modalPlanPago #tablaPlanPago');
            
            if (tablaPlanPago) {
                const filasTabla = tablaPlanPago.querySelectorAll('tr');
                filasTabla.forEach(function(fila, index) {
                    const celdas = fila.querySelectorAll('td');
                    if (celdas.length > 0) {
                        filas += '<tr>';
                        celdas.forEach(function(celda) {
                            filas += `<td>${celda.textContent}</td>`;
                        });
                        filas += '</tr>';
                    }
                });
            }
            
            return filas || '<tr><td colspan="6" class="text-center">No hay datos disponibles</td></tr>';
        }
        
        function cargarCuotas() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '/tipos-creditos/' + tipoCreditoId + '/cuotas',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        resolve(response.data || []);
                    },
                    error: function(xhr) {
                        console.log('Error al cargar cuotas:', xhr);
                        resolve([]);
                    }
                });
            });
        }
        
        async function cargarCampos() {
            try {
                // Cargar campos y cuotas en paralelo
                const [camposResponse, cuotas] = await Promise.all([
                    $.ajax({
                        url: '/tipos-creditos/' + tipoCreditoId + '/campos/data',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }),
                    cargarCuotas()
                ]);
                
                if (camposResponse.data && camposResponse.data.length > 0) {
                    campos = camposResponse.data;
                    renderizarCampos(cuotas);
                } else {
                    $('#campos-container').html(`
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>No hay campos definidos</strong>
                                <p class="mb-0">Este tipo de crédito no tiene campos definidos. Primero debes definir los campos en la configuración del tipo de crédito.</p>
                            </div>
                        </div>
                    `);
                }
            } catch (error) {
                $('#campos-container').html(`
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Error al cargar campos</strong>
                            <p class="mb-0">No se pudieron cargar los campos del tipo de crédito.</p>
                        </div>
                    </div>
                `);
            }
        }
        
        function renderizarCampos(cuotas = []) {
            let html = '';
            
            // Ordenar campos por orden
            campos.sort((a, b) => a.orden - b.orden);
            
            // Separar campos de cuota de los demás campos
            let camposCuota = campos.filter(campo => campo.tipo_campo === 'cuota');
            let camposOtros = campos.filter(campo => campo.tipo_campo !== 'cuota');
            
            // Renderizar campos que no son cuota
            camposOtros.forEach(function(campo, index) {
                let esRequerido = campo.requerido ? '<span class="text-danger">*</span>' : '';
                let campoHtml = '';
                
                switch(campo.tipo_campo) {
                    case 'texto':
                        campoHtml = `
                            <input type="text" 
                                   class="form-control" 
                                   name="campo_${campo.nombre_campo}" 
                                   id="campo_${campo.nombre_campo}"
                                   placeholder="${campo.alias}"
                                   ${campo.requerido ? 'required' : ''}
                                   ${campo.valor_por_defecto ? 'value="' + campo.valor_por_defecto + '"' : ''}>
                        `;
                        break;
                        
                    case 'numero':
                        campoHtml = `
                            <input type="number" 
                                   class="form-control" 
                                   name="campo_${campo.nombre_campo}" 
                                   id="campo_${campo.nombre_campo}"
                                   placeholder="${campo.alias}"
                                   step="0.01"
                                   ${campo.requerido ? 'required' : ''}
                                   ${campo.valor_por_defecto ? 'value="' + campo.valor_por_defecto + '"' : ''}>
                        `;
                        break;
                        
                    case 'fecha':
                        campoHtml = `
                            <input type="date" 
                                   class="form-control" 
                                   name="campo_${campo.nombre_campo}" 
                                   id="campo_${campo.nombre_campo}"
                                   ${campo.requerido ? 'required' : ''}
                                   ${campo.valor_por_defecto ? 'value="' + campo.valor_por_defecto + '"' : ''}>
                        `;
                        break;
                        
                    case 'selector':
                        let opciones = campo.opciones ? campo.opciones.split(',').map(op => op.trim()) : [];
                        let opcionesHtml = '<option value="">Seleccione una opción</option>';
                        opciones.forEach(function(opcion) {
                            opcionesHtml += `<option value="${opcion}">${opcion}</option>`;
                        });
                        
                        campoHtml = `
                            <select class="form-control" 
                                    name="campo_${campo.nombre_campo}" 
                                    id="campo_${campo.nombre_campo}"
                                    ${campo.requerido ? 'required' : ''}>
                                ${opcionesHtml}
                            </select>
                        `;
                        break;
                        
                    case 'archivo':
                        campoHtml = `
                            <input type="file" 
                                   class="form-control" 
                                   name="campo_${campo.nombre_campo}" 
                                   id="campo_${campo.nombre_campo}"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx"
                                   ${campo.requerido ? 'required' : ''}>
                            <div class="form-text">Formatos permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG, XLS, XLSX (máximo 10MB)</div>
                        `;
                        break;
                }
                
                // Agregar badge para monto transaccional
                let montoTransaccionalBadge = campo.monto_transaccional ? 
                    '<span class="badge bg-success ms-2">Monto Transaccional</span>' : '';
                
                html += `
                    <div class="col-md-6 mb-3">
                        <label for="campo_${campo.nombre_campo}" class="form-label">
                            ${campo.alias} ${esRequerido} ${montoTransaccionalBadge}
                        </label>
                        ${campoHtml}
                        <div class="form-text">${campo.tipo_campo.charAt(0).toUpperCase() + campo.tipo_campo.slice(1)}</div>
                    </div>
                `;
            });
            
            // Renderizar selector de cuotas si hay campos de tipo cuota
            if (camposCuota.length > 0) {
                let esRequerido = camposCuota.some(campo => campo.requerido) ? '<span class="text-danger">*</span>' : '';
                
                // Crear opciones del selector de cuotas
                let opcionesCuotas = '<option value="">Seleccione una cuota</option>';
                
                // Agregar opciones basadas en las cuotas dinámicas
                if (cuotas && cuotas.length > 0) {
                    cuotas.forEach(function(cuota) {
                        opcionesCuotas += `<option value="${cuota.valor}">${cuota.texto}</option>`;
                    });
                } else {
                    // Fallback: opciones estándar si no hay cuotas dinámicas
                    camposCuota.forEach(function(campo) {
                        opcionesCuotas += `<option value="${campo.nombre_campo}_1">${campo.alias} - Cuota 1</option>`;
                        opcionesCuotas += `<option value="${campo.nombre_campo}_2">${campo.alias} - Cuota 2</option>`;
                        opcionesCuotas += `<option value="${campo.nombre_campo}_3">${campo.alias} - Cuota 3</option>`;
                        opcionesCuotas += `<option value="${campo.nombre_campo}_4">${campo.alias} - Cuota 4</option>`;
                        opcionesCuotas += `<option value="${campo.nombre_campo}_5">${campo.alias} - Cuota 5</option>`;
                        opcionesCuotas += `<option value="${campo.nombre_campo}_6">${campo.alias} - Cuota 6</option>`;
                        opcionesCuotas += `<option value="${campo.nombre_campo}_12">${campo.alias} - Cuota 12</option>`;
                        opcionesCuotas += `<option value="${campo.nombre_campo}_18">${campo.alias} - Cuota 18</option>`;
                        opcionesCuotas += `<option value="${campo.nombre_campo}_24">${campo.alias} - Cuota 24</option>`;
                        opcionesCuotas += `<option value="${campo.nombre_campo}_36">${campo.alias} - Cuota 36</option>`;
                    });
                }
                
                html += `
                    <div class="col-md-6 mb-3">
                        <label for="selector_cuotas" class="form-label">
                            Selector de Cuotas ${esRequerido}
                        </label>
                        <select class="form-control" 
                                name="selector_cuotas" 
                                id="selector_cuotas"
                                ${camposCuota.some(campo => campo.requerido) ? 'required' : ''}>
                            ${opcionesCuotas}
                        </select>
                        <div class="form-text">Seleccione la cuota deseada</div>
                    </div>
                `;
            }
            
            $('#campos-container').html(html);
        }
        
        function cargarTiposAmortizacion() {
            $.ajax({
                url: '/tipos-amortizacion/data',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        let html = '<option value="">Seleccione un tipo de amortización</option>';
                        response.data.forEach(function(tipo) {
                            if (tipo.estado) { // Solo mostrar tipos activos
                                html += `<option value="${tipo.id}" data-descripcion="${tipo.descripcion}" data-formula="${tipo.formula}">${tipo.nombre}</option>`;
                            }
                        });
                        $('#tipo_amortizacion_id').html(html);
                    }
                },
                error: function(xhr) {
                    console.log('Error al cargar tipos de amortización:', xhr);
                    $('#tipo_amortizacion_id').html('<option value="">Error al cargar tipos de amortización</option>');
                }
            });
        }
        
        function mostrarInfoAmortizacion() {
            const selectedOption = $('#tipo_amortizacion_id option:selected');
            const descripcion = selectedOption.data('descripcion');
            const formula = selectedOption.data('formula');
            
            let infoHtml = '';
            
            if (selectedOption.val()) {
                if (descripcion) {
                    infoHtml += `<p class="mb-2 no-fade"><strong>Descripción:</strong> ${descripcion}</p>`;
                }
                
                if (formula) {
                    infoHtml += `<p class="mb-0"><strong>Fórmula:</strong> <code>${formula}</code></p>`;
                }
                
                if (!descripcion && !formula) {
                    infoHtml += `<p class="mb-0"><em>No hay información adicional disponible para este tipo de amortización.</em></p>`;
                }
            } else {
                infoHtml += `<p class="mb-0"><em>Seleccione un tipo de amortización para ver su información.</em></p>`;
            }
            
            // Usar solo clases de Bootstrap para mantener visible
            $('#descripcion_amortizacion').html(infoHtml);
            $('#info_amortizacion').removeClass('d-none').addClass('d-block');
        }
        
        function guardarCredito() {
            // Validar que se haya seleccionado un cliente
            const clienteId = $('#cliente_id').val();
            if (!clienteId) {
                showError('Error', 'Debe seleccionar un cliente antes de guardar el crédito');
                return;
            }
            
            // Validar que se haya seleccionado un tipo de amortización
            const tipoAmortizacionId = $('#tipo_amortizacion_id').val();
            if (!tipoAmortizacionId) {
                showError('Error', 'Debe seleccionar un tipo de amortización antes de guardar el crédito');
                return;
            }
            
            // Validar formulario
            if (!$('#creditoForm')[0].checkValidity()) {
                $('#creditoForm')[0].reportValidity();
                return;
            }
            
            // Deshabilitar botón
            $('#btnGuardar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Guardando...');
            
            // Recopilar datos del formulario
            let formData = new FormData($('#creditoForm')[0]);
            
            $.ajax({
                url: $('#creditoForm').attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showSuccess('Éxito', response.message);
                        setTimeout(function() {
                            window.location.href = '{{ route("tipos.creditos.creditos.listado", $tipoCredito->id) }}';
                        }, 1500);
                    } else {
                        showError('Error', response.message);
                        $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Guardar Crédito');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = 'Errores de validación:<br>';
                        $.each(errors, function(field, messages) {
                            errorMessage += '• ' + messages[0] + '<br>';
                        });
                        showError('Error de Validación', errorMessage);
                    } else {
                        showError('Error', 'No se pudo guardar el crédito');
                    }
                    $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Guardar Crédito');
                }
            });
        }
        
        function guardarCreditoPendiente() {
            // Validar que se haya seleccionado un cliente
            const clienteId = $('#cliente_id').val();
            if (!clienteId) {
                showError('Error', 'Debe seleccionar un cliente antes de guardar el crédito');
                return;
            }
            
            // Validar que se haya seleccionado un tipo de amortización
            const tipoAmortizacionId = $('#tipo_amortizacion_id').val();
            if (!tipoAmortizacionId) {
                showError('Error', 'Debe seleccionar un tipo de amortización antes de guardar el crédito');
                return;
            }
            
            // Validar formulario
            if (!$('#creditoForm')[0].checkValidity()) {
                $('#creditoForm')[0].reportValidity();
                return;
            }
            
            // Deshabilitar botón
            $('#btnGuardarPendiente').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Guardando...');
            
            // Recopilar datos del formulario
            let formData = new FormData($('#creditoForm')[0]);
            
            // Agregar estado pendiente
            formData.append('estado', 'pendiente');
            
            $.ajax({
                url: $('#creditoForm').attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showSuccess('Éxito', 'Crédito guardado como pendiente exitosamente');
                        setTimeout(function() {
                            window.location.href = '{{ route("tipos.creditos.creditos.listado", $tipoCredito->id) }}';
                        }, 1500);
                    } else {
                        showError('Error', response.message);
                        $('#btnGuardarPendiente').prop('disabled', false).html('<i class="fas fa-clock me-1"></i>Guardar como Pendiente');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = 'Errores de validación:<br>';
                        $.each(errors, function(field, messages) {
                            errorMessage += '• ' + messages[0] + '<br>';
                        });
                        showError('Error de Validación', errorMessage);
                    } else {
                        showError('Error', 'No se pudo guardar el crédito como pendiente');
                    }
                    $('#btnGuardarPendiente').prop('disabled', false).html('<i class="fas fa-clock me-1"></i>Guardar como Pendiente');
                }
            });
        }
    </script>
    @endpush
@endsection
