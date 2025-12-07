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
        <form method="GET" action="{{ route('medicine.dispense') }}" class="filters">
            <div class="filter-options" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-bottom: 1.5rem;">
                <input type="text" name="dispensed_to" placeholder="Search by patient name or ID..." class="search-input"
                    value="{{ request('dispensed_to') }}" style="flex: 1; min-width: 300px;">

                <select name="medicine_id" class="filter-select">
                    <option value="">All Medicines</option>
                    @foreach($medicines as $medicine)
                        <option value="{{ $medicine->id }}" @selected(request('medicine_id') == $medicine->id)>
                            {{ $medicine->name }}
                        </option>
                    @endforeach
                </select>

                <input type="date" name="from_date" class="filter-select" placeholder="From Date"
                    value="{{ request('from_date') }}" title="From Date">

                <input type="date" name="to_date" class="filter-select" placeholder="To Date"
                    value="{{ request('to_date') }}" title="To Date">

                <button type="submit" class="btn btn-primary" style="padding: 10px 15px !important; font-size: 14px; font-weight: normal;">
                    <i class="bi bi-search"></i> Filter
                </button>

                @if(request()->hasAny(['medicine_id', 'from_date', 'to_date', 'dispensed_to']))
                    <a href="{{ route('medicine.dispense') }}" class="btn btn-secondary" style="padding: 10px 15px !important; font-size: 14px; font-weight: normal;">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                @endif
            </div>
        </form>

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