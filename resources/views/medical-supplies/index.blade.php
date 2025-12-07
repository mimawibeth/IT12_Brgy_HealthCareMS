@extends('layouts.app')

@section('title', 'Medical Supplies')
@section('page-title', 'Medical Supplies Inventory')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css?v=' . time()) }}">
    <link rel="stylesheet" href="{{ asset('css/medicine.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="page-content">
        <!-- Search and Filter Section -->
        <form method="GET" action="{{ route('medical-supplies.index') }}" class="filters">
            <div class="filter-options"
                style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-bottom: 1.5rem;">
                <input type="text" name="search" placeholder="Search supplies..." class="search-input"
                    value="{{ request('search') }}" style="flex: 1; min-width: 300px;">

                <select name="category" class="filter-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary"
                    style="padding: 10px 15px !important; font-size: 14px; font-weight: normal;">
                    <i class="bi bi-search"></i> Filter
                </button>

                @if(request()->hasAny(['search', 'category']))
                    <a href="{{ route('medical-supplies.index') }}" class="btn btn-secondary"
                        style="padding: 10px 15px !important; font-size: 14px; font-weight: normal;">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                @endif

                <button type="button" class="btn btn-primary" id="openAddSupplyModal"
                    style="padding: 10px 15px !important; font-size: 14px; font-weight: normal; margin-left: auto; white-space: nowrap;">
                    <i class="bi bi-plus-circle"></i> Add New Item
                </button>
            </div>
        </form>

        <div class="table-container">
            <div style="overflow-x: auto;">
                <table class="data-table" style="min-width: 800px;">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Unit</th>
                            <th>Quantity on Hand</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplies as $supply)
                            <tr>
                                <td>{{ $supply->item_name }}</td>
                                <td>{{ $supply->category ?? 'N/A' }}</td>
                                <td>{{ $supply->description ?? 'N/A' }}</td>
                                <td>{{ $supply->unit_of_measure ?? 'N/A' }}</td>
                                <td>{{ $supply->quantity_on_hand }}</td>
                                <td class="actions">
                                    <a href="javascript:void(0)" class="btn-action btn-view view-supply"
                                        data-id="{{ $supply->id }}">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding: 40px; color: #7f8c8d;">
                                    <i class="bi bi-inbox"
                                        style="font-size: 48px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                                    No supplies found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($supplies->hasPages())
            <div class="pagination">
                @if($supplies->onFirstPage())
                    <button class="btn-page" disabled>« Previous</button>
                @else
                    <a class="btn-page" href="{{ $supplies->appends(request()->query())->previousPageUrl() }}">« Previous</a>
                @endif

                @php
                    $start = max(1, $supplies->currentPage() - 2);
                    $end = min($supplies->lastPage(), $supplies->currentPage() + 2);
                @endphp

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page === $supplies->currentPage())
                        <span class="btn-page active">{{ $page }}</span>
                    @else
                        <a class="btn-page" href="{{ $supplies->appends(request()->query())->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                <span class="page-info">
                    Page {{ $supplies->currentPage() }} of {{ $supplies->lastPage() }} ({{ $supplies->total() }} total supplies)
                </span>

                @if($supplies->hasMorePages())
                    <a class="btn-page" href="{{ $supplies->appends(request()->query())->nextPageUrl() }}">Next »</a>
                @else
                    <button class="btn-page" disabled>Next »</button>
                @endif
            </div>
        @endif
    </div>

    <!-- View Supply Modal -->
    <div class="modal" id="viewSupplyModal" style="display:none;">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>Supply Details</h3>
                <span class="close-modal" data-close-modal="viewSupplyModal">&times;</span>
            </div>
            <div class="modal-body">
                <div class="form-section section-patient-info">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Supply Information
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Item Name</label>
                            <div class="form-control" id="view_item_name" style="background: #f8f9fa; border: none;"></div>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <div class="form-control" id="view_category" style="background: #f8f9fa; border: none;"></div>
                        </div>
                        <div class="form-group">
                            <label>Unit of Measure</label>
                            <div class="form-control" id="view_unit" style="background: #f8f9fa; border: none;"></div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label>Description</label>
                            <div class="form-control" id="view_description"
                                style="background: #f8f9fa; border: none; min-height: 60px;"></div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Quantity on Hand</label>
                            <div class="form-control" id="view_quantity"
                                style="background: #f8f9fa; border: none; font-weight: bold; font-size: 18px;"></div>
                        </div>
                    </div>
                </div>

                <div class="form-section section-assessment">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Transaction History
                    </h3>
                    <div class="table-container">
                        <table class="data-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Quantity</th>
                                    <th>Received From</th>
                                    <th>Handled By</th>
                                </tr>
                            </thead>
                            <tbody id="supply_history_list">
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 20px;">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" data-close-modal="viewSupplyModal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Supply Modal -->
    <div class="modal" id="addSupplyModal" style="display:none;">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>Add New Supply</h3>
                <span class="close-modal" data-close-modal="addSupplyModal">&times;</span>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('medical-supplies.store') }}" class="patient-form">
                    @csrf

                    <div class="form-section section-patient-info">
                        <h3 class="section-header">
                            <span class="section-indicator"></span>Supply Information
                        </h3>

                        <div class="form-row">
                            <div class="form-group" style="position: relative;">
                                <label for="item_name">Item Name <span class="required-asterisk">*</span></label>
                                <input type="text" id="item_name" name="item_name" class="form-control" required
                                    placeholder="Search or enter new item name" autocomplete="off">
                                <input type="hidden" id="existing_supply_id">
                                <div id="supply_search_results"
                                    style="position: absolute; background: white; border: 1px solid #ddd; border-radius: 4px; max-height: 200px; overflow-y: auto; width: 100%; z-index: 1000; display: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="category">Category</label>
                                <input type="text" id="category" name="category" class="form-control"
                                    placeholder="e.g., Diagnostic, Surgical, Consumable">
                            </div>

                            <div class="form-group">
                                <label for="unit_of_measure">Unit of Measure</label>
                                <input type="text" id="unit_of_measure" name="unit_of_measure" class="form-control"
                                    placeholder="e.g., pieces, boxes, rolls">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="2"
                                    placeholder="Additional details about the item"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-section section-assessment">
                        <h3 class="section-header">
                            <span class="section-indicator"></span>Receipt Information
                        </h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="quantity">Quantity <span class="required-asterisk">*</span></label>
                                <input type="number" id="quantity" name="quantity" class="form-control" min="1" required
                                    value="1">
                            </div>

                            <div class="form-group">
                                <label for="received_from">Received From</label>
                                <input type="text" id="received_from" name="received_from" class="form-control"
                                    placeholder="DOH, LGU, Donation, etc.">
                            </div>

                            <div class="form-group">
                                <label for="date_received">Date Received <span class="required-asterisk">*</span></label>
                                <input type="date" id="date_received" name="date_received" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" data-close-modal="addSupplyModal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Supply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const addSupplyModal = document.getElementById('addSupplyModal');
                const openAddSupplyBtn = document.getElementById('openAddSupplyModal');

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

                if (openAddSupplyBtn && addSupplyModal) {
                    openAddSupplyBtn.addEventListener('click', function () {
                        openModal('addSupplyModal');
                    });
                }

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
                    if (event.target === addSupplyModal) {
                        closeModal('addSupplyModal');
                    }
                });

                // Supply search functionality
                const itemNameInput = document.getElementById('item_name');
                const supplySearchResults = document.getElementById('supply_search_results');
                const categoryInput = document.getElementById('category');
                const descriptionInput = document.getElementById('description');
                const unitInput = document.getElementById('unit_of_measure');
                const existingSupplyId = document.getElementById('existing_supply_id');
                let supplySearchTimeout;

                if (itemNameInput && supplySearchResults) {
                    itemNameInput.addEventListener('input', function () {
                        clearTimeout(supplySearchTimeout);
                        const query = this.value.trim();

                        if (query.length < 2) {
                            supplySearchResults.style.display = 'none';
                            enableFields();
                            existingSupplyId.value = '';
                            return;
                        }

                        supplySearchTimeout = setTimeout(async () => {
                            try {
                                const response = await fetch(`/api/medical-supplies/search?q=${encodeURIComponent(query)}`);
                                const supplies = await response.json();

                                if (supplies.length > 0) {
                                    supplySearchResults.innerHTML = supplies.map(supply => `
                                                        <div class="supply-result-item" 
                                                            data-id="${supply.id}"
                                                            data-name="${supply.item_name}"
                                                            data-category="${supply.category || ''}"
                                                            data-description="${supply.description || ''}"
                                                            data-unit="${supply.unit_of_measure || ''}"
                                                            style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">
                                                            <strong>${supply.item_name}</strong>
                                                            ${supply.category ? `<span style="color: #666;"> - ${supply.category}</span>` : ''}
                                                        </div>
                                                    `).join('');
                                    supplySearchResults.style.display = 'block';

                                    // Add click handlers to results
                                    document.querySelectorAll('.supply-result-item').forEach(item => {
                                        item.addEventListener('mouseenter', function () {
                                            this.style.backgroundColor = '#f0f0f0';
                                        });
                                        item.addEventListener('mouseleave', function () {
                                            this.style.backgroundColor = 'white';
                                        });
                                        item.addEventListener('click', function () {
                                            itemNameInput.value = this.dataset.name;
                                            categoryInput.value = this.dataset.category;
                                            descriptionInput.value = this.dataset.description;
                                            unitInput.value = this.dataset.unit;
                                            existingSupplyId.value = this.dataset.id;

                                            // Make fields read-only
                                            disableFields();

                                            supplySearchResults.style.display = 'none';
                                        });
                                    });
                                } else {
                                    supplySearchResults.style.display = 'none';
                                    enableFields();
                                    existingSupplyId.value = '';
                                }
                            } catch (error) {
                                console.error('Supply search error:', error);
                                supplySearchResults.style.display = 'none';
                            }
                        }, 300);
                    });

                    // Close search results when clicking outside
                    document.addEventListener('click', function (e) {
                        if (!itemNameInput.contains(e.target) && !supplySearchResults.contains(e.target)) {
                            supplySearchResults.style.display = 'none';
                        }
                    });
                }

                function disableFields() {
                    categoryInput.readOnly = true;
                    descriptionInput.readOnly = true;
                    unitInput.readOnly = true;
                    categoryInput.style.backgroundColor = '#f0f0f0';
                    descriptionInput.style.backgroundColor = '#f0f0f0';
                    unitInput.style.backgroundColor = '#f0f0f0';
                }

                function enableFields() {
                    categoryInput.readOnly = false;
                    descriptionInput.readOnly = false;
                    unitInput.readOnly = false;
                    categoryInput.style.backgroundColor = '';
                    descriptionInput.style.backgroundColor = '';
                    unitInput.style.backgroundColor = '';
                }

                // View supply functionality
                document.querySelectorAll('.view-supply').forEach(button => {
                    button.addEventListener('click', async function () {
                        const supplyId = this.dataset.id;
                        try {
                            const response = await fetch(`/medical-supplies/${supplyId}`);
                            const data = await response.json();

                            // Populate supply details
                            document.getElementById('view_item_name').textContent = data.item_name || 'N/A';
                            document.getElementById('view_category').textContent = data.category || 'N/A';
                            document.getElementById('view_unit').textContent = data.unit_of_measure || 'N/A';
                            document.getElementById('view_description').textContent = data.description || 'N/A';
                            document.getElementById('view_quantity').textContent = data.quantity_on_hand || '0';

                            // Populate transaction history
                            const historyList = document.getElementById('supply_history_list');
                            if (data.supply_history && data.supply_history.length > 0) {
                                historyList.innerHTML = data.supply_history.map(record => `
                                            <tr>
                                                <td>${new Date(record.date_received).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                                                <td><span class="quantity-badge quantity-in">+${record.quantity}</span></td>
                                                <td>${record.received_from || 'N/A'}</td>
                                                <td>${record.handled_by}</td>
                                            </tr>
                                        `).join('');
                            } else {
                                historyList.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px; color: #999;">No transaction history</td></tr>';
                            }

                            openModal('viewSupplyModal');
                        } catch (error) {
                            console.error('Error fetching supply details:', error);
                            alert('Error loading supply details');
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection