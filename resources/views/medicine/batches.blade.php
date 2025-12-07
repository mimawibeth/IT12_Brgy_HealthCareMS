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

        @forelse($batches as $medicineId => $medicineBatches)
            @php
                $medicine = $medicineBatches->first()->medicine;
                $totalQuantity = $medicineBatches->sum('quantity_on_hand');
            @endphp

            <div class="table-container" style="margin-bottom: 2rem;">
                <div
                    style="padding: 1rem 1.5rem; background: linear-gradient(135deg, #2f6d7e 0%, #1f4d5c 100%); color: white; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">
                            {{ $medicine->name ?? 'Unknown Medicine' }}
                        </h3>
                        <p style="margin: 0.25rem 0 0 0; font-size: 0.85rem; opacity: 0.9;">Total Available:
                            {{ $totalQuantity }} units across {{ $medicineBatches->count() }} batch(es)
                        </p>
                    </div>
                    <button type="button" class="btn btn-teal btn-sm dispense-batch-btn" data-medicine-id="{{ $medicineId }}"
                        data-medicine-name="{{ $medicine->name ?? '' }}"
                        style="background: white; color: #2f6d7e; padding: 8px 12px !important; font-size: 14px; font-weight: normal;">
                        <i class="bi bi-prescription2"></i> Dispense
                    </button>
                </div>

                <div style="overflow-x: auto;">
                    <table class="data-table" style="min-width: 800px;">
                        <thead>
                            <tr>
                                <th>Batch Code</th>
                                <th>Quantity on Hand</th>
                                <th>Expiry Date</th>
                                <th>Date Received</th>
                                <th>From</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicineBatches as $batch)
                                <tr>
                                    <td>{{ $batch->batch_code ?? 'N/A' }}</td>
                                    <td>{{ $batch->quantity_on_hand }}</td>
                                    <td>{{ optional($batch->expiry_date)->format('M d, Y') }}</td>
                                    <td>{{ optional($batch->date_received)->format('M d, Y') }}</td>
                                    <td>{{ $batch->supplier ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="table-container">
                <div style="overflow-x: auto;">
                    <table class="data-table" style="min-width: 800px;">
                        <thead>
                            <tr>
                                <th>Batch Code</th>
                                <th>Quantity on Hand</th>
                                <th>Expiry Date</th>
                                <th>Date Received</th>
                                <th>From</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" style="text-align:center; padding: 40px; color: #7f8c8d;">
                                    <i class="bi bi-inbox"
                                        style="font-size: 48px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                                    No medicine batches found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endforelse

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
                const openDispenseBtn = document.getElementById('openDispenseFromBatchesModal');

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