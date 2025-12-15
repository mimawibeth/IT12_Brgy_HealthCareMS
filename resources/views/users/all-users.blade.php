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
        <div
            style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: .3rem; flex-wrap: wrap;">
            <form method="GET" class="filters" style="flex: 1; display: flex; gap: 12px; align-items: center;">
                <input type="text" name="search" placeholder="Search users by name, username, email..." class="search-input"
                    style="flex: 1; min-width: 300px;" value="{{ request('search') }}">

                <select name="role" class="filter-select">
                    <option value="">All Roles</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="bhw" {{ request('role') === 'bhw' ? 'selected' : '' }}>Barangay Health Worker</option>
                </select>

                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <button type="button" class="btn btn-secondary" id="clearFiltersBtn"
                    style="padding: 10px 15px; font-size: 14px; white-space: nowrap; background-color: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <i class="bi bi-x-circle"></i> Clear
                </button>

                <a href="{{ route('users.add-new') }}" class="btn btn-primary"
                    style="padding: 10px 15px; font-size: 14px; white-space: nowrap;">
                    <i class="bi bi-person-plus"></i> Add New User
                </a>
            </form>
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
                            <td class="actions">
                                <a href="javascript:void(0)" class="btn-action btn-view view-user" data-id="{{ $user->id }}">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn-action btn-edit">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                @if(in_array(auth()->user()->role ?? null, ['super_admin', 'admin']))
                                    @if($user->role !== 'super_admin' || auth()->user()->role === 'super_admin')
                                        <a href="javascript:void(0)" class="btn-action btn-reset-password" data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}" onclick="openResetPasswordModal(this); return false;">
                                            <i class="bi bi-key"></i> Reset Password
                                        </a>
                                    @endif
                                @endif
                                @if(auth()->user()->role === 'super_admin' && $user->id !== auth()->user()->id)
                                    @if(($user->status ?? 'active') === 'active')
                                        <a href="javascript:void(0)" class="btn-action btn-deactivate" data-id="{{ $user->id }}"
                                            data-name="{{ $fullName }}" onclick="openDeactivateModal(this); return false;"
                                            style="background-color: #6c757d; color: white;">
                                            <i class="bi bi-x-circle"></i> Deactivate
                                        </a>
                                    @else
                                        <a href="javascript:void(0)" class="btn-action btn-reactivate" data-id="{{ $user->id }}"
                                            data-name="{{ $fullName }}" onclick="openReactivateModal(this); return false;"
                                            style="background-color: #28a745; color: white;">
                                            <i class="bi bi-check-circle"></i> Reactivate
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;">No users found.</td>
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

    <!-- Deactivate Account Modal -->
    <div class="modal" id="deactivateAccountModal" style="display:none;">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header" style="background-color: #6c757d; color: white;">
                <h3 style="color: white; margin: 0;"><i class="bi bi-exclamation-triangle"></i> Deactivate Account</h3>
                <span class="close-modal" onclick="closeDeactivateModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 20px; font-size: 15px; line-height: 1.6;">
                    Are you sure you want to deactivate the account for <strong id="deactivateUserName"></strong>?
                </p>
                <div
                    style="background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 5px; padding: 12px; margin-bottom: 20px;">
                    <p style="margin: 0; color: #856404; font-size: 14px;">
                        <i class="bi bi-info-circle"></i> <strong>Warning:</strong> This user will no longer be able to
                        access the system until reactivated.
                    </p>
                </div>
                <form id="deactivateAccountForm" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-secondary" onclick="closeDeactivateModal()">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Deactivate Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reactivate Account Modal -->
    <div class="modal" id="reactivateAccountModal" style="display:none;">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header" style="background-color: #28a745; color: white;">
                <h3 style="color: white; margin: 0;"><i class="bi bi-check-circle"></i> Reactivate Account</h3>
                <span class="close-modal" onclick="closeReactivateModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 20px; font-size: 15px; line-height: 1.6;">
                    Are you sure you want to reactivate the account for <strong id="reactivateUserName"></strong>?
                </p>
                <div
                    style="background-color: #d1ecf1; border: 1px solid #17a2b8; border-radius: 5px; padding: 12px; margin-bottom: 20px;">
                    <p style="margin: 0; color: #0c5460; font-size: 14px;">
                        <i class="bi bi-info-circle"></i> This user will regain access to the system with their existing
                        credentials.
                    </p>
                </div>
                <form id="reactivateAccountForm" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-secondary" onclick="closeReactivateModal()">Cancel</button>
                        <button type="submit" class="btn" style="background-color: #28a745; color: white;">
                            <i class="bi bi-check-circle"></i> Reactivate Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal" id="resetPasswordModal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Reset User Password</h3>
                <span class="close-modal" data-close-modal="resetPasswordModal">&times;</span>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 20px;">Are you sure you want to reset the password for <strong
                        id="reset_user_name"></strong>?</p>
                <div
                    style="background: #fff3cd; padding: 15px; border-radius: 4px; border-left: 4px solid #f39c12; margin-bottom: 20px;">
                    <p style="margin: 0 0 10px 0;"><strong><i class="bi bi-info-circle-fill" style="color: #f39c12;"></i>
                            Note:</strong> A temporary password will be automatically generated and shown to you once.</p>
                    <p style="margin: 0;">Please save or share the temporary password with the user immediately.</p>
                </div>
                <form id="resetPasswordForm" method="POST" action="">
                    @csrf
                    <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-secondary"
                            data-close-modal="resetPasswordModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background: #f39c12;">Confirm Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Generated Password Modal -->
    <div class="modal" id="generatedPasswordModal" style="display:none;">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3><i class="bi bi-check-circle-fill" style="color: #10b981;"></i> Account Created Successfully</h3>
                <span class="close-modal" onclick="closeGeneratedPasswordModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 20px;">The account for <strong id="modal_new_user_name"></strong> has been created
                    successfully.</p>

                <div
                    style="background: #fff3cd; padding: 15px; border-radius: 4px; border-left: 4px solid #f39c12; margin-bottom: 20px;">
                    <p style="margin: 0 0 10px 0;"><strong><i class="bi bi-exclamation-triangle"
                                style="color: #f39c12;"></i> IMPORTANT:</strong> This password will only be shown once and
                        cannot be retrieved later.</p>
                    <p style="margin: 0;">Please save or share these credentials with the user immediately.</p>
                </div>

                <div style="margin-bottom: 15px;">
                    <label
                        style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155; font-size: 14px;">Email
                        Address:</label>
                    <div
                        style="background: #f8fafc; padding: 12px 15px; border-radius: 4px; border: 1px solid #cbd5e1; font-size: 14px;">
                        <i class="bi bi-envelope" style="color: #64748b; margin-right: 8px;"></i><span
                            id="modal_new_user_email"></span>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label
                        style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155; font-size: 14px;">Generated
                        Password:</label>
                    <div
                        style="background: #fff3cd; padding: 12px 15px; border-radius: 4px; border: 1px solid #f39c12; display: flex; align-items: center; gap: 10px;">
                        <i class="bi bi-key-fill" style="color: #f39c12;"></i>
                        <span id="modal_generated_password"
                            style="font-family: 'Courier New', monospace; font-size: 16px; font-weight: 600; color: #000; flex: 1; letter-spacing: 1px;"></span>
                        <button type="button" onclick="copyPasswordToClipboard()" class="btn btn-secondary"
                            style="padding: 6px 12px; font-size: 13px; white-space: nowrap;">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                </div>

                <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeGeneratedPasswordModal()" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> I've Saved the Credentials
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Success Modal -->
    <div class="modal" id="resetPasswordSuccessModal" style="display:none;">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3><i class="bi bi-check-circle-fill" style="color: #10b981;"></i> Password Reset Successfully</h3>
                <span class="close-modal" onclick="closeResetPasswordSuccessModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 20px;">The password for <strong id="modal_reset_user_name"></strong> has been reset
                    successfully.</p>

                <div
                    style="background: #fff3cd; padding: 15px; border-radius: 4px; border-left: 4px solid #f39c12; margin-bottom: 20px;">
                    <p style="margin: 0 0 10px 0;"><strong><i class="bi bi-exclamation-triangle"
                                style="color: #f39c12;"></i> IMPORTANT:</strong> This temporary password will only be shown
                        once and cannot be retrieved later.</p>
                    <p style="margin: 0;">Please save or share these credentials with the user immediately.</p>
                </div>

                <div style="margin-bottom: 15px;">
                    <label
                        style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155; font-size: 14px;">Email
                        Address:</label>
                    <div
                        style="background: #f8fafc; padding: 12px 15px; border-radius: 4px; border: 1px solid #cbd5e1; font-size: 14px;">
                        <i class="bi bi-envelope" style="color: #64748b; margin-right: 8px;"></i><span
                            id="modal_reset_user_email"></span>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label
                        style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155; font-size: 14px;">Temporary
                        Password:</label>
                    <div
                        style="background: #fff3cd; padding: 12px 15px; border-radius: 4px; border: 1px solid #f39c12; display: flex; align-items: center; gap: 10px;">
                        <i class="bi bi-key-fill" style="color: #f39c12;"></i>
                        <span id="modal_reset_password"
                            style="font-family: 'Courier New', monospace; font-size: 16px; font-weight: 600; color: #000; flex: 1; letter-spacing: 1px;"></span>
                        <button type="button" onclick="copyResetPasswordToClipboard()" class="btn btn-secondary"
                            style="padding: 6px 12px; font-size: 13px; white-space: nowrap;">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                </div>

                <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeResetPasswordSuccessModal()" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> I've Saved the Credentials
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Filter auto-submit functionality
            const filterForm = document.querySelector('.filters');
            const searchInput = filterForm.querySelector('input[name="search"]');
            const roleSelect = filterForm.querySelector('select[name="role"]');
            const statusSelect = filterForm.querySelector('select[name="status"]');
            const clearFiltersBtn = document.getElementById('clearFiltersBtn');

            // Auto-submit on search input (with debounce)
            let searchTimeout;
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function () {
                    filterForm.submit();
                }, 500);
            });

            // Auto-submit on select change
            roleSelect.addEventListener('change', function () {
                filterForm.submit();
            });

            statusSelect.addEventListener('change', function () {
                filterForm.submit();
            });

            // Clear filters functionality
            clearFiltersBtn.addEventListener('click', function () {
                searchInput.value = '';
                roleSelect.value = '';
                statusSelect.value = '';
                // Redirect to the page without any query parameters
                window.location.href = window.location.pathname;
            });

            const resetPasswordModal = document.getElementById('resetPasswordModal');
            const resetPasswordForm = document.getElementById('resetPasswordForm');
            const resetUserName = document.getElementById('reset_user_name');

            function openModal(id) {
                const modal = document.getElementById(id);
                if (modal) {
                    modal.style.display = 'flex';
                }
            }

            function closeModal(id) {
                const modal = document.getElementById(id);
                if (modal) {
                    modal.style.display = 'none';
                }
            }

            // Reset password button click
            document.querySelectorAll('.btn-delete[data-name]').forEach(button => {
                // Handler is inline in the button
            });

            window.openResetPasswordModal = function (button) {
                const userId = button.getAttribute('data-id');
                const userName = button.getAttribute('data-name');

                resetUserName.textContent = userName;
                resetPasswordForm.action = `/users/${userId}/reset-password`;

                openModal('resetPasswordModal');
            };

            // Close modal handlers
            document.querySelectorAll('.close-modal[data-close-modal]').forEach(span => {
                span.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-close-modal');
                    closeModal(targetId);
                });
            });

            document.querySelectorAll('button[data-close-modal]').forEach(button => {
                button.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-close-modal');
                    closeModal(targetId);
                });
            });

            window.addEventListener('click', function (event) {
                if (event.target === resetPasswordModal) {
                    closeModal('resetPasswordModal');
                }
            });

            // Generated Password Modal Functions
            window.closeGeneratedPasswordModal = function () {
                const modal = document.getElementById('generatedPasswordModal');
                if (modal) {
                    modal.style.display = 'none';
                }
            };

            window.copyPasswordToClipboard = function () {
                const passwordElement = document.getElementById('modal_generated_password');
                const password = passwordElement.textContent;

                navigator.clipboard.writeText(password).then(function () {
                    alert('Password copied to clipboard!');
                }).catch(function (err) {
                    console.error('Failed to copy password:', err);
                });
            };

            // Reset Password Success Modal Functions
            window.closeResetPasswordSuccessModal = function () {
                const modal = document.getElementById('resetPasswordSuccessModal');
                if (modal) {
                    modal.style.display = 'none';
                }
            };

            window.copyResetPasswordToClipboard = function () {
                const passwordElement = document.getElementById('modal_reset_password');
                const password = passwordElement.textContent;

                navigator.clipboard.writeText(password).then(function () {
                    alert('Temporary password copied to clipboard!');
                }).catch(function (err) {
                    console.error('Failed to copy password:', err);
                });
            };

            // Deactivate Account Modal Functions
            window.openDeactivateModal = function (button) {
                const userId = button.getAttribute('data-id');
                const userName = button.getAttribute('data-name');
                const modal = document.getElementById('deactivateAccountModal');
                const form = document.getElementById('deactivateAccountForm');
                const nameSpan = document.getElementById('deactivateUserName');

                form.action = `/users/${userId}/deactivate`;
                nameSpan.textContent = userName;
                modal.style.display = 'flex';
            };

            window.closeDeactivateModal = function () {
                document.getElementById('deactivateAccountModal').style.display = 'none';
            };

            // Reactivate Account Modal Functions
            window.openReactivateModal = function (button) {
                const userId = button.getAttribute('data-id');
                const userName = button.getAttribute('data-name');
                const modal = document.getElementById('reactivateAccountModal');
                const form = document.getElementById('reactivateAccountForm');
                const nameSpan = document.getElementById('reactivateUserName');

                form.action = `/users/${userId}/reactivate`;
                nameSpan.textContent = userName;
                modal.style.display = 'flex';
            };

            window.closeReactivateModal = function () {
                document.getElementById('reactivateAccountModal').style.display = 'none';
            };

            // Close modals when clicking outside
            window.addEventListener('click', function (event) {
                const deactivateModal = document.getElementById('deactivateAccountModal');
                const reactivateModal = document.getElementById('reactivateAccountModal');

                if (event.target === deactivateModal) {
                    closeDeactivateModal();
                }
                if (event.target === reactivateModal) {
                    closeReactivateModal();
                }
            });

            // Check if we need to show the generated password modal
            @if(session('generated_password'))
                const generatedPasswordModal = document.getElementById('generatedPasswordModal');
                document.getElementById('modal_new_user_email').textContent = '{{ session('new_user_email') }}';
                document.getElementById('modal_generated_password').textContent = '{{ session('generated_password') }}';
                document.getElementById('modal_new_user_name').textContent = '{{ session('new_user_name') }}';
                generatedPasswordModal.style.display = 'flex';
            @endif

                // Check if we need to show the reset password success modal
                @if(session('reset_password'))
                    const resetPasswordSuccessModal = document.getElementById('resetPasswordSuccessModal');
                    document.getElementById('modal_reset_user_email').textContent = '{{ session('reset_user_email') }}';
                    document.getElementById('modal_reset_password').textContent = '{{ session('reset_password') }}';
                    document.getElementById('modal_reset_user_name').textContent = '{{ session('reset_user_name') }}';
                    resetPasswordSuccessModal.style.display = 'flex';
                @endif
                        });
    </script>
@endpush