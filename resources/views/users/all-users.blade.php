{{-- All Users - List of all system users (Super Admin View) --}}
@extends('layouts.app')

@section('title', 'All Users')
@section('page-title', 'All Users')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css?v=' . time()) }}">
    <link rel="stylesheet" href="{{ asset('css/users.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="page-content">
        <!-- Search and Filter Section -->
        <div class="filters">
            <div class="search-box">
                <input type="text" placeholder="Search users by name, username, email..." class="search-input">
                <button class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
                <a href="{{ route('users.add-new') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Add New User
                </a>
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
                            $fullName = trim(($user->first_name ?? '') . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . ($user->last_name ?? '')) ?: ($user->name ?? 'N/A');
                            $roleBadges = [
                                'super_admin' => ['label' => 'Super Admin', 'class' => 'badge-super-admin'],
                                'admin' => ['label' => 'Admin', 'class' => 'badge-admin'],
                                'bhw' => ['label' => 'BHW', 'class' => 'badge-bhw'],
                            ];
                            $roleMeta = $roleBadges[$user->role ?? 'bhw'] ?? ['label' => ucfirst($user->role ?? 'User'), 'class' => 'badge-admin'];
                        @endphp
                        <tr>
                            <td>{{ $user->username ?? 'N/A' }}</td>
                            <td>{{ $fullName }}</td>
                            <td>{{ $user->email ?? 'N/A' }}</td>
                            <td><span class="badge {{ $roleMeta['class'] }}">{{ $roleMeta['label'] }}</span></td>
                            <td>
                                @if(($user->status ?? 'inactive') === 'active')
                                    <span class="status-badge status-active">Active</span>
                                @else
                                    <span class="status-badge status-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>{{ optional($user->created_at)->format('M d, Y') ?? 'N/A' }}</td>
                            <td>—</td>
                            <td class="actions">
                                <a href="javascript:void(0)" class="btn-action btn-view view-user"
                                    data-id="{{ $user->id }}">View</a>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn-action btn-edit">Edit</a>
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

        @php
            $showPagination = !empty($users) && method_exists($users, 'hasPages') && $users->hasPages();
        @endphp
        @if($showPagination)
            <div class="pagination">
                @if($users->onFirstPage())
                    <button class="btn-page" disabled>« Previous</button>
                @else
                    <a class="btn-page" href="{{ $users->previousPageUrl() }}">« Previous</a>
                @endif

                @php
                    $start = max(1, $users->currentPage() - 2);
                    $end = min($users->lastPage(), $users->currentPage() + 2);
                @endphp

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page === $users->currentPage())
                        <span class="btn-page active">{{ $page }}</span>
                    @else
                        <a class="btn-page" href="{{ $users->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                <span class="page-info">
                    Page {{ $users->currentPage() }} of {{ $users->lastPage() }} ({{ $users->total() }} total users)
                </span>

                @if($users->hasMorePages())
                    <a class="btn-page" href="{{ $users->nextPageUrl() }}">Next »</a>
                @else
                    <button class="btn-page" disabled>Next »</button>
                @endif
            </div>
        @endif

    </div>

    @include('users.partials.view-modal')
@endsection