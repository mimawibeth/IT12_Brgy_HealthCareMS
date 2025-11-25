@extends('layouts.app')

@section('title', 'Family Planning')
@section('page-title', 'Family Planning Client Assessment')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="content-header">
            <div>
                <h2>Family Planning Client Assessment</h2>
                <p class="content-subtitle">Manage client intake, risks, and physical examination records.</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary" id="openFpForm">+ Add New Record</button>
                <button class="btn btn-secondary" id="backToFpList" style="display:none;">← Back to Records</button>
            </div>
        </div>

        <div class="filters" id="fpFilters">
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Search client name or FP number">
                <button class="btn btn-search" type="button">Search</button>
            </div>
            <div class="filter-options">
                <select class="filter-select">
                    <option value="">Client Type</option>
                    <option value="new">New Acceptor</option>
                    <option value="current">Current User</option>
                </select>
                <select class="filter-select">
                    <option value="">Reason for FP</option>
                    <option value="spacing">Spacing</option>
                    <option value="limiting">Limiting</option>
                    <option value="medical">Medical Condition</option>
                </select>
                <input type="date" class="filter-select" />
            </div>
        </div>

        <div class="table-container" id="fpTablePanel">
            <div class="table-heading">
                <h3>FP Assessment Records</h3>
                <span class="table-note">Sample dataset for UI demo</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>FP #</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>Last Visit</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>FP-001</td>
                        <td>Jane Villanueva</td>
                        <td>New Acceptor</td>
                        <td>Spacing</td>
                        <td>2025-02-12</td>
                        <td><span class="status-chip status-green">Cleared</span></td>
                        <td>
                            <a href="javascript:void(0)" class="btn-action btn-view">View</a>
                            <a href="{{ route('health-programs.family-planning-edit', 1) }}" class="btn-action btn-edit">Edit</a>
                        </td>
                    </tr>
                    <tr>
                        <td>FP-002</td>
                        <td>Rosa Alvarez</td>
                        <td>Current User</td>
                        <td>Limiting</td>
                        <td>2025-01-28</td>
                        <td><span class="status-chip status-amber">Follow-up</span></td>
                        <td>
                            <a href="javascript:void(0)" class="btn-action btn-view">View</a>
                            <a href="{{ route('health-programs.family-planning-edit', 2) }}" class="btn-action btn-edit">Edit</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="form-container" id="fpFormPanel" style="display:none;">
            <h2 class="form-title">Family Planning Client Assessment Record</h2>
            <div id="fp-alert" class="alert" style="display:none"></div>

            <form id="fpForm" class="patient-form" novalidate>
                @csrf

                <div class="form-section section-patient-info">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Client & Household Information
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fp_client_name">Name of Client <span class="required-asterisk">*</span></label>
                            <input type="text" id="fp_client_name" name="fp_client_name" class="form-control" required>
                            <span class="error-message" data-for="fp_client_name"></span>
                        </div>
                        <div class="form-group">
                            <label for="fp_dob">Date of Birth</label>
                            <input type="date" id="fp_dob" name="fp_dob" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="fp_age">Age</label>
                            <input type="number" id="fp_age" name="fp_age" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fp_address">Address</label>
                            <input type="text" id="fp_address" name="fp_address" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="fp_contact">Contact Number</label>
                            <input type="text" id="fp_contact" name="fp_contact" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="fp_occupation">Occupation</label>
                            <input type="text" id="fp_occupation" name="fp_occupation" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fp_spouse">Name of Spouse</label>
                            <input type="text" id="fp_spouse" name="fp_spouse" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="fp_spouse_age">Age</label>
                            <input type="number" id="fp_spouse_age" name="fp_spouse_age" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="fp_children">No. living children</label>
                            <input type="number" id="fp_children" name="fp_children" class="form-control">
                        </div>
                    </div>
                </div>

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
                                <label><input type="checkbox" name="fp_reason[]" value="medical"> Medical Condition</label>
                                <label><input type="checkbox" name="fp_reason[]" value="side-effects"> Side Effects</label>
                            </div>
                        </div>
                    </div>
                </div>

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

                <div class="form-section section-assessment">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Risk Screening & Physical Exam
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Risk for STIs</label>
                            <div class="checkbox-group">
                                <label><input type="checkbox" name="fp_sti[]" value="abnormal-discharge"> Abnormal discharge</label>
                                <label><input type="checkbox" name="fp_sti[]" value="pain"> Pain / burning sensation</label>
                                <label><input type="checkbox" name="fp_sti[]" value="partner-symptoms"> Partner has symptoms</label>
                                <label><input type="checkbox" name="fp_sti[]" value="treated"> Treated for STI</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Risk for VAW</label>
                            <div class="checkbox-group">
                                <label><input type="checkbox" name="fp_vaw[]" value="domestic-violence"> History of domestic violence</label>
                                <label><input type="checkbox" name="fp_vaw[]" value="sexual-abuse"> Unpleasant sexual relationship</label>
                                <label><input type="checkbox" name="fp_vaw[]" value="referred"> Referred to WCPU / DSWD</label>
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
                            <textarea id="fp_exam_findings" name="fp_exam_findings" class="form-control" rows="3"></textarea>
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
                            <input type="text" id="fp_client_signature" name="fp_client_signature" class="form-control"
                                placeholder="Type client name as signature">
                        </div>
                        <div class="form-group">
                            <label for="fp_consent_date">Date</label>
                            <input type="date" id="fp_consent_date" name="fp_consent_date" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Record</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formPanel = document.getElementById('fpFormPanel');
            const tablePanel = document.getElementById('fpTablePanel');
            const filters = document.getElementById('fpFilters');
            const openBtn = document.getElementById('openFpForm');
            const backBtn = document.getElementById('backToFpList');
            const form = document.getElementById('fpForm');
            const alertBox = document.getElementById('fp-alert');

            const toggleForm = (show) => {
                formPanel.style.display = show ? 'block' : 'none';
                tablePanel.style.display = show ? 'none' : 'block';
                filters.style.display = show ? 'none' : 'block';
                openBtn.style.display = show ? 'none' : 'inline-flex';
                backBtn.style.display = show ? 'inline-flex' : 'none';

                if (!show) {
                    alertBox.style.display = 'none';
                    form.reset();
                }
            };

            openBtn.addEventListener('click', () => toggleForm(true));
            backBtn.addEventListener('click', () => toggleForm(false));

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

                alertBox.className = 'alert alert-success';
                alertBox.style.display = 'block';
                alertBox.textContent = 'Record saved successfully (UI-only).';
                form.scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
@endpush
@endsection

