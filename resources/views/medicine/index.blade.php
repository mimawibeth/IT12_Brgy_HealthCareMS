@extends('layouts.app')

@section('title', 'Medicine List')
@section('page-title', 'Medicine Inventory')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css?v=' . time()) }}">
    <link rel="stylesheet" href="{{ asset('css/medicine.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="content-header">
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-text">
                    <h3 class="stat-title">Total Medicines</h3>
                    <p class="stat-number">{{ $totalMedicines }}</p>
                    <span class="stat-trend">Different Items</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-text">
                    <h3 class="stat-title">Total Stock</h3>
                    <p class="stat-number">{{ $totalStock }}</p>
                    <span class="stat-trend">Units Available</span>
                </div>
            </div>

            <div class="stat-card alert-card">
                <div class="stat-text">
                    <h3 class="stat-title">Low Stock Items</h3>
                    <p class="stat-number">{{ $lowStockCount }}</p>
                    <span class="stat-trend">Need Reorder</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-text">
                    <h3 class="stat-title">Expiring Soon</h3>
                    <p class="stat-number">{{ $expiringSoonCount }}</p>
                    <span class="stat-trend">Batches within 30 days</span>
                </div>
            </div>
        </div>

        <div
            style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: .3rem; flex-wrap: wrap;">
            <form method="GET" action="{{ route('medicine.index') }}" id="medicineFilterForm" class="filters"
                style="flex: 1; display: flex; gap: 12px; align-items: center;">
                <input type="text" name="search" id="medicineSearch" placeholder="Search medicines..." class="search-input"
                    value="{{ request('search') }}" style="flex: 1; min-width: 300px;">

                <select name="dosage_form" id="dosageFormFilter" class="filter-select">
                    <option value="">All Forms</option>
                    <option value="Tablet" {{ request('dosage_form') === 'Tablet' ? 'selected' : '' }}>Tablet</option>
                    <option value="Capsule" {{ request('dosage_form') === 'Capsule' ? 'selected' : '' }}>Capsule</option>
                    <option value="Syrup" {{ request('dosage_form') === 'Syrup' ? 'selected' : '' }}>Syrup</option>
                    <option value="Injection" {{ request('dosage_form') === 'Injection' ? 'selected' : '' }}>Injection
                    </option>
                    <option value="Cream" {{ request('dosage_form') === 'Cream' ? 'selected' : '' }}>Cream</option>
                    <option value="Ointment" {{ request('dosage_form') === 'Ointment' ? 'selected' : '' }}>Ointment</option>
                </select>

                <select name="stock_status" id="stockStatusFilter" class="filter-select">
                    <option value="">All Stock</option>
                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="normal" {{ request('stock_status') === 'normal' ? 'selected' : '' }}>Normal Stock</option>
                    <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Out of Stock</option>
                </select>

                <select name="expiry_status" id="expiryStatusFilter" class="filter-select">
                    <option value="">All Expiry</option>
                    <option value="expired" {{ request('expiry_status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="expiring_soon" {{ request('expiry_status') === 'expiring_soon' ? 'selected' : '' }}>
                        Expiring Soon (30 days)</option>
                    <option value="valid" {{ request('expiry_status') === 'valid' ? 'selected' : '' }}>Valid</option>
                </select>

                <button type="button" id="clearMedicineFilters" class="btn btn-secondary"
                    style="padding: 10px 15px !important; font-size: 14px; font-weight: normal;">
                    <i class="bi bi-x-circle"></i> Clear
                </button>

                <button type="button" class="btn btn-primary" id="openAddMedicineModal"
                    style="padding: 10px 15px !important; font-size: 14px; font-weight: normal; white-space: nowrap;">
                    <i class="bi bi-plus-circle"></i> Add Medicine
                </button>
            </form>
        </div>

        <div class="table-container">
            <div style="overflow-x: auto;">
                <table class="data-table" style="min-width: 800px;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Generic Name</th>
                            <th>Form</th>
                            <th>Dosage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($medicines as $medicine)
                            <tr>
                                <td>{{ $medicine->name }}</td>
                                <td>{{ $medicine->generic_name }}</td>
                                <td>{{ $medicine->dosage_form }}</td>
                                <td>{{ $medicine->strength }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align:center;">No medicines found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($medicines->hasPages())
            <div class="pagination">
                @if($medicines->onFirstPage())
                    <button class="btn-page" disabled>« Previous</button>
                @else
                    <a class="btn-page" href="{{ $medicines->appends(request()->query())->previousPageUrl() }}">« Previous</a>
                @endif

                @php
                    $start = max(1, $medicines->currentPage() - 2);
                    $end = min($medicines->lastPage(), $medicines->currentPage() + 2);
                @endphp

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page === $medicines->currentPage())
                        <span class="btn-page active">{{ $page }}</span>
                    @else
                        <a class="btn-page" href="{{ $medicines->appends(request()->query())->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                <span class="page-info">
                    Page {{ $medicines->currentPage() }} of {{ $medicines->lastPage() }} ({{ $medicines->total() }} total
                    medicines)
                </span>

                @if($medicines->hasMorePages())
                    <a class="btn-page" href="{{ $medicines->appends(request()->query())->nextPageUrl() }}">Next »</a>
                @else
                    <button class="btn-page" disabled>Next »</button>
                @endif
            </div>
        @endif
    </div>
    </div>

    <div class="modal" id="medicineViewModal" style="display:none;">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>Medicine Details</h3>
                <span class="close-modal" id="closeMedicineModal">&times;</span>
            </div>
            <div class="modal-body" id="medicineModalBody">
                <div class="loading-spinner" style="text-align:center; padding: 2rem;">
                    <p>Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="addMedicineModal" style="display:none;">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>Add New Medicine</h3>
                <span class="close-modal" data-close-modal="addMedicineModal">&times;</span>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('medicine.store') }}" class="patient-form" novalidate>
                    @csrf

                    <div class="form-section section-patient-info">
                        <h3 class="section-header">
                            <span class="section-indicator"></span>Medicine Information
                        </h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="modal_name">Brand Name <span class="required-asterisk">*</span></label>
                                <input type="text" id="modal_name" name="name" class="form-control" required
                                    value="{{ old('name') }}">
                                <span class="error-message" data-for="name"></span>
                            </div>

                            <div class="form-group">
                                <label for="modal_generic_name">Generic Name</label>
                                <input type="text" id="modal_generic_name" name="generic_name" class="form-control"
                                    value="{{ old('generic_name') }}">
                            </div>

                            <div class="form-group">
                                <label for="modal_dosage_form">Dosage Form</label>
                                <input type="text" id="modal_dosage_form" name="dosage_form" class="form-control"
                                    placeholder="Tablet, Syrup, Capsule, etc." value="{{ old('dosage_form') }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="modal_strength">Dosage</label>
                                <input type="text" id="modal_strength" name="strength" class="form-control"
                                    placeholder="e.g., 500 mg" value="{{ old('strength') }}">
                            </div>

                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" data-close-modal="addMedicineModal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Medicine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('medicineViewModal');
                const closeModal = document.getElementById('closeMedicineModal');

                const addMedicineModal = document.getElementById('addMedicineModal');
                const openAddMedicineBtn = document.getElementById('openAddMedicineModal');

                function openModalById(id) {
                    const m = document.getElementById(id);
                    if (m) {
                        m.style.display = 'flex';
                    }
                }

                function closeModalById(id) {
                    const m = document.getElementById(id);
                    if (m) {
                        m.style.display = 'none';
                    }
                }

                if (openAddMedicineBtn && addMedicineModal) {
                    openAddMedicineBtn.addEventListener('click', function () {
                        addMedicineModal.style.display = 'flex';
                    });
                }


                document.querySelectorAll('.view-medicine').forEach(button => {
                    button.addEventListener('click', async function () {
                        const medicineId = this.getAttribute('data-id');

                        modal.style.display = 'flex';
                        const modalBody = document.getElementById('medicineModalBody');
                        modalBody.innerHTML = '<div style="text-align:center; padding: 2rem;"><p>Loading...</p></div>';

                        try {
                            const response = await fetch(`/medicine/${medicineId}`);
                            const data = await response.json();

                            const expiryDate = data.expiry_date ? new Date(data.expiry_date) : null;
                            const today = new Date();
                            const daysUntilExpiry = expiryDate ? Math.floor((expiryDate - today) / (1000 * 60 * 60 * 24)) : null;
                            const isExpired = daysUntilExpiry !== null && daysUntilExpiry < 0;
                            const isNearExpiry = daysUntilExpiry !== null && daysUntilExpiry >= 0 && daysUntilExpiry <= 30;
                            const isLowStock = data.quantity_on_hand <= data.reorder_level;

                            modalBody.innerHTML = `
                                                                                                                                                                                        <div class="form-section section-patient-info">
                                                                                                                                                                                            <h3 class="section-header"><span class="section-indicator"></span>Basic Information</h3>
                                                                                                                                                                                            <div class="form-row">
                                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                                    <label><strong>Medicine Name:</strong></label>
                                                                                                                                                                                                    <p>${data.name || 'N/A'}</p>
                                                                                                                                                                                                </div>
                                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                                    <label><strong>Generic Name:</strong></label>
                                                                                                                                                                                                    <p>${data.generic_name || 'N/A'}</p>
                                                                                                                                                                                                </div>
                                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                                    <label><strong>Dosage Form:</strong></label>
                                                                                                                                                                                                    <p>${data.dosage_form || 'N/A'}</p>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            <div class="form-row">
                                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                                    <label><strong>Dosage:</strong></label>
                                                                                                                                                                                                    <p>${data.strength || 'N/A'}</p>
                                                                                                                                                                                                </div>
                                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                                    <label><strong>Unit:</strong></label>
                                                                                                                                                                                                    <p>${data.unit || 'N/A'}</p>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </div>

                                                                                                                                                                                        <div class="form-section section-screening">
                                                                                                                                                                                            <h3 class="section-header"><span class="section-indicator"></span>Inventory Status</h3>
                                                                                                                                                                                            <div class="form-row">
                                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                                    <label><strong>Quantity on Hand:</strong></label>
                                                                                                                                                                                                    <p style="${isLowStock ? 'color: #e74c3c; font-weight: bold;' : ''}">
                                                                                                                                                                                                        ${data.quantity_on_hand || 0}
                                                                                                                                                                                                        ${isLowStock ? '<span style="color: #e74c3c;"> ⚠ Low Stock</span>' : ''}
                                                                                                                                                                                                    </p>
                                                                                                                                                                                                </div>
                                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                                    <label><strong>Reorder Level:</strong></label>
                                                                                                                                                                                                    <p>${data.reorder_level || 'N/A'}</p>
                                                                                                                                                                                                </div>
                                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                                    <label><strong>Expiry Date:</strong></label>
                                                                                                                                                                                                    <p style="${isExpired ? 'color: #e74c3c; font-weight: bold;' : isNearExpiry ? 'color: #f39c12; font-weight: bold;' : ''}">
                                                                                                                                                                                                        ${expiryDate ? expiryDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A'}
                                                                                                                                                                                                        ${isExpired ? '<span style="color: #e74c3c;"> ⚠ Expired</span>' : ''}
                                                                                                                                                                                                        ${isNearExpiry ? '<span style="color: #f39c12;"> ⚠ Expiring Soon</span>' : ''}
                                                                                                                                                                                                    </p>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            ${data.remarks ? `
                                                                                                                                                                                            <div class="form-row">
                                                                                                                                                                                                <div class="form-group full-width">
                                                                                                                                                                                                    <label><strong>Remarks:</strong></label>
                                                                                                                                                                                                    <p>${data.remarks}</p>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                            ` : ''}
                                                                                                                                                                                        </div>

                                                                                                                                                                                        <div class="form-section section-history">
                                                                                                                                                                                            <h3 class="section-header"><span class="section-indicator"></span>Record Information</h3>
                                                                                                                                                                                            <div class="form-row">
                                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                                    <label><strong>Date Added:</strong></label>
                                                                                                                                                                                                    <p>${data.created_at ? new Date(data.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A'}</p>
                                                                                                                                                                                                </div>
                                                                                                                                                                                                <div class="form-group">
                                                                                                                                                                                                    <label><strong>Last Updated:</strong></label>
                                                                                                                                                                                                    <p>${data.updated_at ? new Date(data.updated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A'}</p>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    `;
                        } catch (error) {
                            modalBody.innerHTML = '<div style="text-align:center; padding: 2rem; color: red;"><p>Error loading medicine details.</p></div>';
                        }
                    });
                });

                closeModal.addEventListener('click', () => modal.style.display = 'none');
                window.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }

                    if (event.target === addMedicineModal) {
                        addMedicineModal.style.display = 'none';
                    }
                });

                document.querySelectorAll('.close-modal[data-close-modal]').forEach(span => {
                    span.addEventListener('click', function () {
                        const targetId = this.getAttribute('data-close-modal');
                        closeModalById(targetId);
                    });
                });

                document.querySelectorAll('button[data-close-modal]').forEach(button => {
                    button.addEventListener('click', function () {
                        const targetId = this.getAttribute('data-close-modal');
                        closeModalById(targetId);
                    });
                });

                // Auto-submit filter form on input/change
                const medicineForm = document.getElementById('medicineFilterForm');
                const searchInput = document.getElementById('medicineSearch');
                const dosageFormFilter = document.getElementById('dosageFormFilter');
                const stockStatusFilter = document.getElementById('stockStatusFilter');
                const expiryStatusFilter = document.getElementById('expiryStatusFilter');
                const clearMedicineBtn = document.getElementById('clearMedicineFilters');

                let searchTimeout;

                // Auto-submit on search input with debounce
                searchInput.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        medicineForm.submit();
                    }, 500);
                });

                // Auto-submit on filter change
                dosageFormFilter.addEventListener('change', () => medicineForm.submit());
                stockStatusFilter.addEventListener('change', () => medicineForm.submit());
                expiryStatusFilter.addEventListener('change', () => medicineForm.submit());

                // Clear all filters
                clearMedicineBtn.addEventListener('click', function () {
                    searchInput.value = '';
                    dosageFormFilter.value = '';
                    stockStatusFilter.value = '';
                    expiryStatusFilter.value = '';
                    medicineForm.submit();
                });
            });
        </script>
    @endpush
@endsection