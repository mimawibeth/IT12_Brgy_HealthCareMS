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
use App\Http\Controllers\EventController;
use App\Http\Controllers\MedicalSupplyController;

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
    // If user is authenticated, redirect to dashboard, otherwise to login
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Auth check endpoint (for JavaScript to verify authentication status)
Route::get('/auth/check', function () {
    return response()->json(['authenticated' => auth()->check()]);
})->name('auth.check');

// Login routes - only accessible to guests (non-authenticated users)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
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
}); // End of guest middleware group

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

        // Update patient
        Route::put('/{id}', [PatientController::class, 'update'])->name('update');

        // Delete patient
        Route::delete('/{id}', [PatientController::class, 'destroy'])->name('destroy');

        // Store new assessments for patient
        Route::post('/{id}/assessments', [PatientController::class, 'storeAssessments'])->name('assessments.store');
    });

    // API Routes for patient search
    Route::get('/api/patients/search', [PatientController::class, 'search'])->name('api.patients.search');

    // ====================
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

        Route::get('/prenatal/{record}', [PrenatalRecordController::class, 'show'])
            ->name('prenatal-show');

        // Family Planning
        Route::get('/family-planning', [FamilyPlanningRecordController::class, 'index'])
            ->name('family-planning-view');

        Route::post('/family-planning', [FamilyPlanningRecordController::class, 'store'])
            ->name('family-planning-store');

        Route::get('/family-planning/{record}/edit', [FamilyPlanningRecordController::class, 'edit'])
            ->name('family-planning-edit');

        Route::put('/family-planning/{record}', [FamilyPlanningRecordController::class, 'update'])
            ->name('family-planning-update');

        Route::get('/family-planning/{record}', [FamilyPlanningRecordController::class, 'show'])
            ->name('family-planning-show');

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

        Route::get('/new-immunization/{record}', [NewNipRecordController::class, 'show'])
            ->name('new-nip-show');
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

        // Medicine batches (per-batch inventory tracking)
        Route::get('/batches', [MedicineBatchController::class, 'index'])->name('batches.index');
        Route::post('/batches', [MedicineBatchController::class, 'store'])->name('batches.store');
        Route::put('/batches/{batch}', [MedicineBatchController::class, 'update'])->name('batches.update');
        Route::delete('/batches/{batch}', [MedicineBatchController::class, 'destroy'])->name('batches.destroy');

        // Edit medicine
        Route::get('/{medicine}/edit', [MedicineController::class, 'edit'])->name('edit');

        // Show medicine details
        Route::get('/{medicine}', [MedicineController::class, 'show'])->name('show');

        // Update medicine
        Route::put('/{medicine}', [MedicineController::class, 'update'])->name('update');

        // Delete medicine
        Route::delete('/{medicine}', [MedicineController::class, 'destroy'])->name('destroy');
    });

    // ====================
    // MEDICAL SUPPLIES INVENTORY ROUTES
    // ====================
    Route::prefix('medical-supplies')->name('medical-supplies.')->group(function () {
        // Display medical supplies inventory list
        Route::get('/', [MedicalSupplyController::class, 'index'])->name('index');

        // Display supply history (incoming and outgoing transactions) - MUST be before /{supply}
        Route::get('/history', [MedicalSupplyController::class, 'history'])->name('history');

        // Store new supply or add to existing
        Route::post('/store', [MedicalSupplyController::class, 'store'])->name('store');

        // View supply details - MUST be last due to dynamic parameter
        Route::get('/{supply}', [MedicalSupplyController::class, 'show'])->name('show');
    });

    // API route for supply search
    Route::get('/api/medical-supplies/search', [MedicalSupplyController::class, 'search'])->name('api.medical-supplies.search');

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

        // Edit user
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');

        // View user details (must be after /edit to avoid conflicts)
        Route::get('/{id}', [UserController::class, 'show'])->name('show');

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
                'first_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['nullable', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'username' => ['required', 'string', 'max:255'],
                'contact_number' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:500'],
                'dark_mode' => ['required', 'in:0,1'],
                'text_size' => ['required', 'in:small,medium,large'],
            ]);

            $user = auth()->user();
            if ($user) {
                $user->fill([
                    'first_name' => $data['first_name'],
                    'middle_name' => $data['middle_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'username' => $data['username'],
                    'contact_number' => $data['contact_number'],
                    'address' => $data['address'],
                    'dark_mode' => $data['dark_mode'] === '1',
                    'text_size' => $data['text_size'],
                ]);
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
    // EVENT CALENDAR ROUTES
    // ====================
    Route::prefix('events')->name('events.')->group(function () {
        // Event calendar view (accessible to all authenticated users)
        Route::get('/', [EventController::class, 'index'])->name('index');

        // Get events as JSON for calendar
        Route::get('/api', [EventController::class, 'getEvents'])->name('api');

        // CRUD routes (only for superadmin and admin - authorization checked in controller)
        Route::get('/create', [EventController::class, 'create'])->name('create');
        Route::post('/', [EventController::class, 'store'])->name('store');
        Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
        Route::put('/{event}', [EventController::class, 'update'])->name('update');
        Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
    });

}); // End of auth middleware group

