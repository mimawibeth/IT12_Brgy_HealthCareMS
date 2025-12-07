{{-- Dispense History --}}
@extends('layouts.app')

@section('title', 'Dispense History')
@section('page-title', 'Dispense History')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
    <link rel="stylesheet" href="{{ asset('css/medicine.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <!-- Search and Filter Section -->
        <form method="GET" action="{{ route('medicine.dispense') }}" class="filters" id="dispenseFilterForm">
            <div class="filter-options" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-bottom: 1.5rem;">
                <input type="text" name="dispensed_to" id="dispensedToSearch" placeholder="Search by medicine..." class="search-input"
                    value="{{ request('dispensed_to') }}" style="flex: 1; min-width: 250px;">

                <select name="medicine_id" id="medicineFilter" class="filter-select">
                    <option value="">All Medicines</option>
                    @foreach($medicines as $medicine)
                        <option value="{{ $medicine->id }}" @selected(request('medicine_id') == $medicine->id)>
                            {{ $medicine->name }}
                        </option>
                    @endforeach
                </select>

                <div style="display: flex; align-items: center; gap: 8px;">
                    <label style="font-size: 12px; color: #6c757d; margin: 0; font-weight: 500; white-space: nowrap;">From</label>
                    <input type="date" name="from_date" id="fromDateFilter" class="filter-select"
                        value="{{ request('from_date') }}" style="margin: 0; max-width: 160px;">
                </div>

                <div style="display: flex; align-items: center; gap: 8px;">
                    <label style="font-size: 12px; color: #6c757d; margin: 0; font-weight: 500; white-space: nowrap;">To</label>
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
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('dispenseFilterForm');
                    const searchInput = document.getElementById('dispensedToSearch');
                    const medicineFilter = document.getElementById('medicineFilter');
                    const fromDateFilter = document.getElementById('fromDateFilter');
                    const toDateFilter = document.getElementById('toDateFilter');

                    let searchTimeout;

                    // Auto-submit on search input with debounce
                    searchInput.addEventListener('input', function() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            form.submit();
                        }, 500);
                    });

                    // Auto-submit on filter changes
                    medicineFilter.addEventListener('change', () => form.submit());
                    fromDateFilter.addEventListener('change', () => form.submit());
                    toDateFilter.addEventListener('change', () => form.submit());
                });
            </script>
        @endpush

        <div class="table-container">
            <div style="overflow-x: auto;">
                <table class="data-table" style="min-width: 800px;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Medicine</th>
                        <th>Quantity</th>
                        <th>Dispensed To</th>
                        <th>Reference No.</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dispenses as $dispense)
                        <tr>
                            <td>{{ optional($dispense->dispensed_at)->format('M d, Y') ?? $dispense->created_at->format('M d, Y') }}
                            </td>
                            <td>{{ $dispense->medicine->name ?? 'N/A' }}</td>
                            <td>{{ $dispense->quantity }} {{ $dispense->medicine->unit ?? '' }}</td>
                            <td>{{ $dispense->dispensed_to ?: '—' }}</td>
                            <td>{{ $dispense->reference_no ?: '—' }}</td>
                            <td>{{ $dispense->remarks ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #7f8c8d;">
                                <i class="bi bi-inbox"
                                    style="font-size: 48px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                                No dispenses recorded yet.
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
@endsection