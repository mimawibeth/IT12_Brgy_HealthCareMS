@extends('layouts.app')

@section('title', 'Medicine Batches')
@section('page-title', 'Medicine Batches')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
    <link rel="stylesheet" href="{{ asset('css/medicine.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <!-- Search and Filter Section -->
        <form method="GET" action="{{ route('medicine.batches.index') }}" class="filters">
            <div class="filter-options"
                style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-bottom: 1rem;">
                <select name="medicine_id" class="filter-select" onchange="this.form.submit()"
                    style="flex: 1; min-width: 300px;">
                    <option value="">All Medicines</option>
                    @foreach($medicines as $medicine)
                        <option value="{{ $medicine->id }}" @selected(request('medicine_id') == $medicine->id)>
                            {{ $medicine->name }}
                        </option>
                    @endforeach
                </select>

                <select name="filter" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Batches</option>
                    <option value="expiring" {{ request('filter') === 'expiring' ? 'selected' : '' }}>Expiring Soon</option>
                    <option value="expired" {{ request('filter') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>

                <a href="{{ route('medicine.batches.index') }}" class="btn btn-secondary"
                    style="padding: 9px 15px !important; font-size: 14px; font-weight: normal; display: inline-flex !important; align-items: center; gap: 6px; height: 38px; line-height: 1;">
                    <i class="bi bi-x-circle"></i> Clear
                </a>

                <div style="margin-left: auto; display: flex; gap: 12px;">
                    <button type="button" class="btn btn-primary" id="openAddBatchModal"
                        style="padding: 9px 15px !important; font-size: 14px; font-weight: normal; white-space: nowrap; height: 38px; line-height: 1; display: inline-flex !important; align-items: center; gap: 6px;">
                        <i class="bi bi-plus-circle"></i> Add Batch
                    </button>

                    <button type="button" class="btn btn-teal" id="openDispenseFromBatchesModal"
                        style="padding: 9px 15px !important; font-size: 14px; font-weight: normal; white-space: nowrap; height: 38px; line-height: 1; display: inline-flex !important; align-items: center; gap: 6px;">
                        <i class="bi bi-prescription2"></i> Dispense Medicine
                    </button>
                </div>
            </div>
        </form>

        @forelse($batches as $batchGroup)
            @php
                $medicineId = $batchGroup['medicine_id'];
                $medicineBatches = $batchGroup['batches'];
                $medicine = $medicineBatches->first()->medicine;
                $totalQuantity = $medicineBatches->sum('quantity_on_hand');
                $hasExpired = $medicineBatches->where('expiry_date', '<', now())->count() > 0;
                $hasExpiringSoon = $medicineBatches->where('expiry_date', '>=', now())->where('expiry_date', '<=', now()->addDays(30))->count() > 0;
            @endphp

            <div class="batch-medicine-card" style="margin-bottom: 1.5rem;">
                <div class="batch-card-header" data-medicine-id="{{ $medicineId }}">
                    <div class="batch-header-content">
                        <div class="batch-info-section">
                            <div class="batch-title-row">
                                <i class="bi bi-chevron-down batch-toggle-icon"></i>
                                <h3 class="batch-medicine-name">{{ $medicine->name ?? 'Unknown Medicine' }}</h3>
                                @if($hasExpired)
                                    <span class="batch-badge badge-danger">
                                        <i class="bi bi-exclamation-triangle"></i> Has Expired
                                    </span>
                                @elseif($hasExpiringSoon)
                                    <span class="batch-badge badge-warning">
                                        <i class="bi bi-clock-history"></i> Expiring Soon
                                    </span>
                                @endif
                            </div>
                            <div class="batch-stats">
                                <span class="batch-stat-item">
                                    <i class="bi bi-box-seam"></i>
                                    <strong>{{ $totalQuantity }}</strong> units
                                </span>
                                <span class="batch-stat-separator">•</span>
                                <span class="batch-stat-item">
                                    <i class="bi bi-layers"></i>
                                    <strong>{{ $medicineBatches->count() }}</strong>
                                    batch{{ $medicineBatches->count() > 1 ? 'es' : '' }}
                                </span>
                            </div>
                        </div>
                        <div class="batch-actions">
                            <button type="button" class="btn btn-teal btn-sm dispense-batch-btn"
                                data-medicine-id="{{ $medicineId }}" data-medicine-name="{{ $medicine->name ?? '' }}">
                                <i class="bi bi-prescription2"></i> Dispense
                            </button>
                        </div>
                    </div>
                </div>

                <div class="batch-card-body" id="batch-body-{{ $medicineId }}">
                    <div class="batch-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Batch Code</th>
                                    <th>Quantity on Hand</th>
                                    <th>Expiry Date</th>
                                    <th>Date Received</th>
                                    <th>From</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($medicineBatches as $batch)
                                    @php
                                        $isExpired = $batch->expiry_date && $batch->expiry_date < now();
                                        $isExpiringSoon = $batch->expiry_date && $batch->expiry_date >= now() && $batch->expiry_date <= now()->addDays(30);
                                    @endphp
                                    <tr class="{{ $isExpired ? 'batch-expired' : ($isExpiringSoon ? 'batch-expiring-soon' : '') }}">
                                        <td><strong>{{ $batch->batch_code ?? 'N/A' }}</strong></td>
                                        <td>
                                            <span
                                                class="quantity-badge {{ $batch->quantity_on_hand > 0 ? 'quantity-available' : 'quantity-empty' }}">
                                                {{ $batch->quantity_on_hand }}
                                            </span>
                                        </td>
                                        <td>{{ optional($batch->expiry_date)->format('M d, Y') ?? 'N/A' }}</td>
                                        <td>{{ optional($batch->date_received)->format('M d, Y') ?? 'N/A' }}</td>
                                        <td>{{ $batch->supplier ?? 'N/A' }}</td>
                                        <td>
                                            @if($isExpired)
                                                <span class="status-badge status-expired">
                                                    <i class="bi bi-x-circle"></i> Expired
                                                </span>
                                            @elseif($isExpiringSoon)
                                                <span class="status-badge status-expiring">
                                                    <i class="bi bi-exclamation-circle"></i> Expiring Soon
                                                </span>
                                            @else
                                                <span class="status-badge status-valid">
                                                    <i class="bi bi-check-circle"></i> Valid
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="bi bi-inbox" style="font-size: 64px; color: #bdc3c7; margin-bottom: 16px;"></i>
                <h3 style="color: #7f8c8d; font-size: 18px; margin-bottom: 8px;">No Medicine Batches Found</h3>
                <p style="color: #95a5a6; font-size: 14px; margin-bottom: 20px;">
                    @if(request('medicine_id') || request('filter'))
                        No batches match your current filters. Try adjusting your search criteria.
                    @else
                        Get started by adding your first medicine batch.
                    @endif
                </p>
                <button type="button" class="btn btn-primary" id="openAddBatchModalEmpty">
                    <i class="bi bi-plus-circle"></i> Add First Batch
                </button>
            </div>
        @endforelse

        @if($batches->hasPages())
            <div class="pagination">
                @if($batches->onFirstPage())
                    <button class="btn-page" disabled>« Previous</button>
                @else
                    <a class="btn-page" href="{{ $batches->previousPageUrl() }}">« Previous</a>
                @endif

                @php
                    $start = max(1, $batches->currentPage() - 2);
                    $end = min($batches->lastPage(), $batches->currentPage() + 2);
                @endphp

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page === $batches->currentPage())
                        <span class="btn-page active">{{ $page }}</span>
                    @else
                        <a class="btn-page" href="{{ $batches->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                <span class="page-info">
                    Page {{ $batches->currentPage() }} of {{ $batches->lastPage() }} ({{ $batches->total() }} total
                    medicine{{ $batches->total() > 1 ? 's' : '' }})
                </span>

                @if($batches->hasMorePages())
                    <a class="btn-page" href="{{ $batches->nextPageUrl() }}">Next »</a>
                @else
                    <button class="btn-page" disabled>Next »</button>
                @endif
            </div>
        @endif

        <div class="modal" id="addBatchModal" style="display:none;">
            <div class="modal-content modal-large">
                <div class="modal-header">
                    <h3>Add Medicine Batch</h3>
                    <span class="close-modal" data-close-modal="addBatchModal">&times;</span>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('medicine.batches.store') }}" class="patient-form">
                        @csrf

                        <div class="form-section section-patient-info">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Batch Information
                            </h3>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="add_batch_medicine_id">Medicine <span
                                            class="required-asterisk">*</span></label>
                                    <select id="add_batch_medicine_id" name="medicine_id" class="form-control" required>
                                        <option value="">-- Select or search medicine --</option>
                                        @foreach($medicines as $medicine)
                                            <option value="{{ $medicine->id }}"
                                                @selected(request('medicine_id') == $medicine->id)>
                                                {{ $medicine->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="batch_code">Batch Code</label>
                                    <input type="text" id="batch_code" name="batch_code" class="form-control"
                                        value="{{ old('batch_code') }}" readonly style="background-color: #f0f0f0;">
                                </div>

                                <div class="form-group">
                                    <label for="quantity_on_hand">Quantity on Hand <span
                                            class="required-asterisk">*</span></label>
                                    <input type="number" id="quantity_on_hand" name="quantity_on_hand" class="form-control"
                                        min="0" required value="{{ old('quantity_on_hand', 0) }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiry_date">Expiry Date <span class="required-asterisk">*</span></label>
                                    <input type="date" id="expiry_date" name="expiry_date" class="form-control"
                                        value="{{ old('expiry_date') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="date_received">Date Received <span
                                            class="required-asterisk">*</span></label>
                                    <input type="date" id="date_received" name="date_received" class="form-control"
                                        value="{{ old('date_received') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="supplier">From</label>
                                    <input type="text" id="supplier" name="supplier" class="form-control"
                                        value="{{ old('supplier') }}" placeholder="DOH, LGU, Donation, etc.">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" data-close-modal="addBatchModal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Batch</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal" id="dispenseFromBatchesModal" style="display:none;">
            <div class="modal-content modal-large">
                <div class="modal-header">
                    <h3>Dispense Medicine</h3>
                    <span class="close-modal" data-close-modal="dispenseFromBatchesModal">&times;</span>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('medicine.dispense.store') }}" class="patient-form">
                        @csrf

                        <div class="form-section section-patient-info">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Medicine Details
                            </h3>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dispense_medicine_id">Medicine <span
                                            class="required-asterisk">*</span></label>
                                    <select id="dispense_medicine_id" name="medicine_id" class="form-control" required>
                                        <option value="">-- Select or search medicine --</option>
                                        @foreach($medicines as $medicine)
                                            <option value="{{ $medicine->id }}">
                                                {{ $medicine->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="dispense_quantity">Quantity to Dispense <span
                                            class="required-asterisk">*</span></label>
                                    <input type="number" id="dispense_quantity" name="quantity" class="form-control" min="1"
                                        required value="{{ old('quantity', 1) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-section section-assessment">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Dispensation Information
                            </h3>

                            <div class="form-row">
                                <div class="form-group" style="position: relative;">
                                    <label for="dispense_dispensed_to">Dispensed To (Patient Name / ID)</label>
                                    <input type="text" id="dispense_dispensed_to" name="dispensed_to" class="form-control"
                                        value="{{ old('dispensed_to') }}" placeholder="Search patient name or ID"
                                        autocomplete="off">
                                    <div id="patient_search_results"
                                        style="position: absolute; background: white; border: 1px solid #ddd; border-radius: 4px; max-height: 200px; overflow-y: auto; width: 100%; z-index: 1000; display: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="dispense_reference_no">Reference No.</label>
                                    <input type="text" id="dispense_reference_no" name="reference_no" class="form-control"
                                        value="{{ old('reference_no') }}" readonly style="background-color: #f0f0f0;">
                                </div>

                                <div class="form-group">
                                    <label for="dispense_dispensed_at">Dispensed Date</label>
                                    <input type="date" id="dispense_dispensed_at" name="dispensed_at" class="form-control"
                                        value="{{ old('dispensed_at') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dispense_remarks">Remarks</label>
                                    <textarea id="dispense_remarks" name="remarks" class="form-control" rows="3"
                                        placeholder="Additional notes or instructions">{{ old('remarks') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary"
                                data-close-modal="dispenseFromBatchesModal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Dispense Medicine</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const addBatchModal = document.getElementById('addBatchModal');
                const dispenseModal = document.getElementById('dispenseFromBatchesModal');
                const openAddBatchBtn = document.getElementById('openAddBatchModal');
                const openAddBatchBtnEmpty = document.getElementById('openAddBatchModalEmpty');
                const openDispenseBtn = document.getElementById('openDispenseFromBatchesModal');

                // Collapsible Batch Cards
                function initializeCollapsibleCards() {
                    const batchHeaders = document.querySelectorAll('.batch-card-header');

                    batchHeaders.forEach((header, index) => {
                        const medicineId = header.getAttribute('data-medicine-id');
                        const batchBody = document.getElementById(`batch-body-${medicineId}`);
                        const toggleIcon = header.querySelector('.batch-toggle-icon');

                        // Set initial state - first card open, rest collapsed
                        if (index === 0) {
                            batchBody.classList.add('active');
                            toggleIcon.style.transform = 'rotate(0deg)';
                        } else {
                            batchBody.classList.remove('active');
                            toggleIcon.style.transform = 'rotate(-90deg)';
                        }

                        header.addEventListener('click', function (e) {
                            // Don't toggle if clicking the dispense button
                            if (e.target.closest('.dispense-batch-btn')) {
                                return;
                            }

                            const isActive = batchBody.classList.contains('active');

                            // Toggle current card
                            if (isActive) {
                                batchBody.classList.remove('active');
                                toggleIcon.style.transform = 'rotate(-90deg)';
                            } else {
                                batchBody.classList.add('active');
                                toggleIcon.style.transform = 'rotate(0deg)';
                            }
                        });
                    });
                }

                // Initialize collapsible cards on page load
                initializeCollapsibleCards();

                function openModal(id) {
                    const modal = document.getElementById(id);
                    if (modal) {
                        modal.style.display = 'flex';
                    }
                }

                function closeModal(id) {
                    const modal = document.getElementById(id);
                    if (modal) {
                        modal.style.display = 'none';
                    }
                }

                if (openAddBatchBtn && addBatchModal) {
                    openAddBatchBtn.addEventListener('click', function () {
                        generateBatchCode();
                        openModal('addBatchModal');
                    });
                }

                if (openAddBatchBtnEmpty && addBatchModal) {
                    openAddBatchBtnEmpty.addEventListener('click', function () {
                        generateBatchCode();
                        openModal('addBatchModal');
                    });
                }

                if (openDispenseBtn && dispenseModal) {
                    openDispenseBtn.addEventListener('click', function () {
                        const filterMedicineId = '{{ request('medicine_id') }}';
                        const select = document.getElementById('dispense_medicine_id');
                        if (filterMedicineId && select) {
                            select.value = filterMedicineId;
                        }
                        generateReferenceNumber();
                        openModal('dispenseFromBatchesModal');
                    });
                }

                document.querySelectorAll('.dispense-batch-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const medicineId = this.getAttribute('data-medicine-id');
                        const select = document.getElementById('dispense_medicine_id');
                        if (select && medicineId) {
                            select.value = medicineId;
                        }
                        generateReferenceNumber();
                        openModal('dispenseFromBatchesModal');
                    });
                });

                document.querySelectorAll('.close-modal[data-close-modal]').forEach(span => {
                    span.addEventListener('click', function () {
                        const targetId = this.getAttribute('data-close-modal');
                        closeModal(targetId);
                    });
                });

                document.querySelectorAll('button[data-close-modal]').forEach(button => {
                    button.addEventListener('click', function () {
                        const targetId = this.getAttribute('data-close-modal');
                        closeModal(targetId);
                    });
                });

                window.addEventListener('click', function (event) {
                    if (event.target === addBatchModal) {
                        closeModal('addBatchModal');
                    }

                    if (event.target === dispenseModal) {
                        closeModal('dispenseFromBatchesModal');
                    }
                });

                // Make select searchable with Select2-like functionality
                function makeSelectSearchable(selectId) {
                    const select = document.getElementById(selectId);
                    if (!select) return;

                    const wrapper = document.createElement('div');
                    wrapper.style.position = 'relative';
                    wrapper.style.width = '100%';

                    const searchInput = document.createElement('input');
                    searchInput.type = 'text';
                    searchInput.className = 'form-control';
                    searchInput.placeholder = 'Search medicine...';
                    searchInput.style.marginBottom = '5px';

                    select.parentNode.insertBefore(wrapper, select);
                    wrapper.appendChild(searchInput);
                    wrapper.appendChild(select);

                    let filteredOptions = Array.from(select.options).slice(1); // Skip first "Select" option

                    searchInput.addEventListener('input', function () {
                        const searchTerm = this.value.toLowerCase();
                        let hasMatch = false;

                        Array.from(select.options).forEach((option, index) => {
                            if (index === 0) return; // Skip first option

                            const text = option.text.toLowerCase();
                            const matches = text.includes(searchTerm);
                            option.style.display = matches ? '' : 'none';

                            if (matches && !hasMatch) {
                                hasMatch = true;
                            }
                        });

                        select.size = searchTerm ? Math.min(10, select.options.length) : 1;
                    });

                    searchInput.addEventListener('focus', function () {
                        select.size = Math.min(10, select.options.length);
                    });

                    searchInput.addEventListener('blur', function () {
                        setTimeout(() => {
                            select.size = 1;
                        }, 200);
                    });

                    select.addEventListener('change', function () {
                        if (this.value) {
                            searchInput.value = this.options[this.selectedIndex].text;
                            select.size = 1;
                        }
                    });

                    select.addEventListener('click', function () {
                        searchInput.focus();
                    });
                }

                makeSelectSearchable('add_batch_medicine_id');
                makeSelectSearchable('dispense_medicine_id');

                // Generate batch code
                function generateBatchCode() {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const random = Math.random().toString(36).substring(2, 5).toUpperCase();
                    const batchCode = `B-${year}${month}-${random}`;
                    document.getElementById('batch_code').value = batchCode;
                }

                // Generate reference number
                function generateReferenceNumber() {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const random = Math.floor(Math.random() * 9999).toString().padStart(4, '0');
                    const refNo = `DISP-${year}${month}${day}-${random}`;
                    document.getElementById('dispense_reference_no').value = refNo;
                }

                // Patient search functionality
                const patientSearchInput = document.getElementById('dispense_dispensed_to');
                const patientSearchResults = document.getElementById('patient_search_results');
                let patientSearchTimeout;

                if (patientSearchInput && patientSearchResults) {
                    patientSearchInput.addEventListener('input', function () {
                        clearTimeout(patientSearchTimeout);
                        const query = this.value.trim();

                        if (query.length < 2) {
                            patientSearchResults.style.display = 'none';
                            return;
                        }

                        patientSearchTimeout = setTimeout(async () => {
                            try {
                                const response = await fetch(`/api/patients/search?q=${encodeURIComponent(query)}`);
                                const patients = await response.json();

                                if (patients.length > 0) {
                                    patientSearchResults.innerHTML = patients.map(patient => `
                                                                        <div class="patient-result-item" data-name="${patient.full_name}" data-id="${patient.patient_id}" 
                                                                            style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">
                                                                            <strong>${patient.full_name}</strong> <span style="color: #666;">(${patient.patient_id})</span>
                                                                        </div>
                                                                    `).join('');
                                    patientSearchResults.style.display = 'block';

                                    // Add click handlers to results
                                    document.querySelectorAll('.patient-result-item').forEach(item => {
                                        item.addEventListener('mouseenter', function () {
                                            this.style.backgroundColor = '#f0f0f0';
                                        });
                                        item.addEventListener('mouseleave', function () {
                                            this.style.backgroundColor = 'white';
                                        });
                                        item.addEventListener('click', function () {
                                            patientSearchInput.value = this.dataset.name + ' (' + this.dataset.id + ')';
                                            patientSearchResults.style.display = 'none';
                                        });
                                    });
                                } else {
                                    patientSearchResults.innerHTML = '<div style="padding: 8px 12px; color: #666;">No patients found</div>';
                                    patientSearchResults.style.display = 'block';
                                }
                            } catch (error) {
                                console.error('Patient search error:', error);
                                patientSearchResults.style.display = 'none';
                            }
                        }, 300);
                    });

                    // Close search results when clicking outside
                    document.addEventListener('click', function (e) {
                        if (!patientSearchInput.contains(e.target) && !patientSearchResults.contains(e.target)) {
                            patientSearchResults.style.display = 'none';
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection