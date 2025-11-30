{{-- Dashboard Page: Shows overview and statistics --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <!-- Dashboard Overview Section -->
    <div class="dashboard-overview">

        @php
            $role = auth()->user()->role ?? 'bhw';
        @endphp
        <div class="info-box" style="margin-bottom: 20px;">
            @if($role === 'super_admin')
            @elseif($role === 'admin')
            @else
            @endif
        </div>

        <!-- Overview Cards: Display quick statistics -->
        <div class="stats-grid">

            <!-- Total Registered Patients Card -->
            <div class="stat-card">
                <div class="stat-icon stat-icon-blue"><i class="bi bi-people"></i></div>
                <div class="stat-details">
                    <h3>Registered Patients</h3>
                    <p class="stat-number">{{ number_format($registeredPatients ?? 0) }}</p>
                    <span class="stat-label">Total ITR Records</span>
                </div>
            </div>

            <!-- Active Health Programs Card -->
            <div class="stat-card">
                <div class="stat-icon stat-icon-green"><i class="bi bi-heart-pulse"></i></div>
                <div class="stat-details">
                    <h3>Health Programs</h3>
                    <p class="stat-number">{{ number_format($healthPrograms ?? 0) }}</p>
                    <span class="stat-label">Active Participants</span>
                </div>
            </div>

            <!-- Monthly Services Card -->
            <div class="stat-card">
                <div class="stat-icon stat-icon-orange"><i class="bi bi-clipboard2-pulse"></i></div>
                <div class="stat-details">
                    <h3>Monthly Services</h3>
                    <p class="stat-number">{{ number_format($monthlyServices ?? 0) }}</p>
                    <span class="stat-label">{{ $currentMonthName ?? '' }}</span>
                </div>
            </div>

            <!-- Medicine Inventory Card -->
            <div class="stat-card">
                <div class="stat-icon stat-icon-purple"><i class="bi bi-capsule"></i></div>
                <div class="stat-details">
                    <h3>Medicine Stock</h3>
                    <p class="stat-number">{{ number_format($medicineStock ?? 0) }}</p>
                    <span class="stat-label">Available Items</span>
                </div>
            </div>

        </div>

        <!-- Health Programs Overview Section -->
        <div class="program-summary">
            <h2>Health Programs Overview</h2>

            <div class="program-cards">
                <!-- Prenatal Care Services -->
                <div class="program-card">
                    <h3><i class="bi bi-heart-pulse"></i> Prenatal Care Services</h3>
                    <p class="program-count">{{ $prenatalTotal ?? 0 }} Registered Pregnant Women</p>
                    <a href="{{ route('health-programs.prenatal-view') }}" class="btn-link">View Records <i
                            class="bi bi-arrow-right"></i></a>
                </div>

                <!-- Family Planning Services -->
                <div class="program-card">
                    <h3><i class="bi bi-people-fill"></i> Family Planning Services</h3>
                    <p class="program-count">{{ $familyPlanningTotal ?? 0 }} Active FP Clients</p>
                    <a href="{{ route('health-programs.family-planning-view') }}" class="btn-link">View Records <i
                            class="bi bi-arrow-right"></i></a>
                </div>

                <!-- Immunization Program (NIP) -->
                <div class="program-card">
                    <h3><i class="bi bi-shield-check"></i> Immunization Program</h3>
                    <p class="program-count">{{ $nipTotal ?? 0 }} Children Enrolled</p>
                    <a href="{{ route('health-programs.nip-view') }}" class="btn-link">View Records <i
                            class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Statistical Analysis Section (Admins only) -->
        @if(in_array($role, ['super_admin', 'admin']))
            <div class="charts-section">
                <h2>Statistical Analysis</h2>

                <div class="charts-grid">
                    <!-- Monthly Services Trend -->
                    <div class="chart-card" onclick="window.location.href='{{ route('reports.monthly') }}'">
                        <h3><i class="bi bi-graph-up"></i> Monthly Services Trend</h3>
                        <div class="chart-placeholder">
                            <canvas id="consultationsChart"></canvas>
                        </div>
                        <p class="chart-footer">Click to view detailed monthly reports</p>
                    </div>

                    <!-- Program Distribution -->
                    <div class="chart-card" onclick="window.location.href='{{ route('reports.monthly') }}'">
                        <h3><i class="bi bi-pie-chart"></i> Program Distribution</h3>
                        <div class="chart-placeholder">
                            <canvas id="programsChart"></canvas>
                        </div>
                        <p class="chart-footer">Click to view program statistics</p>
                    </div>

                    <!-- Medicine Dispensing -->
                    <div class="chart-card" onclick="window.location.href='{{ route('reports.monthly') }}'">
                        <h3><i class="bi bi-bar-chart-line"></i> Medicine Dispensing</h3>
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
                <h2>Monthly Service Summary</h2>

                <div class="stats-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Service Category</th>
                                <th>November 2025</th>
                                <th>October 2025</th>
                                <th>Variance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Prenatal Care Consultations</td>
                                <td>{{ $summary['prenatal']['current'] ?? 0 }}</td>
                                <td>{{ $summary['prenatal']['previous'] ?? 0 }}</td>
                                @php
                                    $prenatalVar = $summary['prenatal']['variance'] ?? 0;
                                @endphp
                                <td class="{{ $prenatalVar >= 0 ? 'positive' : 'negative' }}">
                                    {{ $prenatalVar >= 0 ? '+' : '' }}{{ $prenatalVar }}%
                                </td>
                            </tr>
                            <tr>
                                <td>Family Planning Services</td>
                                <td>{{ $summary['fp']['current'] ?? 0 }}</td>
                                <td>{{ $summary['fp']['previous'] ?? 0 }}</td>
                                @php
                                    $fpVar = $summary['fp']['variance'] ?? 0;
                                @endphp
                                <td class="{{ $fpVar >= 0 ? 'positive' : 'negative' }}">
                                    {{ $fpVar >= 0 ? '+' : '' }}{{ $fpVar }}%
                                </td>
                            </tr>
                            <tr>
                                <td>Immunizations Administered</td>
                                <td>{{ $summary['nip']['current'] ?? 0 }}</td>
                                <td>{{ $summary['nip']['previous'] ?? 0 }}</td>
                                @php
                                    $nipVar = $summary['nip']['variance'] ?? 0;
                                @endphp
                                <td class="{{ $nipVar >= 0 ? 'positive' : 'negative' }}">
                                    {{ $nipVar >= 0 ? '+' : '' }}{{ $nipVar }}%
                                </td>
                            </tr>
                            <tr>
                                <td>Medicine Items Dispensed</td>
                                <td>{{ $summary['medicine']['current'] ?? 0 }}</td>
                                <td>{{ $summary['medicine']['previous'] ?? 0 }}</td>
                                @php
                                    $medVar = $summary['medicine']['variance'] ?? 0;
                                @endphp
                                <td class="{{ $medVar >= 0 ? 'positive' : 'negative' }}">
                                    {{ $medVar >= 0 ? '+' : '' }}{{ $medVar }}%
                                </td>
                            </tr>
                            <tr>
                                <td>New Patient Registrations</td>
                                <td>{{ $summary['patients']['current'] ?? 0 }}</td>
                                <td>{{ $summary['patients']['previous'] ?? 0 }}</td>
                                @php
                                    $patVar = $summary['patients']['variance'] ?? 0;
                                @endphp
                                <td class="{{ $patVar >= 0 ? 'positive' : 'negative' }}">
                                    {{ $patVar >= 0 ? '+' : '' }}{{ $patVar }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: Recent System Activities -->
            <div class="recent-activities">
                <h2>Recent System Activities</h2>

                <div class="activity-list">
                    @forelse($recentLogs as $log)
                        @php
                            $icon = 'bi-activity';
                            switch ($log->action) {
                                case 'login':
                                    $icon = 'bi-person-check';
                                    break;
                                case 'logout':
                                    $icon = 'bi-box-arrow-right';
                                    break;
                                case 'create':
                                    $icon = 'bi-plus-circle';
                                    break;
                                case 'update':
                                    $icon = 'bi-pencil-square';
                                    break;
                                case 'delete':
                                    $icon = 'bi-trash';
                                    break;
                                case 'dispense':
                                    $icon = 'bi-capsule';
                                    break;
                                case 'export':
                                    $icon = 'bi-file-earmark-arrow-down';
                                    break;
                            }

                            $userName = $log->user->name ?? 'System';
                        @endphp
                        <div class="activity-item">
                            <span class="activity-icon"><i class="bi {{ $icon }}"></i></span>
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

        const ctx1 = document.getElementById('consultationsChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: servicesLabels,
                datasets: [{
                    label: 'Health Services Provided',
                    data: servicesData,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // Health Programs Distribution Chart
        const programData = @json(array_values($programDistribution ?? []));
        const ctx2 = document.getElementById('programsChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Prenatal Care', 'Family Planning', 'Immunization (NIP)'],
                datasets: [{
                    data: programData,
                    backgroundColor: ['#e74c3c', '#9b59b6', '#2ecc71']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Medicine Dispensing Chart
        const weekLabels = @json($weeksLabels ?? []);
        const weekData = @json($weeksData ?? []);

        const ctx3 = document.getElementById('medicineChart').getContext('2d');
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: weekLabels,
                datasets: [{
                    label: 'Medicine Items Dispensed',
                    data: weekData,
                    backgroundColor: '#3498db'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    </script>
@endpush