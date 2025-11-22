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
        <div class="form-container">
            <div class="form-header">
                <h2 class="form-title">Edit User Account</h2>
                <p class="form-subtitle">Update user account information</p>
            </div>

            <form method="POST" action="{{ route('users.update', 1) }}" class="user-form">
                @csrf
                @method('PUT')

                <!-- Account Information Section -->
                <div class="form-section">
                    <h3 class="section-header"><span class="section-indicator"></span>Account Information</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input type="text" id="username" name="username" class="form-control" required value="admin01"
                                readonly>
                            <small class="form-text">Username cannot be changed</small>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-control" required
                                value="juan.delacruz@brgy.gov.ph">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Leave blank to keep current password">
                            <small class="form-text">Only fill if you want to change password</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" placeholder="Re-enter new password">
                        </div>
                    </div>
                </div>

                <!-- Personal Information Section -->
                <div class="form-section">
                    <h3 class="section-header"><span class="section-indicator"></span>Personal Information</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" required value="Juan">
                        </div>

                        <div class="form-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" class="form-control" value="Santos">
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" required
                                value="Dela Cruz">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <input type="text" id="contact_number" name="contact_number" class="form-control"
                                value="0912-345-6789">
                        </div>

                        <div class="form-group">
                            <label for="position">Position/Designation</label>
                            <input type="text" id="position" name="position" class="form-control"
                                value="System Administrator">
                        </div>
                    </div>
                </div>

                <!-- Role and Access Section -->
                <div class="form-section">
                    <h3 class="section-header"><span class="section-indicator"></span>Role and Access</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="role">User Role *</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="">Select Role</option>
                                <option value="admin" selected>Admin</option>
                                <option value="bhw">Barangay Health Worker (BHW)</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Account Status *</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="notes">Notes/Remarks</label>
                            <textarea id="notes" name="notes" class="form-control"
                                rows="3">Primary system administrator responsible for user management and system configuration.</textarea>
                        </div>
                    </div>
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