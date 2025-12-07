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
                    <button class="btn btn-primary" type="button" onclick="exportPDF()">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </button>
                    <button class="btn btn-secondary" type="button" onclick="printReport()">
                        <i class="bi bi-printer"></i> Print Report
                    </button>
                </div>
            </form>
        </div>

        <div class="charts-grid">
            <!-- Monthly Services Trend -->
            <div class="chart-card" data-chart-id="servicesChart" data-chart-title="Monthly Services Trend"
                data-chart-icon="bi-graph-up" data-chart-period="Last 6 Months"
                data-chart-description="Overall service delivery trends across all programs">
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
            <div class="chart-card" data-chart-id="programDistributionChart" data-chart-title="Health Program Distribution"
                data-chart-icon="bi-pie-chart" data-chart-period="{{ $selectedMonthLabel ?? 'Current Month' }}"
                data-chart-description="Service breakdown by health programs">
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
            <div class="chart-card" data-chart-id="demographicsChart" data-chart-title="Patient Demographics"
                data-chart-icon="bi-people" data-chart-period="Age Group Distribution"
                data-chart-description="Patient breakdown by age groups">
                <div class="chart-header">
                    <h3><i class="bi bi-people"></i> Patient Demographics</h3>
                    <span class="chart-period">Age Group Distribution</span>
                </div>
                <div class="chart-placeholder">
                    <canvas id="demographicsChart"></canvas>
                    <p>Patient breakdown by age groups</p>
                </div>
            </div>

            <!-- Gender Distribution -->
            <div class="chart-card" data-chart-id="genderChart" data-chart-title="Gender Distribution"
                data-chart-icon="bi-gender-ambiguous" data-chart-period="Patient Statistics"
                data-chart-description="Male vs Female patient breakdown">
                <div class="chart-header">
                    <h3><i class="bi bi-gender-ambiguous"></i> Gender Distribution</h3>
                    <span class="chart-period">Patient Statistics</span>
                </div>
                <div class="chart-placeholder">
                    <canvas id="genderChart"></canvas>
                    <p>Male vs Female patient breakdown</p>
                </div>
            </div>

            <!-- Medicine Dispensing Trend -->
            <div class="chart-card" data-chart-id="medicineDispenseChart" data-chart-title="Medicine Dispensing Trend"
                data-chart-icon="bi-capsule" data-chart-period="Last 6 Months"
                data-chart-description="Monthly medicine dispensing volume">
                <div class="chart-header">
                    <h3><i class="bi bi-capsule"></i> Medicine Dispensing Trend</h3>
                    <span class="chart-period">Last 6 Months</span>
                </div>
                <div class="chart-placeholder">
                    <canvas id="medicineDispenseChart"></canvas>
                    <p>Monthly medicine dispensing volume</p>
                </div>
            </div>

            <!-- Top Dispensed Medicines -->
            <div class="chart-card" data-chart-id="topMedicinesChart" data-chart-title="Top Dispensed Medicines"
                data-chart-icon="bi-bar-chart-fill" data-chart-period="{{ $selectedMonthLabel ?? 'Current Month' }}"
                data-chart-description="Most frequently dispensed medicines">
                <div class="chart-header">
                    <h3><i class="bi bi-bar-chart-fill"></i> Top Dispensed Medicines</h3>
                    <span class="chart-period">{{ $selectedMonthLabel ?? 'Current Month' }}</span>
                </div>
                <div class="chart-placeholder">
                    <canvas id="topMedicinesChart"></canvas>
                    <p>Most frequently dispensed medicines</p>
                </div>
            </div>
        </div>

        <!-- Chart Zoom Modal -->
        <div id="chartModal" class="chart-modal">
            <div class="chart-modal-overlay"></div>
            <div class="chart-modal-content">
                <div class="chart-modal-header">
                    <h3 id="modalChartTitle"></h3>
                    <span id="modalChartPeriod" class="chart-period"></span>
                    <button class="chart-modal-close" id="closeModal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="chart-modal-body">
                    <canvas id="modalChartCanvas"></canvas>
                    <p id="modalChartDescription"></p>
                </div>
            </div>
        </div>

        <!-- Health Programs Monthly Summary -->
        <div class="report-section">
            <h3 class="section-title">Health Programs Summary - {{ $selectedMonthLabel ?? 'Current Month' }}</h3>
            <div class="table-container">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Program/Service</th>
                            <th>Records This Month</th>
                            <th>Total Records (All Time)</th>
                            <th>Percentage of Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Prenatal Care</strong></td>
                            <td>{{ number_format($prenatalCount ?? 0) }}</td>
                            <td>{{ number_format($totalPrenatalRecords ?? 0) }}</td>
                            <td>{{ $totalHealthPrograms > 0 ? number_format(($totalPrenatalRecords / $totalHealthPrograms) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td><strong>Family Planning</strong></td>
                            <td>{{ number_format($fpCount ?? 0) }}</td>
                            <td>{{ number_format($totalFPRecords ?? 0) }}</td>
                            <td>{{ $totalHealthPrograms > 0 ? number_format(($totalFPRecords / $totalHealthPrograms) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td><strong>Immunization (NIP)</strong></td>
                            <td>{{ number_format($nipCount ?? 0) }}</td>
                            <td>{{ number_format($totalNIPRecords ?? 0) }}</td>
                            <td>{{ $totalHealthPrograms > 0 ? number_format(($totalNIPRecords / $totalHealthPrograms) * 100, 1) : 0 }}%</td>
                        </tr>
                        @php
                            $totalCasesThisMonth = ($prenatalCount ?? 0) + ($fpCount ?? 0) + ($nipCount ?? 0);
                        @endphp
                        <tr class="table-total">
                            <td><strong>TOTAL</strong></td>
                            <td><strong>{{ number_format($totalCasesThisMonth) }}</strong></td>
                            <td><strong>{{ number_format($totalHealthPrograms ?? 0) }}</strong></td>
                            <td><strong>100%</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Age Distribution Report -->
        <div class="report-section">
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

        <!-- Medicine Dispensing Summary -->
        <div class="report-section">
            <h3 class="section-title">Medicine Dispensing Summary - {{ $selectedMonthLabel ?? 'Current Month' }}</h3>
            <div class="table-container">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Medicine Name</th>
                            <th>Quantity Dispensed</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalQuantityDispensed = array_sum($topMedicineQuantities ?? []);
                        @endphp
                        @foreach(($topMedicineNames ?? []) as $i => $medicineName)
                            @php
                                $quantity = $topMedicineQuantities[$i] ?? 0;
                                $percent = $totalQuantityDispensed > 0 ? round(($quantity / $totalQuantityDispensed) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td><strong>{{ $medicineName }}</strong></td>
                                <td>{{ number_format($quantity) }}</td>
                                <td>{{ $percent }}%</td>
                            </tr>
                        @endforeach
                        @if(empty($topMedicineNames))
                            <tr>
                                <td colspan="3" style="text-align: center;">No medicine dispensing records for this month</td>
                            </tr>
                        @else
                            <tr class="table-total">
                                <td><strong>TOTAL</strong></td>
                                <td><strong>{{ number_format($totalDispensesThisMonth ?? 0) }}</strong></td>
                                <td><strong>—</strong></td>
                            </tr>
                        @endif
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
        const programCtx = document.getElementById('programDistributionChart');
        if (programCtx) {
            new Chart(programCtx.getContext('2d'), {
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
        }

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

        // Initialize Medicine Dispensing Trend Chart
        const medicineDispenseSeries = @json($medicineDispenseSeries ?? []);
        const medicineDispenseCtx = document.getElementById('medicineDispenseChart');
        if (medicineDispenseCtx) {
            new Chart(medicineDispenseCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Medicines Dispensed',
                        data: medicineDispenseSeries,
                        borderColor: '#9b59b6',
                        backgroundColor: 'rgba(155, 89, 182, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    ...chartOptions,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // Initialize Top Dispensed Medicines Chart
        const topMedicineNames = @json($topMedicineNames ?? []);
        const topMedicineQuantities = @json($topMedicineQuantities ?? []);
        const topMedicinesCtx = document.getElementById('topMedicinesChart');
        if (topMedicinesCtx && topMedicineNames.length > 0) {
            new Chart(topMedicinesCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: topMedicineNames,
                    datasets: [{
                        label: 'Quantity Dispensed',
                        data: topMedicineQuantities,
                        backgroundColor: ['#3498db', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6'],
                        borderColor: ['#2980b9', '#27ae60', '#d68910', '#c0392b', '#8e44ad'],
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

        // Store chart instances
        const chartInstances = {};

        // Function to get chart instance by canvas ID
        function getChartInstance(canvasId) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return null;

            // Get Chart.js instance from canvas
            const chart = Chart.getChart(canvas);
            return chart;
        }

        // Function to clone chart to modal
        function cloneChartToModal(originalChart, modalCanvas) {
            if (!originalChart) return null;

            // Get the original chart configuration
            const config = originalChart.config;

            // Create new config for modal
            const modalConfig = {
                type: config.type,
                data: JSON.parse(JSON.stringify(config.data)), // Deep clone data
                options: {
                    ...config.options,
                    maintainAspectRatio: true,
                    responsive: true,
                    plugins: {
                        ...config.options.plugins,
                        legend: {
                            ...config.options.plugins?.legend,
                            display: true
                        }
                    }
                }
            };

            // Destroy existing chart if any
            const existingChart = Chart.getChart(modalCanvas);
            if (existingChart) {
                existingChart.destroy();
            }

            // Create new chart in modal
            return new Chart(modalCanvas.getContext('2d'), modalConfig);
        }

        // Modal functionality
        const modal = document.getElementById('chartModal');
        const closeModalBtn = document.getElementById('closeModal');
        const modalTitle = document.getElementById('modalChartTitle');
        const modalPeriod = document.getElementById('modalChartPeriod');
        const modalDescription = document.getElementById('modalChartDescription');
        const modalCanvas = document.getElementById('modalChartCanvas');

        // Open modal function
        function openChartModal(chartCard) {
            try {
                const chartId = chartCard.getAttribute('data-chart-id');
                const chartTitle = chartCard.getAttribute('data-chart-title');
                const chartIcon = chartCard.getAttribute('data-chart-icon');
                const chartPeriod = chartCard.getAttribute('data-chart-period');
                const chartDescription = chartCard.getAttribute('data-chart-description');

                console.log('Opening modal for chart:', chartId);

                // Get original chart instance
                const originalChart = getChartInstance(chartId);

                if (!originalChart) {
                    console.error('Chart not found:', chartId);
                    alert('Chart not yet loaded. Please wait a moment and try again.');
                    return;
                }

                // Check if modal elements exist
                if (!modal || !modalTitle || !modalPeriod || !modalDescription || !modalCanvas) {
                    console.error('Modal elements not found');
                    return;
                }

                // Set modal content
                modalTitle.innerHTML = `<i class="bi ${chartIcon}"></i> ${chartTitle}`;
                modalPeriod.textContent = chartPeriod;
                modalDescription.textContent = chartDescription;

                // Show modal
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';

                // Clone chart to modal after a short delay to ensure modal is visible
                setTimeout(() => {
                    try {
                        cloneChartToModal(originalChart, modalCanvas);
                    } catch (error) {
                        console.error('Error cloning chart:', error);
                    }
                }, 200);
            } catch (error) {
                console.error('Error opening modal:', error);
            }
        }

        // Close modal function
        function closeChartModal() {
            modal.classList.add('closing');
            document.body.style.overflow = '';

            setTimeout(() => {
                modal.classList.remove('active', 'closing');

                // Destroy modal chart
                const modalChart = Chart.getChart(modalCanvas);
                if (modalChart) {
                    modalChart.destroy();
                }
            }, 300);
        }

        // Initialize modal functionality after DOM is ready and charts are loaded
        function initializeModalFunctionality() {
            console.log('Initializing modal functionality...');

            // Check if modal elements exist
            if (!modal) {
                console.error('Modal element not found!');
                return;
            }

            // Wait a bit to ensure all charts are initialized
            setTimeout(() => {
                // Add click event listeners to chart cards
                const chartCards = document.querySelectorAll('.chart-card');
                console.log('Found chart cards:', chartCards.length);

                if (chartCards.length === 0) {
                    console.error('No chart cards found!');
                    return;
                }

                chartCards.forEach((card, index) => {
                    card.style.cursor = 'pointer';
                    const chartId = card.getAttribute('data-chart-id');
                    console.log(`Setting up click handler for card ${index + 1}: ${chartId}`);

                    card.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Chart card clicked:', chartId);
                        openChartModal(this);
                    });
                });

                // Close modal events
                if (closeModalBtn) {
                    closeModalBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        closeChartModal();
                    });
                } else {
                    console.error('Close modal button not found!');
                }

                const overlay = modal.querySelector('.chart-modal-overlay');
                if (overlay) {
                    overlay.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        closeChartModal();
                    });
                } else {
                    console.error('Modal overlay not found!');
                }

                // Close modal on Escape key
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && modal.classList.contains('active')) {
                        closeChartModal();
                    }
                });

                console.log('Modal functionality initialized successfully!');
            }, 1000); // Increased delay to ensure charts are fully initialized
        }

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(initializeModalFunctionality, 100);
            });
        } else {
            setTimeout(initializeModalFunctionality, 100);
        }

        // Report Functions
        function exportPDF() {
            // Get current filter values
            const form = document.getElementById('report-filters-form');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // Redirect to PDF export route with current filters
            window.location.href = '{{ route('reports.export.pdf') }}?' + params.toString();
        }

        function printReport() {
            // Get current filter values
            const form = document.getElementById('report-filters-form');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // Open print view in new window
            const printWindow = window.open('{{ route('reports.print') }}?' + params.toString(), '_blank');
            
            // Wait for the page to load, then trigger print
            if (printWindow) {
                printWindow.onload = function() {
                    setTimeout(function() {
                        printWindow.print();
                    }, 500);
                };
            }
        }
    </script>
@endpush