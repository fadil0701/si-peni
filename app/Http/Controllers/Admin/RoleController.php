<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->latest()->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Role::create($validated);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil dibuat.');
    }

    public function show($id)
    {
        $role = Role::with('users')->findOrFail($id);
        return view('admin.roles.show', compact('role'));
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $role->update($validated);

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
