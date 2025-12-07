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
        <form method="GET" action="{{ route('medical-supplies.history') }}" class="filters">
            <div class="filter-options" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <input type="text" id="searchHistory" name="search" placeholder="Search by item name, source, or handler..."
                    class="search-input" value="{{ request('search') }}" style="flex: 1; min-width: 300px;">

                <select id="sourceFilter" name="source" class="filter-select">
                    <option value="">All Sources</option>
                    @foreach($sources as $source)
                        <option value="{{ $source }}" {{ request('source') === $source ? 'selected' : '' }}>
                            {{ $source }}
                        </option>
                    @endforeach
                </select>

                <select id="dateRangeFilter" name="date_range" class="filter-select">
                    <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date_range') === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date_range', 'month') === 'month' ? 'selected' : '' }}>This Month
                    </option>
                    <option value="quarter" {{ request('date_range') === 'quarter' ? 'selected' : '' }}>This Quarter</option>
                    <option value="year" {{ request('date_range') === 'year' ? 'selected' : '' }}>This Year</option>
                </select>

                <a href="{{ route('medical-supplies.history') }}" class="btn btn-secondary"
                    style="padding: 9px 15px !important; font-size: 14px; font-weight: normal; display: inline-flex !important; align-items: center; gap: 6px; height: 38px; line-height: 1;">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
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
                            <th width="18%">Received From</th>
                            <th width="15%">Handled By</th>
                            <th width="20%">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        @forelse($history as $record)
                            <tr>
                                <td>
                                    <div class="transaction-date">
                                        {{ $record->date_received->format('M d, Y') }}<br>
                                        <small style="color: #6c757d;">{{ $record->created_at->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="item-name">{{ $record->item_name }}</span>
                                </td>
                                <td>
                                    @php
                                        $qty = (int) $record->quantity;
                                        $isOutgoing = $qty < 0;
                                        $displayQty = abs($qty);
                                        $badgeClass = $isOutgoing ? 'quantity-out' : 'quantity-in';
                                        $sign = $isOutgoing ? '-' : '+';
                                    @endphp
                                    <span class="quantity-badge {{ $badgeClass }}">
                                        {{ $sign }}{{ $displayQty }}
                                    </span>
                                </td>
                                <td>
                                    <small style="color: #6c757d;">{{ $record->received_from ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <small style="color: #6c757d;">{{ $record->handled_by }}</small>
                                </td>
                                <td class="actions">
                                    <a href="javascript:void(0)" class="btn-action btn-view view-transaction"
                                        data-id="{{ $record->id }}">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding: 40px; color: #7f8c8d;">
                                    <i class="bi bi-inbox"
                                        style="font-size: 48px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                                    No transaction history found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($history->hasPages())
            <div class="pagination">
                @if($history->onFirstPage())
                    <button class="btn-page" disabled>« Previous</button>
                @else
                    <a class="btn-page" href="{{ $history->appends(request()->query())->previousPageUrl() }}">« Previous</a>
                @endif

                @php
                    $start = max(1, $history->currentPage() - 2);
                    $end = min($history->lastPage(), $history->currentPage() + 2);
                @endphp

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page === $history->currentPage())
                        <span class="btn-page active">{{ $page }}</span>
                    @else
                        <a class="btn-page" href="{{ $history->appends(request()->query())->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                <span class="page-info">
                    Page {{ $history->currentPage() }} of {{ $history->lastPage() }} ({{ $history->total() }} total records)
                </span>

                @if($history->hasMorePages())
                    <a class="btn-page" href="{{ $history->appends(request()->query())->nextPageUrl() }}">Next »</a>
                @else
                    <button class="btn-page" disabled>Next »</button>
                @endif
            </div>
        @endif
    </div>

    <!-- View Transaction Modal -->
    <div class="modal" id="viewTransactionModal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Transaction Details</h3>
                <span class="close-modal" data-close-modal="viewTransactionModal">&times;</span>
            </div>
            <div class="modal-body">
                <div class="form-section section-patient-info">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Transaction Information
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Item Name</label>
                            <div class="form-control" id="trans_item_name" style="background: #f8f9fa; border: none;"></div>
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <div class="form-control" id="trans_quantity"
                                style="background: #f8f9fa; border: none; font-weight: bold;"></div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Received From</label>
                            <div class="form-control" id="trans_received_from" style="background: #f8f9fa; border: none;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Date Received</label>
                            <div class="form-control" id="trans_date_received" style="background: #f8f9fa; border: none;">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Handled By</label>
                            <div class="form-control" id="trans_handled_by" style="background: #f8f9fa; border: none;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Transaction Date/Time</label>
                            <div class="form-control" id="trans_created_at" style="background: #f8f9fa; border: none;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" data-close-modal="viewTransactionModal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            const viewTransactionModal = document.getElementById('viewTransactionModal');
            window.addEventListener('click', function (event) {
                if (event.target === viewTransactionModal) {
                    closeModal('viewTransactionModal');
                }
            });

            // View transaction functionality
            document.querySelectorAll('.view-transaction').forEach(button => {
                button.addEventListener('click', function () {
                    const recordId = this.dataset.id;
                    const row = this.closest('tr');

                    // Extract data from the row
                    const cells = row.querySelectorAll('td');
                    const dateText = cells[0].querySelector('.transaction-date').textContent.trim();
                    const itemName = cells[1].querySelector('.item-name').textContent.trim();
                    const quantity = cells[2].querySelector('.quantity-badge').textContent.trim();
                    const receivedFrom = cells[3].querySelector('small').textContent.trim();
                    const handledBy = cells[4].querySelector('small').textContent.trim();

                    // Populate modal
                    document.getElementById('trans_item_name').textContent = itemName;
                    document.getElementById('trans_quantity').textContent = quantity;
                    document.getElementById('trans_received_from').textContent = receivedFrom;
                    document.getElementById('trans_date_received').textContent = dateText.split('\n')[0].trim();
                    document.getElementById('trans_handled_by').textContent = handledBy;
                    document.getElementById('trans_created_at').textContent = dateText.replace(/\s+/g, ' ').trim();

                    openModal('viewTransactionModal');
                });
            });

            // Auto-submit filter form
            const filterForm = document.querySelector('.filters');
            const searchInput = document.getElementById('searchHistory');
            const sourceFilter = document.getElementById('sourceFilter');
            const dateRangeFilter = document.getElementById('dateRangeFilter');
            
            let searchTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    filterForm.submit();
                }, 500);
            });
            
            sourceFilter.addEventListener('change', () => filterForm.submit());
            dateRangeFilter.addEventListener('change', () => filterForm.submit());
        });
    </script>
@endpush