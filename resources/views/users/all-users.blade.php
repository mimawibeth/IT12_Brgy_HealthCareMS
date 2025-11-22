{{-- All Users - List of all system users (Super Admin View) --}}
@extends('layouts.app')

@section('title', 'All Users')
@section('page-title', 'All Users')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Header with Add Button -->
        <div class="content-header">
            <h2>All System Users</h2>
            <a href="{{ route('users.add-new') }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Add New User
            </a>
        </div>

        <!-- Search and Filter Section -->
        <div class="filters">
            <div class="search-box">
                <input type="text" placeholder="Search users by name, username, email..." class="search-input">
                <button class="btn-search"><i class="bi bi-search"></i> Search</button>
            </div>

            <div class="filter-options">
                <select class="filter-select">
                    <option value="">All Roles</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="admin">Admin</option>
                    <option value="bhw">Barangay Health Worker</option>
                </select>

                <select class="filter-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Super Admin User -->
                    <tr>
                        <td>superadmin</td>
                        <td>System Administrator</td>
                        <td>superadmin@brgy.gov.ph</td>
                        <td><span class="badge badge-super-admin">Super Admin</span></td>
                        <td><span class="status-badge status-active">Active</span></td>
                        <td>Jan 01, 2025</td>
                        <td>Nov 22, 2025 - 8:30 AM</td>
                        <td class="actions">
                            <a href="#" class="btn-action btn-view">View</a>
                            <a href="#" class="btn-action btn-edit">Edit</a>
                        </td>
                    </tr>

                    <!-- Admin User -->
                    <tr>
                        <td>admin01</td>
                        <td>Juan Dela Cruz</td>
                        <td>juan.delacruz@brgy.gov.ph</td>
                        <td><span class="badge badge-admin">Admin</span></td>
                        <td><span class="status-badge status-active">Active</span></td>
                        <td>Jan 15, 2025</td>
                        <td>Nov 22, 2025 - 7:45 AM</td>
                        <td class="actions">
                            <a href="#" class="btn-action btn-view">View</a>
                            <a href="#" class="btn-action btn-edit">Edit</a>
                        </td>
                    </tr>

                    <!-- BHW User 1 -->
                    <tr>
                        <td>bhw01</td>
                        <td>Maria Santos</td>
                        <td>maria.santos@brgy.gov.ph</td>
                        <td><span class="badge badge-bhw">BHW</span></td>
                        <td><span class="status-badge status-active">Active</span></td>
                        <td>Feb 20, 2025</td>
                        <td>Nov 21, 2025 - 2:30 PM</td>
                        <td class="actions">
                            <a href="#" class="btn-action btn-view">View</a>
                            <a href="#" class="btn-action btn-edit">Edit</a>
                        </td>
                    </tr>

                    <!-- BHW User 2 -->
                    <tr>
                        <td>bhw02</td>
                        <td>Ana Reyes</td>
                        <td>ana.reyes@brgy.gov.ph</td>
                        <td><span class="badge badge-bhw">BHW</span></td>
                        <td><span class="status-badge status-inactive">Inactive</span></td>
                        <td>Mar 10, 2025</td>
                        <td>Nov 18, 2025 - 10:15 AM</td>
                        <td class="actions">
                            <a href="#" class="btn-action btn-view">View</a>
                            <a href="#" class="btn-action btn-edit">Edit</a>
                        </td>
                    </tr>

                    <!-- Admin User 2 -->
                    <tr>
                        <td>admin02</td>
                        <td>Pedro Garcia</td>
                        <td>pedro.garcia@brgy.gov.ph</td>
                        <td><span class="badge badge-admin">Admin</span></td>
                        <td><span class="status-badge status-active">Active</span></td>
                        <td>Apr 05, 2025</td>
                        <td>Nov 20, 2025 - 9:00 AM</td>
                        <td class="actions">
                            <a href="#" class="btn-action btn-view">View</a>
                            <a href="#" class="btn-action btn-edit">Edit</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <button class="btn-page">« Previous</button>
            <span class="page-info">Page 1 of 1</span>
            <button class="btn-page">Next »</button>
        </div>

    </div>
@endsection