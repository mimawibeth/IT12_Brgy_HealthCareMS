{{-- Add New User Form --}}
@extends('layouts.app')

@section('title', 'Add New User')
@section('page-title', 'Add New User')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="page-content" style="max-width: 1200px; margin: 0 auto;">

        <!-- Form Container -->
        <div class="form-container">
            <div class="form-header" style="margin-bottom: 20px;">
                <h2 class="form-title" style="font-size: 20px; color: #2c3e50; margin-bottom: 4px;">Create New User Account</h2>
                <p class="form-subtitle" style="color: #7f8c8d; font-size: 13px;">Fill in the required information below to create a new system user</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger"
                    style="background: #fee2e2; border: 1px solid #fecaca; padding: 15px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #dc2626;">
                    <strong style="color: #dc2626; display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                        <i class="bi bi-exclamation-triangle-fill"></i> Validation Errors:
                    </strong>
                    <ul style="margin: 0 0 0 20px; color: #991b1b; line-height: 1.6;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('users.store') }}" class="user-form">
                @csrf

                <!-- Account Credentials -->
                <div class="form-section" style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 18px; border: 1px solid #e2e8f0;">
                    <h3 class="section-header" style="color: #2f6d7e; font-size: 16px; margin-bottom: 18px; padding-bottom: 8px; border-bottom: 2px solid #2f6d7e; display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-shield-lock-fill" style="font-size: 15px;"></i> Account Credentials
                    </h3>

                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px;">
                        <div class="form-group">
                            <label for="username" style="display: block; font-weight: 600; margin-bottom: 6px; color: #334155; font-size: 13px;">Username <span style="color: #dc2626;">*</span></label>
                            <input type="text" id="username" name="username" class="form-control" required
                                placeholder="Enter username" value="{{ old('username') }}"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px;">
                            @error('username')
                                <small style="color: #dc2626; display: block; margin-top: 5px;">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" style="display: block; font-weight: 600; margin-bottom: 6px; color: #334155; font-size: 13px;">Email Address <span style="color: #dc2626;">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" required
                                placeholder="user@example.com" value="{{ old('email') }}"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px;">
                            @error('email')
                                <small style="color: #dc2626; display: block; margin-top: 5px;">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
                        <div class="form-group">
                            <label for="password" style="display: block; font-weight: 600; margin-bottom: 6px; color: #334155; font-size: 13px;">Password <span style="color: #dc2626;">*</span></label>
                            <input type="password" id="password" name="password" class="form-control" required
                                placeholder="Min. 8 characters"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px;">
                            @error('password')
                                <small style="color: #dc2626; display: block; margin-top: 5px;">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" style="display: block; font-weight: 600; margin-bottom: 6px; color: #334155; font-size: 13px;">Confirm Password <span style="color: #dc2626;">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" required placeholder="Re-enter password"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px;">
                        </div>
                    </div>
                </div>

                <!-- Personal Details -->
                <div class="form-section" style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 18px; border: 1px solid #e2e8f0;">
                    <h3 class="section-header" style="color: #2f6d7e; font-size: 16px; margin-bottom: 18px; padding-bottom: 8px; border-bottom: 2px solid #2f6d7e; display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-person-fill" style="font-size: 15px;"></i> Personal Details
                    </h3>

                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; margin-bottom: 18px;">
                        <div class="form-group">
                            <label for="first_name" style="display: block; font-weight: 600; margin-bottom: 6px; color: #334155; font-size: 13px;">First Name <span style="color: #dc2626;">*</span></label>
                            <input type="text" id="first_name" name="first_name" class="form-control" required
                                placeholder="First name" value="{{ old('first_name') }}"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px;">
                        </div>

                        <div class="form-group">
                            <label for="middle_name" style="display: block; font-weight: 600; margin-bottom: 6px; color: #334155; font-size: 13px;">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" class="form-control"
                                placeholder="Middle name" value="{{ old('middle_name') }}"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px;">
                        </div>

                        <div class="form-group">
                            <label for="last_name" style="display: block; font-weight: 600; margin-bottom: 6px; color: #334155; font-size: 13px;">Last Name <span style="color: #dc2626;">*</span></label>
                            <input type="text" id="last_name" name="last_name" class="form-control" required
                                placeholder="Last name" value="{{ old('last_name') }}"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px;">
                        </div>
                    </div>

                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 2fr; gap: 18px;">
                        <div class="form-group">
                            <label for="contact_number" style="display: block; font-weight: 600; margin-bottom: 6px; color: #334155; font-size: 13px;">Contact Number</label>
                            <input type="tel" id="contact_number" name="contact_number" class="form-control"
                                placeholder="09XXXXXXXXX" maxlength="11" pattern="[0-9]{11}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                value="{{ old('contact_number') }}"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px;">
                        </div>

                        <div class="form-group">
                            <label for="address" style="display: block; font-weight: 600; margin-bottom: 6px; color: #334155; font-size: 13px;">Address</label>
                            <input type="text" id="address" name="address" class="form-control"
                                placeholder="Complete address" value="{{ old('address') }}"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px;">
                        </div>
                    </div>
                </div>

                <!-- Role & Access -->
                <div class="form-section" style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
                    <h3 class="section-header" style="color: #2f6d7e; font-size: 16px; margin-bottom: 18px; padding-bottom: 8px; border-bottom: 2px solid #2f6d7e; display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-key-fill" style="font-size: 15px;"></i> Role & Access
                    </h3>

                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
                        <div class="form-group">
                            <label for="role" style="display: block; font-weight: 600; margin-bottom: 6px; color: #334155; font-size: 13px;">User Role <span style="color: #dc2626;">*</span></label>
                            <select id="role" name="role" class="form-control" required
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px;">
                                <option value="">Select Role</option>
                                <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin
                                </option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="bhw" {{ old('role') === 'bhw' ? 'selected' : '' }}>Barangay Health Worker
                                </option>
                            </select>
                            @error('role')
                                <small style="color: #dc2626; display: block; margin-top: 5px;">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="status" style="display: block; font-weight: 600; margin-bottom: 6px; color: #334155; font-size: 13px;">Status <span style="color: #dc2626;">*</span></label>
                            <select id="status" name="status" class="form-control" required
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px;">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions" style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 10px;">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()"
                        style="padding: 10px 20px; background-color: #94a3b8; color: white; border: none; border-radius: 5px; font-size: 14px; font-weight: 500; cursor: pointer;">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary"
                        style="padding: 10px 20px; background-color: #2f6d7e; color: white; border: none; border-radius: 5px; font-size: 14px; font-weight: 500; cursor: pointer;">
                        <i class="bi bi-person-plus-fill"></i> Create User
                    </button>
                </div>

            </form>
        </div>

    </div>
@endsection