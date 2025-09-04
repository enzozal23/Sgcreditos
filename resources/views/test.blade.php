<!DOCTYPE html>
<html>
<head>
    <title>Test View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Vista de Prueba</h3>
                    </div>
                    <div class="card-body">
                        <p>Si puedes ver esto, el problema NO es con las vistas individuales.</p>
                        <p>El problema está en el layout principal.</p>
                        <hr>
                        <h5>Información del Sistema:</h5>
                        <ul>
                            <li>Laravel Version: {{ app()->version() }}</li>
                            <li>PHP Version: {{ phpversion() }}</li>
                            <li>App Name: {{ config('app.name') }}</li>
                            <li>Environment: {{ config('app.env') }}</li>
                        </ul>
                        <a href="/admin/permisos" class="btn btn-primary">Ir a Permisos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
