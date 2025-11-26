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
            <div>
                <h2>System Users</h2>
                <p class="content-subtitle">
                    View and manage all registered users in the system.
                </p>
            </div>
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
                    @forelse($users ?? [] as $user)
                        @php
                            $fullName = trim(($user->first_name ?? '') . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . ($user->last_name ?? '')) ?: $user->name;
                            $roleBadges = [
                                'super_admin' => ['label' => 'Super Admin', 'class' => 'badge-super-admin'],
                                'admin' => ['label' => 'Admin', 'class' => 'badge-admin'],
                                'bhw' => ['label' => 'BHW', 'class' => 'badge-bhw'],
                            ];
                            $roleMeta = $roleBadges[$user->role] ?? ['label' => ucfirst($user->role), 'class' => 'badge-admin'];
                        @endphp
                        <tr>
                            <td>{{ $user->username }}</td>
                            <td>{{ $fullName }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge {{ $roleMeta['class'] }}">{{ $roleMeta['label'] }}</span></td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="status-badge status-active">Active</span>
                                @else
                                    <span class="status-badge status-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>{{ optional($user->created_at)->format('M d, Y') }}</td>
                            <td>—</td>
                            <td class="actions">
                                <a href="#" class="btn-action btn-view">View</a>
                                <a href="#" class="btn-action btn-edit">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;">No users found.</td>
                        </tr>
                    @endforelse
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