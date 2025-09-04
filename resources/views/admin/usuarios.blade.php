@extends('layouts.app')

@section('content')
    
    @php
        $breadcrumbs = [
            ['title' => 'Administración', 'url' => '#'],
            ['title' => 'Usuarios', 'url' => route('admin.usuarios')]
        ];
    @endphp
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Gestión de Usuarios
                    </h5>
                    <button class="btn btn-primary btn-sm" onclick="nuevoUsuario()">
                        <i class="fas fa-plus me-1"></i>Nuevo Usuario
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="usuarios-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Estado</th>
                                    <th>Último Login</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\User::all() as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->nombre }} {{ $user->apellido }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>
                                        @if($user->habilitado)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->ultimo_login ? $user->ultimo_login->format('d/m/Y H:i') : 'Nunca' }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Editar" onclick="editarUsuario({{ $user->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="eliminarUsuario({{ $user->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
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
            $('#usuarios-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                responsive: true,
                order: [[0, 'asc']],
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

        function editarUsuario(id) {
            showInfo('Editar Usuario', `Funcionalidad para editar usuario con ID: ${id}`);
            // Aquí puedes abrir un modal o redirigir a la página de edición
        }
        
        function eliminarUsuario(id) {
            deleteWithConfirmation(`/admin/usuarios/${id}/eliminar`, 'este usuario');
        }
        
        function nuevoUsuario() {
            showInfo('Nuevo Usuario', 'Funcionalidad para crear nuevo usuario');
            // Aquí puedes abrir un modal o redirigir a la página de creación
        }
    </script>
    @endpush
@endsection
