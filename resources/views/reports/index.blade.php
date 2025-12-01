{{-- Reports & Analytics - Monthly Health Reports (Super Admin & Admin View) --}}
@extends('layouts.app')

@section('title', 'Monthly Reports')
@section('page-title', 'Monthly Reports')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <!-- Header -->
        <div class="content-header">

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
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ number_format($totalPatients ?? 0) }}</div>
                    <div class="stat-label">Total Patients</div>
                    <div class="stat-change">{{ $selectedMonthLabel ?? '' }}</div>
                </div>
            </div>

            <div class="stat-card stat-card-danger">
                <div class="stat-icon">
                    <i class="bi bi-heart-pulse"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ number_format($prenatalCount ?? 0) }}</div>
                    <div class="stat-label">Prenatal Consultations</div>
                    <div class="stat-change">{{ $selectedMonthLabel ?? '' }}</div>
                </div>
            </div>

            <div class="stat-card stat-card-success">
                <div class="stat-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ number_format($nipCount ?? 0) }}</div>
                    <div class="stat-label">Immunizations Given</div>
                    <div class="stat-change">{{ $selectedMonthLabel ?? '' }}</div>
                </div>
            </div>

            <div class="stat-card stat-card-info">
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ number_format($fpCount ?? 0) }}</div>
                    <div class="stat-label">Family Planning Clients</div>
                    <div class="stat-change">{{ $selectedMonthLabel ?? '' }}</div>
                </div>
            </div>
        </div>

        <!-- Report Filter Section -->
        <div class="filters">
            <form id="report-filters-form" method="GET" action="{{ route('reports.monthly') }}">
                <div class="filter-options">
                    <select name="month" class="filter-select">
                        @for ($m = 1; $m <= 12; $m++)
                            @php
                                $label = \Carbon\Carbon::create(2000, $m, 1)->format('F');
                            @endphp
                            <option value="{{ $m }}" @selected(($selectedMonthValue ?? null) == $m)>{{ $label }}</option>
                        @endfor
                    </select>
                    <select name="year" class="filter-select">
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" @selected(($selectedYearValue ?? null) == $y)>{{ $y }}</option>
                        @endfor
                    </select>
                    <select name="type" class="filter-select">
                        <option value="summary">Summary Report</option>
                        <option value="patients">Patient Statistics</option>
                        <option value="prenatal">Prenatal Services</option>
                        <option value="fp">Family Planning</option>
                        <option value="immunization">Immunization</option>
                        <option value="medicine">Medicine Inventory</option>
                    </select>
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-file-earmark-bar-graph"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Charts Section -->
        <div class="charts-section-title">
            <h2>Statistical Analysis & Trends</h2>
            <p>Visual representation of health center data and performance metrics</p>
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
                    <span class="chart-period">{{ $selectedMonthLabel ?? 'Current Month' }}</span>
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
                    <span class="chart-period">{{ $selectedMonthLabel ?? 'Current Month' }}</span>
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
                <p>{{ $selectedMonthLabel ?? '' }} Summary Report</p>
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
                            <td>{{ $prenatalCount ?? 0 }}</td>
                            <td>—</td>
                            <td>—</td>
                            <td>—</td>
                            <td>—</td>
                        </tr>
                        <tr>
                            <td><strong>Family Planning</strong></td>
                            <td>{{ $fpCount ?? 0 }}</td>
                            <td>—</td>
                            <td>—</td>
                            <td>—</td>
                            <td>—</td>
                        </tr>
                        <tr>
                            <td><strong>Immunization (NIP)</strong></td>
                            <td>{{ $nipCount ?? 0 }}</td>
                            <td>—</td>
                            <td>—</td>
                            <td>{{ $nipCount ?? 0 }}</td>
                            <td>0</td>
                        </tr>
                        @php
                            $totalCases = ($prenatalCount ?? 0) + ($fpCount ?? 0) + ($nipCount ?? 0);
                        @endphp
                        <tr class="table-total">
                            <td><strong>TOTAL</strong></td>
                            <td><strong>{{ $totalCases }}</strong></td>
                            <td><strong>—</strong></td>
                            <td><strong>—</strong></td>
                            <td><strong>—</strong></td>
                            <td><strong>—</strong></td>
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
                @php
                    $grandMale = array_sum($ageMale ?? []);
                    $grandFemale = array_sum($ageFemale ?? []);
                    $grandTotal = $grandMale + $grandFemale;
                @endphp
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
                        @foreach(($ageLabels ?? []) as $i => $label)
                            @php
                                $male = $ageMale[$i] ?? 0;
                                $female = $ageFemale[$i] ?? 0;
                                $total = $male + $female;
                                $percent = $grandTotal > 0 ? round(($total / $grandTotal) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td><strong>{{ $label }}</strong></td>
                                <td>{{ $male }}</td>
                                <td>{{ $female }}</td>
                                <td>{{ $total }}</td>
                                <td>{{ $percent }}%</td>
                            </tr>
                        @endforeach
                        <tr class="table-total">
                            <td><strong>TOTAL</strong></td>
                            <td><strong>{{ $grandMale }}</strong></td>
                            <td><strong>{{ $grandFemale }}</strong></td>
                            <td><strong>{{ $grandTotal }}</strong></td>
                            <td><strong>{{ $grandTotal > 0 ? '100%' : '0%' }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 }
                }
            }
        };

        const monthLabels = @json($monthLabels ?? []);
        const prenatalSeries = @json($prenatalSeries ?? []);
        const fpSeries = @json($fpSeries ?? []);
        const nipSeries = @json($nipSeries ?? []);

        // Initialize Monthly Services Trend Chart
        const servicesCtx = document.getElementById('servicesChart');
        if (servicesCtx) {
            new Chart(servicesCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: monthLabels,
                    datasets: [
                        {
                            label: 'Prenatal Care',
                            data: prenatalSeries,
                            borderColor: '#e74c3c',
                            backgroundColor: 'rgba(231, 76, 60, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Family Planning',
                            data: fpSeries,
                            borderColor: '#3498db',
                            backgroundColor: 'rgba(52, 152, 219, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Immunization',
                            data: nipSeries,
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
        }

        // Initialize Program Distribution Chart
        const programCtx = document.getElementById('programDistributionChart').getContext('2d');
        new Chart(programCtx, {
            type: 'doughnut',
            data: {
                labels: ['Prenatal Care', 'Family Planning', 'Immunization'],
                datasets: [{
                    data: [
                                        {{ $programDistribution['prenatal'] ?? 0 }},
                                        {{ $programDistribution['fp'] ?? 0 }},
                                        {{ $programDistribution['nip'] ?? 0 }},
                    ],
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
        const ageLabels = @json($ageLabels ?? []);
        const ageMale = @json($ageMale ?? []);
        const ageFemale = @json($ageFemale ?? []);

        const demographicsCtx = document.getElementById('demographicsChart');
        if (demographicsCtx) {
            new Chart(demographicsCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ageLabels,
                    datasets: [
                        {
                            label: 'Male',
                            data: ageMale,
                            backgroundColor: '#3498db',
                            borderColor: '#2980b9',
                            borderWidth: 1
                        },
                        {
                            label: 'Female',
                            data: ageFemale,
                            backgroundColor: '#e74c3c',
                            borderColor: '#c0392b',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    ...chartOptions,
                    scales: {
                        y: { beginAtZero: true }
                    }
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
                    data: @json($completionData ?? [0, 0, 0]),
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
            type: 'bar',
            data: {
                labels: ['Deworming', 'Immunization', 'Prenatal Care', 'Family Planning', 'Nutrition'],
                datasets: [{
                    label: 'Cases Handled',
                    data: @json($topProgramsData),
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
                    data: [
                                        {{ $genderCounts['F'] ?? 0 }},
                                        {{ $genderCounts['M'] ?? 0 }},
                    ],
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

        // Initialize Service Completion Rate Chart
        const completionCtx = document.getElementById('completionChart');
        if (completionCtx) {
            new Chart(completionCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Pending', 'Follow-ups'],
                    datasets: [{
                        data: @json($completionData ?? [0, 0, 0]),
                        backgroundColor: [
                            '#2ecc71',
                            '#e74c3c',
                            '#f39c12'
                        ],
                        borderColor: '#fff',
                        borderWidth: 3
                    }]
                },
                options: chartOptions
            });
        }

        // Initialize Top Health Programs Chart
        const topProgramsCtx = document.getElementById('topProgramsChart');
        if (topProgramsCtx) {
            new Chart(topProgramsCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Deworming', 'Immunization', 'Prenatal Care', 'Family Planning', 'Nutrition'],
                    datasets: [{
                        label: 'Cases Handled',
                        data: @json($topProgramsData ?? []),
                        backgroundColor: ['#3498db', '#2ecc71', '#e74c3c', '#9b59b6', '#f39c12'],
                        borderColor: ['#2980b9', '#27ae60', '#c0392b', '#8e44ad', '#d68910'],
                        borderWidth: 1,
                        borderRadius: 6
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
        }

        // Initialize Gender Distribution Chart
        const genderCtx = document.getElementById('genderChart');
        if (genderCtx) {
            new Chart(genderCtx.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Female', 'Male'],
                    datasets: [{
                        data: [
                                        {{ $genderCounts['F'] ?? 0 }},
                                        {{ $genderCounts['M'] ?? 0 }},
                        ],
                        backgroundColor: ['#e74c3c', '#3498db'],
                        borderColor: '#fff',
                        borderWidth: 3
                    }]
                },
                options: chartOptions
            });
        }

        // Report Functions
        function exportReport() {
            alert('Export functionality will be implemented with backend');
        }

        function printReport() {
            window.print();
        }
    </script>
@endpush