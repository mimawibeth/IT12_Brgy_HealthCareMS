<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;

/*
|--------------------------------------------------------------------------
| Web Routes - Barangay Health Center System
|--------------------------------------------------------------------------
| Simple route structure with clear naming
| All routes are organized by module
*/

// ====================
// AUTHENTICATION ROUTES
// ====================
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    // Validate login credentials
    $credentials = request()->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    // Attempt to authenticate user
    if (auth()->attempt($credentials)) {
        request()->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    // Authentication failed
    return back()->withErrors([
        'username' => 'Invalid credentials. Please try again.',
    ])->onlyInput('username');
})->name('login.post');

Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// ====================
// PROTECTED ROUTES (Require Authentication)
// ====================
Route::middleware('auth')->group(function () {

    // ====================
// DASHBOARD ROUTE
// ====================
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // ====================
    // PATIENT MANAGEMENT ROUTES
    // ====================
    Route::prefix('patients')->name('patients.')->group(function () {
        // Patient list
        Route::get('/', [PatientController::class, 'index'])->name('index');

        // Add new patient form
        Route::get('/create', [PatientController::class, 'create'])->name('create');

        // Save new patient (with assessments)
        Route::post('/store', [PatientController::class, 'store'])->name('store');

        // JSON details for view modal
        Route::get('/{patient}', [PatientController::class, 'show'])->name('show');

        // Edit, update, and delete can be wired later as needed
        Route::get('/{id}/edit', function ($id) {
            return view('patients.edit', compact('id'));
        })->name('edit');

        Route::put('/{id}', function ($id) {
            // TODO: Update patient logic
            return redirect()->route('patients.index')->with('success', 'Patient updated successfully');
        })->name('update');

        Route::delete('/{id}', function ($id) {
            // TODO: Delete patient logic
            return redirect()->route('patients.index')->with('success', 'Patient deleted successfully');
        })->name('destroy');
    });

    // ====================
    // HEALTH PROGRAMS ROUTES
    // ====================
    Route::prefix('health-programs')->name('health-programs.')->group(function () {
        // Prenatal Care
        Route::get('/prenatal', function () {
            return view('health-programs.prenatal');
        })->name('prenatal-view');

        Route::get('/prenatal/{id}/edit', function ($id) {
            return view('health-programs.prenatal-edit', compact('id'));
        })->name('prenatal-edit');

        // Family Planning
        Route::get('/family-planning', function () {
            return view('health-programs.family-planning');
        })->name('family-planning-view');

        Route::get('/family-planning/{id}/edit', function ($id) {
            return view('health-programs.family-planning-edit', compact('id'));
        })->name('family-planning-edit');

        // Immunization (NIP)
        Route::get('/immunization', function () {
            return view('health-programs.nip');
        })->name('nip-view');

        Route::get('/immunization/{id}/edit', function ($id) {
            return view('health-programs.nip-edit', compact('id'));
        })->name('nip-edit');
    });

    Route::get('/health-programs/other-services', function () {
        return view('health-programs.other-services');
    })->name('health-programs.other-services');

    // ====================
    // MEDICINE & INVENTORY ROUTES
    // ====================
    Route::prefix('medicine')->name('medicine.')->group(function () {
        // Medicine list
        Route::get('/', function () {
            return view('medicine.index');
        })->name('index');

        // Add medicine
        Route::get('/create', function () {
            return view('medicine.create');
        })->name('create');

        // Store medicine
        Route::post('/store', function () {
            // TODO: Store medicine logic
            return redirect()->route('medicine.index')->with('success', 'Medicine added successfully');
        })->name('store');

        // Dispense medicine page
        Route::get('/dispense', function () {
            return view('medicine.dispense');
        })->name('dispense');

        // Process dispense
        Route::post('/dispense/store', function () {
            // TODO: Process dispense logic
            return redirect()->route('medicine.dispense')->with('success', 'Medicine dispensed successfully');
        })->name('dispense.store');

        // Edit medicine
        Route::get('/{id}/edit', function ($id) {
            return view('medicine.edit', compact('id'));
        })->name('edit');

        // Update medicine
        Route::put('/{id}', function ($id) {
            // TODO: Update medicine logic
            return redirect()->route('medicine.index')->with('success', 'Medicine updated successfully');
        })->name('update');

        // Delete medicine
        Route::delete('/{id}', function ($id) {
            // TODO: Delete medicine logic
            return redirect()->route('medicine.index')->with('success', 'Medicine deleted successfully');
        })->name('destroy');
    });

    // ====================
    // USER MANAGEMENT ROUTES (Super Admin Access)
    // ====================
    Route::prefix('users')->name('users.')->group(function () {
        // All Users - View all system users (Super Admin, Admin, BHW)
        Route::get('/all-users', function () {
            return view('users.all-users');
        })->name('all-users');

        // Add New User - Form to create new user account
        Route::get('/add-new-user', function () {
            return view('users.add-new-user');
        })->name('add-new');

        // Store new user
        Route::post('/store', function () {
            // TODO: Store user logic with role assignment
            return redirect()->route('users.all-users')->with('success', 'User account created successfully');
        })->name('store');

        // Admin Accounts - View only Admin users
        Route::get('/admin-accounts', function () {
            return view('users.admin-accounts');
        })->name('admin-accounts');

        // Role Management - View and manage user roles and permissions
        Route::get('/role-management', function () {
            return view('users.role-management');
        })->name('role-management');

        // View user details
        Route::get('/{id}', function ($id) {
            return view('users.show', compact('id'));
        })->name('show');

        // Edit user
        Route::get('/{id}/edit', function ($id) {
            return view('users.edit', compact('id'));
        })->name('edit');

        // Update user
        Route::put('/{id}', function ($id) {
            // TODO: Update user logic
            return redirect()->route('users.all-users')->with('success', 'User updated successfully');
        })->name('update');

        // Delete user
        Route::delete('/{id}', function ($id) {
            // TODO: Delete user logic
            return redirect()->route('users.all-users')->with('success', 'User deleted successfully');
        })->name('destroy');

        // Update user role
        Route::post('/{id}/update-role', function ($id) {
            // TODO: Update user role logic (Super Admin, Admin, BHW)
            return back()->with('success', 'User role updated successfully');
        })->name('update-role');

        // Reset password
        Route::post('/{id}/reset-password', function ($id) {
            // TODO: Reset password logic
            return back()->with('success', 'Password reset successfully');
        })->name('reset-password');

        // Toggle user status (Active/Inactive)
        Route::post('/{id}/toggle-status', function ($id) {
            // TODO: Toggle user status logic
            return back()->with('success', 'User status updated successfully');
        })->name('toggle-status');

        // Store new role
        Route::post('/roles/store', function () {
            // TODO: Store new role logic
            return redirect()->route('users.role-management')->with('success', 'Role created successfully');
        })->name('roles.store');

        // Edit role
        Route::get('/roles/{id}/edit', function ($id) {
            // TODO: Return edit role view
            return back();
        })->name('roles.edit');

        // Update role
        Route::put('/roles/{id}', function ($id) {
            // TODO: Update role logic
            return redirect()->route('users.role-management')->with('success', 'Role updated successfully');
        })->name('roles.update');

        // Delete role
        Route::delete('/roles/{id}', function ($id) {
            // TODO: Delete role logic (with validation - don't delete if users assigned)
            return redirect()->route('users.role-management')->with('success', 'Role deleted successfully');
        })->name('roles.destroy');

        // Promote existing user to Admin
        Route::post('/promote-admin', function () {
            // TODO: Promote user to admin role logic
            return redirect()->route('users.admin-accounts')->with('success', 'User promoted to Admin successfully');
        })->name('promote-admin');
    });

    // ====================
    // REPORTS & ANALYTICS ROUTES
    // ====================
    Route::prefix('reports')->name('reports.')->group(function () {
        // Reports dashboard
        Route::get('/monthly', function () {
            return view('reports.index');
        })->name('monthly');

        // Generate report
        Route::post('/generate', function () {
            // TODO: Generate report logic
            return back()->with('success', 'Report generated successfully');
        })->name('generate');

        // Export report
        Route::get('/export', function () {
            // TODO: Export report logic (PDF/Excel)
            return back()->with('success', 'Report exported successfully');
        })->name('export');

        // Print report
        Route::get('/print/{type}', function ($type) {
            return view('reports.print', compact('type'));
        })->name('print');
    });

    // ====================
    // AUDIT LOGS ROUTES
    // ====================
    Route::prefix('logs')->name('logs.')->group(function () {
        // Audit logs - view all system activities
        Route::get('/audit', function () {
            return view('logs.audit');
        })->name('audit');

        // Filter logs by date/user/action
        Route::get('/audit/filter', function () {
            // TODO: Filter logs logic
            return view('logs.audit');
        })->name('audit.filter');

        // Export logs
        Route::get('/audit/export', function () {
            // TODO: Export logs logic
            return back()->with('success', 'Logs exported successfully');
        })->name('audit.export');
    });

    // ====================
    // SETTINGS ROUTES
    // ====================
    Route::prefix('settings')->name('settings.')->group(function () {
        // System settings page
        Route::get('/', function () {
            return view('settings.index');
        })->name('index');

        // Update settings
        Route::post('/update', function () {
            // TODO: Update settings logic
            return back()->with('success', 'Settings updated successfully');
        })->name('update');

        // Backup database
        Route::post('/backup', function () {
            // TODO: Backup database logic
            return back()->with('success', 'Database backup created successfully');
        })->name('backup');

        // Restore database
        Route::post('/restore', function () {
            // TODO: Restore database logic
            return back()->with('success', 'Database restored successfully');
        })->name('restore');

        // Profile settings
        Route::get('/profile', function () {
            return view('settings.profile');
        })->name('profile');

        // Update profile
        Route::put('/profile/update', function () {
            // TODO: Update profile logic
            return back()->with('success', 'Profile updated successfully');
        })->name('profile.update');

        // Change password
        Route::post('/change-password', function () {
            // TODO: Change password logic
            return back()->with('success', 'Password changed successfully');
        })->name('change-password');
    });

}); // End of auth middleware group
