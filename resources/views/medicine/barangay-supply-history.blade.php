@extends('layouts.app')

@section('title', 'Supply History')
@section('page-title', 'Medical Supplies History')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css?v=' . time()) }}">
    <link rel="stylesheet" href="{{ asset('css/barangay-supplies-inventory.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="page-content">
        <!-- Search and Filter Section -->
        <form method="GET" class="filters">
            <div class="filter-options" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <input type="text" id="searchHistory" name="search"
                    placeholder="Search by item name, reference, or source..." class="search-input"
                    value="{{ request('search') }}" style="flex: 1; min-width: 300px;">

                <select id="transactionTypeFilter" name="type" class="filter-select">
                    <option value="">All Transaction Types</option>
                    <option value="received">Received</option>
                    <option value="issued">Issued</option>
                    <option value="adjustment">Adjustment</option>
                    <option value="return">Return</option>
                </select>

                <select id="sourceFilter" name="source" class="filter-select">
                    <option value="">All Sources</option>
                    <option value="doh">DOH</option>
                    <option value="lgu">LGU</option>
                    <option value="philhealth">PhilHealth</option>
                    <option value="donation">Donation</option>
                    <option value="barangay">Barangay Fund</option>
                </select>

                <select id="dateRangeFilter" name="date_range" class="filter-select">
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month" selected>This Month</option>
                    <option value="quarter">This Quarter</option>
                    <option value="year">This Year</option>
                    <option value="custom">Custom Range</option>
                </select>

                <button type="button" class="btn btn-secondary" id="clearFiltersBtn">
                    <i class="bi bi-x-circle"></i> Clear
                </button>
            </div>
        </form>

        <div class="table-container">
            <div style="overflow-x: auto;">
                <table class="data-table" style="min-width: 1000px;">
                    <thead>
                        <tr>
                            <th width="12%">Date Received</th>
                            <th width="25%">Item Name</th>
                            <th width="10%">Quantity</th>
                            <th width="18%">Handled By</th>
                            <th width="15%">Type</th>
                            <th width="20%">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        @php
                            $historyData = [
                                [
                                    'id' => 1,
                                    'date' => '2024-12-07 09:30:00',
                                    'type' => 'received',
                                    'item_name' => 'Disposable Syringes (5ml)',
                                    'quantity' => 200,
                                    'source' => 'DOH Regional Office',
                                    'reference' => 'DOH-2024-1234',
                                    'handled_by' => 'Admin Staff',
                                    'balance' => 450,
                                ],
                                [
                                    'id' => 2,
                                    'date' => '2024-12-07 11:15:00',
                                    'type' => 'issued',
                                    'item_name' => 'Alcohol 70% (500ml)',
                                    'quantity' => 5,
                                    'source' => 'Resident: Maria Santos',
                                    'reference' => 'ISS-2024-0089',
                                    'handled_by' => 'BHW Cruz',
                                    'balance' => 145,
                                ],
                                [
                                    'id' => 3,
                                    'date' => '2024-12-06 14:20:00',
                                    'type' => 'received',
                                    'item_name' => 'Surgical Gloves (Box)',
                                    'quantity' => 20,
                                    'source' => 'LGU Health Office',
                                    'reference' => 'LGU-2024-0567',
                                    'handled_by' => 'Admin Staff',
                                    'balance' => 78,
                                ],
                                [
                                    'id' => 4,
                                    'date' => '2024-12-06 10:45:00',
                                    'type' => 'issued',
                                    'item_name' => 'Cotton Balls (500g)',
                                    'quantity' => 10,
                                    'source' => 'Vaccination Program',
                                    'reference' => 'ISS-2024-0088',
                                    'handled_by' => 'Midwife Santos',
                                    'balance' => 240,
                                ],
                                [
                                    'id' => 5,
                                    'date' => '2024-12-05 16:30:00',
                                    'type' => 'received',
                                    'item_name' => 'Face Masks (Box of 50)',
                                    'quantity' => 50,
                                    'source' => 'Donation - Private Company',
                                    'reference' => 'DON-2024-0034',
                                    'handled_by' => 'Admin Staff',
                                    'balance' => 320,
                                ],
                                [
                                    'id' => 6,
                                    'date' => '2024-12-05 13:10:00',
                                    'type' => 'issued',
                                    'item_name' => 'Bandages (2" x 5yds)',
                                    'quantity' => 15,
                                    'source' => 'Emergency Response',
                                    'reference' => 'ISS-2024-0087',
                                    'handled_by' => 'BHW Cruz',
                                    'balance' => 85,
                                ],
                                [
                                    'id' => 7,
                                    'date' => '2024-12-04 09:00:00',
                                    'type' => 'adjustment',
                                    'item_name' => 'Thermometer (Digital)',
                                    'quantity' => -2,
                                    'source' => 'Stock Count Correction',
                                    'reference' => 'ADJ-2024-0012',
                                    'handled_by' => 'Admin Staff',
                                    'balance' => 28,
                                ],
                                [
                                    'id' => 8,
                                    'date' => '2024-12-03 15:45:00',
                                    'type' => 'received',
                                    'item_name' => 'Vitamin B-Complex (Bottle)',
                                    'quantity' => 30,
                                    'source' => 'PhilHealth Program',
                                    'reference' => 'PH-2024-0892',
                                    'handled_by' => 'Admin Staff',
                                    'balance' => 180,
                                ],
                                [
                                    'id' => 9,
                                    'date' => '2024-12-03 11:20:00',
                                    'type' => 'issued',
                                    'item_name' => 'Disposable Syringes (5ml)',
                                    'quantity' => 25,
                                    'source' => 'Immunization Drive',
                                    'reference' => 'ISS-2024-0086',
                                    'handled_by' => 'Midwife Santos',
                                    'balance' => 250,
                                ],
                                [
                                    'id' => 10,
                                    'date' => '2024-12-02 14:00:00',
                                    'type' => 'return',
                                    'item_name' => 'Blood Pressure Monitor',
                                    'quantity' => 1,
                                    'source' => 'Return from Field Visit',
                                    'reference' => 'RET-2024-0005',
                                    'handled_by' => 'BHW Cruz',
                                    'balance' => 12,
                                ],
                            ];
                        @endphp

                        @forelse($historyData as $transaction)
                            <tr class="history-row" data-type="{{ $transaction['type'] }}"
                                data-date="{{ $transaction['date'] }}">
                                <td>
                                    <div class="transaction-date">
                                        {{ date('M d, Y', strtotime($transaction['date'])) }}<br>
                                        <small
                                            style="color: #6c757d;">{{ date('h:i A', strtotime($transaction['date'])) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="item-name">{{ $transaction['item_name'] }}</span>
                                </td>
                                <td>
                                    <span
                                        class="quantity-badge {{ $transaction['type'] == 'received' || $transaction['type'] == 'return' ? 'quantity-in' : ($transaction['type'] == 'issued' ? 'quantity-out' : '') }}">
                                        {{ $transaction['type'] == 'received' || $transaction['type'] == 'return' ? '+' : ($transaction['type'] == 'issued' ? '-' : '') }}{{ abs($transaction['quantity']) }}
                                    </span>
                                </td>
                                <td>
                                    <small style="color: #6c757d;">{{ $transaction['handled_by'] }}</small>
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'received' => 'success',
                                            'issued' => 'primary',
                                            'adjustment' => 'warning',
                                            'return' => 'info'
                                        ];
                                        $typeIcons = [
                                            'received' => 'box-arrow-in-down',
                                            'issued' => 'box-arrow-up',
                                            'adjustment' => 'wrench',
                                            'return' => 'arrow-return-left'
                                        ];
                                    @endphp
                                    <span class="badge-{{ $typeColors[$transaction['type']] ?? 'secondary' }}">
                                        {{ ucfirst($transaction['type']) }}
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="javascript:void(0)" class="btn-action btn-view view-transaction"
                                        data-id="{{ $transaction['id'] }}">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="no-data">
                                    <i class="bi bi-inbox"></i>
                                    <p>No transaction history found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @php
            // For demo purposes - when you have actual data from controller, this will work automatically
            // Just pass paginated data from controller like: $transactions = SupplyTransaction::paginate(15);
            $historyCollection = collect($historyData);
            $perPage = 15;
            $currentPage = request()->get('page', 1);
            $historyPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $historyCollection->forPage($currentPage, $perPage),
                $historyCollection->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        @endphp

        <!-- Pagination -->
        <div class="pagination">
            @if($historyPaginated->onFirstPage())
                <button class="btn-page" disabled>« Previous</button>
            @else
                <a class="btn-page" href="{{ $historyPaginated->previousPageUrl() }}">« Previous</a>
            @endif

            @php
                $start = max(1, $historyPaginated->currentPage() - 2);
                $end = min($historyPaginated->lastPage(), $historyPaginated->currentPage() + 2);
            @endphp

            @for ($page = $start; $page <= $end; $page++)
                @if ($page === $historyPaginated->currentPage())
                    <span class="btn-page active">{{ $page }}</span>
                @else
                    <a class="btn-page" href="{{ $historyPaginated->url($page) }}">{{ $page }}</a>
                @endif
            @endfor

            <span class="page-info">
                Page {{ $historyPaginated->currentPage() }} of {{ $historyPaginated->lastPage() }}
                ({{ $historyPaginated->total() }} total transactions)
            </span>

            @if($historyPaginated->hasMorePages())
                <a class="btn-page" href="{{ $historyPaginated->nextPageUrl() }}">Next »</a>
            @else
                <button class="btn-page" disabled>Next »</button>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Search functionality
        document.getElementById('searchHistory')?.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            filterTransactions();
        });

        // Filter dropdowns
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', filterTransactions);
        });

        function filterTransactions() {
            const searchTerm = document.getElementById('searchHistory').value.toLowerCase();
            const typeFilter = document.getElementById('transactionTypeFilter').value;
            const sourceFilter = document.getElementById('sourceFilter').value;

            document.querySelectorAll('.history-row').forEach(row => {
                const text = row.textContent.toLowerCase();
                const type = row.getAttribute('data-type');

                const matchesSearch = text.includes(searchTerm);
                const matchesType = !typeFilter || type === typeFilter;

                row.style.display = (matchesSearch && matchesType) ? '' : 'none';
            });
        }

        // Clear Filters
        document.getElementById('clearFiltersBtn')?.addEventListener('click', function () {
            document.getElementById('searchHistory').value = '';
            document.getElementById('transactionTypeFilter').value = '';
            document.getElementById('sourceFilter').value = '';
            document.getElementById('dateRangeFilter').value = 'month';
            filterTransactions();
        });
    </script>
@endpush