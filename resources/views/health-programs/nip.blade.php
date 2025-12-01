@extends('layouts.app')

@section('title', 'NIP - Immunization')
@section('page-title', 'National Immunization Program (NIP)')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
<div class="page-content">
    <div class="content-header">
        <div>
            <h2>National Immunization Program Records</h2>
            <p class="content-subtitle">
                Track newborn visits, vaccines, and monitoring follow-ups.
            </p>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary" id="openNipForm">+ Add New Record</button>
            <button class="btn btn-secondary" id="backToNipList" style="display:none;">← Back to List</button>
        </div>
    </div>

    <div class="filters" id="nipFilters">
        <div class="search-box">
            <input type="text" class="search-input" id="nipSearch" placeholder="Search child name or record ID">
            <button class="btn btn-search" type="button">Search</button>
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

    <div class="table-container" id="nipTablePanel">
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
                @forelse ($records ?? [] as $record)
                <tr>
                    <td>{{ $record->record_no ?? 'NIP-' . str_pad($record->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $record->child_name }}</td>
                    <td>{{ optional($record->dob)->format('Y-m-d') }}</td>
                    <td>{{ $record->mother_name }}</td>
                    <td>
                        @php($lastVisit = optional($record->visits)->sortByDesc('age_months')->first())
                        {{ $lastVisit ? $lastVisit->age_months . ' months' : '—' }}
                    </td>
                    <td><span class="status-chip status-green">Recorded</span></td>
                    <td>
                        <a href="javascript:void(0)" class="btn-action btn-view view-nip" data-id="{{ $record->id }}"
                            data-record="{{ $record->record_no ?? 'NIP-' . str_pad($record->id, 3, '0', STR_PAD_LEFT) }}">View</a>
                        <a href="{{ route('health-programs.nip-edit', $record) }}" class="btn-action btn-edit">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;">No NIP records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @php
        $showPagination = !empty($records) && method_exists($records, 'hasPages') && $records->hasPages();
    @endphp
    @if($showPagination)
        <div class="pagination" id="nipPagination">
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

    <div class="form-container" id="nipFormPanel" style="display:none;">
        <h2 class="form-title">Newborn / Immunization Intake Form</h2>
        <div id="nip-alert" class="alert" style="display:none"></div>

        <form id="nipForm" class="patient-form" method="POST" action="{{ route('health-programs.nip-store') }}">
            @csrf

            <!-- Child Information Section -->
            <div class="form-section section-patient-info">
                <h3 class="section-header">
                    <span class="section-indicator"></span>Child & Family Information
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nip_date">Date <span class="required-asterisk">*</span></label>
                        <input type="date" id="nip_date" name="nip_date" class="form-control" required>
                        <span class="error-message" data-for="nip_date"></span>
                    </div>
                    <div class="form-group">
                        <label for="child_name">Name of child <span class="required-asterisk">*</span></label>
                        <input type="text" id="child_name" name="child_name" class="form-control" required>
                        <span class="error-message" data-for="child_name"></span>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth <span class="required-asterisk">*</span></label>
                        <input type="date" id="dob" name="dob" class="form-control" required>
                        <span class="error-message" data-for="dob"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="address">Complete Purok Address <span class="required-asterisk">*</span></label>
                        <input type="text" id="address" name="address" class="form-control" required>
                        <span class="error-message" data-for="address"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="mother_name">Complete Name of Mother (with middle name) <span
                                class="required-asterisk">*</span></label>
                        <input type="text" id="mother_name" name="mother_name" class="form-control" required>
                        <span class="error-message" data-for="mother_name"></span>
                    </div>
                    <div class="form-group">
                        <label for="father_name">Name of Father</label>
                        <input type="text" id="father_name" name="father_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="contact">Cell # <span class="required-asterisk">*</span></label>
                        <input type="text" id="contact" name="contact" class="form-control" required>
                        <span class="error-message" data-for="contact"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nhts_4ps_id">NHTS / 4Ps CCT ID Number</label>
                        <input type="text" id="nhts_4ps_id" name="nhts_4ps_id" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="phic_id">PHIC ID Number</label>
                        <input type="text" id="phic_id" name="phic_id" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="birth_order">No. of child</label>
                        <input type="number" id="birth_order" name="birth_order" class="form-control" min="1">
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
                        <label for="tt_status_mother">TT status of Mother <span
                                class="required-asterisk">*</span></label>
                        <input type="text" id="tt_status_mother" name="tt_status_mother" class="form-control" required>
                        <span class="error-message" data-for="tt_status_mother"></span>
                    </div>
                    <div class="form-group">
                        <label for="place_delivery">Place of delivery <span class="required-asterisk">*</span></label>
                        <input type="text" id="place_delivery" name="place_delivery" class="form-control" required>
                        <span class="error-message" data-for="place_delivery"></span>
                    </div>
                    <div class="form-group">
                        <label for="attended_by">Attended by <span class="required-asterisk">*</span></label>
                        <input type="text" id="attended_by" name="attended_by" class="form-control" required>
                        <span class="error-message" data-for="attended_by"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="sex_baby">Sex of baby <span class="required-asterisk">*</span></label>
                        <select id="sex_baby" name="sex_baby" class="form-control" required>
                            <option value="">Select</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                        <span class="error-message" data-for="sex_baby"></span>
                    </div>
                    <div class="form-group">
                        <label for="birth_length">Length by</label>
                        <input type="text" id="birth_length" name="birth_length" class="form-control"
                            placeholder="e.g., 50 cm">
                    </div>
                    <div class="form-group">
                        <label for="birth_weight">Birth weight <span class="required-asterisk">*</span></label>
                        <input type="text" id="birth_weight" name="birth_weight" class="form-control"
                            placeholder="e.g., 3.2 kg" required>
                        <span class="error-message" data-for="birth_weight"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="delivery_type">Type of delivery <span class="required-asterisk">*</span></label>
                        <input type="text" id="delivery_type" name="delivery_type" class="form-control"
                            placeholder="e.g., Normal, CS" required>
                        <span class="error-message" data-for="delivery_type"></span>
                    </div>
                    <div class="form-group">
                        <label for="initiated_breastfeeding">Initiated breastfeeding after birth <span
                                class="required-asterisk">*</span></label>
                        <select id="initiated_breastfeeding" name="initiated_breastfeeding" class="form-control"
                            required>
                            <option value="">Select</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                        <span class="error-message" data-for="initiated_breastfeeding"></span>
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
                        <label for="newborn_screening_date">Date of Newborn Screening <span
                                class="required-asterisk">*</span></label>
                        <input type="date" id="newborn_screening_date" name="newborn_screening_date"
                            class="form-control" required>
                        <span class="error-message" data-for="newborn_screening_date"></span>
                    </div>
                    <div class="form-group">
                        <label for="newborn_screening_result">Result of Newborn Screening <span
                                class="required-asterisk">*</span></label>
                        <input type="text" id="newborn_screening_result" name="newborn_screening_result"
                            class="form-control" required>
                        <span class="error-message" data-for="newborn_screening_result"></span>
                    </div>
                    <div class="form-group">
                        <label for="hearing_test_screened">Screened Hearing Test <span
                                class="required-asterisk">*</span></label>
                        <select id="hearing_test_screened" name="hearing_test_screened" class="form-control" required>
                            <option value="">Select</option>
                            <option value="pass">Pass</option>
                            <option value="fail">Fail</option>
                        </select>
                        <span class="error-message" data-for="hearing_test_screened"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="vit_k">Vit. K <span class="required-asterisk">*</span></label>
                        <select id="vit_k" name="vit_k" class="form-control" required>
                            <option value="">Select</option>
                            <option value="given">Given</option>
                            <option value="not_given">Not Given</option>
                        </select>
                        <span class="error-message" data-for="vit_k"></span>
                    </div>
                    <div class="form-group">
                        <label for="bcg">BCG <span class="required-asterisk">*</span></label>
                        <select id="bcg" name="bcg" class="form-control" required>
                            <option value="">Select</option>
                            <option value="given">Given</option>
                            <option value="not_given">Not Given</option>
                        </select>
                        <span class="error-message" data-for="bcg"></span>
                    </div>
                    <div class="form-group">
                        <label for="hepa_b_24h">Hepa B birth dose within 24 hrs <span
                                class="required-asterisk">*</span></label>
                        <select id="hepa_b_24h" name="hepa_b_24h" class="form-control" required>
                            <option value="">Select</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                        <span class="error-message" data-for="hepa_b_24h"></span>
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

                <div id="nipVisitsContainer"></div>

                <button type="button" class="btn btn-secondary" id="addVisitBtn" style="margin-top: 1rem;">
                    <i class="bi bi-plus-circle"></i> Add Another Visit
                </button>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Record</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="nipViewModal" style="display:none;">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>NIP Record Details</h3>
            <span class="close-modal" id="closeNipModal">&times;</span>
        </div>
        <div class="modal-body" id="nipModalBody">
            <div class="loading-spinner" style="text-align:center; padding: 2rem;">
                <p>Loading...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formPanel = document.getElementById('nipFormPanel');
            const tablePanel = document.getElementById('nipTablePanel');
            const filters = document.getElementById('nipFilters');
            const openBtn = document.getElementById('openNipForm');
            const backBtn = document.getElementById('backToNipList');
            const form = document.getElementById('nipForm');
            const alertBox = document.getElementById('nip-alert');
            const visitsContainer = document.getElementById('nipVisitsContainer');
            const addVisitBtn = document.getElementById('addVisitBtn');
            const modal = document.getElementById('nipViewModal');
            const closeModal = document.getElementById('closeNipModal');
            const pagination = document.getElementById('nipPagination');

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
                filters.style.display = show ? 'none' : 'block';
                openBtn.style.display = show ? 'none' : 'inline-flex';
                backBtn.style.display = show ? 'inline-flex' : 'none';
                if (pagination) pagination.style.display = show ? 'none' : 'block';

                if (!show) {
                    alertBox.style.display = 'none';
                    form.reset();
                    visitsContainer.innerHTML = '';
                    visitCount = 0;
                    addVisit();
                }
            };

            openBtn.addEventListener('click', () => toggleForm(true));
            backBtn.addEventListener('click', () => toggleForm(false));

            // View button functionality
            document.querySelectorAll('.view-nip').forEach(button => {
                button.addEventListener('click', async function () {
                    const recordId = this.getAttribute('data-record');
                    const recordDbId = this.getAttribute('data-id');

                    modal.style.display = 'flex';
                    const modalBody = document.getElementById('nipModalBody');
                    modalBody.innerHTML = '<div style="text-align:center; padding: 2rem;"><p>Loading...</p></div>';

                    try {
                        const response = await fetch('/health-programs/immunization/' + recordDbId);
                        const data = await response.json();

                        let visitsHtml = '';
                        if (data.visits && data.visits.length > 0) {
                            data.visits.forEach((visit, idx) => {
                                visitsHtml = visitsHtml + '<div class="visit-summary"><h4>Visit ' + (idx + 1) + '</h4>' +
                                    '<div class="form-row"><div class="form-group"><label><strong>Date:</strong></label><p>' + (visit.date || 'N/A') + '</p></div>' +
                                    '<div class="form-group"><label><strong>Age:</strong></label><p>' + (visit.age_months || 'N/A') + ' months</p></div>' +
                                    '<div class="form-group"><label><strong>Weight:</strong></label><p>' + (visit.weight || 'N/A') + ' kg</p></div></div>' +
                                    '<div class="form-row"><div class="form-group"><label><strong>Status:</strong></label><p>' + (visit.status || 'N/A') + '</p></div>' +
                                    '<div class="form-group"><label><strong>Temperature:</strong></label><p>' + (visit.temperature || 'N/A') + '°C</p></div>' +
                                    '<div class="form-group"><label><strong>Vaccine:</strong></label><p>' + (visit.vaccine || 'N/A') + '</p></div></div></div>';
                            });
                        }

                        modalBody.innerHTML =
                            '<div class="form-section section-patient-info">' +
                            '<h3 class="section-header"><span class="section-indicator"></span>Child Information</h3>' +
                            '<div class="form-row">' +
                            '<div class="form-group"><label><strong>Record #:</strong></label><p>' + (data.record_no || 'N/A') + '</p></div>' +
                            '<div class="form-group"><label><strong>Child Name:</strong></label><p>' + (data.child_name || 'N/A') + '</p></div>' +
                            '<div class="form-group"><label><strong>Birth Date:</strong></label><p>' + (data.dob || 'N/A') + '</p></div>' +
                            '</div>' +
                            '<div class="form-row">' +
                            '<div class="form-group"><label><strong>Mother:</strong></label><p>' + (data.mother_name || 'N/A') + '</p></div>' +
                            '<div class="form-group"><label><strong>Father:</strong></label><p>' + (data.father_name || 'N/A') + '</p></div>' +
                            '<div class="form-group"><label><strong>Contact:</strong></label><p>' + (data.contact || 'N/A') + '</p></div>' +
                            '</div>' +
                            '</div>' +
                            '<div class="form-section section-history">' +
                            '<h3 class="section-header"><span class="section-indicator"></span>Birth Details</h3>' +
                            '<div class="form-row">' +
                            '<div class="form-group"><label><strong>Birth Weight:</strong></label><p>' + (data.birth_weight || 'N/A') + '</p></div>' +
                            '<div class="form-group"><label><strong>Place of Delivery:</strong></label><p>' + (data.place_delivery || 'N/A') + '</p></div>' +
                            '<div class="form-group"><label><strong>Delivery Type:</strong></label><p>' + (data.delivery_type || 'N/A') + '</p></div>' +
                            '</div>' +
                            '</div>' +
                            (visitsHtml ? '<div class="form-section section-assessment"><h3 class="section-header"><span class="section-indicator"></span>Visit History</h3>' + visitsHtml + '</div>' : '');
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
        });
    </script>
@endpush
@endsection