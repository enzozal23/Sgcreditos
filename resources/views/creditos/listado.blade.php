<x-app-layout>
    <x-slot name="title">Listado de Créditos - {{ $tipoCredito->nombre }}</x-slot>
    
    @php
        $breadcrumbs = [
            ['title' => 'Créditos', 'url' => route('creditos.index')],
            ['title' => 'Tipos de Créditos', 'url' => route('creditos.index')],
            ['title' => $tipoCredito->nombre, 'url' => '#']
        ];
    @endphp
    
    <div class="row">
        <div class="col-md-12">
            <div class="card bg-white border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Listado de Créditos
                        </h5>
                        <small class="text-muted">Tipo: <span class="fw-bold">{{ $tipoCredito->nombre }}</span> | Tabla: <span class="badge bg-info">{{ $tipoCredito->tabla_credito }}</span></small>
                    </div>
                    <div>
                        <a href="{{ route('creditos.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" onclick="crearNuevoCredito()">
                            <i class="fas fa-plus me-1"></i>Nuevo Crédito
                        </button>
                    </div>
                </div>
                <div class="card-body bg-white">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Información de la Tabla</h6>
                        <p class="mb-1"><strong>Tabla:</strong> <span class="badge bg-info">{{ $tipoCredito->tabla_credito }}</span></p>
                        <p class="mb-0"><small>Esta tabla contiene todos los créditos del tipo "{{ $tipoCredito->nombre }}". Aquí puedes ver, crear y gestionar los créditos.</small></p>
                    </div>
                    
                    <div class="table-responsive">
                        <table id="creditos-table" class="table table-striped table-hover w-100">
                            <thead>
                                <tr>
                                    <!-- Las columnas se cargarán dinámicamente -->
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

    <!-- Modal para Plan de Pago -->
    <div class="modal fade" id="modalPlanPago" tabindex="-1" aria-labelledby="modalPlanPagoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPlanPagoLabel">
                        <i class="fas fa-calendar-alt me-2"></i>Plan de Pago
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="alert alert-info py-2">
                        <h6 class="mb-1"><i class="fas fa-info-circle me-2"></i>Información del Plan</h6>
                        <p class="mb-0 small">Aquí se mostrará el plan de pago detallado del crédito seleccionado.</p>
                    </div>
                    
                    <div class="table-responsive">
                        <table id="plan-pago-table" class="table table-sm table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>N° Cuota</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Capital</th>
                                    <th>Interés</th>
                                    <th>Cuota</th>
                                    <th>Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="exportarPlanPago()">
                        <i class="fas fa-download me-1"></i>Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let table;
        let tipoCreditoId = {{ $tipoCredito->id }};
        
        $(document).ready(function() {
            // Ajustar columnas cuando cambie el tamaño de la ventana
            $(window).on('resize', function() {
                if (table) {
                    table.columns.adjust();
                }
            });
            
            // Obtener datos primero para configurar columnas
            $.ajax({
                url: '/tipos-creditos/' + tipoCreditoId + '/creditos/data',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        // Configurar columnas dinámicamente
                        const primerRegistro = response.data[0];
                        const columnas = [];
                        
                        // Crear columnas para cada campo
                        for (const [key, value] of Object.entries(primerRegistro)) {
                            if (key !== 'acciones') {
                                // Ocultar columnas de ID y timestamps
                                if (key === 'cliente_id' || key === 'tipo_cliente_id' || key === 'amortizacion_id' || 
                                    key === 'id' || key === 'created_at' || key === 'updated_at') {
                                    continue;
                                }
                                
                                // Usar nombres en lugar de IDs
                                let columnKey = key;
                                let columnTitle = key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ');
                                
                                if (key === 'cliente_nombre') {
                                    columnTitle = 'Cliente';
                                } else if (key === 'tipo_cliente_nombre') {
                                    columnTitle = 'Tipo de Cliente';
                                } else if (key === 'amortizacion_nombre') {
                                    columnTitle = 'Tipo de Amortización';
                                }
                                
                                // Verificar si es un campo de tipo cuota (valor 0 o 1)
                                const esCampoCuota = typeof value === 'number' && (value === 0 || value === 1);
                                
                                if (esCampoCuota) {
                                    // Para campos de cuota, mostrar check o X
                                    columnas.push({ 
                                        data: columnKey, 
                                        title: columnTitle,
                                        render: function(data) {
                                            return data == 1 ? 
                                                '<span class="text-success"><i class="fas fa-check-circle"></i> ✓</span>' : 
                                                '<span class="text-muted"><i class="fas fa-times-circle"></i> ✗</span>';
                                        }
                                    });
                                } else {
                                    // Para otros campos, mostrar normalmente
                                    columnas.push({ 
                                        data: columnKey, 
                                        title: columnTitle
                                    });
                                }
                            }
                        }
                        
                        // Agregar columna de acciones
                        columnas.push({ 
                            data: 'acciones', 
                            title: 'Acciones',
                            orderable: false,
                            searchable: false,
                            width: '200px',
                            className: 'text-center'
                        });
                        
                        // Inicializar DataTable
                        inicializarDataTable(columnas);
                        
                        // Ajustar columnas después de la inicialización
                        setTimeout(function() {
                            if (table) {
                                table.columns.adjust();
                            }
                        }, 100);
                    } else {
                        $('#creditos-table').html('<tr><td colspan="3" class="text-center">No hay créditos registrados</td></tr>');
                    }
                },
                error: function() {
                    $('#creditos-table').html('<tr><td colspan="3" class="text-center">Error al cargar los datos</td></tr>');
                }
            });
        });
        
        function inicializarDataTable(columnas) {
            table = $('#creditos-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '/tipos-creditos/' + tipoCreditoId + '/creditos/data',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: columnas,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                responsive: false,
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
        }

        function crearNuevoCredito() {
            window.location.href = '/tipos-creditos/' + tipoCreditoId + '/creditos/crear';
        }

        function editarCredito(id) {
            showInfo('Editar Crédito', 'Funcionalidad para editar el crédito ID: ' + id);
        }

        function verCredito(id) {
            showInfo('Ver Crédito', 'Funcionalidad para ver detalles del crédito ID: ' + id);
        }

        function eliminarCredito(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este crédito?')) {
                showInfo('Eliminar Crédito', 'Funcionalidad para eliminar el crédito ID: ' + id);
            }
        }

        // Variables para el plan de pago
        let planPagoTable;
        let creditoActualId;

        function verPlanPago(id) {
            creditoActualId = id;
            
            // Mostrar modal
            $('#modalPlanPago').modal('show');
            
            // Inicializar DataTable del plan de pago
            inicializarPlanPagoTable();
            
            // Cargar datos del plan de pago
            cargarPlanPago(id);
            
            // Ajustar el ancho del DataTable cuando el modal esté completamente abierto
            $('#modalPlanPago').on('shown.bs.modal', function () {
                if (planPagoTable) {
                    planPagoTable.columns.adjust();
                }
            });
        }

        function inicializarPlanPagoTable() {
            if (planPagoTable) {
                planPagoTable.destroy();
            }
            
            planPagoTable = $('#plan-pago-table').DataTable({
                processing: true,
                serverSide: false,
                data: [], // Se cargarán los datos dinámicamente
                autoWidth: false,
                scrollX: false,
                columns: [
                    { data: 'numero_cuota', title: 'N° Cuota', width: '10%' },
                    { data: 'fecha_vencimiento', title: 'Fecha Vencimiento', width: '15%' },
                    { 
                        data: 'capital', 
                        title: 'Capital',
                        width: '20%',
                        render: function(data) {
                            return new Intl.NumberFormat('es-AR', {
                                style: 'currency',
                                currency: 'ARS'
                            }).format(data);
                        }
                    },
                    { 
                        data: 'interes', 
                        title: 'Interés',
                        width: '20%',
                        render: function(data) {
                            return new Intl.NumberFormat('es-AR', {
                                style: 'currency',
                                currency: 'ARS'
                            }).format(data);
                        }
                    },
                    { 
                        data: 'cuota', 
                        title: 'Cuota',
                        width: '20%',
                        render: function(data) {
                            return new Intl.NumberFormat('es-AR', {
                                style: 'currency',
                                currency: 'ARS'
                            }).format(data);
                        }
                    },
                    { 
                        data: 'saldo', 
                        title: 'Saldo',
                        width: '15%',
                        render: function(data) {
                            return new Intl.NumberFormat('es-AR', {
                                style: 'currency',
                                currency: 'ARS'
                            }).format(data);
                        }
                    }
                ],
                language: {
                    "sProcessing":     "Procesando...",
                    "sLengthMenu":     "Mostrar _MENU_ registros",
                    "sZeroRecords":    "No se encontraron resultados",
                    "sEmptyTable":     "Ningún dato disponible en esta tabla",
                    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix":    "",
                    "sSearch":         "Buscar:",
                    "sUrl":            "",
                    "sInfoThousands":  ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst":    "Primero",
                        "sLast":     "Último",
                        "sNext":     "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                },
                pageLength: 6,
                lengthMenu: [[6, 12, 24, -1], [6, 12, 24, "Todas"]],
                responsive: false,
                order: [[0, 'asc']],
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copy', text: '<i class="fas fa-copy"></i> Copiar', className: 'btn btn-sm btn-outline-info' },
                    { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-sm btn-outline-success' },
                    { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-sm btn-outline-primary' },
                    { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-outline-danger' },
                    { extend: 'print', text: '<i class="fas fa-print"></i> Imprimir', className: 'btn btn-sm btn-outline-warning' }
                ]
            });
        }

        function cargarPlanPago(creditoId) {
            // Mostrar loading en la tabla
            $('#plan-pago-table').addClass('processing');
            
            $.ajax({
                url: '/tipos-creditos/' + tipoCreditoId + '/creditos/' + creditoId + '/plan-pago',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Ocultar loading
                    $('#plan-pago-table').removeClass('processing');
                    
                    // Mostrar debug en consola
                    if (response.debug) {
                        console.log('=== DEBUG PLAN DE PAGO ===');
                        response.debug.forEach(function(debugLine) {
                            console.log(debugLine);
                        });
                        console.log('=== FIN DEBUG ===');
                    }
                    
                    if (response.success) {
                        planPagoTable.clear().rows.add(response.data).draw();
                        
                        const info = response.info;
                        $('#modalPlanPagoLabel').html(
                            '<i class="fas fa-calendar-alt me-2"></i>Plan de Pago - Crédito ID: ' + creditoId + 
                            '<br><small class="text-muted">Monto: $' + new Intl.NumberFormat('es-AR').format(info.monto) + 
                            ' | Tipo: ' + info.tipo_amortizacion + 
                            ' | Cuotas: ' + info.total_cuotas + '</small>'
                        );
                        
                        $('.modal-body .alert-info p').html(
                            '<strong>Monto del crédito:</strong> $' + new Intl.NumberFormat('es-AR').format(info.monto) + '<br>' +
                            '<strong>Tipo de amortización:</strong> ' + info.tipo_amortizacion + '<br>' +
                            '<strong>Total de cuotas:</strong> ' + info.total_cuotas
                        );
                    } else {
                        planPagoTable.clear().draw();
                        showError('Error', response.message || 'Error al cargar el plan de pago');
                    }
                },
                error: function(xhr) {
                    // Ocultar loading
                    $('#plan-pago-table').removeClass('processing');
                    
                    let errorMessage = 'Error al cargar el plan de pago';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showError('Error', errorMessage);
                    planPagoTable.clear().draw();
                    
                    // Mostrar debug del error en consola
                    if (xhr.responseJSON && xhr.responseJSON.debug) {
                        console.log('=== DEBUG ERROR ===');
                        xhr.responseJSON.debug.forEach(function(debugLine) {
                            console.log(debugLine);
                        });
                        console.log('=== FIN DEBUG ERROR ===');
                    }
                }
            });
        }

        function exportarPlanPago() {
            // Función para exportar el plan de pago
            showInfo('Exportar', 'Funcionalidad de exportación del plan de pago del crédito ID: ' + creditoActualId);
        }
    </script>
    @endpush
    <style>
        .processing {
            position: relative;
        }
        
        .processing::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 1000;
        }
        
        .processing::before {
            content: 'Cargando...';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1001;
            font-weight: bold;
            color: #007bff;
        }
        
        /* Estilos para la columna de acciones */
        #creditos-table th:last-child,
        #creditos-table td:last-child {
            min-width: 200px !important;
            max-width: 200px !important;
            width: 200px !important;
        }
        
        /* Asegurar que todas las columnas sean visibles */
        #creditos-table th,
        #creditos-table td {
            white-space: nowrap !important;
            min-width: 120px !important;
        }
        
        /* Columnas específicas con ancho fijo */
        #creditos-table th:first-child,
        #creditos-table td:first-child {
            min-width: 80px !important;
            max-width: 80px !important;
        }
        
        /* Asegurar que los botones de acciones no se rompan */
        #creditos-table .btn-group {
            white-space: nowrap;
            flex-wrap: nowrap;
        }
        
        #creditos-table .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        /* Scroll horizontal para la tabla */
        .table-responsive {
            overflow-x: auto;
            overflow-y: hidden;
        }
        
        /* Asegurar que la tabla tenga scroll horizontal cuando sea necesario */
        #creditos-table {
            min-width: 100%;
            width: max-content;
        }
        
        /* Mejorar la apariencia del scroll */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</x-app-layout>
