@extends('layouts.app')

@section('title', 'Edit Medicine')
@section('page-title', 'Edit Medicine')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="form-container">
            <h2 class="form-title">Edit Medicine</h2>
            <div id="form-alert" class="alert" style="display:none"></div>

            <form method="POST" action="{{ route('medicine.update', $medicine) }}" class="patient-form" novalidate>
                @csrf
                @method('PUT')

                <div class="form-section section-patient-info">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Medicine Information
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Brand Name <span class="required-asterisk">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" required
                                value="{{ old('name', $medicine->name) }}">
                            <span class="error-message" data-for="name"></span>
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
                    </div>

                    <div class="form-row">
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
                            <label for="expiry_date">Expiry Date</label>
                            <input type="date" id="expiry_date" name="expiry_date" class="form-control"
                                value="{{ old('expiry_date', optional($medicine->expiry_date)->toDateString()) }}">
                        </div>
                    </div>
                </div>

                <div class="form-section section-assessment">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Inventory Details
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="quantity_on_hand">Quantity on Hand <span class="required-asterisk">*</span></label>
                            <input type="number" id="quantity_on_hand" name="quantity_on_hand" class="form-control" min="0"
                                required value="{{ old('quantity_on_hand', $medicine->quantity_on_hand) }}">
                            <span class="error-message" data-for="quantity_on_hand"></span>
                        </div>

                        <div class="form-group">
                            <label for="reorder_level">Reorder Level</label>
                            <input type="number" id="reorder_level" name="reorder_level" class="form-control" min="0"
                                value="{{ old('reorder_level', $medicine->reorder_level) }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="remarks">Remarks</label>
                            <textarea id="remarks" name="remarks" class="form-control"
                                rows="3">{{ old('remarks', $medicine->remarks) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Medicine</button>
                    <a href="{{ route('medicine.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('.patient-form');
            const alertBox = document.getElementById('form-alert');

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const requiredEls = form.querySelectorAll('[required]');
                let valid = true;
                requiredEls.forEach(function (el) {
                    const err = form.querySelector('.error-message[data-for="' + el.id + '"]');
                    if (!el.value) {
                        valid = false;
                        if (err) err.textContent = 'This field is required.';
                    } else {
                        if (err) err.textContent = '';
                    }
                });

                if (!valid) {
                    alertBox.className = 'alert alert-error';
                    alertBox.style.display = 'block';
                    alertBox.textContent = 'Please fix validation errors before saving.';
                    return;
                }

                alertBox.style.display = 'none';
                form.submit();
            });
        });
    </script>
@endpush