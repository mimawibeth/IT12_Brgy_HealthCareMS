{{-- Dashboard Page: Shows overview and statistics --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <!-- Header -->
        <div class="content-header">
            <div>
                <h2>Dashboard Overview</h2>
                <p class="content-subtitle">Monitor health center activities, patient statistics, and program performance at
                    a glance.</p>
            </div>
        </div>

        @php
            $role = auth()->user()->role ?? 'bhw';
        @endphp

        <!-- Overview Cards: Display quick statistics -->
        <div class="stats-grid">
            <!-- Total Registered Patients Card -->
            <div class="stat-card">
                <div class="stat-icon stat-icon-blue">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ number_format($registeredPatients ?? 0) }}</div>
                    <div class="stat-label">Registered Patients</div>
                    <div class="stat-change">Total ITR Records</div>
                </div>
            </div>

            <!-- Active Health Programs Card -->
            <div class="stat-card">
                <div class="stat-icon stat-icon-green">
                    <i class="bi bi-heart-pulse"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ number_format($healthPrograms ?? 0) }}</div>
                    <div class="stat-label">Health Programs</div>
                    <div class="stat-change">Active Participants</div>
                </div>
            </div>

            <!-- Monthly Services Card -->
            <div class="stat-card">
                <div class="stat-icon stat-icon-purple">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ number_format($monthlyServices ?? 0) }}</div>
                    <div class="stat-label">Monthly Services</div>
                    <div class="stat-change">{{ $currentMonthName ?? 'Current Month' }}</div>
                </div>
            </div>

            <!-- Medicine Inventory Card -->
            <div class="stat-card">
                <div class="stat-icon stat-icon-orange">
                    <i class="bi bi-capsule"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ number_format($medicineStock ?? 0) }}</div>
                    <div class="stat-label">Medicine Stock</div>
                    <div class="stat-change">Available Items</div>
                </div>
            </div>
        </div>

        <!-- Health Programs Overview Section -->
        <div class="program-summary">
            <div class="section-header-inline">
                <h2>Health Programs Overview</h2>
                <p>Quick access to health program records and statistics</p>
            </div>

            <div class="program-cards">
                <!-- Prenatal Care Services -->
                <div class="program-card">
                    <div class="program-icon">
                        <i class="bi bi-heart-pulse"></i>
                    </div>
                    <div class="program-content">
                        <h3>Prenatal Care Services</h3>
                        <p class="program-count">{{ $prenatalTotal ?? 0 }} Registered Pregnant Women</p>
                        <a href="{{ route('health-programs.prenatal-view') }}" class="btn-link">
                            View Records <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Family Planning Services -->
                <div class="program-card">
                    <div class="program-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="program-content">
                        <h3>Family Planning Services</h3>
                        <p class="program-count">{{ $familyPlanningTotal ?? 0 }} Active FP Clients</p>
                        <a href="{{ route('health-programs.family-planning-view') }}" class="btn-link">
                            View Records <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Immunization Program (NIP) -->
                <div class="program-card">
                    <div class="program-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="program-content">
                        <h3>Immunization Program</h3>
                        <p class="program-count">{{ $nipTotal ?? 0 }} Children Enrolled</p>
                        <a href="{{ route('health-programs.nip-view') }}" class="btn-link">
                            View Records <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistical Analysis Section (Admins only) -->
        @if(in_array($role, ['super_admin', 'admin']))
            <div class="charts-section">
                <div class="section-header-inline">
                    <h2>Statistical Analysis</h2>
                    <p>Visual insights into health center performance and trends</p>
                </div>

                <div class="charts-grid">
                    <!-- Monthly Services Trend -->
                    <div class="chart-card" onclick="window.location.href='{{ route('reports.monthly') }}'">
                        <div class="chart-header">
                            <h3><i class="bi bi-graph-up"></i> Monthly Services Trend</h3>
                            <span class="chart-badge">Last 6 Months</span>
                        </div>
                        <div class="chart-placeholder">
                            <canvas id="consultationsChart"></canvas>
                        </div>
                        <p class="chart-footer">Click to view detailed monthly reports</p>
                    </div>

                    <!-- Program Distribution -->
                    <div class="chart-card" onclick="window.location.href='{{ route('reports.monthly') }}'">
                        <div class="chart-header">
                            <h3><i class="bi bi-pie-chart"></i> Program Distribution</h3>
                            <span class="chart-badge">{{ $currentMonthName ?? 'Current' }}</span>
                        </div>
                        <div class="chart-placeholder">
                            <canvas id="programsChart"></canvas>
                        </div>
                        <p class="chart-footer">Click to view program statistics</p>
                    </div>

                    <!-- Medicine Dispensing -->
                    <div class="chart-card" onclick="window.location.href='{{ route('reports.monthly') }}'">
                        <div class="chart-header">
                            <h3><i class="bi bi-bar-chart-line"></i> Medicine Dispensing</h3>
                            <span class="chart-badge">Weekly</span>
                        </div>
                        <div class="chart-placeholder">
                            <canvas id="medicineChart"></canvas>
                        </div>
                        <p class="chart-footer">Click to view inventory reports</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Two Column Layout: Stats and Activities -->
        <div class="dashboard-grid">
            <!-- Left Column: Monthly Statistics -->
            <div class="monthly-stats">
                <div class="section-header-inline">
                    <h2>Monthly Service Summary</h2>
                    <p>Compare current month with previous month performance</p>
                </div>

                <div class="stats-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Service Category</th>
                                <th>{{ $currentMonthName ?? 'Current' }}</th>
                                <th>Previous Month</th>
                                <th>Variance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Prenatal Care Consultations</strong></td>
                                <td>{{ $summary['prenatal']['current'] ?? 0 }}</td>
                                <td>{{ $summary['prenatal']['previous'] ?? 0 }}</td>
                                @php
                                    $prenatalVar = $summary['prenatal']['variance'] ?? 0;
                                @endphp
                                <td class="{{ $prenatalVar >= 0 ? 'positive' : 'negative' }}">
                                    <i class="bi bi-{{ $prenatalVar >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ $prenatalVar >= 0 ? '+' : '' }}{{ $prenatalVar }}%
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Family Planning Services</strong></td>
                                <td>{{ $summary['fp']['current'] ?? 0 }}</td>
                                <td>{{ $summary['fp']['previous'] ?? 0 }}</td>
                                @php
                                    $fpVar = $summary['fp']['variance'] ?? 0;
                                @endphp
                                <td class="{{ $fpVar >= 0 ? 'positive' : 'negative' }}">
                                    <i class="bi bi-{{ $fpVar >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ $fpVar >= 0 ? '+' : '' }}{{ $fpVar }}%
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Immunizations Administered</strong></td>
                                <td>{{ $summary['nip']['current'] ?? 0 }}</td>
                                <td>{{ $summary['nip']['previous'] ?? 0 }}</td>
                                @php
                                    $nipVar = $summary['nip']['variance'] ?? 0;
                                @endphp
                                <td class="{{ $nipVar >= 0 ? 'positive' : 'negative' }}">
                                    <i class="bi bi-{{ $nipVar >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ $nipVar >= 0 ? '+' : '' }}{{ $nipVar }}%
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Medicine Items Dispensed</strong></td>
                                <td>{{ $summary['medicine']['current'] ?? 0 }}</td>
                                <td>{{ $summary['medicine']['previous'] ?? 0 }}</td>
                                @php
                                    $medVar = $summary['medicine']['variance'] ?? 0;
                                @endphp
                                <td class="{{ $medVar >= 0 ? 'positive' : 'negative' }}">
                                    <i class="bi bi-{{ $medVar >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ $medVar >= 0 ? '+' : '' }}{{ $medVar }}%
                                </td>
                            </tr>
                            <tr>
                                <td><strong>New Patient Registrations</strong></td>
                                <td>{{ $summary['patients']['current'] ?? 0 }}</td>
                                <td>{{ $summary['patients']['previous'] ?? 0 }}</td>
                                @php
                                    $patVar = $summary['patients']['variance'] ?? 0;
                                @endphp
                                <td class="{{ $patVar >= 0 ? 'positive' : 'negative' }}">
                                    <i class="bi bi-{{ $patVar >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ $patVar >= 0 ? '+' : '' }}{{ $patVar }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: Recent System Activities -->
            <div class="recent-activities">
                <div class="section-header-inline">
                    <h2>Recent System Activities</h2>
                    <p>Latest actions and updates in the system</p>
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
        // Monthly Services Trend Chart
        const servicesLabels = @json($monthLabels ?? []);
        const servicesData = @json($servicesSeries ?? []);

        const ctx1 = document.getElementById('consultationsChart');
        if (ctx1) {
            new Chart(ctx1.getContext('2d'), {
                type: 'line',
                data: {
                    labels: servicesLabels,
                    datasets: [{
                        label: 'Health Services Provided',
                        data: servicesData,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // Health Programs Distribution Chart
        const programData = @json(array_values($programDistribution ?? []));
        const ctx2 = document.getElementById('programsChart');
        if (ctx2) {
            new Chart(ctx2.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Prenatal Care', 'Family Planning', 'Immunization (NIP)'],
                    datasets: [{
                        data: programData,
                        backgroundColor: ['#e74c3c', '#9b59b6', '#2ecc71'],
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

        // Medicine Dispensing Chart
        const weekLabels = @json($weeksLabels ?? []);
        const weekData = @json($weeksData ?? []);
        const ctx3 = document.getElementById('medicineChart');
        if (ctx3) {
            new Chart(ctx3.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: weekLabels,
                    datasets: [{
                        label: 'Medicine Items Dispensed',
                        data: weekData,
                        backgroundColor: '#3498db',
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