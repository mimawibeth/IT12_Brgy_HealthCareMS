{{-- User Management - User List Page --}}
@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Header with Add Button -->
        <div class="content-header">
            <h2>All Users</h2>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Add New User
            </a>
        </div>

        <!-- Search and Filter Section -->
        <div class="filters">
            <div class="search-box">
                <input type="text" placeholder="Search users..." class="search-input">
                <button class="btn-search"><i class="bi bi-search"></i> Search</button>
            </div>

            <div class="filter-options">
                <select class="filter-select">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="bhw">Barangay Health Worker</option>
                    <option value="staff">Staff</option>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Sample User Row -->
                    <tr>
                        <td>admin01</td>
                        <td>Juan Dela Cruz</td>
                        <td>juan.delacruz@brgy.gov.ph</td>
                        <td><span class="badge badge-admin">Admin</span></td>
                        <td><span class="status-badge status-active">Active</span></td>
                        <td>Jan 15, 2025</td>
                        <td class="actions">
                            <a href="#" class="btn-action btn-view">View</a>
                            <a href="#" class="btn-action btn-edit">Edit</a>
                            <a href="#" class="btn-action btn-delete">Delete</a>
                        </td>
                    </tr>

                    <tr>
                        <td>bhw01</td>
                        <td>Maria Santos</td>
                        <td>maria.santos@brgy.gov.ph</td>
                        <td><span class="badge badge-bhw">BHW</span></td>
                        <td><span class="status-badge status-active">Active</span></td>
                        <td>Feb 20, 2025</td>
                        <td class="actions">
                            <a href="#" class="btn-action btn-view">View</a>
                            <a href="#" class="btn-action btn-edit">Edit</a>
                            <a href="#" class="btn-action btn-delete">Delete</a>
                        </td>
                    </tr>

                    <tr>
                        <td>staff01</td>
                        <td>Ana Reyes</td>
                        <td>ana.reyes@brgy.gov.ph</td>
                        <td><span class="badge badge-staff">Staff</span></td>
                        <td><span class="status-badge status-inactive">Inactive</span></td>
                        <td>Mar 10, 2025</td>
                        <td class="actions">
                            <a href="#" class="btn-action btn-view">View</a>
                            <a href="#" class="btn-action btn-edit">Edit</a>
                            <a href="#" class="btn-action btn-delete">Delete</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <button class="btn-page">« Previous</button>
            <span class="page-info">Page 1 of 5</span>
            <button class="btn-page">Next »</button>
        </div>

    </div>
@endsection