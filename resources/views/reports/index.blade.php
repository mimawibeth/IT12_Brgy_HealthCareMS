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

        <!-- Summary Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">152</div>
                    <div class="stat-label">Total Patients</div>
                    <div class="stat-change positive">+12 from last month</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                    <i class="bi bi-heart-pulse"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">48</div>
                    <div class="stat-label">Prenatal Consultations</div>
                    <div class="stat-change positive">+5 from last month</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">89</div>
                    <div class="stat-label">Immunizations Given</div>
                    <div class="stat-change positive">+15 from last month</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">34</div>
                    <div class="stat-label">Family Planning Clients</div>
                    <div class="stat-change">No change</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-grid">
            <!-- Patient Registration Trend -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Patient Registration Trend</h3>
                    <span class="chart-period">Last 6 Months</span>
                </div>
                <div class="chart-placeholder">
                    <i class="bi bi-bar-chart-line"></i>
                    <p>Chart will be displayed here</p>
                    <small>Shows monthly patient registration trends</small>
                </div>
            </div>

            <!-- Service Distribution -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Service Distribution</h3>
                    <span class="chart-period">November 2025</span>
                </div>
                <div class="chart-placeholder">
                    <i class="bi bi-pie-chart"></i>
                    <p>Chart will be displayed here</p>
                    <small>Shows distribution of health services</small>
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