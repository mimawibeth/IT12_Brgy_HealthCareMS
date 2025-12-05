@extends('layouts.app')

@section('title', isset($record) ? 'Edit Immunization Record' : 'New Immunization Record')
@section('page-title', isset($record) ? 'Edit Immunization Record' : 'New Immunization Record')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="form-container">
            @if(isset($record))
                <h2 class="form-title">Editing Immunization Record #{{ $record->record_no ?? ('NIP-' . str_pad($record->id, 3, '0', STR_PAD_LEFT)) }}</h2>

                <div class="form-section section-patient-info">
                    <h3 class="section-header"><span class="section-indicator"></span>Child Summary</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Child Name</label>
                            <input type="text" class="form-control" value="{{ $record->child_name }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" class="form-control" value="{{ optional($record->dob)->format('Y-m-d') }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Mother's Name</label>
                            <input type="text" class="form-control" value="{{ $record->mother_name }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Contact #</label>
                            <input type="text" class="form-control" value="{{ $record->contact }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="form-section section-history">
                    <h3 class="section-header"><span class="section-indicator"></span>Logged Visits</h3>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Visit #</th>
                                    <th>Date</th>
                                    <th>Age (months)</th>
                                    <th>Weight</th>
                                    <th>Status</th>
                                    <th>Vaccine</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($record->visits as $index => $visit)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ optional($visit->visit_date)->format('Y-m-d') }}</td>
                                        <td>{{ $visit->age_months }}</td>
                                        <td>{{ $visit->weight }}</td>
                                        <td>{{ $visit->status }}</td>
                                        <td>{{ $visit->vaccine }}</td>
                                        <td>
                                            <a href="javascript:void(0)" class="btn-action btn-view view-nip-visit" 
                                               data-visit-id="{{ $visit->id }}"
                                               data-visit-index="{{ $index }}">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="text-align:center;">No visits recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <h2 class="form-title">New Immunization Record</h2>
            @endif

            <div class="form-section section-assessment">
                <div class="section-header section-between">
                    <span><span class="section-indicator"></span>{{ isset($record) ? 'Add Follow-up Visit' : 'Child & Immunization Details' }}</span>
                    @if(isset($record))
                        <button type="button" class="btn btn-outline" id="addFollowUpBtn">+ Add Visit</button>
                    @endif
                </div>
                <p class="form-note">
                    @if(isset($record))
                        Capture immunization and growth monitoring details for follow-up visits. Previously saved visits appear in the table above.
                    @else
                        Provide child, family, birth, and immunization information.
                    @endif
                </p>

                <form id="nipForm" method="POST" action="{{ isset($record) ? route('health-programs.new-nip-update', $record) : route('health-programs.new-nip-store') }}">
                    @csrf
                    @if(isset($record))
                        @method('PUT')
                    @endif

                    @if(!isset($record))
                        <!-- Child Information Section -->
                        <div class="form-section section-patient-info">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Child & Family Information
                            </h3>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nip_date">Date <span class="required-asterisk">*</span></label>
                                    <input type="date" id="nip_date" name="nip_date" class="form-control" required
                                        value="{{ old('nip_date') }}">
                                    <span class="error-message" data-for="nip_date"></span>
                                </div>
                                <div class="form-group">
                                    <label for="nip_child_name">Name of child <span class="required-asterisk">*</span></label>
                                    <input type="text" id="nip_child_name" name="nip_child_name" class="form-control"
                                        required value="{{ old('nip_child_name') }}">
                                    <span class="error-message" data-for="nip_child_name"></span>
                                </div>
                                <div class="form-group">
                                    <label for="nip_dob">Date of Birth <span class="required-asterisk">*</span></label>
                                    <input type="date" id="nip_dob" name="nip_dob" class="form-control" required
                                        value="{{ old('nip_dob') }}">
                                    <span class="error-message" data-for="nip_dob"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="nip_address">Complete Purok Address <span class="required-asterisk">*</span></label>
                                    <input type="text" id="nip_address" name="nip_address" class="form-control" required
                                        value="{{ old('nip_address') }}">
                                    <span class="error-message" data-for="nip_address"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nip_mother_name">Complete Name of Mother (with middle name) <span class="required-asterisk">*</span></label>
                                    <input type="text" id="nip_mother_name" name="nip_mother_name" class="form-control"
                                        required value="{{ old('nip_mother_name') }}">
                                    <span class="error-message" data-for="nip_mother_name"></span>
                                </div>
                                <div class="form-group">
                                    <label for="nip_father_name">Name of Father</label>
                                    <input type="text" id="nip_father_name" name="nip_father_name" class="form-control"
                                        value="{{ old('nip_father_name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="nip_contact">Cell # <span class="required-asterisk">*</span></label>
                                    <input type="text" id="nip_contact" name="nip_contact" class="form-control" required
                                        value="{{ old('nip_contact') }}">
                                    <span class="error-message" data-for="nip_contact"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nip_nhts_4ps_id">NHTS / 4Ps CCT ID Number</label>
                                    <input type="text" id="nip_nhts_4ps_id" name="nip_nhts_4ps_id" class="form-control"
                                        value="{{ old('nip_nhts_4ps_id') }}">
                                </div>
                                <div class="form-group">
                                    <label for="nip_phic_id">PHIC ID Number</label>
                                    <input type="text" id="nip_phic_id" name="nip_phic_id" class="form-control"
                                        value="{{ old('nip_phic_id') }}">
                                </div>
                                <div class="form-group">
                                    <label for="nip_birth_order">No. of child</label>
                                    <input type="number" id="nip_birth_order" name="nip_birth_order" class="form-control"
                                        min="1" value="{{ old('nip_birth_order') }}">
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
                                    <label for="nip_tt_status_mother">TT status of Mother <span class="required-asterisk">*</span></label>
                                    <input type="text" id="nip_tt_status_mother" name="nip_tt_status_mother"
                                        class="form-control" required value="{{ old('nip_tt_status_mother') }}">
                                    <span class="error-message" data-for="nip_tt_status_mother"></span>
                                </div>
                                <div class="form-group">
                                    <label for="nip_place_delivery">Place of delivery <span class="required-asterisk">*</span></label>
                                    <input type="text" id="nip_place_delivery" name="nip_place_delivery" class="form-control"
                                        required value="{{ old('nip_place_delivery') }}">
                                    <span class="error-message" data-for="nip_place_delivery"></span>
                                </div>
                                <div class="form-group">
                                    <label for="nip_attended_by">Attended by <span class="required-asterisk">*</span></label>
                                    <input type="text" id="nip_attended_by" name="nip_attended_by" class="form-control"
                                        required value="{{ old('nip_attended_by') }}">
                                    <span class="error-message" data-for="nip_attended_by"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nip_sex_baby">Sex of baby <span class="required-asterisk">*</span></label>
                                    <select id="nip_sex_baby" name="nip_sex_baby" class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="M" @selected(old('nip_sex_baby') === 'M')>Male</option>
                                        <option value="F" @selected(old('nip_sex_baby') === 'F')>Female</option>
                                    </select>
                                    <span class="error-message" data-for="nip_sex_baby"></span>
                                </div>
                                <div class="form-group">
                                    <label for="nip_birth_length">Length at birth</label>
                                    <input type="text" id="nip_birth_length" name="nip_birth_length" class="form-control"
                                        placeholder="e.g., 50 cm" value="{{ old('nip_birth_length') }}">
                                </div>
                                <div class="form-group">
                                    <label for="nip_birth_weight">Birth weight <span class="required-asterisk">*</span></label>
                                    <input type="text" id="nip_birth_weight" name="nip_birth_weight" class="form-control"
                                        placeholder="e.g., 3.2 kg" required value="{{ old('nip_birth_weight') }}">
                                    <span class="error-message" data-for="nip_birth_weight"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nip_delivery_type">Type of delivery <span class="required-asterisk">*</span></label>
                                    <input type="text" id="nip_delivery_type" name="nip_delivery_type" class="form-control"
                                        placeholder="e.g., Normal, CS" required value="{{ old('nip_delivery_type') }}">
                                    <span class="error-message" data-for="nip_delivery_type"></span>
                                </div>
                                <div class="form-group">
                                    <label for="nip_initiated_breastfeeding">Initiated breastfeeding after birth <span class="required-asterisk">*</span></label>
                                    <select id="nip_initiated_breastfeeding" name="nip_initiated_breastfeeding"
                                        class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="yes" @selected(old('nip_initiated_breastfeeding') === 'yes')>Yes</option>
                                        <option value="no" @selected(old('nip_initiated_breastfeeding') === 'no')>No</option>
                                    </select>
                                    <span class="error-message" data-for="nip_initiated_breastfeeding"></span>
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
                                    <label for="nip_newborn_screening_date">Date of Newborn Screening <span class="required-asterisk">*</span></label>
                                    <input type="date" id="nip_newborn_screening_date" name="nip_newborn_screening_date"
                                        class="form-control" required value="{{ old('nip_newborn_screening_date') }}">
                                    <span class="error-message" data-for="nip_newborn_screening_date"></span>
                                </div>
                                <div class="form-group">
                                    <label for="nip_newborn_screening_result">Result of Newborn Screening <span class="required-asterisk">*</span></label>
                                    <input type="text" id="nip_newborn_screening_result" name="nip_newborn_screening_result"
                                        class="form-control" required value="{{ old('nip_newborn_screening_result') }}">
                                    <span class="error-message" data-for="nip_newborn_screening_result"></span>
                                </div>
                                <div class="form-group">
                                    <label for="nip_hearing_test_screened">Screened Hearing Test <span class="required-asterisk">*</span></label>
                                    <select id="nip_hearing_test_screened" name="nip_hearing_test_screened"
                                        class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="pass" @selected(old('nip_hearing_test_screened') === 'pass')>Pass</option>
                                        <option value="fail" @selected(old('nip_hearing_test_screened') === 'fail')>Fail</option>
                                    </select>
                                    <span class="error-message" data-for="nip_hearing_test_screened"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nip_vit_k">Vit. K <span class="required-asterisk">*</span></label>
                                    <select id="nip_vit_k" name="nip_vit_k" class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="given" @selected(old('nip_vit_k') === 'given')>Given</option>
                                        <option value="not_given" @selected(old('nip_vit_k') === 'not_given')>Not Given</option>
                                    </select>
                                    <span class="error-message" data-for="nip_vit_k"></span>
                                </div>
                                <div class="form-group">
                                    <label for="nip_bcg">BCG <span class="required-asterisk">*</span></label>
                                    <select id="nip_bcg" name="nip_bcg" class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="given" @selected(old('nip_bcg') === 'given')>Given</option>
                                        <option value="not_given" @selected(old('nip_bcg') === 'not_given')>Not Given</option>
                                    </select>
                                    <span class="error-message" data-for="nip_bcg"></span>
                                </div>
                                <div class="form-group">
                                    <label for="nip_hepa_b_24h">Hepa B birth dose within 24 hrs <span class="required-asterisk">*</span></label>
                                    <select id="nip_hepa_b_24h" name="nip_hepa_b_24h" class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="yes" @selected(old('nip_hepa_b_24h') === 'yes')>Yes</option>
                                        <option value="no" @selected(old('nip_hepa_b_24h') === 'no')>No</option>
                                    </select>
                                    <span class="error-message" data-for="nip_hepa_b_24h"></span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div id="followUpContainer"></div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">{{ isset($record) ? 'Save Visits' : 'Save Record' }}</button>
                        <a href="{{ route('health-programs.new-nip-view') }}" class="btn btn-secondary">Back to Records</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- NIP Visit View Modal -->
    @if(isset($record))
        <div id="nipVisitViewModal" class="modal" style="display:none;">
            <div class="modal-content modal-large">
                <div class="modal-header">
                    <h3>Immunization Visit Details</h3>
                    <span class="close-modal" id="closeNipVisitModal">&times;</span>
                </div>
                <div class="modal-body" id="nipVisitModalBody">
                    <div class="loading-spinner" style="text-align:center; padding: 2rem;">
                        <p>Loading...</p>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" id="closeNipVisitModalBtn">Close</button>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('followUpContainer');
            const addBtn = document.getElementById('addFollowUpBtn');
            let visitCount = 0;

            const visitTemplate = (index) => `
                <div class="visit-box" data-index="${index}">
                    <div class="visit-box-header">
                        <h4>Visit ${index + 1}</h4>
                        <button type="button" class="btn btn-link remove-follow-up" data-index="${index}">Remove</button>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Date <span class="required-asterisk">*</span></label>
                            <input type="date" name="new_visits[${index}][date]" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Age in months <span class="required-asterisk">*</span></label>
                            <input type="text" name="new_visits[${index}][age]" class="form-control" placeholder="e.g., 2" required>
                        </div>
                        <div class="form-group">
                            <label>Weight <span class="required-asterisk">*</span></label>
                            <input type="text" name="new_visits[${index}][weight]" class="form-control" placeholder="kg" required>
                        </div>
                        <div class="form-group">
                            <label>Length for age</label>
                            <input type="text" name="new_visits[${index}][length]" class="form-control" placeholder="cm">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Status <span class="required-asterisk">*</span></label>
                            <input type="text" name="new_visits[${index}][status]" class="form-control" placeholder="Normal, Underweight, etc." required>
                        </div>
                        <div class="form-group">
                            <label>Breastfeeding <span class="required-asterisk">*</span></label>
                            <select name="new_visits[${index}][breast]" class="form-control" required>
                                <option value="">Select</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Temperature <span class="required-asterisk">*</span></label>
                            <input type="text" name="new_visits[${index}][temp]" class="form-control" placeholder="°C" required>
                        </div>
                        <div class="form-group">
                            <label>Vaccine <span class="required-asterisk">*</span></label>
                            <input type="text" name="new_visits[${index}][vaccine]" class="form-control" placeholder="Vaccine given" required>
                        </div>
                    </div>
                </div>
            `;

            const addVisitCard = () => {
                const wrapper = document.createElement('div');
                wrapper.innerHTML = visitTemplate(visitCount);
                container.appendChild(wrapper.firstElementChild);
                visitCount++;
            };

            if (addBtn) {
                container.addEventListener('click', (event) => {
                    if (event.target.classList.contains('remove-follow-up')) {
                        event.target.closest('.visit-box').remove();
                    }
                });

                addBtn.addEventListener('click', addVisitCard);
            }

            // View NIP Visit Modal
            @if(isset($record))
                const visitModal = document.getElementById('nipVisitViewModal');
                const visitModalBody = document.getElementById('nipVisitModalBody');
                const closeVisitModal = document.getElementById('closeNipVisitModal');
                const closeVisitModalBtn = document.getElementById('closeNipVisitModalBtn');

                // Store visits data from server
                const visitsData = @json($record->visits);

                document.querySelectorAll('.view-nip-visit').forEach(button => {
                    button.addEventListener('click', function() {
                        const visitId = parseInt(this.getAttribute('data-visit-id'));
                        const visitIndex = parseInt(this.getAttribute('data-visit-index'));
                        
                        visitModal.style.display = 'flex';
                        visitModalBody.innerHTML = '<div style="text-align:center; padding: 2rem;"><p>Loading...</p></div>';

                        // Find the visit data
                        const visit = visitsData[visitIndex];

                        if (visit) {
                            const formatDate = (value) => {
                                if (!value) return 'N/A';
                                const d = new Date(value);
                                if (isNaN(d.getTime())) return value;
                                return d.toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: '2-digit'
                                });
                            };

                            visitModalBody.innerHTML = `
                                <div class="form-section section-patient-info">
                                    <h3 class="section-header"><span class="section-indicator"></span>Visit Information</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Visit Date:</strong></label>
                                            <p>${formatDate(visit.visit_date)}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Age (months):</strong></label>
                                            <p>${visit.age_months || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Weight:</strong></label>
                                            <p>${visit.weight || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Length:</strong></label>
                                            <p>${visit.length || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section section-history">
                                    <h3 class="section-header"><span class="section-indicator"></span>Health Status</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Status:</strong></label>
                                            <p>${visit.status || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Breastfeeding:</strong></label>
                                            <p>${visit.breastfeeding || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Temperature:</strong></label>
                                            <p>${visit.temperature || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section section-assessment">
                                    <h3 class="section-header"><span class="section-indicator"></span>Vaccine Information</h3>
                                    <div class="form-row">
                                        <div class="form-group full-width">
                                            <label><strong>Vaccine:</strong></label>
                                            <p>${visit.vaccine || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        } else {
                            visitModalBody.innerHTML = '<div style="text-align:center; padding: 2rem; color: red;"><p>Visit not found.</p></div>';
                        }
                    });
                });

                closeVisitModal.addEventListener('click', () => visitModal.style.display = 'none');
                closeVisitModalBtn.addEventListener('click', () => visitModal.style.display = 'none');
                window.addEventListener('click', (event) => {
                    if (event.target === visitModal) {
                        visitModal.style.display = 'none';
                    }
                });
            @endif
        });
    </script>
@endpush
