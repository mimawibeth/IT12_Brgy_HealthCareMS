{{-- Dashboard Page: Shows overview and statistics --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css?v=' . time()) }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="page-content">


        @php
            $role = auth()->user()->role ?? 'super_admin';
        @endphp

        <!-- Overview Cards: Display quick statistics -->
        <div class="stats-grid">
            <!-- Total System Users Card -->
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-blue">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">Total System Users</h3>
                    <p class="stat-number">{{ number_format($totalSystemUsers ?? 0) }}</p>
                    <span class="stat-trend">+{{ number_format($newUsersThisMonth ?? 0) }} new this month</span>
                </div>
            </div>

            <!-- Total Patients Card -->
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-green">
                    <i class="bi bi-person-lines-fill"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">Total Patients</h3>
                    <p class="stat-number">{{ number_format($totalPatients ?? 0) }}</p>
                    <span class="stat-trend">+{{ number_format($newPatientsThisMonth ?? 0) }} new this month</span>
                </div>
            </div>

            <!-- Active vs Inactive Users Card -->
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-yellow">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">Active / Inactive Users</h3>
                    <p class="stat-number">{{ number_format($activeUsers ?? 0) }} / {{ number_format($inactiveUsers ?? 0) }}</p>
                    <span class="stat-trend">User Status Overview</span>
                </div>
            </div>

            <!-- Total Health Programs Card -->
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-pink">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">Total Health Program Records</h3>
                    <p class="stat-number">{{ number_format($totalHealthPrograms ?? 0) }}</p>
                    <span class="stat-trend">Prenatal: {{ $totalPrenatalRecords ?? 0 }} | FP: {{ $totalFPRecords ?? 0 }} | NIP: {{ $totalNIPRecords ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="section-header-inline">
                <h2>Health Programs Analytics</h2>
                <p>Visual insights into health program records and trends</p>
            </div>

            <div class="charts-grid">
                <!-- Patient Registration Trend -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="bi bi-graph-up"></i> Patient Registration Trend</h3>
                        <span class="chart-badge">Last 6 Months</span>
                    </div>
                    <div class="chart-placeholder">
                        <canvas id="userTrendChart"></canvas>
                    </div>
                </div>

                <!-- Health Programs Distribution -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="bi bi-pie-chart"></i> Health Programs</h3>
                        <span class="chart-badge">Total Records</span>
                    </div>
                    <div class="chart-placeholder">
                        <canvas id="activityTypeChart"></canvas>
                    </div>
                </div>

                <!-- Monthly Health Records -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="bi bi-bar-chart-line"></i> Monthly Records</h3>
                        <span class="chart-badge">This Week</span>
                    </div>
                    <div class="chart-placeholder">
                        <canvas id="weekActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Oversight Section -->
        <div class="dashboard-grid">
            <!-- Recent System Activities -->
            <div class="recent-activities">
                <div class="section-header-inline">
                    <h2>Recent System Activities</h2>
                    <p>Latest 20 actions and updates in the system</p>
                </div>

                <div class="activity-list">
                    @forelse($recentLogs as $log)
                        @php
                            $icon = 'bi-activity';
                            $iconColor = '#3498db';
                            switch ($log->action) {
                                case 'login':
                                    $icon = 'bi-person-check';
                                    $iconColor = '#2ecc71';
                                    break;
                                case 'logout':
                                    $icon = 'bi-box-arrow-right';
                                    $iconColor = '#95a5a6';
                                    break;
                                case 'create':
                                    $icon = 'bi-plus-circle';
                                    $iconColor = '#3498db';
                                    break;
                                case 'update':
                                    $icon = 'bi-pencil-square';
                                    $iconColor = '#f39c12';
                                    break;
                                case 'delete':
                                    $icon = 'bi-trash';
                                    $iconColor = '#e74c3c';
                                    break;
                                case 'dispense':
                                    $icon = 'bi-capsule';
                                    $iconColor = '#9b59b6';
                                    break;
                                case 'export':
                                    $icon = 'bi-file-earmark-arrow-down';
                                    $iconColor = '#16a085';
                                    break;
                            }

                            $userName = $log->user->name ?? 'System';
                        @endphp
                        <div class="activity-item">
                            <span class="activity-icon" style="color: {{ $iconColor }};">
                                <i class="bi {{ $icon }}"></i>
                            </span>
                            <div class="activity-details">
                                <p class="activity-text">{{ $log->module }}: {{ $log->description ?? $log->action }}</p>
                                <span class="activity-time">{{ $userName }} • {{ $log->created_at?->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="activity-item">
                            <span class="activity-icon"><i class="bi bi-activity"></i></span>
                            <div class="activity-details">
                                <p class="activity-text">No recent activities recorded.</p>
                                <span class="activity-time">—</span>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Patient Registration Trend Chart
        const userTrendLabels = @json($userTrendLabels ?? []);
        const userTrendData = @json($userTrendData ?? []);

        const userChart = document.getElementById('userTrendChart');
        if (userChart) {
            new Chart(userChart.getContext('2d'), {
                type: 'line',
                data: {
                    labels: userTrendLabels,
                    datasets: [{
                        label: 'New Patients',
                        data: userTrendData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12
                        }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // Health Programs Distribution Chart
        const activityTypes = @json($activityTypes ?? []);
        const activityLabels = Object.keys(activityTypes);
        const activityData = Object.values(activityTypes);

        const activityChart = document.getElementById('activityTypeChart');
        if (activityChart) {
            new Chart(activityChart.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: activityLabels,
                    datasets: [{
                        data: activityData,
                        backgroundColor: ['#ef4444', '#8b5cf6', '#10b981'],
                        borderWidth: 3,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 12 }
                            }
                        }
                    }
                }
            });
        }

        // Weekly Health Records Bar Chart
        const weekLabels = @json($weekLabels ?? []);
        const weekActivityData = @json($weekActivityData ?? []);

        const weekChart = document.getElementById('weekActivityChart');
        if (weekChart) {
            new Chart(weekChart.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: weekLabels,
                    datasets: [{
                        label: 'Health Records',
                        data: weekActivityData,
                        backgroundColor: '#3b82f6',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12
                        }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    </script>
@endpush