
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name', 'sistema de gestion de creditos') }}</title>
	<link rel="shortcut icon" type="image/png" href="{{ asset('build/sgc_theme/img/favicon.png') }}"/>

	<!--SECCION CSS-->
	<link rel="stylesheet" type="text/css" href="{{ asset('build/assets/bootstrap/dist/css/bootstrap.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('build/sgc_theme/css/custom.css?v=20250623') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('build/assets/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('build/assets/jqueryui/themes/base/jquery-ui.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('build/assets/font-awesome/css/all.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('build/assets/font-awesome/css/fontawesome.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('build/assets/bootstrap-icons/font/bootstrap-icons.min.css') }}">
	<!--SECCION JS-->
	<script type="text/javascript" src="{{ asset('build/assets/jquery/jquery.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/jqueryui/jquery-ui.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/form/dist/jquery.form.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/datatables.net/js/dataTables.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/sweet-alert/resources/js/sweetalert.all.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/ajax-setup.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/sweet-alert-helper.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/html2canvas/dist/html2canvas.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/buttons/dataTables.buttons.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/amcharts/plugins/export/libs/jszip/jszip.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/tableexport.jquery.plugin/libs/pdfmake/pdfmake.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/tableexport.jquery.plugin/libs/pdfmake/vfs_fonts.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/buttons/buttons.html5.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/buttons/buttons.print.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/js/date-helper.js') }}"></script>
	<!-- Alpine.js para dropdowns -->
	<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased">
	<style>
		.accordion-header > button{
			background-color: #e2e9ff;
			color: #337ab7;
		}

		.accordion-header > button:hover{
			color: #23527c;
			text-decoration: underline;
		}

		.colapsable-aleph {
			width: 100%;
			height: 35px;
			padding-top: 8px;
			padding-left: 20px;
			font-weight: 900;
			user-select: none;
			background-color: #e2e9ff;
			margin-top: 10px;
			margin-bottom: 10px;
		}

        .error403{
            font-size: 3rem;
        }

		/* Estilos personalizados para botones de DataTables */
		.dt-buttons .btn-outline-info {
			color: #0dcaf0 !important;
			border-color: #0dcaf0 !important;
			background-color: transparent !important;
		}
		.dt-buttons .btn-outline-info:hover {
			color: #fff !important;
			background-color: #0dcaf0 !important;
		}

		.dt-buttons .btn-outline-success {
			color: #198754 !important;
			border-color: #198754 !important;
			background-color: transparent !important;
		}
		.dt-buttons .btn-outline-success:hover {
			color: #fff !important;
			background-color: #198754 !important;
		}

		.dt-buttons .btn-outline-primary {
			color: #0d6efd !important;
			border-color: #0d6efd !important;
			background-color: transparent !important;
		}
		.dt-buttons .btn-outline-primary:hover {
			color: #fff !important;
			background-color: #0d6efd !important;
		}

		.dt-buttons .btn-outline-danger {
			color: #dc3545 !important;
			border-color: #dc3545 !important;
			background-color: transparent !important;
		}
		.dt-buttons .btn-outline-danger:hover {
			color: #fff !important;
			background-color: #dc3545 !important;
		}

		.dt-buttons .btn-outline-warning {
			color: #ffc107 !important;
			border-color: #ffc107 !important;
			background-color: transparent !important;
		}
		.dt-buttons .btn-outline-warning:hover {
			color: #000 !important;
			background-color: #ffc107 !important;
		}

		/* Remover el fondo gris de los botones de DataTables */
		.dt-buttons .btn-secondary {
			background-color: transparent !important;
			border-color: #6c757d !important;
			color: #6c757d !important;
		}
		.dt-buttons .btn-secondary:hover {
			background-color: #6c757d !important;
			color: #fff !important;
		}
	</style>

	@include('layouts.partials.header')
	
	<header>
		@if(Auth::check())
			@include('layouts.partials.navigation')
			@if(!request()->routeIs('dashboard'))
				<x-breadcrumb :breadcrumbs="$breadcrumbs ?? []" :page="$title ?? ''" />
			@endif
		@endif
	</header>

	<div class="min-h-screen bg-gray-100">
		<main class="container">
			{{ $slot }}
		</main>
	</div>

	<!-- Footer removido -->

	<script type="text/javascript" src="{{ asset('build/assets/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/jqueryui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/datatables.net/js/dataTables.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/buttons/dataTables.buttons.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/buttons/buttons.html5.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/buttons/buttons.print.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/sweet-alert/resources/js/sweetalert.all.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/ajax-setup.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/sgc_theme/js/customv7.js?v='.date('Y-m-d')) }}"></script>

	<script>
		$(document).ready(function() {
			// Set up CSRF token for all AJAX requests
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			new DataTable('#example', {
				order: [[4, 'desc']]
			});

			new DataTable('#permisos');

			$('.dropdown-submenu a.test').on("click", function(e) {
				$(this).next('ul').toggle();
				e.stopPropagation();
				e.preventDefault();
			});

			$(document).on("click", ".colapsable-aleph", function() {
				arrow_span = $(this).find('span');

				if ($(arrow_span).hasClass('glyphicon-chevron-right')) {
					$(arrow_span).removeClass('glyphicon-chevron-right');
					$(arrow_span).addClass('glyphicon-chevron-down');
				} else {
					$(arrow_span).removeClass('glyphicon-chevron-down');
					$(arrow_span).addClass('glyphicon-chevron-right');
				}
			});

			$(".usuarios_autocomplete").autocomplete({
				source: function(request, response) {
					$.ajax({
						url: baseUrl + "/usuarios/autocomplete",
						type: 'post',
						dataType: "json",
						data: {
							search: request.term
						},
						success: function(data) {
							response(data);
						}
					});
				},
				select: function(event, ui) {
					$(this).val(ui.item.label); // display the selected text
					return false;
				}
			});
			
			// Esperar 5 segundos antes de ocultar el mensaje
			setTimeout(function() {
                $('.alert').not('.no-fade-alert').fadeOut('slow');
            }, 5000);
		});

		function show_password(elemento){
            var x = document.getElementById("password");
            if($(elemento).prop('checked')){
                x.type = "text";
            }else{
                x.type = "password";
            }

            var x = document.getElementById("password_confirmation");
            if($(elemento).prop('checked')){
                x.type = "text";
            }else{
                x.type = "password";
            }
		}
	</script>

	@stack('scripts')
	
	<!-- DataTables CSS y JS adicionales -->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
	
	<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
</body>

</html>
