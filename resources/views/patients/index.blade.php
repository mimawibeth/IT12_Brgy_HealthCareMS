{{-- Patient List Page: Displays all registered patients --}}
@extends('layouts.app')

@section('title', 'Patient List')
@section('page-title', 'Patient List')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Search and Filter Section -->
        <div
            style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: .3rem; flex-wrap: wrap;">
            <form method="GET" action="{{ route('patients.index') }}" id="patientFilterForm" class="filters"
                style="flex: 1; display: flex; gap: 12px; align-items: center;">
                <input type="text" name="search" id="searchInput" placeholder="Search patients..." class="search-input"
                    value="{{ request('search') }}" style="flex: 1; min-width: 300px;">

                <select name="gender" id="genderFilter" class="filter-select">
                    <option value="">All Gender</option>
                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                </select>

                <select name="age_group" id="ageGroupFilter" class="filter-select">
                    <option value="">All Ages</option>
                    <option value="child" {{ request('age_group') === 'child' ? 'selected' : '' }}>Children (0-12)</option>
                    <option value="teen" {{ request('age_group') === 'teen' ? 'selected' : '' }}>Teenagers (13-19)</option>
                    <option value="adult" {{ request('age_group') === 'adult' ? 'selected' : '' }}>Adults (20-59)</option>
                    <option value="senior" {{ request('age_group') === 'senior' ? 'selected' : '' }}>Senior (60+)</option>
                </select>

                <button type="button" id="clearFiltersBtn" class="btn btn-secondary"
                    style="padding: 10px 15px !important; font-size: 14px; font-weight: normal; line-height: normal;">
                    <i class="bi bi-x-circle"></i> Clear
                </button>

                <a href="{{ route('patients.create') }}" class="btn btn-primary"
                    style="padding: 10px 15px !important; font-size: 14px; white-space: nowrap; font-weight: normal; line-height: normal; display: inline-flex; align-items: center;">
                    <i class="bi bi-person-plus"></i> Add New Patient
                </a>
            </form>
        </div>

        <!-- Patients Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date Registered</th>
                        <th>Patient No.</th>
                        <th>Name</th>
                        <th>Sex</th>
                        <th>Birthday</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($patients as $patient)
                        <tr>
                            <td>
                                {{ $patient->dateRegistered ? \Carbon\Carbon::parse($patient->dateRegistered)->format('M d, Y') : '' }}
                            </td>
                            <td>{{ $patient->patientNo }}</td>
                            <td>{{ $patient->name }}</td>
                            <td>{{ $patient->sex }}</td>
                            <td>
                                {{ $patient->birthday ? \Carbon\Carbon::parse($patient->birthday)->format('M d, Y') : '' }}
                            </td>
                            <td>{{ $patient->address }}</td>
                            <td>{{ $patient->contactNumber }}</td>
                            <td class="actions">
                                <a href="#" class="btn-action btn-view" data-patient-id="{{ $patient->PatientID }}">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('patients.edit', $patient->PatientID) }}" class="btn-action btn-edit">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                @if(auth()->user()->role === 'super_admin')
                                    <a href="javascript:void(0)" class="btn-action btn-delete"
                                        onclick="openDeleteModal({{ $patient->PatientID }}, '{{ $patient->name }}')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center;">No patients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($patients->hasPages())
            <div class="pagination">
                @if($patients->onFirstPage())
                    <button class="btn-page" disabled>« Previous</button>
                @else
                    <a class="btn-page" href="{{ $patients->previousPageUrl() }}">« Previous</a>
                @endif

                @php
                    $start = max(1, $patients->currentPage() - 2);
                    $end = min($patients->lastPage(), $patients->currentPage() + 2);
                @endphp

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page === $patients->currentPage())
                        <span class="btn-page active">{{ $page }}</span>
                    @else
                        <a class="btn-page" href="{{ $patients->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                <span class="page-info">
                    Page {{ $patients->currentPage() }} of {{ $patients->lastPage() }} ({{ $patients->total() }} total patients)
                </span>

                @if($patients->hasMorePages())
                    <a class="btn-page" href="{{ $patients->nextPageUrl() }}">Next »</a>
                @else
                    <button class="btn-page" disabled>Next »</button>
                @endif
            </div>
        @endif

        <!-- Patient View Modal -->
        <div id="patientViewModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Patient Details</h3>
                    <span class="close-modal" onclick="closePatientViewModal()">&times;</span>
                </div>

                <div class="modal-body">
                    <!-- Section 1: Patient Information (from ITR) -->
                    <div class="form-section section-patient-info">
                        <h4 class="section-header"><span class="section-indicator"></span>Patient Information</h4>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label>Full Name</label>
                                <p id="modalName"></p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Patient No.</label>
                                <p id="modalPatientNo"></p>
                            </div>
                            <div class="form-group">
                                <label>Sex</label>
                                <p id="modalSex"></p>
                            </div>
                            <div class="form-group">
                                <label>Birthday</label>
                                <p id="modalBirthday"></p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label>Address</label>
                                <p id="modalAddress"></p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Contact Number</label>
                                <p id="modalContactNumber"></p>
                            </div>
                            <div class="form-group">
                                <label>Date Registered</label>
                                <p id="modalDateRegistered"></p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>NHTS ID No.</label>
                                <p id="modalNhtsIdNo"></p>
                            </div>
                            <div class="form-group">
                                <label>PWD ID No.</label>
                                <p id="modalPwdIdNo"></p>
                            </div>
                            <div class="form-group">
                                <label>PHIC ID No.</label>
                                <p id="modalPhicIdNo"></p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>4Ps/CCT ID No.</label>
                                <p id="modalFourPsCctIdNo"></p>
                            </div>
                            <div class="form-group">
                                <label>Ethnic Group</label>
                                <p id="modalEthnicGroup"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Latest Health Assessment (Initial Visit Assessment) -->
                    <div class="form-section section-assessment">
                        <h4 class="section-header"><span class="section-indicator"></span>Initial Visit Assessment -
                            Monitoring Parameters</h4>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Assessment Date</label>
                                <p id="modalAssessmentDate"></p>
                            </div>
                            <div class="form-group">
                                <label>Age</label>
                                <p id="modalAssessmentAge"></p>
                            </div>
                            <div class="form-group">
                                <label>CVD Risk</label>
                                <p id="modalAssessmentCvdRisk"></p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>BP Systolic (mmHg)</label>
                                <p id="modalAssessmentBpSystolic"></p>
                            </div>
                            <div class="form-group">
                                <label>BP Diastolic (mmHg)</label>
                                <p id="modalAssessmentBpDiastolic"></p>
                            </div>
                            <div class="form-group">
                                <label>Weight (kg)</label>
                                <p id="modalAssessmentWt"></p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Height (cm)</label>
                                <p id="modalAssessmentHt"></p>
                            </div>
                            <div class="form-group">
                                <label>FBS/RBS (mg/dL)</label>
                                <p id="modalAssessmentFbsRbs"></p>
                            </div>
                            <div class="form-group">
                                <label>Lipid Profile</label>
                                <p id="modalAssessmentLipidProfile"></p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label>Chief Complaint</label>
                                <p id="modalAssessmentChiefComplaint"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closePatientViewModal()">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Patient Modal (Super Admin Only) -->
        @if(auth()->user()->role === 'super_admin')
            <div class="modal" id="deletePatientModal" style="display:none;">
                <div class="modal-content" style="max-width: 500px;">
                    <div class="modal-header" style="background: #dc2626; color: white;">
                        <h3 style="margin: 0;"><i class="bi bi-exclamation-triangle-fill"></i> Delete Patient</h3>
                        <span class="close-modal" onclick="closeDeleteModal()"
                            style="color: white; cursor: pointer; font-size: 28px;">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p style="margin-bottom: 20px;">Are you sure you want to permanently delete the patient record for
                            <strong id="deletePatientName"></strong>?</p>

                        <div
                            style="background: #fee2e2; padding: 15px; border-radius: 4px; border-left: 4px solid #dc2626; margin-bottom: 20px;">
                            <p style="margin: 0 0 10px 0; color: #991b1b;"><strong><i class="bi bi-exclamation-triangle"
                                        style="color: #dc2626;"></i> WARNING:</strong> This action cannot be undone!</p>
                            <p style="margin: 0; color: #991b1b; font-size: 13px;">All patient data including assessments,
                                medical history, and related records will be permanently deleted.</p>
                        </div>

                        <form method="POST" id="deletePatientForm" action="">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" id="deletePatientId" name="patient_id">
                            <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
                                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                                <button type="submit" class="btn btn-primary" style="background: #dc2626;"
                                    onclick="this.form.action='/patients/' + document.getElementById('deletePatientId').value">
                                    <i class="bi bi-trash"></i> Delete Patient
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <script>
        // Auto-submit filter form on input/change
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('patientFilterForm');
            const searchInput = document.getElementById('searchInput');
            const genderFilter = document.getElementById('genderFilter');
            const ageGroupFilter = document.getElementById('ageGroupFilter');
            const clearBtn = document.getElementById('clearFiltersBtn');

            let searchTimeout;

            // Auto-submit on search input with debounce
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    form.submit();
                }, 500);
            });

            // Auto-submit on filter change
            genderFilter.addEventListener('change', function () {
                form.submit();
            });

            ageGroupFilter.addEventListener('change', function () {
                form.submit();
            });

            // Clear all filters
            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                genderFilter.value = '';
                ageGroupFilter.value = '';
                form.submit();
            });
        });

        function openPatientViewModal(patientId) {
            fetch('{{ route('patients.index') }}/' + patientId)
                .then(response => response.json())
                .then(data => {
                    const patient = data.patient || data;

                    const formatDate = (value) => {
                        if (!value) return '';
                        const d = new Date(value);
                        if (isNaN(d.getTime())) {
                            return value;
                        }
                        return d.toLocaleDateString('en-US', {
                            month: 'short',
                            day: '2-digit',
                            year: 'numeric'
                        });
                    };

                    document.getElementById('modalDateRegistered').textContent = formatDate(patient.dateRegistered);
                    document.getElementById('modalPatientNo').textContent = patient.patientNo || '';
                    document.getElementById('modalSex').textContent = patient.sex || '';
                    document.getElementById('modalName').textContent = patient.name || '';
                    document.getElementById('modalBirthday').textContent = formatDate(patient.birthday);
                    document.getElementById('modalContactNumber').textContent = patient.contactNumber || '';
                    document.getElementById('modalAddress').textContent = patient.address || '';
                    document.getElementById('modalNhtsIdNo').textContent = patient.nhtsIdNo || '';
                    document.getElementById('modalPwdIdNo').textContent = patient.pwdIdNo || '';
                    document.getElementById('modalPhicIdNo').textContent = patient.phicIdNo || '';
                    document.getElementById('modalFourPsCctIdNo').textContent = patient.fourPsCctIdNo || '';
                    document.getElementById('modalEthnicGroup').textContent = patient.ethnicGroup || '';

                    const assessments = data.assessments || patient.assessments || [];
                    const latest = assessments.length ? assessments[assessments.length - 1] : null;

                    document.getElementById('modalAssessmentDate').textContent = latest ? formatDate(latest.date) : '';
                    document.getElementById('modalAssessmentAge').textContent = latest && latest.age ? latest.age : '';
                    document.getElementById('modalAssessmentCvdRisk').textContent = latest && latest.cvdRisk ? latest.cvdRisk : '';
                    document.getElementById('modalAssessmentBpSystolic').textContent = latest && latest.bpSystolic ? latest.bpSystolic : '';
                    document.getElementById('modalAssessmentBpDiastolic').textContent = latest && latest.bpDiastolic ? latest.bpDiastolic : '';
                    document.getElementById('modalAssessmentWt').textContent = latest && latest.wt ? latest.wt : '';
                    document.getElementById('modalAssessmentHt').textContent = latest && latest.ht ? latest.ht : '';
                    document.getElementById('modalAssessmentFbsRbs').textContent = latest && latest.fbsRbs ? latest.fbsRbs : '';
                    document.getElementById('modalAssessmentLipidProfile').textContent = latest && latest.lipidProfile ? latest.lipidProfile : '';
                    document.getElementById('modalAssessmentChiefComplaint').textContent = latest && latest.chiefComplaint ? latest.chiefComplaint : '';

                    document.getElementById('patientViewModal').style.display = 'flex';
                });
        }

        function closePatientViewModal() {
            document.getElementById('patientViewModal').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const viewButtons = document.querySelectorAll('.btn-view');

            viewButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const patientId = this.getAttribute('data-patient-id');
                    if (patientId) {
                        openPatientViewModal(patientId);
                    }
                });
            });

            window.addEventListener('click', function (event) {
                const modal = document.getElementById('patientViewModal');
                if (event.target === modal) {
                    closePatientViewModal();
                }
            });
        });

        // Delete Modal Functions
        function openDeleteModal(patientId, patientName) {
            document.getElementById('deletePatientId').value = patientId;
            document.getElementById('deletePatientName').textContent = patientName;
            document.getElementById('deletePatientModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deletePatientModal').style.display = 'none';
        }

        window.addEventListener('click', function (event) {
            const deleteModal = document.getElementById('deletePatientModal');
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        });
    </script>
@endsection