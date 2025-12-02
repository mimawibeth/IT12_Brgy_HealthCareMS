@extends('layouts.app')

@section('title', 'New Immunization')
@section('page-title', 'New Immunization')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <div class="filters" id="newNipFilters">
            <div class="search-box">
                <input type="text" class="search-input" id="newNipSearch" placeholder="Search child name or record ID">
                <button class="btn btn-search" type="button"><i class="bi bi-search"></i> Search</button>
                <button class="btn btn-primary" id="openNewNipForm" style="margin-left: 10px;">
                    <i class="bi bi-plus-circle"></i> Add New Record
                </button>
                <button class="btn btn-secondary" id="backToNewNipList" style="display:none; margin-left: 10px;">
                    <i class="bi bi-arrow-left"></i> Back to Records
                </button>
            </div>
            <div class="filter-options">
                <select class="filter-select">
                    <option value="">Age Group</option>
                    <option value="0-3">0-3 months</option>
                    <option value="4-6">4-6 months</option>
                    <option value="7-12">7-12 months</option>
                </select>
                <select class="filter-select">
                    <option value="">Visit Status</option>
                    <option value="due">Due</option>
                    <option value="complete">Complete</option>
                </select>
                <input type="date" class="filter-select" />
            </div>
        </div>

        <div class="table-container" id="newNipTablePanel">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Record #</th>
                        <th>Child</th>
                        <th>Birth Date</th>
                        <th>Mother</th>
                        <th>Last Visit</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" style="text-align:center;">No records found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="form-container" id="newNipFormPanel" style="display:none;">
            <h2 class="form-title">Newborn / Immunization Intake Form</h2>
            <div id="newNip-alert" class="alert" style="display:none"></div>

            <form id="newNipForm" class="patient-form">
                <!-- Child Information Section -->
                <div class="form-section section-patient-info">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Child & Family Information
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="newNip_date">Date <span class="required-asterisk">*</span></label>
                            <input type="date" id="newNip_date" name="newNip_date" class="form-control" required>
                            <span class="error-message" data-for="newNip_date"></span>
                        </div>
                        <div class="form-group">
                            <label for="newNip_child_name">Name of child <span class="required-asterisk">*</span></label>
                            <input type="text" id="newNip_child_name" name="newNip_child_name" class="form-control"
                                required>
                            <span class="error-message" data-for="newNip_child_name"></span>
                        </div>
                        <div class="form-group">
                            <label for="newNip_dob">Date of Birth <span class="required-asterisk">*</span></label>
                            <input type="date" id="newNip_dob" name="newNip_dob" class="form-control" required>
                            <span class="error-message" data-for="newNip_dob"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="newNip_address">Complete Purok Address <span
                                    class="required-asterisk">*</span></label>
                            <input type="text" id="newNip_address" name="newNip_address" class="form-control" required>
                            <span class="error-message" data-for="newNip_address"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="newNip_mother_name">Complete Name of Mother (with middle name) <span
                                    class="required-asterisk">*</span></label>
                            <input type="text" id="newNip_mother_name" name="newNip_mother_name" class="form-control"
                                required>
                            <span class="error-message" data-for="newNip_mother_name"></span>
                        </div>
                        <div class="form-group">
                            <label for="newNip_father_name">Name of Father</label>
                            <input type="text" id="newNip_father_name" name="newNip_father_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="newNip_contact">Cell # <span class="required-asterisk">*</span></label>
                            <input type="text" id="newNip_contact" name="newNip_contact" class="form-control" required>
                            <span class="error-message" data-for="newNip_contact"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="newNip_nhts_4ps_id">NHTS / 4Ps CCT ID Number</label>
                            <input type="text" id="newNip_nhts_4ps_id" name="newNip_nhts_4ps_id" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="newNip_phic_id">PHIC ID Number</label>
                            <input type="text" id="newNip_phic_id" name="newNip_phic_id" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="newNip_birth_order">No. of child</label>
                            <input type="number" id="newNip_birth_order" name="newNip_birth_order" class="form-control"
                                min="1">
                        </div>
                    </div>
                </div>

                <!-- Birth & Delivery Information Section -->
                <div class="form-section section-history">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Birth & Delivery Information
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="newNip_tt_status_mother">TT status of Mother <span
                                    class="required-asterisk">*</span></label>
                            <input type="text" id="newNip_tt_status_mother" name="newNip_tt_status_mother"
                                class="form-control" required>
                            <span class="error-message" data-for="newNip_tt_status_mother"></span>
                        </div>
                        <div class="form-group">
                            <label for="newNip_place_delivery">Place of delivery <span
                                    class="required-asterisk">*</span></label>
                            <input type="text" id="newNip_place_delivery" name="newNip_place_delivery" class="form-control"
                                required>
                            <span class="error-message" data-for="newNip_place_delivery"></span>
                        </div>
                        <div class="form-group">
                            <label for="newNip_attended_by">Attended by <span class="required-asterisk">*</span></label>
                            <input type="text" id="newNip_attended_by" name="newNip_attended_by" class="form-control"
                                required>
                            <span class="error-message" data-for="newNip_attended_by"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="newNip_sex_baby">Sex of baby <span class="required-asterisk">*</span></label>
                            <select id="newNip_sex_baby" name="newNip_sex_baby" class="form-control" required>
                                <option value="">Select</option>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                            </select>
                            <span class="error-message" data-for="newNip_sex_baby"></span>
                        </div>
                        <div class="form-group">
                            <label for="newNip_birth_length">Length by</label>
                            <input type="text" id="newNip_birth_length" name="newNip_birth_length" class="form-control"
                                placeholder="e.g., 50 cm">
                        </div>
                        <div class="form-group">
                            <label for="newNip_birth_weight">Birth weight <span class="required-asterisk">*</span></label>
                            <input type="text" id="newNip_birth_weight" name="newNip_birth_weight" class="form-control"
                                placeholder="e.g., 3.2 kg" required>
                            <span class="error-message" data-for="newNip_birth_weight"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="newNip_delivery_type">Type of delivery <span
                                    class="required-asterisk">*</span></label>
                            <input type="text" id="newNip_delivery_type" name="newNip_delivery_type" class="form-control"
                                placeholder="e.g., Normal, CS" required>
                            <span class="error-message" data-for="newNip_delivery_type"></span>
                        </div>
                        <div class="form-group">
                            <label for="newNip_initiated_breastfeeding">Initiated breastfeeding after birth <span
                                    class="required-asterisk">*</span></label>
                            <select id="newNip_initiated_breastfeeding" name="newNip_initiated_breastfeeding"
                                class="form-control" required>
                                <option value="">Select</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            <span class="error-message" data-for="newNip_initiated_breastfeeding"></span>
                        </div>
                    </div>
                </div>

                <!-- Newborn Screening Section -->
                <div class="form-section section-screening">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Newborn Screening & Immunizations
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="newNip_newborn_screening_date">Date of Newborn Screening <span
                                    class="required-asterisk">*</span></label>
                            <input type="date" id="newNip_newborn_screening_date" name="newNip_newborn_screening_date"
                                class="form-control" required>
                            <span class="error-message" data-for="newNip_newborn_screening_date"></span>
                        </div>
                        <div class="form-group">
                            <label for="newNip_newborn_screening_result">Result of Newborn Screening <span
                                    class="required-asterisk">*</span></label>
                            <input type="text" id="newNip_newborn_screening_result" name="newNip_newborn_screening_result"
                                class="form-control" required>
                            <span class="error-message" data-for="newNip_newborn_screening_result"></span>
                        </div>
                        <div class="form-group">
                            <label for="newNip_hearing_test_screened">Screened Hearing Test <span
                                    class="required-asterisk">*</span></label>
                            <select id="newNip_hearing_test_screened" name="newNip_hearing_test_screened"
                                class="form-control" required>
                                <option value="">Select</option>
                                <option value="pass">Pass</option>
                                <option value="fail">Fail</option>
                            </select>
                            <span class="error-message" data-for="newNip_hearing_test_screened"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="newNip_vit_k">Vit. K <span class="required-asterisk">*</span></label>
                            <select id="newNip_vit_k" name="newNip_vit_k" class="form-control" required>
                                <option value="">Select</option>
                                <option value="given">Given</option>
                                <option value="not_given">Not Given</option>
                            </select>
                            <span class="error-message" data-for="newNip_vit_k"></span>
                        </div>
                        <div class="form-group">
                            <label for="newNip_bcg">BCG <span class="required-asterisk">*</span></label>
                            <select id="newNip_bcg" name="newNip_bcg" class="form-control" required>
                                <option value="">Select</option>
                                <option value="given">Given</option>
                                <option value="not_given">Not Given</option>
                            </select>
                            <span class="error-message" data-for="newNip_bcg"></span>
                        </div>
                        <div class="form-group">
                            <label for="newNip_hepa_b_24h">Hepa B birth dose within 24 hrs <span
                                    class="required-asterisk">*</span></label>
                            <select id="newNip_hepa_b_24h" name="newNip_hepa_b_24h" class="form-control" required>
                                <option value="">Select</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            <span class="error-message" data-for="newNip_hepa_b_24h"></span>
                        </div>
                    </div>
                </div>

                <!-- Visit Monitoring Section -->
                <div class="form-section section-assessment">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Immunization Visit Monitoring
                    </h3>
                    <p style="margin-bottom: 1rem; color: #7f8c8d;">Add multiple visits to track vaccine schedules and
                        growth monitoring.</p>

                    <div id="newNipVisitsContainer"></div>

                    <button type="button" class="btn btn-secondary" id="addNewNipVisitBtn" style="margin-top: 1rem;">
                        <i class="bi bi-plus-circle"></i> Add Another Visit
                    </button>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-primary"
                        onclick="alert('Backend processing not yet implemented')">Save Record</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const formPanel = document.getElementById('newNipFormPanel');
                const tablePanel = document.getElementById('newNipTablePanel');
                const filters = document.getElementById('newNipFilters');
                const openBtn = document.getElementById('openNewNipForm');
                const backBtn = document.getElementById('backToNewNipList');
                const form = document.getElementById('newNipForm');
                const visitsContainer = document.getElementById('newNipVisitsContainer');
                const addVisitBtn = document.getElementById('addNewNipVisitBtn');

                let visitCount = 0;

                const createVisitBox = (index) => {
                    const visitBox = document.createElement('div');
                    visitBox.className = 'visit-box';
                    visitBox.setAttribute('data-index', index);

                    visitBox.innerHTML =
                        '<div class="visit-box-header">' +
                        '<h4>Visit ' + (index + 1) + '</h4>' +
                        '<button type="button" class="btn btn-link remove-visit" data-index="' + index + '">Remove</button>' +
                        '</div>' +
                        '<div class="form-row small-row">' +
                        '<div class="form-group">' +
                        '<label>Date <span class="required-asterisk">*</span></label>' +
                        '<input type="date" name="visits[' + index + '][date]" class="form-control" required>' +
                        '</div>' +
                        '<div class="form-group">' +
                        '<label>Age in months <span class="required-asterisk">*</span></label>' +
                        '<input type="text" name="visits[' + index + '][age]" class="form-control" placeholder="e.g., 2" required>' +
                        '</div>' +
                        '<div class="form-group">' +
                        '<label>Weight <span class="required-asterisk">*</span></label>' +
                        '<input type="text" name="visits[' + index + '][weight]" class="form-control" placeholder="kg" required>' +
                        '</div>' +
                        '<div class="form-group">' +
                        '<label>Length for age</label>' +
                        '<input type="text" name="visits[' + index + '][length]" class="form-control" placeholder="cm">' +
                        '</div>' +
                        '</div>' +
                        '<div class="form-row small-row">' +
                        '<div class="form-group">' +
                        '<label>Status <span class="required-asterisk">*</span></label>' +
                        '<input type="text" name="visits[' + index + '][status]" class="form-control" placeholder="Normal, Underweight, etc." required>' +
                        '</div>' +
                        '<div class="form-group">' +
                        '<label>Breastfeeding <span class="required-asterisk">*</span></label>' +
                        '<select name="visits[' + index + '][breast]" class="form-control" required>' +
                        '<option value="">Select</option>' +
                        '<option value="yes">Yes</option>' +
                        '<option value="no">No</option>' +
                        '</select>' +
                        '</div>' +
                        '<div class="form-group">' +
                        '<label>Temp <span class="required-asterisk">*</span></label>' +
                        '<input type="text" name="visits[' + index + '][temp]" class="form-control" placeholder="°C" required>' +
                        '</div>' +
                        '<div class="form-group">' +
                        '<label>Vaccine <span class="required-asterisk">*</span></label>' +
                        '<input type="text" name="visits[' + index + '][vaccine]" class="form-control" placeholder="Vaccine given" required>' +
                        '</div>' +
                        '</div>';

                    return visitBox;
                };

                const addVisit = () => {
                    const visitBox = createVisitBox(visitCount);
                    visitsContainer.appendChild(visitBox);
                    visitCount++;
                };

                const removeVisit = (index) => {
                    const visitBox = visitsContainer.querySelector('[data-index="' + index + '"]');
                    if (visitBox) {
                        visitBox.remove();
                        refreshVisitNumbers();
                    }
                };

                const refreshVisitNumbers = () => {
                    const visitBoxes = visitsContainer.querySelectorAll('.visit-box');
                    visitBoxes.forEach((box, idx) => {
                        box.setAttribute('data-index', idx);
                        const header = box.querySelector('h4');
                        if (header) {
                            header.textContent = 'Visit ' + (idx + 1);
                        }
                        const removeBtn = box.querySelector('.remove-visit');
                        if (removeBtn) {
                            removeBtn.setAttribute('data-index', idx);
                        }

                        const inputs = box.querySelectorAll('input, select');
                        inputs.forEach(input => {
                            const name = input.getAttribute('name');
                            if (name) {
                                const newName = name.replace(/visits\[\d+\]/, 'visits[' + idx + ']');
                                input.setAttribute('name', newName);
                            }
                        });
                    });
                    visitCount = visitBoxes.length;
                };

                addVisitBtn.addEventListener('click', addVisit);

                visitsContainer.addEventListener('click', (e) => {
                    if (e.target.classList.contains('remove-visit')) {
                        const index = parseInt(e.target.getAttribute('data-index'));
                        removeVisit(index);
                    }
                });

                // Add first visit by default
                addVisit();

                const toggleForm = (show) => {
                    formPanel.style.display = show ? 'block' : 'none';
                    tablePanel.style.display = show ? 'none' : 'block';
                    filters.style.display = show ? 'none' : 'flex';
                    openBtn.style.display = show ? 'none' : 'inline-flex';
                    backBtn.style.display = show ? 'inline-flex' : 'none';

                    if (!show) {
                        form.reset();
                        visitsContainer.innerHTML = '';
                        visitCount = 0;
                        addVisit();
                    }
                };

                openBtn.addEventListener('click', () => toggleForm(true));
                backBtn.addEventListener('click', () => toggleForm(false));
            });
        </script>
    @endpush
@endsection