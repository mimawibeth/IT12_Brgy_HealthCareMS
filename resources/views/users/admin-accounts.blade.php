{{-- Admin Accounts - List of Admin users only (Super Admin View) --}}
@extends('layouts.app')

@section('title', 'Admin Accounts')
@section('page-title', 'Admin Accounts')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Header -->
        <div class="content-header">
            <h2>Admin Accounts</h2>
        </div>

        <!-- Admin Creation Options -->
        <div class="admin-options-container">
            <div class="option-card">
                <div class="option-icon">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h3>Add New User as Admin</h3>
                <p>Create a brand new user account and assign the Admin role</p>
                <a href="{{ route('users.add-new') }}" class="btn btn-primary btn-block">
                    <i class="bi bi-person-plus"></i> Create New Admin
                </a>
            </div>

            <div class="option-card">
                <div class="option-icon">
                    <i class="bi bi-person-gear"></i>
                </div>
                <h3>Set Existing User as Admin</h3>
                <p>Promote an existing user account to Admin role</p>
                <button class="btn btn-secondary btn-block" onclick="openSetAdminModal()">
                    <i class="bi bi-arrow-up-circle"></i> Promote to Admin
                </button>
            </div>
        </div>

        <!-- Search Section -->
        <div class="filters">
            <div class="search-box">
                <input type="text" placeholder="Search admin accounts..." class="search-input">
                <button class="btn-search"><i class="bi bi-search"></i> Search</button>
            </div>

            <div class="filter-options">
                <select class="filter-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>



        <!-- Admin Accounts Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins ?? [] as $admin)
                        @php
                            $fullName = trim(($admin->first_name ?? '') . ' ' . ($admin->middle_name ? $admin->middle_name . ' ' : '') . ($admin->last_name ?? '')) ?: $admin->name;
                        @endphp
                        <tr>
                            <td>{{ $admin->username }}</td>
                            <td>{{ $fullName }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>System Administrator</td>
                            <td>
                                @if($admin->status === 'active')
                                    <span class="status-badge status-active">Active</span>
                                @else
                                    <span class="status-badge status-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>{{ optional($admin->created_at)->format('M d, Y') }}</td>
                            <td>—</td>
                            <td class="actions">
                                <a href="#" class="btn-action btn-view">View</a>
                                <a href="#" class="btn-action btn-edit">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;">No admin accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- Set Admin Modal -->
    <div id="setAdminModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Promote User to Admin</h3>
                <span class="close-modal" onclick="closeSetAdminModal()">&times;</span>
            </div>
            <form method="POST" action="{{ route('users.promote-admin') }}" class="role-form">
                @csrf

                <div class="form-section">
                    <h4 class="section-header"><span class="section-indicator"></span>Select User to Promote</h4>

                    <div class="form-group">
                        <label for="user_id">Select User *</label>
                        <select id="user_id" name="user_id" class="form-control" required>
                            <option value="">-- Select a User --</option>
                            @foreach(\App\Models\User::whereNotIn('role', ['admin', 'super_admin'])->orderBy('name')->get() as $user)
                                @php
                                    $fullName = trim(($user->first_name ?? '') . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . ($user->last_name ?? '')) ?: $user->name;
                                @endphp
                                <option value="{{ $user->id }}">{{ $user->username }} - {{ $fullName }}
                                    ({{ strtoupper($user->role) }})</option>
                            @endforeach
                        </select>
                        <small class="form-text">Only non-admin users are shown in this list</small>
                    </div>

                    <div class="info-box">
                        <h4><i class="bi bi-info-circle"></i> Important</h4>
                        <p>Promoting a user to Admin will grant them management-level access to:</p>
                        <ul>
                            <li>Manage all patient records</li>
                            <li>Generate and export reports</li>
                            <li>View system analytics</li>
                        </ul>
                        <p><strong>Note:</strong> They will NOT be able to manage users or access super admin settings.</p>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeSetAdminModal()">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-up-circle"></i> Promote to Admin
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openSetAdminModal() {
            document.getElementById('setAdminModal').style.display = 'block';
        }

        function closeSetAdminModal() {
            document.getElementById('setAdminModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const modal = document.getElementById('setAdminModal');
            if (event.target == modal) {
                closeSetAdminModal();
            }
        }
    </script>
@endsection