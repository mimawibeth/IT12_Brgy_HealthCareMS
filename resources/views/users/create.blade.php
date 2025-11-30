{{-- Add New User Form --}}
@extends('layouts.app')

@section('title', 'Add New User')
@section('page-title', 'Add New User')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Form Container -->
        <div class="form-container compact-form">
            <div class="form-header">
                <h2 class="form-title"><i class="bi bi-person-plus"></i> Create New User Account</h2>
                <p class="form-subtitle">Fill in the required information below</p>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="user-form">
                @csrf

                <!-- Account Credentials -->
                <div class="form-section compact-section">
                    <h3 class="section-header"><i class="bi bi-shield-lock"></i> Account Credentials</h3>

                    <div class="form-row two-col">
                        <div class="form-group">
                            <label for="username"><i class="bi bi-person-badge"></i> Username *</label>
                            <input type="text" id="username" name="username" class="form-control" required
                                placeholder="Username">
                        </div>

                        <div class="form-group">
                            <label for="email"><i class="bi bi-envelope"></i> Email Address *</label>
                            <input type="email" id="email" name="email" class="form-control" required
                                placeholder="user@example.com">
                        </div>
                    </div>

                    <div class="form-row two-col">
                        <div class="form-group">
                            <label for="password"><i class="bi bi-key"></i> Password *</label>
                            <input type="password" id="password" name="password" class="form-control" required
                                placeholder="Min. 8 characters">
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation"><i class="bi bi-key-fill"></i> Confirm Password *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" required placeholder="Re-enter password">
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
                                placeholder="First name">
                        </div>

                        <div class="form-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" class="form-control"
                                placeholder="Middle name">
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" required
                                placeholder="Last name">
                        </div>
                    </div>

                    <div class="form-row three-col">
                        <div class="form-group">
                            <label for="contact_number"><i class="bi bi-telephone"></i> Contact Number</label>
                            <input type="text" id="contact_number" name="contact_number" class="form-control"
                                placeholder="09XX-XXX-XXXX">
                        </div>

                        <div class="form-group col-span-2">
                            <label for="address"><i class="bi bi-geo-alt"></i> Address</label>
                            <input type="text" id="address" name="address" class="form-control"
                                placeholder="Complete address">
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
                                <option value="bhw">Barangay Health Worker</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status"><i class="bi bi-toggle-on"></i> Status *</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>


                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Create User
                    </button>
                </div>

            </form>
        </div>

    </div>
@endsection