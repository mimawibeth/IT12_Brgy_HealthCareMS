{{-- Reports & Analytics - Monthly Health Reports (Super Admin & Admin View) --}}
@extends('layouts.app')

@section('title', 'Monthly Reports')
@section('page-title', 'Monthly Reports')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Header -->
        <div class="content-header">
            <h2>Monthly Health Reports</h2>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="printReport()">
                    <i class="bi bi-printer"></i> Print Report
                </button>
                <button class="btn btn-primary" onclick="exportReport()">
                    <i class="bi bi-download"></i> Export Report
                </button>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">152</div>
                    <div class="stat-label">Total Patients</div>
                    <div class="stat-change positive">+12 from last month</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-heart-pulse"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">48</div>
                    <div class="stat-label">Prenatal Consultations</div>
                    <div class="stat-change positive">+5 from last month</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">89</div>
                    <div class="stat-label">Immunizations Given</div>
                    <div class="stat-change positive">+15 from last month</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">34</div>
                    <div class="stat-label">Family Planning Clients</div>
                    <div class="stat-change">No change</div>
                </div>
            </div>
        </div>

        <!-- Report Filter Section -->
        <div class="report-filters">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="report-month">Month</label>
                    <select id="report-month" class="filter-select">
                        <option value="11">November 2025</option>
                        <option value="10">October 2025</option>
                        <option value="9">September 2025</option>
                        <option value="8">August 2025</option>
                        <option value="7">July 2025</option>
                        <option value="6">June 2025</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="report-type">Report Type</label>
                    <select id="report-type" class="filter-select">
                        <option value="summary">Summary Report</option>
                        <option value="patients">Patient Statistics</option>
                        <option value="prenatal">Prenatal Services</option>
                        <option value="fp">Family Planning</option>
                        <option value="immunization">Immunization</option>
                        <option value="medicine">Medicine Inventory</option>
                    </select>
                </div>

                <button class="btn btn-primary" onclick="generateReport()">
                    <i class="bi bi-file-earmark-bar-graph"></i> Generate Report
                </button>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section-title">
            <h2>Statistical Analysis & Trends</h2>
        </div>

        <div class="charts-grid">
            <!-- Monthly Services Trend -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="bi bi-graph-up"></i> Monthly Services Trend</h3>
                    <span class="chart-period">Last 6 Months</span>
                </div>
                <div class="chart-placeholder">
                    <canvas id="servicesChart"></canvas>
                    <p>Overall service delivery trends across all programs</p>
                </div>
            </div>

            <!-- Program Distribution -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="bi bi-pie-chart"></i> Health Program Distribution</h3>
                    <span class="chart-period">November 2025</span>
                </div>
                <div class="chart-placeholder">
                    <canvas id="programDistributionChart"></canvas>
                    <p>Service breakdown by health programs</p>
                </div>
            </div>

            <!-- Patient Demographics -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="bi bi-people"></i> Patient Demographics</h3>
                    <span class="chart-period">Age Group Distribution</span>
                </div>
                <div class="chart-placeholder">
                    <canvas id="demographicsChart"></canvas>
                    <p>Patient breakdown by age groups</p>
                </div>
            </div>

            <!-- Service Completion Rate -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="bi bi-check-circle"></i> Service Completion Rate</h3>
                    <span class="chart-period">November 2025</span>
                </div>
                <div class="chart-placeholder">
                    <canvas id="completionChart"></canvas>
                    <p>Completed vs Pending services</p>
                </div>
            </div>

            <!-- Top Health Programs -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="bi bi-bar-chart"></i> Top Health Programs</h3>
                    <span class="chart-period">Cases Handled</span>
                </div>
                <div class="chart-placeholder">
                    <canvas id="topProgramsChart"></canvas>
                    <p>Most active health programs this month</p>
                </div>
            </div>

            <!-- Gender Distribution -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="bi bi-gender-ambiguous"></i> Gender Distribution</h3>
                    <span class="chart-period">Patient Statistics</span>
                </div>
                <div class="chart-placeholder">
                    <canvas id="genderChart"></canvas>
                    <p>Male vs Female patient breakdown</p>
                </div>
            </div>
        </div>

        <!-- Detailed Statistics Table -->
        <div class="report-section">
            <div class="section-header">
                <h3>Detailed Monthly Statistics</h3>
                <p>November 2025 Summary Report</p>
            </div>

            <div class="table-container">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Program/Service</th>
                            <th>Total Cases</th>
                            <th>New Cases</th>
                            <th>Follow-ups</th>
                            <th>Completed</th>
                            <th>Pending</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Prenatal Care</strong></td>
                            <td>48</td>
                            <td>12</td>
                            <td>36</td>
                            <td>8</td>
                            <td>40</td>
                        </tr>
                        <tr>
                            <td><strong>Family Planning</strong></td>
                            <td>34</td>
                            <td>5</td>
                            <td>29</td>
                            <td>30</td>
                            <td>4</td>
                        </tr>
                        <tr>
                            <td><strong>Immunization (NIP)</strong></td>
                            <td>89</td>
                            <td>45</td>
                            <td>44</td>
                            <td>89</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td><strong>Nutrition Program</strong></td>
                            <td>56</td>
                            <td>18</td>
                            <td>38</td>
                            <td>52</td>
                            <td>4</td>
                        </tr>
                        <tr>
                            <td><strong>Child Care Services</strong></td>
                            <td>67</td>
                            <td>23</td>
                            <td>44</td>
                            <td>65</td>
                            <td>2</td>
                        </tr>
                        <tr>
                            <td><strong>Deworming</strong></td>
                            <td>120</td>
                            <td>120</td>
                            <td>0</td>
                            <td>120</td>
                            <td>0</td>
                        </tr>
                        <tr class="table-total">
                            <td><strong>TOTAL</strong></td>
                            <td><strong>414</strong></td>
                            <td><strong>223</strong></td>
                            <td><strong>191</strong></td>
                            <td><strong>364</strong></td>
                            <td><strong>50</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Age Distribution Report -->
        <div class="report-section">
            <div class="section-header">
                <h3>Patient Age Distribution</h3>
                <p>Breakdown by age groups</p>
            </div>

            <div class="table-container">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Age Group</th>
                            <th>Male</th>
                            <th>Female</th>
                            <th>Total</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>0-5 years (Infant/Toddler)</strong></td>
                            <td>28</td>
                            <td>25</td>
                            <td>53</td>
                            <td>34.9%</td>
                        </tr>
                        <tr>
                            <td><strong>6-12 years (Children)</strong></td>
                            <td>18</td>
                            <td>20</td>
                            <td>38</td>
                            <td>25.0%</td>
                        </tr>
                        <tr>
                            <td><strong>13-19 years (Adolescent)</strong></td>
                            <td>8</td>
                            <td>12</td>
                            <td>20</td>
                            <td>13.2%</td>
                        </tr>
                        <tr>
                            <td><strong>20-59 years (Adult)</strong></td>
                            <td>5</td>
                            <td>32</td>
                            <td>37</td>
                            <td>24.3%</td>
                        </tr>
                        <tr>
                            <td><strong>60+ years (Senior)</strong></td>
                            <td>2</td>
                            <td>2</td>
                            <td>4</td>
                            <td>2.6%</td>
                        </tr>
                        <tr class="table-total">
                            <td><strong>TOTAL</strong></td>
                            <td><strong>61</strong></td>
                            <td><strong>91</strong></td>
                            <td><strong>152</strong></td>
                            <td><strong>100%</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // Chart configuration
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    labels: {
                        font: { size: 12 },
                        usePointStyle: true,
                        padding: 15
                    }
                }
            }
        };

        // Initialize Monthly Services Trend Chart
        const servicesCtx = document.getElementById('servicesChart').getContext('2d');
        new Chart(servicesCtx, {
            type: 'line',
            data: {
                labels: ['June', 'July', 'August', 'September', 'October', 'November'],
                datasets: [
                    {
                        label: 'Prenatal Care',
                        data: [42, 45, 44, 46, 45, 48],
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Family Planning',
                        data: [110, 115, 120, 122, 118, 125],
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Immunization',
                        data: [75, 80, 85, 90, 95, 89],
                        borderColor: '#2ecc71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Initialize Program Distribution Chart
        const programCtx = document.getElementById('programDistributionChart').getContext('2d');
        new Chart(programCtx, {
            type: 'doughnut',
            data: {
                labels: ['Prenatal Care', 'Family Planning', 'Immunization', 'Nutrition Program', 'Child Care'],
                datasets: [{
                    data: [48, 125, 89, 56, 67],
                    backgroundColor: [
                        '#e74c3c',
                        '#3498db',
                        '#2ecc71',
                        '#f39c12',
                        '#9b59b6'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: chartOptions
        });

        // Initialize Patient Demographics Chart
        const demographicsCtx = document.getElementById('demographicsChart').getContext('2d');
        new Chart(demographicsCtx, {
            type: 'bar',
            data: {
                labels: ['0-5 years', '6-12 years', '13-19 years', '20-59 years', '60+ years'],
                datasets: [{
                    label: 'Number of Patients',
                    data: [53, 38, 20, 37, 4],
                    backgroundColor: '#3498db',
                    borderColor: '#2980b9',
                    borderWidth: 1
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Initialize Service Completion Rate Chart
        const completionCtx = document.getElementById('completionChart').getContext('2d');
        new Chart(completionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending', 'Follow-ups'],
                datasets: [{
                    data: [364, 50, 191],
                    backgroundColor: [
                        '#2ecc71',
                        '#e74c3c',
                        '#f39c12'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: chartOptions
        });

        // Initialize Top Health Programs Chart
        const topProgramsCtx = document.getElementById('topProgramsChart').getContext('2d');
        new Chart(topProgramsCtx, {
            type: 'horizontalBar',
            type: 'bar',
            data: {
                labels: ['Deworming', 'Immunization', 'Prenatal Care', 'Family Planning', 'Nutrition'],
                datasets: [{
                    label: 'Cases Handled',
                    data: [120, 89, 48, 34, 56],
                    backgroundColor: ['#3498db', '#2ecc71', '#e74c3c', '#9b59b6', '#f39c12'],
                    borderColor: ['#2980b9', '#27ae60', '#c0392b', '#8e44ad', '#d68910'],
                    borderWidth: 1
                }]
            },
            options: {
                ...chartOptions,
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });

        // Initialize Gender Distribution Chart
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        new Chart(genderCtx, {
            type: 'pie',
            data: {
                labels: ['Female', 'Male'],
                datasets: [{
                    data: [91, 61],
                    backgroundColor: ['#e74c3c', '#3498db'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: chartOptions
        });

        // Report Functions
        function generateReport() {
            const month = document.getElementById('report-month').value;
            const type = document.getElementById('report-type').value;

            // TODO: Implement report generation logic
            console.log('Generating report:', { month, type });
            alert('Report generation will be implemented with backend');
        }

        function exportReport() {
            // TODO: Implement export logic
            console.log('Exporting report...');
            alert('Export functionality will be implemented with backend');
        }

        function printReport() {
            window.print();
        }
    </script>
@endpush