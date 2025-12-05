{{-- Add New User Form --}}
@extends('layouts.app')

@section('title', 'Add New User')
@section('page-title', 'Add New User')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Form Container -->
        <div class="form-container compact-form">
            <div class="form-header">
                <h2 class="form-title">Create New User Account</h2>
                <p class="form-subtitle">Fill in the required information below</p>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="user-form">
                @csrf

                <!-- Account Credentials -->
                <div class="form-section compact-section">
                    <h3 class="section-header">Account Credentials</h3>

                    <div class="form-row two-col">
                        <div class="form-group">
                            <label for="username">Username <span class="required-asterisk">*</span></label>
                            <input type="text" id="username" name="username" class="form-control" required
                                placeholder="Username">
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address <span class="required-asterisk">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" required
                                placeholder="user@example.com">
                        </div>
                    </div>

                    <div class="form-row two-col">
                        <div class="form-group">
                            <label for="password">Password <span class="required-asterisk">*</span></label>
                            <input type="password" id="password" name="password" class="form-control" required
                                placeholder="Min. 8 characters">
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password <span
                                    class="required-asterisk">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" required placeholder="Re-enter password">
                        </div>
                    </div>
                </div>

                <!-- Personal Details -->
                <div class="form-section compact-section">
                    <h3 class="section-header">Personal Details</h3>

                    <div class="form-row three-col">
                        <div class="form-group">
                            <label for="first_name">First Name <span class="required-asterisk">*</span></label>
                            <input type="text" id="first_name" name="first_name" class="form-control" required
                                placeholder="First name">
                        </div>

                        <div class="form-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" class="form-control"
                                placeholder="Middle name">
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name <span class="required-asterisk">*</span></label>
                            <input type="text" id="last_name" name="last_name" class="form-control" required
                                placeholder="Last name">
                        </div>
                    </div>

                    <div class="form-row three-col">
                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <input type="tel" id="contact_number" name="contact_number" class="form-control"
                                placeholder="09XXXXXXXXX" maxlength="11" pattern="[0-9]{11}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>

                        <div class="form-group col-span-2">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" class="form-control"
                                placeholder="Complete address">
                        </div>
                    </div>
                </div>

                <!-- Role & Access -->
                <div class="form-section compact-section">
                    <h3 class="section-header">Role & Access</h3>

                    <div class="form-row two-col">
                        <div class="form-group">
                            <label for="role">User Role <span class="required-asterisk">*</span></label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="">Select Role</option>
                                <option value="bhw">Barangay Health Worker</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span class="required-asterisk">*</span></label>
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
                        <i class="bi bi-person-plus-fill"></i> Create User
                    </button>
                </div>

            </form>
        </div>

    </div>
@endsection