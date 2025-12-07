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

        <!-- Today's Operations Overview -->
        <div class="stats-grid">
            <!-- New Patients This Week -->
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-blue">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">ITR Records This Week</h3>
                    <p class="stat-number">{{ number_format($newITRThisWeek ?? 0) }}</p>
                    <span class="stat-trend">Total: {{ number_format($totalITRRecords ?? 0) }}</span>
                </div>
            </div>

            <!-- Health Programs This Month -->
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-green">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">Health Programs This Month</h3>
                    <p class="stat-number">{{ number_format($totalProgramsThisMonth ?? 0) }}</p>
                    <span class="stat-trend">Total: {{ number_format($totalProgramsAll ?? 0) }}</span>
                </div>
            </div>

            <!-- Medicines in Stock -->
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-purple">
                    <i class="bi bi-capsule-pill"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">Medicines</h3>
                    <p class="stat-number">{{ $medicinesInStock ?? 0 }}</p>
                    <span class="stat-trend">In Stock</span>
                </div>
            </div>

            <!-- Medical Supplies in Stock -->
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-orange">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">Medical Supplies</h3>
                    <p class="stat-number">{{ $medicalSuppliesInStock ?? 0 }}</p>
                    <span class="stat-trend">In Stock</span>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="section-header-inline">
                <h2>Operational Analytics</h2>
                <p>Visual insights into daily operations and health programs</p>
            </div>

            <div class="charts-grid">
                <!-- Patient Registration Trend -->
                <div class="chart-card" data-chart="patient">
                    <div class="chart-header">
                        <h3><i class="bi bi-graph-up"></i> Patient Registration</h3>
                        <span class="chart-badge">Last 6 Months</span>
                    </div>
                    <div class="chart-placeholder">
                        <canvas id="patientTrendChart"></canvas>
                    </div>
                </div>

                <!-- Health Programs Distribution -->
                <div class="chart-card" data-chart="program">
                    <div class="chart-header">
                        <h3><i class="bi bi-pie-chart"></i> Program Distribution</h3>
                        <span class="chart-badge">Total Records</span>
                    </div>
                    <div class="chart-placeholder">
                        <canvas id="programDistChart"></canvas>
                    </div>
                </div>

                <!-- Medicine Dispensing This Week -->
                <div class="chart-card" data-chart="medicine">
                    <div class="chart-header">
                        <h3><i class="bi bi-bar-chart-line"></i> Medicine Dispensing</h3>
                        <span class="chart-badge">This Week</span>
                    </div>
                    <div class="chart-placeholder">
                        <canvas id="medicineWeekChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Detail Modal -->
        <div id="chartModal" class="chart-modal">
            <div class="chart-modal-content">
                <div class="chart-modal-header">
                    <h3 id="modalTitle"></h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="chart-modal-body">
                    <div class="chart-modal-canvas">
                        <canvas id="modalChart"></canvas>
                    </div>
                    <div id="modalStats" class="modal-stats"></div>
                </div>
            </div>
        </div>

        <!-- Recent Activities & Events Section -->
        <div class="dashboard-grid">
            <!-- Recent Activities -->
            <div class="recent-activities">
                <div class="section-header-inline">
                    <h2>Recent Activities</h2>
                    <p>Latest system operations</p>
                </div>

                <div class="activity-list">
                    @forelse($recentActivities as $log)
                        @php
                            $icon = 'bi-activity';
                            $iconColor = '#3498db';
                            switch ($log->action) {
                                case 'login': $icon = 'bi-person-check'; $iconColor = '#2ecc71'; break;
                                case 'logout': $icon = 'bi-box-arrow-right'; $iconColor = '#95a5a6'; break;
                                case 'create': $icon = 'bi-plus-circle'; $iconColor = '#3498db'; break;
                                case 'update': $icon = 'bi-pencil-square'; $iconColor = '#f39c12'; break;
                                case 'delete': $icon = 'bi-trash'; $iconColor = '#e74c3c'; break;
                                case 'dispense': $icon = 'bi-capsule'; $iconColor = '#9b59b6'; break;
                                case 'export': $icon = 'bi-file-earmark-arrow-down'; $iconColor = '#16a085'; break;
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

            <!-- Upcoming Events -->
            <div class="monthly-stats">
                <div class="section-header-inline">
                    <h2>Upcoming Events</h2>
                    <p>Events scheduled this month</p>
                </div>

                @if(count($eventsThisMonth ?? []) > 0)
                    <div class="activity-list">
                        @foreach($eventsThisMonth as $event)
                            <div class="activity-item">
                                <span class="activity-icon icon-purple">
                                    <i class="bi bi-calendar-event"></i>
                                </span>
                                <div class="activity-details">
                                    <p class="activity-text">{{ $event->title ?? 'Event' }}</p>
                                    <span class="activity-time">{{ $event->start_date?->format('M d, Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="no-data-message">No events scheduled this month</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Patient Registration Trend
        const patientTrendLabels = @json($patientTrendLabels ?? []);
        const patientTrendData = @json($patientTrendData ?? []);

        const patientChart = document.getElementById('patientTrendChart');
        if (patientChart) {
            new Chart(patientChart.getContext('2d'), {
                type: 'line',
                data: {
                    labels: patientTrendLabels,
                    datasets: [{
                        label: 'New Patients',
                        data: patientTrendData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
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

        // Program Distribution Chart
        const programDistribution = @json($programDistribution ?? []);
        const programChart = document.getElementById('programDistChart');
        if (programChart) {
            new Chart(programChart.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Prenatal Care', 'Family Planning', 'Immunization (NIP)'],
                    datasets: [{
                        data: [
                            programDistribution.prenatal || 0,
                            programDistribution.fp || 0,
                            programDistribution.nip || 0
                        ],
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

        // Medicine Dispensing This Week
        const medicineWeekLabels = @json($medicineWeekLabels ?? []);
        const medicineWeekData = @json($medicineWeekData ?? []);

        const medicineChart = document.getElementById('medicineWeekChart');
        if (medicineChart) {
            new Chart(medicineChart.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: medicineWeekLabels,
                    datasets: [{
                        label: 'Units Dispensed',
                        data: medicineWeekData,
                        backgroundColor: '#f59e0b',
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

        // Monthly Health Program Records Trend
        const recordsLabels = @json($recordsLabels ?? []);
        const prenatalRecords = @json($prenatalRecords ?? []);
        const fpRecords = @json($fpRecords ?? []);
        const nipRecords = @json($nipRecords ?? []);

        const recordsChart = document.getElementById('recordsTrendChart');
        if (recordsChart) {
            new Chart(recordsChart.getContext('2d'), {
                type: 'line',
                data: {
                    labels: recordsLabels,
                    datasets: [
                        {
                            label: 'Prenatal',
                            data: prenatalRecords,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: false,
                            borderWidth: 2
                        },
                        {
                            label: 'Family Planning',
                            data: fpRecords,
                            borderColor: '#8b5cf6',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            tension: 0.4,
                            fill: false,
                            borderWidth: 2
                        },
                        {
                            label: 'Immunization',
                            data: nipRecords,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: false,
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                padding: 15,
                                font: { size: 12 }
                            }
                        },
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

    <script>
        // Chart Modal Functionality
        let modalChartInstance = null;
        const modal = document.getElementById('chartModal');
        const modalClose = document.querySelector('.modal-close');
        const chartCards = document.querySelectorAll('.chart-card');

        // Close modal when clicking X or outside
        modalClose.onclick = () => modal.style.display = 'none';
        window.onclick = (e) => { if (e.target === modal) modal.style.display = 'none'; };

        // Add click handlers to chart cards
        chartCards.forEach(card => {
            card.addEventListener('click', function() {
                const chartType = this.getAttribute('data-chart');
                showChartModal(chartType);
            });
        });

        function showChartModal(chartType) {
            const modalTitle = document.getElementById('modalTitle');
            const modalStats = document.getElementById('modalStats');
            const modalCanvas = document.getElementById('modalChart');

            // Destroy previous chart if exists
            if (modalChartInstance) {
                modalChartInstance.destroy();
            }

            modal.style.display = 'flex';

            switch(chartType) {
                case 'patient':
                    modalTitle.textContent = 'Patient Registration Trend - Detailed Statistics';
                    const totalPatients = patientTrendData.reduce((a, b) => a + b, 0);
                    const avgPatients = (totalPatients / patientTrendData.length).toFixed(1);
                    const maxPatients = Math.max(...patientTrendData);
                    
                    modalStats.innerHTML = `
                        <div class="stat-item"><strong>Total Registered:</strong> ${totalPatients}</div>
                        <div class="stat-item"><strong>Average per Month:</strong> ${avgPatients}</div>
                        <div class="stat-item"><strong>Peak Month:</strong> ${maxPatients} patients</div>
                    `;

                    modalChartInstance = new Chart(modalCanvas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: patientTrendLabels,
                            datasets: [{
                                label: 'New Patients',
                                data: patientTrendData,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: true, position: 'top' },
                                tooltip: { backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12 }
                            },
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                    break;

                case 'program':
                    modalTitle.textContent = 'Health Programs Distribution - Detailed Statistics';
                    const prenatal = programDistribution.prenatal || 0;
                    const fp = programDistribution.fp || 0;
                    const nip = programDistribution.nip || 0;
                    const totalPrograms = prenatal + fp + nip;

                    modalStats.innerHTML = `
                        <div class="stat-item"><strong>Total Records:</strong> ${totalPrograms}</div>
                        <div class="stat-item"><strong>Prenatal Care:</strong> ${prenatal} (${((prenatal/totalPrograms)*100).toFixed(1)}%)</div>
                        <div class="stat-item"><strong>Family Planning:</strong> ${fp} (${((fp/totalPrograms)*100).toFixed(1)}%)</div>
                        <div class="stat-item"><strong>Immunization:</strong> ${nip} (${((nip/totalPrograms)*100).toFixed(1)}%)</div>
                    `;

                    modalChartInstance = new Chart(modalCanvas.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Prenatal Care', 'Family Planning', 'Immunization (NIP)'],
                            datasets: [{
                                data: [prenatal, fp, nip],
                                backgroundColor: ['#ef4444', '#8b5cf6', '#10b981'],
                                borderWidth: 3,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { padding: 15, font: { size: 14 } } }
                            }
                        }
                    });
                    break;

                case 'medicine':
                    modalTitle.textContent = 'Medicine Dispensing This Week - Detailed Statistics';
                    const totalDispensed = medicineWeekData.reduce((a, b) => a + b, 0);
                    const avgDispensed = (totalDispensed / medicineWeekData.length).toFixed(1);
                    const maxDispensed = Math.max(...medicineWeekData);

                    modalStats.innerHTML = `
                        <div class="stat-item"><strong>Total Dispensed:</strong> ${totalDispensed} units</div>
                        <div class="stat-item"><strong>Daily Average:</strong> ${avgDispensed} units</div>
                        <div class="stat-item"><strong>Peak Day:</strong> ${maxDispensed} units</div>
                    `;

                    modalChartInstance = new Chart(modalCanvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: medicineWeekLabels,
                            datasets: [{
                                label: 'Units Dispensed',
                                data: medicineWeekData,
                                backgroundColor: '#f59e0b',
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: true, position: 'top' },
                                tooltip: { backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12 }
                            },
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                    break;
            }
        }
    </script>
@endpush