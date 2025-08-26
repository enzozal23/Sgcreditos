<x-app-layout>
    <x-slot name="title">Crear Nuevo Crédito - {{ $tipoCredito->nombre }}</x-slot>
    
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
                                             <div class="alert alert-success">
                                                 <h6 class="mb-2">
                                                     <i class="fas fa-check-circle me-2"></i>Cliente Seleccionado
                                                 </h6>
                                                 <div id="info_cliente_seleccionado"></div>
                                             </div>
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
                $('#cliente_search').val('');
                $('#resultados_busqueda').hide();
                $('#cliente_seleccionado').hide();
                $('#cliente_id').val('');
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
            $('#cliente_id').val(id);
            
            // Crear información dinámica del cliente seleccionado
            let infoHtml = `<div class="row">`;
            infoHtml += `<div class="col-md-6"><strong>ID:</strong> ${id}<br>`;
            
                         if (nombre) infoHtml += `<strong>Nombre:</strong> ${nombre}<br>`;
             if (dniCuit) {
                 // Formatear campo numérico para que no muestre decimales
                 let valorFormateado = dniCuit;
                 if (dniCuit && dniCuit !== '' && !isNaN(dniCuit) && Number.isInteger(parseFloat(dniCuit))) {
                     valorFormateado = parseInt(dniCuit);
                 }
                 infoHtml += `<strong>DNI:</strong> ${valorFormateado}<br>`;
             }
             if (email) infoHtml += `<strong>Email:</strong> ${email}<br>`;
             if (telefono) infoHtml += `<strong>Teléfono:</strong> ${telefono}<br>`;
            
            infoHtml += `</div></div>`;
            
            $('#info_cliente_seleccionado').html(infoHtml);
            
            $('#cliente_seleccionado').show();
            $('#resultados_busqueda').hide();
            $('#cliente_search').val('');
            
            showSuccess('Cliente Seleccionado', `Cliente seleccionado correctamente`);
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
                            errorMessage += `• ${messages[0]}<br>`;
                        });
                        showError('Error de Validación', errorMessage);
                    } else {
                        showError('Error', 'No se pudo guardar el crédito');
                    }
                    $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Guardar Crédito');
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
