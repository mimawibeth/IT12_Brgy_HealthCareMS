@extends('layouts.app')

@section('title', 'Prenatal Checkup')
@section('page-title', 'Prenatal Check Up Form')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="content-header">
            <div>
                <h2>Prenatal Care Records</h2>
                <p class="content-subtitle">
                    Record new prenatal assessments, maternal health details, and vital signs.
                </p>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary" id="openPrenatalForm">+ Add New Record</button>
            </div>
        </div>

        <div class="filters" id="prenatalFilters">
            <div class="search-box">
                <input type="text" class="search-input" id="prenatalSearch" placeholder="Search patient name or ID">
                <button class="btn btn-search" type="button">Search</button>
            </div>
            <div class="filter-options">
                <select class="filter-select">
                    <option value="">Filter by Purok</option>
                    <option value="Sto. Niño">Purok 1</option>
                    <option value="Poblacion">Purok 2</option>
                </select>
                <select class="filter-select">
                    <option value="">Filter by LMP status</option>
                    <option value="recent">Recent (≤ 4 weeks)</option>
                    <option value="follow-up">Follow-up needed</option>
                </select>
                <input type="date" class="filter-select" />
            </div>
        </div>

        <div class="table-container" id="prenatalTablePanel">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Record #</th>
                        <th>Mother</th>
                        <th>LMP</th>
                        <th>Contact</th>
                        <th>Purok</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records ?? [] as $record)
                        <tr>
                            <td>{{ $record->record_no ?? 'PT-' . str_pad($record->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $record->mother_name }}</td>
                            <td>{{ optional($record->lmp)->format('Y-m-d') }}</td>
                            <td>{{ $record->cell }}</td>
                            <td>{{ $record->purok }}</td>
                            <td><span class="status-chip status-green">Recorded</span></td>
                            <td>
                                <a href="javascript:void(0)" class="btn-action btn-view view-prenatal"
                                    data-id="{{ $record->id }}"
                                    data-record="{{ $record->record_no ?? 'PT-' . str_pad($record->id, 3, '0', STR_PAD_LEFT) }}">View</a>
                                <a href="{{ route('health-programs.prenatal-edit', $record) }}"
                                    class="btn-action btn-edit">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;">No prenatal records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php
            $showPagination = !empty($records) && method_exists($records, 'hasPages') && $records->hasPages();
        @endphp
        @if($showPagination)
            <div class="pagination" id="prenatalPagination">
                @if($records->onFirstPage())
                    <button class="btn-page" disabled>« Previous</button>
                @else
                    <a class="btn-page" href="{{ $records->previousPageUrl() }}">« Previous</a>
                @endif

                @php
                    $start = max(1, $records->currentPage() - 2);
                    $end = min($records->lastPage(), $records->currentPage() + 2);
                @endphp

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page === $records->currentPage())
                        <span class="btn-page active">{{ $page }}</span>
                    @else
                        <a class="btn-page" href="{{ $records->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                <span class="page-info">
                    Page {{ $records->currentPage() }} of {{ $records->lastPage() }} ({{ $records->total() }} total records)
                </span>

                @if($records->hasMorePages())
                    <a class="btn-page" href="{{ $records->nextPageUrl() }}">Next »</a>
                @else
                    <button class="btn-page" disabled>Next »</button>
                @endif
            </div>
        @endif

        <div class="form-container wizard-container" id="prenatalFormPanel" style="display:none;">
            <h2 class="form-title">Prenatal Care Intake Form</h2>

            <div id="prenatal-alert" class="alert" style="display:none"></div>

            <div class="wizard-steps">
                <div class="step active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Mother Profile</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Family & Labs</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Prenatal Visits (SOAP)</div>
                </div>
            </div>

            <div class="wizard-content">
                <form id="prenatalWizardForm" class="patient-form" method="POST"
                    action="{{ route('health-programs.prenatal-store') }}">
                    @csrf

                    <!-- Step 1 -->
                    <div class="step-content active" data-step="1">
                        <div class="form-section section-patient-info">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Personal & Background Information (Mother)
                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="mother_name">Name <span class="required-asterisk">*</span></label>
                                    <input type="text" id="mother_name" name="mother_name" class="form-control" required>
                                    <span class="error-message" data-for="mother_name"></span>
                                </div>
                                <div class="form-group">
                                    <label for="purok">Purok <span class="required-asterisk">*</span></label>
                                    <input type="text" id="purok" name="purok" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="age">Age <span class="required-asterisk">*</span></label>
                                    <input type="number" id="age" name="age" class="form-control" min="10" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dob">Date of Birth <span class="required-asterisk">*</span></label>
                                    <input type="date" id="dob" name="dob" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="occupation">Occupation</label>
                                    <input type="text" id="occupation" name="occupation" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="education">Educational Attainment</label>
                                    <select id="education" name="education" class="form-control">
                                        <option value="">Select</option>
                                        <option value="elementary">Elementary</option>
                                        <option value="highschool">High School</option>
                                        <option value="vocational">Vocational</option>
                                        <option value="college">College</option>
                                        <option value="post-graduate">Post Graduate</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label><input type="checkbox" id="is_4ps" name="is_4ps" value="1"> 4Ps / NHTS
                                        Member</label>
                                </div>
                                <div class="form-group">
                                    <label for="four_ps_no">4Ps / NHTS No.</label>
                                    <input type="text" id="four_ps_no" name="four_ps_no" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="cell">Cell # <span class="required-asterisk">*</span></label>
                                    <input type="text" id="cell" name="cell" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="lmp">LMP <span class="required-asterisk">*</span></label>
                                    <input type="date" id="lmp" name="lmp" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="edc">EDC <span class="required-asterisk">*</span></label>
                                    <input type="date" id="edc" name="edc" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="urinalysis">Urinalysis</label>
                                    <input type="text" id="urinalysis" name="urinalysis" class="form-control"
                                        placeholder="Result / Date">
                                </div>
                            </div>
                        </div>

                        <div class="form-section section-screening">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Pregnancy History & Laboratory Screening
                            </h3>
                            <div class="form-row small-row">
                                <div class="form-group">
                                    <label for="gravida">G (Gravida) <span class="required-asterisk">*</span></label>
                                    <input type="number" id="gravida" name="gravida" class="form-control" min="0" required>
                                </div>
                                <div class="form-group">
                                    <label for="para">P (Para) <span class="required-asterisk">*</span></label>
                                    <input type="number" id="para" name="para" class="form-control" min="0" required>
                                </div>
                                <div class="form-group">
                                    <label for="abortion">A (Abortion)</label>
                                    <input type="number" id="abortion" name="abortion" class="form-control" min="0">
                                </div>
                                <div class="form-group">
                                    <label for="delivery_count">D (Delivery)</label>
                                    <input type="number" id="delivery_count" name="delivery_count" class="form-control"
                                        min="0">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="last_delivery_date">Date of Last Delivery</label>
                                    <input type="date" id="last_delivery_date" name="last_delivery_date"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="delivery_type">Type of Delivery</label>
                                    <select id="delivery_type" name="delivery_type" class="form-control">
                                        <option value="">Select</option>
                                        <option value="nsvd">NSVD</option>
                                        <option value="cs">Cesarean Section</option>
                                        <option value="assisted">Assisted Delivery</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="hemoglobin_first">1st Hemoglobin</label>
                                    <input type="text" id="hemoglobin_first" name="hemoglobin_first" class="form-control"
                                        placeholder="g/dL">
                                </div>
                                <div class="form-group">
                                    <label for="hemoglobin_second">2nd Hemoglobin</label>
                                    <input type="text" id="hemoglobin_second" name="hemoglobin_second" class="form-control"
                                        placeholder="g/dL">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="blood_type">Blood Typing <span class="required-asterisk">*</span></label>
                                    <input type="text" id="blood_type" name="blood_type" class="form-control"
                                        placeholder="e.g., O+" required>
                                </div>
                                <div class="form-group">
                                    <label for="urinalysis_protein">Urinalysis - Protein</label>
                                    <input type="text" id="urinalysis_protein" name="urinalysis_protein"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="urinalysis_sugar">Urinalysis - Sugar</label>
                                    <input type="text" id="urinalysis_sugar" name="urinalysis_sugar" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="step-content" data-step="2">
                        <div class="form-section section-history">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Father & Delivery Preparation
                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="husband_name">Husband / Partner</label>
                                    <input type="text" id="husband_name" name="husband_name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="husband_occupation">Occupation</label>
                                    <input type="text" id="husband_occupation" name="husband_occupation"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="husband_education">Educational Attainment</label>
                                    <select id="husband_education" name="husband_education" class="form-control">
                                        <option value="">Select</option>
                                        <option value="elementary">Elementary</option>
                                        <option value="highschool">High School</option>
                                        <option value="vocational">Vocational</option>
                                        <option value="college">College</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="family_religion">Religion</label>
                                    <input type="text" id="family_religion" name="family_religion" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="amount_prepared">Amount Prepared</label>
                                    <input type="text" id="amount_prepared" name="amount_prepared" class="form-control"
                                        placeholder="₱">
                                </div>
                                <div class="form-group">
                                    <label>PhilHealth Member</label>
                                    <select id="philhealth_member" name="philhealth_member" class="form-control">
                                        <option value="">Select</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-section section-patient-info">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Mother & Child Book Plan
                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="delivery_location">Where to Deliver</label>
                                    <input type="text" id="delivery_location" name="delivery_location" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="delivery_partner">Partner During Delivery</label>
                                    <input type="text" id="delivery_partner" name="delivery_partner" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-section section-screening">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Vaccinations & Laboratory Tests
                            </h3>
                            <div class="form-row small-row">
                                @for ($dose = 1; $dose <= 5; $dose++)
                                    <div class="form-group">
                                        <label for="td{{ $dose }}">TD{{ $dose }}</label>
                                        <input type="date" id="td{{ $dose }}" name="td{{ $dose }}" class="form-control">
                                    </div>
                                @endfor
                                <div class="form-group">
                                    <label for="tdl">TDL</label>
                                    <input type="date" id="tdl" name="tdl" class="form-control">
                                </div>
                            </div>
                            <div class="form-row small-row">
                                <div class="form-group">
                                    <label for="fbs">FBS</label>
                                    <input type="text" id="fbs" name="fbs" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="rbs">RBS</label>
                                    <input type="text" id="rbs" name="rbs" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="ogtt">OGTT</label>
                                    <input type="text" id="ogtt" name="ogtt" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="vdrl">Syphilis / VDRL</label>
                                    <input type="text" id="vdrl" name="vdrl" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="hbsag">HBsAg</label>
                                    <input type="text" id="hbsag" name="hbsag" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="hiv">HIV Test</label>
                                    <input type="text" id="hiv" name="hiv" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="step-content" data-step="3">
                        <div class="form-section section-assessment">
                            <div class="section-header">
                                <span class="section-indicator"></span>Prenatal Visit Record (SOAP)
                            </div>
                            <div id="prenatalVisitsContainer"></div>
                        </div>
                    </div>

                    <div class="wizard-buttons">
                        <a href="{{ route('health-programs.prenatal-view') }}" class="btn btn-cancel">Cancel</a>
                        <div class="wizard-nav">
                            <button type="button" class="btn btn-prev" id="prenatalPrevBtn">← Previous</button>
                            <button type="button" class="btn btn-next" id="prenatalNextBtn">Next →</button>
                            <button type="submit" class="btn btn-submit" id="prenatalSubmitBtn" style="display:none;">Save
                                Record</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="prenatalViewModal" style="display:none;">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>Prenatal Record Details</h3>
                <span class="close-modal" id="closePrenatalModal">&times;</span>
            </div>
            <div class="modal-body" id="prenatalModalBody">
                <div class="loading-spinner" style="text-align:center; padding: 2rem;">
                    <p>Loading...</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const formPanel = document.getElementById('prenatalFormPanel');
                const tablePanel = document.getElementById('prenatalTablePanel');
                const filters = document.getElementById('prenatalFilters');
                const openBtn = document.getElementById('openPrenatalForm');
                const backBtn = null;
                const wizardForm = document.getElementById('prenatalWizardForm');
                const alertBox = document.getElementById('prenatal-alert');
                const modal = document.getElementById('prenatalViewModal');
                const closeModal = document.getElementById('closePrenatalModal');
                const wizardSteps = document.querySelectorAll('#prenatalFormPanel .wizard-steps .step');
                const stepContents = document.querySelectorAll('#prenatalFormPanel .step-content');
                const prevBtn = document.getElementById('prenatalPrevBtn');
                const nextBtn = document.getElementById('prenatalNextBtn');
                const submitBtn = document.getElementById('prenatalSubmitBtn');
                const visitsContainer = document.getElementById('prenatalVisitsContainer');
                let currentStep = 1;
                const totalSteps = stepContents.length;
                let visitCount = 0;

                const visitTemplate = (index) => `
                                                        <div class="visit-box" data-index="${index}">
                                                            <div class="visit-box-header">
                                                                <h4>Visit ${index + 1}</h4>
                                                                <button type="button" class="btn btn-link remove-visit" ${index === 0 ? 'disabled' : ''}>Remove</button>
                                                            </div>
                                                            <div class="visit-subsection">
                                                                <h5>S – Subjective</h5>
                                                                <div class="form-row">
                                                                    <div class="form-group">
                                                                        <label>Date</label>
                                                                        <input type="date" name="visits[${index}][date]" class="form-control">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Trimester</label>
                                                                        <select name="visits[${index}][trimester]" class="form-control">
                                                                            <option value="">Select</option>
                                                                            <option value="1st">1st</option>
                                                                            <option value="2nd">2nd</option>
                                                                            <option value="3rd">3rd</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Risk Code</label>
                                                                        <select name="visits[${index}][risk]" class="form-control">
                                                                            <option value="">Select</option>
                                                                            <option value="low">Low Risk</option>
                                                                            <option value="high">High Risk</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="form-row">
                                                                    <div class="form-group">
                                                                        <label>Is this the 1st Visit?</label>
                                                                        <select name="visits[${index}][first_visit]" class="form-control">
                                                                            <option value="">Select</option>
                                                                            <option value="yes">Yes</option>
                                                                            <option value="no">No</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group full-width">
                                                                        <label>Subjective Notes</label>
                                                                        <textarea name="visits[${index}][subjective]" class="form-control" rows="2" placeholder="Patient concerns / complaints"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="visit-subsection">
                                                                <h5>O – Objective</h5>
                                                                <div class="form-row small-row">
                                                                    <div class="form-group">
                                                                        <label>AOG (weeks)</label>
                                                                        <input type="text" name="visits[${index}][aog]" class="form-control">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Weight (kg)</label>
                                                                        <input type="text" name="visits[${index}][weight]" class="form-control">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Height (cm)</label>
                                                                        <input type="text" name="visits[${index}][height]" class="form-control">
                                                                    </div>
                                                                </div>
                                                                <div class="form-row small-row">
                                                                    <div class="form-group">
                                                                        <label>B/P</label>
                                                                        <input type="text" name="visits[${index}][bp]" class="form-control" placeholder="mmHg">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>PR</label>
                                                                        <input type="text" name="visits[${index}][pr]" class="form-control">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>FH (Fundal Height)</label>
                                                                        <input type="text" name="visits[${index}][fh]" class="form-control">
                                                                    </div>
                                                                </div>
                                                                <div class="form-row small-row">
                                                                    <div class="form-group">
                                                                        <label>FHT</label>
                                                                        <input type="text" name="visits[${index}][fht]" class="form-control">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Presentation</label>
                                                                        <input type="text" name="visits[${index}][presentation]" class="form-control" placeholder="Cephalic / Breech">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>BMI</label>
                                                                        <input type="text" name="visits[${index}][bmi]" class="form-control">
                                                                    </div>
                                                                </div>
                                                                <div class="form-row small-row">
                                                                    <div class="form-group">
                                                                        <label>RR</label>
                                                                        <input type="text" name="visits[${index}][rr]" class="form-control">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>HR</label>
                                                                        <input type="text" name="visits[${index}][hr]" class="form-control">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="visit-subsection">
                                                                <h5>A – Assessment</h5>
                                                                <div class="form-row">
                                                                    <div class="form-group full-width">
                                                                        <label>Assessment</label>
                                                                        <textarea name="visits[${index}][assessment]" class="form-control" rows="2" placeholder="e.g., Normal pregnancy, at risk, etc."></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="visit-subsection">
                                                                <h5>P – Plan</h5>
                                                                <div class="form-row">
                                                                    <div class="form-group">
                                                                        <label>Next Visit</label>
                                                                        <input type="date" name="visits[${index}][next_visit]" class="form-control">
                                                                    </div>
                                                                    <div class="form-group full-width">
                                                                        <label>Plan / Orders</label>
                                                                        <textarea name="visits[${index}][plan]" class="form-control" rows="2" placeholder="Medications, tests, advice"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    `;

                const refreshVisitHeadings = () => {
                    const boxes = visitsContainer.querySelectorAll('.visit-box');
                    boxes.forEach((box, idx) => {
                        const title = box.querySelector('h4');
                        if (title) {
                            title.textContent = `Visit ${idx + 1}`;
                        }
                        const removeBtn = box.querySelector('.remove-visit');
                        if (removeBtn) {
                            removeBtn.disabled = idx === 0;
                        }
                    });
                };

                const addVisitCard = () => {
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = visitTemplate(visitCount);
                    visitsContainer.appendChild(wrapper.firstElementChild);
                    visitCount++;
                    refreshVisitHeadings();
                };

                addVisitCard();

                visitsContainer.addEventListener('click', (event) => {
                    if (event.target.classList.contains('remove-visit') && !event.target.disabled) {
                        const card = event.target.closest('.visit-box');
                        if (card) {
                            card.remove();
                            refreshVisitHeadings();
                        }
                    }
                });

                const updateWizard = () => {
                    stepContents.forEach((section, idx) => {
                        const stepNum = idx + 1;
                        section.classList.toggle('active', stepNum === currentStep);
                    });

                    wizardSteps.forEach(step => {
                        const stepNum = parseInt(step.getAttribute('data-step'), 10);
                        step.classList.toggle('active', stepNum === currentStep);
                        step.classList.toggle('completed', stepNum < currentStep);
                    });

                    prevBtn.style.display = currentStep === 1 ? 'none' : 'inline-flex';
                    nextBtn.style.display = currentStep === totalSteps ? 'none' : 'inline-flex';
                    submitBtn.style.display = currentStep === totalSteps ? 'inline-flex' : 'none';
                };

                const changeStep = (direction) => {
                    const newStep = currentStep + direction;
                    if (newStep < 1 || newStep > totalSteps) return;
                    currentStep = newStep;
                    updateWizard();
                    document.querySelector('.wizard-content').scrollTop = 0;
                };

                prevBtn.addEventListener('click', () => changeStep(-1));
                nextBtn.addEventListener('click', () => changeStep(1));

                wizardSteps.forEach(step => {
                    step.addEventListener('click', () => {
                        const stepNum = parseInt(step.getAttribute('data-step'), 10);
                        if (stepNum < currentStep) {
                            currentStep = stepNum;
                            updateWizard();
                        }
                    });
                });

                const pagination = document.getElementById('prenatalPagination');
                
                const toggleForm = (showForm) => {
                    formPanel.style.display = showForm ? 'block' : 'none';
                    tablePanel.style.display = showForm ? 'none' : 'block';
                    filters.style.display = showForm ? 'none' : 'block';
                    openBtn.style.display = showForm ? 'none' : 'inline-flex';
                    if (pagination) pagination.style.display = showForm ? 'none' : 'block';
                    if (backBtn) backBtn.style.display = showForm ? 'inline-flex' : 'none';

                    if (!showForm) {
                        alertBox.style.display = 'none';
                        wizardForm.reset();
                        visitsContainer.innerHTML = '';
                        visitCount = 0;
                        addVisitCard();
                        currentStep = 1;
                        updateWizard();
                    }
                };

                openBtn.addEventListener('click', () => {
                    toggleForm(true);
                    currentStep = 1;
                    updateWizard();
                });
                if (backBtn) backBtn.addEventListener('click', () => toggleForm(false));
                updateWizard();

                document.querySelectorAll('.view-prenatal').forEach(button => {
                    button.addEventListener('click', async function () {
                        const recordId = this.getAttribute('data-record');
                        const recordDbId = this.getAttribute('data-id');

                        modal.style.display = 'flex';
                        const modalBody = document.getElementById('prenatalModalBody');
                        modalBody.innerHTML = '<div style="text-align:center; padding: 2rem;"><p>Loading...</p></div>';

                        try {
                            const response = await fetch(`/health-programs/prenatal/${recordDbId}`);
                            const data = await response.json();

                            modalBody.innerHTML = `
                                        <div class="form-section section-patient-info">
                                            <h3 class="section-header"><span class="section-indicator"></span>Mother Information</h3>
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label><strong>Record #:</strong></label>
                                                    <p>${data.record_no || 'N/A'}</p>
                                                </div>
                                                <div class="form-group">
                                                    <label><strong>Name:</strong></label>
                                                    <p>${data.mother_name || 'N/A'}</p>
                                                </div>
                                                <div class="form-group">
                                                    <label><strong>Age:</strong></label>
                                                    <p>${data.age || 'N/A'}</p>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label><strong>Purok:</strong></label>
                                                    <p>${data.purok || 'N/A'}</p>
                                                </div>
                                                <div class="form-group">
                                                    <label><strong>Contact:</strong></label>
                                                    <p>${data.cell || 'N/A'}</p>
                                                </div>
                                                <div class="form-group">
                                                    <label><strong>Occupation:</strong></label>
                                                    <p>${data.occupation || 'N/A'}</p>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label><strong>LMP (Last Menstrual Period):</strong></label>
                                                    <p>${data.lmp || 'N/A'}</p>
                                                </div>
                                                <div class="form-group">
                                                    <label><strong>EDC (Expected Date of Confinement):</strong></label>
                                                    <p>${data.edc || 'N/A'}</p>
                                                </div>
                                                <div class="form-group">
                                                    <label><strong>Blood Type:</strong></label>
                                                    <p>${data.blood_type || 'N/A'}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-section section-screening">
                                            <h3 class="section-header"><span class="section-indicator"></span>Pregnancy History</h3>
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
                                                    <label><strong>A (Abortion):</strong></label>
                                                    <p>${data.abortion ?? 'N/A'}</p>
                                                </div>
                                                <div class="form-group">
                                                    <label><strong>Last Delivery:</strong></label>
                                                    <p>${data.last_delivery_date || 'N/A'}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-section section-history">
                                            <h3 class="section-header"><span class="section-indicator"></span>Partner Information</h3>
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label><strong>Husband/Partner:</strong></label>
                                                    <p>${data.husband_name || 'N/A'}</p>
                                                </div>
                                                <div class="form-group">
                                                    <label><strong>Occupation:</strong></label>
                                                    <p>${data.husband_occupation || 'N/A'}</p>
                                                </div>
                                                <div class="form-group">
                                                    <label><strong>PhilHealth:</strong></label>
                                                    <p>${data.philhealth_member || 'N/A'}</p>
                                                </div>
                                            </div>
                                        </div>

                                        ${data.visits && data.visits.length > 0 ? `
                                        <div class="form-section section-assessment">
                                            <h3 class="section-header"><span class="section-indicator"></span>Prenatal Visits (${data.visits.length})</h3>
                                            ${data.visits.map((visit, idx) => `
                                                <div class="visit-box" style="margin-bottom: 1rem; border: 1px solid #ddd; padding: 1rem; border-radius: 4px;">
                                                    <h4 style="margin-bottom: 0.5rem;">Visit ${idx + 1} - ${visit.date || 'N/A'}</h4>
                                                    <div class="form-row">
                                                        <div class="form-group">
                                                            <label><strong>Trimester:</strong></label>
                                                            <p>${visit.trimester || 'N/A'}</p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label><strong>Weight:</strong></label>
                                                            <p>${visit.weight || 'N/A'} kg</p>
                                                        </div>
                                                        <div class="form-group">
                                                            <label><strong>BP:</strong></label>
                                                            <p>${visit.bp || 'N/A'}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                        ` : ''}
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

                wizardForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const requiredEls = wizardForm.querySelectorAll('[required]');
                    let valid = true;
                    requiredEls.forEach(function (el) {
                        const err = wizardForm.querySelector('.error-message[data-for="' + el.id + '"]');
                        if (!el.value) {
                            valid = false;
                            if (err) err.textContent = 'This field is required.';
                        } else if (err) {
                            err.textContent = '';
                        }
                    });

                    if (!valid) {
                        alertBox.className = 'alert alert-error';
                        alertBox.style.display = 'block';
                        alertBox.textContent = 'Please fix validation errors before saving.';
                        return;
                    }

                    alertBox.style.display = 'none';
                    wizardForm.submit();
                });
            });
        </script>
    @endpush
@endsection