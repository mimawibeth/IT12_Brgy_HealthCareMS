{{-- Role Management - Manage user roles and permissions (Super Admin View) --}}
@extends('layouts.app')

@section('title', 'Role Management')
@section('page-title', 'Role Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="page-content">

        <div class="content-header" style="justify-content: flex-end;">
            <button class="btn btn-primary" onclick="openAddRoleModal()">
                <i class="bi bi-plus-circle"></i> Add New Role
            </button>
        </div>




        <!-- All Roles Table -->
        <div class="table-container" style="margin-bottom: 30px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Badge</th>
                        <th>Total Users</th>
                        <th>Active Users</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Super Admin Role -->
                    <tr>
                        <td>Super Administrator</td>
                        <td><span class="badge badge-super-admin">Super Admin</span></td>
                        <td>{{ $rolesSummary['super_admin']['total'] ?? 0 }}</td>
                        <td>{{ $rolesSummary['super_admin']['active'] ?? 0 }}</td>
                        <td>—</td>
                        <td class="actions">
                            <a href="#" class="btn-action btn-view" onclick="viewRoleDetails('super_admin'); return false;">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>

                    <!-- Admin Role -->
                    <tr>
                        <td>Administrator</td>
                        <td><span class="badge badge-admin">Admin</span></td>
                        <td>{{ $rolesSummary['admin']['total'] ?? 0 }}</td>
                        <td>{{ $rolesSummary['admin']['active'] ?? 0 }}</td>
                        <td>—</td>
                        <td class="actions">
                            <a href="#" class="btn-action btn-view" onclick="viewRoleDetails('admin'); return false;">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>

                    <!-- BHW Role -->
                    <tr>
                        <td>Barangay Health Worker</td>
                        <td><span class="badge badge-bhw">BHW</span></td>
                        <td>{{ $rolesSummary['bhw']['total'] ?? 0 }}</td>
                        <td>{{ $rolesSummary['bhw']['active'] ?? 0 }}</td>
                        <td>—</td>
                        <td class="actions">
                            <a href="#" class="btn-action btn-view" onclick="viewRoleDetails('bhw'); return false;">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>
                                <span class="badge badge-custom"
                                    style="background-color: {{ $role->badge_color ?? '#3498db' }};">
                                    {{ $role->name }}
                                </span>
                            </td>
                            <td>0</td>
                            <td>0</td>
                            <td>{{ $role->created_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="actions">
                                <a href="#" class="btn-action btn-view" onclick="viewRoleDetails('{{ $role->slug }}'); return false;">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <form action="{{ route('users.roles.destroy', $role) }}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete"
                                        onclick="return confirm('Delete this role? Users with this role (if any) may need to be reassigned.')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <!-- Role View Modal -->
    <div class="modal" id="roleViewModal" style="display:none;">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>Role Details</h3>
                <span class="close-modal" onclick="document.getElementById('roleViewModal').style.display='none'">&times;</span>
            </div>
            <div class="modal-body" id="roleModalBody">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div id="addRoleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Role</h3>
                <span class="close-modal" onclick="closeAddRoleModal()">&times;</span>
            </div>
            <form method="POST" action="{{ route('users.roles.store') }}" class="role-form">
                @csrf

                <div class="form-section">
                    <h4 class="section-header"><span class="section-indicator"></span>Role Information</h4>

                    <div class="form-group">
                        <label for="role_name">Role Name *</label>
                        <input type="text" id="role_name" name="role_name" class="form-control" required
                            placeholder="e.g., Nurse, Doctor, Midwife">
                        <small class="form-text">Enter a descriptive name for this role</small>
                    </div>

                    <div class="form-group">
                        <label for="role_color">Badge Color</label>
                        <select id="role_color" name="role_color" class="form-control">
                            <option value="#8e44ad">Purple</option>
                            <option value="#e74c3c">Red</option>
                            <option value="#3498db" selected>Blue</option>
                            <option value="#27ae60">Green</option>
                            <option value="#f39c12">Orange</option>
                            <option value="#95a5a6">Gray</option>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="section-header"><span class="section-indicator"></span>Permissions</h4>

                    <div class="permissions-grid">
                        <div class="permission-group">
                            <h5>Patient Management</h5>
                            <label class="checkbox-label">
                                <input type="checkbox" name="permissions[]" value="view_patients">
                                View Patients
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="permissions[]" value="add_patients">
                                Add Patients
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="permissions[]" value="edit_patients">
                                Edit Patients
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="permissions[]" value="delete_patients">
                                Delete Patients
                            </label>
                        </div>

                        <div class="permission-group">
                            <h5>Health Programs</h5>
                            <label class="checkbox-label">
                                <input type="checkbox" name="permissions[]" value="view_programs">
                                View Programs
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="permissions[]" value="manage_programs">
                                Manage Programs
                            </label>
                        </div>

                        <div class="permission-group">
                            <h5>Medicine & Inventory</h5>
                            <label class="checkbox-label">
                                <input type="checkbox" name="permissions[]" value="view_medicine">
                                View Medicine
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="permissions[]" value="manage_medicine">
                                Manage Medicine
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="permissions[]" value="dispense_medicine">
                                Dispense Medicine
                            </label>
                        </div>

                        <div class="permission-group">
                            <h5>User Management</h5>
                            <label class="checkbox-label">
                                <input type="checkbox" name="permissions[]" value="view_users">
                                View Users
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="permissions[]" value="manage_users">
                                Manage Users
                            </label>
                        </div>

                        <div class="permission-group">
                            <h5>Reports & Settings</h5>
                            <label class="checkbox-label">
                                <input type="checkbox" name="permissions[]" value="view_reports">
                                View Reports
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="permissions[]" value="generate_reports">
                                Generate Reports
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="permissions[]" value="system_settings">
                                System Settings
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeAddRoleModal()">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddRoleModal() {
            document.getElementById('addRoleModal').style.display = 'block';
        }

        function closeAddRoleModal() {
            document.getElementById('addRoleModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const modal = document.getElementById('addRoleModal');
            if (event.target == modal) {
                closeAddRoleModal();
            }
        }

        // View role details - Open modal with role information
        function viewRoleDetails(roleType) {
            const modal = document.getElementById('roleViewModal');
            const modalBody = document.getElementById('roleModalBody');
            
            const roleData = {
                'super_admin': {
                    name: 'Super Administrator',
                    badge: 'Super Admin',
                    badgeClass: 'badge-super-admin',
                    description: 'Full system access with all administrative privileges',
                    permissions: [
                        'Manage all users and roles',
                        'Access system settings',
                        'View and manage all patient records',
                        'Generate and export all reports',
                        'Manage health programs',
                        'Manage medicine inventory',
                        'View audit logs',
                        'System configuration'
                    ]
                },
                'admin': {
                    name: 'Administrator',
                    badge: 'Admin',
                    badgeClass: 'badge-admin',
                    description: 'Management-level access for daily operations',
                    permissions: [
                        'View and manage patient records',
                        'Generate and export reports',
                        'Manage health programs',
                        'Manage medicine inventory',
                        'View system analytics',
                        'Dispense medicines'
                    ]
                },
                'bhw': {
                    name: 'Barangay Health Worker',
                    badge: 'BHW',
                    badgeClass: 'badge-bhw',
                    description: 'Field worker with patient care and program implementation access',
                    permissions: [
                        'Add and update patient records',
                        'Record health program visits',
                        'View patient history',
                        'Dispense medicines',
                        'Generate basic reports'
                    ]
                }
            };
            
            const role = roleData[roleType] || {
                name: roleType.replace('_', ' ').toUpperCase(),
                badge: roleType.replace('_', ' '),
                badgeClass: 'badge-custom',
                description: 'Custom role',
                permissions: ['Custom permissions defined']
            };
            
            let permissionsHtml = role.permissions.map(p => `<li>${p}</li>`).join('');
            
            modalBody.innerHTML = `
                <div class="form-section">
                    <h3 class="section-header"><span class="section-indicator"></span>Role Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label><strong>Role Name:</strong></label>
                            <p>${role.name}</p>
                        </div>
                        <div class="form-group">
                            <label><strong>Badge Display:</strong></label>
                            <p><span class="badge ${role.badgeClass}">${role.badge}</span></p>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><strong>Description:</strong></label>
                            <p>${role.description}</p>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3 class="section-header"><span class="section-indicator"></span>Permissions & Access</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <ul style="margin-left: 20px; line-height: 1.8;">
                                ${permissionsHtml}
                            </ul>
                        </div>
                    </div>
                </div>
            `;
            
            modal.style.display = 'flex';
        }

        // Edit role (for custom roles)
        function editRole(roleId) {
            // TODO: Open edit modal with role data
            alert('Edit role functionality will be implemented soon');
        }

        // Delete role (for custom roles)
        function deleteRole(roleId) {
            if (confirm('Are you sure you want to delete this role? Users with this role will need to be reassigned.')) {
                // TODO: Implement delete role logic
                alert('Delete role functionality will be implemented soon');
            }
        }
    </script>
@endsection