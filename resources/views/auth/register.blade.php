<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Registro - Aleph Manager</title>
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">
    	<link rel="shortcut icon" type="image/png" href="{{ asset('build/sgc_theme/img/favicon.png') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('build/assets/bootstrap/dist/css/bootstrap.min.css') }}">

    <script type="text/javascript" src="{{ asset('build/assets/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/jqueryui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/form/dist/jquery.form.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/bootstrap/dist/js/bootstrap.min.js') }}"></script>

    <style>
        body, html {
            height: 100%;
            background-repeat: no-repeat;
            background: url(/images/login-background.jpg) center top no-repeat;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .register-box {
            background: rgba(8, 34, 71, 0.82);
            border: none;
            border-radius: 10px;
            padding: 30px;
            margin-top: 50px;
        }

        .form-control {
            height: 44px;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .btn-register {
            background-color: #0055CC;
            padding: 10px;
            font-weight: 700;
            font-size: 14px;
            height: 44px;
            border-radius: 3px;
            border: none;
            color: white;
            width: 100%;
        }

        .btn-register:hover {
            background-color: #005fff;
        }

        .text-white {
            color: white !important;
        }

        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card register-box">
                    <div class="card-heading text-center">
                        <h3 class="text-white">Registro de Usuario</h3>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Username -->
                            <div class="form-group">
                                <input type="text" id="username" name="username" class="form-control" 
                                       placeholder="Username" value="{{ old('username') }}" required autofocus>
                            </div>

                            <!-- Nombre -->
                            <div class="form-group">
                                <input type="text" id="nombre" name="nombre" class="form-control" 
                                       placeholder="Nombre" value="{{ old('nombre') }}" required>
                            </div>

                            <!-- Apellido -->
                            <div class="form-group">
                                <input type="text" id="apellido" name="apellido" class="form-control" 
                                       placeholder="Apellido" value="{{ old('apellido') }}" required>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <input type="email" id="email" name="email" class="form-control" 
                                       placeholder="Email" value="{{ old('email') }}" required>
                            </div>

                            <!-- Password -->
                            <div class="form-group">
                                <input type="password" id="password" name="password" class="form-control" 
                                       placeholder="Contraseña" required>
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-group">
                                <input type="password" id="password_confirmation" name="password_confirmation" 
                                       class="form-control" placeholder="Confirmar Contraseña" required>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-register">
                                    Registrarse
                                </button>
                            </div>

                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-white">
                                    ¿Ya tienes cuenta? Inicia sesión
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
