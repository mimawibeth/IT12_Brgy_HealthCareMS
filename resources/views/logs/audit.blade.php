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
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-activity"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ $stats['totalToday'] ?? 0 }}</div>
                    <div class="stat-label">Total Activities Today</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ $stats['successToday'] ?? 0 }}</div>
                    <div class="stat-label">Successful Actions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ $stats['failedToday'] ?? 0 }}</div>
                    <div class="stat-label">Failed Attempts</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ $stats['activeUsersToday'] ?? 0 }}</div>
                    <div class="stat-label">Active Users</div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filters-section">
            <form id="logs-filters-form" method="GET" action="{{ route('logs.audit') }}">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="date-from">Date From</label>
                        <input type="date" id="date-from" name="date_from" class="filter-input"
                            value="{{ request('date_from') }}">
                    </div>

                    <div class="filter-group">
                        <label for="date-to">Date To</label>
                        <input type="date" id="date-to" name="date_to" class="filter-input"
                            value="{{ request('date_to') }}">
                    </div>

                    <div class="filter-group">
                        <label for="user-filter">User</label>
                        <select id="user-filter" name="user_id" class="filter-select">
                            <option value="">All Users</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>
                                    {{ $u->name }} ({{ strtoupper($u->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="action-filter">Action Type</label>
                        <select id="action-filter" name="action" class="filter-select">
                            <option value="">All Actions</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" @selected(request('action') === $action)>{{ ucfirst($action) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="module-filter">Module</label>
                        <select id="module-filter" name="module" class="filter-select">
                            <option value="">All Modules</option>
                            @foreach($modules as $module)
                                <option value="{{ $module }}" @selected(request('module') === $module)>{{ $module }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="status-filter">Status</label>
                        <select id="status-filter" name="status" class="filter-select">
                            <option value="">All Statuses</option>
                            <option value="success" @selected(request('status') === 'success')>Success</option>
                            <option value="failed" @selected(request('status') === 'failed')>Failed</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button class="btn btn-primary" type="button" onclick="applyFilters()">
                            <i class="bi bi-funnel"></i> Apply Filters
                        </button>
                        <button class="btn btn-secondary" type="button" onclick="clearFilters()">
                            <i class="bi bi-x-circle"></i> Clear
                        </button>
                    </div>
                </div>

                <div class="search-box">
                    <input type="text" name="search" placeholder="Search logs by description or details..."
                        class="search-input" value="{{ request('search') }}">
                    <button class="btn-search" type="button" onclick="applyFilters()"><i class="bi bi-search"></i>
                        Search</button>
                </div>
            </form>
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
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $user = $log->user;
                            $role = $log->user_role ?? ($user->role ?? null);
                            $roleBadgeClass = 'badge-unknown';
                            $roleLabel = 'N/A';
                            if ($role === 'super_admin') {
                                $roleBadgeClass = 'badge-super-admin';
                                $roleLabel = 'Super Admin';
                            } elseif ($role === 'admin') {
                                $roleBadgeClass = 'badge-admin';
                                $roleLabel = 'Admin';
                            } elseif ($role === 'bhw') {
                                $roleBadgeClass = 'badge-bhw';
                                $roleLabel = 'BHW';
                            }

                            $actionClass = 'action-view';
                            if ($log->action === 'login') {
                                $actionClass = 'action-login';
                            } elseif ($log->action === 'logout') {
                                $actionClass = 'action-logout';
                            } elseif ($log->action === 'create') {
                                $actionClass = 'action-create';
                            } elseif ($log->action === 'update') {
                                $actionClass = 'action-update';
                            } elseif ($log->action === 'delete') {
                                $actionClass = 'action-delete';
                            } elseif ($log->action === 'export') {
                                $actionClass = 'action-export';
                            }

                            $statusClass = $log->status === 'failed' ? 'status-failed' : 'status-success';
                        @endphp
                        <tr>
                            <td>{{ $log->created_at?->format('M d, Y - h:i A') ?? '—' }}</td>
                            <td>
                                <div class="user-info">
                                    <span class="user-name">{{ $user->name ?? 'System' }}</span>
                                    <span class="user-badge {{ $roleBadgeClass }}">{{ $roleLabel }}</span>
                                </div>
                            </td>
                            <td><span class="action-badge {{ $actionClass }}">{{ ucfirst($log->action ?? 'other') }}</span>
                            </td>
                            <td>{{ $log->module }}</td>
                            <td>{{ $log->description }}</td>
                            <td><span class="status-badge {{ $statusClass }}">{{ ucfirst($log->status ?? 'unknown') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center;">No logs found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            @if($logs->onFirstPage())
                <button class="btn-page" disabled>« Previous</button>
            @else
                <a class="btn-page" href="{{ $logs->previousPageUrl() }}">« Previous</a>
            @endif

            @php
                $start = max(1, $logs->currentPage() - 2);
                $end = min($logs->lastPage(), $logs->currentPage() + 2);
            @endphp

            @for ($page = $start; $page <= $end; $page++)
                @if ($page === $logs->currentPage())
                    <span class="btn-page active">{{ $page }}</span>
                @else
                    <a class="btn-page" href="{{ $logs->url($page) }}">{{ $page }}</a>
                @endif
            @endfor

            <span class="page-info">
                Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }} ({{ $logs->total() }} total logs)
            </span>

            @if($logs->hasMorePages())
                <a class="btn-page" href="{{ $logs->nextPageUrl() }}">Next »</a>
            @else
                <button class="btn-page" disabled>Next »</button>
            @endif
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function applyFilters() {
            const form = document.getElementById('logs-filters-form');
            if (form) {
                form.submit();
            }
        }

        function clearFilters() {
            window.location.href = '{{ route('logs.audit') }}';
        }

        function exportLogs() {
            alert('Export functionality will be implemented with backend');
        }
    </script>
@endpush