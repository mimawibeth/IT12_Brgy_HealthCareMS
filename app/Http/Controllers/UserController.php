<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('name')->paginate(10);

        return view('users.all-users', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:12',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{12,}$/',
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:50'],
            'role' => ['required', 'in:super_admin,admin,bhw'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $fullName = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name']);

        $user = User::create([
            'name' => $fullName,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'contact_number' => $validated['contact_number'],
            'status' => $validated['status'],
        ]);

        AuditLog::create([
            'user_id' => $request->user()->id ?? null,
            'user_role' => $request->user()->role ?? null,
            'action' => 'create',
            'module' => 'User Management',
            'description' => 'Created new user account: ' . $user->name . ' (' . $user->role . ')',
            'ip_address' => $request->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('users.all-users')->with('success', 'User account created successfully');
    }

    public function adminAccounts()
    {
        if ((auth()->user()->role ?? null) !== 'super_admin') {
            abort(403);
        }

        $admins = User::whereIn('role', ['super_admin', 'admin'])->orderBy('name')->get();

        return view('users.admin-accounts', compact('admins'));
    }

    public function roleManagement()
    {
        if ((auth()->user()->role ?? null) !== 'super_admin') {
            abort(403);
        }

        $rolesSummary = [
            'super_admin' => [
                'total' => User::where('role', 'super_admin')->count(),
                'active' => User::where('role', 'super_admin')->where('status', 'active')->count(),
            ],
            'admin' => [
                'total' => User::where('role', 'admin')->count(),
                'active' => User::where('role', 'admin')->where('status', 'active')->count(),
            ],
            'bhw' => [
                'total' => User::where('role', 'bhw')->count(),
                'active' => User::where('role', 'bhw')->where('status', 'active')->count(),
            ],
        ];

        $roles = Role::orderBy('name')->get();

        return view('users.role-management', compact('rolesSummary', 'roles'));
    }

    public function promoteAdmin(Request $request)
    {
        if (($request->user()->role ?? null) !== 'super_admin') {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->role = 'admin';
        $user->save();

        AuditLog::create([
            'user_id' => $request->user()->id ?? null,
            'user_role' => $request->user()->role ?? null,
            'action' => 'update',
            'module' => 'User Management',
            'description' => 'Promoted user ' . $user->name . ' to Admin',
            'ip_address' => $request->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('users.admin-accounts')->with('success', 'User promoted to Admin successfully');
    }

    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);

        return response()->json($user);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => [
                'nullable',
                'string',
                'min:12',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{12,}$/',
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:50'],
            'role' => ['required', 'in:super_admin,admin,bhw'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $fullName = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name']);

        $updateData = [
            'name' => $fullName,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'contact_number' => $validated['contact_number'],
            'status' => $validated['status'],
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        AuditLog::create([
            'user_id' => $request->user()->id ?? null,
            'user_role' => $request->user()->role ?? null,
            'action' => 'update',
            'module' => 'User Management',
            'description' => 'Updated user account: ' . $user->name . ' (' . $user->role . ')',
            'ip_address' => $request->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('users.all-users')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            return redirect()->route('users.all-users')->with('error', 'You cannot delete your own account');
        }

        $userName = $user->name;
        $userRole = $user->role;

        $user->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role ?? null,
            'action' => 'delete',
            'module' => 'User Management',
            'description' => 'Deleted user account: ' . $userName . ' (' . $userRole . ')',
            'ip_address' => request()->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('users.all-users')->with('success', 'User deleted successfully');
    }

    public function updateRole(Request $request, $id)
    {
        if ((auth()->user()->role ?? null) !== 'super_admin') {
            abort(403);
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role' => ['required', 'in:super_admin,admin,bhw'],
        ]);

        $oldRole = $user->role;
        $user->role = $validated['role'];
        $user->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role ?? null,
            'action' => 'update',
            'module' => 'User Management',
            'description' => 'Updated role for ' . $user->name . ' from ' . $oldRole . ' to ' . $validated['role'],
            'ip_address' => request()->ip(),
            'status' => 'success',
        ]);

        return back()->with('success', 'User role updated successfully');
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:12',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{12,}$/',
            ],
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role ?? null,
            'action' => 'update',
            'module' => 'User Management',
            'description' => 'Reset password for user: ' . $user->name,
            'ip_address' => request()->ip(),
            'status' => 'success',
        ]);

        return back()->with('success', 'Password reset successfully');
    }
}
