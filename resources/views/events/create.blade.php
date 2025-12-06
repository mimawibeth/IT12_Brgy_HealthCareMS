@extends('layouts.app')

@section('title', 'Add Event')
@section('page-title', 'Add New Event')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="form-container">
            <h2 class="form-title">Add New Event</h2>
            <div id="form-alert" class="alert" style="display:none"></div>

            <form method="POST" action="{{ route('events.store') }}" class="patient-form" novalidate>
                @csrf

                <div class="form-section section-event-info">
                    <h3 class="section-header">
                        <span class="section-indicator"></span>Event Information
                    </h3>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="title">Event Title <span class="required-asterisk">*</span></label>
                            <input type="text" id="title" name="title" class="form-control" required value="{{ old('title') }}" placeholder="Enter event title">
                            <span class="error-message" data-for="title"></span>
                            @error('title')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter event description">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date">Start Date <span class="required-asterisk">*</span></label>
                            <input type="date" id="start_date" name="start_date" class="form-control" required value="{{ old('start_date', request('date')) }}">
                            <span class="error-message" data-for="start_date"></span>
                            @error('start_date')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                            @error('end_date')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_time">Start Time</label>
                            <input type="time" id="start_time" name="start_time" class="form-control" value="{{ old('start_time') }}">
                            @error('start_time')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="end_time">End Time</label>
                            <input type="time" id="end_time" name="end_time" class="form-control" value="{{ old('end_time') }}">
                            @error('end_time')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" class="form-control" value="{{ old('location') }}" placeholder="Enter event location">
                            @error('location')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="color">Event Color</label>
                            <input type="color" id="color" name="color" class="form-control" value="{{ old('color', '#4a90a4') }}" style="height: 40px;">
                            @error('color')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Create Event
                    </button>
                    <a href="{{ route('events.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('.patient-form');
            const alertBox = document.getElementById('form-alert');
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            // Set end_date default to start_date if not set
            if (!endDate.value && startDate.value) {
                endDate.value = startDate.value;
            }

            // Update end_date when start_date changes
            startDate.addEventListener('change', function() {
                if (!endDate.value || endDate.value < startDate.value) {
                    endDate.value = startDate.value;
                }
                endDate.min = startDate.value;
            });

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

                // Validate end_date is after or equal to start_date
                if (endDate.value && startDate.value && endDate.value < startDate.value) {
                    valid = false;
                    alert('End date must be after or equal to start date.');
                }

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

@endsection

