{{-- Dispense History --}}
@extends('layouts.app')

@section('title', 'Dispensing Log')
@section('page-title', 'Dispensing Log')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
    <link rel="stylesheet" href="{{ asset('css/medicine.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <!-- Search and Filter Section -->
        <form method="GET" action="{{ route('medicine.dispense') }}" class="filters" id="dispenseFilterForm">
            <div class="filter-options"
                style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-bottom: 1.5rem;">
                <input type="text" name="dispensed_to" id="dispensedToSearch" placeholder="Search by medicine..."
                    class="search-input" value="{{ request('dispensed_to') }}" style="flex: 1; min-width: 250px;">

                <select name="medicine_id" id="medicineFilter" class="filter-select">
                    <option value="">All Medicines</option>
                    @foreach($medicines as $medicine)
                        <option value="{{ $medicine->id }}" @selected(request('medicine_id') == $medicine->id)>
                            {{ $medicine->name }}
                        </option>
                    @endforeach
                </select>

                <div style="display: flex; align-items: center; gap: 8px;">
                    <label
                        style="font-size: 12px; color: #6c757d; margin: 0; font-weight: 500; white-space: nowrap;">From</label>
                    <input type="date" name="from_date" id="fromDateFilter" class="filter-select"
                        value="{{ request('from_date') }}" style="margin: 0; max-width: 160px;">
                </div>

                <div style="display: flex; align-items: center; gap: 8px;">
                    <label
                        style="font-size: 12px; color: #6c757d; margin: 0; font-weight: 500; white-space: nowrap;">To</label>
                    <input type="date" name="to_date" id="toDateFilter" class="filter-select"
                        value="{{ request('to_date') }}" style="margin: 0; max-width: 160px;">
                </div>

                <a href="{{ route('medicine.dispense') }}" class="btn btn-secondary"
                    style="padding: 9px 15px !important; font-size: 14px; font-weight: normal; display: inline-flex !important; align-items: center; gap: 6px; height: 38px; line-height: 1;">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const form = document.getElementById('dispenseFilterForm');
                    const searchInput = document.getElementById('dispensedToSearch');
                    const medicineFilter = document.getElementById('medicineFilter');
                    const fromDateFilter = document.getElementById('fromDateFilter');
                    const toDateFilter = document.getElementById('toDateFilter');

                    let searchTimeout;

                    // Auto-submit on search input with debounce
                    searchInput.addEventListener('input', function () {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            form.submit();
                        }, 500);
                    });

                    // Auto-submit on filter changes
                    medicineFilter.addEventListener('change', () => form.submit());
                    fromDateFilter.addEventListener('change', () => form.submit());
                    toDateFilter.addEventListener('change', () => form.submit());

                    // Modal functions
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

                    // Close modal handlers
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

                    const viewDispenseModal = document.getElementById('viewDispenseModal');
                    window.addEventListener('click', function (event) {
                        if (event.target === viewDispenseModal) {
                            closeModal('viewDispenseModal');
                        }
                    });

                    // View dispense functionality
                    document.querySelectorAll('.view-dispense').forEach(button => {
                        button.addEventListener('click', function () {
                            const medicine = this.dataset.medicine;
                            const quantity = this.dataset.quantity;
                            const dispensedTo = this.dataset.dispensedTo;
                            const reference = this.dataset.reference;
                            const remarks = this.dataset.remarks;
                            const date = this.dataset.date;
                            const time = this.dataset.time;

                            // Populate modal
                            document.getElementById('disp_medicine_name').textContent = medicine;
                            document.getElementById('disp_quantity').textContent = quantity;
                            document.getElementById('disp_dispensed_to').textContent = dispensedTo;
                            document.getElementById('disp_reference_no').textContent = reference;
                            document.getElementById('disp_date_dispensed').textContent = date;
                            document.getElementById('disp_time').textContent = time;
                            document.getElementById('disp_remarks').textContent = remarks;

                            openModal('viewDispenseModal');
                        });
                    });
                });
            </script>
        @endpush

        <div class="table-container">
            <div style="overflow-x: auto;">
                <table class="data-table" style="min-width: 1000px;">
                    <thead>
                        <tr>
                            <th width="12%">Date Dispensed</th>
                            <th width="20%">Medicine Name</th>
                            <th width="10%">Quantity</th>
                            <th width="18%">Dispensed To</th>
                            <th width="15%">Reference No.</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dispensingTableBody">
                        @forelse($dispenses as $dispense)
                            <tr>
                                <td>
                                    <div class="transaction-date">
                                        {{ optional($dispense->dispensed_at)->format('M d, Y') ?? $dispense->created_at->format('M d, Y') }}<br>
                                        <small
                                            style="color: #6c757d;">{{ optional($dispense->dispensed_at)->format('h:i A') ?? $dispense->created_at->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="item-name">{{ $dispense->medicine->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="quantity-badge quantity-out">
                                        {{ $dispense->quantity }} {{ $dispense->medicine->unit ?? '' }}
                                    </span>
                                </td>
                                <td>
                                    <small style="color: #6c757d;">{{ $dispense->dispensed_to ?: '—' }}</small>
                                </td>
                                <td>
                                    <small style="color: #6c757d;">{{ $dispense->reference_no ?: '—' }}</small>
                                </td>
                                <td class="actions">
                                    <a href="javascript:void(0)" class="btn-action btn-view view-dispense"
                                        data-id="{{ $dispense->id }}" data-medicine="{{ $dispense->medicine->name ?? 'N/A' }}"
                                        data-quantity="{{ $dispense->quantity }} {{ $dispense->medicine->unit ?? '' }}"
                                        data-dispensed-to="{{ $dispense->dispensed_to ?: '—' }}"
                                        data-reference="{{ $dispense->reference_no ?: '—' }}"
                                        data-remarks="{{ $dispense->remarks ?: '—' }}"
                                        data-date="{{ optional($dispense->dispensed_at)->format('M d, Y') ?? $dispense->created_at->format('M d, Y') }}"
                                        data-time="{{ optional($dispense->dispensed_at)->format('h:i A') ?? $dispense->created_at->format('h:i A') }}">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #7f8c8d;">
                                    <i class="bi bi-inbox"
                                        style="font-size: 48px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                                    No dispensing records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @php
            $showPagination = !empty($dispenses) && method_exists($dispenses, 'hasPages') && $dispenses->hasPages();
        @endphp
        @if($showPagination)
            <div class="pagination">
                @if($dispenses->onFirstPage())
                    <button class="btn-page" disabled>« Previous</button>
                @else
                    <a class="btn-page" href="{{ $dispenses->previousPageUrl() }}">« Previous</a>
                @endif

                @php
                    $start = max(1, $dispenses->currentPage() - 2);
                    $end = min($dispenses->lastPage(), $dispenses->currentPage() + 2);
                @endphp

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page === $dispenses->currentPage())
                        <span class="btn-page active">{{ $page }}</span>
                    @else
                        <a class="btn-page" href="{{ $dispenses->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                <span class="page-info">
                    Page {{ $dispenses->currentPage() }} of {{ $dispenses->lastPage() }} ({{ $dispenses->total() }} total
                    dispenses)
                </span>

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page === $dispenses->currentPage())
                        <span class="btn-page active">{{ $page }}</span>
                    @else
                        <a class="btn-page" href="{{ $dispenses->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                <span class="page-info">
                    Page {{ $dispenses->currentPage() }} of {{ $dispenses->lastPage() }} ({{ $dispenses->total() }} total
                    records)
                </span>

                @if($dispenses->hasMorePages())
                    <a class="btn-page" href="{{ $dispenses->nextPageUrl() }}">Next »</a>
                @else
                    <button class="btn-page" disabled>Next »</button>
                @endif
            </div>
        @endif
    </div>

    <!-- View Dispense Modal -->
    <div class="modal" id="viewDispenseModal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Dispensing Record Details</h3>
                <span class="close-modal" data-close-modal="viewDispenseModal">&times;</span>
            </div>
            <div class="modal-body">
                <div class="form-section section-patient-info">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Dispensing Information
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Medicine Name</label>
                            <div class="form-control" id="disp_medicine_name" style="background: #f8f9fa; border: none;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <div class="form-control" id="disp_quantity"
                                style="background: #f8f9fa; border: none; font-weight: bold;"></div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Dispensed To</label>
                            <div class="form-control" id="disp_dispensed_to" style="background: #f8f9fa; border: none;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Reference No.</label>
                            <div class="form-control" id="disp_reference_no" style="background: #f8f9fa; border: none;">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date Dispensed</label>
                            <div class="form-control" id="disp_date_dispensed" style="background: #f8f9fa; border: none;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Dispensed At</label>
                            <div class="form-control" id="disp_time" style="background: #f8f9fa; border: none;">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Remarks</label>
                            <div class="form-control" id="disp_remarks"
                                style="background: #f8f9fa; border: none; min-height: 60px;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" data-close-modal="viewDispenseModal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection