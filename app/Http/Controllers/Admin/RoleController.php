<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $perPage = \App\Helpers\PaginationHelper::getPerPage($request, 10);
        $roles = Role::withCount('users')->latest()->paginate($perPage)->appends($request->query());
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('module')->orderBy('sort_order')->get()->groupBy('module');
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Attach permissions
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil dibuat.');
    }

    public function show($id)
    {
        $role = Role::with(['users', 'permissions'])->findOrFail($id);
        return view('admin.roles.show', compact('role'));
    }

    public function edit($id)
    {
        $role = Role::with(['permissions', 'users.modules'])->findOrFail($id);
        
        // Ambil modules dari semua user yang menggunakan role ini
        $userModules = collect();
        foreach ($role->users as $user) {
            $userModules = $userModules->merge($user->modules->pluck('name'));
        }
        $userModules = $userModules->unique()->sort()->values();
        
        // Filter permissions berdasarkan modules user
        if ($userModules->isNotEmpty()) {
            $permissions = Permission::whereIn('module', $userModules)
                ->orderBy('module')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('module');
        } else {
            // Jika tidak ada user atau user tidak punya modules, tampilkan semua permission
        $permissions = Permission::orderBy('module')->orderBy('sort_order')->get()->groupBy('module');
        }
        
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions', 'userModules'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Sync permissions
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        } else {
            $role->permissions()->detach();
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Prevent deletion if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role tidak dapat dihapus karena masih memiliki user.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}
