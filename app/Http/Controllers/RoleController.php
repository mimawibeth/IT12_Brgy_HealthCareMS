<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:255'],
            'role_color' => ['nullable', 'string', 'max:20'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'max:50'],
        ]);

        $slug = Str::slug($validated['role_name']);

        $role = Role::create([
            'name' => $validated['role_name'],
            'slug' => $slug,
            'badge_color' => $validated['role_color'] ?? '#3498db',
            'permissions' => $validated['permissions'] ?? [],
        ]);

        AuditLog::create([
            'user_id' => $request->user()->id ?? null,
            'user_role' => $request->user()->role ?? null,
            'action' => 'create',
            'module' => 'Role Management',
            'description' => 'Created role: '.$role->name,
            'ip_address' => $request->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('users.role-management')->with('success', 'Role created successfully');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:255'],
            'role_color' => ['nullable', 'string', 'max:20'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'max:50'],
        ]);

        $slug = Str::slug($validated['role_name']);

        $role->update([
            'name' => $validated['role_name'],
            'slug' => $slug,
            'badge_color' => $validated['role_color'] ?? $role->badge_color,
            'permissions' => $validated['permissions'] ?? [],
        ]);

        AuditLog::create([
            'user_id' => $request->user()->id ?? null,
            'user_role' => $request->user()->role ?? null,
            'action' => 'update',
            'module' => 'Role Management',
            'description' => 'Updated role: '.$role->name,
            'ip_address' => $request->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('users.role-management')->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role, Request $request)
    {
        $name = $role->name;
        $role->delete();

        AuditLog::create([
            'user_id' => $request->user()->id ?? null,
            'user_role' => $request->user()->role ?? null,
            'action' => 'delete',
            'module' => 'Role Management',
            'description' => 'Deleted role: '.$name,
            'ip_address' => $request->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('users.role-management')->with('success', 'Role deleted successfully');
    }
}
