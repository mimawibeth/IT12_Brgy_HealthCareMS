@extends('layouts.app')

@section('title', 'Edit Patient')
@section('page-title', 'Edit Patient Record')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="form-container">
            <h2 class="form-title">Editing Patient: {{ $patient->name }} ({{ $patient->patientNo }})</h2>

            <div class="form-section section-patient-info">
                <h3 class="section-header"><span class="section-indicator"></span>Patient Summary</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Patient No.</label>
                        <input type="text" class="form-control" value="{{ $patient->patientNo }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" value="{{ $patient->name }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Sex</label>
                        <input type="text" class="form-control" value="{{ $patient->sex }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Birthday</label>
                        <input type="date" class="form-control" value="{{ optional($patient->birthday)->format('Y-m-d') }}" readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control" value="{{ $patient->address }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" class="form-control" value="{{ $patient->contactNumber }}" readonly>
                    </div>
                </div>
            </div>

            <div class="form-section section-history">
                <h3 class="section-header"><span class="section-indicator"></span>Previous Visit Assessments</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Visit #</th>
                                <th>Date</th>
                                <th>Age</th>
                                <th>CVD Risk</th>
                                <th>BP (Systolic/Diastolic)</th>
                                <th>Weight</th>
                                <th>Height</th>
                                <th>Chief Complaint</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($patient->assessments as $index => $assessment)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ optional($assessment->date)->format('Y-m-d') }}</td>
                                    <td>
                                        @php
                                            $age = null;
                                            if ($patient->birthday) {
                                                $age = \Carbon\Carbon::parse($patient->birthday)->age;
                                            }
                                        @endphp
                                        {{ $age ?? ($assessment->age ?? '—') }}
                                    </td>
                                    <td>{{ $assessment->cvdRisk ?? '—' }}</td>
                                    <td>{{ $assessment->bpSystolic ?? '—' }}/{{ $assessment->bpDiastolic ?? '—' }}</td>
                                    <td>{{ $assessment->wt ?? '—' }}</td>
                                    <td>{{ $assessment->ht ?? '—' }}</td>
                                    <td>{{ Str::limit($assessment->chiefComplaint ?? '—', 30) }}</td>
                                    <td>
                                        <a href="javascript:void(0)" class="btn-action btn-view view-assessment" 
                                           data-assessment-id="{{ $assessment->id }}"
                                           data-patient-id="{{ $patient->PatientID }}">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" style="text-align:center;">No assessments recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-section section-assessment">
                <div class="section-header section-between">
                    <span><span class="section-indicator"></span>Add New Visit Assessment</span>
                    <button type="button" class="btn btn-outline" id="addAssessmentBtn">+ Add Assessment</button>
                </div>
                <p class="form-note">Add new visit assessments for this patient. Previously saved assessments appear in the table above.</p>

                <form id="assessmentForm" method="POST" action="{{ route('patients.assessments.store', $patient->PatientID) }}">
                    @csrf
                    <div id="assessmentContainer"></div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Assessments</button>
                        <a href="{{ route('patients.index') }}" class="btn btn-secondary">Back to Patient List</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assessment View Modal -->
    <div id="assessmentViewModal" class="modal" style="display:none;">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>Visit Assessment Details</h3>
                <span class="close-modal" id="closeAssessmentModal">&times;</span>
            </div>
            <div class="modal-body" id="assessmentModalBody">
                <div class="loading-spinner" style="text-align:center; padding: 2rem;">
                    <p>Loading...</p>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" id="closeAssessmentModalBtn">Close</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('assessmentContainer');
            const addBtn = document.getElementById('addAssessmentBtn');
            let assessmentCount = 0;

            const assessmentTemplate = (index) => `
                <div class="visit-box" data-index="${index}" style="border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 8px; background-color: #f9f9f9;">
                    <div class="visit-box-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #2f6d7e;">
                        <h4 style="margin: 0; color: #2f6d7e;">Visit Assessment ${index + 1}</h4>
                        <button type="button" class="btn btn-link remove-assessment" data-index="${index}" style="color: #e74c3c; text-decoration: none; padding: 5px 10px;">Remove</button>
                    </div>

                    <div style="display: flex; gap: 20px;">
                        <!-- Monitoring Parameters (Left Column) -->
                        <div style="flex: 1;">
                            <h4 style="margin-bottom: 10px; color: #2f6d7e;">Monitoring Parameters</h4>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Date <span class="required-asterisk">*</span></label>
                                    <input type="date" name="assessments[${index}][date]" class="form-control assessment-date" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Age</label>
                                    <input type="text" name="assessments[${index}][age]" class="form-control assessment-age" readonly>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>CVD Risk</label>
                                    <input type="text" name="assessments[${index}][cvd_risk]" class="form-control" placeholder="e.g., Low, Medium, High">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>BP: (Systolic)</label>
                                    <input type="text" name="assessments[${index}][bp_systolic]" class="form-control" placeholder="mmHg">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>BP: (Diastolic)</label>
                                    <input type="text" name="assessments[${index}][bp_diastolic]" class="form-control" placeholder="mmHg">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Wt (Weight)</label>
                                    <input type="text" name="assessments[${index}][wt]" class="form-control" placeholder="kg">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Ht (Height)</label>
                                    <input type="text" name="assessments[${index}][ht]" class="form-control" placeholder="cm">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>FBS/RBS</label>
                                    <input type="text" name="assessments[${index}][fbs_rbs]" class="form-control">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Lipid Profile</label>
                                    <input type="text" name="assessments[${index}][lipid_profile]" class="form-control">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Urine Ketones</label>
                                    <input type="text" name="assessments[${index}][urine_ketones]" class="form-control">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Urine Protein</label>
                                    <input type="text" name="assessments[${index}][urine_protein]" class="form-control">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Foot Check</label>
                                    <input type="text" name="assessments[${index}][foot_check]" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Chief Complaint / History / Physical Examination (Middle Column) -->
                        <div style="flex: 1;">
                            <h4 style="margin-bottom: 10px; color: #2f6d7e;">Chief Complaint / Diagnosis</h4>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Chief Complaint</label>
                                    <textarea name="assessments[${index}][chief_complaint]" class="form-control" rows="3" placeholder="Patient's main complaint"></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>History / Physical Examination</label>
                                    <textarea name="assessments[${index}][history_physical]" class="form-control" rows="7" placeholder="Medical history and physical examination findings"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Management (Right Column) -->
                        <div style="flex: 1;">
                            <h4 style="margin-bottom: 10px; color: #2f6d7e;">Management</h4>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Management Plan</label>
                                    <textarea name="assessments[${index}][management]" class="form-control" rows="12" placeholder="Treatment plan, medications, follow-up instructions"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const addAssessmentCard = () => {
                const wrapper = document.createElement('div');
                wrapper.innerHTML = assessmentTemplate(assessmentCount);
                container.appendChild(wrapper.firstElementChild);
                
                // Calculate age based on today's date and patient's birthday
                const ageInput = container.querySelector(`[data-index="${assessmentCount}"] .assessment-age`);
                if (ageInput) {
                    const patientBirthday = '{{ optional($patient->birthday)->format("Y-m-d") }}';
                    if (patientBirthday) {
                        const today = new Date();
                        const birthday = new Date(patientBirthday);
                        if (!isNaN(birthday.getTime())) {
                            let age = today.getFullYear() - birthday.getFullYear();
                            const monthDiff = today.getMonth() - birthday.getMonth();
                            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
                                age--;
                            }
                            ageInput.value = age >= 0 ? age : '';
                        }
                    }
                }
                
                assessmentCount++;
            };

            container.addEventListener('click', (event) => {
                if (event.target.classList.contains('remove-assessment')) {
                    event.target.closest('.visit-box').remove();
                }
            });

            addBtn.addEventListener('click', addAssessmentCard);

            // View Assessment Modal
            const modal = document.getElementById('assessmentViewModal');
            const modalBody = document.getElementById('assessmentModalBody');
            const closeModal = document.getElementById('closeAssessmentModal');
            const closeModalBtn = document.getElementById('closeAssessmentModalBtn');

            document.querySelectorAll('.view-assessment').forEach(button => {
                button.addEventListener('click', async function() {
                    const assessmentId = this.getAttribute('data-assessment-id');
                    const patientId = this.getAttribute('data-patient-id');

                    modal.style.display = 'flex';
                    modalBody.innerHTML = '<div style="text-align:center; padding: 2rem;"><p>Loading...</p></div>';

                    try {
                        const response = await fetch('{{ url("/patients") }}/' + patientId);
                        const data = await response.json();
                        const assessments = data.assessments || [];
                        const assessment = assessments.find(a => a.id == assessmentId);

                        if (assessment) {
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

                            // Calculate current age
                            const patientBirthday = '{{ optional($patient->birthday)->format("Y-m-d") }}';
                            let currentAge = 'N/A';
                            if (patientBirthday) {
                                const today = new Date();
                                const birthday = new Date(patientBirthday);
                                if (!isNaN(birthday.getTime())) {
                                    let age = today.getFullYear() - birthday.getFullYear();
                                    const monthDiff = today.getMonth() - birthday.getMonth();
                                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
                                        age--;
                                    }
                                    currentAge = age >= 0 ? age : 'N/A';
                                }
                            }

                            modalBody.innerHTML = `
                                <div class="form-section section-patient-info">
                                    <h3 class="section-header"><span class="section-indicator"></span>Assessment Information</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Assessment Date:</strong></label>
                                            <p>${formatDate(assessment.date)}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Patient Age (Current):</strong></label>
                                            <p>${currentAge} years old</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>CVD Risk:</strong></label>
                                            <p>${assessment.cvdRisk || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section section-history">
                                    <h3 class="section-header"><span class="section-indicator"></span>Monitoring Parameters</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>BP Systolic:</strong></label>
                                            <p>${assessment.bpSystolic || 'N/A'} ${assessment.bpSystolic ? 'mmHg' : ''}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>BP Diastolic:</strong></label>
                                            <p>${assessment.bpDiastolic || 'N/A'} ${assessment.bpDiastolic ? 'mmHg' : ''}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Weight:</strong></label>
                                            <p>${assessment.wt || 'N/A'} ${assessment.wt ? 'kg' : ''}</p>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Height:</strong></label>
                                            <p>${assessment.ht || 'N/A'} ${assessment.ht ? 'cm' : ''}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>FBS/RBS:</strong></label>
                                            <p>${assessment.fbsRbs || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Lipid Profile:</strong></label>
                                            <p>${assessment.lipidProfile || 'N/A'}</p>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Urine Ketones:</strong></label>
                                            <p>${assessment.urineKetones || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Urine Protein:</strong></label>
                                            <p>${assessment.urineProtein || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Foot Check:</strong></label>
                                            <p>${assessment.footCheck || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section section-assessment">
                                    <h3 class="section-header"><span class="section-indicator"></span>Chief Complaint / Diagnosis</h3>
                                    <div class="form-row">
                                        <div class="form-group full-width">
                                            <label><strong>Chief Complaint:</strong></label>
                                            <p style="white-space: pre-wrap;">${assessment.chiefComplaint || 'N/A'}</p>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group full-width">
                                            <label><strong>History / Physical Examination:</strong></label>
                                            <p style="white-space: pre-wrap;">${assessment.historyPhysical || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section section-screening">
                                    <h3 class="section-header"><span class="section-indicator"></span>Management</h3>
                                    <div class="form-row">
                                        <div class="form-group full-width">
                                            <label><strong>Management Plan:</strong></label>
                                            <p style="white-space: pre-wrap;">${assessment.management || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        } else {
                            modalBody.innerHTML = '<div style="text-align:center; padding: 2rem; color: red;"><p>Assessment not found.</p></div>';
                        }
                    } catch (error) {
                        modalBody.innerHTML = '<div style="text-align:center; padding: 2rem; color: red;"><p>Error loading assessment details.</p></div>';
                    }
                });
            });

            closeModal.addEventListener('click', () => modal.style.display = 'none');
            closeModalBtn.addEventListener('click', () => modal.style.display = 'none');
            window.addEventListener('click', (event) => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
@endpush

