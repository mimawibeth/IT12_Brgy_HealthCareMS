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
                        modalBody.innerHTML = `
                                    <div style="padding: 1.5rem;">
                                        <h4 style="color: #2c3e50; margin-bottom: 1rem;">Immunization Record: ${recordId}</h4>
                                        <p style="color: #7f8c8d;">Loading full record details...</p>
                                    </div>
                                `;
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