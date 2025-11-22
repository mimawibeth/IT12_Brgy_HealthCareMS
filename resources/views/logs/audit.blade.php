{{-- Audit Logs - System Activity Tracking (Super Admin View) --}}
@extends('layouts.app')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/audit-logs.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Header -->
        <div class="content-header">
            <h2>System Audit Logs</h2>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="exportLogs()">
                    <i class="bi bi-download"></i> Export Logs
                </button>
            </div>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <p><strong>System Activity Monitoring:</strong> Track all user actions, system changes, and access logs. Records
                are maintained for security and compliance purposes.</p>
        </div>

        <!-- Summary Stats -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bi bi-activity"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">248</div>
                    <div class="stat-label">Total Activities Today</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">235</div>
                    <div class="stat-label">Successful Actions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">13</div>
                    <div class="stat-label">Failed Attempts</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">5</div>
                    <div class="stat-label">Active Users</div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filters-section">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="date-from">Date From</label>
                    <input type="date" id="date-from" class="filter-input">
                </div>

                <div class="filter-group">
                    <label for="date-to">Date To</label>
                    <input type="date" id="date-to" class="filter-input">
                </div>

                <div class="filter-group">
                    <label for="user-filter">User</label>
                    <select id="user-filter" class="filter-select">
                        <option value="">All Users</option>
                        <option value="superadmin">Super Admin</option>
                        <option value="admin01">Juan Dela Cruz (Admin)</option>
                        <option value="admin02">Pedro Garcia (Admin)</option>
                        <option value="bhw01">Maria Santos (BHW)</option>
                        <option value="bhw02">Ana Reyes (BHW)</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="action-filter">Action Type</label>
                    <select id="action-filter" class="filter-select">
                        <option value="">All Actions</option>
                        <option value="login">Login</option>
                        <option value="logout">Logout</option>
                        <option value="create">Create</option>
                        <option value="update">Update</option>
                        <option value="delete">Delete</option>
                        <option value="view">View</option>
                        <option value="export">Export</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="module-filter">Module</label>
                    <select id="module-filter" class="filter-select">
                        <option value="">All Modules</option>
                        <option value="users">User Management</option>
                        <option value="patients">Patient Records</option>
                        <option value="prenatal">Prenatal Care</option>
                        <option value="fp">Family Planning</option>
                        <option value="immunization">Immunization</option>
                        <option value="nutrition">Nutrition Program</option>
                        <option value="reports">Reports</option>
                        <option value="settings">System Settings</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button class="btn btn-primary" onclick="applyFilters()">
                        <i class="bi bi-funnel"></i> Apply Filters
                    </button>
                    <button class="btn btn-secondary" onclick="clearFilters()">
                        <i class="bi bi-x-circle"></i> Clear
                    </button>
                </div>
            </div>

            <div class="search-box">
                <input type="text" placeholder="Search logs by description, IP address, or details..." class="search-input">
                <button class="btn-search"><i class="bi bi-search"></i> Search</button>
            </div>
        </div>

        <!-- Audit Logs Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Recent Login -->
                    <tr>
                        <td>Nov 22, 2025 - 8:30 AM</td>
                        <td>
                            <div class="user-info">
                                <span class="user-name">System Administrator</span>
                                <span class="user-badge badge-super-admin">Super Admin</span>
                            </div>
                        </td>
                        <td><span class="action-badge action-login">Login</span></td>
                        <td>Authentication</td>
                        <td>User logged in successfully</td>
                        <td>192.168.1.100</td>
                        <td><span class="status-badge status-success">Success</span></td>
                    </tr>

                    <!-- Patient Record Created -->
                    <tr>
                        <td>Nov 22, 2025 - 7:45 AM</td>
                        <td>
                            <div class="user-info">
                                <span class="user-name">Juan Dela Cruz</span>
                                <span class="user-badge badge-admin">Admin</span>
                            </div>
                        </td>
                        <td><span class="action-badge action-create">Create</span></td>
                        <td>Patient Records</td>
                        <td>Created new patient record: Maria Garcia (ITR-2025-001)</td>
                        <td>192.168.1.105</td>
                        <td><span class="status-badge status-success">Success</span></td>
                    </tr>

                    <!-- Failed Login Attempt -->
                    <tr>
                        <td>Nov 22, 2025 - 7:30 AM</td>
                        <td>
                            <div class="user-info">
                                <span class="user-name">Unknown User</span>
                                <span class="user-badge badge-unknown">N/A</span>
                            </div>
                        </td>
                        <td><span class="action-badge action-login">Login</span></td>
                        <td>Authentication</td>
                        <td>Failed login attempt - Invalid credentials</td>
                        <td>192.168.1.200</td>
                        <td><span class="status-badge status-failed">Failed</span></td>
                    </tr>

                    <!-- Prenatal Record Updated -->
                    <tr>
                        <td>Nov 21, 2025 - 3:15 PM</td>
                        <td>
                            <div class="user-info">
                                <span class="user-name">Maria Santos</span>
                                <span class="user-badge badge-bhw">BHW</span>
                            </div>
                        </td>
                        <td><span class="action-badge action-update">Update</span></td>
                        <td>Prenatal Care</td>
                        <td>Updated prenatal record for Ana Reyes - Added checkup details</td>
                        <td>192.168.1.110</td>
                        <td><span class="status-badge status-success">Success</span></td>
                    </tr>

                    <!-- Report Exported -->
                    <tr>
                        <td>Nov 21, 2025 - 2:00 PM</td>
                        <td>
                            <div class="user-info">
                                <span class="user-name">Pedro Garcia</span>
                                <span class="user-badge badge-admin">Admin</span>
                            </div>
                        </td>
                        <td><span class="action-badge action-export">Export</span></td>
                        <td>Reports</td>
                        <td>Exported monthly health report (October 2025)</td>
                        <td>192.168.1.105</td>
                        <td><span class="status-badge status-success">Success</span></td>
                    </tr>

                    <!-- User Account Created -->
                    <tr>
                        <td>Nov 20, 2025 - 10:00 AM</td>
                        <td>
                            <div class="user-info">
                                <span class="user-name">System Administrator</span>
                                <span class="user-badge badge-super-admin">Super Admin</span>
                            </div>
                        </td>
                        <td><span class="action-badge action-create">Create</span></td>
                        <td>User Management</td>
                        <td>Created new user account: Ana Reyes (BHW)</td>
                        <td>192.168.1.100</td>
                        <td><span class="status-badge status-success">Success</span></td>
                    </tr>

                    <!-- Immunization Record Viewed -->
                    <tr>
                        <td>Nov 20, 2025 - 9:30 AM</td>
                        <td>
                            <div class="user-info">
                                <span class="user-name">Maria Santos</span>
                                <span class="user-badge badge-bhw">BHW</span>
                            </div>
                        </td>
                        <td><span class="action-badge action-view">View</span></td>
                        <td>Immunization</td>
                        <td>Viewed immunization records for Patient ID: ITR-2025-045</td>
                        <td>192.168.1.110</td>
                        <td><span class="status-badge status-success">Success</span></td>
                    </tr>

                    <!-- Role Updated -->
                    <tr>
                        <td>Nov 19, 2025 - 4:00 PM</td>
                        <td>
                            <div class="user-info">
                                <span class="user-name">System Administrator</span>
                                <span class="user-badge badge-super-admin">Super Admin</span>
                            </div>
                        </td>
                        <td><span class="action-badge action-update">Update</span></td>
                        <td>User Management</td>
                        <td>Updated role permissions for Admin role</td>
                        <td>192.168.1.100</td>
                        <td><span class="status-badge status-success">Success</span></td>
                    </tr>

                    <!-- Logout -->
                    <tr>
                        <td>Nov 19, 2025 - 5:30 PM</td>
                        <td>
                            <div class="user-info">
                                <span class="user-name">Juan Dela Cruz</span>
                                <span class="user-badge badge-admin">Admin</span>
                            </div>
                        </td>
                        <td><span class="action-badge action-logout">Logout</span></td>
                        <td>Authentication</td>
                        <td>User logged out successfully</td>
                        <td>192.168.1.105</td>
                        <td><span class="status-badge status-success">Success</span></td>
                    </tr>

                    <!-- System Settings Changed -->
                    <tr>
                        <td>Nov 18, 2025 - 11:00 AM</td>
                        <td>
                            <div class="user-info">
                                <span class="user-name">System Administrator</span>
                                <span class="user-badge badge-super-admin">Super Admin</span>
                            </div>
                        </td>
                        <td><span class="action-badge action-update">Update</span></td>
                        <td>System Settings</td>
                        <td>Updated system configuration: Session timeout changed to 30 minutes</td>
                        <td>192.168.1.100</td>
                        <td><span class="status-badge status-success">Success</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <button class="btn-page">« Previous</button>
            <span class="page-info">Page 1 of 5 (50 total logs)</span>
            <button class="btn-page">Next »</button>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function applyFilters() {
            const dateFrom = document.getElementById('date-from').value;
            const dateTo = document.getElementById('date-to').value;
            const userFilter = document.getElementById('user-filter').value;
            const actionFilter = document.getElementById('action-filter').value;
            const moduleFilter = document.getElementById('module-filter').value;

            // TODO: Implement filter logic
            console.log('Applying filters:', {
                dateFrom,
                dateTo,
                userFilter,
                actionFilter,
                moduleFilter
            });
        }

        function clearFilters() {
            document.getElementById('date-from').value = '';
            document.getElementById('date-to').value = '';
            document.getElementById('user-filter').value = '';
            document.getElementById('action-filter').value = '';
            document.getElementById('module-filter').value = '';

            // TODO: Reload logs without filters
            console.log('Filters cleared');
        }

        function exportLogs() {
            // TODO: Implement export logic
            console.log('Exporting logs...');
            alert('Export functionality will be implemented with backend');
        }
    </script>
@endpush