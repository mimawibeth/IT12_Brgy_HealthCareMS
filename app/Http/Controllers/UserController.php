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
        $query = User::query();

        // Hide super_admin accounts from admin users
        if (auth()->user()->role === 'admin') {
            $query->where('role', '!=', 'super_admin');
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('name')->paginate(10);

        return view('users.all-users', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        // Admin users can only create BHW accounts
        if (auth()->user()->role === 'admin' && $request->input('role') !== 'bhw') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['role' => 'Admin users can only create Barangay Health Worker accounts.']);
        }

        // Validate allowed roles based on user permission
        $allowedRoles = auth()->user()->role === 'super_admin'
            ? ['super_admin', 'admin', 'bhw']
            : ['bhw'];

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'role' => ['required', 'in:' . implode(',', $allowedRoles)],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Generate personalized strong password with user details
        $firstName = ucfirst(strtolower($validated['first_name']));
        $lastName = ucfirst(strtolower($validated['last_name']));

        // Get first 2-3 letters from first name
        $namePart = substr($firstName, 0, min(3, strlen($firstName)));

        // Get last name initial
        $lastInitial = substr($lastName, 0, 1);

        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lowercase = 'abcdefghjkmnpqrstuvwxyz';
        $numbers = '23456789';
        $specialChars = '@#$%&*!?';

        // Build password: NamePart + LastInitial + Numbers + SpecialChars + RandomUpper
        $generatedPassword =
            $namePart .                                    // First name part (2-3 chars)
            $lastInitial .                                 // Last name initial
            substr(str_shuffle($numbers), 0, 3) .         // 3 random numbers
            substr(str_shuffle($specialChars), 0, 2) .    // 2 special characters
            substr(str_shuffle($uppercase), 0, 2);        // 2 random uppercase

        // Shuffle the password while keeping name part recognizable at start
        $nameBase = $namePart . $lastInitial;
        $randomPart = substr($generatedPassword, strlen($nameBase));
        $generatedPassword = $nameBase . str_shuffle($randomPart);

        $fullName = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name']);

        $user = User::create([
            'name' => $fullName,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($generatedPassword),
            'role' => $validated['role'],
            'contact_number' => $validated['contact_number'] ?? null,
            'address' => $validated['address'] ?? null,
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

        return redirect()->route('users.all-users')->with([
            'success' => 'User account created successfully',
            'generated_password' => $generatedPassword,
            'new_user_email' => $user->email,
            'new_user_name' => $user->name
        ]);
    }

    public function adminAccounts(Request $request)
    {
        if ((auth()->user()->role ?? null) !== 'super_admin') {
            abort(403);
        }

        $query = User::whereIn('role', ['super_admin', 'admin']);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $admins = $query->orderBy('name')->get();

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

    public function deactivate(Request $request, $id)
    {
        // Only super admin can deactivate accounts
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);

        // Prevent deactivating own account
        if ($user->id === auth()->user()->id) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->status = 'inactive';
        $user->save();

        AuditLog::create([
            'user_id' => auth()->user()->id,
            'user_role' => auth()->user()->role,
            'action' => 'deactivate',
            'module' => 'User Management',
            'description' => 'Deactivated account: ' . $user->name . ' (ID: ' . $user->id . ')',
            'ip_address' => $request->ip(),
            'status' => 'success',
        ]);

        return redirect()->back()->with('success', 'Account deactivated successfully. User can no longer access the system.');
    }

    public function reactivate(Request $request, $id)
    {
        // Only super admin can reactivate accounts
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);

        $user->status = 'active';
        $user->save();

        AuditLog::create([
            'user_id' => auth()->user()->id,
            'user_role' => auth()->user()->role,
            'action' => 'reactivate',
            'module' => 'User Management',
            'description' => 'Reactivated account: ' . $user->name . ' (ID: ' . $user->id . ')',
            'ip_address' => $request->ip(),
            'status' => 'success',
        ]);

        return redirect()->back()->with('success', 'Account reactivated successfully. User can now access the system.');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

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
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username,' . $user->id],
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
            'contact_number' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'role' => ['required', 'in:super_admin,admin,bhw'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $fullName = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name']);

        $updateData = [
            'name' => $fullName,
            'username' => $validated['username'],
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'contact_number' => $validated['contact_number'] ?? null,
            'address' => $validated['address'] ?? null,
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
        // Only super_admin and admin can reset passwords
        if (!in_array(auth()->user()->role ?? null, ['super_admin', 'admin'])) {
            abort(403);
        }

        $user = User::findOrFail($id);

        // Prevent resetting super admin password unless you are super admin
        if ($user->role === 'super_admin' && auth()->user()->role !== 'super_admin') {
            return back()->with('error', 'Only Super Admin can reset Super Admin passwords');
        }

        // Generate personalized temporary password with user details
        $firstName = ucfirst(strtolower($user->first_name));
        $lastName = ucfirst(strtolower($user->last_name));

        // Get first 2-3 letters from first name
        $namePart = substr($firstName, 0, min(3, strlen($firstName)));

        // Get last name initial
        $lastInitial = substr($lastName, 0, 1);

        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lowercase = 'abcdefghjkmnpqrstuvwxyz';
        $numbers = '23456789';
        $specialChars = '@#$%&*!?';

        // Build password: NamePart + LastInitial + Numbers + SpecialChars + RandomUpper
        $temporaryPassword =
            $namePart .                                    // First name part (2-3 chars)
            $lastInitial .                                 // Last name initial
            substr(str_shuffle($numbers), 0, 3) .         // 3 random numbers
            substr(str_shuffle($specialChars), 0, 2) .    // 2 special characters
            substr(str_shuffle($uppercase), 0, 2);        // 2 random uppercase

        // Shuffle the password while keeping name part recognizable at start
        $nameBase = $namePart . $lastInitial;
        $randomPart = substr($temporaryPassword, strlen($nameBase));
        $temporaryPassword = $nameBase . str_shuffle($randomPart);

        $user->password = Hash::make($temporaryPassword);
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

        return back()->with([
            'success' => 'Password reset successfully',
            'reset_password' => $temporaryPassword,
            'reset_user_email' => $user->email,
            'reset_user_name' => $user->name
        ]);
    }
}
