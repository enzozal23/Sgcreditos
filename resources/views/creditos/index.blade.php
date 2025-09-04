@extends('layouts.app')

@section('content')
    
    @php
        $breadcrumbs = [
            ['title' => 'Créditos', 'url' => '#'],
            ['title' => 'Tipos de Créditos', 'url' => route('creditos.index')]
        ];
    @endphp
    
    <div class="row">
        <div class="col-md-12">
            <div class="card bg-white border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>Créditos
                    </h5>
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

    @push('scripts')
    <script>
        let table;
        
        $(document).ready(function() {
            table = $('#tipos-creditos-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '/tipos-creditos/data-creditos',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    { data: 'identificador' },
                    { data: 'tabla_credito' },
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

        function nuevoTipoCredito() {
            // Redirigir a la página de tipos de créditos
            window.location.href = '/tipos-creditos';
        }

        function verListadoCreditos(tipoCreditoId) {
            // Redirigir a la vista de listado de créditos del tipo específico
            window.location.href = '/tipos-creditos/' + tipoCreditoId + '/creditos';
        }

        function crearNuevoCredito(tipoCreditoId) {
            // Redirigir a la vista de creación de crédito
            window.location.href = '/tipos-creditos/' + tipoCreditoId + '/creditos/crear';
        }
    </script>
    @endpush
@endsection
