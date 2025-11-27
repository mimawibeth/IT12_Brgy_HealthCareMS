@extends('layouts.app')

@section('title', 'Medicine List')
@section('page-title', 'Medicine Inventory')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
<div class="page-content">
    <div class="content-header">
        <div>
            <h2>Medicine Inventory</h2>
            <p class="content-subtitle">Manage medicine stock, inventory levels, and dispense records.</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('medicine.create') }}" class="btn btn-primary">+ Add Medicine</a>
            <a href="{{ route('medicine.dispense') }}" class="btn btn-secondary">Dispense Medicine</a>
        </div>
    </div>

    <div class="filters">
        <div class="search-box">
            <input type="text" class="search-input" placeholder="Search medicine name or generic name">
            <button class="btn btn-search" type="button">Search</button>
        </div>
        <div class="filter-options">
            <select class="filter-select">
                <option value="">All Forms</option>
                <option value="tablet">Tablet</option>
                <option value="syrup">Syrup</option>
                <option value="capsule">Capsule</option>
            </select>
            <select class="filter-select">
                <option value="">Stock Status</option>
                <option value="low">Low Stock</option>
                <option value="in_stock">In Stock</option>
                <option value="out_of_stock">Out of Stock</option>
            </select>
        </div>
    </div>

    <div class="table-container">
        <div class="table-heading">
            <h3>Medicine List</h3>
            <span class="table-note">Current inventory and stock levels</span>
        </div>
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
                        <td>{{ $medicine->generic_name ?? '—' }}</td>
                        <td>{{ $medicine->dosage_form ?? '—' }}</td>
                        <td>{{ $medicine->strength ?? '—' }}</td>
                        <td>{{ $medicine->unit ?? '—' }}</td>
                        <td>{{ $medicine->quantity_on_hand }}</td>
                        <td>{{ $medicine->reorder_level ?? '—' }}</td>
                        <td>{{ optional($medicine->expiry_date)->format('M d, Y') ?? '—' }}</td>
                        <td>
                            <a href="{{ route('medicine.edit', $medicine) }}" class="btn-action btn-edit">Edit</a>
                        
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

    @php
        $showPagination = !empty($medicines) && method_exists($medicines, 'hasPages') && $medicines->hasPages();
    @endphp
    @if($showPagination)
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
                Page {{ $medicines->currentPage() }} of {{ $medicines->lastPage() }} ({{ $medicines->total() }} total medicines)
            </span>

            @if($medicines->hasMorePages())
                <a class="btn-page" href="{{ $medicines->nextPageUrl() }}">Next »</a>
            @else
                <button class="btn-page" disabled>Next »</button>
            @endif
        </div>
    @endif
</div>
@endsection
