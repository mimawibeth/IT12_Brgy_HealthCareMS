<?php

use Illuminate\Support\Facades\Route;

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
        Route::get('/', function () {
            return view('patients.index');
        })->name('index');

        // Add new patient form
        Route::get('/create', function () {
            return view('patients.create');
        })->name('create');

        // Save new patient
        Route::post('/store', function () {
            // TODO: Save patient logic
            return redirect()->route('patients.index')->with('success', 'Patient added successfully');
        })->name('store');

        // View patient details
        Route::get('/{id}', function ($id) {
            return view('patients.show', compact('id'));
        })->name('show');

        // Edit patient
        Route::get('/{id}/edit', function ($id) {
            return view('patients.edit', compact('id'));
        })->name('edit');

        // Update patient
        Route::put('/{id}', function ($id) {
            // TODO: Update patient logic
            return redirect()->route('patients.index')->with('success', 'Patient updated successfully');
        })->name('update');

        // Delete patient
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
        Route::prefix('prenatal')->name('prenatal.')->group(function () {
            // View all prenatal records
            Route::get('/records', function () {
                return view('health-programs.prenatal.index');
            })->name('records');

            // Add prenatal record
            Route::post('/records/store', function () {
                // TODO: Store prenatal record logic
                return redirect()->route('health-programs.prenatal.records')->with('success', 'Prenatal record added successfully');
            })->name('records.store');

            // Update prenatal record
            Route::put('/records/{id}', function ($id) {
                // TODO: Update prenatal record logic
                return redirect()->route('health-programs.prenatal.records')->with('success', 'Prenatal record updated successfully');
            })->name('records.update');
        });

        // Family Planning
        Route::prefix('family-planning')->name('fp.')->group(function () {
            // View all FP client records
            Route::get('/client-records', function () {
                return view('health-programs.family-planning.index');
            })->name('records');

            // Add FP record
            Route::post('/records/store', function () {
                // TODO: Store FP record logic
                return redirect()->route('health-programs.fp.records')->with('success', 'FP record added successfully');
            })->name('records.store');

            // Update FP record
            Route::put('/records/{id}', function ($id) {
                // TODO: Update FP record logic
                return redirect()->route('health-programs.fp.records')->with('success', 'FP record updated successfully');
            })->name('records.update');
        });

        // Immunization (NIP)
        Route::prefix('immunization')->name('immunization.')->group(function () {
            // View all immunization records
            Route::get('/records', function () {
                return view('health-programs.immunization.index');
            })->name('records');

            // Add immunization record
            Route::post('/records/store', function () {
                // TODO: Store immunization record logic
                return redirect()->route('health-programs.immunization.records')->with('success', 'Immunization record added successfully');
            })->name('records.store');

            // Update immunization record
            Route::put('/records/{id}', function ($id) {
                // TODO: Update immunization record logic
                return redirect()->route('health-programs.immunization.records')->with('success', 'Immunization record updated successfully');
            })->name('records.update');
        });

        // Other Services (Wound dressing, basic emergency, etc.)
        Route::get('/other-services', function () {
            return view('health-programs.other-services');
        })->name('other-services');

        // Store other service record
        Route::post('/other-services/store', function () {
            // TODO: Store other service record logic
            return redirect()->route('health-programs.other-services')->with('success', 'Service record added successfully');
        })->name('other-services.store');
    });

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
    // USER MANAGEMENT ROUTES
    // ====================
    Route::prefix('users')->name('users.')->group(function () {
        // All users list (BHW, Admin, Workers)
        Route::get('/bhw', function () {
            return view('users.index');
        })->name('bhw');

        // Create user
        Route::get('/create', function () {
            return view('users.create');
        })->name('create');

        // Store user
        Route::post('/store', function () {
            // TODO: Store user logic
            return redirect()->route('users.bhw')->with('success', 'User created successfully');
        })->name('store');

        // Admin accounts list
        Route::get('/admin', function () {
            return view('users.admin');
        })->name('admin');

        // Assign admin role (for Super Admin only)
        Route::post('/{id}/assign-admin', function ($id) {
            // TODO: Assign admin role logic
            return back()->with('success', 'Admin role assigned successfully');
        })->name('assign-admin');

        // Role management
        Route::get('/roles', function () {
            return view('users.roles');
        })->name('roles');

        // Update user role
        Route::post('/{id}/update-role', function ($id) {
            // TODO: Update user role logic
            return back()->with('success', 'User role updated successfully');
        })->name('update-role');

        // Edit user
        Route::get('/{id}/edit', function ($id) {
            return view('users.edit', compact('id'));
        })->name('edit');

        // Update user
        Route::put('/{id}', function ($id) {
            // TODO: Update user logic
            return redirect()->route('users.bhw')->with('success', 'User updated successfully');
        })->name('update');

        // Delete user
        Route::delete('/{id}', function ($id) {
            // TODO: Delete user logic
            return redirect()->route('users.bhw')->with('success', 'User deleted successfully');
        })->name('destroy');

        // Reset password
        Route::post('/{id}/reset-password', function ($id) {
            // TODO: Reset password logic
            return back()->with('success', 'Password reset successfully');
        })->name('reset-password');
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
