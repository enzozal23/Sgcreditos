<?php

/**
 * Script para crear el primer usuario administrador
 * Uso: php create_admin_user.php
 */

require_once 'vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== CREADOR DE USUARIO ADMINISTRADOR ===\n\n";

// Datos del usuario administrador
$adminData = [
    'name' => 'Administrador',
    'username' => 'admin',
    'nombre' => 'Administrador',
    'apellido' => 'Sistema',
    'email' => 'admin@aleph.com',
    'password' => 'admin123',
    'intentos_login' => 0,
    'bloqueado' => false,
    'habilitado' => true
];

try {
    // Verificar si ya existe un usuario con ese email
    $existingUser = User::where('email', $adminData['email'])->first();
    
    if ($existingUser) {
        echo "❌ Ya existe un usuario con el email: {$adminData['email']}\n";
        echo "Si deseas crear un nuevo usuario, modifica el email en este script.\n";
        exit(1);
    }

    // Verificar si ya existe un usuario con ese username
    $existingUsername = User::where('username', $adminData['username'])->first();
    
    if ($existingUsername) {
        echo "❌ Ya existe un usuario con el username: {$adminData['username']}\n";
        echo "Si deseas crear un nuevo usuario, modifica el username en este script.\n";
        exit(1);
    }

    // Crear el usuario
    $user = User::create([
        'name' => $adminData['name'],
        'username' => $adminData['username'],
        'nombre' => $adminData['nombre'],
        'apellido' => $adminData['apellido'],
        'email' => $adminData['email'],
        'password' => Hash::make($adminData['password']),
        'intentos_login' => $adminData['intentos_login'],
        'bloqueado' => $adminData['bloqueado'],
        'habilitado' => $adminData['habilitado'],
        'email_verified_at' => now() // Marcar email como verificado
    ]);

    echo "✅ Usuario administrador creado exitosamente!\n\n";
    echo "📋 Detalles del usuario:\n";
    echo "   Nombre: {$user->nombre} {$user->apellido}\n";
    echo "   Username: {$user->username}\n";
    echo "   Email: {$user->email}\n";
    echo "   Contraseña: {$adminData['password']}\n";
    echo "   Estado: " . ($user->habilitado ? 'Habilitado' : 'Deshabilitado') . "\n";
    echo "   Bloqueado: " . ($user->bloqueado ? 'Sí' : 'No') . "\n\n";
    
    echo "🔐 Puedes acceder al sistema con:\n";
    echo "   URL: http://localhost:8000/login\n";
    echo "   Usuario: {$user->username} o {$user->email}\n";
    echo "   Contraseña: {$adminData['password']}\n\n";
    
    echo "⚠️  IMPORTANTE: Cambia la contraseña después del primer acceso por seguridad.\n";

} catch (Exception $e) {
    echo "❌ Error al crear el usuario: " . $e->getMessage() . "\n";
    exit(1);
}
