{{-- Audit Logs - System Activity Tracking (Super Admin View) --}}
@extends('layouts.app')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css?v=' . time()) }}">
    <link rel="stylesheet" href="{{ asset('css/audit-logs.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="page-content">
        <!-- Summary Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-blue">
                    <i class="bi bi-activity"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ $stats['totalToday'] ?? 0 }}</div>
                    <div class="stat-label">Total Activities Today</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-green">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ $stats['successToday'] ?? 0 }}</div>
                    <div class="stat-label">Successful Actions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-red">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ $stats['failedToday'] ?? 0 }}</div>
                    <div class="stat-label">Failed Attempts</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-purple">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ $stats['activeUsersToday'] ?? 0 }}</div>
                    <div class="stat-label">Active Users</div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filters">
            <form id="logs-filters-form" method="GET" action="{{ route('logs.audit') }}">
                <div class="search-box">
                    <input type="text" name="search" placeholder="Search logs by description, module, or IP address..."
                        class="search-input" value="{{ request('search') }}">
                    <button class="btn btn-primary" type="button" onclick="applyFilters()">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <button class="btn btn-secondary" type="button" onclick="exportLogs()">
                        <i class="bi bi-download"></i> Export Logs
                    </button>
                </div>
            </form>
        </div>

        <!-- Audit Logs Table -->
        <div class="table-container">

            <div style="overflow-x: auto;">
                <table class="data-table" style="min-width: 900px;">
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
                                <td>
                                    <div style="white-space: nowrap;">
                                        {{ $log->created_at ? $log->created_at->format('M d, Y') : '—' }}
                                        <br>
                                        <small
                                            style="color: #7f8c8d;">{{ $log->created_at ? $log->created_at->format('g:i:s A') : '—' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-info">
                                        <span class="user-name">{{ $user->name ?? 'System' }}</span>
                                        <span class="user-badge {{ $roleBadgeClass }}">{{ $roleLabel }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="action-badge {{ $actionClass }}">{{ ucfirst($log->action ?? 'other') }}</span>
                                </td>
                                <td>{{ $log->module ?? '—' }}</td>
                                <td style="max-width: 300px;" title="{{ $log->description ?? '—' }}">
                                    @php
                                        $desc = $log->description ?? '—';
                                        $truncated = strlen($desc) > 60 ? substr($desc, 0, 60) . '...' : $desc;
                                    @endphp
                                    {{ $truncated }}
                                </td>
                                <td>
                                    <code style="font-size: 12px; color: #555;">{{ $log->ip_address ?? '—' }}</code>
                                </td>
                                <td>
                                    <span
                                        class="status-badge {{ $statusClass }}">{{ ucfirst($log->status ?? 'unknown') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center;">No logs found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @php
            $showPagination = !empty($logs) && method_exists($logs, 'hasPages') && $logs->hasPages();
        @endphp
        @if($showPagination)
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
        @endif
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