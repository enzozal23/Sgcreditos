<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::withCount('roles')->paginate(25);
        return view('admin.permisos.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permisos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500'
        ]);

        Permission::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
            'description' => $request->description
        ]);

        return redirect()->route('admin.permisos.index')
            ->with('success', 'Permiso creado exitosamente');
    }

    public function show(Permission $permission)
    {
        $permission->load(['roles' => function($query) {
            $query->withCount('users');
        }]);
        
        return view('admin.permisos.show', compact('permission'));
    }

    public function edit(Permission $permission)
    {
        return view('admin.permisos.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'guard_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500'
        ]);

        $permission->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
            'description' => $request->description
        ]);

        return redirect()->route('admin.permisos.index')
            ->with('success', 'Permiso actualizado exitosamente');
    }

    public function destroy(Permission $permission)
    {
        if ($permission->roles()->count() > 0) {
            return redirect()->route('admin.permisos.index')
                ->with('error', 'No se puede eliminar el permiso porque está asignado a roles');
        }

        $permission->delete();

        return redirect()->route('admin.permisos.index')
            ->with('success', 'Permiso eliminado exitosamente');
    }

    public function roles(Permission $permission)
    {
        $roles = Role::all();
        $permission->load('roles');
        
        return view('admin.permisos.roles', compact('permission', 'roles'));
    }

    public function updateRoles(Request $request, Permission $permission)
    {
        $request->validate([
            'roles' => 'array'
        ]);

        $permission->syncRoles($request->roles ?? []);

        return redirect()->route('admin.permisos.roles', $permission)
            ->with('success', 'Roles del permiso actualizados exitosamente');
    }
}
