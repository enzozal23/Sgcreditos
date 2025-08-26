<div class="card bg-white border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-table me-2"></i>{{ $title ?? 'Tabla de Datos' }}
        </h5>
        @if(isset($addButton))
            {!! $addButton !!}
        @endif
    </div>
    <div class="card-body bg-white">
        @if($searchable)
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="search-{{ $id }}" placeholder="Buscar...">
                </div>
            </div>
        </div>
        @endif

        <div class="{{ $responsive ? 'table-responsive' : '' }}">
            <table id="{{ $id }}" class="{{ $tableClass }}">
                <thead>
                    <tr>
                        @if(isset($headers) && is_array($headers))
                            @foreach($headers as $header)
                                <th>{{ $header }}</th>
                            @endforeach
                        @endif
                        @if($actions)
                            <th class="text-center">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if(isset($data) && is_array($data) && count($data) > 0)
                        @foreach($data as $row)
                            <tr>
                                @if(is_array($row))
                                    @foreach($row as $key => $value)
                                        @if($key !== 'actions')
                                            <td>
                                                @if(is_bool($value))
                                                    @if($value)
                                                        <span class="badge bg-success">Sí</span>
                                                    @else
                                                        <span class="badge bg-danger">No</span>
                                                    @endif
                                                @elseif(is_string($value) && str_contains($value, 'http'))
                                                    <a href="{{ $value }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-external-link-alt"></i> Ver
                                                    </a>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                @endif
                                @if($actions)
                                    <td class="text-center">
                                        @if(isset($row['actions']))
                                            {!! $row['actions'] !!}
                                        @else
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ (isset($headers) && is_array($headers) ? count($headers) : 1) + ($actions ? 1 : 0) }}" class="text-center text-muted">
                                {{ $emptyMessage }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Configuración base de DataTable
    var tableConfig = {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        responsive: {{ $responsive ? 'true' : 'false' }},
        order: [[0, 'asc']],
        columnDefs: []
    };

    // Configurar columnDefs para la columna de acciones si existe
    @if($actions)
        tableConfig.columnDefs.push({
            targets: -1,
            orderable: false,
            searchable: false
        });
    @endif

    // Agregar funcionalidades según configuración
    @if($searchable)
        tableConfig.searching = true;
    @else
        tableConfig.searching = false;
    @endif

    @if($sortable)
        tableConfig.ordering = true;
    @else
        tableConfig.ordering = false;
    @endif

    @if($pageable)
        tableConfig.paging = true;
    @else
        tableConfig.paging = false;
    @endif

    @if($exportable)
        tableConfig.dom = 'Bfrtip';
        tableConfig.buttons = [
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
        ];
    @endif

    // Inicializar DataTable
    var table = $('#{{ $id }}').DataTable(tableConfig);

    // Búsqueda personalizada
    @if($searchable)
    $('#search-{{ $id }}').on('keyup', function() {
        table.search(this.value).draw();
    });
    @endif

    // Confirmación para eliminar
    $('#{{ $id }}').on('click', '.btn-outline-danger', function() {
        confirmDelete('¿Eliminar registro?', 'Esta acción no se puede deshacer.').then((result) => {
            if (result.isConfirmed) {
                // Aquí puedes agregar la lógica de eliminación
                showInfo('Eliminar', 'Funcionalidad para eliminar registro');
            }
        });
    });

    // Tooltips para botones
    $('[title]').tooltip();
});
</script>
@endpush