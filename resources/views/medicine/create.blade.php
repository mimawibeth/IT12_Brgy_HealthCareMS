{{-- Add Medicine --}}
@extends('layouts.app')

@section('title', 'Add Medicine')
@section('page-title', 'Add Medicine')

@section('content')
    <div class="page-content">
        <div class="content-header">
            <h2>Add New Medicine</h2>
        </div>

        <form method="POST" action="{{ route('medicine.store') }}" class="patient-form">
            @csrf

            <div class="form-group">
                <label for="name">Brand Name *</label>
                <input type="text" id="name" name="name" class="form-control" required value="{{ old('name') }}">
            </div>

            <div class="form-group">
                <label for="generic_name">Generic Name</label>
                <input type="text" id="generic_name" name="generic_name" class="form-control"
                    value="{{ old('generic_name') }}">
            </div>

            <div class="form-group">
                <label for="dosage_form">Dosage Form</label>
                <input type="text" id="dosage_form" name="dosage_form" class="form-control"
                    placeholder="Tablet, Syrup, Capsule, etc." value="{{ old('dosage_form') }}">
            </div>

            <div class="form-group">
                <label for="strength">Strength</label>
                <input type="text" id="strength" name="strength" class="form-control"
                    placeholder="e.g., 500 mg" value="{{ old('strength') }}">
            </div>

            <div class="form-group">
                <label for="unit">Unit</label>
                <input type="text" id="unit" name="unit" class="form-control" placeholder="tablet, mL, vial"
                    value="{{ old('unit', 'tablet') }}">
            </div>

            <div class="form-group">
                <label for="quantity_on_hand">Quantity on Hand *</label>
                <input type="number" id="quantity_on_hand" name="quantity_on_hand" class="form-control" min="0"
                    required value="{{ old('quantity_on_hand', 0) }}">
            </div>

            <div class="form-group">
                <label for="reorder_level">Reorder Level</label>
                <input type="number" id="reorder_level" name="reorder_level" class="form-control" min="0"
                    value="{{ old('reorder_level', 0) }}">
            </div>

            <div class="form-group">
                <label for="expiry_date">Expiry Date</label>
                <input type="date" id="expiry_date" name="expiry_date" class="form-control"
                    value="{{ old('expiry_date') }}">
            </div>

            <div class="form-group">
                <label for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks" class="form-control" rows="3">{{ old('remarks') }}</textarea>
            </div>

            <div class="form-actions">
                <a href="{{ route('medicine.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Medicine</button>
            </div>
        </form>
    </div>
@endsection
