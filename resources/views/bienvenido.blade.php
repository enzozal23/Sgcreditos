<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Mi Proyecto Laravel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            margin: 2rem;
        }

        .logo {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }

        h1 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 2.5rem;
        }

        .subtitle {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .feature {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .feature h3 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .feature p {
            color: #666;
            font-size: 0.9rem;
        }

        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            margin: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }

        .info {
            background: #e3f2fd;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            border-left: 4px solid #2196f3;
        }

        .version {
            color: #666;
            font-size: 0.9rem;
            margin-top: 2rem;
        }

        .user-info {
            background: #f0f8ff;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            border-left: 4px solid #4CAF50;
        }

        .btn-logout {
            background: #f44336 !important;
        }

        .btn-logout:hover {
            background: #d32f2f !important;
        }
    </style>
</head>
<body>
    <div class="container">
        @auth
            <div class="user-info">
                <strong>👤 Usuario:</strong> {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}<br>
                <strong>📧 Email:</strong> {{ Auth::user()->email }}<br>
                <strong>🆔 Username:</strong> {{ Auth::user()->username }}
            </div>
        @endauth
        
        <div class="logo">🚀</div>
        <h1>¡Bienvenido a Laravel!</h1>
        <p class="subtitle">Tu proyecto Laravel está funcionando correctamente</p>
        
        <div class="info">
            <strong>¡Felicidades!</strong> Has creado exitosamente tu primer proyecto Laravel desde cero.
        </div>

        <div class="features">
            <div class="feature">
                <h3>🎯 MVC</h3>
                <p>Arquitectura Modelo-Vista-Controlador para organizar tu código</p>
            </div>
            <div class="feature">
                <h3>🗄️ Eloquent</h3>
                <p>ORM elegante para trabajar con bases de datos</p>
            </div>
            <div class="feature">
                <h3>🔧 Artisan</h3>
                <p>Herramientas de línea de comandos para desarrollo</p>
            </div>
            <div class="feature">
                <h3>🛡️ Seguridad</h3>
                <p>Protección CSRF, autenticación y autorización</p>
            </div>
        </div>

        <div>
            <a href="/" class="btn">Página Principal</a>
            <a href="https://laravel.com/docs" target="_blank" class="btn">Documentación</a>
            @auth
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-logout">Cerrar Sesión</button>
                </form>
            @endauth
        </div>

        <div class="version">
            Laravel {{ app()->version() }} | PHP {{ phpversion() }}
        </div>
    </div>
</body>
</html>
