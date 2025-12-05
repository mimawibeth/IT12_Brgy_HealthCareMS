{{-- User Management - User List Page --}}
@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css?v=' . time()) }}">
    <link rel="stylesheet" href="{{ asset('css/users.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Search and Filter Section -->
        <div class="filters">
            <div class="search-box">
                <input type="text" placeholder="Search users..." class="search-input">
                <button class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Add New User
                </a>
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
            <div style="overflow-x: auto;">
                <table class="data-table" style="min-width: 800px;">
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
                                <a href="javascript:void(0)" class="btn-action btn-view view-user" data-id="1">View</a>
                                <a href="{{ route('users.edit', 1) }}" class="btn-action btn-edit">Edit</a>
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
                                <a href="javascript:void(0)" class="btn-action btn-view view-user" data-id="2">View</a>
                                <a href="{{ route('users.edit', 2) }}" class="btn-action btn-edit">Edit</a>
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
                                <a href="javascript:void(0)" class="btn-action btn-view view-user" data-id="3">View</a>
                                <a href="{{ route('users.edit', 3) }}" class="btn-action btn-edit">Edit</a>
                                <a href="#" class="btn-action btn-delete">Delete</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <button class="btn-page">« Previous</button>
            <span class="page-info">Page 1 of 5</span>
            <button class="btn-page">Next »</button>
        </div>

    </div>

    <!-- User View Modal -->
    <div class="modal" id="userViewModal" style="display:none;">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>User Details</h3>
                <span class="close-modal" id="closeUserModal">&times;</span>
            </div>
            <div class="modal-body" id="userModalBody">
                <div class="loading-spinner" style="text-align:center; padding: 2rem;">
                    <p>Loading...</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('userViewModal');
                const closeModal = document.getElementById('closeUserModal');

                document.querySelectorAll('.view-user').forEach(button => {
                    button.addEventListener('click', async function () {
                        const userId = this.getAttribute('data-id');
                        console.log('User ID:', userId);

                        modal.style.display = 'flex';
                        const modalBody = document.getElementById('userModalBody');
                        modalBody.innerHTML = '<div style=\"text-align:center; padding: 2rem;\"><p>Loading...</p></div>';

                        try {
                            const response = await fetch(`/users/${userId}`);
                            console.log('Response status:', response.status);

                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }

                            const data = await response.json();
                            console.log('User data:', data);

                            const roleLabels = {
                                'super_admin': 'Super Admin',
                                'admin': 'Admin',
                                'bhw': 'Barangay Health Worker',
                                'staff': 'Staff'
                            };

                            const roleBadges = {
                                'super_admin': 'badge-super-admin',
                                'admin': 'badge-admin',
                                'bhw': 'badge-bhw',
                                'staff': 'badge-staff'
                            };

                            const statusBadge = data.status === 'active' ? 'status-active' : 'status-inactive';

                            modalBody.innerHTML = `
                                                                <div class="form-section section-patient-info">
                                                                    <h3 class="section-header"><span class="section-indicator"></span>Account Information</h3>
                                                                    <div class="form-row">
                                                                        <div class="form-group">
                                                                            <label><strong>Username:</strong></label>
                                                                            <p>${data.username || 'N/A'}</p>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label><strong>Email Address:</strong></label>
                                                                            <p>${data.email || 'N/A'}</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-row">
                                                                        <div class="form-group">
                                                                            <label><strong>User Role:</strong></label>
                                                                            <p><span class="badge ${roleBadges[data.role] || 'badge-staff'}">${roleLabels[data.role] || 'N/A'}</span></p>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label><strong>Account Status:</strong></label>
                                                                            <p><span class="status-badge ${statusBadge}">${data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : 'N/A'}</span></p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="form-section section-screening">
                                                                    <h3 class="section-header"><span class="section-indicator"></span>Personal Information</h3>
                                                                    <div class="form-row">
                                                                        <div class="form-group">
                                                                            <label><strong>First Name:</strong></label>
                                                                            <p>${data.first_name || 'N/A'}</p>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label><strong>Middle Name:</strong></label>
                                                                            <p>${data.middle_name || 'N/A'}</p>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label><strong>Last Name:</strong></label>
                                                                            <p>${data.last_name || 'N/A'}</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-row">
                                                                        <div class="form-group">
                                                                            <label><strong>Contact Number:</strong></label>
                                                                            <p>${data.contact_number || 'N/A'}</p>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label><strong>Address:</strong></label>
                                                                            <p>${data.address || 'N/A'}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="form-section section-history">
                                                                    <h3 class="section-header"><span class="section-indicator"></span>Additional Information</h3>
                                                                    <div class="form-row">
                                                                        <div class="form-group">
                                                                            <label><strong>Account Created:</strong></label>
                                                                            <p>${data.created_at ? new Date(data.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A'}</p>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label><strong>Last Updated:</strong></label>
                                                                            <p>${data.updated_at ? new Date(data.updated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A'}</p>
                                                                        </div>
                                                                    </div>
                                                                    ${data.notes ? `
                                                                    <div class="form-row">
                                                                        <div class="form-group full-width">
                                                                            <label><strong>Notes/Remarks:</strong></label>
                                                                            <p>${data.notes}</p>
                                                                        </div>
                                                                    </div>
                                                                    ` : ''}
                                                                </div>
                                                            `;
                        } catch (error) {
                            modalBody.innerHTML = '<div style="text-align:center; padding: 2rem; color: red;"><p>Error loading user details.</p></div>';
                        }
                    });
                });

                closeModal.addEventListener('click', () => modal.style.display = 'none');
                window.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            });
        </script>
    @endpush
@endsection