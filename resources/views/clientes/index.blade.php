@extends('layouts.app')

@section('content')
    
    @php
        $breadcrumbs = [
            ['title' => 'Clientes', 'url' => '#'],
            ['title' => 'Tipos de Clientes', 'url' => route('clientes.index')]
        ];
    @endphp
    
    <div class="row">
        <div class="col-md-12">
            <div class="card bg-white border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-tags me-2"></i>Tipos de Clientes
                    </h5>
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
        $(document).ready(function() {
            // Inicializar DataTable
            $('#tipos-clientes-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '/tipos-clientes/data',
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
                        data: 'estado',
                        render: function(data) {
                            if (data === 'activo') {
                                return '<span class="badge bg-success">Activo</span>';
                            } else {
                                return '<span class="badge bg-secondary">Inactivo</span>';
                            }
                        }
                    },
                    { data: 'fecha_creacion' },
                    { 
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<button type="button" class="btn btn-sm btn-info" onclick="verClientes(' + data + ')" title="Ver Clientes">' +
                                    '<i class="fas fa-users"></i> Ver Clientes</button>';
                        }
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

         // Función para ver clientes de un tipo específico
         function verClientes(tipoClienteId) {
             // Redirigir a la vista de clientes del tipo seleccionado
             window.location.href = `/clientes/tipo/${tipoClienteId}`;
         }
     </script>
    @endpush
@endsection
