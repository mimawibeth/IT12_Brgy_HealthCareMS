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
            <p class="content-subtitle">Track newborn visits, vaccines, and monitoring follow-ups.</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary" id="openNipForm">+ Add New Record</button>
            <button class="btn btn-secondary" id="backToNipList" style="display:none;">← Back to Records</button>
        </div>
    </div>

    <div class="filters" id="nipFilters">
        <div class="search-box">
            <input type="text" class="search-input" placeholder="Search child name or record ID">
            <button class="btn btn-search" type="button">Search</button>
        </div>
        <div class="filter-options">
            <select class="filter-select">
                <option value="">Age Group</option>
                <option value="0-3">0-3 months</option>
                <option value="4-6">4-6 months</option>
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
        <div class="table-heading">
            <h3>Recent NIP Records</h3>
            <span class="table-note">Latest saved NIP records</span>
        </div>
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
                        @php($lastVisit = $record->visits->sortByDesc('age_months')->first())
                        {{ $lastVisit ? $lastVisit->age_months . ' months' : '—' }}
                    </td>
                    <td><span class="status-chip status-green">Recorded</span></td>
                    <td>
                        <a href="javascript:void(0)" class="btn-action btn-view view-nip"
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

    @if($records->hasPages())
        <div class="pagination">
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
        <h2 class="form-title">Newborn / Immunization Form</h2>
        <div id="form-alert" class="alert" style="display:none"></div>

        <form id="nipForm" class="patient-form" method="POST" action="{{ route('health-programs.nip-store') }}"
            novalidate>
            @csrf

            <div class="form-section section-patient-info">
                <h3 class="section-header">
                    <span class="section-indicator"></span>Baby / Mother Details
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
                        <label for="mother_name">Complete Name of Mother <span class="required-asterisk">*</span></label>
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

            <div class="form-section section-history">
                <h3 class="section-header">
                    <span class="section-indicator"></span>Newborn Screening & Immunizations
                </h3>

                <div class="form-row">
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
                    <div class="form-group">
                        <label for="sex_baby">Sex of baby <span class="required-asterisk">*</span></label>
                        <select id="sex_baby" name="sex_baby" class="form-control" required>
                            <option value="">Select</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                        <span class="error-message" data-for="sex_baby"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tt_status_mother">TT status of Mother <span class="required-asterisk">*</span></label>
                        <input type="text" id="tt_status_mother" name="tt_status_mother" class="form-control" required>
                        <span class="error-message" data-for="tt_status_mother"></span>
                    </div>
                    <div class="form-group">
                        <label for="birth_length">Length at birth</label>
                        <input type="text" id="birth_length" name="birth_length" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="birth_weight">Birth weight <span class="required-asterisk">*</span></label>
                        <input type="text" id="birth_weight" name="birth_weight" class="form-control" required>
                        <span class="error-message" data-for="birth_weight"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="delivery_type">Type of delivery <span class="required-asterisk">*</span></label>
                        <input type="text" id="delivery_type" name="delivery_type" class="form-control" required>
                        <span class="error-message" data-for="delivery_type"></span>
                    </div>
                    <div class="form-group">
                        <label for="initiated_breastfeeding">Initiated breastfeeding after birth <span class="required-asterisk">*</span></label>
                        <select id="initiated_breastfeeding" name="initiated_breastfeeding" class="form-control" required>
                            <option value="">Select</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                        <span class="error-message" data-for="initiated_breastfeeding"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="newborn_screening_date">Date of Newborn Screening <span class="required-asterisk">*</span></label>
                        <input type="date" id="newborn_screening_date" name="newborn_screening_date" class="form-control" required>
                        <span class="error-message" data-for="newborn_screening_date"></span>
                    </div>
                    <div class="form-group">
                        <label for="newborn_screening_result">Result of Newborn Screening <span class="required-asterisk">*</span></label>
                        <input type="text" id="newborn_screening_result" name="newborn_screening_result" class="form-control" required>
                        <span class="error-message" data-for="newborn_screening_result"></span>
                    </div>
                    <div class="form-group">
                        <label for="hearing_test_screened">Screened Hearing Test <span class="required-asterisk">*</span></label>
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
                        <label for="hepa_b_24h">Hepa B birth dose within 24 hrs <span class="required-asterisk">*</span></label>
                        <select id="hepa_b_24h" name="hepa_b_24h" class="form-control" required>
                            <option value="">Select</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                        <span class="error-message" data-for="hepa_b_24h"></span>
                    </div>
                </div>
            </div>

            <div class="form-section section-assessment">
                <div class="section-header section-between">
                    <div>
                        <span class="section-indicator"></span>Visit Monitoring
                    </div>
                    <button type="button" class="btn btn-outline" id="addVisitBtn">
                        + Add Monitoring Visit
                    </button>
                </div>

                <div id="nipVisitsContainer"></div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Record</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>

    <div class="table-container" id="visitSummary" style="display:none;">
        <div class="table-heading">
            <h3>Visit Summary (UI Preview)</h3>
            <span class="table-note">Only visits with values are shown after saving.</span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Age (mos)</th>
                    <th>Weight</th>
                    <th>Length</th>
                    <th>Breastfeeding</th>
                    <th>Temperature</th>
                    <th>Vaccine</th>
                </tr>
            </thead>
            <tbody id="visitSummaryBody"></tbody>
        </table>
    </div>
