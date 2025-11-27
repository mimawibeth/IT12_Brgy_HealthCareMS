{{-- Medicine List - Inventory Overview --}}
@extends('layouts.app')

@section('title', 'Medicine List')
@section('page-title', 'Medicine List')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/patients.css') }}">
<link rel="stylesheet" href="{{ asset('css/medicine.css') }}">
{{-- Medicine List - Inventory Overview --}}
@extends('layouts.app')

@section('title', 'Medicine List')
@section('page-title', 'Medicine List')

@section('content')
    <div class="page-content">
        <div class="content-header">
            <h2>Medicine Inventory</h2>
            <div class="header-actions">
                <a href="{{ route('medicine.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Medicine
                </a>
                <a href="{{ route('medicine.dispense') }}" class="btn btn-secondary">
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
                                    <a href="{{ route('medicine.edit', $medicine) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form action="{{ route('medicine.destroy', $medicine) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this medicine?')">Delete</button>
                                    </form>
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