<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos básicos
        $permissions = [
            // Usuarios
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
            
            // Roles
            'roles.ver',
            'roles.crear',
            'roles.editar',
            'roles.eliminar',
            
            // Permisos
            'permisos.ver',
            'permisos.crear',
            'permisos.editar',
            'permisos.eliminar',
            
            // Empresas
            'empresas.ver',
            'empresas.crear',
            'empresas.editar',
            'empresas.eliminar',
            
            // Clientes
            'clientes.ver',
            'clientes.crear',
            'clientes.editar',
            'clientes.eliminar',
            
            // Créditos
            'creditos.ver',
            'creditos.crear',
            'creditos.editar',
            'creditos.eliminar',
            
            // Tipos de Cliente
            'tipos_cliente.ver',
            'tipos_cliente.crear',
            'tipos_cliente.editar',
            'tipos_cliente.eliminar',
            
            // Tipos de Crédito
            'tipos_credito.ver',
            'tipos_credito.crear',
            'tipos_credito.editar',
            'tipos_credito.eliminar',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ], [
                'description' => 'Permiso para ' . str_replace('.', ' ', $permission)
            ]);
        }

        // Crear roles básicos
        $roles = [
            'super_admin' => [
                'name' => 'Super Administrador',
                'description' => 'Acceso completo a todas las funcionalidades del sistema',
                'permissions' => $permissions
            ],
            'admin' => [
                'name' => 'Administrador',
                'description' => 'Administrador del sistema con acceso a la mayoría de funcionalidades',
                'permissions' => array_filter($permissions, function($permission) {
                    return !str_contains($permission, 'permisos.') && !str_contains($permission, 'roles.');
                })
            ],
            'usuario' => [
                'name' => 'Usuario',
                'description' => 'Usuario estándar con acceso básico',
                'permissions' => [
                    'clientes.ver',
                    'creditos.ver',
                    'tipos_cliente.ver',
                    'tipos_credito.ver'
                ]
            ],
            'supervisor' => [
                'name' => 'Supervisor',
                'description' => 'Supervisor con acceso a gestión de clientes y créditos',
                'permissions' => [
                    'clientes.ver',
                    'clientes.crear',
                    'clientes.editar',
                    'creditos.ver',
                    'creditos.crear',
                    'creditos.editar',
                    'tipos_cliente.ver',
                    'tipos_credito.ver'
                ]
            ]
        ];

        foreach ($roles as $key => $roleData) {
            $role = Role::firstOrCreate([
                'name' => $key,
                'guard_name' => 'web'
            ], [
                'description' => $roleData['description'],
                'is_active' => true
            ]);

            $role->syncPermissions($roleData['permissions']);
        }
    }
}
