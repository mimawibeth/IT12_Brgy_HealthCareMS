{{-- Edit User Form --}}
@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Form Container -->
        <div class="form-container compact-form">
            <div class="form-header">
                <h2 class="form-title"><i class="bi bi-person-gear"></i> Edit User Account</h2>
                <p class="form-subtitle">Update user information below</p>
            </div>

            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('users.update', $user) }}" class="user-form">
                @csrf
                @method('PUT')

                <!-- Account Credentials -->
                <div class="form-section compact-section">
                    <h3 class="section-header"><i class="bi bi-shield-lock"></i> Account Credentials</h3>

                    <div class="form-row two-col">
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input type="text" id="username" name="username" class="form-control" required
                                value="{{ old('username', $user->username) }}" readonly>
                            <small class="form-text text-muted">Cannot be changed</small>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-control" required
                                value="{{ old('email', $user->email) }}">
                        </div>
                    </div>

                    <div class="form-row two-col">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Leave blank to keep current password">
                            <small class="form-text">Only fill if you want to change password (min 12 chars, must include
                                uppercase, lowercase, number, and special character)</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation"><i class="bi bi-key-fill"></i> Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" placeholder="Re-enter new password">
                        </div>
                    </div>
                </div>

                <!-- Personal Details -->
                <div class="form-section compact-section">
                    <h3 class="section-header"><i class="bi bi-person-vcard"></i> Personal Details</h3>

                    <div class="form-row three-col">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" required
                                value="{{ old('first_name', $user->first_name) }}">
                        </div>

                        <div class="form-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" class="form-control"
                                value="{{ old('middle_name', $user->middle_name) }}">
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" required
                                value="{{ old('last_name', $user->last_name) }}">
                        </div>
                    </div>

                    <div class="form-row three-col">
                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <input type="text" id="contact_number" name="contact_number" class="form-control"
                                value="{{ old('contact_number', $user->contact_number) }}">
                        </div>

                        <div class="form-group col-span-2">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" class="form-control"
                                value="{{ old('address', $user->address) }}">
                        </div>
                    </div>
                </div>

                <!-- Role & Access -->
                <div class="form-section compact-section">
                    <h3 class="section-header"><i class="bi bi-gear"></i> Role & Access</h3>

                    <div class="form-row two-col">
                        <div class="form-group">
                            <label for="role"><i class="bi bi-person-check"></i> User Role *</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="">Select Role</option>
                                <<<<<<< HEAD <option value="admin" selected>Admin</option>
                                    <option value="bhw">Barangay Health Worker</option>
                                    <option value="staff">Staff</option>
                                    =======
                                    <option value="super_admin" @selected(old('role', $user->role) === 'super_admin')>Super
                                        Admin</option>
                                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                                    <option value="bhw" @selected(old('role', $user->role) === 'bhw')>Barangay Health Worker
                                        (BHW)</option>
                                    >>>>>>> 3723e67444b4297a57b2a8d141a24e107def6990
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status"><i class="bi bi-toggle-on"></i> Status *</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="active" @selected(old('status', $user->status) === 'active')>Active</option>
                                <option value="inactive" @selected(old('status', $user->status) === 'inactive')>Inactive
                                </option>
                            </select>
                        </div>
                    </div>
                    <<<<<<< HEAD <div class="form-row">
                        <div class="form-group full-width">
                            <label for="notes"><i class="bi bi-chat-left-text"></i> Notes/Remarks</label>
                            <textarea id="notes" name="notes" class="form-control"
                                rows="2">Primary system administrator responsible for user management and system configuration.</textarea>
                        </div>
                </div>
                =======
                >>>>>>> 3723e67444b4297a57b2a8d141a24e107def6990
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                Cancel
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Update User
            </button>
        </div>

        </form>
    </div>

    </div>
@endsection