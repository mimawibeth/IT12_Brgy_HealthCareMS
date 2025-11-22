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

        <!-- Overview Cards: Display quick statistics -->
        <div class="stats-grid">

            <!-- Total Patients Card -->
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <div class="stat-details">
                    <h3>Total Patients</h3>
                    <p class="stat-number">1,234</p>
                    <span class="stat-label">Registered</span>
                </div>
            </div>

            <!-- Monthly Consultations Card -->
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-hospital"></i></div>
                <div class="stat-details">
                    <h3>Consultations</h3>
                    <p class="stat-number">345</p>
                    <span class="stat-label">This Month</span>
                </div>
            </div>

            <!-- Medicines in Stock Card -->
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-capsule"></i></div>
                <div class="stat-details">
                    <h3>Medicines</h3>
                    <p class="stat-number">89</p>
                    <span class="stat-label">In Stock</span>
                </div>
            </div>

            <!-- Low Stock Alerts Card -->
            <div class="stat-card alert-card">
                <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="stat-details">
                    <h3>Low Stock</h3>
                    <p class="stat-number">12</p>
                    <span class="stat-label">Items</span>
                </div>
            </div>

        </div>

        <!-- Program Summary Section -->
        <div class="program-summary">
            <h2>Program Summary</h2>

            <div class="program-cards">
                <!-- Prenatal Care -->
                <div class="program-card">
                    <h3><i class="bi bi-heart-pulse"></i> Prenatal Care</h3>
                    <p class="program-count">45 Active Mothers</p>
                    <a href="{{ route('health-programs.prenatal.records') }}" class="btn-link">View Details <i
                            class="bi bi-arrow-right"></i></a>
                </div>

                <!-- Family Planning -->
                <div class="program-card">
                    <h3><i class="bi bi-people-fill"></i> Family Planning</h3>
                    <p class="program-count">128 Active Clients</p>
                    <a href="{{ route('health-programs.fp.records') }}" class="btn-link">View Details <i
                            class="bi bi-arrow-right"></i></a>
                </div>

                <!-- Immunization -->
                <div class="program-card">
                    <h3><i class="bi bi-hospital"></i> Immunization</h3>
                    <p class="program-count">234 Children</p>
                    <a href="{{ route('health-programs.immunization.records') }}" class="btn-link">View Details <i
                            class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <h2>Performance Overview</h2>

            <div class="charts-grid">
                <!-- Consultations Trend Chart -->
                <div class="chart-card" onclick="window.location.href='{{ route('reports.monthly') }}'">
                    <h3><i class="bi bi-graph-up"></i> Consultations Trend</h3>
                    <div class="chart-placeholder">
                        <canvas id="consultationsChart"></canvas>
                    </div>
                    <p class="chart-footer">Click to view detailed reports</p>
                </div>

                <!-- Health Programs Distribution -->
                <div class="chart-card" onclick="window.location.href='{{ route('reports.monthly') }}'">
                    <h3><i class="bi bi-pie-chart"></i> Health Programs</h3>
                    <div class="chart-placeholder">
                        <canvas id="programsChart"></canvas>
                    </div>
                    <p class="chart-footer">Click to view detailed reports</p>
                </div>

                <!-- Medicine Usage -->
                <div class="chart-card" onclick="window.location.href='{{ route('reports.monthly') }}'">
                    <h3><i class="bi bi-bar-chart-line"></i> Medicine Usage</h3>
                    <div class="chart-placeholder">
                        <canvas id="medicineChart"></canvas>
                    </div>
                    <p class="chart-footer">Click to view detailed reports</p>
                </div>
            </div>
        </div>

        <!-- Two Column Layout: Stats and Activities -->
        <div class="dashboard-grid">
            <!-- Left Column: Monthly Stats -->
            <div class="monthly-stats">
                <h2>Monthly Statistics</h2>

                <div class="stats-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>This Month</th>
                                <th>Last Month</th>
                                <th>Change</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Consultations</td>
                                <td>345</td>
                                <td>312</td>
                                <td class="positive">+10.6%</td>
                            </tr>
                            <tr>
                                <td>New Patients</td>
                                <td>67</td>
                                <td>54</td>
                                <td class="positive">+24.1%</td>
                            </tr>
                            <tr>
                                <td>Medicines Dispensed</td>
                                <td>1,234</td>
                                <td>1,456</td>
                                <td class="negative">-15.2%</td>
                            </tr>
                            <tr>
                                <td>Prenatal Visits</td>
                                <td>89</td>
                                <td>95</td>
                                <td class="negative">-6.3%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: Recent Activities -->
            <div class="recent-activities">
                <h2>Recent Activities</h2>

                <div class="activity-list">
                    <!-- Activity Item -->
                    <div class="activity-item">
                        <span class="activity-icon"><i class="bi bi-check-circle"></i></span>
                        <div class="activity-details">
                            <p class="activity-text">New patient registered: Maria Santos</p>
                            <span class="activity-time">5 minutes ago</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <span class="activity-icon"><i class="bi bi-capsule"></i></span>
                        <div class="activity-details">
                            <p class="activity-text">Medicine dispensed: Paracetamol (10 tablets)</p>
                            <span class="activity-time">15 minutes ago</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <span class="activity-icon"><i class="bi bi-journal-text"></i></span>
                        <div class="activity-details">
                            <p class="activity-text">Prenatal visit recorded for Ana Cruz</p>
                            <span class="activity-time">1 hour ago</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <span class="activity-icon"><i class="bi bi-exclamation-triangle"></i></span>
                        <div class="activity-details">
                            <p class="activity-text">Low stock alert: Amoxicillin</p>
                            <span class="activity-time">2 hours ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Consultations Trend Chart
        const ctx1 = document.getElementById('consultationsChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Consultations',
                    data: [280, 295, 310, 298, 312, 345],
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

        // Health Programs Pie Chart
        const ctx2 = document.getElementById('programsChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Prenatal', 'Family Planning', 'Immunization'],
                datasets: [{
                    data: [45, 128, 234],
                    backgroundColor: ['#3498db', '#9b59b6', '#1abc9c']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Medicine Usage Bar Chart
        const ctx3 = document.getElementById('medicineChart').getContext('2d');
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Medicines Dispensed',
                    data: [280, 350, 298, 306],
                    backgroundColor: '#2ecc71'
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