<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\FamilyPlanningRecordController;
use App\Http\Controllers\PrenatalRecordController;
use App\Http\Controllers\NipRecordController;
use App\Http\Controllers\NewNipRecordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\MedicineBatchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ApprovalController;

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
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $ip = request()->ip();

    // Attempt to authenticate user
    $remember = request()->boolean('remember');

    if (
        auth()->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember)
    ) {
        request()->session()->regenerate();

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role ?? null,
            'action' => 'login',
            'module' => 'Authentication',
            'description' => 'User logged in successfully',
            'ip_address' => $ip,
            'status' => 'success',
        ]);

        return redirect()->intended('dashboard');
    }

    \App\Models\AuditLog::create([
        'user_id' => null,
        'user_role' => null,
        'action' => 'login',
        'module' => 'Authentication',
        'description' => 'Failed login attempt - Invalid credentials',
        'ip_address' => $ip,
        'status' => 'failed',
    ]);

    // Authentication failed
    return back()->withErrors([
        'email' => 'Invalid credentials. Please try again.',
    ])->onlyInput('email');
})->name('login.post');

Route::get('/logout', function () {
    $user = auth()->user();
    $ip = request()->ip();

    if ($user) {
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'user_role' => $user->role ?? null,
            'action' => 'logout',
            'module' => 'Authentication',
            'description' => 'User logged out',
            'ip_address' => $ip,
            'status' => 'success',
        ]);
    }

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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

        // Edit patient
        Route::get('/{id}/edit', [PatientController::class, 'edit'])->name('edit');

        // Store new assessments for patient
        Route::post('/{id}/assessments', [PatientController::class, 'storeAssessments'])->name('assessments.store');

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
        Route::get('/prenatal', [PrenatalRecordController::class, 'index'])
            ->name('prenatal-view');

        Route::post('/prenatal', [PrenatalRecordController::class, 'store'])
            ->name('prenatal-store');

        Route::get('/prenatal/{record}/edit', [PrenatalRecordController::class, 'edit'])
            ->name('prenatal-edit');

        Route::post('/prenatal/{record}/visits', [PrenatalRecordController::class, 'storeVisits'])
            ->name('prenatal-visits-store');

        // Family Planning
        Route::get('/family-planning', [FamilyPlanningRecordController::class, 'index'])
            ->name('family-planning-view');

        Route::post('/family-planning', [FamilyPlanningRecordController::class, 'store'])
            ->name('family-planning-store');

        Route::get('/family-planning/{record}/edit', [FamilyPlanningRecordController::class, 'edit'])
            ->name('family-planning-edit');

        Route::put('/family-planning/{record}', [FamilyPlanningRecordController::class, 'update'])
            ->name('family-planning-update');

        // New Immunization (NIP) - dedicated controller and view
        Route::get('/new-immunization', [NewNipRecordController::class, 'index'])
            ->name('new-nip-view');

        Route::get('/new-immunization/create', [NewNipRecordController::class, 'create'])
            ->name('new-nip-create');

        Route::post('/new-immunization', [NewNipRecordController::class, 'store'])
            ->name('new-nip-store');

        Route::get('/new-immunization/{record}/edit', [NewNipRecordController::class, 'edit'])
            ->name('new-nip-edit');

        Route::put('/new-immunization/{record}', [NewNipRecordController::class, 'update'])
            ->name('new-nip-update');
    });

    Route::get('/health-programs/other-services', function () {
        return view('health-programs.other-services');
    })->name('health-programs.other-services');

    // ====================
    // MEDICINE & INVENTORY ROUTES
    // ====================
    Route::prefix('medicine')->name('medicine.')->group(function () {
        // Medicine list
        Route::get('/', [MedicineController::class, 'index'])->name('index');

        // Add medicine
        Route::get('/create', [MedicineController::class, 'create'])->name('create');

        // Store medicine
        Route::post('/store', [MedicineController::class, 'store'])->name('store');

        // Dispense medicine page
        Route::get('/dispense', [MedicineController::class, 'dispense'])->name('dispense');

        // Process dispense
        Route::post('/dispense/store', [MedicineController::class, 'storeDispense'])->name('dispense.store');

        // Edit medicine
        Route::get('/{medicine}/edit', [MedicineController::class, 'edit'])->name('edit');

        // Update medicine
        Route::put('/{medicine}', [MedicineController::class, 'update'])->name('update');

        // Delete medicine
        Route::delete('/{medicine}', [MedicineController::class, 'destroy'])->name('destroy');

        // Medicine batches (per-batch inventory tracking)
        Route::get('/batches', [MedicineBatchController::class, 'index'])->name('batches.index');
        Route::post('/batches', [MedicineBatchController::class, 'store'])->name('batches.store');
        Route::put('/batches/{batch}', [MedicineBatchController::class, 'update'])->name('batches.update');
        Route::delete('/batches/{batch}', [MedicineBatchController::class, 'destroy'])->name('batches.destroy');
    });

    // ====================
    // USER MANAGEMENT ROUTES (Super Admin/Admin UI; backend checks can be added via middleware later)
    // ====================
    Route::prefix('users')->name('users.')->group(function () {
        // All Users - View all system users
        Route::get('/all-users', [UserController::class, 'index'])->name('all-users');

        // Add New User - Form to create new user account
        Route::get('/add-new-user', [UserController::class, 'create'])->name('add-new');

        // Store new user
        Route::post('/store', [UserController::class, 'store'])->name('store');

        // Admin Accounts - View only Admin users
        Route::get('/admin-accounts', [UserController::class, 'adminAccounts'])->name('admin-accounts');

        // Role Management - View and manage user roles and permissions
        Route::get('/role-management', [UserController::class, 'roleManagement'])->name('role-management');

        // View user details
        Route::get('/{id}', [UserController::class, 'show'])->name('show');

        // Edit user
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');

        // Update user
        Route::put('/{id}', [UserController::class, 'update'])->name('update');

        // Delete user
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');

        // Update user role
        Route::post('/{id}/update-role', [UserController::class, 'updateRole'])->name('update-role');

        // Reset password
        Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');

        // Toggle user status (Active/Inactive)
        Route::post('/{id}/toggle-status', function ($id) {
            // TODO: Toggle user status logic
            return back()->with('success', 'User status updated successfully');
        })->name('toggle-status');

        // Store new role
        Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');

        // Update role
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');

        // Delete role
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        // Promote existing user to Admin
        Route::post('/promote-admin', [UserController::class, 'promoteAdmin'])->name('promote-admin');
    });

    // ====================
    // REPORTS & ANALYTICS ROUTES
    // ====================
    Route::prefix('reports')->name('reports.')->group(function () {
        // Reports dashboard
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');

        // Generate report (placeholder - can be extended later)
        Route::post('/generate', [ReportController::class, 'monthly'])->name('generate');

        // Export and print remain UI-only for now
        Route::get('/export', function () {
            if ((auth()->user()->role ?? null) === 'bhw') {
                abort(403);
            }
            return back()->with('success', 'Report exported successfully');
        })->name('export');

        Route::get('/print/{type}', function ($type) {
            if ((auth()->user()->role ?? null) === 'bhw') {
                abort(403);
            }
            return view('reports.print', compact('type'));
        })->name('print');
    });

    // ====================
    // AUDIT LOGS ROUTES (Super Admin/Admin only)
    // ====================
    Route::prefix('logs')->name('logs.')->group(function () {
        // Audit logs - view all system activities
        Route::get('/audit', [AuditLogController::class, 'index'])->name('audit');
    });

    // ====================
    // SETTINGS ROUTES
    // ====================
    // SETTINGS
    // ====================
    Route::prefix('settings')->name('settings.')->group(function () {
        // System settings page
        Route::get('/', function () {
            return view('settings.index');
        })->name('index');

        // Update settings
        Route::post('/update', function () {
            $data = request()->validate([
                'dark_mode' => ['required', 'in:0,1'],
            ]);

            $user = auth()->user();
            if ($user) {
                $user->dark_mode = $data['dark_mode'] === '1';
                $user->save();
            }

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

    // ====================
    // APPROVAL ROUTES
    // ====================
    // Dashboard - accessible to all authenticated users
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');

    // Financial Assistance Requests
    Route::get('/approvals/financial/create', [ApprovalController::class, 'createFinancial'])->name('approvals.financial.create');
    Route::post('/approvals/financial', [ApprovalController::class, 'storeFinancial'])->name('approvals.financial.store');

    // Medical Supplies Requests
    Route::get('/approvals/medical/create', [ApprovalController::class, 'createMedical'])->name('approvals.medical.create');
    Route::post('/approvals/medical', [ApprovalController::class, 'storeMedical'])->name('approvals.medical.store');

    // Admin approval actions
    Route::post('/approvals/{type}/{id}/admin-approve', [ApprovalController::class, 'adminApprove'])->name('approvals.admin-approve');
    Route::post('/approvals/{type}/{id}/admin-reject', [ApprovalController::class, 'adminReject'])->name('approvals.admin-reject');

    // Superadmin approval actions
    Route::post('/approvals/{type}/{id}/superadmin-approve', [ApprovalController::class, 'superadminApprove'])->name('approvals.superadmin-approve');
    Route::post('/approvals/{type}/{id}/superadmin-reject', [ApprovalController::class, 'superadminReject'])->name('approvals.superadmin-reject');

    // View request details (JSON endpoint for modal)
    Route::get('/approvals/{type}/{id}', [ApprovalController::class, 'show'])->name('approvals.show');

}); // End of auth middleware group

