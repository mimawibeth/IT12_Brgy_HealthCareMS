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

            <!-- Total Registered Patients Card -->
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <div class="stat-details">
                    <h3>Registered Patients</h3>
                    <p class="stat-number">1,234</p>
                    <span class="stat-label">Total ITR Records</span>
                </div>
            </div>

            <!-- Active Health Programs Card -->
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-heart-pulse"></i></div>
                <div class="stat-details">
                    <h3>Health Programs</h3>
                    <p class="stat-number">407</p>
                    <span class="stat-label">Active Participants</span>
                </div>
            </div>

            <!-- Monthly Services Card -->
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-clipboard2-pulse"></i></div>
                <div class="stat-details">
                    <h3>Monthly Services</h3>
                    <p class="stat-number">345</p>
                    <span class="stat-label">November 2025</span>
                </div>
            </div>

            <!-- Medicine Inventory Card -->
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-capsule"></i></div>
                <div class="stat-details">
                    <h3>Medicine Stock</h3>
                    <p class="stat-number">89</p>
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
                    <p class="program-count">48 Registered Pregnant Women</p>
                    <a href="{{ route('health-programs.prenatal.records') }}" class="btn-link">View Records <i
                            class="bi bi-arrow-right"></i></a>
                </div>

                <!-- Family Planning Services -->
                <div class="program-card">
                    <h3><i class="bi bi-people-fill"></i> Family Planning Services</h3>
                    <p class="program-count">125 Active FP Clients</p>
                    <a href="{{ route('health-programs.fp.records') }}" class="btn-link">View Records <i
                            class="bi bi-arrow-right"></i></a>
                </div>

                <!-- Immunization Program (NIP) -->
                <div class="program-card">
                    <h3><i class="bi bi-shield-check"></i> Immunization Program</h3>
                    <p class="program-count">234 Children Enrolled</p>
                    <a href="{{ route('health-programs.immunization.records') }}" class="btn-link">View Records <i
                            class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Statistical Analysis Section -->
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
                                <td>48</td>
                                <td>45</td>
                                <td class="positive">+6.7%</td>
                            </tr>
                            <tr>
                                <td>Family Planning Services</td>
                                <td>125</td>
                                <td>118</td>
                                <td class="positive">+5.9%</td>
                            </tr>
                            <tr>
                                <td>Immunizations Administered</td>
                                <td>89</td>
                                <td>95</td>
                                <td class="negative">-6.3%</td>
                            </tr>
                            <tr>
                                <td>Medicine Items Dispensed</td>
                                <td>1,234</td>
                                <td>1,456</td>
                                <td class="negative">-15.2%</td>
                            </tr>
                            <tr>
                                <td>New Patient Registrations</td>
                                <td>67</td>
                                <td>54</td>
                                <td class="positive">+24.1%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: Recent System Activities -->
            <div class="recent-activities">
                <h2>Recent System Activities</h2>

                <div class="activity-list">
                    <!-- Activity Item -->
                    <div class="activity-item">
                        <span class="activity-icon"><i class="bi bi-person-plus"></i></span>
                        <div class="activity-details">
                            <p class="activity-text">Patient Registration: Maria Santos (ITR-2025-152)</p>
                            <span class="activity-time">5 minutes ago</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <span class="activity-icon"><i class="bi bi-heart-pulse"></i></span>
                        <div class="activity-details">
                            <p class="activity-text">Prenatal Consultation: Ana Cruz - Checkup completed</p>
                            <span class="activity-time">25 minutes ago</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <span class="activity-icon"><i class="bi bi-shield-check"></i></span>
                        <div class="activity-details">
                            <p class="activity-text">Immunization: BCG vaccine administered to Juan Reyes</p>
                            <span class="activity-time">1 hour ago</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <span class="activity-icon"><i class="bi bi-capsule"></i></span>
                        <div class="activity-details">
                            <p class="activity-text">Medicine Dispensed: Paracetamol 500mg (10 tablets)</p>
                            <span class="activity-time">1 hour ago</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <span class="activity-icon"><i class="bi bi-exclamation-triangle"></i></span>
                        <div class="activity-details">
                            <p class="activity-text">Inventory Alert: Amoxicillin stock level critical</p>
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
        // Monthly Services Trend Chart
        const ctx1 = document.getElementById('consultationsChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['June', 'July', 'August', 'September', 'October', 'November'],
                datasets: [{
                    label: 'Health Services Provided',
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

        // Health Programs Distribution Chart
        const ctx2 = document.getElementById('programsChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Prenatal Care', 'Family Planning', 'Immunization (NIP)'],
                datasets: [{
                    data: [48, 125, 234],
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
        const ctx3 = document.getElementById('medicineChart').getContext('2d');
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Medicine Items Dispensed',
                    data: [280, 350, 298, 306],
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