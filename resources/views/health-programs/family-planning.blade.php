@extends('layouts.app')

@section('title', 'Family Planning')
@section('page-title', 'Family Planning Records')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <div
            style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <form method="GET" id="fpFilterForm" class="filters"
                style="flex: 1; display: flex; gap: 12px; align-items: center;">
                <input type="text" name="search" id="fpSearch" placeholder="Search client name or FP number"
                    class="search-input" style="flex: 1; min-width: 300px;">

                <select name="client_type" id="clientTypeFilter" class="filter-select">
                    <option value="">Client Type</option>
                    <option value="new">New Acceptor</option>
                    <option value="current">Current User</option>
                </select>

                <select name="reason" id="reasonFilter" class="filter-select">
                    <option value="">Reason for FP</option>
                    <option value="spacing">Spacing</option>
                    <option value="limiting">Limiting</option>
                    <option value="medical">Medical Condition</option>
                </select>

                <input type="date" name="date" id="fpDateFilter" class="filter-select" />

                <button type="button" id="clearFpFilters" class="btn btn-secondary"
                    style="padding: 10px 15px; font-size: 14px;">
                    <i class="bi bi-x-circle"></i> Clear
                </button>

                <button type="button" class="btn btn-primary" id="openFpForm"
                    style="padding: 10px 15px; font-size: 14px; white-space: nowrap;">
                    <i class="bi bi-plus-circle"></i> Add New Record
                </button>
            </form>

            <button class="btn btn-secondary" id="backToFpList" style="display:none; padding: 10px 15px; font-size: 14px;">
                <i class="bi bi-arrow-left"></i> Back to Records
            </button>
        </div>

        <div class="table-container" id="fpTablePanel">

            <table class="data-table">
                <thead>
                    <tr>
                        <th>FP #</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>Last Update</th>
                        <!-- <th>Status</th> -->
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records ?? [] as $record)
                        <tr>
                            <td>{{ $record->record_no ?? 'FP-' . str_pad($record->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $record->client_name }}</td>
                            <td>{{ $record->client_type ? ucfirst(str_replace('-', ' ', $record->client_type)) : '-' }}</td>
                            <td>
                                @if (is_array($record->reason) && count($record->reason))
                                    {{ implode(', ', $record->reason) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ optional($record->updated_at)->format('Y-m-d') }}</td>
                            <!-- <td style="color: #48bb78;"><i class="bi bi-check-circle"></i> Recorded</td> -->
                            <td>
                                <a href="javascript:void(0)" class="btn-action btn-view view-fp" data-id="{{ $record->id }}"
                                    data-record="{{ $record->record_no ?? 'FP-' . str_pad($record->id, 3, '0', STR_PAD_LEFT) }}">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('health-programs.family-planning-edit', $record) }}"
                                    class="btn-action btn-edit">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                @if(auth()->user()->role === 'super_admin')
                                    <a href="javascript:void(0)" class="btn-action btn-delete"
                                        onclick="openDeleteModal({{ $record->id }}, '{{ $record->client_name }}')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center;">No family planning records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php
            $showPagination = !empty($records) && method_exists($records, 'hasPages') && $records->hasPages();
        @endphp
        @if($showPagination)
            <div class="pagination" id="fpPagination">
                @if($records->onFirstPage())
                    <button class="btn-page" disabled>« Previous</button>
                @else
                    <a class="btn-page" href="{{ $records->appends(request()->query())->previousPageUrl() }}">« Previous</a>
                @endif

                @php
                    $start = max(1, $records->currentPage() - 2);
                    $end = min($records->lastPage(), $records->currentPage() + 2);
                @endphp

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page === $records->currentPage())
                        <span class="btn-page active">{{ $page }}</span>
                    @else
                        <a class="btn-page" href="{{ $records->appends(request()->query())->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                <span class="page-info">
                    Page {{ $records->currentPage() }} of {{ $records->lastPage() }} ({{ $records->total() }} total records)
                </span>

                @if($records->hasMorePages())
                    <a class="btn-page" href="{{ $records->appends(request()->query())->nextPageUrl() }}">Next »</a>
                @else
                    <button class="btn-page" disabled>Next »</button>
                @endif
            </div>
        @endif

        <div class="form-container wizard-container" id="fpFormPanel" style="display:none;">
            <h2 class="form-title">Family Planning Client Assessment Record</h2>
            <div id="fp-alert" class="alert" style="display:none"></div>

            <!-- Wizard Steps Indicator -->
            <div class="wizard-steps">
                <div class="step active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Client Info</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Client Type</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Medical History</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-circle">4</div>
                    <div class="step-label">Obstetrical</div>
                </div>
                <div class="step" data-step="5">
                    <div class="step-circle">5</div>
                    <div class="step-label">Assessment</div>
                </div>
            </div>

            <!-- Wizard Content -->
            <div class="wizard-content">
                <form id="fpForm" class="patient-form" method="POST"
                    action="{{ route('health-programs.family-planning-store') }}">
                    @csrf

                    <!-- Step 1: Client & Household Information -->
                    <div class="step-content active" data-step="1">
                        <div class="form-section section-patient-info">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Client & Household Information
                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="fp_client_name">Name of Client <span
                                            class="required-asterisk">*</span></label>
                                    <input type="text" id="fp_client_name" name="fp_client_name" class="form-control"
                                        required>
                                    <span class="error-message" data-for="fp_client_name"></span>
                                </div>
                                <div class="form-group">
                                    <label for="fp_dob">Date of Birth <span class="required-asterisk">*</span> </label>
                                    <input type="date" id="fp_dob" name="fp_dob" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="fp_age">Age <span class="required-asterisk">*</span></label>
                                    <input type="number" id="fp_age" name="fp_age" class="form-control">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="fp_address">Address <span class="required-asterisk">*</span></label>
                                    <input type="text" id="fp_address" name="fp_address" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="fp_contact">Contact Number <span class="required-asterisk">*</span></label>
                                    <input type="text" id="fp_contact" name="fp_contact" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="fp_occupation">Occupation <span class="required-asterisk">*</span></label>
                                    <input type="text" id="fp_occupation" name="fp_occupation" class="form-control">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="fp_spouse">Name of Spouse <span class="required-asterisk">*</span></label>
                                    <input type="text" id="fp_spouse" name="fp_spouse" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="fp_children">No. living children</label>
                                    <input type="number" id="fp_children" name="fp_children" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Client Type & Reason -->
                    <div class="step-content" data-step="2">
                        <div class="form-section section-history">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Client Type & Reason
                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Type of Client</label>
                                    <div class="checkbox-group">
                                        <label><input type="radio" name="fp_type" value="new"> New Acceptor</label>
                                        <label><input type="radio" name="fp_type" value="current"> Current User</label>
                                        <label><input type="radio" name="fp_type" value="changing"> Changing Method</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Reason for FP</label>
                                    <div class="checkbox-group">
                                        <label><input type="checkbox" name="fp_reason[]" value="spacing"> Spacing</label>
                                        <label><input type="checkbox" name="fp_reason[]" value="limiting"> Limiting</label>
                                        <label><input type="checkbox" name="fp_reason[]" value="medical"> Medical
                                            Condition</label>
                                        <label><input type="checkbox" name="fp_reason[]" value="side-effects"> Side
                                            Effects</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Medical History -->
                    <div class="step-content" data-step="3">
                        <div class="form-section section-screening">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Medical History
                            </h3>
                            <div class="form-row small-row">
                                @php
                                    $medicalHistory = [
                                        'Severe headaches / migraine',
                                        'History of stroke / hypertension',
                                        'Heavy menstrual bleeding',
                                        'Breast cancer / mass',
                                        'Jaundice',
                                        'Vaginal bleeding in last 24 hrs',
                                        'Smoker',
                                        'With disability'
                                    ];
                                @endphp
                                @foreach ($medicalHistory as $item)
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="fp_med_history[]" value="{{ $item }}">
                                            {{ $item }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Obstetrical History -->
                    <div class="step-content" data-step="4">
                        <div class="form-section section-history">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Obstetrical History
                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="fp_gravida">Number of pregnancies (G)</label>
                                    <input type="number" id="fp_gravida" name="fp_gravida" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="fp_para">Live births (P)</label>
                                    <input type="number" id="fp_para" name="fp_para" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="fp_last_delivery">Date of last delivery</label>
                                    <input type="date" id="fp_last_delivery" name="fp_last_delivery" class="form-control">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="fp_last_period">Last menstrual period</label>
                                    <input type="date" id="fp_last_period" name="fp_last_period" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="fp_menstrual_flow">Menstrual flow</label>
                                    <select id="fp_menstrual_flow" name="fp_menstrual_flow" class="form-control">
                                        <option value="">Select</option>
                                        <option value="light">Light (1-2 pads/day)</option>
                                        <option value="moderate">Moderate (3-5 pads/day)</option>
                                        <option value="heavy">Heavy (>5 pads/day)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="fp_dysmenorrhea">Dysmenorrhea</label>
                                    <select id="fp_dysmenorrhea" name="fp_dysmenorrhea" class="form-control">
                                        <option value="">Select</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Risk Screening, Physical Exam & Acknowledgement -->
                    <div class="step-content" data-step="5">
                        <div class="form-section section-assessment">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Risk Screening & Physical Exam
                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Risk for STIs</label>
                                    <div class="checkbox-group">
                                        <label><input type="checkbox" name="fp_sti[]" value="abnormal-discharge"> Abnormal
                                            discharge</label>
                                        <label><input type="checkbox" name="fp_sti[]" value="pain"> Pain / burning
                                            sensation</label>
                                        <label><input type="checkbox" name="fp_sti[]" value="partner-symptoms"> Partner has
                                            symptoms</label>
                                        <label><input type="checkbox" name="fp_sti[]" value="treated"> Treated for
                                            STI</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Risk for VAW</label>
                                    <div class="checkbox-group">
                                        <label><input type="checkbox" name="fp_vaw[]" value="domestic-violence"> History of
                                            domestic
                                            violence</label>
                                        <label><input type="checkbox" name="fp_vaw[]" value="sexual-abuse"> Unpleasant
                                            sexual
                                            relationship</label>
                                        <label><input type="checkbox" name="fp_vaw[]" value="referred"> Referred to WCPU /
                                            DSWD</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="fp_bp">Blood Pressure</label>
                                    <input type="text" id="fp_bp" name="fp_bp" class="form-control" placeholder="mmHg">
                                </div>
                                <div class="form-group">
                                    <label for="fp_weight">Weight (kg)</label>
                                    <input type="text" id="fp_weight" name="fp_weight" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="fp_height">Height (cm)</label>
                                    <input type="text" id="fp_height" name="fp_height" class="form-control">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="fp_exam_findings">Physical Examination Findings</label>
                                    <textarea id="fp_exam_findings" name="fp_exam_findings" class="form-control"
                                        rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-section section-patient-info">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Acknowledgement
                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="fp_counseled_by">Counseled By</label>
                                    <input type="text" id="fp_counseled_by" name="fp_counseled_by" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="fp_client_signature">Client Signature</label>
                                    <input type="text" id="fp_client_signature" name="fp_client_signature"
                                        class="form-control" placeholder="Type client name as signature">
                                </div>
                                <div class="form-group">
                                    <label for="fp_consent_date">Date</label>
                                    <input type="date" id="fp_consent_date" name="fp_consent_date" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            </form>

            <!-- Wizard Navigation Buttons -->
            <div class="wizard-buttons">
                <button type="button" class="btn btn-cancel" id="fpCancelBtn">Cancel</button>
                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn btn-prev" id="fpPrevBtn" onclick="changeFpStep(-1)">← Previous</button>
                    <button type="button" class="btn btn-next" id="fpNextBtn" onclick="changeFpStep(1)">Next →</button>
                    <button type="submit" class="btn btn-submit" id="fpSubmitBtn" form="fpForm" style="display: none;">Save
                        Record</button>
                </div>
            </div>
        </div>

        <div class="modal" id="fpViewModal" style="display:none;">
            <div class="modal-content modal-large">
                <div class="modal-header">
                    <h3>Family Planning Record Details</h3>
                    <span class="close-modal" id="closeFpModal">&times;</span>
                </div>
                <div class="modal-body" id="fpModalBody">
                    <div class="loading-spinner" style="text-align:center; padding: 2rem;">
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Record Modal (Super Admin Only) -->
        @if(auth()->user()->role === 'super_admin')
            <div class="modal" id="deleteModal" style="display:none;">
                <div class="modal-content" style="max-width: 500px;">
                    <div class="modal-header" style="background: #dc2626; color: white;">
                        <h3 style="margin: 0;"><i class="bi bi-exclamation-triangle-fill"></i> Delete Family Planning Record
                        </h3>
                        <span class="close-modal" onclick="closeDeleteModal()"
                            style="color: white; cursor: pointer; font-size: 28px;">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p style="margin-bottom: 20px;">Are you sure you want to permanently delete the family planning record
                            for <strong id="deleteRecordName"></strong>?</p>

                        <div
                            style="background: #fee2e2; padding: 15px; border-radius: 4px; border-left: 4px solid #dc2626; margin-bottom: 20px;">
                            <p style="margin: 0 0 10px 0; color: #991b1b;"><strong><i class="bi bi-exclamation-triangle"
                                        style="color: #dc2626;"></i> WARNING:</strong> This action cannot be undone!</p>
                            <p style="margin: 0; color: #991b1b; font-size: 13px;">All family planning data and health records
                                will be permanently deleted.</p>
                        </div>

                        <form method="POST" id="deleteForm" action="">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" id="deleteRecordId" name="record_id">
                            <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
                                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                                <button type="submit" class="btn btn-primary" style="background: #dc2626;"
                                    onclick="this.form.action='/health-programs/family-planning/' + document.getElementById('deleteRecordId').value">
                                    <i class="bi bi-trash"></i> Delete Record
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @push('scripts')
            <script>
                // Multi-step wizard functionality for Family Planning
                let currentFpStep = 1;
                const totalFpSteps = 5;

                function changeFpStep(direction) {
                    const newStep = currentFpStep + direction;

                    if (newStep < 1 || newStep > totalFpSteps) return;

                    // Hide current step
                    document.querySelector(`#fpFormPanel .step-content[data-step="${currentFpStep}"]`).classList.remove('active');
                    document.querySelector(`#fpFormPanel .step[data-step="${currentFpStep}"]`).classList.remove('active');

                    // Show next step
                    currentFpStep = newStep;
                    document.querySelector(`#fpFormPanel .step-content[data-step="${currentFpStep}"]`).classList.add('active');
                    document.querySelector(`#fpFormPanel .step[data-step="${currentFpStep}"]`).classList.add('active');

                    // Mark previous steps as completed
                    document.querySelectorAll('#fpFormPanel .step').forEach(step => {
                        const stepNum = parseInt(step.getAttribute('data-step'));
                        if (stepNum < currentFpStep) {
                            step.classList.add('completed');
                        } else {
                            step.classList.remove('completed');
                        }
                    });

                    // Update button visibility
                    updateFpButtons();

                    // Scroll to top of form
                    document.querySelector('#fpFormPanel .wizard-content').scrollTop = 0;
                }

                function updateFpButtons() {
                    const prevBtn = document.getElementById('fpPrevBtn');
                    const nextBtn = document.getElementById('fpNextBtn');
                    const submitBtn = document.getElementById('fpSubmitBtn');

                    // Show/hide previous button
                    prevBtn.style.display = currentFpStep === 1 ? 'none' : 'block';
                    prevBtn.disabled = currentFpStep === 1;

                    // Show next button on steps 1-4, hide on step 5
                    nextBtn.style.display = currentFpStep === totalFpSteps ? 'none' : 'block';
                    nextBtn.disabled = currentFpStep === totalFpSteps;

                    // Show submit button only on step 5
                    submitBtn.style.display = currentFpStep === totalFpSteps ? 'block' : 'none';
                }

                document.addEventListener('DOMContentLoaded', function () {
                    const formPanel = document.getElementById('fpFormPanel');
                    const tablePanel = document.getElementById('fpTablePanel');
                    const filterWrapper = document.querySelector('.page-content > div:first-child'); // The filter wrapper div
                    const openBtn = document.getElementById('openFpForm');
                    const backBtn = document.getElementById('backToFpList');
                    const cancelBtn = document.getElementById('fpCancelBtn');
                    const form = document.getElementById('fpForm');
                    const alertBox = document.getElementById('fp-alert');
                    const modal = document.getElementById('fpViewModal');
                    const closeModal = document.getElementById('closeFpModal');

                    const pagination = document.getElementById('fpPagination');

                    const toggleForm = (show) => {
                        formPanel.style.display = show ? 'block' : 'none';
                        tablePanel.style.display = show ? 'none' : 'block';
                        if (filterWrapper) filterWrapper.style.display = show ? 'none' : 'flex';
                        if (pagination) pagination.style.display = show ? 'none' : 'flex';

                        if (!show) {
                            alertBox.style.display = 'none';
                            form.reset();
                            // Reset wizard to step 1
                            currentFpStep = 1;
                            document.querySelectorAll('#fpFormPanel .step-content').forEach(content => content.classList.remove('active'));
                            document.querySelectorAll('#fpFormPanel .step').forEach(step => {
                                step.classList.remove('active', 'completed');
                            });
                            document.querySelector('#fpFormPanel .step-content[data-step="1"]').classList.add('active');
                            document.querySelector('#fpFormPanel .step[data-step="1"]').classList.add('active');
                            updateFpButtons();
                        } else {
                            updateFpButtons();
                        }
                    };

                    openBtn.addEventListener('click', () => toggleForm(true));
                    if (backBtn) backBtn.addEventListener('click', () => toggleForm(false));
                    if (cancelBtn) cancelBtn.addEventListener('click', () => toggleForm(false));

                    // Make wizard steps clickable (to go back to previous steps)
                    document.querySelectorAll('#fpFormPanel .step').forEach(step => {
                        step.addEventListener('click', function () {
                            const stepNum = parseInt(this.getAttribute('data-step'));
                            if (stepNum < currentFpStep) {
                                while (currentFpStep > stepNum) {
                                    changeFpStep(-1);
                                }
                            }
                        });
                    });

                    // View button functionality
                    document.querySelectorAll('.view-fp').forEach(button => {
                        button.addEventListener('click', async function () {
                            const recordId = this.getAttribute('data-record');
                            const recordDbId = this.getAttribute('data-id');

                            modal.style.display = 'flex';
                            const modalBody = document.getElementById('fpModalBody');
                            modalBody.innerHTML = '<div style="text-align:center; padding: 2rem;"><p>Loading...</p></div>';

                            try {
                                const response = await fetch(`/health-programs/family-planning/${recordDbId}`);
                                const data = await response.json();

                                const reasonArray = Array.isArray(data.reason) ? data.reason : JSON.parse(data.reason || '[]');
                                const medHistory = Array.isArray(data.medical_history) ? data.medical_history : JSON.parse(data.medical_history || '[]');
                                const stiRisk = Array.isArray(data.sti_risk) ? data.sti_risk : JSON.parse(data.sti_risk || '[]');
                                const vawRisk = Array.isArray(data.vaw_risk) ? data.vaw_risk : JSON.parse(data.vaw_risk || '[]');

                                modalBody.innerHTML = `
                                                                                                                <div class="form-section section-patient-info">
                                                                                                                    <h3 class="section-header"><span class="section-indicator"></span>Client Information</h3>
                                                                                                                    <div class="form-row">
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Record #:</strong></label>
                                                                                                                            <p>${data.record_no || 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Client Name:</strong></label>
                                                                                                                            <p>${data.client_name || 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Age:</strong></label>
                                                                                                                            <p>${data.age || 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="form-row">
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Address:</strong></label>
                                                                                                                            <p>${data.address || 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Contact:</strong></label>
                                                                                                                            <p>${data.contact || 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Occupation:</strong></label>
                                                                                                                            <p>${data.occupation || 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="form-row">
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Client Type:</strong></label>
                                                                                                                            <p>${data.client_type ? data.client_type.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Reason for FP:</strong></label>
                                                                                                                            <p>${reasonArray.length ? reasonArray.join(', ') : 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                                <div class="form-section section-history">
                                                                                                                    <h3 class="section-header"><span class="section-indicator"></span>Spouse & Family</h3>
                                                                                                                    <div class="form-row">
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Spouse Name:</strong></label>
                                                                                                                            <p>${data.spouse_name || 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Spouse Age:</strong></label>
                                                                                                                            <p>${data.spouse_age || 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Number of Children:</strong></label>
                                                                                                                            <p>${data.children_count || 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                                <div class="form-section section-screening">
                                                                                                                    <h3 class="section-header"><span class="section-indicator"></span>Medical History & Examination</h3>
                                                                                                                    <div class="form-row">
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>G (Gravida):</strong></label>
                                                                                                                            <p>${data.gravida ?? 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>P (Para):</strong></label>
                                                                                                                            <p>${data.para ?? 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Last Period:</strong></label>
                                                                                                                            <p>${data.last_period || 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="form-row">
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Blood Pressure:</strong></label>
                                                                                                                            <p>${data.bp || 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Weight:</strong></label>
                                                                                                                            <p>${data.weight || 'N/A'} kg</p>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Height:</strong></label>
                                                                                                                            <p>${data.height || 'N/A'} cm</p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    ${medHistory.length ? `
                                                                                                                    <div class="form-row">
                                                                                                                        <div class="form-group full-width">
                                                                                                                            <label><strong>Medical History:</strong></label>
                                                                                                                            <p>${medHistory.join(', ')}</p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    ` : ''}
                                                                                                                    ${data.exam_findings ? `
                                                                                                                    <div class="form-row">
                                                                                                                        <div class="form-group full-width">
                                                                                                                            <label><strong>Examination Findings:</strong></label>
                                                                                                                            <p>${data.exam_findings}</p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    ` : ''}
                                                                                                                </div>

                                                                                                                <div class="form-section section-patient-info">
                                                                                                                    <h3 class="section-header"><span class="section-indicator"></span>Consent & Counseling</h3>
                                                                                                                    <div class="form-row">
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Counseled By:</strong></label>
                                                                                                                            <p>${data.counseled_by || 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label><strong>Consent Date:</strong></label>
                                                                                                                            <p>${data.consent_date || 'N/A'}</p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            `;
                            } catch (error) {
                                modalBody.innerHTML = '<div style="text-align:center; padding: 2rem; color: red;"><p>Error loading record details.</p></div>';
                            }
                        });
                    });

                    closeModal.addEventListener('click', () => modal.style.display = 'none');
                    window.addEventListener('click', (event) => {
                        if (event.target === modal) {
                            modal.style.display = 'none';
                        }
                    });

                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        const requiredEls = form.querySelectorAll('[required]');
                        let valid = true;
                        requiredEls.forEach(function (el) {
                            const err = form.querySelector('.error-message[data-for="' + el.id + '"]');
                            if (!el.value) {
                                valid = false;
                                if (err) err.textContent = 'This field is required.';
                            } else {
                                if (err) err.textContent = '';
                            }
                        });

                        if (!valid) {
                            alertBox.className = 'alert alert-error';
                            alertBox.style.display = 'block';
                            alertBox.textContent = 'Please fix validation errors before saving.';
                            return;
                        }

                        alertBox.style.display = 'none';
                        form.submit();
                    });

                    // Auto-submit filter form on input/change
                    const fpForm = document.getElementById('fpFilterForm');
                    const fpSearch = document.getElementById('fpSearch');
                    const clientTypeFilter = document.getElementById('clientTypeFilter');
                    const reasonFilter = document.getElementById('reasonFilter');
                    const fpDateFilter = document.getElementById('fpDateFilter');
                    const clearFpBtn = document.getElementById('clearFpFilters');

                    let fpSearchTimeout;

                    // Auto-submit on search input with debounce
                    fpSearch.addEventListener('input', function () {
                        clearTimeout(fpSearchTimeout);
                        fpSearchTimeout = setTimeout(() => {
                            fpForm.submit();
                        }, 500);
                    });

                    // Auto-submit on filter change
                    clientTypeFilter.addEventListener('change', () => fpForm.submit());
                    reasonFilter.addEventListener('change', () => fpForm.submit());
                    fpDateFilter.addEventListener('change', () => fpForm.submit());

                    // Clear all filters
                    clearFpBtn.addEventListener('click', function () {
                        fpSearch.value = '';
                        clientTypeFilter.value = '';
                        reasonFilter.value = '';
                        fpDateFilter.value = '';
                        fpForm.submit();
                    });
                });

                // Delete Modal Functions
                window.openDeleteModal = function (recordId, recordName) {
                    document.getElementById('deleteRecordId').value = recordId;
                    document.getElementById('deleteRecordName').textContent = recordName;
                    document.getElementById('deleteModal').style.display = 'flex';
                };

                window.closeDeleteModal = function () {
                    document.getElementById('deleteModal').style.display = 'none';
                };

                window.addEventListener('click', function (event) {
                    const deleteModal = document.getElementById('deleteModal');
                    if (event.target === deleteModal) {
                        closeDeleteModal();
                    }
                });
            </script>
        @endpush
@endsection