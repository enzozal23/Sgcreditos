<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'guard_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
            'is_active' => 'boolean'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol creado exitosamente');
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $role->load('permissions');
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'guard_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
            'is_active' => 'boolean'
        ]);

        $role->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol actualizado exitosamente');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol eliminado exitosamente');
    }

    public function permissions(Role $role)
    {
        $permissions = Permission::all();
        $role->load('permissions');
        return view('admin.roles.permissions', compact('role', 'permissions'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array'
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles.permissions', $role)
            ->with('success', 'Permisos del rol actualizados exitosamente');
    }

    public function toggleEstado(Role $role)
    {
        $role->update([
            'is_active' => !$role->is_active
        ]);

        $message = $role->is_active ? 'Rol activado' : 'Rol desactivado';
        return redirect()->route('admin.roles.index')
            ->with('success', $message . ' exitosamente');
    }
}
