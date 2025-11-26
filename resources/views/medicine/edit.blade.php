{{-- Edit Medicine --}}
@extends('layouts.app')

@section('title', 'Edit Medicine')
@section('page-title', 'Edit Medicine')

@section('content')
    <div class="page-content">
        <div class="content-header">
            <h2>Edit Medicine</h2>
        </div>

        <form method="POST" action="{{ route('medicine.update', $medicine) }}" class="patient-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Brand Name *</label>
                <input type="text" id="name" name="name" class="form-control" required value="{{ old('name', $medicine->name) }}">
            </div>

            <div class="form-group">
                <label for="generic_name">Generic Name</label>
                <input type="text" id="generic_name" name="generic_name" class="form-control"
                    value="{{ old('generic_name', $medicine->generic_name) }}">
            </div>

            <div class="form-group">
                <label for="dosage_form">Dosage Form</label>
                <input type="text" id="dosage_form" name="dosage_form" class="form-control"
                    value="{{ old('dosage_form', $medicine->dosage_form) }}">
            </div>

            <div class="form-group">
                <label for="strength">Strength</label>
                <input type="text" id="strength" name="strength" class="form-control"
                    value="{{ old('strength', $medicine->strength) }}">
            </div>

            <div class="form-group">
                <label for="unit">Unit</label>
                <input type="text" id="unit" name="unit" class="form-control"
                    value="{{ old('unit', $medicine->unit) }}">
            </div>

            <div class="form-group">
                <label for="quantity_on_hand">Quantity on Hand *</label>
                <input type="number" id="quantity_on_hand" name="quantity_on_hand" class="form-control" min="0"
                    required value="{{ old('quantity_on_hand', $medicine->quantity_on_hand) }}">
            </div>

            <div class="form-group">
                <label for="reorder_level">Reorder Level</label>
                <input type="number" id="reorder_level" name="reorder_level" class="form-control" min="0"
                    value="{{ old('reorder_level', $medicine->reorder_level) }}">
            </div>

            <div class="form-group">
                <label for="expiry_date">Expiry Date</label>
                <input type="date" id="expiry_date" name="expiry_date" class="form-control"
                    value="{{ old('expiry_date', optional($medicine->expiry_date)->toDateString()) }}">
            </div>

            <div class="form-group">
                <label for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks" class="form-control" rows="3">{{ old('remarks', $medicine->remarks) }}</textarea>
            </div>

            <div class="form-actions">
                <a href="{{ route('medicine.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Medicine</button>
            </div>
        </form>
    </div>
@endsection
