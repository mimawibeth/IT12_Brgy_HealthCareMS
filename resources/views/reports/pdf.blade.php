<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Health Report - {{ $selectedMonthLabel ?? 'Report' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .header h2 {
            font-size: 14px;
            color: #34495e;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 10px;
            color: #7f8c8d;
        }

        .report-info {
            background: #ecf0f1;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .report-info table {
            width: 100%;
        }

        .report-info td {
            padding: 3px 10px;
            font-size: 10px;
        }

        .report-info td:first-child {
            font-weight: bold;
            width: 30%;
        }

        h3 {
            font-size: 13px;
            color: #2c3e50;
            margin: 20px 0 10px 0;
            padding: 8px 12px;
            background: #34495e;
            color: white;
            border-radius: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background: #95a5a6;
            color: white;
        }

        table thead th {
            padding: 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #7f8c8d;
        }

        table tbody td {
            padding: 6px 8px;
            border: 1px solid #bdc3c7;
            font-size: 10px;
        }

        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        table tbody tr:hover {
            background: #e8f4f8;
        }

        .table-total {
            background: #34495e !important;
            color: white !important;
            font-weight: bold;
        }

        .table-total td {
            border-color: #2c3e50 !important;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-info {
            background: #3498db;
            color: white;
        }

        .badge-warning {
            background: #f39c12;
            color: white;
        }

        .badge-success {
            background: #27ae60;
            color: white;
        }

        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-row {
            display: table-row;
        }

        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 10px;
            border: 2px solid #34495e;
            text-align: center;
            background: #ecf0f1;
        }

        .summary-item .label {
            font-size: 9px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .summary-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }

        .summary-item .sub-value {
            font-size: 8px;
            color: #95a5a6;
            margin-top: 3px;
        }

        .footer {
            position: fixed;
            bottom: 15px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 9px;
            color: #7f8c8d;
            border-top: 1px solid #bdc3c7;
            padding-top: 10px;
        }

        .page-break {
            page-break-after: always;
        }

        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            padding: 10px;
        }

        .signature-line {
            border-top: 1px solid #2c3e50;
            margin-top: 50px;
            padding-top: 5px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
        }

        .signature-label {
            font-size: 9px;
            color: #7f8c8d;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>BARANGAY STO. NIÑO HEALTH CENTER</h1>
        <h2>Monthly Health Services Report</h2>
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <!-- Report Information -->
    <div class="report-info">
        <table>
            <tr>
                <td>Report Period:</td>
                <td><strong>{{ $selectedMonthLabel ?? 'N/A' }}</strong></td>
                <td>Total System Users:</td>
                <td><strong>{{ number_format($totalUsers ?? 0) }}</strong></td>
            </tr>
            <tr>
                <td>Total Patients:</td>
                <td><strong>{{ number_format($totalPatients ?? 0) }}</strong></td>
                <td>New Patients This Month:</td>
                <td><strong>{{ number_format($newPatientsThisMonth ?? 0) }}</strong></td>
            </tr>
            <tr>
                <td>Total Health Programs:</td>
                <td><strong>{{ number_format($totalHealthPrograms ?? 0) }}</strong></td>
                <td>Total Medicines:</td>
                <td><strong>{{ number_format($totalMedicines ?? 0) }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Health Programs Summary -->
    <h3>I. HEALTH PROGRAMS SUMMARY</h3>
    <table>
        <thead>
            <tr>
                <th>Program/Service</th>
                <th class="text-right">This Month</th>
                <th class="text-right">All Time Total</th>
                <th class="text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Prenatal Care</td>
                <td class="text-right">{{ number_format($prenatalCount ?? 0) }}</td>
                <td class="text-right">{{ number_format($totalPrenatalRecords ?? 0) }}</td>
                <td class="text-right">{{ $totalHealthPrograms > 0 ? number_format(($totalPrenatalRecords / $totalHealthPrograms) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Family Planning</td>
                <td class="text-right">{{ number_format($fpCount ?? 0) }}</td>
                <td class="text-right">{{ number_format($totalFPRecords ?? 0) }}</td>
                <td class="text-right">{{ $totalHealthPrograms > 0 ? number_format(($totalFPRecords / $totalHealthPrograms) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Immunization (NIP)</td>
                <td class="text-right">{{ number_format($nipCount ?? 0) }}</td>
                <td class="text-right">{{ number_format($totalNIPRecords ?? 0) }}</td>
                <td class="text-right">{{ $totalHealthPrograms > 0 ? number_format(($totalNIPRecords / $totalHealthPrograms) * 100, 1) : 0 }}%</td>
            </tr>
            <tr class="table-total">
                <td>TOTAL</td>
                <td class="text-right">{{ number_format(($prenatalCount ?? 0) + ($fpCount ?? 0) + ($nipCount ?? 0)) }}</td>
                <td class="text-right">{{ number_format($totalHealthPrograms ?? 0) }}</td>
                <td class="text-right">100%</td>
            </tr>
        </tbody>
    </table>

    <!-- Patient Demographics -->
    <h3>II. PATIENT DEMOGRAPHICS BY AGE GROUP</h3>
    <table>
        <thead>
            <tr>
                <th>Age Group</th>
                <th class="text-right">Male</th>
                <th class="text-right">Female</th>
                <th class="text-right">Total</th>
                <th class="text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandMale = array_sum($ageMale ?? []);
                $grandFemale = array_sum($ageFemale ?? []);
                $grandTotal = $grandMale + $grandFemale;
            @endphp
            @foreach(($ageLabels ?? []) as $i => $label)
                @php
                    $male = $ageMale[$i] ?? 0;
                    $female = $ageFemale[$i] ?? 0;
                    $total = $male + $female;
                    $percent = $grandTotal > 0 ? round(($total / $grandTotal) * 100, 1) : 0;
                @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td class="text-right">{{ number_format($male) }}</td>
                    <td class="text-right">{{ number_format($female) }}</td>
                    <td class="text-right">{{ number_format($total) }}</td>
                    <td class="text-right">{{ $percent }}%</td>
                </tr>
            @endforeach
            <tr class="table-total">
                <td>TOTAL</td>
                <td class="text-right">{{ number_format($grandMale) }}</td>
                <td class="text-right">{{ number_format($grandFemale) }}</td>
                <td class="text-right">{{ number_format($grandTotal) }}</td>
                <td class="text-right">100%</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Medicine Dispensing Summary -->
    <h3>III. MEDICINE DISPENSING SUMMARY - {{ $selectedMonthLabel ?? 'Current Month' }}</h3>
    <table>
        <thead>
            <tr>
                <th>Medicine Name</th>
                <th class="text-right">Quantity Dispensed</th>
                <th class="text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalQuantityDispensed = array_sum($topMedicineQuantities ?? []);
            @endphp
            @if(!empty($topMedicineNames))
                @foreach($topMedicineNames as $i => $medicineName)
                    @php
                        $quantity = $topMedicineQuantities[$i] ?? 0;
                        $percent = $totalQuantityDispensed > 0 ? round(($quantity / $totalQuantityDispensed) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td>{{ $medicineName }}</td>
                        <td class="text-right">{{ number_format($quantity) }}</td>
                        <td class="text-right">{{ $percent }}%</td>
                    </tr>
                @endforeach
                <tr class="table-total">
                    <td>TOTAL DISPENSED THIS MONTH</td>
                    <td class="text-right">{{ number_format($totalDispensesThisMonth ?? 0) }}</td>
                    <td class="text-right">—</td>
                </tr>
            @else
                <tr>
                    <td colspan="3" class="text-center">No medicine dispensing records for this month</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Summary Statistics -->
    <h3>IV. MONTHLY SUMMARY STATISTICS</h3>
    <table>
        <thead>
            <tr>
                <th>Metric</th>
                <th class="text-right">Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Health Program Records This Month</td>
                <td class="text-right">{{ number_format(($prenatalCount ?? 0) + ($fpCount ?? 0) + ($nipCount ?? 0)) }}</td>
            </tr>
            <tr>
                <td>Unique Medicines Dispensed</td>
                <td class="text-right">{{ number_format($uniqueMedicinesDispensed ?? 0) }}</td>
            </tr>
            <tr>
                <td>Total Medicines Dispensed (Quantity)</td>
                <td class="text-right">{{ number_format($totalDispensesThisMonth ?? 0) }}</td>
            </tr>
            <tr>
                <td>Male Patients</td>
                <td class="text-right">{{ number_format($genderCounts['M'] ?? 0) }}</td>
            </tr>
            <tr>
                <td>Female Patients</td>
                <td class="text-right">{{ number_format($genderCounts['F'] ?? 0) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                _______________________________
            </div>
            <div class="signature-label">Prepared By</div>
            <div style="text-align: center; margin-top: 5px; font-size: 9px;">
                (Barangay Health Worker)
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                _______________________________
            </div>
            <div class="signature-label">Reviewed By</div>
            <div style="text-align: center; margin-top: 5px; font-size: 9px;">
                (Barangay Health Center Administrator)
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is a system-generated report from Brgy. Sto. Niño Healthcare Management System</p>
        <p>Page 1 of 1 | Generated: {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>
