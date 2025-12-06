@extends('layouts.app')

@section('title', 'Medical Supplies Inventory')
@section('page-title', 'Barangay Medical Supplies Inventory')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css?v=' . time()) }}">
    <link rel="stylesheet" href="{{ asset('css/barangay-supplies-inventory.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="page-content">
        <!-- Overview Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon-wrapper"
                    style="background: #e0f2f1; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                    <i class="bi bi-box2-heart-fill" style="font-size: 18px; color: #0d9488;"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">Total Supply Items</h3>
                    <p class="stat-number">{{ $totalSupplies ?? 0 }}</p>
                    <span class="stat-trend">Different Types</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper"
                    style="background: #d1f4e0; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                    <i class="bi bi-boxes-fill" style="font-size: 18px; color: #10b981;"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">In Stock Items</h3>
                    <p class="stat-number">{{ $inStockItems ?? 0 }}</p>
                    <span class="stat-trend">Available Now</span>
                </div>
            </div>

            <div class="stat-card alert-card-warning">
                <div class="stat-icon-wrapper"
                    style="background: #fef3c7; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 18px; color: #f59e0b;"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">Low Stock Alert</h3>
                    <p class="stat-number">{{ $lowStockCount ?? 0 }}</p>
                    <span class="stat-trend">Needs Restocking</span>
                </div>
            </div>

            <div class="stat-card alert-card-critical">
                <div class="stat-icon-wrapper"
                    style="background: #fee2e2; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                    <i class="bi bi-x-circle-fill" style="font-size: 18px; color: #ef4444;"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">Out of Stock</h3>
                    <p class="stat-number">{{ $outOfStockCount ?? 0 }}</p>
                    <span class="stat-trend">Immediate Action Required</span>
                </div>
            </div>
        </div>

        <div
            style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <div style="display: flex; gap: 12px; align-items: center; flex: 1;">
                <input type="text" id="supplySearch" class="search-input"
                    placeholder="Search by item name, category, or supplier..." style="flex: 1; min-width: 300px;">

                <select id="categoryFilter" class="filter-select">
                    <option value="">All Categories</option>
                    <option value="consumables">Consumables</option>
                    <option value="medical-equipment">Medical Equipment</option>
                    <option value="ppe">Personal Protective Equipment (PPE)</option>
                    <option value="first-aid">First Aid Supplies</option>
                    <option value="diagnostic">Diagnostic Tools</option>
                    <option value="sanitation">Sanitation & Hygiene</option>
                </select>

                <select id="stockStatusFilter" class="filter-select">
                    <option value="">All Stock Status</option>
                    <option value="in-stock">In Stock</option>
                    <option value="low-stock">Low Stock</option>
                    <option value="out-of-stock">Out of Stock</option>
                </select>

                <button type="button" class="btn btn-secondary" id="clearFiltersBtn"
                    style="background-color: transparent; border: 1px solid #6c757d; color: #6c757d;">
                    <i class="bi bi-x-circle"></i> Clear
                </button>
            </div>

            @if(in_array(auth()->user()->role ?? '', ['super_admin', 'admin']))
                <div class="header-actions">
                    <button type="button" class="btn btn-primary" id="openAddSupplyModal">
                        <i class="bi bi-plus-circle"></i> Add New Item
                    </button>
                </div>
            @endif
        </div>

        <!-- Supplies Inventory Table -->
        <div class="table-container">
            <div style="overflow-x: auto;">
                <table class="data-table" style="min-width: 1000px;">
                    <thead>
                        <tr>
                            <th width="20%">Item Name</th>
                            <th width="12%">Category</th>
                            <th width="10%">Stock on Hand</th>
                            <th width="8%">Unit</th>
                            <th width="10%">Reorder Level</th>
                            <th width="10%">Status</th>
                            <th width="15%">Last Restocked</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="suppliesTableBody">
                        @php
                            // Sample data for demonstration - Replace with actual database query
                            $supplies = [
                                [
                                    'id' => 1,
                                    'name' => 'Cotton Balls (500g)',
                                    'category' => 'Consumables',
                                    'stock' => 45,
                                    'unit' => 'pcs',
                                    'reorder_level' => 20,
                                    'status' => 'in-stock',
                                    'last_restocked' => '2025-12-01',
                                    'supplier' => 'Medical Depot Manila'
                                ],
                                [
                                    'id' => 2,
                                    'name' => 'Alcohol 70% (500ml)',
                                    'category' => 'Sanitation & Hygiene',
                                    'stock' => 15,
                                    'unit' => 'bottles',
                                    'reorder_level' => 25,
                                    'status' => 'low-stock',
                                    'last_restocked' => '2025-11-20',
                                    'supplier' => 'DOH Provincial Office'
                                ],
                                [
                                    'id' => 3,
                                    'name' => 'Disposable Syringes (3ml)',
                                    'category' => 'Medical Equipment',
                                    'stock' => 0,
                                    'unit' => 'boxes',
                                    'reorder_level' => 10,
                                    'status' => 'out-of-stock',
                                    'last_restocked' => '2025-10-15',
                                    'supplier' => 'Zuellig Pharma'
                                ],
                                [
                                    'id' => 4,
                                    'name' => 'Surgical Gloves (Medium)',
                                    'category' => 'PPE',
                                    'stock' => 120,
                                    'unit' => 'boxes',
                                    'reorder_level' => 30,
                                    'status' => 'in-stock',
                                    'last_restocked' => '2025-11-28',
                                    'supplier' => 'RiteMed Healthcare'
                                ],
                                [
                                    'id' => 5,
                                    'name' => 'Gauze Pads (4x4 inches)',
                                    'category' => 'First Aid Supplies',
                                    'stock' => 85,
                                    'unit' => 'packs',
                                    'reorder_level' => 40,
                                    'status' => 'in-stock',
                                    'last_restocked' => '2025-12-03',
                                    'supplier' => 'Medical Depot Manila'
                                ],
                                [
                                    'id' => 6,
                                    'name' => 'Digital Thermometer',
                                    'category' => 'Diagnostic Tools',
                                    'stock' => 8,
                                    'unit' => 'pcs',
                                    'reorder_level' => 5,
                                    'status' => 'in-stock',
                                    'last_restocked' => '2025-09-15',
                                    'supplier' => 'Omron Healthcare'
                                ],
                                [
                                    'id' => 7,
                                    'name' => 'Bandage Rolls (3 inches)',
                                    'category' => 'First Aid Supplies',
                                    'stock' => 22,
                                    'unit' => 'rolls',
                                    'reorder_level' => 30,
                                    'status' => 'low-stock',
                                    'last_restocked' => '2025-11-10',
                                    'supplier' => 'DOH Provincial Office'
                                ],
                                [
                                    'id' => 8,
                                    'name' => 'Face Masks (Surgical)',
                                    'category' => 'PPE',
                                    'stock' => 350,
                                    'unit' => 'boxes',
                                    'reorder_level' => 100,
                                    'status' => 'in-stock',
                                    'last_restocked' => '2025-12-05',
                                    'supplier' => 'PhilHealth Supplies Co.'
                                ]
                            ];
                        @endphp

                        @forelse($supplies as $supply)
                            <tr class="supply-row" data-id="{{ $supply['id'] }}"
                                data-category="{{ strtolower($supply['category']) }}" data-status="{{ $supply['status'] }}">
                                <td>
                                    <div class="item-info">
                                        <span class="item-name">{{ $supply['name'] }}</span>
                                        <small class="item-supplier">{{ $supply['supplier'] }}</small>
                                    </div>
                                </td>
                                <td>{{ $supply['category'] }}</td>
                                <td>{{ $supply['stock'] }}</td>
                                <td>{{ $supply['unit'] }}</td>
                                <td>{{ $supply['reorder_level'] }}</td>
                                <td>
                                    @if($supply['status'] == 'in-stock')
                                        <span class="badge badge-success">In Stock</span>
                                    @elseif($supply['status'] == 'low-stock')
                                        <span class="badge badge-warning">Low Stock</span>
                                    @else
                                        <span class="badge badge-danger">Out of Stock</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($supply['last_restocked'])->format('M d, Y') }}</td>
                                <td class="actions">
                                    <a href="javascript:void(0)" class="btn-action btn-view view-supply"
                                        data-id="{{ $supply['id'] }}">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="no-data">
                                    <i class="bi bi-inbox"></i>
                                    <p>No medical supplies found in inventory</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @php
            // For demo purposes - when you have actual data from controller, this will work automatically
            // Just pass paginated data from controller like: $supplies = MedicalSupply::paginate(15);
            $suppliesCollection = collect($supplies);
            $perPage = 15;
            $currentPage = request()->get('page', 1);
            $suppliesPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $suppliesCollection->forPage($currentPage, $perPage),
                $suppliesCollection->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        @endphp

        <!-- Pagination -->
        <div class="pagination">
            @if($suppliesPaginated->onFirstPage())
                <button class="btn-page" disabled>« Previous</button>
            @else
                <a class="btn-page" href="{{ $suppliesPaginated->previousPageUrl() }}">« Previous</a>
            @endif

            @php
                $start = max(1, $suppliesPaginated->currentPage() - 2);
                $end = min($suppliesPaginated->lastPage(), $suppliesPaginated->currentPage() + 2);
            @endphp

            @for ($page = $start; $page <= $end; $page++)
                @if ($page === $suppliesPaginated->currentPage())
                    <span class="btn-page active">{{ $page }}</span>
                @else
                    <a class="btn-page" href="{{ $suppliesPaginated->url($page) }}">{{ $page }}</a>
                @endif
            @endfor

            <span class="page-info">
                Page {{ $suppliesPaginated->currentPage() }} of {{ $suppliesPaginated->lastPage() }}
                ({{ $suppliesPaginated->total() }} total items)
            </span>

            @if($suppliesPaginated->hasMorePages())
                <a class="btn-page" href="{{ $suppliesPaginated->nextPageUrl() }}">Next »</a>
            @else
                <button class="btn-page" disabled>Next »</button>
            @endif
        </div>
    </div>

    <!-- Add New Supply Item Modal -->
    <div class="modal" id="addSupplyModal" style="display:none;">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3><i class="bi bi-plus-circle"></i> Add New Medical Supply Item</h3>
                <span class="close-modal" data-modal="addSupplyModal">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addSupplyForm" class="patient-form">
                    @csrf
                    <div class="form-section">
                        <h3 class="section-header">
                            <span class="section-indicator"></span>Item Information
                        </h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="item_name">Item Name <span class="required-asterisk">*</span></label>
                                <input type="text" id="item_name" name="item_name" class="form-control" required
                                    placeholder="e.g., Cotton Balls, Alcohol, Bandages">
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="item_category">Category <span class="required-asterisk">*</span></label>
                                <select id="item_category" name="category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <option value="consumables">Consumables</option>
                                    <option value="medical-equipment">Medical Equipment</option>
                                    <option value="ppe">Personal Protective Equipment (PPE)</option>
                                    <option value="first-aid">First Aid Supplies</option>
                                    <option value="diagnostic">Diagnostic Tools</option>
                                    <option value="sanitation">Sanitation & Hygiene</option>
                                </select>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="item_description">Description</label>
                                <textarea id="item_description" name="description" class="form-control" rows="2"
                                    placeholder="Detailed description of the item"></textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="initial_stock">Quantity Received <span
                                        class="required-asterisk">*</span></label>
                                <input type="number" id="initial_stock" name="initial_stock" class="form-control" min="0"
                                    required placeholder="0">
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="unit_of_measure">Unit of Measure <span
                                        class="required-asterisk">*</span></label>
                                <input type="text" id="unit_of_measure" name="unit" class="form-control" required
                                    placeholder="e.g., pcs, boxes, bottles, rolls">
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="reorder_level">Reorder Level <span class="required-asterisk">*</span></label>
                                <input type="number" id="reorder_level" name="reorder_level" class="form-control" min="1"
                                    required placeholder="Minimum stock level">
                                <span class="error-message"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="section-header">
                            <span class="section-indicator"></span>Source & Supplier Information
                        </h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="receive_source">Source/Supplier <span class="required-asterisk">*</span></label>
                                <select id="receive_source" name="source" class="form-control" required>
                                    <option value="">Select Source</option>
                                    <option value="doh">DOH Provincial/Regional Office</option>
                                    <option value="lgu">LGU (Municipal/City Health Office)</option>
                                    <option value="barangay">Barangay Procurement</option>
                                    <option value="donation">Donation (NGO/Private Sector)</option>
                                    <option value="philhealth">PhilHealth</option>
                                    <option value="other">Other</option>
                                </select>
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="supplier_name">Supplier Name</label>
                                <input type="text" id="supplier_name" name="supplier_name" class="form-control"
                                    placeholder="e.g., DOH Provincial Office, Medical Depot Manila">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="receive_remarks">Remarks/Notes</label>
                                <textarea id="receive_remarks" name="remarks" class="form-control" rows="2"
                                    placeholder="Additional notes about this delivery"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" data-close-modal="addSupplyModal">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Save Supply Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Issue/Dispense Supply Modal -->
    <div class="modal" id="issueSupplyModal" style="display:none;">
        <div class="modal-content modal-medium">
            <div class="modal-header">
                <h3><i class="bi bi-box-arrow-right"></i> Issue/Dispense Supply</h3>
                <span class="close-modal" data-modal="issueSupplyModal">&times;</span>
            </div>
            <div class="modal-body">
                <form id="issueSupplyForm" class="patient-form">
                    @csrf
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <span id="currentStockInfo">Current stock will be displayed here</span>
                    </div>

                    <div class="form-section">
                        <h3 class="section-header">
                            <span class="section-indicator"></span>Dispensing Details
                        </h3>

                        <input type="hidden" id="issue_supply_id" name="supply_id">

                        <div class="form-row">
                            <div class="form-group">
                                <label for="issue_item_name">Item Name</label>
                                <input type="text" id="issue_item_name" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="issue_quantity">Quantity to Issue <span
                                        class="required-asterisk">*</span></label>
                                <input type="number" id="issue_quantity" name="quantity" class="form-control" min="1"
                                    required placeholder="0">
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="issue_date">Date Issued <span class="required-asterisk">*</span></label>
                                <input type="date" id="issue_date" name="issued_date" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="issued_to">Issued To <span class="required-asterisk">*</span></label>
                                <input type="text" id="issued_to" name="issued_to" class="form-control" required
                                    placeholder="Name of recipient/patient/program">
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="issue_purpose">Purpose <span class="required-asterisk">*</span></label>
                                <select id="issue_purpose" name="purpose" class="form-control" required>
                                    <option value="">Select Purpose</option>
                                    <option value="patient-treatment">Patient Treatment</option>
                                    <option value="prenatal-program">Prenatal Program</option>
                                    <option value="immunization">Immunization Program</option>
                                    <option value="family-planning">Family Planning</option>
                                    <option value="medical-mission">Medical Mission</option>
                                    <option value="emergency">Emergency Response</option>
                                    <option value="other">Other</option>
                                </select>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="issue_remarks">Remarks/Notes</label>
                                <textarea id="issue_remarks" name="remarks" class="form-control" rows="2"
                                    placeholder="Additional information about this issuance"></textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="issued_by">Issued By <span class="required-asterisk">*</span></label>
                                <input type="text" id="issued_by" name="issued_by" class="form-control"
                                    value="{{ auth()->user()->name }}" required readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" data-close-modal="issueSupplyModal">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Issue Supply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Supply Details Modal -->
    <div class="modal" id="viewSupplyModal" style="display:none;">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3><i class="bi bi-eye"></i> Supply Item Details</h3>
                <span class="close-modal" data-modal="viewSupplyModal">&times;</span>
            </div>
            <div class="modal-body" id="supplyDetailsContent">
                <div class="loading-spinner">
                    <i class="bi bi-hourglass-split"></i>
                    <p>Loading supply details...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Modal Controls
            const modals = {
                addSupplyModal: document.getElementById('addSupplyModal'),
                issueSupplyModal: document.getElementById('issueSupplyModal'),
                viewSupplyModal: document.getElementById('viewSupplyModal')
            };

            // Open Modal Functions
            document.getElementById('openAddSupplyModal')?.addEventListener('click', () => {
                modals.addSupplyModal.style.display = 'flex';
            });

            // Close Modal Functions
            document.querySelectorAll('.close-modal').forEach(btn => {
                btn.addEventListener('click', function () {
                    const modalId = this.getAttribute('data-modal');
                    if (modalId && modals[modalId]) {
                        modals[modalId].style.display = 'none';
                    }
                });
            });

            document.querySelectorAll('[data-close-modal]').forEach(btn => {
                btn.addEventListener('click', function () {
                    const modalId = this.getAttribute('data-close-modal');
                    if (modals[modalId]) {
                        modals[modalId].style.display = 'none';
                    }
                });
            });

            // Close modal when clicking outside
            window.addEventListener('click', (event) => {
                Object.values(modals).forEach(modal => {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            });

            // Search Functionality
            const searchInput = document.getElementById('supplySearch');
            const categoryFilter = document.getElementById('categoryFilter');
            const statusFilter = document.getElementById('stockStatusFilter');

            function filterSupplies() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCategory = categoryFilter.value.toLowerCase();
                const selectedStatus = statusFilter.value;

                document.querySelectorAll('.supply-row').forEach(row => {
                    const name = row.querySelector('.item-name').textContent.toLowerCase();
                    const supplier = row.querySelector('.item-supplier').textContent.toLowerCase();
                    const category = row.getAttribute('data-category');
                    const status = row.getAttribute('data-status');

                    const matchesSearch = name.includes(searchTerm) || supplier.includes(searchTerm);
                    const matchesCategory = !selectedCategory || category.includes(selectedCategory.replace(/\s+/g, '-').replace(/&/g, ''));
                    const matchesStatus = !selectedStatus || status === selectedStatus;

                    row.style.display = (matchesSearch && matchesCategory && matchesStatus) ? '' : 'none';
                });
            }

            searchInput?.addEventListener('input', filterSupplies);
            categoryFilter?.addEventListener('change', filterSupplies);
            statusFilter?.addEventListener('change', filterSupplies);

            // Clear Filters
            document.getElementById('clearFiltersBtn')?.addEventListener('click', function () {
                document.getElementById('supplySearch').value = '';
                document.getElementById('categoryFilter').value = '';
                document.getElementById('stockStatusFilter').value = '';
                filterSupplies();
            });

            // Issue Supply Button
            document.querySelectorAll('.issue-supply').forEach(btn => {
                btn.addEventListener('click', function () {
                    const supplyId = this.getAttribute('data-id');
                    const supplyName = this.getAttribute('data-name');
                    const currentStock = this.getAttribute('data-stock');

                    document.getElementById('issue_supply_id').value = supplyId;
                    document.getElementById('issue_item_name').value = supplyName;
                    document.getElementById('currentStockInfo').textContent =
                        `Current stock: ${currentStock} units available`;

                    modals.issueSupplyModal.style.display = 'flex';
                });
            });

            // View Supply Details
            document.querySelectorAll('.view-supply').forEach(btn => {
                btn.addEventListener('click', function () {
                    const supplyId = this.getAttribute('data-id');
                    modals.viewSupplyModal.style.display = 'flex';
                    // Load supply details via AJAX here
                });
            });

            // Form Submissions
            document.getElementById('addSupplyForm')?.addEventListener('submit', function (e) {
                e.preventDefault();
                // Handle form submission via AJAX
                console.log('Add supply form submitted');
                alert('Add New Supply functionality - to be implemented with backend');
            });

            document.getElementById('issueSupplyForm')?.addEventListener('submit', function (e) {
                e.preventDefault();
                // Handle form submission via AJAX
                console.log('Issue supply form submitted');
                alert('Issue/Dispense Supply functionality - to be implemented with backend');
            });
        });
    </script>
@endpush