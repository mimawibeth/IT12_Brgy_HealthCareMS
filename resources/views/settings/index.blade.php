{{-- Settings - Account & Display Preferences --}}
@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
    <div class="page-content">
        <form method="POST" action="{{ route('settings.update') }}" class="patient-form">
            @csrf

            {{-- Account Settings --}}
            <div class="card" style="margin-bottom: 20px;">
                <h3>Account Settings</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" class="form-control"
                            value="{{ old('first_name', auth()->user()->first_name) }}" required>
                        @error('first_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name" class="form-control"
                            value="{{ old('middle_name', auth()->user()->middle_name) }}">
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" class="form-control"
                            value="{{ old('last_name', auth()->user()->last_name) }}" required>
                        @error('last_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="form-control"
                            value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>


                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_number">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" class="form-control"
                            value="{{ old('contact_number', auth()->user()->contact_number) }}"
                            placeholder="e.g., 09171234567">
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" class="form-control"
                            value="{{ old('address', auth()->user()->address) }}">
                    </div>
                </div>

                <p style="font-size: 13px; color: #7f8c8d; margin-top: 12px;">
                    <i class="bi bi-info-circle"></i> Update your personal information and contact details.
                </p>
            </div>

            {{-- Display Preferences --}}
            <div class="card" style="margin-bottom: 20px;">
                <h3>Display Preferences</h3>

                <div class="form-group">
                    <label for="dark_mode">Color Theme</label>
                    <select id="dark_mode" name="dark_mode" class="form-control">
                        <option value="0" @selected(!(auth()->user()->dark_mode ?? false))>Light Mode</option>
                        <option value="1" @selected(auth()->user()->dark_mode ?? false)>Dark Mode</option>
                    </select>
                </div>

                <p style="font-size: 13px; color: #7f8c8d; margin-bottom: 16px;">
                    Dark mode applies to this account only and updates the colors of the dashboard, side navigation, and
                    content areas.
                </p>

                <div class="form-group">
                    <label for="text_size">Text Size</label>
                    <select id="text_size" name="text_size" class="form-control">
                        <option value="small" @selected((auth()->user()->text_size ?? 'medium') === 'small')>Small</option>
                        <option value="medium" @selected((auth()->user()->text_size ?? 'medium') === 'medium')>Medium
                            (Default)</option>
                        <option value="large" @selected((auth()->user()->text_size ?? 'medium') === 'large')>Large</option>
                    </select>
                </div>

                <p style="font-size: 13px; color: #7f8c8d;">
                    Adjust the text size across the entire application for better readability.
                </p>
            </div>

            <div class="form-actions" style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
@endsection