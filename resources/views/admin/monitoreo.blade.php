@extends('layouts.app')

@section('content')
    
    @php
        $breadcrumbs = [
            ['title' => 'Administración', 'url' => '#'],
            ['title' => 'Monitoreo', 'url' => route('admin.monitoreo')]
        ];
    @endphp
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Registros de Monitoreo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="monitoreo-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>IP</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>admin@example.com</td>
                                    <td>Login</td>
                                    <td>192.168.1.100</td>
                                    <td>{{ now()->format('d/m/Y H:i:s') }}</td>
                                    <td>Exitoso</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info" title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>user@example.com</td>
                                    <td>Logout</td>
                                    <td>192.168.1.101</td>
                                    <td>{{ now()->subMinutes(5)->format('d/m/Y H:i:s') }}</td>
                                    <td>Exitoso</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info" title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
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
            $('#monitoreo-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                responsive: true,
                order: [[0, 'desc']],
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        searchable: false
                    }
                ],
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

        function verDetalles(id) {
            console.log('Ver detalles del registro:', id);
            // Lógica para mostrar detalles
        }
    </script>
    @endpush
@endsection
