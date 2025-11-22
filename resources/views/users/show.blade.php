{{-- View User Details --}}
@extends('layouts.app')

@section('title', 'User Details')
@section('page-title', 'User Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Detail Container -->
        <div class="detail-container">
            <div class="detail-header">
                <div>
                    <h2 class="detail-title">User Account Information</h2>
                    <p class="detail-subtitle">Complete details of the user account</p>
                </div>
                <div class="detail-actions">
                    <a href="{{ route('users.all-users') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                    <a href="#" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit User
                    </a>
                </div>
            </div>

            <!-- Account Information Section -->
            <div class="detail-section">
                <h3 class="section-header"><span class="section-indicator"></span>Account Information</h3>

                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Username</label>
                        <p>admin01</p>
                    </div>
                    <div class="detail-item">
                        <label>Email Address</label>
                        <p>juan.delacruz@brgy.gov.ph</p>
                    </div>
                    <div class="detail-item">
                        <label>User Role</label>
                        <p><span class="badge badge-admin">Admin</span></p>
                    </div>
                    <div class="detail-item">
                        <label>Account Status</label>
                        <p><span class="status-badge status-active">Active</span></p>
                    </div>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="detail-section">
                <h3 class="section-header"><span class="section-indicator"></span>Personal Information</h3>

                <div class="detail-grid">
                    <div class="detail-item">
                        <label>First Name</label>
                        <p>Juan</p>
                    </div>
                    <div class="detail-item">
                        <label>Middle Name</label>
                        <p>Santos</p>
                    </div>
                    <div class="detail-item">
                        <label>Last Name</label>
                        <p>Dela Cruz</p>
                    </div>
                    <div class="detail-item">
                        <label>Contact Number</label>
                        <p>0912-345-6789</p>
                    </div>
                    <div class="detail-item">
                        <label>Position/Designation</label>
                        <p>System Administrator</p>
                    </div>
                </div>
            </div>

            <!-- Activity Information Section -->
            <div class="detail-section">
                <h3 class="section-header"><span class="section-indicator"></span>Activity Information</h3>

                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Account Created</label>
                        <p>January 15, 2025 at 10:30 AM</p>
                    </div>
                    <div class="detail-item">
                        <label>Last Login</label>
                        <p>November 22, 2025 at 8:45 AM</p>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <p>October 10, 2025 at 2:15 PM</p>
                    </div>
                    <div class="detail-item">
                        <label>Total Logins</label>
                        <p>156 times</p>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="detail-section">
                <h3 class="section-header"><span class="section-indicator"></span>Notes/Remarks</h3>

                <div class="detail-notes">
                    <p>Primary system administrator responsible for user management and system configuration.</p>
                </div>
            </div>

        </div>

    </div>
@endsection