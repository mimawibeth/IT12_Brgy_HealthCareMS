@extends('layouts.app')

@section('title', 'Edit Prenatal Record')
@section('page-title', 'Edit Prenatal Record')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="form-container">
            <h2 class="form-title">Editing Prenatal Record #{{ $record->record_no ?? ('PT-' . str_pad($record->id, 3, '0', STR_PAD_LEFT)) }}</h2>

            <div class="form-section section-patient-info">
                <h3 class="section-header"><span class="section-indicator"></span>Mother Summary</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" value="{{ $record->mother_name }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>LMP</label>
                        <input type="date" class="form-control" value="{{ optional($record->lmp)->format('Y-m-d') }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>EDC</label>
                        <input type="date" class="form-control" value="{{ optional($record->edc)->format('Y-m-d') }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Contact #</label>
                        <input type="text" class="form-control" value="{{ $record->cell }}" readonly>
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
                                <th>Trimester</th>
                                <th>Risk Code</th>
                                <th>Assessment</th>
                                <th>Plan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($record->visits as $index => $visit)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ optional($visit->date)->format('Y-m-d') }}</td>
                                    <td>{{ $visit->trimester }}</td>
                                    <td>{{ $visit->risk }}</td>
                                    <td>{{ $visit->assessment }}</td>
                                    <td>{{ $visit->plan }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center;">No visits recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-section section-assessment">
                <div class="section-header section-between">
                    <span><span class="section-indicator"></span>Add Follow-up Visit (SOAP)</span>
                    <button type="button" class="btn btn-outline" id="addFollowUpBtn">+ Add Visit</button>
                </div>
                <p class="form-note">Capture S/O/A/P details for follow-up visits. Previously saved visits appear in the
                    table above.</p>

                <form id="followUpForm" method="POST" action="{{ route('health-programs.prenatal-visits-store', $record) }}">
                    @csrf
                    <div id="followUpContainer"></div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Visits</button>
                        <a href="{{ route('health-programs.prenatal-view') }}" class="btn btn-secondary">Back to Records</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                            <h4>Visit ${index + 3}</h4>
                            <button type="button" class="btn btn-link remove-follow-up" data-index="${index}">Remove</button>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Date <span class="required-asterisk">*</span></label>
                                <input type="date" name="new_visits[${index}][date]" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Trimester</label>
                                <select name="new_visits[${index}][trimester]" class="form-control">
                                    <option value="">Select</option>
                                    <option value="1st">1st</option>
                                    <option value="2nd">2nd</option>
                                    <option value="3rd">3rd</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Risk Code</label>
                                <input type="text" name="new_visits[${index}][risk_code]" class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label>S – Subjective</label>
                                <textarea name="new_visits[${index}][subjective]" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="soap-label">O – Objective</div>
                        <div class="form-row small-row">
                            <div class="form-group"><label>AOG</label><input type="text" name="new_visits[${index}][aog]" class="form-control"></div>
                            <div class="form-group"><label>WT</label><input type="text" name="new_visits[${index}][wt]" class="form-control"></div>
                            <div class="form-group"><label>HT</label><input type="text" name="new_visits[${index}][ht]" class="form-control"></div>
                        </div>
                        <div class="form-row small-row">
                            <div class="form-group"><label>B/P</label><input type="text" name="new_visits[${index}][bp]" class="form-control"></div>
                            <div class="form-group"><label>PR</label><input type="text" name="new_visits[${index}][pr]" class="form-control"></div>
                            <div class="form-group"><label>FH</label><input type="text" name="new_visits[${index}][fh]" class="form-control"></div>
                        </div>
                        <div class="form-row small-row">
                            <div class="form-group"><label>FHT</label><input type="text" name="new_visits[${index}][fht]" class="form-control"></div>
                            <div class="form-group"><label>Pres.</label><input type="text" name="new_visits[${index}][presentation]" class="form-control"></div>
                            <div class="form-group"><label>BMI</label><input type="text" name="new_visits[${index}][bmi]" class="form-control"></div>
                        </div>
                        <div class="form-row small-row">
                            <div class="form-group"><label>RR</label><input type="text" name="new_visits[${index}][rr]" class="form-control"></div>
                            <div class="form-group"><label>HR</label><input type="text" name="new_visits[${index}][hr]" class="form-control"></div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label>A – Assessment</label>
                                <textarea name="new_visits[${index}][assessment]" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label>P – Plan</label>
                                <textarea name="new_visits[${index}][plan]" class="form-control" rows="2"></textarea>
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

            container.addEventListener('click', (event) => {
                if (event.target.classList.contains('remove-follow-up')) {
                    event.target.closest('.visit-box').remove();
                }
            });

            addBtn.addEventListener('click', addVisitCard);
        });
    </script>
@endpush