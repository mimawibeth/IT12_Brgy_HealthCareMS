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
        <div class="content-header"></div>

        <div style="margin: 1.5rem 0;">
            <form method="GET" action="{{ route('medicine.dispense') }}" class="patient-form" style="padding: 1rem;">
                <div class="form-section section-patient-info">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Filter Dispense History
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="filter_medicine_id">Medicine</label>
                            <select id="filter_medicine_id" name="medicine_id" class="form-control">
                                <option value="">All Medicines</option>
                                @foreach($medicines as $medicine)
                                    <option value="{{ $medicine->id }}" @selected(request('medicine_id') == $medicine->id)>
                                        {{ $medicine->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="from_date">From Date</label>
                            <input type="date" id="from_date" name="from_date" class="form-control"
                                value="{{ request('from_date') }}">
                        </div>

                        <div class="form-group">
                            <label for="to_date">To Date</label>
                            <input type="date" id="to_date" name="to_date" class="form-control"
                                value="{{ request('to_date') }}">
                        </div>

                        <div class="form-group">
                            <label for="filter_dispensed_to">Dispensed To</label>
                            <input type="text" id="filter_dispensed_to" name="dispensed_to" class="form-control"
                                value="{{ request('dispensed_to') }}" placeholder="Patient name or ID">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="{{ route('medicine.dispense') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container">

            <table class="data-table">
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