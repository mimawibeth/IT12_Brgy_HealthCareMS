@extends('layouts.app')

@section('title', 'Medicine List')
@section('page-title', 'Medicine Inventory')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
    <link rel="stylesheet" href="{{ asset('css/medicine.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="content-header">
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-purple"><i class="bi bi-capsule"></i></div>
                <div class="stat-details">
                    <h3>Total Medicines</h3>
                    <p class="stat-number">{{ $totalMedicines }}</p>
                    <span class="stat-label">Different Items</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-blue"><i class="bi bi-box-seam"></i></div>
                <div class="stat-details">
                    <h3>Total Stock</h3>
                    <p class="stat-number">{{ number_format($totalStock) }}</p>
                    <span class="stat-label">Units Available</span>
                </div>
            </div>

            <div class="stat-card alert-card">
                <div class="stat-icon stat-icon-red"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="stat-details">
                    <h3>Low Stock Items</h3>
                    <p class="stat-number">{{ $lowStock }}</p>
                    <span class="stat-label">Need Reorder</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-orange"><i class="bi bi-clock-history"></i></div>
                <div class="stat-details">
                    <h3>Expiring Soon</h3>
                    <p class="stat-number">{{ $expiringSoon }}</p>
                    <span class="stat-label">Within 30 Days</span>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
            <div class="search-container" style="flex: 1; max-width: 400px; margin: 0;">
                <input type="text" id="medicineSearch" class="search-input" placeholder="Search medicines...">
            </div>
            <div class="header-actions">
                <a href="{{ route('medicine.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Medicine
                </a>
                <a href="{{ route('medicine.dispense') }}" class="btn btn-teal">
                    <i class="bi bi-prescription2"></i> Dispense Medicine
                </a>
            </div>
        </div>

        <div class="table-container">
            <div class="card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Generic Name</th>
                            <th>Form</th>
                            <th>Strength</th>
                            <th>Unit</th>
                            <th>Qty on Hand</th>
                            <th>Reorder Level</th>
                            <th>Expiry Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($medicines as $medicine)
                            <tr>
                                <td>{{ $medicine->name }}</td>
                                <td>{{ $medicine->generic_name }}</td>
                                <td>{{ $medicine->dosage_form }}</td>
                                <td>{{ $medicine->strength }}</td>
                                <td>{{ $medicine->unit }}</td>
                                <td>{{ $medicine->quantity_on_hand }}</td>
                                <td>{{ $medicine->reorder_level }}</td>
                                <td>{{ optional($medicine->expiry_date)->format('M d, Y') }}</td>
                                <td class="actions">
                                    <a href="javascript:void(0)" class="btn-action btn-view view-medicine"
                                        data-id="{{ $medicine->id }}" data-name="{{ $medicine->name }}">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
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
                    <a class="btn-page" href="{{ $medicines->previousPageUrl() }}">« Previous</a>
                @endif

                @php
                    $start = max(1, $medicines->currentPage() - 2);
                    $end = min($medicines->lastPage(), $medicines->currentPage() + 2);
                @endphp

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page === $medicines->currentPage())
                        <span class="btn-page active">{{ $page }}</span>
                    @else
                        <a class="btn-page" href="{{ $medicines->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                <span class="page-info">
                    Page {{ $medicines->currentPage() }} of {{ $medicines->lastPage() }} ({{ $medicines->total() }} total
                    medicines)
                </span>

                @if($medicines->hasMorePages())
                    <a class="btn-page" href="{{ $medicines->nextPageUrl() }}">Next »</a>
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('medicineViewModal');
                const closeModal = document.getElementById('closeMedicineModal');

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
                                                                                    <label><strong>Strength:</strong></label>
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
                });

                // Search functionality
                const searchInput = document.getElementById('medicineSearch');
                const tableRows = document.querySelectorAll('.data-table tbody tr');

                searchInput.addEventListener('keyup', function () {
                    const searchTerm = this.value.toLowerCase();

                    tableRows.forEach(row => {
                        // Skip empty state row
                        if (row.querySelector('td[colspan]')) {
                            return;
                        }

                        const name = row.cells[0].textContent.toLowerCase();
                        const genericName = row.cells[1].textContent.toLowerCase();
                        const form = row.cells[2].textContent.toLowerCase();
                        const strength = row.cells[3].textContent.toLowerCase();

                        if (name.includes(searchTerm) ||
                            genericName.includes(searchTerm) ||
                            form.includes(searchTerm) ||
                            strength.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection