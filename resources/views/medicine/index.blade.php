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
                                <form action="{{ route('medicine.destroy', $medicine) }}" method="POST" style="display:inline-block;">
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
@endsection
