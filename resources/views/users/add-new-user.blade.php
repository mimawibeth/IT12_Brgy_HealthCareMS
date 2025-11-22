{{-- Add New User Form (Super Admin View) --}}
@extends('layouts.app')

@section('title', 'Add New User')
@section('page-title', 'Add New User')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Form Container -->
        <div class="form-container">
            <div class="form-header">
                <h2 class="form-title">Create New User Account</h2>
                <p class="form-subtitle">Fill in the details to create a new user account for the system</p>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="user-form">
                @csrf

                <!-- Account Information Section -->
                <div class="form-section">
                    <h3 class="section-header"><span class="section-indicator"></span>Account Information</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input type="text" id="username" name="username" class="form-control" required
                                placeholder="Enter unique username">
                            <small class="form-text">Username must be unique and alphanumeric (no spaces)</small>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-control" required
                                placeholder="user@brgy.gov.ph">
                            <small class="form-text">Official barangay email address</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" id="password" name="password" class="form-control" required
                                placeholder="Enter secure password">
                            <small class="form-text">Minimum 8 characters, include letters and numbers</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" required placeholder="Re-enter password">
                        </div>
                    </div>
                </div>

                <!-- Personal Information Section -->
                <div class="form-section">
                    <h3 class="section-header"><span class="section-indicator"></span>Personal Information</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" required
                                placeholder="Enter first name">
                        </div>

                        <div class="form-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" class="form-control"
                                placeholder="Enter middle name (optional)">
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" required
                                placeholder="Enter last name">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_number">Contact Number *</label>
                            <input type="text" id="contact_number" name="contact_number" class="form-control" required
                                placeholder="09XX-XXX-XXXX">
                        </div>
                    </div>
                </div>

                <!-- Role and Access Section -->
                <div class="form-section">
                    <h3 class="section-header"><span class="section-indicator"></span>Role and Access Level</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="role">User Role *</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="">Select User Role</option>
                                <option value="super_admin">Super Admin - Full system access</option>
                                <option value="admin">Admin - Management access</option>
                                <option value="bhw">Barangay Health Worker (BHW) - Field access</option>
                            </select>
                            <small class="form-text">Select appropriate role based on user responsibilities</small>
                        </div>

                        <div class="form-group">
                            <label for="status">Account Status *</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="active">Active - User can login immediately</option>
                                <option value="inactive">Inactive - Account disabled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('users.all-users') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Create User Account
                    </button>
                </div>

            </form>
        </div>

    </div>
@endsection