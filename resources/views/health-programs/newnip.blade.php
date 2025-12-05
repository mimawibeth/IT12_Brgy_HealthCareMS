@extends('layouts.app')

@section('title', 'Immunization')
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
                    @php
                        $recordsList = $records ?? collect();
                    @endphp
                    @forelse($recordsList as $item)
                        @php
                            $lastVisit = $item->visits->sortByDesc('visit_date')->first();
                            $status = $lastVisit->status ?? 'N/A';
                        @endphp
                        <tr>
                            <td>{{ $item->record_no ?? ('NIP-' . str_pad($item->id, 3, '0', STR_PAD_LEFT)) }}</td>
                            <td>{{ $item->child_name }}</td>
                            <td>{{ optional($item->dob)->format('M d, Y') }}</td>
                            <td>{{ $item->mother_name }}</td>
                            <td>{{ optional(optional($lastVisit)->visit_date)->format('M d, Y') ?? '—' }}</td>
                            <td>{{ $status }}</td>
                            <td class="actions">
                                <a href="javascript:void(0)" class="btn-action btn-view view-nip" data-id="{{ $item->id }}"
                                    data-record="{{ $item->record_no ?? ('NIP-' . str_pad($item->id, 3, '0', STR_PAD_LEFT)) }}">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('health-programs.new-nip-edit', $item) }}" class="btn-action btn-edit">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <div class="modal" id="nipViewModal" style="display:none;">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>Immunization Record Details</h3>
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
                const openBtn = document.getElementById('openNewNipForm');
                const modal = document.getElementById('nipViewModal');
                const closeModal = document.getElementById('closeNipModal');

                openBtn.addEventListener('click', () => {
                    window.location.href = '{{ route("health-programs.new-nip-create") }}';
                });

                // View button functionality
                document.querySelectorAll('.view-nip').forEach(button => {
                    button.addEventListener('click', async function () {
                        const recordId = this.getAttribute('data-record');
                        const recordDbId = this.getAttribute('data-id');

                        modal.style.display = 'flex';
                        const modalBody = document.getElementById('nipModalBody');
                        modalBody.innerHTML = '<div style="text-align:center; padding: 2rem;"><p>Loading...</p></div>';

                        try {
                            const response = await fetch(`/health-programs/new-immunization/${recordDbId}`);
                            if (!response.ok) {
                                throw new Error('Failed to fetch record');
                            }
                            const data = await response.json();

                            modalBody.innerHTML = `
                                <div class="form-section section-patient-info">
                                    <h3 class="section-header"><span class="section-indicator"></span>Child Information</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Record #:</strong></label>
                                            <p>${data.record_no || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Child Name:</strong></label>
                                            <p>${data.child_name || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Date of Birth:</strong></label>
                                            <p>${data.dob || 'N/A'}</p>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Sex:</strong></label>
                                            <p>${data.sex_baby || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Birth Weight:</strong></label>
                                            <p>${data.birth_weight || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Birth Length:</strong></label>
                                            <p>${data.birth_length || 'N/A'}</p>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Birth Order:</strong></label>
                                            <p>${data.birth_order || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Delivery Type:</strong></label>
                                            <p>${data.delivery_type || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Place of Delivery:</strong></label>
                                            <p>${data.place_delivery || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section section-history">
                                    <h3 class="section-header"><span class="section-indicator"></span>Family Information</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Mother's Name:</strong></label>
                                            <p>${data.mother_name || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Father's Name:</strong></label>
                                            <p>${data.father_name || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Contact:</strong></label>
                                            <p>${data.contact || 'N/A'}</p>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Address:</strong></label>
                                            <p>${data.address || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Attended By:</strong></label>
                                            <p>${data.attended_by || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section section-screening">
                                    <h3 class="section-header"><span class="section-indicator"></span>Initial Immunization & Screening</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>TT Status (Mother):</strong></label>
                                            <p>${data.tt_status_mother || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Vitamin K:</strong></label>
                                            <p>${data.vit_k || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>BCG:</strong></label>
                                            <p>${data.bcg || 'N/A'}</p>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Hepa B (24h):</strong></label>
                                            <p>${data.hepa_b_24h || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Initiated Breastfeeding:</strong></label>
                                            <p>${data.initiated_breastfeeding || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Newborn Screening Date:</strong></label>
                                            <p>${data.newborn_screening_date || 'N/A'}</p>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Newborn Screening Result:</strong></label>
                                            <p>${data.newborn_screening_result || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Hearing Test Screened:</strong></label>
                                            <p>${data.hearing_test_screened || 'N/A'}</p>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>NHTS/4Ps ID:</strong></label>
                                            <p>${data.nhts_4ps_id || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>PHIC ID:</strong></label>
                                            <p>${data.phic_id || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>

                                ${data.visits && data.visits.length > 0 ? `
                                <div class="form-section section-assessment">
                                    <h3 class="section-header"><span class="section-indicator"></span>Immunization Visits (${data.visits.length})</h3>
                                    ${data.visits.map((visit, idx) => `
                                        <div class="visit-box" style="margin-bottom: 1rem; border: 1px solid #ddd; padding: 1rem; border-radius: 4px;">
                                            <h4 style="margin-bottom: 0.5rem;">Visit ${idx + 1} - ${visit.visit_date || 'N/A'}</h4>
                                            <div class="form-row">
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
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label><strong>Status:</strong></label>
                                                    <p>${visit.status || 'N/A'}</p>
                                                </div>
                                                <div class="form-group">
                                                    <label><strong>Vaccine:</strong></label>
                                                    <p>${visit.vaccine || 'N/A'}</p>
                                                </div>
                                                <div class="form-group">
                                                    <label><strong>Breastfeeding:</strong></label>
                                                    <p>${visit.breastfeeding || 'N/A'}</p>
                                                </div>
                                            </div>
                                            ${visit.temperature ? `
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label><strong>Temperature:</strong></label>
                                                    <p>${visit.temperature}</p>
                                                </div>
                                            </div>
                                            ` : ''}
                                        </div>
                                    `).join('')}
                                </div>
                                ` : ''}
                            `;
                        } catch (error) {
                            console.error('Error loading record:', error);
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
            });
        </script>
    @endpush
@endsection