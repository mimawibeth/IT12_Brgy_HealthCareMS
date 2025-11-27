@extends('layouts.app')

@section('title', 'Edit NIP Record')
@section('page-title', 'Edit Immunization Entry')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="form-container">
            <h2 class="form-title">Editing NIP Record #{{ $record->record_no ?? ('NIP-' . str_pad($record->id, 3, '0', STR_PAD_LEFT)) }}</h2>
            <div id="form-alert" class="alert" style="display:none"></div>

            <form class="patient-form" method="POST" action="{{ route('health-programs.nip-update', $record) }}" novalidate>
                @csrf
                @method('PUT')

                <div class="form-section section-patient-info">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Baby / Mother Details
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nip_date">Date <span class="required-asterisk">*</span></label>
                            <input type="date" id="nip_date" name="nip_date" class="form-control" value="{{ old('nip_date', optional($record->date)->format('Y-m-d')) }}" required>
                            <span class="error-message" data-for="nip_date"></span>
                        </div>

                        <div class="form-group">
                            <label for="child_name">Name of child <span class="required-asterisk">*</span></label>
                            <input type="text" id="child_name" name="child_name" class="form-control" value="{{ old('child_name', $record->child_name) }}" required>
                            <span class="error-message" data-for="child_name"></span>
                        </div>

                        <div class="form-group">
                            <label for="dob">Date of Birth <span class="required-asterisk">*</span></label>
                            <input type="date" id="dob" name="dob" class="form-control" value="{{ old('dob', optional($record->dob)->format('Y-m-d')) }}" required>
                            <span class="error-message" data-for="dob"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="address">Complete Purok Address <span class="required-asterisk">*</span></label>
                            <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $record->address) }}" required>
                            <span class="error-message" data-for="address"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="mother_name">Complete Name of Mother <span class="required-asterisk">*</span></label>
                            <input type="text" id="mother_name" name="mother_name" class="form-control" value="{{ old('mother_name', $record->mother_name) }}" required>
                            <span class="error-message" data-for="mother_name"></span>
                        </div>
                        <div class="form-group">
                            <label for="father_name">Name of Father</label>
                            <input type="text" id="father_name" name="father_name" class="form-control" value="{{ old('father_name', $record->father_name) }}">
                        </div>
                        <div class="form-group">
                            <label for="contact">Cell # <span class="required-asterisk">*</span></label>
                            <input type="text" id="contact" name="contact" class="form-control" value="{{ old('contact', $record->contact) }}" required>
                            <span class="error-message" data-for="contact"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nhts_4ps_id">NHTS / 4Ps CCT ID Number</label>
                            <input type="text" id="nhts_4ps_id" name="nhts_4ps_id" class="form-control" value="{{ old('nhts_4ps_id', $record->nhts_4ps_id) }}">
                        </div>
                        <div class="form-group">
                            <label for="phic_id">PHIC ID Number</label>
                            <input type="text" id="phic_id" name="phic_id" class="form-control" value="{{ old('phic_id', $record->phic_id) }}">
                        </div>
                        <div class="form-group">
                            <label for="birth_order">No. of child</label>
                            <input type="number" id="birth_order" name="birth_order" class="form-control" min="1" value="{{ old('birth_order', $record->birth_order) }}">
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
                            <input type="text" id="place_delivery" name="place_delivery" class="form-control" value="{{ old('place_delivery', $record->place_delivery) }}" required>
                            <span class="error-message" data-for="place_delivery"></span>
                        </div>
                        <div class="form-group">
                            <label for="attended_by">Attended by <span class="required-asterisk">*</span></label>
                            <input type="text" id="attended_by" name="attended_by" class="form-control" value="{{ old('attended_by', $record->attended_by) }}" required>
                            <span class="error-message" data-for="attended_by"></span>
                        </div>
                        <div class="form-group">
                            <label for="sex_baby">Sex of baby <span class="required-asterisk">*</span></label>
                            <select id="sex_baby" name="sex_baby" class="form-control" required>
                                <option value="">Select</option>
                                <option value="M" @selected(old('sex_baby', $record->sex_baby) === 'M')>Male</option>
                                <option value="F" @selected(old('sex_baby', $record->sex_baby) === 'F')>Female</option>
                            </select>
                            <span class="error-message" data-for="sex_baby"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tt_status_mother">TT status of Mother <span class="required-asterisk">*</span></label>
                            <input type="text" id="tt_status_mother" name="tt_status_mother" class="form-control" value="{{ old('tt_status_mother', $record->tt_status_mother) }}" required>
                            <span class="error-message" data-for="tt_status_mother"></span>
                        </div>
                        <div class="form-group">
                            <label for="birth_length">Length at birth</label>
                            <input type="text" id="birth_length" name="birth_length" class="form-control" value="{{ old('birth_length', $record->birth_length) }}">
                        </div>
                        <div class="form-group">
                            <label for="birth_weight">Birth weight <span class="required-asterisk">*</span></label>
                            <input type="text" id="birth_weight" name="birth_weight" class="form-control" value="{{ old('birth_weight', $record->birth_weight) }}" required>
                            <span class="error-message" data-for="birth_weight"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="delivery_type">Type of delivery <span class="required-asterisk">*</span></label>
                            <input type="text" id="delivery_type" name="delivery_type" class="form-control" value="{{ old('delivery_type', $record->delivery_type) }}" required>
                            <span class="error-message" data-for="delivery_type"></span>
                        </div>
                        <div class="form-group">
                            <label for="initiated_breastfeeding">Initiated breastfeeding after birth <span class="required-asterisk">*</span></label>
                            <select id="initiated_breastfeeding" name="initiated_breastfeeding" class="form-control" required>
                                <option value="">Select</option>
                                <option value="yes" @selected(old('initiated_breastfeeding', $record->initiated_breastfeeding) === 'yes')>Yes</option>
                                <option value="no" @selected(old('initiated_breastfeeding', $record->initiated_breastfeeding) === 'no')>No</option>
                            </select>
                            <span class="error-message" data-for="initiated_breastfeeding"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="newborn_screening_date">Date of Newborn Screening <span class="required-asterisk">*</span></label>
                            <input type="date" id="newborn_screening_date" name="newborn_screening_date" class="form-control" value="{{ old('newborn_screening_date', optional($record->newborn_screening_date)->format('Y-m-d')) }}" required>
                            <span class="error-message" data-for="newborn_screening_date"></span>
                        </div>
                        <div class="form-group">
                            <label for="newborn_screening_result">Result of Newborn Screening <span class="required-asterisk">*</span></label>
                            <input type="text" id="newborn_screening_result" name="newborn_screening_result" class="form-control" value="{{ old('newborn_screening_result', $record->newborn_screening_result) }}" required>
                            <span class="error-message" data-for="newborn_screening_result"></span>
                        </div>
                        <div class="form-group">
                            <label for="hearing_test_screened">Screened Hearing Test <span class="required-asterisk">*</span></label>
                            <select id="hearing_test_screened" name="hearing_test_screened" class="form-control" required>
                                <option value="">Select</option>
                                <option value="pass" @selected(old('hearing_test_screened', $record->hearing_test_screened) === 'pass')>Pass</option>
                                <option value="fail" @selected(old('hearing_test_screened', $record->hearing_test_screened) === 'fail')>Fail</option>
                            </select>
                            <span class="error-message" data-for="hearing_test_screened"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="vit_k">Vit. K <span class="required-asterisk">*</span></label>
                            <select id="vit_k" name="vit_k" class="form-control" required>
                                <option value="">Select</option>
                                <option value="given" @selected(old('vit_k', $record->vit_k) === 'given')>Given</option>
                                <option value="not_given" @selected(old('vit_k', $record->vit_k) === 'not_given')>Not Given</option>
                            </select>
                            <span class="error-message" data-for="vit_k"></span>
                        </div>
                        <div class="form-group">
                            <label for="bcg">BCG <span class="required-asterisk">*</span></label>
                            <select id="bcg" name="bcg" class="form-control" required>
                                <option value="">Select</option>
                                <option value="given" @selected(old('bcg', $record->bcg) === 'given')>Given</option>
                                <option value="not_given" @selected(old('bcg', $record->bcg) === 'not_given')>Not Given</option>
                            </select>
                            <span class="error-message" data-for="bcg"></span>
                        </div>
                        <div class="form-group">
                            <label for="hepa_b_24h">Hepa B birth dose within 24 hrs <span class="required-asterisk">*</span></label>
                            <select id="hepa_b_24h" name="hepa_b_24h" class="form-control" required>
                                <option value="">Select</option>
                                <option value="yes" @selected(old('hepa_b_24h', $record->hepa_b_24h) === 'yes')>Yes</option>
                                <option value="no" @selected(old('hepa_b_24h', $record->hepa_b_24h) === 'no')>No</option>
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

                    <div id="nipVisitsContainer">
                        @forelse($record->visits as $index => $visit)
                            <div class="visit-box" data-index="{{ $index }}" data-visit-id="{{ $visit->id }}">
                                <div class="visit-box-header">
                                    <h4>Visit {{ $index + 1 }}</h4>
                                    <button type="button" class="btn btn-link remove-visit" data-index="{{ $index }}" {{ $index === 0 ? 'disabled' : '' }}>Remove</button>
                                </div>
                                <div class="form-row small-row">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <input type="date" name="visits[{{ $index }}][date]" class="form-control" value="{{ optional($visit->visit_date)->format('Y-m-d') }}">
                                        <input type="hidden" name="visits[{{ $index }}][id]" value="{{ $visit->id }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Age in months</label>
                                        <input type="text" name="visits[{{ $index }}][age]" class="form-control" value="{{ $visit->age_months }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Weight</label>
                                        <input type="text" name="visits[{{ $index }}][weight]" class="form-control" value="{{ $visit->weight }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Length for age</label>
                                        <input type="text" name="visits[{{ $index }}][length]" class="form-control" value="{{ $visit->length }}">
                                    </div>
                                </div>
                                <div class="form-row small-row">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <input type="text" name="visits[{{ $index }}][status]" class="form-control" value="{{ $visit->status }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Breastfeeding</label>
                                        <select name="visits[{{ $index }}][breast]" class="form-control">
                                            <option value="">Select</option>
                                            <option value="yes" @selected($visit->breastfeeding === 'yes')>Yes</option>
                                            <option value="no" @selected($visit->breastfeeding === 'no')>No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Temperature</label>
                                        <input type="text" name="visits[{{ $index }}][temp]" class="form-control" value="{{ $visit->temperature }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Vaccine</label>
                                        <input type="text" name="visits[{{ $index }}][vaccine]" class="form-control" value="{{ $visit->vaccine }}">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="visit-box" data-index="0">
                                <div class="visit-box-header">
                                    <h4>Visit 1</h4>
                                    <button type="button" class="btn btn-link remove-visit" data-index="0" disabled>Remove</button>
                                </div>
                                <div class="form-row small-row">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <input type="date" name="visits[0][date]" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Age in months</label>
                                        <input type="text" name="visits[0][age]" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Weight</label>
                                        <input type="text" name="visits[0][weight]" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Length for age</label>
                                        <input type="text" name="visits[0][length]" class="form-control">
                                    </div>
                                </div>
                                <div class="form-row small-row">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <input type="text" name="visits[0][status]" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Breastfeeding</label>
                                        <select name="visits[0][breast]" class="form-control">
                                            <option value="">Select</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Temperature</label>
                                        <input type="text" name="visits[0][temp]" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Vaccine</label>
                                        <input type="text" name="visits[0][vaccine]" class="form-control">
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Record</button>
                    <a href="{{ route('health-programs.nip-view') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('.patient-form');
            const alertBox = document.getElementById('form-alert');
            const visitsContainer = document.getElementById('nipVisitsContainer');
            const addVisitBtn = document.getElementById('addVisitBtn');
            let visitCount = Math.max({{ optional($record->visits)->count() ?? 0 }}, 1);

            const visitTemplate = (index) => `
                <div class="visit-box" data-index="${index}">
                    <div class="visit-box-header">
                        <h4>Visit ${index + 1}</h4>
                        <button type="button" class="btn btn-link remove-visit" data-index="${index}">Remove</button>
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