</div>

<div class="modal" id="nipViewModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>NIP Record Details</h3>
            <span class="close-modal" id="closeNipModal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-section section-patient-info">
                <h3 class="section-header"><span class="section-indicator"></span>Summary</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Record #</label>
                        <p id="nipModalRecord">NIP-2025-001</p>
                    </div>
                    <div class="form-group">
                        <label>Child</label>
                        <p id="nipModalChild">Baby Liam Cruz</p>
                    </div>
                    <div class="form-group">
                        <label>Last Visit</label>
                        <p id="nipModalVisit">2 months</p>
                    </div>
                </div>
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
            const alertBox = document.getElementById('form-alert');
            const visitsContainer = document.getElementById('nipVisitsContainer');
            const addVisitBtn = document.getElementById('addVisitBtn');
            const visitSummary = document.getElementById('visitSummary');
            const visitSummaryBody = document.getElementById('visitSummaryBody');
            const modal = document.getElementById('nipViewModal');
            const closeModal = document.getElementById('closeNipModal');
            let visitCount = 0;

            const toggleForm = (showForm) => {
                formPanel.style.display = showForm ? 'block' : 'none';
                tablePanel.style.display = showForm ? 'none' : 'block';
                filters.style.display = showForm ? 'none' : 'block';
                openBtn.style.display = showForm ? 'none' : 'inline-flex';
                backBtn.style.display = showForm ? 'inline-flex' : 'none';

                if (!showForm) {
                    alertBox.style.display = 'none';
                    form.reset();
                    visitsContainer.innerHTML = '';
                    visitCount = 0;
                    addVisitCard();
                }
            };

            const visitTemplate = (index) => `
                                                <div class="visit-box" data-index="${index}">
                                                    <div class="visit-box-header">
                                                        <h4>Visit ${index + 1}</h4>
                                                        <button type="button" class="btn btn-link remove-visit" data-index="${index}" ${index === 0 ? 'disabled' : ''}>Remove</button>
                                                    </div>
                                                    <div class="form-row small-row">
                                                        <div class="form-group">
                                                            <label>Date</label>
                                                            <input type="date" name="visits[${index}][date]" class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Age in months</label>
                                                            <input type="text" name="visits[${index}][age]" class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Weight</label>
                                                            <input type="text" name="visits[${index}][weight]" class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Length for age</label>
                                                            <input type="text" name="visits[${index}][length]" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="form-row small-row">
                                                        <div class="form-group">
                                                            <label>Status</label>
                                                            <input type="text" name="visits[${index}][status]" class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Breastfeeding</label>
                                                            <select name="visits[${index}][breast]" class="form-control">
                                                                <option value="">Select</option>
                                                                <option value="yes">Yes</option>
                                                                <option value="no">No</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Temperature</label>
                                                            <input type="text" name="visits[${index}][temp]" class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Vaccine</label>
                                                            <input type="text" name="visits[${index}][vaccine]" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            `;

            const addVisitCard = () => {
                const wrapper = document.createElement('div');
                wrapper.innerHTML = visitTemplate(visitCount);
                visitsContainer.appendChild(wrapper.firstElementChild);
                visitCount++;
            };

            visitsContainer.addEventListener('click', (event) => {
                if (event.target.classList.contains('remove-visit')) {
                    const index = parseInt(event.target.getAttribute('data-index'), 10);
                    if (visitsContainer.children.length > 1 && !event.target.disabled) {
                        const card = visitsContainer.querySelector(`.visit-box[data-index="${index}"]`);
                        if (card) card.remove();
                    }
                }
            });

            addVisitBtn.addEventListener('click', addVisitCard);
            addVisitCard(); // load first visit

            openBtn.addEventListener('click', () => toggleForm(true));
            backBtn.addEventListener('click', () => toggleForm(false));

            document.querySelectorAll('.view-nip').forEach(button => {
                button.addEventListener('click', () => {
                    const row = button.closest('tr');
                    document.getElementById('nipModalRecord').textContent = row.children[0].textContent;
                    document.getElementById('nipModalChild').textContent = row.children[1].textContent;
                    document.getElementById('nipModalVisit').textContent = row.children[4].textContent;
                    modal.style.display = 'flex';
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