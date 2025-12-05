@extends('layouts.app')

@section('title', 'Medicine Batches')
@section('page-title', 'Medicine Batches')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
    <link rel="stylesheet" href="{{ asset('css/medicine.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="content-header"></div>

        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
            <form method="GET" action="{{ route('medicine.batches.index') }}" style="display: flex; gap: 0.75rem; align-items: center; flex: 1; max-width: 480px;">
                <div class="form-group" style="flex: 1;">
                    <select name="medicine_id" class="form-control" onchange="this.form.submit()">
                        <option value="">All Medicines</option>
                        @foreach($medicines as $medicine)
                            <option value="{{ $medicine->id }}" @selected(request('medicine_id') == $medicine->id)>
                                {{ $medicine->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(request('medicine_id'))
                    <a href="{{ route('medicine.batches.index') }}" class="btn btn-secondary">Clear</a>
                @endif
            </form>

            <div class="header-actions" style="display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-end;">
                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" class="btn btn-primary" id="openAddBatchModal">
                        <i class="bi bi-plus-circle"></i> Add Batch
                    </button>
                    <button type="button" class="btn btn-teal" id="openDispenseFromBatchesModal">
                        <i class="bi bi-prescription2"></i> Dispense Medicine
                    </button>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('medicine.batches.index', array_filter(['medicine_id' => request('medicine_id')])) }}"
                        class="btn {{ request('filter') ? 'btn-secondary' : 'btn-primary' }}">All</a>
                    <a href="{{ route('medicine.batches.index', array_filter(['medicine_id' => request('medicine_id'), 'filter' => 'expiring'])) }}"
                        class="btn {{ request('filter') === 'expiring' ? 'btn-primary' : 'btn-secondary' }}">Expiring Soon</a>
                    <a href="{{ route('medicine.batches.index', array_filter(['medicine_id' => request('medicine_id'), 'filter' => 'expired'])) }}"
                        class="btn {{ request('filter') === 'expired' ? 'btn-primary' : 'btn-secondary' }}">Expired</a>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Batch Code</th>
                            <th>Quantity on Hand</th>
                            <th>Expiry Date</th>
                            <th>Date Received</th>
                            <th>Supplier</th>
                            <th>Unit Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                            <tr>
                                <td>{{ $batch->medicine->name ?? 'N/A' }}</td>
                                <td>{{ $batch->batch_code ?? 'N/A' }}</td>
                                <td>{{ $batch->quantity_on_hand }}</td>
                                <td>{{ optional($batch->expiry_date)->format('M d, Y') }}</td>
                                <td>{{ optional($batch->date_received)->format('M d, Y') }}</td>
                                <td>{{ $batch->supplier ?? 'N/A' }}</td>
                                <td>
                                    @if(!is_null($batch->unit_price))
                                        {{ number_format($batch->unit_price, 2) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="actions">
                                    <button type="button" class="btn btn-teal btn-sm dispense-batch-btn"
                                        data-medicine-id="{{ $batch->medicine_id }}"
                                        data-medicine-name="{{ $batch->medicine->name ?? '' }}">
                                        <i class="bi bi-prescription2"></i> Dispense
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center; padding: 40px; color: #7f8c8d;">
                                    <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                                    No medicine batches found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

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
                    Page {{ $batches->currentPage() }} of {{ $batches->lastPage() }} ({{ $batches->total() }} total batches)
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
                                    <label for="add_batch_medicine_search">Search Medicine</label>
                                    <input type="text" id="add_batch_medicine_search" class="form-control" placeholder="Type to search medicine">
                                </div>

                                <div class="form-group">
                                    <label for="add_batch_medicine_id">Medicine <span class="required-asterisk">*</span></label>
                                    <select id="add_batch_medicine_id" name="medicine_id" class="form-control" required>
                                        <option value="">-- Select Medicine --</option>
                                        @foreach($medicines as $medicine)
                                            <option value="{{ $medicine->id }}" @selected(request('medicine_id') == $medicine->id)>
                                                {{ $medicine->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="batch_code">Batch Code</label>
                                    <input type="text" id="batch_code" name="batch_code" class="form-control" value="{{ old('batch_code') }}">
                                </div>

                                <div class="form-group">
                                    <label for="quantity_on_hand">Quantity on Hand <span class="required-asterisk">*</span></label>
                                    <input type="number" id="quantity_on_hand" name="quantity_on_hand" class="form-control" min="0" required value="{{ old('quantity_on_hand', 0) }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiry_date">Expiry Date <span class="required-asterisk">*</span></label>
                                    <input type="date" id="expiry_date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="date_received">Date Received <span class="required-asterisk">*</span></label>
                                    <input type="date" id="date_received" name="date_received" class="form-control" value="{{ old('date_received') }}" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="supplier">Supplier</label>
                                    <input type="text" id="supplier" name="supplier" class="form-control" value="{{ old('supplier') }}">
                                </div>

                                <div class="form-group">
                                    <label for="unit_price">Unit Price</label>
                                    <input type="number" id="unit_price" name="unit_price" class="form-control" min="0" step="0.01" value="{{ old('unit_price') }}">
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
                                    <label for="dispense_medicine_search">Search Medicine</label>
                                    <input type="text" id="dispense_medicine_search" class="form-control" placeholder="Type to search medicine">
                                </div>

                                <div class="form-group">
                                    <label for="dispense_medicine_id">Medicine <span class="required-asterisk">*</span></label>
                                    <select id="dispense_medicine_id" name="medicine_id" class="form-control" required>
                                        <option value="">-- Select Medicine --</option>
                                        @foreach($medicines as $medicine)
                                            <option value="{{ $medicine->id }}">
                                                {{ $medicine->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="dispense_quantity">Quantity to Dispense <span class="required-asterisk">*</span></label>
                                    <input type="number" id="dispense_quantity" name="quantity" class="form-control" min="1" required value="{{ old('quantity', 1) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-section section-assessment">
                            <h3 class="section-header">
                                <span class="section-indicator"></span>Dispensation Information
                            </h3>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dispense_dispensed_to">Dispensed To (Patient Name / ID)</label>
                                    <input type="text" id="dispense_dispensed_to" name="dispensed_to" class="form-control" value="{{ old('dispensed_to') }}" placeholder="Enter patient name or ID">
                                </div>

                                <div class="form-group">
                                    <label for="dispense_reference_no">Reference No. (ITR / Program)</label>
                                    <input type="text" id="dispense_reference_no" name="reference_no" class="form-control" value="{{ old('reference_no') }}" placeholder="Enter reference number">
                                </div>

                                <div class="form-group">
                                    <label for="dispense_dispensed_at">Dispensed Date</label>
                                    <input type="date" id="dispense_dispensed_at" name="dispensed_at" class="form-control" value="{{ old('dispensed_at') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dispense_remarks">Remarks</label>
                                    <textarea id="dispense_remarks" name="remarks" class="form-control" rows="3" placeholder="Additional notes or instructions">{{ old('remarks') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" data-close-modal="dispenseFromBatchesModal">Cancel</button>
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

                function attachSearch(inputId, selectId) {
                    const input = document.getElementById(inputId);
                    const select = document.getElementById(selectId);

                    if (!input || !select) {
                        return;
                    }

                    input.addEventListener('keyup', function () {
                        const term = this.value.toLowerCase();
                        Array.from(select.options).forEach(option => {
                            if (!option.value) {
                                return;
                            }

                            const text = option.text.toLowerCase();
                            option.style.display = text.includes(term) ? '' : 'none';
                        });
                    });
                }

                attachSearch('add_batch_medicine_search', 'add_batch_medicine_id');
                attachSearch('dispense_medicine_search', 'dispense_medicine_id');
            });
        </script>
    @endpush
@endsection
