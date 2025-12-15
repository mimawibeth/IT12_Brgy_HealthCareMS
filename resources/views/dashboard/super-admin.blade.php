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
                    <p class="stat-number">{{ number_format($activeUsers ?? 0) }} / {{ number_format($inactiveUsers ?? 0) }}
                    </p>
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
                    <span class="stat-trend">Prenatal: {{ $totalPrenatalRecords ?? 0 }} | FP: {{ $totalFPRecords ?? 0 }} |
                        NIP: {{ $totalNIPRecords ?? 0 }}</span>
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
                <div class="chart-card clickable-chart" onclick="openChartModal('patientTrendModal')">
                    <div class="chart-header">
                        <h3><i class="bi bi-graph-up"></i> Patient Registration Trend</h3>
                        <span class="chart-badge">Last 6 Months</span>
                    </div>
                    <div class="chart-placeholder">
                        <canvas id="userTrendChart"></canvas>
                    </div>
                </div>

                <!-- Health Programs Distribution -->
                <div class="chart-card clickable-chart" onclick="openChartModal('healthProgramsModal')">
                    <div class="chart-header">
                        <h3><i class="bi bi-pie-chart"></i> Health Programs</h3>
                        <span class="chart-badge">Total Records</span>
                    </div>
                    <div class="chart-placeholder">
                        <canvas id="activityTypeChart"></canvas>
                    </div>
                </div>

                <!-- Monthly Health Records -->
                <div class="chart-card clickable-chart" onclick="openChartModal('monthlyRecordsModal')">
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

        <!-- Chart Popup Modals -->
        <!-- Patient Trend Chart Modal -->
        <div class="modal" id="patientTrendModal" style="display:none;">
            <div class="modal-content chart-modal-content">
                <div class="chart-modal-header">
                    <h3><i class="bi bi-graph-up"></i> Patient Registration Trend - Last 6 Months</h3>
                    <span class="close-modal" onclick="closeChartModal('patientTrendModal')">&times;</span>
                </div>
                <div class="chart-modal-body">
                    <div class="chart-modal-canvas">
                        <canvas id="userTrendChartModal"></canvas>
                    </div>
                    <div class="modal-stats">
                        <div class="modal-stat-item">
                            <span class="modal-stat-label">Total Patients</span>
                            <span class="modal-stat-value">{{ array_sum($userTrendData ?? []) }}</span>
                        </div>
                        <div class="modal-stat-item">
                            <span class="modal-stat-label">Average/Month</span>
                            <span
                                class="modal-stat-value">{{ count($userTrendData ?? []) > 0 ? round(array_sum($userTrendData ?? []) / count($userTrendData ?? []), 1) : 0 }}</span>
                        </div>
                        <div class="modal-stat-item">
                            <span class="modal-stat-label">Highest Month</span>
                            <span
                                class="modal-stat-value">{{ count($userTrendData ?? []) > 0 ? max($userTrendData ?? []) : 0 }}</span>
                        </div>
                        <div class="modal-stat-item">
                            <span class="modal-stat-label">Lowest Month</span>
                            <span
                                class="modal-stat-value">{{ count($userTrendData ?? []) > 0 ? min($userTrendData ?? []) : 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health Programs Chart Modal -->
        <div class="modal" id="healthProgramsModal" style="display:none;">
            <div class="modal-content chart-modal-content">
                <div class="chart-modal-header">
                    <h3><i class="bi bi-pie-chart"></i> Health Programs Distribution</h3>
                    <span class="close-modal" onclick="closeChartModal('healthProgramsModal')">&times;</span>
                </div>
                <div class="chart-modal-body">
                    <div class="chart-modal-canvas">
                        <canvas id="activityTypeChartModal"></canvas>
                    </div>
                    <div class="modal-stats">
                        <div class="modal-stat-item">
                            <span class="modal-stat-label">Prenatal Care</span>
                            <span class="modal-stat-value">{{ $totalPrenatalRecords ?? 0 }}</span>
                        </div>
                        <div class="modal-stat-item">
                            <span class="modal-stat-label">Family Planning</span>
                            <span class="modal-stat-value">{{ $totalFPRecords ?? 0 }}</span>
                        </div>
                        <div class="modal-stat-item">
                            <span class="modal-stat-label">NIP (Immunization)</span>
                            <span class="modal-stat-value">{{ $totalNIPRecords ?? 0 }}</span>
                        </div>
                        <div class="modal-stat-item">
                            <span class="modal-stat-label">Total Records</span>
                            <span class="modal-stat-value">{{ $totalHealthPrograms ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Records Chart Modal -->
        <div class="modal" id="monthlyRecordsModal" style="display:none;">
            <div class="modal-content chart-modal-content">
                <div class="chart-modal-header">
                    <h3><i class="bi bi-bar-chart-line"></i> Weekly Health Records</h3>
                    <span class="close-modal" onclick="closeChartModal('monthlyRecordsModal')">&times;</span>
                </div>
                <div class="chart-modal-body">
                    <div class="chart-modal-canvas">
                        <canvas id="weekActivityChartModal"></canvas>
                    </div>
                    <div class="modal-stats">
                        <div class="modal-stat-item">
                            <span class="modal-stat-label">Total This Week</span>
                            <span class="modal-stat-value">{{ array_sum($weekActivityData ?? []) }}</span>
                        </div>
                        <div class="modal-stat-item">
                            <span class="modal-stat-label">Daily Average</span>
                            <span
                                class="modal-stat-value">{{ count($weekActivityData ?? []) > 0 ? round(array_sum($weekActivityData ?? []) / count($weekActivityData ?? []), 1) : 0 }}</span>
                        </div>
                        <div class="modal-stat-item">
                            <span class="modal-stat-label">Busiest Day</span>
                            <span
                                class="modal-stat-value">{{ count($weekActivityData ?? []) > 0 ? max($weekActivityData ?? []) : 0 }}</span>
                        </div>
                        <div class="modal-stat-item">
                            <span class="modal-stat-label">Lowest Day</span>
                            <span
                                class="modal-stat-value">{{ count($weekActivityData ?? []) > 0 ? min($weekActivityData ?? []) : 0 }}</span>
                        </div>
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

        // Chart Modal Functions
        let modalCharts = {};

        function openChartModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';

                // Initialize the chart in the modal
                setTimeout(() => {
                    if (modalId === 'patientTrendModal') {
                        initModalChart('userTrendChartModal', 'line');
                    } else if (modalId === 'healthProgramsModal') {
                        initModalChart('activityTypeChartModal', 'doughnut');
                    } else if (modalId === 'monthlyRecordsModal') {
                        initModalChart('weekActivityChartModal', 'bar');
                    }
                }, 100);
            }
        }

        function closeChartModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';

                // Destroy the modal chart to free memory
                const chartId = modal.querySelector('canvas').id;
                if (modalCharts[chartId]) {
                    modalCharts[chartId].destroy();
                    delete modalCharts[chartId];
                }
            }
        }

        function initModalChart(canvasId, type) {
            // Destroy existing chart if any
            if (modalCharts[canvasId]) {
                modalCharts[canvasId].destroy();
            }

            const canvas = document.getElementById(canvasId);
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            if (type === 'line') {
                // Patient Trend Line Chart
                modalCharts[canvasId] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($userTrendLabels ?? []),
                        datasets: [{
                            label: 'New Patients',
                            data: @json($userTrendData ?? []),
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: { size: 14, weight: 'bold' },
                                    padding: 20
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 14, weight: 'bold' },
                                bodyFont: { size: 13 }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { font: { size: 12 } },
                                grid: { color: 'rgba(0, 0, 0, 0.05)' }
                            },
                            x: {
                                ticks: { font: { size: 12 } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            } else if (type === 'doughnut') {
                // Health Programs Doughnut Chart
                const activityData = @json($activityTypes ?? []);
                modalCharts[canvasId] = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(activityData),
                        datasets: [{
                            data: Object.values(activityData),
                            backgroundColor: ['#ef4444', '#8b5cf6', '#10b981'],
                            borderWidth: 3,
                            borderColor: '#fff',
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    font: { size: 14 },
                                    padding: 20,
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 14, weight: 'bold' },
                                bodyFont: { size: 13 },
                                callbacks: {
                                    label: function (context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            } else if (type === 'bar') {
                // Weekly Records Bar Chart
                modalCharts[canvasId] = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($weekLabels ?? []),
                        datasets: [{
                            label: 'Health Records',
                            data: @json($weekActivityData ?? []),
                            backgroundColor: 'rgba(16, 185, 129, 0.8)',
                            borderColor: '#10b981',
                            borderWidth: 2,
                            borderRadius: 8,
                            hoverBackgroundColor: '#10b981'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: { size: 14, weight: 'bold' },
                                    padding: 20
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 14, weight: 'bold' },
                                bodyFont: { size: 13 }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { font: { size: 12 } },
                                grid: { color: 'rgba(0, 0, 0, 0.05)' }
                            },
                            x: {
                                ticks: { font: { size: 12 } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        }

        function closeChartModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Close modal when clicking outside
        window.addEventListener('click', function (event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });

        // Add hover effect to clickable charts
        document.addEventListener('DOMContentLoaded', function () {
            const clickableCharts = document.querySelectorAll('.clickable-chart');
            clickableCharts.forEach(chart => {
                chart.style.cursor = 'pointer';
                chart.style.transition = 'transform 0.2s ease, box-shadow 0.2s ease';

                chart.addEventListener('mouseenter', function () {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 8px 16px rgba(0,0,0,0.15)';
                });

                chart.addEventListener('mouseleave', function () {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '';
                });
            });
        });
    </script>
@endpush